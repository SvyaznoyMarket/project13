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

			// если для приоритетного метода доставки существуют пункты доставки, то пользователю необходимо выбрать пункт доставки, если нет - то приравниваем идентификатор пункта доставки к 0
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