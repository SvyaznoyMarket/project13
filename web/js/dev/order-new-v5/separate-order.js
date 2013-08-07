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

	self.OrderModel = OrderModel; // создаем ссылку на OrderModel
	self.createdBox = createdBox; // создаем ссылку на createdBox

	self.deliveryPoint = choosenPointForBox;
	self.products = [];
	self.fullPrice = 0;
	self.state = state;
	self.deliveryPrice = Number.POSITIVE_INFINITY; // берем максимально возможное значение, чтобы сравнивая с ним находить минимальное
	self.showMessage = null;
	self.choosenDate = null;

	self.addProductGroup(products);

	self.OrderModel.deliveryBoxes.push(self);
}

/**
 * Получить имя первого свойства объекта
 * 
 * @param 	{Object}	obj		Объект у которого необходимо получить первое свойство
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
 * @param	{Object}		product		Продукт который нужно добавить
 */
DeliveryBox.prototype.addProduct = function( product ) {
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

		if ( self.createdBox[token] !== undefined ) {
			// Блок для этого типа доставки в этот пункт уже существует. Добавляем продукт в блок
			self.createdBox[token].addProduct( product );
		}
		else {
			// Блока для этого типа доставки в этот пункт еще существует
			tempProductArray.push(product);
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
		productImg: product.image
	});
};

/**
 * Добавление нескольких товаров в блок доставки
 * 
 * @this	{DeliveryBox}
 * @param	{Array}			products	Продукты которые нужно добавить
 */
DeliveryBox.prototype.addProductGroup = function( products ) {
	// добавляем товары в блок
	for ( var i = products.length - 1; i >= 0; i-- ) {
		this.addProduct(products[i]);
	}
};





;(function(){
	console.info('Логика разбиения заказа для оформления заказа v.5');

	var getDataUrl = '/ajax/order-delivery', // HARDCODE
		orderData = {},
		choosenDeliveryType = null,
		choosenPoint = null,

		createdBox = {};
	// end of vars
	

	/**
	 * Логика разбиения заказа на подзаказы
	 * Берутся states из выбранного способа доставки в порядке приоритета.
	 * Каждый новый states - новый подзаказ.
	 *
	 * @param	{Array}		statesPriority	Приоритет методов доставки
	 */
	var separateOrder = function separateOrder( statesPriority ) {
			/**
			 * Уже обработанные продукты, которые попали в какой-либо блок доставки
			 * @type {Object}
			 */
		var preparedProducts = {},

			/**
			 * Массив продуктов, которые есть в данном способе доставки
			 * @type {Array}
			 */
			productInState = [],

			/**
			 * Массив продуктов которые должны попасть в новый блок доставки
			 * @type {Array}
			 */
			productsToNewBox = [],

			/**
			 * Точка доставки для блока самовывоза
			 * @type {Number}
			 */
			choosenPointForBox = null,

			/**
			 * Временное имя для создаваемого блока
			 * @type {String}
			 */
			token = null;
		// end of vars


		/**
		 * Перебор states в выбранном способе доставки в порядке приоритета
		 */
		for ( var i = 0, len = statesPriority.length; i < len; i++ ) {
			console.info('перебирем метод '+statesPriority[i]);

			productsToNewBox = [];

			if ( !orderData.deliveryStates.hasOwnProperty(statesPriority[i]) ) {
				console.warn('для метода '+statesPriority[i]+' нет товаров')
				continue;
			}

			productInState = orderData.deliveryStates[statesPriority[i]].products;
			
			/**
			 * Перебор продуктов в текущем deliveryStates
			 */
			for ( var j = productInState.length - 1; j >= 0; j-- ) {

				if ( preparedProducts[productInState[j]] ) {
					// если этот товар уже находили
					console.log('товар '+productInState[j]+' уже определялся к блоку');

					continue;
				}
				
				console.log('добавляем товар '+productInState[j]+' в блок для метода '+statesPriority[i]);

				preparedProducts[productInState[j]] = true;
				productsToNewBox.push( orderData.products[productInState[j]] );
			}

			if ( productsToNewBox.length ) {
				choosenPointForBox = ( orderData.pointsByDelivery[statesPriority[i]] ) ? choosenPoint : 0;

				token = statesPriority[i]+'_'+choosenPointForBox;

				if ( createdBox[token] !== undefined ) {
					// Блок для этого типа доставки в этот пункт уже существует
					createdBox[token].addProductGroup( productsToNewBox );
				}
				else {
					// Блока для этого типа доставки в этот пункт еще существует
					createdBox[token] = new DeliveryBox( productsToNewBox, statesPriority[i], choosenPointForBox, createdBox, OrderModel );
				}
			}
		}

		console.info('Созданные блоки:');
		console.log(createdBox);

		if ( preparedProducts.length !== orderData.products.length ) {
			console.warn('не все товары были обработаны');
		}
	};


	/**
	 * ORDER MODEL
	 */
	var OrderModel = {
		prepareData: ko.observable(false),

		/**
		 * Массив способов доставок доступных пользователю
		 */
		deliveryTypes: ko.observableArray([]),

		/**
		 * Массив блоков доставок
		 */
		deliveryBoxes: ko.observableArray([]),

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
			var statesPriority = data.states,
				priorityState = statesPriority[0];
			// end of vars

			// очищаем объект созданых блоков, удаляем блоки из модели
			createdBox = {};
			OrderModel.deliveryBoxes.removeAll();

			// запоминаем выбранный способ доставки
			choosenDeliveryType = data.token;

			// если для приоритетного state существуют точки доставки, то пользователь необходимо выбрать точку доставки, если нет точек доставки, то приравниваем точку к 0
			if ( orderData.pointsByDelivery[priorityState] ) {
				console.log('есть точки доставки из которых нужно выбрать');
				// здесь необходимо реализовать логику выбора магазина
				choosenPoint = 13; // HARDCODE
			}
			else {
				console.log('для выбранного метода доставки нет точек доставки');

				choosenPoint = 0;
			}

			console.log('выбранный метод доставки '+choosenDeliveryType);
			console.log('выбранное место доставки '+choosenPoint);

			separateOrder( statesPriority );
		}
	}

	
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

		orderData = res;

		OrderModel.deliveryTypes(res.deliveryTypes);
		OrderModel.prepareData(true);		
	};

	$.ajax({
		type: 'GET',
		url: getDataUrl,
		success: renderOrderData
	});
}());