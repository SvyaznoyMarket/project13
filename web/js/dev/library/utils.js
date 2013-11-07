;(function ( ENTER ) {
	var utils = ENTER.utils;


	/**
	 * Возвращает колчество свойств в объекте.
	 *
	 * @param	{Object}	obj
	 * 
	 * @return	{Number}	count
	 */
	utils.objLen = function objLen( obj ) {
		var len = 0,
			p;
		// end of vars

		for ( p in obj ) {
			if ( obj.hasOwnProperty(p) ) {
				len++;
			}
		}

		return len;
	};

	/**
	 * Глобально доступный метод получения пользовательской корзины
	 *
	 * @param	{Boolean}			returnObject	Флаг, возвращать объект(true) или строку(false)
	 *
	 * @return	{Object|String}
	 */
	utils.getUserCart = function getUserCart( returnObject ) {
		var cart = ENTER.config.clientCart.products;

		return (returnObject) ? cart : JSON.stringify(cart);
	};

	/**
	 * Глобально доступный метод применения пользовательской корзины
	 *
	 * @param	{Object}			cart			Корзина
	 */
	utils.applyUserCart = function applyUserCart( cart ) {
		console.log('apply');
		console.log(typeof cart);
	};


}(window.ENTER));