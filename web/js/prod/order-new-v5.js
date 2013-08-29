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

	if ( self.hasPointDelivery && !self.OrderModel.orderDictionary.getPointByStateAndId(self.state, choosenPointForBox) ) {
		// Доставка в выбранный пункт
		console.info('есть точки доставки для выбранного метода доставки, но выбранная точка не доступна для этого метода доставки. Берем первую точку для выбранного метода доставки');

		self.choosenPoint( self.OrderModel.orderDictionary.getFirstPointByState(self.state) );
	}
	else if ( self.hasPointDelivery ) {
		// Доставка в первый пункт для данного метода доставки
		console.info('есть точки доставки для выбранного метода доставки, и выбранная точка доступна для этого метода доставки');

		self.choosenPoint( self.OrderModel.orderDictionary.getPointByStateAndId(self.state, choosenPointForBox) );
	}
	else {
		console.info('для выбранного метода доставки не нужна точка доставки');

		// Передаем в модель, что есть блок с доставкой домой и генерируем событие об этом
		self.OrderModel.hasHomeDelivery(true);
		$('body').trigger('orderdeliverychange',[true]);
	}

	// Отступ слайдера дат
	self.calendarSliderLeft = ko.observable(0);

	self.addProductGroup(products);

	if ( !self.products.length ) {
		// если после распределения в блоке не осталось товаров
		console.warn('в блоке '+self.token+' неосталось товаров');

		return;
	}

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
			self.OrderModel.createdBox[token] = new DeliveryBox( tempProductArray, self.state, firstAvaliblePoint, self.OrderModel );
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

	if ( !self.products.length ) {
		console.warn('в блоке '+self.token+' нет товаров');

		return;
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
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Работа с кредитными брокерами
 */
;(function( global ){
	var bankWrap = $('.bBankWrap');


	var creditInit = function creditInit() {
		var bankLink  = bankWrap.find('.bBankLink'),
			bankLinkName = bankWrap.find('.bBankLink__eName'),
			select = bankWrap.find('.bSelect'),
			bankField = $('#selectedBank'),
			bankName = bankWrap.find('.bSelectWrap_eText');
		// end of vars

		var selectBank = function selectBank() {
			var chosenBankLink = $("option:selected", select).attr('data-link'),
				chosenBankId = $("option:selected", select).val(),
				chosenBankName = $("option:selected", select).html();
			// end of vars

			bankName.html(chosenBankName);
			bankLinkName.html(chosenBankName);
			bankField.val(chosenBankId);
			bankLink.attr('href', chosenBankLink);
		};

		$("option", select).eq(0).attr('selected','selected');

		select.change(selectBank);
		selectBank();

		DirectCredit.init( $('#jsCreditBank').data('value'), $('#creditPrice') );
	};
	
	if ( bankWrap.length ) {
		creditInit();
	}
}(this));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Вспомагательная обертка вокруг данных приходящих с сервера
 * Явлется неким драйвером для доступа к данным
 *
 * @author	Zaytsev Alexandr
 * @this	{OrderDictionary}
 *
 * @param	{Object}	orderData	Данные о доставке
 *
 * @constructor
 */
function OrderDictionary( orderData ) {
	this.orderData = orderData;

	// alias
	this.serverTime = this.orderData.time;
	this.deliveryTypes = this.orderData.deliveryTypes;
	this.deliveryStates = this.orderData.deliveryStates;
	this.pointsByDelivery = this.orderData.pointsByDelivery;
	this.products = this.orderData.products;
}

/**
 * Получить имя метода доставки
 *
 * @this	{OrderDictionary}
 * 
 * @param	{String}	state	Метод доставки
 * @return	{String}			Имя метода доставки
 */
OrderDictionary.prototype.getNameOfState = function( state ) {
	if ( !this.hasDeliveryState(state) ) {
		console.warn('Не найден метод доставки '+state);
		
		return false;
	}

	return this.deliveryStates[state].name;
};

/**
 * Получить таймштамп от которого нужно вести расчет
 *
 * @this	{OrderDictionary}
 * 
 * @return	{Number}	Таймштамп
 */
OrderDictionary.prototype.getToday = function() {
	return this.serverTime;
};

/**
 * Есть ли метод доставки
 *
 * @this	{OrderDictionary}
 * 
 * @param	{String}	state	Метод доставки
 * @return	{Boolean}
 */
OrderDictionary.prototype.hasDeliveryState = function( state ) {
	return this.deliveryStates.hasOwnProperty(state);
};

/**
 * Есть ли для метода доставки пункты доставки
 *
 * @this	{OrderDictionary}
 * 
 * @param	{String}	state	Метод доставки
 * @return	{Boolean}
 */
OrderDictionary.prototype.hasPointDelivery = function( state ) {
	return this.pointsByDelivery.hasOwnProperty(state);
};

/**
 * Получить данные о точке доставки по методу доставки и идетификатору точки доставки
 *
 * @this	{OrderDictionary}
 * 
 * @param	{String}	state		Метод доставки
 * @param	{String}	pointId		Идентификатор точки доставки
 * @return	{Object}				Данные о точке доставки
 */
OrderDictionary.prototype.getPointByStateAndId = function( state, pointId ) {
	var points = this.getAllPointsByState(state),
		findedPoint = null;
	// end of vars
	
	pointId = pointId+'';

	for ( var i = points.length - 1; i >= 0; i-- ) {
		if ( points[i].id === pointId ) {
			return window.cloneObject(points[i]);
		}
	}

	return false;
};

/**
 * Получение первой точки доставки для метода доставки
 *
 * @this	{OrderDictionary}
 * 
 * @param	{String}	state		Метод доставки
 * @return	{Object}				Данные о точке доставки
 */
OrderDictionary.prototype.getFirstPointByState = function( state ) {
	var points = this.getAllPointsByState(state),
		findedPoint = null;
	// end of vars

	return window.cloneObject(points[0]);
};

/**
 * @this	{OrderDictionary}
 *
 * @param	{String}	state	Метод доставки
 */
OrderDictionary.prototype.getAllPointsByState = function( state ) {
	var pointName = this.pointsByDelivery[state];

	return this.orderData[pointName];
};


/**
 * Получить спискок продуктов для которых доступен данный метод доставки
 * 
 * @this	{OrderDictionary}
 * 
 * @param	{String}	state	Метод доставки
 * @return	{Array}				Массив идентификаторов продуктов
 */
OrderDictionary.prototype.getProductFromState = function( state ) {
	if ( !this.hasDeliveryState(state) ) {
		console.warn('Не найден метод доставки '+state);

		return false;
	}

	return this.deliveryStates[state].products;
};

/**
 * Получить данные о продукте по идентификатору продукта
 *
 * @this	{OrderDictionary}
 * 
 * @param	{Number}	productId	Идентификатор продукта
 * @return	{Object}				Данные о продукте
 */
OrderDictionary.prototype.getProductById = function( productId ) {
	if ( !this.products.hasOwnProperty(productId) ) {
		console.warn('Такого продукта не найдено');

		return false;
	}

	return this.products[productId];
};
 
 
/** 
 * NEW FILE!!! 
 */
 
 
	/* Sertificate */
;(function( global ) {
	if( !$('#paymentMethod-10').length ) {
		console.warn('нет метода оплаты сертификатом');

		return false;
	}

	var sertificateWrap = $('#paymentMethod-10').parent(),
		code = sertificateWrap.find('.mCardNumber'),
		pin = sertificateWrap.find('.mCardPin'),
		fieldsWrap = sertificateWrap.find('.bPayMethodAction'),
		urlCheck = fieldsWrap.attr('data-url');
	// end of vars


	var SertificateCard = (function() {

		var checked = false,
			processTmpl = 'processBlock';
		// end of vars

		var getCode = function getCode() {
				return code.val().replace(/[^0-9]/g,'');
			},

			getPIN = function getPIN() {
				return pin.val().replace(/[^0-9]/g,'');
			},

			getParams = function getParams() {
				return { code: getCode() , pin: getPIN() };
			},

			isActive = function isActive() {
				if( checked && ( getCode() !== '' ) && getCode().length === 14 && ( getPIN() !== '' ) && getPIN().length === 4) {
					return true;
				}

				return false;
			},

			checkCard = function checkCard() {
				if ( pin.val().length !== 4) {
					console.warn('пин код мал');
					return false;
				}
				
				if ( !code.val().length ) {
					console.warn('номер карты не введен');
					return false;
				}

				setProcessingStatus( 'mOrange', 'Проверка по номеру карты' );
				$.post( urlCheck, getParams(), function( data ) {
					if( !('success' in data) ) {
						return false;
					}

					if( !data.success ) {
						var err = ( typeof(data.error) !== 'undefined' ) ? data.error : 'ERROR';

						setProcessingStatus( 'mRed', err );

						return false;
					}

					setProcessingStatus( 'mGreen', data.data );
				});
				// pin.focus()
			},

			setProcessingStatus = function setProcessingStatus( status, data ) {
				console.info('setProcessingStatus');

				var blockProcess = $('.process').first(),
					options = { typeNum: status };

				if( !blockProcess.hasClass('picked') ) {
					blockProcess.remove();
				}

				switch( status ) {
					case 'mOrange':
						options.text = data;
						checked = false;

						break;
					case 'mRed':
						options.text = 'Произошла ошибка: ' + data;
						checked = false;

						break;
					case 'mGreen':
						if( 'activated' in data ) {
							options.text = 'Карта '+ data.code + ' на сумму ' + data.sum + ' активирована!';
						}
						else {
							options.text = 'Карта '+ data.code + ' имеет номинал ' + data.sum;
						}

						checked = true;

						break;
				}

				fieldsWrap.after( tmpl( processTmpl, options) );

				if ( typeof data['activated']  !== 'undefined' ) {
					$('.process').first().addClass('picked');
				}
			};
		// end of function

		return {
			checkCard: checkCard,
			setProcessingStatus: setProcessingStatus,
			isActive: isActive,
			getCode: getCode,
			getPIN: getPIN
		};
	})();

	code.bind('change',function() {
		SertificateCard.checkCard();
	});

	pin.bind('change',function() {
		SertificateCard.checkCard();
	});

	pin.mask('9999', { completed: SertificateCard.checkCard, placeholder: '*' } );
}(this));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Валидация формы. Отправка на сервер. Аналитика
 */
;(function( global ){
	var orderValidator = {},
		subwayArray = $('#metrostations').data('name'),

		// form fields
		firstNameField = $('#order_recipient_first_name'),
		lastNameField = $('#order_recipient_last_name'),
		emailField = $('#order_recipient_email'),
		phoneField = $('#order_recipient_phonenumbers'),
		subwayField = $('#order_address_metro'),
		metroIdFiled = $('#order_subway_id'),
		streetField = $('#order_address_street'),
		buildingField = $('#order_address_building'),
		sclub = $('#sclub-number'),
		paymentRadio = $('.jsCustomRadio[name="order[payment_method_id]"]'),
		qiwiPhone = $('#qiwi-phone'),
		orderAgreed = $('#order_agreed'),

		// complete button
		orderCompleteBtn = $('#completeOrder'),


		// analytics data
		ajaxStart = null,
		ajaxStop = null,
		ajaxDelta = null,

		/**
		 * Конфигурация валидатора
		 * @type {Object}
		 */
		validationConfig = {
			fields: [
				{
					fieldNode: firstNameField,
					require: true,
					customErr: 'Введите имя получателя',
					validateOnChange: true
				},
				{
					fieldNode: emailField,
					validBy: 'isEmail',
					customErr: 'Некорректно введен e-mail',
					validateOnChange: true
				},
				{
					fieldNode: phoneField,
					require: true,
					customErr: 'Некорректно введен телефон',
					validateOnChange: true
				},
				{
					fieldNode: orderAgreed,
					require: true,
					customErr: 'Необходимо согласие',
				},
				{
					fieldNode: paymentRadio,
					require: true,
					customErr: 'Необходимо выбрать метод оплаты'
				}
			]
		},

		subwayAutocompleteConfig = {
			source: subwayArray,
			appendTo: '#metrostations',
			minLength: 2,
			select : function( event, ui ) {
				metroIdFiled.val(ui.item.val);
			}
		};
	// end of vars
	
	orderValidator = new FormValidator(validationConfig);


		/**
		 * Показ сообщений об ошибках при оформлении заказа
		 * 
		 * @param	{String}	msg		Сообщение которое необходимо показать пользователю
		 */
	var showError = function showError( msg, callback ) {
			var content = '<div class="popupbox width290">' +
					'<div class="font18 pb18"> '+msg+'</div>'+
					'</div>' +
					'<p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>',
				block = $('<div>').addClass('popup').html(content);
			// end of vars
			
			block.appendTo('body');

			var errorPopupCloser = function() {
				block.trigger('close');
				block.remove();

				if ( callback !== undefined ) {
					callback();
				}

				return false;
			};

			block.lightbox_me({
				centered:true,
				onClose: errorPopupCloser
			});

			block.find('.closePopup').bind('click', errorPopupCloser);
		},

		/**
		 * Обработка ошибок формы
		 * 
		 * @param	{Object}	formError	Объект с полем содержащим ошибки
		 */
		formErrorHandler = function formErrorHandler( formError ) {
			console.warn('Ошибка в поле');
			
			var field = $('[name="order['+formError.field+']"]');

			var clearError = function clearError() {
				orderValidator._unmarkFieldError($(this));
			};

			orderValidator._markFieldError(field, formError.message);
			field.bind('focus', clearError);
		},
	
		/**
		 * Обработка ошибок из ответа сервера
		 */
		serverErrorHandler = {
			default: function( res ) {
				console.log('обработчик ошибки');

				if ( res.error && res.error.message ) {
					showError(res.error.message, function() {
						document.location.href = res.redirect;
					});

					return;
				}

				document.location.href = res.redirect;
			},

			0: function( res ) {
				console.warn('обработка ошибок формы')
				var formError = null;

				if ( res.redirect ) {
					showError(res.error.message, function(){
						document.location.href = res.redirect;
					});

					return;
				}

				showError(res.error.message);

				for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
					formError = res.form.error[i];
					console.warn(formError);
					formErrorHandler(formError);
				}

				$.scrollTo($('.mError').eq(0), 500, {offset:-15});
			},

			743: function( res ) {
				showError(res.error.message);
			}
		},

		/**
		 * Аналитика завершения заказа
		 */
		completeAnalytics = function completeAnalytics() {
			if ( typeof _gaq !== 'undefined') {
				for ( var i in global.OrderModel.createdBox ) {
					_gaq.push(['_trackEvent', 'Order card', 'Completed', 'выбрана '+global.OrderModel.choosenDeliveryTypeId+' доставят '+global.OrderModel.createdBox[i].state]);
				}

				_gaq.push(['_trackEvent', 'Order complete', global.getKeysLength(global.OrderModel.createdBox), global.OrderModel.orderDictionary.products.length]);
				_gaq.push(['_trackTiming', 'Order complete', 'DB response', ajaxDelta]);
			}

			if ( typeof yaCounter10503055 !== 'undefined' ) {
				yaCounter10503055.reachGoal('\orders\complete');
			}
		},

		/**
		 * Обработка ответа от сервера
		 *
		 * @param	{Object}	res		Ответ сервера
		 */
		processingResponse = function processingResponse( res ) {
			console.info('данные отправлены. получен ответ от сервера');
			
			ajaxStop = new Date().getTime();
			ajaxDelta = ajaxStop - ajaxStart;

			console.log(res);

			if ( !res.success ) {
				console.log('ошибка оформления заказа');

				global.OrderModel.blockScreen.unblock();

				if ( serverErrorHandler.hasOwnProperty(res.error.code) ) {
					console.log('есть обработчик')
					serverErrorHandler[res.error.code](res);
				}
				else {
					console.log('дефолтный обработчик')
					serverErrorHandler['default'](res);
				}

				return false;
			}

			completeAnalytics();

			document.location.href = res.redirect;
		},

		/**
		 * Подготовка данных для отправки на сервер
		 * Отправка данных
		 */
		preparationData = function preparationData() {
			var currentDeliveryBox = null,
				parts = [],
				dataToSend = [],
				tmpPart = {},
				orderForm = $('#order-form');
			// end of vars
			
			global.OrderModel.blockScreen.block('Ваш заказ оформляется');

			/**
			 * Перебираем блоки доставки
			 */
			console.info('Перебираем блоки доставки');
			for ( var i in global.OrderModel.createdBox ) {
				tmpPart = {};
				currentDeliveryBox = global.OrderModel.createdBox[i];
				console.log(currentDeliveryBox);

				tmpPart = {
					deliveryMethod_token: currentDeliveryBox.state,
					date: currentDeliveryBox.choosenDate().value,
					interval: [
						currentDeliveryBox.choosenInterval().start,
						currentDeliveryBox.choosenInterval().end
					],
					point_id: currentDeliveryBox.choosenPoint().id,
					products : []
				};

				for ( var j = currentDeliveryBox.products.length - 1; j >= 0; j-- ) {
					tmpPart.products.push(currentDeliveryBox.products[j].id);
				}

				parts.push(tmpPart);
			}

			dataToSend = orderForm.serializeArray();
			dataToSend.push({ name: 'order[delivery_type_id]', value: global.OrderModel.choosenDeliveryTypeId });
			dataToSend.push({ name: 'order[part]', value: JSON.stringify(parts) });

			console.log(dataToSend);

			ajaxStart = new Date().getTime();

			$.ajax({
				url: orderForm.attr('action'),
				timeout: 120000,
				type: "POST",
				data: dataToSend,
				success: processingResponse,
				statusCode: {
					500: function() {
						console.log(this.statusCode)
						showError('Неудалось создать заказ. Попробуйте позднее.');
					},
					504: function() {
						console.log(this.statusCode)
						showError('Неудалось создать заказ. Попробуйте позднее.');
					}
				}
			});
		},

		/**
		 * Обработчик нажатия на кнопку завершения заказа
		 */
		orderCompleteBtnHandler = function orderCompleteBtnHandler() {
			console.info('завершить оформление заказа');

			orderValidator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);

					$.scrollTo(err[err.length - 1].fieldNode, 500, {offset:-15});
				},
				onValid: preparationData
			});

			return false;
		},

		/**
		 * Обработчик изменения в поле выбора станции метро
		 * Проверка корректности заполнения поля
		 */
		subwayChange = function subwayChange() {
			for ( var i = subwayArray.length - 1; i >= 0; i-- ) {
				if ( subwayField.val() === subwayArray[i].label ) {
					return;
				}
			}

			subwayField.val('');
		},

		/**
		 * Изменение типа доставки в одом из блоков
		 * 
		 * @param	{Event}		event				Данные о событии
		 * @param	{Boolean}	hasHomeDelivery		Есть ли блок с доставкой домой
		 */
		orderDeliveryChangeHandler = function orderDeliveryChangeHandler( event, hasHomeDelivery ) {
			if ( hasHomeDelivery ) {
				// Добавялем поле ввода улицы в список валидируемых полей
				orderValidator.setValidate( streetField, {
					require: true,
					customErr: 'Не введено название улицы',
					validateOnChange: true
				});

				// Добавялем поле ввода номера дома в список валидируемых полей
				orderValidator.setValidate( buildingField, {
					require: true,
					customErr: 'Не введен номер дома',
					validateOnChange: true
				});

				if ( subwayArray !== undefined ) {
					// Добавлем валидацию поля метро
					orderValidator.setValidate( subwayField , {
						fieldNode: subwayField,
						customErr: 'Не выбрана станция метро',
						require: true,
						validateOnChange: true
					});
				}
			}
			else {
				// Удаляем поле ввода улицы из списка валидируемых полей
				orderValidator.setValidate( streetField, {
					require: false
				});

				// Удаляем поле ввода номера дома из списка валидируемых полей
				orderValidator.setValidate( buildingField, {
					require: false
				});

				if ( subwayArray !== undefined ) {
					// Удаляем поле метро из списка валидируемых полей
					orderValidator.setValidate( subwayField , {
						require: false
					});
				}
			}

			console.info('Изменен тип доставки');
			console.log(orderValidator);
		};
	// end of functions
	
	sclub.mask("* ****** ******", { placeholder: "*" } );
	qiwiPhone.mask("(999) 999-99-99");
	phoneField.mask("(999) 999-99-99");

	/**
	 * AB-test
	 * Обязательное поле e-mail
	 */
	if ( global.docCookies.getItem('emails') ) {
		console.log('AB TEST: e-mail require');

		orderValidator.setValidate( emailField , {
			require: true
		});
	}


	/**
	 * Подстановка значений в поля
	 */
	var defaultValueToField = function defaultValueToField( fields ) {
		var fieldNode = null;

		console.info('defaultValueToField');
		for ( var field in fields ) {
			console.log('поле '+field);
			if ( fields[field] ) {
				console.log('для поля есть значение '+fields[field]);
				fieldNode = $('input[name="'+field+'"]');

				// радио кнопка
				if ( fieldNode.attr('type') === 'radio' ) {
					fieldNode.filter('[value="'+fields[field]+'"]').attr('checked', 'checked');

					continue;
				}

				// поле текстовое	
				fieldNode.val( fields[field] );
			}
		}
	};
	defaultValueToField($('#jsOrderForm').data('value'));


	/**
	 * Включение автокомплита метро
	 * Подстановка станции метро по id
	 */
	if ( subwayArray !== undefined ) {
		subwayField.autocomplete(subwayAutocompleteConfig);
		subwayField.bind('change', subwayChange);

		for ( var i = subwayArray.length - 1; i >= 0; i-- ) {
			if ( parseInt(metroIdFiled.val(), 10) === subwayArray[i].val ) {
				subwayField.val(subwayArray[i].label);

				break;
			}
		}
	}

	$('body').bind('orderdeliverychange', orderDeliveryChangeHandler);
	orderCompleteBtn.bind('click', orderCompleteBtnHandler);
}(this));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
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
            var tID = null;

			console.info('обновление данных с сервера');

			var updateResponceHandler = function updateResponceHandler( res ) {
				renderOrderData(res);
				global.OrderModel.blockScreen.unblock();

				separateOrder( global.OrderModel.statesPriority );
			};

            tID = setTimeout(function() {
                clearTimeout(tID);
                $.ajax({
                    type: 'GET',
                    url: global.OrderModel.updateUrl,
                    success: updateResponceHandler
                });
            }, 1200);
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
			var totalPrice = 0,
				totalQuan = 0,

				toKISS = {};
			// end of vars

			for ( var product in orderData.products ) {
				totalPrice += product[product].price;
				totalQuan += product[product].quantity;
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
		};
	// end of functions

	$('body').on('click', '.shopchoose', selectPointOnBaloon);

	renderOrderData( serverData );

	// запуск аналитики
	if ( typeof _gaq !== 'undefined' || typeof _kmq !== 'undefined' ) {
		analyticsStep_1( serverData );
	}
}(this));