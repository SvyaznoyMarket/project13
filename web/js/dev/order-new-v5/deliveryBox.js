;(function ( window, document, $, ENTER, ko ) {
	var
		constructors = ENTER.constructors,
		utils = ENTER.utils,
		OrderModel,
		pageConfig = ENTER.config.pageConfig,
		prepayment = pageConfig.prepayment;
	// end of vars

	console.info('deliveryBox.js init');
	console.log(ENTER.OrderModel);

	constructors.DeliveryBox = (function() {
		'use strict';
	
		/**
		 * Создает блок доставки.
		 * Если для товара недоступна выбранная точка доставки, создается новый блок
		 * Стоимость блока расчитывается из суммы всех товаров.
		 * Стоимость доставки считается минимальная из стоимостей доставок всех товаров в блоке
		 *
		 * @author	Zaytsev Alexandr
		 * 
		 * @this	{DeliveryBox}
		 * 
		 * @param	{Array}			products			Массив продуктов которые необходимо добавить в блок
		 * @param	{String}		state				Текущий метод доставки для блока
		 * @param	{Number}		choosenPointForBox	Выбранная точка доставки
		 * 
		 * @constructor
		 */
		function DeliveryBox( products, state, choosenPointForBox ) {
			
			// enforces new
			if ( !(this instanceof DeliveryBox) ) {
				return new DeliveryBox(products, state, choosenPointForBox);
			}
			// constructor body
			
			console.info('Cоздание блока доставки %s (state) для %s (choosenPointForBox)', state, choosenPointForBox, this);


			OrderModel = ENTER.OrderModel;

			var 
				self = this;
			// end of vars

			// Уникальность продуктов в этом типе доставки
			//self.isUnique = isUnique || false;
			self.isUnique = OrderModel.orderDictionary.isUniqueDeliveryState(state);
			// Токен блока
			self.token = state+'_'+choosenPointForBox;
			/*if (self.isUnique) {
				self.token += self.addUniqueSuffix();
			}*/

			// Продукты в блоке
			self.products = [];
			// Общая стоимость товаров в блоке
			self.fullPrice = ko.observable(0);
			// Полная стоимость блока с учетом доставки
			self.totalBlockSum = 0;
			// Метод доставки
			self.state = state;
			// Название метода доставки
			self.deliveryName = OrderModel.orderDictionary.getNameOfState(state);
			// Стоимость доставки. Берем минимально возможное значение, чтобы сравнивая с ним находить максимальное
			self.deliveryPrice = Number.NEGATIVE_INFINITY;

			// Выбранная дата доставки
			self.choosenDate = ko.observable();

			self.choosenNameOfWeek = ko.observable();
			// Выбранная точка доставки
			self.choosenPoint = ko.observable({id:choosenPointForBox});
			// Выбранный интервал доставки
			self.choosenInterval = ko.observable();

			self.showPopupWithPoints = ko.observable(false);

			// Есть ли доступные точки доставки
			self.hasPointDelivery = OrderModel.orderDictionary.hasPointDelivery(state);

			// Стоимость заказа равна или больше напр. 100 тыс. руб.
			self.isExpensiveOrder = ko.computed(function(){
                if ( prepayment.enabled ) {
                    // отображение/скрытие блока предоплаты
                    return prepayment.priceLimit <= (parseInt(self.fullPrice(), 10) + parseInt(self.deliveryPrice, 10)) ? true : false;
                } else return false;
            });

			// Есть ли в заказе товар, требующий предоплату (шильдик предоплата)
			self.hasProductWithPrepayment = false;

			// Массив всех доступных дат для блока
			self.allDatesForBlock = ko.observableArray([]);
			// Массив всех точек доставок
			self.pointList = [];

			// Название пункта — магазина, постамата или тп
			//self.point_name = ''; // здесь не нужно это поле здесь (но в ядро передавать нужно)


			// Текст на кнопки смены точки доставки
			self.changePointButtonText = OrderModel.orderDictionary.getChangeButtonText(state);


			if ( self.hasPointDelivery && !OrderModel.orderDictionary.getPointByStateAndId(self.state, choosenPointForBox) ) {
				// Доставка в выбранный пункт
				console.info('есть точки доставки для выбранного метода доставки, но выбранная точка не доступна для этого метода доставки. Берем первую точку для выбранного метода доставки');

				self.choosenPoint( OrderModel.orderDictionary.getFirstPointByState(self.state) );
			}
			else if ( self.hasPointDelivery ) {
				// Доставка в первый пункт для данного метода доставки
				console.info('есть точки доставки для выбранного метода доставки, и выбранная точка доступна для этого метода доставки');

				self.choosenPoint( OrderModel.orderDictionary.getPointByStateAndId(self.state, choosenPointForBox) );
			}
			else {
				console.info('для выбранного метода доставки не нужна точка доставки');

				// Передаем в модель, что есть блок с доставкой домой и генерируем событие об этом
				OrderModel.hasHomeDelivery(true);
				$('body').trigger('orderdeliverychange',[true]);
			}

			// Отступ слайдера дат
			self.calendarSliderLeft = ko.observable(0);

            try {
                console.groupCollapsed('Таблица продуктов для блока %s', self.token);
                var consoleProducts = [];
                for (var a in products) {
                    var temp = products[a],
                        chPoint = Object.keys(products[a].deliveries[self.state])[0];
                    temp.choosenPoint = chPoint;
                    temp.deliveries_types = JSON.stringify(Object.keys(products[a].deliveries));
                    temp.firstDate = products[a].deliveries[self.state][chPoint].dates[0].name;
                    temp.lastDate = products[a].deliveries[self.state][chPoint].dates[products[a].deliveries[self.state][chPoint].dates.length - 1].name;
                    consoleProducts.push(temp);
                }
                console.table(consoleProducts, ['id', 'name', 'price', 'sum', 'quantity', 'stock', 'isPrepayment', 'choosenPoint', 'deliveries_types', 'firstDate', 'lastDate']);
            } catch (e) {
                console.debug('Delivery\'s box self.state: %s, self.choosenPoint.id: %s', self.state, self.choosenPoint().id);
                console.debug('Products', products);
                console.error(e);
            } finally {
                console.groupEnd();
            }

			self.addProductGroup(products);

			if ( self.products.length === 0 ) {
				// если после распределения в блоке не осталось товаров
				console.warn('в блоке '+self.token+' не осталось товаров');

				return;
			}

			/*if ( 'pickpoint' === state ) {
				// Получим и сохраним в названии пункта название выбранного пикпойнта:
				/*for ( i = self.pointList.length - 1; i >= 0; i-- ) {
					if ( choosenPointForBox == self.pointList[i].id ) {
						self.point_name = self.pointList[i].point_name;
					}
				}* ///old
				// название и так храниться в choosPoint
			}*/

			OrderModel.deliveryBoxes.push(self);
		}


		/**
		 * Делаем список общих для всех товаров в блоке точек доставок для данного метода доставки
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype._makePointList = function() {
			console.info('Создание списка точек доставки');

			var
				self = this,
				res = true,
				tmpPoint = null,
				point,
				i, j;
			// end of vars

			/**
			 * Перебираем точки доставки для первого товара
			 */
			for ( point in self.products[0].deliveries[self.state] ) {

				/**
				 * Перебираем все товары в блоке, проверяя доступна ли данная точка доставки для них
				 */
				for ( i = self.products.length - 1; i >= 0; i-- ) {
					res = self.products[i].deliveries[self.state].hasOwnProperty(point);

					if ( !res ) {
						break;
					}
				}

				if ( res ) {
					// Точка достаки доступна для всех товаров в блоке
					tmpPoint = OrderModel.orderDictionary.getPointByStateAndId(self.state, point);

					if ( self.isUniquePointIdInPointList(point, self.pointList) ) {
						console.warn('Add point ' + point + ' to pointList');
						self.pointList.push( tmpPoint );
					}
				}
				else {
					for ( j in self.pointList ) {
						if (undefined === self.pointList[j]['id']) continue;

						if ( point === self.pointList[j]['id'] ) {
							console.warn('Delete point ' + point + ' from pointList');
							self.pointList.splice(j);
						}
					}
				}
			}

			console.log('Точки доставки созданы');
			console.log(self.pointList);
		};

		/**
		 * Проверяем наличие точки доставки (pointId) в массиве pointList
		 *
		 * @param	{String}	pointId		Идентификатор точки доставки
		 * @param 	{Array}		pointList	Массив точек доставок
		 * @returns {boolean}
		 */
		DeliveryBox.prototype.isUniquePointIdInPointList = function ( pointId, pointList ) {
			var
				defaultValue = true,
				point;
			//end of vars

			if ( undefined === pointId || undefined === pointList ) {
				return defaultValue;
			}

			for ( point in pointList ) {
				if ( pointList[point]['id'] == pointId ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Генерирует случайное окончание (суффикс) для строки
		 *
		 * @param       {string}      str
		 * @returns     {string}      str
		 */
		DeliveryBox.prototype.addUniqueSuffix = function ( str ) {
			var
				randSuff;
			// end of vars

			str = str || '';

			//randSuff = new Date().getTime();
			randSuff = Math.floor((Math.random() * 10000) + 1);
			str += '_' + randSuff;

			return str;
		};


		/**
		 * Смена пункта доставки. Переименовываем token блока
		 * Удаляем старый блок из массива блоков и добавяем туда новый с новым токеном
		 * Если уже есть блок с таким токеном, необходиом добавить товары из текущего блока в него
		 *
		 * @this	{DeliveryBox}
		 * 
		 * @param	{Object}	data	Данные о пункте доставки
		 */
		DeliveryBox.prototype.selectPoint = function( data ) {
			var self = this,
				newToken = self.state+'_'+data.id,
				choosenBlock = null,
				productIds = [],
				i;
			// end of vars

			if ( OrderModel.hasDeliveryBox(newToken) ) {
				// запоминаем массив ids продуктов
				for ( i = self.products.length - 1; i >= 0; i-- ) {
					self.products[i].id && productIds.push(self.products[i].id);
				}

				if ( !self._hasProductsAlreadyAdded(productIds) ) {
					choosenBlock = OrderModel.getDeliveryBoxByToken(newToken);
					choosenBlock.addProductGroup( self.products );
					OrderModel.removeDeliveryBox(self.token);
				}
			}
			else {

				if (self.isUnique) {
					newToken += self.addUniqueSuffix();
				}
				console.info('удаляем старый блок');
				console.log('старый токен '+self.token);
				console.log('новый токен '+newToken);

				self.token = newToken;
				self.choosenPoint(OrderModel.orderDictionary.getPointByStateAndId(self.state, data.id));
				ENTER.OrderModel.choosenPoint(data.id);

				choosenBlock = OrderModel.getDeliveryBoxByToken(newToken);
				choosenBlock.allDatesForBlock([]);
				choosenBlock.calculateDate();

				console.log(OrderModel.deliveryBoxes());

				if ( OrderModel.paypalECS() ) {
					console.info('PayPal ECS включен. Необходимо сохранить выбранную точку доставки в cookie');

					window.docCookies.setItem('chPoint_paypalECS', data.id, 10 * 60);
				}
			}

			OrderModel.showPopupWithPoints(false);

			return false;
		};
		
		/**
		 * Показ окна с пунктами доставки
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype.changePoint = function( ) {
			var
				self = this,
				i;
			// end of vars

			// запонимаем токен бокса которому она принадлежит
			for ( i = self.pointList.length - 1; i >= 0; i-- ) {
				self.pointList[i].parentBoxToken = self.token;
			}

			OrderModel.popupWithPoints({
				header: 'Выберите точку доставки',
				points: self.pointList
			});

			OrderModel.showPopupWithPoints(true);

			return false;
		};


		/**
		 * Получить имя первого свойства объекта
		 *
		 * @this	{DeliveryBox}
		 * 
		 * @param	{Object}	obj		Объект у которого необходимо получить первое свойство
		 * @return	{Object}			Возвращает свойство объекта
		 */
		DeliveryBox.prototype._getFirstPropertyName = function( obj ) {
			for ( var i in obj ) {
				return i;
			}
		};

		/**
		 * Добавление продукта в блок доставки
		 *
		 * @this	{DeliveryBox}
		 * 
		 * @param	{Object}		product		Продукт который нужно добавить
		 */
		DeliveryBox.prototype._addProduct = function( product ) {
			var self = this,
				productDeliveryPrice = null,
				token = null,
				firstAvaliblePoint = null,
				tempProductArray = [],
				nowTotalSum,
				deletedBlock,
				newState,

				choosenBlock = null,

				tmpProduct = {};
			// end of vars

			if ( self._hasProductsAlreadyAdded([product.id]) ) {
				return;
			}

			/**
			 * Если для продукта нет доставки в выбранный пункт доставки, то нужно создать новый блок доставки
			 */
			if ( !product.deliveries[self.state].hasOwnProperty(self.choosenPoint().id) && !/_shipped$/.test(self.token) ) {
				console.warn('Для товара '+product.id+' нет пункта доставки '+self.choosenPoint().id+' Необходимо создать новый блок');

				firstAvaliblePoint = self._getFirstPropertyName(product.deliveries[self.state]);
				token = self.state+'_'+firstAvaliblePoint;

				tempProductArray.push(product);

				if ( OrderModel.hasDeliveryBox(token) ) {
					console.log('Блок для этого типа доставки в этот пункт уже существует. Добавляем продукт в блок' , token);

					choosenBlock = OrderModel.getDeliveryBoxByToken(token);
					//choosenBlock.addProductGroup( tempProductArray ); //массив на вход нужен // добавим ниже
					//OrderModel.removeDeliveryBox(self.token); // не находит и не удаляет никогда

					// Удаляем неполный блок и добавляем (push) полный
					//deletedBlock = OrderModel.deliveryBoxes.pop(); // удалит последний (обычно он и есть неполный)
					deletedBlock = OrderModel.removeDeliveryBox(token); // удалит по токену нужный

					// пересчитываем и обновляем общую сумму всех блоков
					nowTotalSum = OrderModel.totalSum() - deletedBlock.fullPrice() - choosenBlock.deliveryPrice;
					OrderModel.totalSum(nowTotalSum);

					choosenBlock.addProductGroup( tempProductArray ); //массив на вход нужен
					OrderModel.deliveryBoxes.push( choosenBlock );
				}
				else {
                    /* приоритет разбивки по типу доставки */
                    new DeliveryBox( tempProductArray, self.state, firstAvaliblePoint );

                    /* приоритет разбивки по магазину
					console.info('Блока для этого типа доставки в этот пункт еще не существует');
					console.warn('Необходимо попробовать найти другую доставку в тот же магазин');
					newState = OrderModel.orderDictionary.getStateToProductByDeliveryID(product.id, self.choosenPoint().id);
					console.info('newState complete');
					console.log(newState);

					if ( newState ) {
						console.log('Найден вариант доставки в тот же магазин но способом ' + newState);
						new DeliveryBox( tempProductArray, newState, self.choosenPoint().id );
					} else {
						console.warn('Не найден вариант доставки в тот же магазин. Будет выбран тот же способ доставки, но первый доступный магазин');
						new DeliveryBox( tempProductArray, self.state, firstAvaliblePoint );
					}
					*/
				}

				return;
			}

/*            if (product.stock == 9223372036854776000 && self.token != 'standart_furniture_1') {
                console.log('Есть продукт от поставщика, необходимо добавить в другой блок доставки: ', product);
                token = self.state+'_'+'1';
                tempProductArray.push(product);
                if (!OrderModel.hasDeliveryBox(token)) new DeliveryBox(tempProductArray, self.state, '1');
                return;
            }*/

			// Определение стоимости доставки. Если стоимость доставки данного товара выше стоимости доставки блока, то стоимость доставки блока становится равной стоимости доставки данного товара
            if (/_shipped$/.test(self.token)) self.choosenPoint({id: 0});

            productDeliveryPrice = parseInt(product.deliveries[self.state][self.choosenPoint().id].price, 10);
            self.deliveryPrice = ( self.deliveryPrice < productDeliveryPrice ) ? productDeliveryPrice : self.deliveryPrice;

			tmpProduct = {
				id: product.id,
				name: product.name,
				price: (product.sum) ? product.sum : product.price,
				quantity: product.quantity,
                stock: product.stock,
				deleteUrl: product.deleteUrl,
				setUrl: product.setUrl,
				productUrl: product.url,
				productImg: (product.image) ? product.image : product.productImg,
				deliveries: {},
				isPrepayment: product.isPrepayment
			};

			if ( self.isUnique && (product.oldQuantity - 1) > 0 ) {
				console.log('Переделываем deleteUrl:');
				console.log(tmpProduct.deleteUrl);
				tmpProduct.deleteUrl = tmpProduct.deleteUrl.replace('delete-', 'add-'); // TODO cart.product.set изменмить Url
				tmpProduct.deleteUrl += '?quantity=' + ( product.oldQuantity - 1 );
				console.log(tmpProduct.deleteUrl);
			}

			if ( tmpProduct.isPrepayment ) {
				self.hasProductWithPrepayment = true;
			}

			tmpProduct.deliveries[self.state] = product.deliveries[self.state];

			// Добавляем стоимость продукта к общей стоимости блока доставки
			self.fullPrice(ENTER.utils.numMethods.sumDecimal(tmpProduct.price, self.fullPrice()));

			self.products.push(tmpProduct);

		};

		/**
		 * Добавлены ли продукти в блок доставки
		 *
		 * @this	{DeliveryBox}
		 *
		 * @param	{Array}		ids		Ids продуктов
		 */
		DeliveryBox.prototype._hasProductsAlreadyAdded = function( ids ) {
			var
				self = this,
				exist = false,
				i;
			// end of vars

			if ( ids === undefined || !ids.length ) {
				return exist;
			}

			for ( i = self.products.length - 1; i >= 0; i-- ) {
				if ( -1 !== $.inArray( self.products[i].id, ids ) ) {
					exist = true;
				}
			}

			return exist;
		};

		/**
		 * Перерасчет общей стоимости заказа
		 */
		DeliveryBox.prototype.updateTotalPrice = function() {
			console.info('Перерасчет общей стоимости заказа');

			var self = this,
				nowTotalSum = OrderModel.totalSum();
			// end of vars

			self.totalBlockSum = ENTER.utils.numMethods.sumDecimal(self.fullPrice(), self.deliveryPrice);
			nowTotalSum = ENTER.utils.numMethods.sumDecimal(self.totalBlockSum, nowTotalSum);
			OrderModel.totalSum(nowTotalSum);

			console.log(OrderModel.totalSum());
		};

		/**
		 * Добавление нескольких товаров в блок доставки
		 * После добавления продуктов запускает получение общей даты доставки и наполнение списка точек доставок, если они доступны
		 * 
		 * @this	{DeliveryBox}
		 * 
		 * @param	{Array}			products	Продукты которые нужно добавить
		 */
		DeliveryBox.prototype.addProductGroup = function( products ) {
			var
				self = this,
                shipped = [],
				i;
			// end of vars
			
			console.groupCollapsed('Добавление товаров в блок, количество товаров: %s', products.length);
			// добавляем товары в блок
            // первая итерация
            if ( !/_shipped$/.test(self.token) ) {
                for (i = products.length - 1; i >= 0; i--) {
                    console.log(i + '-ый товар: ', products[i]);
                    if (products[i].stock != 9223372036854776000) self._addProduct(products[i]);
                    else shipped.push(products[i]);
                }
            }
            // вторая итерация, если есть товары от поставщика
            if ( /_shipped$/.test(self.token) ) {
                for ( i = products.length - 1; i >= 0; i-- ) {
                    console.log(i+'-ый товар: ', products[i]);
                    self._addProduct(products[i]);
                }
            }

            console.groupEnd();

            if (shipped.length && !/_shipped$/.test(self.token) ) new DeliveryBox(shipped, self.state, 'shipped');

			if ( !self.products.length ) {
				console.warn('в блоке '+self.token+' нет товаров');

				return;
			}


			self.calculateDate();
			self.updateTotalPrice();

			if ( self.hasPointDelivery ) {
				console.info('У товара есть точки доставки. Создаем список точек доставки');

				self._makePointList();
			}
		};

		/**
		 * Получение сокращенного человекочитаемого названия дня недели
		 * 
		 * @param	{Number}	dateFromModel	Номер дня недели
		 * @return	{String}					Человекочитаемый день недели
		 */
		DeliveryBox.prototype._getNameDayOfWeek = function( dayOfWeek ) {
			var
				days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
			// end of vars

			return days[dayOfWeek];
		};

		/**
		 * Получение полного человекочитаемого названия дня недели
		 * 
		 * @param	{Number}	dateFromModel	Номер дня недели
		 * @return	{String}					Человекочитаемый день недели
		 */
		DeliveryBox.prototype._getFullNameDayOfWeek = function( dayOfWeek ) {
			var
				days = ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];
			// end of vars

			return days[dayOfWeek];
		};

		/**
		 * Проверка даты, доступна ли дата доставки для всех товаров в боксе
		 *
		 * @this	{DeliveryBox}
		 *
		 * @param	{Number}	checkTS		Таймштамп даты, которую необходимо проверить
		 * 
		 * @return	{Boolean}
		 */
		DeliveryBox.prototype._hasDateInAllProducts = function( checkTS ) {
			var
				self = this,
				nowProductDates = null,
				nowTS = null,

				res = true,

				len,
				i,
				j;
			// end of vars

			/**
			 * Перебор всех продуктов в блоке
			 */
			for ( i = 0; i < self.products.length ; i++ ) {
                //console.groupCollapsed('Проверка существования даты доставки продукта %s в других продуктах', self.products[i].name);

				nowProductDates = self.products[i].deliveries[self.state][self.choosenPoint().id].dates;

				/**
				 * Перебор всех дат доставок в блоке
				 */
				for ( j = 0, len = nowProductDates.length; j < len; j++ ) {
					nowTS = nowProductDates[j].value;
                    //console.log('Diff dates: %s', nowTS - checkTS)

					if ( nowTS === checkTS ) {
						res = true;

						break;
					}
					else {
						res = false;
					}
				}

                //console.groupEnd();

				if ( !res ) {
					break;
				}
			}

			return res;
		};


		/**
		 * Выбор новой даты в календаре
		 *
		 * @this	{DeliveryBox}
		 *
		 * @param	{Object}	data		Данные о новой дате
		 */
		DeliveryBox.prototype.clickCalendarDay = function( data ) {
			var
				self = this;
			// end of vars
			
			if ( !data.avalible ) {
				return false;
			}

			// Если включен PayPal ECS необходимо сохранить выбранную дату в cookie
			if ( OrderModel.paypalECS() ) {
				console.info('PayPal ECS включен. Необходимо сохранить выбранную дату в cookie');

				window.docCookies.setItem('chDate_paypalECS', JSON.stringify(data), 10 * 60);
			}

			self.choosenNameOfWeek(self._getFullNameDayOfWeek(data.dayOfWeek));
			self.choosenDate(data);
		};

		/**
		 * Получение общей ближайшей даты доставки
		 * Заполнение массива общих дат
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype.calculateDate = function() {
			console.info('Вычисление общей даты для продуктов в блоке', this);

			var
				self = this,
				todayTS = OrderModel.orderDictionary.getToday(),
				nowProductDates = null,
				nowTS = null,

				newToken = '',
				tempProduct = null,
                tempDate = null,
				tempProductArray = [],
				dateFromCookie = null,
				chooseDate = null,

				len,
				i;
			// end of vars
            if (!self.products.length) {
                console.warn('Нет продуктов для этого блока, выходим из calculateDate()');
                return;
            }
			console.log('Сегодняшняя дата с сервера '+todayTS);

			/**
			 * Перебираем даты в первом товаре
			 */
            if ( /_shipped$/.test(self.token) ) self.choosenPoint({id: 0});
			nowProductDates = self.products[0].deliveries[self.state][self.choosenPoint().id].dates;

			for ( i = 0, len = nowProductDates.length; i < len; i++ ) {
				nowTS = nowProductDates[i].value;

				if ( self._hasDateInAllProducts(nowTS) && nowTS >= todayTS ) {
					nowProductDates[i].avalible = true;
					nowProductDates[i].humanDayOfWeek = self._getNameDayOfWeek(nowProductDates[i].dayOfWeek);

					self.allDatesForBlock().push(nowProductDates[i]);
				}
			}

			if ( !self.allDatesForBlock().length ) {
				console.warn('Нет общих дат для блока. Необходимо разделить продукты в блоке.');


                /* [start] Новый метод разделения */
                console.info('Выделяем в отдельный блок товары от поставщика');
                var shipperProductArray = [];
                shipperProductArray = self.products.reduceRight(function(previousValue, currentValue, index, arr) {
                    if (9223372036854776000 == currentValue.stock) {
                        arr.splice(index, 1);
                        previousValue.push(currentValue);
                        self.fullPrice(ENTER.utils.numMethods.sumDecimal(self.fullPrice(), -currentValue.price));
                    }
                    return previousValue;
                },[]);
                console.log('Количество товаров от поставщика = %s', shipperProductArray.length);
                if (shipperProductArray.length) {
                    new DeliveryBox( shipperProductArray, self.state, self.choosenPoint().id );
                }
                /* [end] Новый метод разделения */

                tempProductArray = self.products.reduceRight(function(previousValue, currentValue, index, arr) {
                    var currFirstDate = currentValue.deliveries[self.state][self.choosenPoint().id].dates[0].value;
                    if (tempDate === null) tempDate = currFirstDate;
                    if (tempDate == currFirstDate) {
                        arr.splice(index, 1);
                        previousValue.push(currentValue);
                        self.fullPrice(ENTER.utils.numMethods.sumDecimal(self.fullPrice(), -currentValue.price));
                    }
                    return previousValue;
                },[]);

                console.log('Продукты в новом блоке:', tempProductArray);

				newToken = self.state + '_' + self.choosenPoint().id + '_' + self.addUniqueSuffix();
				console.log('новый токен '+newToken);
				console.log(self);

				new DeliveryBox( tempProductArray, self.state, self.choosenPoint().id );

				self.calculateDate();
			}

			/**
			 * Выбираем ближайшую доступную дату
			 * Если включен PayPal ECS и уже есть сохраненная дата в куки - берем ее из куки
			 */
			if ( OrderModel.paypalECS() && window.docCookies.hasItem('chDate_paypalECS') ) {
				console.info('PayPal ECS включен. Необходимо взять выбранную дату из cookie');

				dateFromCookie = window.docCookies.getItem('chDate_paypalECS');
				chooseDate = JSON.parse(dateFromCookie);
			}
			// else if ( self.choosenDate() && self.choosenDate().avalible ) {
			// 	console.warn('======= self.choosenDate() уже была =========');
			// 	chooseDate = self.choosenDate();
			// }
			else {
				chooseDate = self.getFirstAvalibleDate();
			}

			console.log('Выбранная дата (chooseDate) ', chooseDate);
			console.log('Все даты для блока ', self.allDatesForBlock());
			if ( chooseDate && true === chooseDate.avalible ) {
				self.choosenDate( chooseDate );
			}
			else {
				console.warn('Блок недоступен. Вычисление общей даты для продуктов в блоке невозможно. Выходим.');
				return false;
			}

			if ( undefined === typeof(self.choosenDate().intervals) ) {
				console.warn('В блоке нет интервалов. Вычисление даты невозможно. Выходим.');
				return false;
			}

			/**
			 * Человекочитаемый день недели
			 */
			self.choosenNameOfWeek( self._getFullNameDayOfWeek(self.choosenDate().dayOfWeek) );
			/**
			 * Выбираем первый интервал
			 */
			if ( self.choosenDate().intervals.length !== 0 ) {
				self.choosenInterval( self.choosenDate().intervals[0] );
			}

			self.makeCalendar();
		};

		DeliveryBox.prototype.getFirstAvalibleDate = function() {
			var
				self = this,
				i;
			// end of vars

			for ( i = 0; i < self.allDatesForBlock().length; i++ ) {
				if ( self.allDatesForBlock()[i].avalible ) {
					return self.allDatesForBlock()[i];
				}
			}

			return false;
		};

		/**
		 * Создание календаря, округление до целых недель
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype.makeCalendar = function() {
			console.groupCollapsed('Создание календаря, округление до целых недель');

			var
				self = this,
				addCountDays = 0,
				tmpDay = {},
				tmpVal = null,
				tmpDate = null,

				ONE_DAY = 24*60*60*1000,

				i, j, k;
			// end of vars
			
			/**
			 * Проверка дат на разрывы  в числах
			 * Если меются разрывы в числах - заполнить пробелы датами
			 */
			for ( k = 0; k <= self.allDatesForBlock().length - 1; k++ ) {
				if ( self.allDatesForBlock()[k + 1] === undefined ) {
					console.info('Следущая дата последняя. заканчиваем цикл');
					
					break;
				}
				if ( k > 99 ) {
					// Ограничение, дабы 100% не нарваться на вечный цикл
					break;
				}

				tmpDay = {};
				tmpVal = self.allDatesForBlock()[k].value + ONE_DAY;
				tmpDate = new Date(tmpVal);

				if ( tmpVal !== self.allDatesForBlock()[k + 1].value ) {
					tmpDay = {
						value: tmpVal,
						avalible: false,
						humanDayOfWeek: self._getNameDayOfWeek(tmpDate.getDay()),
						dayOfWeek: tmpDate.getDay(),
						day: tmpDate.getDate()
					};

					console.log(
						'предыдущая дата была ' + new Date(self.allDatesForBlock()[k].value).getDate() +
						' новая дата вклинилась ' + tmpDate.getDate() +
						' следущая дата ' + new Date(self.allDatesForBlock()[k + 1].value).getDate()
					);
					self.allDatesForBlock.splice(k + 1, 0, tmpDay);
				}
			}
			

			/**
			 * Проверка первой даты
			 * Если она не понедельник - достроить календарь в начале до понедельника
			 */
			if ( self.allDatesForBlock()[0].dayOfWeek !== 1 ) {
				addCountDays = ( self.allDatesForBlock()[0].dayOfWeek === 0 ) ? 6 : self.allDatesForBlock()[0].dayOfWeek - 1;
				tmpVal = self.allDatesForBlock()[0].value;

				for ( i = addCountDays; i > 0; i-- ) {
					tmpVal -= ONE_DAY;
					tmpDate = new Date(tmpVal);

					tmpDay = {
                        value: tmpVal,
						avalible: false,
						humanDayOfWeek: self._getNameDayOfWeek(tmpDate.getDay()),
						dayOfWeek: tmpDate.getDay(),
						day: tmpDate.getDate()
					};

					self.allDatesForBlock.unshift(tmpDay);
				}
			}

			/**
			 * Проверка последней даты
			 * Если она не воскресенье - достроить календарь в конце до воскресенья
			 */
			if ( self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek !== 0 ) {
				addCountDays = 7 - self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek;
				tmpVal = self.allDatesForBlock()[self.allDatesForBlock().length - 1].value;

				for ( j = addCountDays; j > 0; j-- ) {
					// dayOfWeek = ( self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek + 1 === 7 ) ? 0 : self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek + 1;
					tmpVal += ONE_DAY;
					tmpDate = new Date(tmpVal);

					tmpDay = {
                        value: tmpVal,
						avalible: false,
						humanDayOfWeek: self._getNameDayOfWeek(tmpDate.getDay()),
						dayOfWeek: tmpDate.getDay(),
						day: tmpDate.getDate()
					};

					self.allDatesForBlock.push(tmpDay);
				}
			}

            console.groupEnd();
		};


		/**
		 * =========== CALENDAR SLIDER ===================
		 */
		DeliveryBox.prototype.calendarLeftBtn = function() {
			var
				self = this,
				nowLeft = parseInt(self.calendarSliderLeft(), 10);
			// end of vars
			
			nowLeft += 380;
			self.calendarSliderLeft(nowLeft);
		};

		DeliveryBox.prototype.calendarRightBtn = function() {
			var
				self = this,
				nowLeft = parseInt(self.calendarSliderLeft(), 10);
			// end of vars
			
			nowLeft -= 380;
			self.calendarSliderLeft(nowLeft);
		};
		/**
		 * =========== END CALENDAR SLIDER ================
		 */

	
		return DeliveryBox;
	
	}());

}(this, this.document, this.jQuery, this.ENTER, this.ko));