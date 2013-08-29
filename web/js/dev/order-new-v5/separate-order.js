/**
 * Получение данных с сервера
 * Разбиение заказа
 * Модель knockout
 * Аналитика
 *
 * @author	Zaytsev Alexandr
 */
;(function( global ) {
	console.info('Логика разбиения заказа для оформления заказа v.5');

	var serverData = $('#jsOrderDelivery').data('value');

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
	var separateOrder = function separateOrder( statesPriority ) {

		var preparedProducts = {},
			productInState = [],
			productsToNewBox = [],
			choosenPointForBox = null,
			token = null,
			nowState = null,
			nowProduct = null,

			discounts = global.OrderModel.orderDictionary.orderData.discounts;
		// end of vars


		// очищаем объект созданых блоков, удаляем блоки из модели
		global.OrderModel.createdBox = {};
		global.OrderModel.deliveryBoxes.removeAll();

		// Маркируем выбранный способ доставки
		$('#'+global.OrderModel.deliveryTypesButton).attr('checked','checked');
			
		// Обнуляем общую стоимость заказа
		global.OrderModel.totalSum(0);

		// Обнуляем блоки с доставкой на дом и генерируем событие об этом
		global.OrderModel.hasHomeDelivery(false);
		$('body').trigger('orderdeliverychange',[false]);

		// Добавляем купоны
		global.OrderModel.couponsBox(discounts);


		/**
		 * Перебор states в выбранном способе доставки в порядке приоритета
		 */
		for ( var i = 0, len = statesPriority.length; i < len; i++ ) {
			nowState = statesPriority[i];

			console.info('перебирем метод '+nowState);

			productsToNewBox = [];

			if ( !global.OrderModel.orderDictionary.hasDeliveryState(nowState) ) {
				console.info('для метода '+nowState+' нет товаров');

				continue;
			}

			productInState = global.OrderModel.orderDictionary.getProductFromState(nowState);
			
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
				productsToNewBox.push( global.OrderModel.orderDictionary.getProductById(nowProduct) );
			}

			if ( productsToNewBox.length ) {
				choosenPointForBox = ( global.OrderModel.orderDictionary.hasPointDelivery(nowState) ) ? global.OrderModel.choosenPoint() : 0;

				token = nowState+'_'+choosenPointForBox;

				if ( global.OrderModel.createdBox[token] !== undefined ) {
					// Блок для этого типа доставки в этот пункт уже существует
					global.OrderModel.createdBox[token].addProductGroup( productsToNewBox );
				}
				else {
					// Блока для этого типа доставки в этот пункт еще существует
					global.OrderModel.createdBox[token] = new DeliveryBox( productsToNewBox, nowState, choosenPointForBox, global.OrderModel );
				}
			}
		}

		console.info('Созданные блоки:');
		console.log(global.OrderModel.createdBox);

		// выбираем URL для проверки купонов - первый видимый купон
		global.OrderModel.couponUrl( $('.bSaleList__eItem:visible .jsCustomRadio').eq(0).val() );

		if ( preparedProducts.length !== global.OrderModel.orderDictionary.orderData.products.length ) {
			console.warn('не все товары были обработаны');
		}
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
				map = new CreateMap('pointPopupMap', global.OrderModel.popupWithPoints().points, $('#mapInfoBlock'));

				$(element).lightbox_me({
					centered: true,
					onClose: function() {
						console.info('закрываем');
						val(false);
					}
				});
			}
			else {
				$('#pointPopupMap').empty();
				$(element).trigger('close');
			}
		}
	};

	/**
	 * Кастомный бинд отображения методов оплаты
	 */
	ko.bindingHandlers.paymentMethodVisible = {
		update: function( element, valueAccessor ) {
			var val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),
				node = $(element),
				maxSum = parseInt($(element).data('value')['max-sum'], 10);
			// end of vars

			if ( isNaN(maxSum) ) {
				return;
			}

			if ( maxSum < unwrapVal ) {
				node.hide();
			}
			else {
				node.show();
			}
		}
	};

	ko.bindingHandlers.couponsVisible = {
		update: function( element, valueAccessor ) {
			var val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),

				node = $(element),
				fieldNode = node.find('.mSaleInput'),
				buttonNode = node.find('.mSaleBtn'),
				titleNode = node.find('.bTitle'),

				emptyBlock = node.find('.bSaleData__eEmptyBlock');
			// end of vars

			$('.bSaleList__eItem').removeClass('hidden');

			for ( var i = unwrapVal.length - 1; i >= 0; i-- ) {
				node.find('.bSaleList__eItem[data-type="'+unwrapVal[i].type+'"]').addClass('hidden');
			}

			if ( $('.bSaleList__eItem.hidden').length === $('.bSaleList__eItem').length ) {
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
	global.OrderModel = {
		updateUrl: $('#jsOrderDelivery').data('url'),
		/**
		 * Флаг завершения обработки данных
		 */
		prepareData: ko.observable(false),

		/**
		 * Флаг открытия окна с выбором точек доставки
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
		 * Хранилище блоков доставки
		 */
		createdBox: {},

		/**
		 * Объект данных для отображения окна с пунктами доставок
		 */
		popupWithPoints: ko.observable({}),

		/**
		 * Общая сумма заказа
		 */
		totalSum: ko.observable(0),

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
		 * Блокер экрана
		 *
		 * @param	{Object}		noti		Объект jQuery блокера экрана
		 * @param	{Function}		block		Функция блокировки экрана. На вход принимает текст который нужно отобразить в окошке блокера
		 * @param	{Function}		unblock		Функция разблокировки экрана. Объект окна блокера удаляется.
		 */
		blockScreen: {
			noti: null,
			block: function( text ) {
				var self = this;

				console.warn('block screen');

				if ( self.noti ) {
					self.unblock();
				}

				self.noti = $('<div>').addClass('noti').html('<div><img src="/images/ajaxnoti.gif" /></br></br> '+ text +'</div>');
				self.noti.appendTo('body');

				self.noti.lightbox_me({
					centered:true,
					closeClick:false,
					closeEsc:false,
					onClose: function() {
						self.noti.remove();
					}
				});
			},

			unblock: function() {
				console.warn('unblock screen');

				this.noti.trigger('close');
			}
		},

		/**
		 * Проверка сертификата
		 */
		checkCoupon: function() {
			console.info('проверяем купон');

			var dataToSend = {
					number: global.OrderModel.couponNumber(),
				},
				url = global.OrderModel.couponUrl();
			// end of vars

			var couponResponceHandler = function couponResponceHandler( res ) {
				global.OrderModel.blockScreen.block('Применяем купон');

				if ( !res.success ) {
					global.OrderModel.couponError(res.error.message);
					global.OrderModel.blockScreen.unblock();

					return;
				}

				global.OrderModel.modelUpdate();
			};

			global.OrderModel.couponError('');

			if ( url === undefined ) {
				console.warn('Не выбран тип сертификата');
				global.OrderModel.couponError('Не выбран тип сертификата');

				return;
			}

			if ( dataToSend.number === undefined || !dataToSend.number.length ) {
				console.warn('Не введен номер сертификата');
				global.OrderModel.couponError('Не введен номер сертификата');

				return;
			}

			$.ajax({
				type: 'POST',
				url: url,
				data: dataToSend,
				success: couponResponceHandler
			});
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

			if ( data.parentBoxToken ) {
				console.log(global.OrderModel.createdBox[data.parentBoxToken]);
				global.OrderModel.createdBox[data.parentBoxToken].selectPoint.apply(global.OrderModel.createdBox[data.parentBoxToken],[data]);

				return false;
			}

			// Сохраняем приоритет методов доставок
			global.OrderModel.statesPriority = global.OrderModel.tmpStatesPriority;

			// Сохраняем выбранную приоритетную точку доставки
			global.OrderModel.choosenPoint(data.id);

			// Скрываем окно с выбором точек доставок
			global.OrderModel.showPopupWithPoints(false);

			// Разбиваем на подзаказы
			separateOrder( global.OrderModel.statesPriority );

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
			var priorityState = data.states[0],
				checkedInputId = event.target.htmlFor;
			// end of vars

			if ( $('#'+checkedInputId).attr('checked') ) {
				return false;
			}

			global.OrderModel.deliveryTypesButton = checkedInputId;
			global.OrderModel.tmpStatesPriority = data.states;
			global.OrderModel.choosenDeliveryTypeId = data.id;

			// если для приоритетного метода доставки существуют пункты доставки, то пользователю необходимо выбрать пункт доставки, если нет - то приравниваем идентификатор пункта доставки к 0
			if ( global.OrderModel.orderDictionary.hasPointDelivery(priorityState) ) {
				global.OrderModel.popupWithPoints({
					header: data.description,
					points: global.OrderModel.orderDictionary.getAllPointsByState(priorityState)
				});

				global.OrderModel.showPopupWithPoints(true);

				return false;
			}

			// Сохраняем приоритет методов доставок
			global.OrderModel.statesPriority = global.OrderModel.tmpStatesPriority;

			// Сохраняем выбранную приоритетную точку доставки (для доставки домой = 0)
			global.OrderModel.choosenPoint(0);

			// Разбиваем на подзаказы
			separateOrder( global.OrderModel.statesPriority );

			return false;
		},

		/**
		 * Обновление данных
		 */
		modelUpdate: function() {
			console.info('обновление данных с сервера');

			var updateResponceHandler = function updateResponceHandler( res ) {
				renderOrderData(res);
				global.OrderModel.blockScreen.unblock();

				separateOrder( global.OrderModel.statesPriority );
			};

			$.ajax({
				type: 'GET',
				url: global.OrderModel.updateUrl,
				success: updateResponceHandler
			});
		},

		/**
		 * Удаление товара
		 * 
		 * @param  {[type]} data  [description]
		 * @param  {[type]} event [description]
		 */
		deleteItem: function( data ) {
			console.info('удаление');

			global.OrderModel.blockScreen.block('Удаляем');

			var itemDeleteAnalytics = function itemDeleteAnalytics() {
					var products = global.OrderModel.orderDictionary.products;
						totalPrice = 0,
						totalQuan = 0,

						toKISS = {};
					// end of vars

					if ( !data.product ) {
						return false;
					}

					for ( var product in products ) {
						totalPrice += product[product].price;
						totalQuan += product[product].quantity;
					}

					toKISS = {
						'Checkout Step 1 SKU Quantity': totalQuan,
						'Checkout Step 1 SKU Total': totalPrice,
					};

					if ( typeof _kmq !== 'undefined' ) {
						_kmq.push(['set', toKISS]);
					}

					if ( typeof _gaq !== 'undefined' ) {
						_gaq.push(['_trackEvent', 'Order card', 'Item deleted']);
					}
				},

				deleteItemResponceHandler = function deleteItemResponceHandler( res ) {
					console.log( res );
					if ( !res.success ) {
						console.warn('не удалось удалить товар');
						global.OrderModel.blockScreen.unblock();

						return false;
					}

					// обновление модели
					global.OrderModel.modelUpdate();

					// запуск аналитики
					if ( typeof _gaq !== 'undefined' || typeof _kmq !== 'undefined' ) {
						itemDeleteAnalytics();
					}
				};
			// end of functions

			console.log(data.deleteUrl);

			$.ajax({
				type: 'GET',
				url: data.deleteUrl,
				success: deleteItemResponceHandler
			});

			return false;
		}
	};

	ko.applyBindings(global.OrderModel);
	/**
	 * ===  END ORDER MODEL ===
	 */

		/**
		 * Показ сообщений об ошибках
		 * 
		 * @param	{String}	msg		Сообщение об ошибке
		 * @return	{Object}			Deferred объект
		 */
	var showError = function showError( msg ) {
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
			800: function( product ) {
				var msg = 'Товар '+product.name+' недоступен для продажи.',

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
				var msg = 'Вы заказали товар '+product.name+' в количестве '+product.quantity+' шт. <br/ >'+product.error.message,

					productErrorIsResolve = $.Deferred();
				// end of vars

				$.when(showError(msg)).then(function() {
					$.ajax({
						type:'GET',
						url: product.setUrl
					}).then(productErrorIsResolve.resolve);
				});

				return productErrorIsResolve.promise();
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

				$.when( productError[code](productsWithError[i]) ).then(function() {
					var newI = i - 1;

					errorCatcher( newI, callback );
				});
			};

			errorCatcher(productsWithError.length - 1, function() {
				console.warn('1 этап закончен');
				if ( res.redirect ) {
					document.location.href = res.redirect;
				}
			});
		},

		/**
		 * Обработка полученных данных
		 * Создание словаря
		 * 
		 * @param	{Object}	res		Данные о заказе
		 */
		renderOrderData = function renderOrderData( res ) {
			if ( !res.success ) {
				console.warn('Данные содержат ошибки');
				console.log(res.error);
				allErrorHandler(res);

				return false;
			}

			console.info('Данные с сервера получены');

			global.OrderModel.orderDictionary = new OrderDictionary(res);

			global.OrderModel.deliveryTypes(res.deliveryTypes);
			global.OrderModel.prepareData(true);
		},

		selectPointOnBaloon = function selectPointOnBaloon( event ) {
			console.log('selectPointOnBaloon');
			console.log(event);

			console.log($(this).data('pointid'));
			console.log($(this).data('parentbox'));

			global.OrderModel.selectPoint({
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

			var totalPrice = 0,
				totalQuan = 0,

				toKISS = {};
			// end of vars

			for ( var product in orderData.products ) {
				totalPrice += orderData.products[product].price;
				totalQuan += orderData.products[product].quantity;
			}

			toKISS = {
				'Checkout Step 1 SKU Quantity': totalQuan,
				'Checkout Step 1 SKU Total': totalPrice,
				'Checkout Step 1 Order Type': 'cart order'
			};

			console.log(toKISS)

			if ( typeof _gaq !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'New order', 'Items', totalQuan]);
			}

			if ( typeof _kmq !== 'undefined' ) {
				_kmq.push(['record', 'Checkout Step 1', toKISS]);
			}
		};
	// end of functions

	$('body').on('click', '.shopchoose', selectPointOnBaloon);

	renderOrderData( serverData );

	analyticsStep_1( serverData );
}(this));