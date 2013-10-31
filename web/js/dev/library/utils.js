;(function ( ENTER ) {
	var utils = ENTER.utils;


	/**
	 * Возвращает колчество свойств в объекте.
	 *
	 * @param       {object}        obj
	 * @returns     {number}        count
	 */
	utils.objLen = function objLen( obj ) {
		var len = 0, p;
		for ( p in obj ) {
			if ( obj.hasOwnProperty(p) ) {
				len++;
			}
		}
		return len;
	}


	/**
	 * Возвращает гет-параметр с именем paramName у ссылки url
	 *
	 * @param 		{string}	paramName
	 * @param 		{string}	url
	 * @returns 	{string}	{*}
	 *
	utils.getURLParam = function getURLParam ( paramName, url ) {
		return decodeURI(
			( RegExp(paramName + '=' + '(.+?)(&|$)').exec(url) || [, null] )[1]
		);
	}*/

}(window.ENTER));