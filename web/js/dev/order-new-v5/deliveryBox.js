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
 * @param	{Object}		OrderModel			Модель оформления заказа
 * 
 * @constructor
 */
function DeliveryBox( products, state, choosenPointForBox, OrderModel ) {
	console.info('Cоздание блока доставки '+state+' для '+choosenPointForBox);

	var self = this;

	self.OrderModel = OrderModel;
	self.token = state+'_'+choosenPointForBox;

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

	self.choosenNameOfWeek = ko.observable();
	// Выбранная точка доставки
	self.choosenPoint = ko.observable({id:choosenPointForBox});
	// Выбранный интервал доставки
	self.choosenInterval = ko.observable();

	self.showPopupWithPoints = ko.observable(false);

	// Есть ли доступные точки доставки
	self.hasPointDelivery = self.OrderModel.orderDictionary.hasPointDelivery(state);

	// Массив всех доступных дат для блока
	self.allDatesForBlock = ko.observableArray([]);
	// Массив всех точек доставок
	self.pointList = [];

	if ( self.hasPointDelivery ) {
		// Доставка в выбранный пункт
		self.choosenPoint( self.OrderModel.orderDictionary.getPointByStateAndId(self.state, choosenPointForBox) );
	}
	else {
		// Передаем в модель, что есть блок с доставкой домой и генерируем событие об этом
		self.OrderModel.hasHomeDelivery(true);
		$('body').trigger('orderdeliverychange',[true]);
	}

	// Отступ слайдера дат
	self.calendarSliderLeft = ko.observable(0);

	self.addProductGroup(products);

	self.OrderModel.deliveryBoxes.push(self);
}

/**
 * Делаем список общих для всех товаров в блоке точек доставок для данного метода доставки
 *
 * @this	{DeliveryBox}
 */
DeliveryBox.prototype._makePointList = function() {
	var self = this,
		res = true,
		tmpPoint = null;
	// end of vars

	/**
	 * Перебираем точки доставки для первого товара
	 */
	for ( var point in self.products[0].deliveries ) {

		/**
		 * Перебираем все товары в блоке, проверяя доступна ли данная точка доставки для них
		 */
		for ( var i = self.products.length - 1; i >= 0; i-- ) {
			res = self.products[i].deliveries.hasOwnProperty(point);

			if ( !res ) {
				break;
			}
		}

		if ( res ) {
			// Точка достаки доступна для всех товаров в блоке
			tmpPoint = self.OrderModel.orderDictionary.getPointByStateAndId(self.state, point);
			self.pointList.push( tmpPoint );
		}
	}
};

/**
 * Смена пункта доставки. Переименовываем token блока
 * Удаляем старый блок из массива блоков и добавлчем туда новый с новым токеном
 * Если уже есть блок с таким токеном, необходиом добавить товары из текущего блока в него
 *
 * @this	{DeliveryBox}
 * 
 * @param	{Object}	data	Данные о пункте доставки
 */
DeliveryBox.prototype.selectPoint = function( data ) {
	var self = this,
		newToken = self.state+'_'+data.id;

	if ( self.OrderModel.createdBox[newToken] !== undefined ) {
		self.OrderModel.createdBox[newToken].addProductGroup(self.products);

		delete self.OrderModel.createdBox[self.token];
	}
	else {
		console.info('удаляем старый блок');
		console.log('старый токен '+self.token);
		console.log('новый токен '+newToken);
		self.OrderModel.createdBox[newToken] = self.OrderModel.createdBox[self.token];
		delete self.OrderModel.createdBox[self.token];

		self.token = newToken;
		self.choosenPoint(self.OrderModel.orderDictionary.getPointByStateAndId(self.state, data.id));
		console.log(self.OrderModel.createdBox);
	}

	self.OrderModel.showPopupWithPoints(false);

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
	
	self.OrderModel.popupWithPoints({
		header: 'Выберите точку доставки',
		points: self.pointList
	});

	self.OrderModel.showPopupWithPoints(true);

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
		tempProductArray = [];
	// end of vars


	/**
	 * Если для продукта нет доставки в выбранный пункт доставки, то нужно создать новый блок доставки
	 */
	if ( !product.deliveries[self.state].hasOwnProperty(self.choosenPoint().id) ) {
		console.warn('Для товара '+product.id+' нет пункта доставки '+self.choosenPoint().id+' Необходимо создать новый блок');

		firstAvaliblePoint = self._getFirstPropertyName(product.deliveries[self.state]);
		token = self.state+'_'+firstAvaliblePoint;

		tempProductArray.push(product);

		if ( self.OrderModel.createdBox[token] !== undefined ) {
			// Блок для этого типа доставки в этот пункт уже существует. Добавляем продукт в блок
			self.OrderModel.createdBox[token].addProductGroup( product );
		}
		else {
			// Блока для этого типа доставки в этот пункт еще существует
			self.OrderModel.createdBox[token] = new DeliveryBox( tempProductArray, self.state, firstAvaliblePoint, self.OrderModel.createdBox, self.OrderModel );
		}

		return;
	}

	// Определение стоимости доставки. Если стоимость доставки данного товара ниже стоимости доставки блока, то стоимость доставки блока становится равной стоимости доставки данного товара
	productDeliveryPrice = parseInt(product.deliveries[self.state][self.choosenPoint().id].price, 10);
	self.deliveryPrice = ( self.deliveryPrice > productDeliveryPrice ) ? productDeliveryPrice : self.deliveryPrice;

	// Добавляем стоимость продукта к общей стоимости блока доставки
	self.fullPrice += product.sum;

	self.products.push({
		id: product.id,
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
 * Перерасчет общей стоимости заказа
 */
DeliveryBox.prototype.updateTotalPrice = function() {
	var self = this,
		nowTotalSum = self.OrderModel.totalSum();
	// end of vars

	nowTotalSum += self.fullPrice + self.deliveryPrice;
	self.OrderModel.totalSum(nowTotalSum);
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

	// добавляем товары в блок
	for ( var i = products.length - 1; i >= 0; i-- ) {
		self._addProduct(products[i]);
	}

	self.calculateDate();
	self.updateTotalPrice();

	if ( self.hasPointDelivery ) {
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
	var days = ['воскресение', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];

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
		nowProductDates = self.products[i].deliveries[self.choosenPoint().id].dates;

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
	nowProductDates = self.products[0].deliveries[self.choosenPoint().id].dates;

	for ( var i = 0, len = nowProductDates.length; i < len; i++ ) {
		nowTS = nowProductDates[i].value;

		if ( self._hasDateInAllProducts(nowTS) && nowTS >= todayTS ) {
			nowProductDates[i].avalible = true;
			nowProductDates[i].humanDayOfWeek = self._getNameDayOfWeek(nowProductDates[i].dayOfWeek);

			self.allDatesForBlock.push(nowProductDates[i]);
		}
	}

	// выбираем ближайшую доступную дату
	self.choosenDate( self.allDatesForBlock()[0] );
	self.choosenNameOfWeek( self._getFullNameDayOfWeek(self.allDatesForBlock()[0].dayOfWeek) );
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
	var self = this,
		addCountDays = 0,
		dayOfWeek = null,
		tmpDay = {},
		tmpVal = null,

		ONE_DAY = 24*60*60*1000;
	// end of vars

	if ( self.allDatesForBlock()[0].dayOfWeek !== 1 ) {
		addCountDays = ( self.allDatesForBlock()[0].dayOfWeek === 0 ) ? 6 : self.allDatesForBlock()[0].dayOfWeek - 1;
		tmpVal = self.allDatesForBlock()[0].value;

		for ( var i = addCountDays; i > 0; i-- ) {
			dayOfWeek = self.allDatesForBlock()[0].dayOfWeek - 1;
			tmpVal -= ONE_DAY;

			tmpDay = {
				avalible: false,
				humanDayOfWeek: self._getNameDayOfWeek(dayOfWeek),
				dayOfWeek: dayOfWeek,
				day: new Date(tmpVal).getDate()
			};

			self.allDatesForBlock.unshift(tmpDay);
		}
	}

	if ( self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek !== 0 ) {
		addCountDays = 7 - self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek;
		tmpVal = self.allDatesForBlock()[self.allDatesForBlock().length - 1].value;

		for ( var j = addCountDays; j > 0; j-- ) {
			dayOfWeek = ( self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek + 1 === 7 ) ? 0 : self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek + 1;
			tmpVal += ONE_DAY;

			tmpDay = {
				avalible: false,
				humanDayOfWeek: self._getNameDayOfWeek(dayOfWeek),
				dayOfWeek: dayOfWeek,
				day: new Date(tmpVal).getDate()
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


ko.bindingHandlers.calendarSlider = {
	update: function( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {
		var slider = $(element),
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