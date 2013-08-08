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
 * @param	{String}	state	Метод доставки
 * @param	{String}	pointId	Идентификатор точки достаки
 * @return	{Object}			Данные о точке доставки
 */
OrderDictionary.prototype.getPointByStateAndId = function( state, pointId ) {
	var pointName = this.pointsByDelivery[state],
		pointId = pointId+'';
	// end of vars

	for (var i = this.orderData[pointName].length - 1; i >= 0; i--) {
		if ( this.orderData[pointName][i].id === pointId ) {
			return this.orderData[pointName][i];
		}
	}
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