/**
 * Создает блок доставки.
 * Если для товара недоступна выбранная точка доставки, создается новый блок
 * Стоимость блока расчитывается из суммы всех товаров.
 * Стоимость доставки считается минимальная из стоимостей доставок всех товаров в блоке
 *
 * @author	Zaytsev Alexandr
 * @this	{DeliveryBox}
 * 
 * @param	{Array}			products			Массив продуктов которые необходимо добавить в блок
 * @param	{String}		state				Текущий метод доставки для блока
 * @param	{Number}		choosenPointForBox	Выбранная точка доставки
 * 
 * @param	{Object}		createdBox			Объект со созданными блоками доставки
 * @param	{Object}		OrderModel			Модель оформления заказа
 * 
 * @constructor
 */
function DeliveryBox( products, state, choosenPointForBox, createdBox, OrderModel ) {
	console.info('Cоздание блока доставки '+state+' для '+choosenPointForBox);

	var self = this;

	self.OrderModel = OrderModel;
	self.createdBox = createdBox;
	self.deliveryPoint = choosenPointForBox;

	// Продукты в блоке
	self.products = [];
	// Общая стоимость блока
	self.fullPrice = 0;
	// Метод доставки
	self.state = state;
	// Название метода доставки
	self.deliveryName = self.OrderModel.orderDictionary.getNameOfState(state);
	// Стоимость доставки. Берем максимально возможное значение, чтобы сравнивая с ним находить минимальное
	self.deliveryPrice = Number.POSITIVE_INFINITY;
	// Выбранная дата доставки
	self.choosenDate = ko.observable();

	self.hasPointDelivery = self.OrderModel.orderDictionary.hasPointDelivery(state);
	console.log(self.hasPointDelivery)
	self.choosenInterval = ko.observable();

	// Массив всех доступных дат для блока
	self.allDatesForBlock = ko.observableArray([]);

	self.addProductGroup(products);

	self.OrderModel.deliveryBoxes.push(self);
}

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
		tempProductArray = [];
	// end of vars


	/**
	 * Если для продукта нет доставки в выбранный пункт доставки, то нужно создать новый блок доставки
	 */
	if ( !product.deliveries[self.state].hasOwnProperty(self.deliveryPoint) ) {
		console.warn('Для товара '+product.id+' нет пункта доставки '+self.deliveryPoint+'. Необходимо создать новый блок');

		firstAvaliblePoint = self._getFirstPropertyName(product.deliveries[self.state]);
		token = self.state+'_'+firstAvaliblePoint;

		tempProductArray.push(product);

		if ( self.createdBox[token] !== undefined ) {
			// Блок для этого типа доставки в этот пункт уже существует. Добавляем продукт в блок
			self.createdBox[token].addProductGroup( product );
		}
		else {
			// Блока для этого типа доставки в этот пункт еще существует
			self.createdBox[token] = new DeliveryBox( tempProductArray, self.state, firstAvaliblePoint, self.createdBox, self.OrderModel );
		}

		return;
	}

	// Определение стоимости доставки. Если стоимость доставки данного товара ниже стоимости доставки блока, то стоимость доставки блока становится равной стоимости доставки данного товара
	productDeliveryPrice = parseInt(product.deliveries[self.state][self.deliveryPoint].price, 10);
	self.deliveryPrice = ( self.deliveryPrice > productDeliveryPrice ) ? productDeliveryPrice : self.deliveryPrice;

	// Добавляем стоимость продукта к общей стоимости блока доставки
	self.fullPrice += product.sum;

	self.products.push({
		name: product.name,
		price: product.sum,
		quantity: product.quantity,
		deleteUrl: product.deleteUrl,
		productUrl: product.url,
		productImg: product.image,
		deliveries: product.deliveries[self.state]
	});
};

/**
 * Добавление нескольких товаров в блок доставки
 * После добавления продуктов запускает получение общей даты доставки
 * 
 * @this	{DeliveryBox}
 * 
 * @param	{Array}			products	Продукты которые нужно добавить
 */
DeliveryBox.prototype.addProductGroup = function( products ) {
	// добавляем товары в блок
	for ( var i = products.length - 1; i >= 0; i-- ) {
		this._addProduct(products[i]);
	}

	this.calculateDate();
};

/**
 * Получение человекочитаемого названия дня недели
 * 
 * @param	{Number}	dateFromModel	Номер дня недели
 * @return	{String}					Человекочитаемый день недели
 */
DeliveryBox.prototype._getNameDayOfWeek = function( dayOfWeek ) {
	var days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];

	return days[dayOfWeek];
};

/**
 * Проверка даты, доступна ли дата доставки для всех товаров в боксе
 *
 * @this	{DeliveryBox}
 *
 * @param	{Number}	checkTS	Таймштамп даты, которую необходимо проверить
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
		nowProductDates = self.products[i].deliveries[self.deliveryPoint].dates;

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

DeliveryBox.prototype.selectDay = function( data ) {
	Console.info('выбор даты');
	console.log(data);
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
		todayTS = self.OrderModel.orderDictionary.getToday(),
		nowProductDates = null,
		nowTS = null;

	console.log('Сегодняшняя дата с сервера '+todayTS);

	if ( !self.products.length ) {
		console.warn('в блоке нет товаров');

		return;
	}

	/**
	 * Перебираем даты в первом товаре
	 */
	nowProductDates = self.products[0].deliveries[self.deliveryPoint].dates;

	for ( var i = 0, len = nowProductDates.length; i < len; i++ ) {
		nowTS = nowProductDates[i].value;

		if ( self._hasDateInAllProducts(nowTS) && nowTS >= todayTS ) {
			nowProductDates[i].avalible = true;
			nowProductDates[i].humanDayOfWeek = self._getNameDayOfWeek(nowProductDates[i].dayOfWeek);
			nowProductDates[i].selectDay = self.selectDay;


			self.allDatesForBlock.push(nowProductDates[i]);
		}
	}

	// выбираем ближайшую доступную дату
	self.choosenDate( self.allDatesForBlock()[0] );
	// выбираем первый интервал
	self.choosenInterval( self.choosenDate().intervals[0] );

	self.makeCalendar();
};

/**
 * Создание календаря, округление до целых недель
 *
 * @this	{DeliveryBox}
 */
DeliveryBox.prototype.makeCalendar = function() {
	console.info('Cобираем календарь');
	var self = this,
		addCountDays = 0,
		dayOfWeek = null,
		tmpDay = {};
	// end of vars

	if ( self.allDatesForBlock()[0].dayOfWeek !== 1 ) {
		addCountDays = ( self.allDatesForBlock()[0].dayOfWeek === 0 ) ? 6 : self.allDatesForBlock()[0].dayOfWeek - 1;
		console.log('первый день в календаре не понедельник. Нужно добавить '+addCountDays+' дней');

		for ( var i = addCountDays; i > 0; i-- ) {
			dayOfWeek = self.allDatesForBlock()[0].dayOfWeek - 1;
			
			tmpDay = {
				avalible: false,
				humanDayOfWeek: self._getNameDayOfWeek(dayOfWeek),
				dayOfWeek: dayOfWeek,
				day: 0
			};

			self.allDatesForBlock.unshift(tmpDay);
		}
	}

	if ( self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek !== 0 ) {
		addCountDays = 7 - self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek;

		console.log('последний день в календаре не воскресение, нужно добавить '+addCountDays);

		for ( var j = addCountDays; j > 0; j-- ) {
			dayOfWeek = ( self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek + 1 === 7 ) ? 0 : self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek + 1;
			
			tmpDay = {
				avalible: false,
				humanDayOfWeek: self._getNameDayOfWeek(dayOfWeek),
				dayOfWeek: dayOfWeek,
				day: 0
			};

			self.allDatesForBlock.push(tmpDay);
		}
	}
}