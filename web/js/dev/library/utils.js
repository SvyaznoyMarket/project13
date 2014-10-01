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
		$.each((params || {}), function(paramName){
			url = url.replace('{' + paramName + '}', this);
		});

		return url;
	};

}(window.ENTER));