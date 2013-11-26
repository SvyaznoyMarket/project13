;(function( ENTER ) {
	var userUrl = ENTER.config.pageConfig.userUrl,
		constructors = ENTER.constructors;
	// end of vars

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
			
			console.info('Cоздание блока доставки '+state+' для '+choosenPointForBox);

			var i, self = this;

			// Уникальность продуктов в этом типе доставки
			//self.isUnique = isUnique || false;
			self.isUnique = window.OrderModel.orderDictionary.isUniqueDeliveryState(state);
			// Токен блока
			self.token = state+'_'+choosenPointForBox;
			/*if (self.isUnique) {
				self.token += self.addUniqueSuffix();
			}*/

			// Продукты в блоке
			self.products = [];
			// Общая стоимость товаров в блоке
			self.fullPrice = 0;
			// Полная стоимость блока с учетом доставки
			self.totalBlockSum = 0;
			// Метод доставки
			self.state = state;
			// Название метода доставки
			self.deliveryName = window.OrderModel.orderDictionary.getNameOfState(state);
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
			self.hasPointDelivery = window.OrderModel.orderDictionary.hasPointDelivery(state);

			// Массив всех доступных дат для блока
			self.allDatesForBlock = ko.observableArray([]);
			// Массив всех точек доставок
			self.pointList = [];

			// Название пункта — магазина, постамата или тп
			//self.point_name = ''; // здесь не нужно это поле здесь (но в ядро передавать нужно)


			// Текст на кнопки смены точки доставки
			self.changePointButtonText = window.OrderModel.orderDictionary.getChangeButtonText(state);


			if ( self.hasPointDelivery && !window.OrderModel.orderDictionary.getPointByStateAndId(self.state, choosenPointForBox) ) {
				// Доставка в выбранный пункт
				console.info('есть точки доставки для выбранного метода доставки, но выбранная точка не доступна для этого метода доставки. Берем первую точку для выбранного метода доставки');

				self.choosenPoint( window.OrderModel.orderDictionary.getFirstPointByState(self.state) );
			}
			else if ( self.hasPointDelivery ) {
				// Доставка в первый пункт для данного метода доставки
				console.info('есть точки доставки для выбранного метода доставки, и выбранная точка доступна для этого метода доставки');

				self.choosenPoint( window.OrderModel.orderDictionary.getPointByStateAndId(self.state, choosenPointForBox) );
			}
			else {
				console.info('для выбранного метода доставки не нужна точка доставки');

				// Передаем в модель, что есть блок с доставкой домой и генерируем событие об этом
				window.OrderModel.hasHomeDelivery(true);
				$('body').trigger('orderdeliverychange',[true]);
			}

			// Отступ слайдера дат
			self.calendarSliderLeft = ko.observable(0);

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

			window.OrderModel.deliveryBoxes.push(self);
		}


		/**
		 * Делаем список общих для всех товаров в блоке точек доставок для данного метода доставки
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype._makePointList = function() {
			console.info('Создание списка точек доставки');

			var self = this,
				res = true,
				tmpPoint = null;
			// end of vars

			/**
			 * Перебираем точки доставки для первого товара
			 */
			for ( var point in self.products[0].deliveries[self.state] ) {

				/**
				 * Перебираем все товары в блоке, проверяя доступна ли данная точка доставки для них
				 */
				for ( var i = self.products.length - 1; i >= 0; i-- ) {
					res = self.products[i].deliveries[self.state].hasOwnProperty(point);

					if ( !res ) {
						break;
					}
				}

				if ( res ) {
					// Точка достаки доступна для всех товаров в блоке
					tmpPoint = window.OrderModel.orderDictionary.getPointByStateAndId(self.state, point);
					self.pointList.push( tmpPoint );
				}
			}

			console.log('Точки доставки созданы');
			console.log(self.pointList);
		};


		/**
		 * Генерирует случайное окончание (суффикс) для строки
		 *
		 * @param       {string}      str
		 * @returns     {string}      str
		 */
		DeliveryBox.prototype.addUniqueSuffix = function ( str ) {
			str = str || '';
			var randSuff;
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
				choosenBlock = null;
			// end of vars

			if ( window.OrderModel.hasDeliveryBox(newToken) ) {
				choosenBlock = global.OrderModel.getDeliveryBoxByToken(newToken);
				choosenBlock.addProductGroup( self.products );

				window.OrderModel.removeDeliveryBox(self.token);
			}
			else {

				if (self.isUnique) {
					newToken += self.addUniqueSuffix();
				}
				console.info('удаляем старый блок');
				console.log('старый токен '+self.token);
				console.log('новый токен '+newToken);

				self.token = newToken;
				self.choosenPoint(window.OrderModel.orderDictionary.getPointByStateAndId(self.state, data.id));
				console.log(window.OrderModel.deliveryBoxes());

				if ( window.OrderModel.paypalECS() ) {
					console.info('PayPal ECS включен. Необходимо сохранить выбранную точку доставки в cookie');

					window.docCookies.setItem('chPoint_paypalECS', data.id, 10 * 60);
				}
			}

			window.OrderModel.showPopupWithPoints(false);

			return false;
		};
		
		/**
		 * Показ окна с пунктами доставки
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype.changePoint = function( ) {
			var self = this;

			// запонимаем токен бокса которому она принадлежит
			for ( var i = self.pointList.length - 1; i >= 0; i-- ) {
				self.pointList[i].parentBoxToken = self.token;
			}

			window.OrderModel.popupWithPoints({
				header: 'Выберите точку доставки',
				points: self.pointList
			});

			window.OrderModel.showPopupWithPoints(true);

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

				choosenBlock = null,

				tmpProduct = {};
			// end of vars


			/**
			 * Если для продукта нет доставки в выбранный пункт доставки, то нужно создать новый блок доставки
			 */
			if ( !product.deliveries[self.state].hasOwnProperty(self.choosenPoint().id) ) {
				console.warn('Для товара '+product.id+' нет пункта доставки '+self.choosenPoint().id+' Необходимо создать новый блок');

				firstAvaliblePoint = self._getFirstPropertyName(product.deliveries[self.state]);
				token = self.state+'_'+firstAvaliblePoint;

				tempProductArray.push(product);

				if ( window.OrderModel.hasDeliveryBox(token) ) {
					console.log('Блок для этого типа доставки в этот пункт уже существует. Добавляем продукт в блок');

					choosenBlock = global.OrderModel.getDeliveryBoxByToken(token);
					choosenBlock.addProductGroup( product );
					window.OrderModel.removeDeliveryBox(self.token);
				}
				else {
					console.log('Блока для этого типа доставки в этот пункт еще существует');

					new DeliveryBox( tempProductArray, self.state, firstAvaliblePoint );
				}

				return;
			}

			// Определение стоимости доставки. Если стоимость доставки данного товара выше стоимости доставки блока, то стоимость доставки блока становится равной стоимости доставки данного товара
			productDeliveryPrice = parseInt(product.deliveries[self.state][self.choosenPoint().id].price, 10);
			self.deliveryPrice = ( self.deliveryPrice < productDeliveryPrice ) ? productDeliveryPrice : self.deliveryPrice;

			tmpProduct = {
				id: product.id,
				name: product.name,
				price: (product.sum) ? product.sum : product.price,
				quantity: product.quantity,
				deleteUrl: product.deleteUrl,
				setUrl: product.setUrl,
				productUrl: product.url,
				productImg: (product.image) ? product.image : product.productImg,
				deliveries: {}
			};

			if ( self.isUnique && (product.oldQuantity - 1) > 0 ) {
				console.log('Переделываем deleteUrl:');
				console.log(tmpProduct.deleteUrl);
				tmpProduct.deleteUrl = tmpProduct.deleteUrl.replace('delete-', 'add-'); // TODO cart.product.set изменмить Url
				tmpProduct.deleteUrl += '?quantity=' + ( product.oldQuantity - 1 );
				console.log(tmpProduct.deleteUrl);
			}

			tmpProduct.deliveries[self.state] = product.deliveries[self.state];

			// Добавляем стоимость продукта к общей стоимости блока доставки
			self.fullPrice = ENTER.utils.numMethods.sumDecimal(tmpProduct.price, self.fullPrice);

			self.products.push(tmpProduct);
		};

		/**
		 * Перерасчет общей стоимости заказа
		 */
		DeliveryBox.prototype.updateTotalPrice = function() {
			console.info('Перерасчет общей стоимости заказа');

			var self = this,
				nowTotalSum = window.OrderModel.totalSum();
			// end of vars

			self.totalBlockSum = ENTER.utils.numMethods.sumDecimal(self.fullPrice, self.deliveryPrice);
			nowTotalSum = ENTER.utils.numMethods.sumDecimal(self.totalBlockSum, nowTotalSum);
			window.OrderModel.totalSum(nowTotalSum);

			console.log(window.OrderModel.totalSum());
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
			var self = this;
			console.info('добавляем товары в блок');
			// добавляем товары в блок
			for ( var i = products.length - 1; i >= 0; i-- ) {
				console.log(i+'ый пошел...');
				console.log(products[i]);
				self._addProduct(products[i]);
			}

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
			var days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];

			return days[dayOfWeek];
		};

		/**
		 * Получение полного человекочитаемого названия дня недели
		 * 
		 * @param	{Number}	dateFromModel	Номер дня недели
		 * @return	{String}					Человекочитаемый день недели
		 */
		DeliveryBox.prototype._getFullNameDayOfWeek = function( dayOfWeek ) {
			var days = ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];

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
			var self = this,
				nowProductDates = null,
				nowTS = null,

				res = true;
			// end of vars

			/**
			 * Перебор всех продуктов в блоке
			 */
			for (var i = self.products.length - 1; i >= 0; i--) {
				nowProductDates = self.products[i].deliveries[self.state][self.choosenPoint().id].dates;

				/**
				 * Перебор всех дат доставок в блоке
				 */
				for ( var j = 0, len = nowProductDates.length; j < len; j++ ) {
					nowTS = nowProductDates[j].value;

					if ( nowTS === checkTS ) {
						res = true;

						break;
					}
					else {
						res = false;
					}
				}

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
			var self = this;
			
			if ( !data.avalible ) {
				return false;
			}

			// Если включен PayPal ECS необходимо сохранить выбранную дату в cookie
			if ( window.OrderModel.paypalECS() ) {
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
			console.info('Вычисление общей даты для продуктов в блоке');

			var self = this,
				todayTS = window.OrderModel.orderDictionary.getToday(),
				nowProductDates = null,
				nowTS = null,

				newToken = '',
				tempProduct = null,
				tempProductArray = [],
				dateFromCookie = null,
				intervalFromCookie = null;
			// end of vars

			console.log('Сегодняшняя дата с сервера '+todayTS);

			/**
			 * Перебираем даты в первом товаре
			 */
			nowProductDates = self.products[0].deliveries[self.state][self.choosenPoint().id].dates;

			for ( var i = 0, len = nowProductDates.length; i < len; i++ ) {
				nowTS = nowProductDates[i].value;

				if ( self._hasDateInAllProducts(nowTS) && nowTS >= todayTS ) {
					nowProductDates[i].avalible = true;
					nowProductDates[i].humanDayOfWeek = self._getNameDayOfWeek(nowProductDates[i].dayOfWeek);

					self.allDatesForBlock.push(nowProductDates[i]);
				}
			}

			if ( !self.allDatesForBlock().length ) {
				console.warn('нет общих дат для блока. Необходимо разделить продукты в блоке');

				tempProduct = self.products.pop();
				tempProductArray.push(tempProduct);
				newToken = self.state + '_' + self.choosenPoint().id + '_' + self.addUniqueSuffix();;
				console.log('новый токен '+newToken);
				console.log(self);

				new DeliveryBox( tempProductArray, self.state, self.choosenPoint().id );

				self.calculateDate();
			}

			/**
			 * Выбираем ближайшую доступную дату
			 * Если включен PayPal ECS и уже есть сохраненная дата в куки - берем ее из куки
			 */
			if ( window.OrderModel.paypalECS() && window.docCookies.hasItem('chDate_paypalECS') ) {
				console.info('PayPal ECS включен. Необходимо взять выбранную дату из cookie');

				dateFromCookie = window.docCookies.getItem('chDate_paypalECS');
				self.choosenDate( JSON.parse(dateFromCookie) );
			}
			else {
				self.choosenDate( self.allDatesForBlock()[0] );
			}

			/**
			 * Человекочитаемы день недели
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

		/**
		 * Создание календаря, округление до целых недель
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype.makeCalendar = function() {
			console.info('Создание календаря, округление до целых недель');

			var self = this,
				addCountDays = 0,
				dayOfWeek = null,
				tmpDay = {},
				tmpVal = null,
				tmpDate = null,

				ONE_DAY = 24*60*60*1000,

				i, j, k;
			// end of vars
			
			/**
			 * Проверка дат на разрывы  вчислах
			 * Если меются разрывы в числах - заполнить пробелы датами
			 */
			for ( k = 0; k <= self.allDatesForBlock().length - 1; k++ ) {
				if ( self.allDatesForBlock()[k + 1] === undefined ) {
					console.info('Следущая дата последняя. заканчиваем цикл');
					
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

					console.log('предыдущая дата была ' + new Date(self.allDatesForBlock()[k].value).getDate() + ' новая дата вклинилась ' + tmpDate.getDate() + ' следущая дата ' + new Date(self.allDatesForBlock()[k + 1].value).getDate());
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
						avalible: false,
						humanDayOfWeek: self._getNameDayOfWeek(tmpDate.getDay()),
						dayOfWeek: tmpDate.getDay(),
						day: tmpDate.getDate()
					};

					self.allDatesForBlock.push(tmpDay);
				}
			}
		};


		/**
		 * =========== CALENDAR SLIDER ===================
		 */
		DeliveryBox.prototype.calendarLeftBtn = function() {
			var self = this,
				nowLeft = parseInt(self.calendarSliderLeft(), 10);
			// end of vars
			
			nowLeft += 380;
			self.calendarSliderLeft(nowLeft);
		};

		DeliveryBox.prototype.calendarRightBtn = function() {
			var self = this,
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

}(window.ENTER));
