;(function ( ENTER ) {
	var
		utils = ENTER.utils;
	// end of vars


	/**
	 * Возвращает колчество свойств в объекте.
	 *
	 * @param	{Object}	obj
	 * 
	 * @return	{Number}	count
	 */
	utils.objLen = function objLen( obj ) {
		var
			len = 0,
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
	 * Возвращает гет-параметр с именем paramName у ссылки url
	 *
	 * @param 		{string}	paramName
	 * @param 		{string}	url
	 * @returns 	{string}	{*}
	 */
	utils.getURLParam = function getURLParam(paramName, url) {
		return decodeURI(
			( RegExp( '[\\?&]' + paramName + '=([^&#]*)' ).exec( url ) || [, null] )[1]
		);
	};

	/**
	 * @param {string} routeName
	 * @param {object} [params]
	 * @return {string}
	 */
	utils.generateUrl = function(routeName, params) {
		var url = ENTER.config.pageConfig.routes[routeName]['pattern'];
		$.each((params || {}), function(paramName, paramValue){
			url = url.replace('{' + paramName + '}', paramValue);
		});

		return url;
	};
	
	utils.getObjectWithElement = function(array, elementKey, expectedElementValue) {
		var object = null;
		if (array) {
			$.each(array, function(arrayKey, arrayValue){
				if (arrayValue[elementKey] === expectedElementValue) {
					object = arrayValue;
					return false;
				}
			});
		}
		
		return object;
	};

	utils.validateEmail = function validateEmailF(email) {
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	}

}(window.ENTER));