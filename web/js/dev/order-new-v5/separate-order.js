/**
/**
 * Получение данных с сервера
 * Разбиение заказа
 * Модель knockout
 * Аналитика
 *
 * @author	Zaytsev Alexandr
 */
;(function ( window, document, $, ENTER, ko ) {
	console.info('separate-order.js init');
	
	var
		serverData = $('#jsOrderDelivery').data('value'),
		utils = ENTER.utils,
		body = $(document.body);
	// end of vars


	var
		/**
		 * Логика разбиения заказа на подзаказы
		 * Берутся states из выбранного способа доставки в порядке приоритета.
		 * Каждый новый states - новый подзаказ.
		 *
		 * @param	{Array}		statesPriority		Приоритет методов доставки
		 * 
		 * @param	{Object}	preparedProducts	Уже обработанные продукты, которые попали в какой-либо блок доставки
		 * @param	{Array}		productInState		Массив продуктов, которые есть в данном способе доставки
		 * @param	{Array}		productsToNewBox	Массив продуктов, которые должны попасть в новый блок доставки
		 * @param	{Number}	choosenPointForBox	Точка доставки для блока самовывоза
		 * @param	{String}	token				Временное имя для создаваемого блока
		 * @param	{String}	nowState			Текущий тип доставки который находится в обработке
		 * @param	{String}	nowProduct			Текущий id продукта который находится в обработке
		 */
		separateOrder = function separateOrder( statesPriority ) {

		var
			preparedProducts = {},
			productInState = [],
			productsToNewBox = [],
			choosenPointForBox = null,
			token = null,
			nowState = null,
			nowProduct = null,
			choosenBlock = null,
			isUnique = null,
			nowProductsToNewBox = [],
            oldDeliveryBoxes = [],
			discounts = ENTER.OrderModel.orderDictionary.orderData.discounts || [],
			deliveryBoxFound;
		// end of vars
		
		if ( ENTER.OrderModel.paypalECS() ) {
			console.info('PayPal ECS включен. Необходимо сохранить выбранные параметры в cookie');

			window.docCookies.setItem('chTypeBtn_paypalECS', ENTER.OrderModel.deliveryTypesButton, 10 * 60);
			window.docCookies.setItem('chPoint_paypalECS', ENTER.OrderModel.choosenPoint(), 10 * 60);
			window.docCookies.setItem('chTypeId_paypalECS', ENTER.OrderModel.choosenDeliveryTypeId, 10 * 60);
			window.docCookies.setItem('chStetesPriority_paypalECS', JSON.stringify(ENTER.OrderModel.statesPriority), 10 * 60);
		}

        if (ENTER.OrderModel.deliveryBoxes().length) {
            oldDeliveryBoxes = ko.toJS(ENTER.OrderModel.deliveryBoxes());
            console.info('[DeliveryBox] Существуют старые блоки доставки', oldDeliveryBoxes);
        }

		// очищаем объект созданых блоков, удаляем блоки из модели
		ENTER.OrderModel.deliveryBoxes.removeAll();

		// обнуляем примененный купон
		ENTER.OrderModel.hasCoupons(false);

		// Маркируем выбранный способ доставки
		console.log('Маркируем выбранный способ доставки');
		$('#'+ENTER.OrderModel.deliveryTypesButton).attr('checked','checked').trigger('change');
			
		// Обнуляем общую стоимость заказа
		ENTER.OrderModel.totalSum(0);

		// Обнуляем блоки с доставкой на дом и генерируем событие об этом
		ENTER.OrderModel.hasHomeDelivery(false);
		body.trigger('orderdeliverychange',[false]);


		/**
		 * Перебор states в выбранном способе доставки в порядке приоритета
		 */
		for ( var i = 0, len = statesPriority.length; i < len; i++ ) {
			nowState = statesPriority[i];
			isUnique = ENTER.OrderModel.orderDictionary.isUniqueDeliveryState(nowState);

			console.info('перебирем ' + (isUnique ? 'уникальный* ' : '') + 'метод ' + nowState);

			productsToNewBox = [];

			if ( !ENTER.OrderModel.orderDictionary.hasDeliveryState(nowState) ) {
				console.info('для метода '+nowState+' нет товаров');

				continue;
			}

			productInState = ENTER.OrderModel.orderDictionary.getProductFromState(nowState);

			/**
			 * Перебор продуктов в текущем deliveryStates
			 */
			for ( var j = productInState.length - 1; j >= 0; j-- ) {
				nowProduct = productInState[j];

				if ( preparedProducts[nowProduct] ) {
					// если этот товар уже находили
					console.log('товар '+nowProduct+' уже определялся к блоку');

					continue;
				}
				
				console.log('добавляем товар '+nowProduct+' в блок для метода '+nowState);

				preparedProducts[nowProduct] = true;
				productsToNewBox.push( ENTER.OrderModel.orderDictionary.getProductById(nowProduct) );
			}

			if ( productsToNewBox.length ) {
				choosenPointForBox = ( ENTER.OrderModel.orderDictionary.hasPointDelivery(nowState) ) ?
					ENTER.OrderModel.choosenPoint() :
					ENTER.OrderModel.orderDictionary.getDefaultPointId(nowState);

				token = nowState+'_'+choosenPointForBox;

				if ( ENTER.OrderModel.hasDeliveryBox(token) ) {
					// Блок для этого типа доставки в этот пункт уже существует
					choosenBlock = ENTER.OrderModel.getDeliveryBoxByToken(token);
					choosenBlock.addProductGroup( productsToNewBox );
				}
				else if ( isUnique ) {
					// Блока для этого типа доставки в этот пункт еще не существует, создадим его:
					// Если есть флаг уникальности, каждый товар в отдельном блоке будет

					// Разделим товары, продуктом считаем уникальную единицу товара:
					// Пример: 5 тетрадок ==> 5 товаров количеством 1 шт
					nowProductsToNewBox = ENTER.OrderModel.prepareProductsQuantityByUniq(productsToNewBox);
					for ( j = nowProductsToNewBox.length - 1; j >= 0; j-- ) {
						nowProduct = [ nowProductsToNewBox[j] ];
						ENTER.constructors.DeliveryBox(nowProduct, nowState, choosenPointForBox);
					}

				} else {
					// Блока для этого типа доставки в этот пункт еще не существует, создадим его:
					// Без флага уникальности, все товары скопом:
					// Пример: 5 тетрадок ==> 1 товар количеством 5 шт
					ENTER.constructors.DeliveryBox(productsToNewBox, nowState, choosenPointForBox);
				}
			}
		}

        for ( var a in oldDeliveryBoxes ) {
            if (ENTER.OrderModel.hasDeliveryBox(oldDeliveryBoxes[a].token)) {
            	deliveryBoxFound = ENTER.OrderModel.getDeliveryBoxByToken(oldDeliveryBoxes[a].token);

                console.log('[Deliverybox] Обнаружен старый блок доставки: ', oldDeliveryBoxes[a].token, ' c выбранной датой ', oldDeliveryBoxes[a].choosenDate);
                console.info('[Deliverybox] Применяю старую дату на блок ', oldDeliveryBoxes[a].token);

                console.warn(oldDeliveryBoxes[a].allDatesForBlock);
                console.warn(oldDeliveryBoxes[a].allDatesForBlock.length);

                deliveryBoxFound.choosenDate(oldDeliveryBoxes[a].choosenDate);
                deliveryBoxFound.allDatesForBlock.removeAll();
                deliveryBoxFound.allDatesForBlock(oldDeliveryBoxes[a].allDatesForBlock);

                console.log(deliveryBoxFound.allDatesForBlock());
                console.log(deliveryBoxFound.allDatesForBlock().length);
            }
        }

		console.info('Созданные блоки:', ENTER.OrderModel.deliveryBoxes());

        if (ENTER.OrderModel.deliveryBoxes().length > 1) body.trigger('trackUserAction', ['1_2 Доставка, заказ разбит', ENTER.OrderModel.deliveryBoxes().length]);

		// Добавляем купоны
		ENTER.OrderModel.couponsBox(discounts);

		// выбираем URL для проверки купонов - первый видимый купон
		ENTER.OrderModel.couponUrl( $('.bSaleList__eItem:visible .jsCustomRadio').eq(0).val() );
		$('.bSaleList__eItem:visible .jsCustomRadio').eq(0).trigger('change');


		// выбираем первый доступный метод оплаты
		if ( 0 === $('.bPayMethod:visible .jsCustomRadio:checked').length ) {
			$('.bPayMethod:visible .jsCustomRadio').eq(0).attr('checked', 'checked').trigger('change');
		}

		/**
		 * Проверка примененных купонов
		 *
		 * Если заказ разбился, то купон применять нельзя или
		 * Если сумма заказа меньше либо равана размеру скидки купона
		 */
		if ( ( ENTER.OrderModel.hasCoupons() && ENTER.OrderModel.deliveryBoxes().length > 1 ) || 
			( ENTER.OrderModel.appliedCoupon() && ENTER.OrderModel.appliedCoupon().sum && 
			( parseFloat(ENTER.OrderModel.totalSum())+parseFloat(ENTER.OrderModel.appliedCoupon().sum) <= parseFloat(ENTER.OrderModel.appliedCoupon().sum) ) ) ) {
			console.warn('Нужно удалить купон');

			var msg = 'Купон не может быть применен при текущем разбиении заказа и будет удален';

			var callback = function() {
				console.log('удаление');
				ENTER.OrderModel.deleteItem(ENTER.OrderModel.appliedCoupon());
			};

			$.when(showError(msg)).then(callback);

			return false;
		}

		if ( preparedProducts.length !== ENTER.OrderModel.orderDictionary.orderData.products.length ) {
			console.warn('не все товары были обработаны');
		}

		console.warn('end');


		$('.bCountSection').goodsCounter({
			onChange:function( count ) {
				console.info('counter change', count);

                body.trigger('trackUserAction', ['1_4_3 Число товаров']);

				var
					seturl = $(this).data('seturl') || '',
					newURl = seturl.addParameterToUrl('quantity', count),
					reqArray;
				// end of vars
				
				console.log(seturl);
				console.log(newURl);

				var
					/**
					 * Обработка ответа измеения количества товаров
					 * 
					 * @param	{Object}	res		Ответ от сервера
					 */
					spinnerResponceHandler = function spinnerResponceHandler( res ) {
						if ( !res.success ) {
							ENTER.OrderModel.couponError(res.error.message);
							utils.blockScreen.unblock();

							return;
						}

						ENTER.OrderModel.couponNumber('');
					};
				// end of functions

				utils.blockScreen.block('Обновляем');

				reqArray = [
					{
						type: 'GET',
						url: newURl,
						// data: dataToSend,
						callback: spinnerResponceHandler
					},
					{
						type: 'GET',
						url: ENTER.OrderModel.updateUrl,
						callback: ENTER.OrderModel.modelUpdate
					}
				];

				utils.packageReq(reqArray);
			}
		});
	};


	/**
	 * Кастомный бинд для открытия окна магазинов
	 */
	ko.bindingHandlers.popupShower = {
		update: function( element, valueAccessor ) {
			var val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),
				map = null;
			// end of vars

			if ( unwrapVal ) {
				// create map
				map = new ENTER.constructors.CreateMap('pointPopupMap', ENTER.OrderModel.popupWithPoints().points, $('#mapInfoBlock'));

				$(element).lightbox_me({
					centered: true,
					onClose: function() {
						console.info('закрываем');
						val(false);
					}
				});
				
				map._showMarkers();
			}
			else {
				$('#pointPopupMap').empty();
				$(element).trigger('close');
			}
		}
	};

	/**
	 * Кастомный бинд для отображения блоков с методами оплаты: "прямо сейчас", "при получении", в кредит..
	 */
	ko.bindingHandlers.payBlockVisible = {
		update: function( element ) {
			var
				node = $(element),
				vars = node.data('vars'),
				toHide = (vars && vars.toHide) ? vars.toHide : false,
				choosenDeliveryTypeId = ENTER.OrderModel.choosenDeliveryTypeId,
				deliveryBoxes = ENTER.OrderModel.deliveryBoxes(),
				dCount = deliveryBoxes.length,
				testDeliveryId,
				testPaymentId,
				nodeHidded = 1
				;

			if ( !dCount ) {
				return;
			}

			/*
			 * Cтарый механизм показа/сокрытия блоков
			 * показываем "кредиты" и "оплату сейчас", если кол-во блоков доставки == 1
			 */
			if ( 1 === dCount ) {
				nodeHidded = 0;
				console.log('Кол-во deliveryBoxes == 1: Показываем payBlock');
			}
			else {
				nodeHidded = 1;
				console.log('Кол-во deliveryBoxes > 1: Скрываем payBlock');
			}

			/**
			 * Если указано toHide в дата-аттрибуте, то скрываем блоки с недоступными методами
			 */
			if ( toHide ) {

				for ( testDeliveryId in toHide ) {
					if ( 'undefined' === typeof toHide[testDeliveryId].length ) {	// !не массив, скрываем для всех
						if ( $.inArray(choosenDeliveryTypeId, toHide) >= 0 ) {
							nodeHidded = 1;
							console.log('toHide NoArr: Скрываем payBlock');
						}
					}
					else if ( choosenDeliveryTypeId === testDeliveryId ) {			// !массив, обходим блоки оплаты
						for ( testPaymentId in toHide[testDeliveryId] ) {
							if ( testPaymentId === vars.typeId ) {
								nodeHidded = 1;
								console.log('toHide Arr: Скрываем payBlock');
							}
						}// end of second for
					}
				}// end of first for

			}

			nodeHidded ? node.hide() : node.show(); // показываем либо скрываем элемент
		}
	};


	/**
	 * Кастомный бинд отображения методов оплаты
	 */
	ko.bindingHandlers.paymentMethodVisible = {
		update: function( element, valueAccessor ) {
			var
				val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),
				node = $(element),
				nodeData = node.data('value'),
				maxSum = parseInt( nodeData['max-sum'], 10 ),
				minSum = parseInt( nodeData['min-sum'], 10 ),
				methodId = nodeData['method_id'],
				isAvailableToPickpoint = nodeData['isAvailableToPickpoint'];
			// end of vars


			if (
			/* 6 is DeliveryTypeId for PickPoint  */
			( 6 === ENTER.OrderModel.choosenDeliveryTypeId && false === isAvailableToPickpoint ) ||
			( 4 === ENTER.OrderModel.choosenDeliveryTypeId && 13 === methodId && ENTER.OrderModel.lifeGift() === false ) ||
			( !isNaN(maxSum) && maxSum < unwrapVal ) || /* Если существует максимальная сумма и текущая сумма больше максимальнодопустимой для этого варианта оплаты */
			( !isNaN(minSum) && minSum > unwrapVal ) /* Если существует минимальная сумма и текущая сумма больше минимальнодопустимой для этого варианта оплаты */ ) {
				node.hide();
                if (methodId == 13 && -1 == $.inArray(ENTER.OrderModel.choosenDeliveryTypeId, [3,4])) node.show(); // показываем Paypal для всех методов доставки, кроме "самовывоза" и "заберу сейчас"
				return;
			}

			node.show();
		}
	};

	/**
	 * Кастомный бинд для смены недель, анимирование слайдера
	 */
	ko.bindingHandlers.calendarSlider = {
		update: function( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {
			var
				slider = $(element),
				nowLeft = valueAccessor(),

				dateItem = slider.find('.bBuyingDatesItem'),
				dateItemW = dateItem.width() + parseInt(dateItem.css('marginRight'), 10) + parseInt(dateItem.css('marginLeft'), 10);
			// end of vars

			slider.width(dateItem.length * dateItemW);

			if ( nowLeft > 0 ) {
				nowLeft -= 380;
				bindingContext.box.calendarSliderLeft(nowLeft);

				return;
			}

			if ( nowLeft < -slider.width() ) {
				nowLeft += 380;
				bindingContext.box.calendarSliderLeft(nowLeft);

				return;
			}

			slider.animate({'left': nowLeft});
		}
	};

	/**
	 * Кастомынй бинд отображения и смены купонов
	 */
	ko.bindingHandlers.couponsVisible = {
		update: function( element, valueAccessor ) {
			var
				val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),

				node = $(element),
				fieldNode = node.find('.mSaleInput'),
				buttonNode = node.find('.mSaleBtn'),

				emptyBlock = node.find('.bSaleData__eEmptyBlock'),

				i;
			// end of vars

			$('.bSaleList__eItem').removeClass('hidden');

			for ( i = unwrapVal.length - 1; i >= 0; i-- ) {
				node.find('.bSaleList__eItem[data-type="'+unwrapVal[i].type+'"]').addClass('hidden');

				if ( unwrapVal[i].type === 'coupon' ) {
					console.log('Есть примененный купон');

					ENTER.OrderModel.hasCoupons(true);
					ENTER.OrderModel.appliedCoupon(unwrapVal[i]);
				}
			}

			if ( $('.bSaleList__eItem.hidden').length === $('.bSaleList__eItem').length ||
				$('.bSaleList__eItem:hidden').length === $('.bSaleList__eItem').length ) {
				// если все скидки применены

				fieldNode.attr('disabled', 'disabled');
				buttonNode.attr('disabled', 'disabled').addClass('mDisabled');
				emptyBlock.show();
			}
			else {
				// не все скидки применены

				fieldNode.removeAttr('disabled');
				buttonNode.removeAttr('disabled').removeClass('mDisabled');
				emptyBlock.hide();
			}
		}
	};


	/**
	 * === ORDER MODEL ===
	 */
	ENTER.OrderModel = {
		/**
		 * URL для обновления данных с сервера
		 */
		updateUrl: $('#jsOrderDelivery').data('url'),

		/**
		 * Флаг завершения обработки данных
		 *
		 * @type {Boolean}
		 */
		prepareData: ko.observable(false),

		/**
		 * Флаг открытия окна с выбором точек доставки
		 *
		 * @type {Boolean}
		 */
		showPopupWithPoints: ko.observable(false),

		/**
		 * Ссылка на элемент input который соответствует выбранному методу доставки
		 */
		deliveryTypesButton: null,

		/**
		 * Приоритет методов доставок на время выбора точек доставки
		 * Если пункт доставки не был выбран - не используется
		 */
		tmpStatesPriority: null,

		/**
		 * Реальный приоритет методов доставок
		 * Сохраняется при выборе пункта доставки или методе доставки не имеющем пунктов доставки
		 */
		statesPriority: null,

		/**
		 * Флаг того что это оформление заказа по акции «Подари жизнь»
		 * https://jira.enter.ru/browse/SITE-2383
		 * 
		 * @type {Boolean}
		 */
		lifeGift: ko.observable(false),

		/**
		 * Флаг того что это оформление заказа типа one-click
		 * https://jira.enter.ru/browse/SITE-2592
		 * 
		 * @type {Boolean}
		 */
		oneClick: ko.observable(false),

		/**
		 * Флаг того что это страница PayPal: схема ECS
		 * https://jira.enter.ru/browse/SITE-1795
		 *
		 * @type {Boolean}
		 */
		paypalECS: ko.observable(false),

		/**
		 * Первоначальная сумма корзины
		 */
		cartSum: null,

		/**
		 * Ссылка на словарь
		 */
		orderDictionary: null,

		/**
		 * Идетификатор приоритетного пункта доставки выбранного пользователем
		 */
		choosenPoint: ko.observable(),

		/**
		 * Есть ли хотя бы один блок доставки на дом
		 */
		hasHomeDelivery: ko.observable(false),

		/**
		 * Массив способов доставок доступных пользователю
		 */
		deliveryTypes: ko.observableArray([]),

		/**
		 * Массив блоков доставок
		 */
		deliveryBoxes: ko.observableArray([]),

		/**
		 * Объект данных для отображения окна с пунктами доставок
		 */
		popupWithPoints: ko.observable({}),

		/**
		 * Общая сумма заказа
		 */
		totalSum: ko.observable(0),

		/**
		 * Есть ли примененные купоны
		 *
		 * @type {Boolean}
		 */
		hasCoupons: ko.observable(false),

		/**
		 * Размер скидки примененного купона
		 */
		appliedCoupon: ko.observable(),

		/**
		 * Номер введенного сертификата
		 */
		couponNumber: ko.observable(),

		/**
		 * URL по которому нужно проверять карту
		 */
		couponUrl: ko.observable(),

		/**
		 * Ошибки сертификата
		 */
		couponError: ko.observable(),

		/**
		 * Массив примененных купонов
		 */
		couponsBox: ko.observableArray([]),


		/**
		 * Существует ли блок доставки
		 * 
		 * @param	String}		token	Токен блока доставки
		 * @return	{boolean}
		 */
		hasDeliveryBox: function( token ) {
			console.info('Существует ли блок доставки '+token);

			var i = null;

			for ( i = ENTER.OrderModel.deliveryBoxes().length - 1; i >= 0; i--) {
				if ( ENTER.OrderModel.deliveryBoxes()[i].token === token ) {
					return true;
				}
			}

			return false;
		},

		/**
		 * Получить ссылку на блок по токену
		 * 
		 * @param	String}		token	Токен блока доставки
		 * @return	{Object}			Объект блока
		 */
		getDeliveryBoxByToken: function( token ) {
			console.info('Получить ссылку на блок по токену '+token);

			var i = null;

			for ( i = ENTER.OrderModel.deliveryBoxes().length - 1; i >= 0; i--) {
				if ( ENTER.OrderModel.deliveryBoxes()[i].token === token ) {
					return ENTER.OrderModel.deliveryBoxes()[i];
				}
			}

			return false;
		},

		/**
		 * Удаление блока доставки по токену
		 * 
		 * @param	String}		token	Токен блока доставки
		 * @returns	{*}			DeliveryBox (удалённый блок доставки либо null)
		 */
		removeDeliveryBox: function( token ) {
			console.info('Поиск для удаления блока по токену ' + token);

			var
				i,
				ret = null,
				dBoxes = ENTER.OrderModel.deliveryBoxes(),
				dBCount = dBoxes.length;

			for ( i = dBCount - 1; i >= 0; i-- ) {
				if ( dBoxes[i].token === token ) {
					console.info('Удаление блока по токену ' + token);
					ret = ENTER.OrderModel.deliveryBoxes.splice(i, 1);
					if ( 'object' === typeof(ret[0]) ) {
						ret = ret[0];
					}
					break;
				}
			}

			return ret;
		},

		/**
		 * Проверка сертификата
		 */
		checkCoupon: function() {
			console.info('проверяем купон');

			var dataToSend = {
					number: ENTER.OrderModel.couponNumber()
				},

				url = ENTER.OrderModel.couponUrl(),

				reqArray;
			// end of vars

			var couponResponceHandler = function couponResponceHandler( res ) {
				if ( !res.success ) {
					ENTER.OrderModel.couponError(res.error.message);
					utils.blockScreen.unblock();
                    body.trigger('trackUserAction', ['2 Купон', 'Отказ']);
					return;
				}
                body.trigger('trackUserAction', ['2 Купон', 'Принят']);
				ENTER.OrderModel.couponNumber('');
			};

			ENTER.OrderModel.couponError('');

			if ( url === undefined ) {
				console.warn('Не выбран тип скидки');
				ENTER.OrderModel.couponError('Не выбран тип скидки');

				return;
			}

			if ( dataToSend.number === undefined || !dataToSend.number.length ) {
				console.warn('Не введен номер');
				ENTER.OrderModel.couponError('Не введен номер');

				return;
			}

			utils.blockScreen.block('Применяем купон');

			reqArray = [
				{
					type: 'POST',
					url: url,
					data: dataToSend,
					callback: couponResponceHandler
				},
				{
					type: 'GET',
					url: ENTER.OrderModel.updateUrl,
					callback: ENTER.OrderModel.modelUpdate
				}
			];

			utils.packageReq(reqArray);

			return false;
		},

		/**
		 * Обработка выбора пункта доставки
		 * 
		 * @param	{String}	id				Идентификатор
		 * @param	{String}	address			Адрес
		 * @param	{Number}	latitude		Широта
		 * @param	{Number}	longitude		Долгота
		 * @param	{String}	name			Полное имя
		 * @param	{String}	regime			Время работы
		 * @param	{Array}		products		Массив идентификаторов продуктов доступных в данном пункте
		 */
		selectPoint: function( data ) {
			console.info('point selected...');
			console.log(data.parentBoxToken);

			var choosenBlock = null;

			if ( data.parentBoxToken ) {
				choosenBlock = ENTER.OrderModel.getDeliveryBoxByToken(data.parentBoxToken);
				console.log(choosenBlock);
				choosenBlock.selectPoint.apply(choosenBlock,[data]);

				return false;
			}

			// Сохраняем приоритет методов доставок
			ENTER.OrderModel.statesPriority = ENTER.OrderModel.tmpStatesPriority;

			// Сохраняем выбранную приоритетную точку доставки
			ENTER.OrderModel.choosenPoint(data.id);

			// Скрываем окно с выбором точек доставок
			ENTER.OrderModel.showPopupWithPoints(false);

			// Разбиваем на подзаказы
			separateOrder( ENTER.OrderModel.statesPriority );

			return false;
		},

		/**
		 * Выбор метода доставки
		 * 
		 * @param	{Object}	data			Данные о типе доставки
		 * @param	{String}	data.token		Выбранный способ доставки
		 * @param	{String}	data.name		Имя выбранного способа доставки
		 * @param	{Array}		data.states		Варианты типов доставки подходящих к этому методу
		 *
		 * @param	{String}	priorityState	Приоритетный метод доставки из массива
		 * @param	{Object}	checkedInputId	Ссылка на элемент input по которому кликнули
		 */
		chooseDeliveryTypes: function( data, event ) {
			console.info('chooseDeliveryTypes');

			var
				priorityState = data.states[0],
				checkedInputId = event.target.htmlFor;
			// end of vars

			console.log(priorityState);
			console.log(checkedInputId);

            body.trigger('trackUserAction', ['1_1 Доставка', data.shortName]);

			if ( $('#'+checkedInputId).attr('checked') ) {
				console.warn('Этот пункт '+checkedInputId+' уже был выбран');

				return false;
			}

			ENTER.OrderModel.deliveryTypesButton = checkedInputId;
			console.log(ENTER.OrderModel.deliveryTypesButton);

			ENTER.OrderModel.tmpStatesPriority = data.states;
			console.log(ENTER.OrderModel.tmpStatesPriority);

			ENTER.OrderModel.choosenDeliveryTypeId = data.id;
			console.log(ENTER.OrderModel.choosenDeliveryTypeId);

			// если для приоритетного метода доставки существуют пункты доставки, то пользователю необходимо выбрать пункт доставки, если нет - то приравниваем идентификатор пункта доставки к 0
			if ( ENTER.OrderModel.orderDictionary.hasPointDelivery(priorityState) ) {
				console.log('Необходимо показать окно с выбором точки доставки');

				ENTER.OrderModel.popupWithPoints({
					header: data.name,
					points: ENTER.OrderModel.orderDictionary.getAllPointsByState(priorityState)
				});

				ENTER.OrderModel.showPopupWithPoints(true);

				return false;
			}

			console.log('Выбор точки доставки не требуется');

			// Сохраняем приоритет методов доставок
			ENTER.OrderModel.statesPriority = ENTER.OrderModel.tmpStatesPriority;

			// Сохраняем выбранную приоритетную точку доставки (для доставки домой = 0)
			ENTER.OrderModel.choosenPoint(0);

			// Разбиваем на подзаказы
			console.info('Отправляем данные на разбивку');
			separateOrder( ENTER.OrderModel.statesPriority );

			return false;
		},


		/**
		 * Обновление данных
		 */
		modelUpdate: function( res ) {
			console.info('обновление данных с сервера');

			renderOrderData(res);

			separateOrder( ENTER.OrderModel.statesPriority );
		},

		/**
		 * Удаление товара
		 * 
		 * @param	{Object}	data	Данные удалямого товара
		 */
		deleteItem: function( data ) {
			console.info('удаление товара');

			var reqArray = null;

			utils.blockScreen.block('Удаляем');

			var itemDeleteAnalytics = function itemDeleteAnalytics( data ) {
					var products = ENTER.OrderModel.orderDictionary.products,
						totalPrice = 0,
						totalQuan = 0,

						toKISS = {};
					// end of vars

					if ( !data.product ) {
						return false;
					}

					for ( var product in products ) {
						totalPrice += products[product].price;
						totalQuan += products[product].quantity;
					}

					toKISS = {
						'Checkout Step 1 SKU Quantity': totalQuan,
						'Checkout Step 1 SKU Total': totalPrice
					};

					if ( typeof _kmq !== 'undefined' ) {
						_kmq.push(['set', toKISS]);
					}

					if ( typeof _gaq !== 'undefined' ) {
						_gaq.push(['_trackEvent', 'Order card', 'Item deleted']);
					}

					if ( data.hasOwnProperty('product') && data.product.hasOwnProperty('id') ) {
						/* RetailRocket */
						console.info('RetailRocket removeFromCart');
						console.log('product_id=' + data.product.id);
						window.rrApiOnReady.push(function(){ window.rrApi.removeFromBasket(data.product.id) });
					}
				},

				deleteItemResponceHandler = function deleteItemResponceHandler( res ) {
					console.info('deleteItemResponceHandler');
					console.log( res );

					if ( !res.success ) {
						console.warn('не удалось удалить товар');
						utils.blockScreen.unblock();

						return false;
					}

					// запуск аналитики
					itemDeleteAnalytics(res);

				};
			// end of functions

			console.log(data.deleteUrl);

			reqArray = [
				{
					type: 'GET',
					url: data.deleteUrl,
					callback: deleteItemResponceHandler
				},
				{
					type: 'GET',
					url: ENTER.OrderModel.updateUrl,
					callback: ENTER.OrderModel.modelUpdate
				}
			];

			utils.packageReq(reqArray);

			return false;
		},


		/**
		 *  Раразбивка массива товаров в массив по уникальным единицам (для PickPoint)
		 *  т.е. вместо продукта в количестве 2 шт, будут 2 проудкта по 1 шт.
		 *
		 * @param       {Array}   productsToNewBox
		 * @returns     {Array}   productsUniq
		 */
		prepareProductsQuantityByUniq: function prepareProductsQuantityByUniq( productsToNewBox ) {
			var productsUniq = [],
				nowProduct,
				j, k;

			for ( j = productsToNewBox.length - 1; j >= 0; j-- ) {
				//!!! важно клонировать объект, дабы не портить для др. типов доставки
				nowProduct = ENTER.utils.cloneObject(productsToNewBox[j]);
				nowProduct.sum = nowProduct.price;
				nowProduct.quantity = 1;
				nowProduct.oldQuantity = productsToNewBox[j].quantity; // сохраняем старое кол-во товаров в блоке
				for ( k = productsToNewBox[j].quantity - 1; k >= 0; k-- ) {
                    productsUniq.push(nowProduct);
				}
			}

			return productsUniq;
		}
	};

	ko.applyBindings(ENTER.OrderModel);
	/**
	 * ===  END ORDER MODEL ===
	 */
	


	var
		/**
		 * Показ сообщений об ошибках
		 * 
		 * @param	{String}	msg		Сообщение об ошибке
		 * @return	{Object}			Deferred объект
		 */
		showError = function showError( msg ) {
			var content = '<div class="popupbox width290">' +
					'<div class="font18 pb18"> '+msg+'</div>'+
					'</div>' +
					'<p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>',
				block = $('<div>').addClass('popup').html(content),

				popupIsClose = $.Deferred();
			// end of vars
			
			block.appendTo('body');

			var errorPopupCloser = function() {
				block.trigger('close');
				block.remove();

				popupIsClose.resolve();
			};

			block.lightbox_me({
				centered:true,
				closeClick:false,
				closeEsc:false
			});

			block.find('.closePopup').bind('click', errorPopupCloser);

			return popupIsClose.promise();
		},

		/**
		 * Обработка ошибок в продуктах
		 */
		productError = {
			// Товар недоступен для продажи
			'default': function( product ) {
				var msg = 'Товар ' + (product.name ? product.name : '') + ' недоступен для продажи.',

					productErrorIsResolve = $.Deferred();
				// end of vars

				$.when(showError(msg)).then(function() {
					$.ajax({
						type:'GET',
						url: product.deleteUrl
					}).then(productErrorIsResolve.resolve);
				});

				return productErrorIsResolve.promise();

			},

			// Нет необходимого количества товара
			708: function( product ) {
				var msg = '',

					productErrorIsResolve = $.Deferred();
				// end of vars
				
				if ( product.name && product.error.message && product.quantity ) {
					msg = 'Вы заказали товар ' + product.name + ' в количестве ' + product.quantity + ' шт. <br/ >' + product.error.message;
				}
				else {
					msg = 'Товар недоступен для продажи';
				}

				$.when(showError(msg)).then(function() {
				    var reqArray = [
				        {
				            type: 'GET',
				            url: product.setUrl,
				            callback: productErrorIsResolve.resolve
				        },
				        {
				            type: 'GET',
				            url: ENTER.OrderModel.updateUrl,
				            callback: ENTER.OrderModel.modelUpdate
				        }
				    ];

				    utils.packageReq(reqArray);

				    return productErrorIsResolve.promise();
				});
			}
		},

		/**
		 * Обработка ошибок в данных
		 *
		 * @param	{Object}	res		Данные о заказе
		 * 
		 * @param	{Object}	product	Данные о продукте
		 * @param	{Number}	code	Код ошибки
		 */
		allErrorHandler = function allErrorHandler( res ) {
			var product = null,

				productsWithError = [];
			// end of vars

			// Cоздаем массив продуктов содержащих ошибки
			for ( product in res.products ) {
				if ( res.products[product].error && res.products[product].error.code ) {
					productsWithError.push(res.products[product]);
				}
			}

			// Обрабатываем ошибки продуктов по очереди
			var errorCatcher = function errorCatcher( i, callback ) {
				var code = null;

				if ( i < 0 ) {
					console.warn('return');

					callback();
					return;
				}

				code = productsWithError[i].error.code;

				code = ( productError.hasOwnProperty(code) ) ? code : 'default';

				$.when( productError[code](productsWithError[i]) ).then(function() {
					var newI = i - 1;

					errorCatcher( newI, callback );
				});
			};

			/**
			 * Если ошибок в продуктах нет, но есть сообщаение об ошибке, вывести сообщение
			 * Иначе начать обработку ошибок в продуктах
			 */
			if ( productsWithError.length === 0 && res.error.message ) {
				$.when(showError(res.error.message)).then(function() {
					if ( res.redirect ) {
						document.location.href = res.redirect;
					}
				});
			}
			else {
				errorCatcher(productsWithError.length - 1, function() {
					console.warn('1 этап закончен');
					if ( res.redirect ) {
						document.location.href = res.redirect;
					}
				});
			}
		},

		/**
		 * Обработка полученных данных
		 * Создание словаря
		 * 
		 * @param	{Object}	res		Данные о заказе
		 */
		renderOrderData = function renderOrderData( res ) {
			var data, firstPoint;
			utils.blockScreen.unblock();

			if ( !res.success ) {
				console.warn('Данные содержат ошибки');
				console.log(res.error);
				allErrorHandler(res);

				return false;
			}

            if (res.error && res.error.message) showError(res.error.message);

			console.info('Данные с сервера получены');

			ENTER.OrderModel.orderDictionary = new ENTER.constructors.OrderDictionary(res);

			if ( res.paypalECS ) {
				console.info('paypal true');
				ENTER.OrderModel.paypalECS(true);
			}

			if ( res.cart && res.cart.sum ) {
				console.info('Есть первоначальная сумма корзины : '+res.cart.sum);
				ENTER.OrderModel.cartSum = res.cart.sum;
			}

			ENTER.OrderModel.deliveryTypes(res.deliveryTypes);
			ENTER.OrderModel.lifeGift(res.lifeGift || false);
			ENTER.OrderModel.oneClick(res.oneClick || false);
			ENTER.OrderModel.prepareData(true);

			if ( ENTER.OrderModel.paypalECS() &&
				window.docCookies.hasItem('chTypeBtn_paypalECS') && 
				window.docCookies.hasItem('chPoint_paypalECS') &&
				window.docCookies.hasItem('chTypeId_paypalECS') && 
				window.docCookies.hasItem('chStetesPriority_paypalECS') ) {

				console.info('PayPal ECS включен. Необходимо применить параметры из cookie');

				ENTER.OrderModel.deliveryTypesButton = window.docCookies.getItem('chTypeBtn_paypalECS');
				ENTER.OrderModel.choosenPoint( window.docCookies.getItem('chPoint_paypalECS') );
				ENTER.OrderModel.choosenDeliveryTypeId = window.docCookies.getItem('chTypeId_paypalECS');
				ENTER.OrderModel.statesPriority = JSON.parse( window.docCookies.getItem('chStetesPriority_paypalECS') );

				separateOrder( ENTER.OrderModel.statesPriority );
			}


			if ( 1 === res.deliveryTypes.length ) {
				data = res.deliveryTypes[0];
				firstPoint =  ENTER.OrderModel.orderDictionary.getFirstPointByState( data.states[0] ) || data.id;

				console.log('Обнаружен только 1 способ доставки: ' + data.name +' — выбираем его.');
				console.log('Выбран первый пункт* доставки:');
				console.log( firstPoint );

				ENTER.OrderModel.statesPriority = data.states;
				ENTER.OrderModel.deliveryTypesButton = 'method_' + data.id;
				ENTER.OrderModel.choosenDeliveryTypeId = data.id;
				ENTER.OrderModel.choosenPoint( firstPoint );
				separateOrder( ENTER.OrderModel.statesPriority );
			}
		},

		selectPointOnBaloon = function selectPointOnBaloon( event ) {
			console.log('selectPointOnBaloon');
			console.log(event);

			console.log($(this).data('pointid'));
			console.log($(this).data('parentbox'));

			ENTER.OrderModel.selectPoint({
				id: $(this).data('pointid'),
				parentBoxToken: $(this).data('parentbox')				
			});

			return false;
		},

		/**
		 * Аналитика загрузки страницы orders/new
		 * 
		 * @param	{Object}	orderData		Данные о заказе
		 */
		analyticsStep_1 = function analyticsStep1( orderData ) {
			console.info('analyticsStep_1');

			var
				totalPrice = 0,
				totalQuan = 0,
                basketProd = [],

				toKISS = {},

				product;
			// end of vars

			for ( product in orderData.products ) {
				totalPrice += orderData.products[product].price;
				totalQuan += orderData.products[product].quantity;

                basketProd.push(
                    {
                    'id':       orderData.products[product].id,
                    'name':     orderData.products[product]['name'],
                    'price':    orderData.products[product].price,
                    'quantity': orderData.products[product].quantity
                    }
                );
			}

			toKISS = {
				'Checkout Step 1 SKU Quantity': totalQuan,
				'Checkout Step 1 SKU Total': totalPrice,
				'Checkout Step 1 Order Type': 'cart order'
			};

			if ( typeof _gaq !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'New order', 'Items', totalQuan]);
			}

			if ( typeof _kmq !== 'undefined' ) {
				_kmq.push(['record', 'Checkout Step 1', toKISS]);
			}

            // ActionPay Analytics:
            window.APRT_DATA = window.APRT_DATA || {};
            window.APRT_DATA.pageType = 5; // оформление заказа (после корзины и до последней страницы заказа)
            window.APRT_DATA.orderInfo = window.APRT_DATA.orderInfo || {};
            window.APRT_DATA.orderInfo.totalPrice = totalPrice;
            window.APRT_DATA.basketProducts = basketProd;

        };
	// end of functions


	console.log(ENTER.OrderModel);

	renderOrderData( serverData );
	analyticsStep_1( serverData );

	body.on('click', '.shopchoose', selectPointOnBaloon);

}(this, this.document, this.jQuery, this.ENTER, this.ko));