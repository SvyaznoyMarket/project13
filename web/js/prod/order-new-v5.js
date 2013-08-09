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
	self.pointList = ko.observableArray([]);

	if ( self.hasPointDelivery ) {
		self.choosenPoint( self.OrderModel.orderDictionary.getPointByStateAndId(self.state, choosenPointForBox) );
	}

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
		res = true;
	// end of vars

	/**
	 * Перебираем точки доставки для первого товара
	 */
	for (var point in self.products[0].deliveries ) {

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
			self.pointList.push( self.OrderModel.orderDictionary.getPointByStateAndId(self.state, point) );
		}
	}
};

/**
 * Смена пункта доставки
 *
 * @this	{DeliveryBox}
 * 
 * @param	{Object}	data	Данные о пункте доставки
 */
DeliveryBox.prototype.selectPoint = function( data ) {
	var self = this;

	self.choosenPoint(data);
	self.showPopupWithPoints(false);

	return false;
};

/**
 * Показ окна с пунктами доставки
 *
 * @this	{DeliveryBox}
 */
DeliveryBox.prototype.changePoint = function( ) {
	var self = this;

	self.showPopupWithPoints(true);

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
	productDeliveryPrice = parseInt(product.deliveries[self.state][self.choosenPoint().id].price, 10);
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

	this.calculateDate();

	if ( self.hasPointDelivery ) {
		self._makePointList();
	}
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
	var self = this,
		addCountDays = 0,
		dayOfWeek = null,
		tmpDay = {};
	// end of vars

	if ( self.allDatesForBlock()[0].dayOfWeek !== 1 ) {
		addCountDays = ( self.allDatesForBlock()[0].dayOfWeek === 0 ) ? 6 : self.allDatesForBlock()[0].dayOfWeek - 1;

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
};
 
 
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
 * @param	{String}	pointId		Идентификатор точки достаки
 * @return	{Object}				Данные о точке доставки
 */
OrderDictionary.prototype.getPointByStateAndId = function( state, pointId ) {
	var points = this.getAllPointsByState(state),
		pointId = pointId+'';
	// end of vars

	for (var i = points.length - 1; i >= 0; i--) {
		if ( points[i].id === pointId ) {
			return points[i];
		}
	}
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
 
 
/**
 * Получение данных с сервера
 * Разбиение заказа
 * Модель knockout
 *
 * @author	Zaytsev Alexandr
 */
;(function(){
	console.info('Логика разбиения заказа для оформления заказа v.5');

	var getDataUrl = '/ajax/order-delivery', // HARDCODE
		choosenDeliveryType = null,
		choosenPoint = null,

		createdBox = {};
	// end of vars
	

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
			nowProduct = null;
		// end of vars


		/**
		 * Перебор states в выбранном способе доставки в порядке приоритета
		 */
		for ( var i = 0, len = statesPriority.length; i < len; i++ ) {
			nowState = statesPriority[i];

			console.info('перебирем метод '+nowState);

			productsToNewBox = [];

			if ( !OrderModel.orderDictionary.hasDeliveryState(nowState) ) {
				console.info('для метода '+nowState+' нет товаров');
				continue;
			}

			productInState = OrderModel.orderDictionary.getProductFromState(nowState);
			
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
				productsToNewBox.push( OrderModel.orderDictionary.getProductById(nowProduct) );
			}

			if ( productsToNewBox.length ) {
				choosenPointForBox = ( OrderModel.orderDictionary.hasPointDelivery(nowState) ) ? choosenPoint : 0;

				token = nowState+'_'+choosenPointForBox;

				if ( createdBox[token] !== undefined ) {
					// Блок для этого типа доставки в этот пункт уже существует
					createdBox[token].addProductGroup( productsToNewBox );
				}
				else {
					// Блока для этого типа доставки в этот пункт еще существует
					createdBox[token] = new DeliveryBox( productsToNewBox, nowState, choosenPointForBox, createdBox, OrderModel );
				}
			}
		}

		console.info('Созданные блоки:');
		console.log(createdBox);

		if ( preparedProducts.length !== OrderModel.orderDictionary.orderData.products.length ) {
			console.warn('не все товары были обработаны');
		}
	};


	/**
	 * ORDER MODEL
	 */
	var OrderModel = {
		prepareData: ko.observable(false),

		statesPriority: null,

		/**
		 * Ссылка на словарь
		 */
		orderDictionary: null,

		/**
		 * Массив способов доставок доступных пользователю
		 */
		deliveryTypes: ko.observableArray([]),

		/**
		 * Массив блоков доставок
		 */
		deliveryBoxes: ko.observableArray([]),

		showPopupWithPoints: ko.observable(false),

		popupWithPoints: ko.observable({}),

		selectPoint: function( data ) {
			console.info('point selected...');

			choosenPoint = data.id;
			OrderModel.showPopupWithPoints(false);
			separateOrder( OrderModel.statesPriority );

			return false;
		},

		/**
		 * Выбор типа доставки. Обработчик созданных кнопок из deliveryTypes
		 * 
		 * @param	{Object}	data			Данные о типе доставки
		 * @param	{String}	data.token		Выбранный способ доставки
		 * @param	{String}	data.name		Имя выбранного способа доставки
		 * @param	{Array}		data.states		Варианты типов доставки подходящих к этому методу
		 *
		 * @param	{Array}		statesPriority	Массив методов доставок в порядке приоритета
		 * @param	{String}	priorityState	Приоритетный метод доставки из массива
		 * 
		 */
		chooseDeliveryTypes: function( data ) {
			var priorityState = data.states[0];

			OrderModel.statesPriority = data.states;

			// очищаем объект созданых блоков, удаляем блоки из модели
			createdBox = {};
			OrderModel.deliveryBoxes.removeAll();

			// запоминаем выбранный способ доставки
			choosenDeliveryType = data.token;

			// если для приоритетного state существуют точки доставки, то пользователь необходимо выбрать точку доставки, если нет точек доставки, то приравниваем точку к 0
			if ( OrderModel.orderDictionary.hasPointDelivery(priorityState) ) {
				OrderModel.popupWithPoints({
					header: data.description,
					points: OrderModel.orderDictionary.getAllPointsByState(priorityState)
				});

				OrderModel.showPopupWithPoints(true);

				return false;
			}

			choosenPoint = 0;
			separateOrder( OrderModel.statesPriority );

			return false;
		}
	};

	
	ko.applyBindings(OrderModel);

	/**
	 * Обработка полученных с сервера данных
	 * 
	 * @param	{Object}	res		Данные о заказе
	 */
	var renderOrderData = function renderOrderData( res ) {
		if ( !res.success ) {
			// TODO: написать обработчки ошибок
			console.warn('произошла ошибка при получении данных с сервера');
			console.log(res.error);

			return false;
		}


		console.log('Данные с сервера получены');

		OrderModel.orderDictionary = new OrderDictionary(res);

		OrderModel.deliveryTypes(res.deliveryTypes);
		OrderModel.prepareData(true);
		$('#order').removeClass('hidden');
	};

	$.ajax({
		type: 'GET',
		url: getDataUrl,
		success: renderOrderData
	});
}());