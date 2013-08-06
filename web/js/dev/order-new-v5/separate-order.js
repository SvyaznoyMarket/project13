/**
 * Логика разбиения заказа для оформления заказа
 */
;(function(){
	console.info('Логика разбиения заказа для оформления заказа v.5');

	var getDataUrl = '/ajax/order-delivery',
		orderData = {},
		choosenDeliveryType = null;
	// end of vars


	/**
	 * ORDER MODEL
	 */
	var OrderModel = {
		prepareData: ko.observable(false),
		deliveryTypes: ko.observableArray([]),

		/**
		 * Выбор типа доставки. Обработчик созданных кнопок из deliveryTypes
		 * 
		 * @param	{Object}	data			Данные о типе доставки
		 * @param	{String}	data.token		Выбранный способ доставки
		 * @param	{String}	data.name		Имя выбранного способа доставки
		 * @param	{Array}		data.states		Варианты типов доставки подходящих к этому методу
		 */
		chooseDeliveryTypes: function( data ) {
			choosenDeliveryType = data.token;
			console.log('выбранный метод доставки '+choosenDeliveryType);
		}
	}

	
	ko.applyBindings(OrderModel);

	/**
	 * Обработка полученных с сервера данных
	 * 
	 * @param	{Object}	res		Данные о заказе
	 */
	var renderOrderData = function renderOrderData( res ) {
		orderData = res;

		console.log('Данные с сервера получены');

		OrderModel.deliveryTypes(res.deliveryTypes);
		OrderModel.prepareData(true);		
	};

	$.ajax({
		type: 'GET',
		url: getDataUrl,
		success: renderOrderData
	});
}());