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
		var result = new RegExp('[\\?&]' + paramName + '=([^&#]*)').exec(url);
		if (result) {
			return decodeURIComponent(result[1]);
		}

		return null;
	};

	/**
	 * Добавляет get-параметр с именем paramName к url или заменяет существующий (удаляя указанный параметр, если paramValue равно null)
	 *
	 * @param 		{string}	paramName
	 * @param 		{string}	paramValue
	 * @param 		{string}	url
	 * @returns 	{string}	Новый URL
	 */
	utils.setURLParam = function(paramName, paramValue, url) {
		var regexp = new RegExp('([\\?&])(' + utils.escapeRegexp(encodeURIComponent(paramName)) + '=)[^&#]*');

		if (regexp.exec(url) === null) {
			if (url.indexOf('?') == -1) {
				url += '?';
			} else if (url.indexOf('?') < url.length - 1) {
				url += '&';
			}

			url += encodeURIComponent(paramName) + '=' + encodeURIComponent(paramValue);
			return url;
		} else if (paramValue === null) {
			return url.replace(regexp, '$1').replace(/\?\&/, '?').replace(/\&\&/, '&').replace(/[\?\&]$/, '');
		} else {
			return url.replace(regexp, '$1$2' + encodeURIComponent(paramValue));
		}
	};

	/**
	 * Экранирует спец. символы регулярного выражения в строке
	 *
	 * @param 		{string}	string
	 * @returns 	{string}	Экранированная строка
	 */
	utils.escapeRegexp = function(string) {
		return string.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
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
	};

	utils.checkEan = function checkEanF(data) {
		// Check if only digits
		var ValidChars = "0123456789",
			i, digit, originalCheck, even, odd, total, checksum, eanCode;

		eanCode = data.toString().replace(/\s+/g, '');

		for (i = 0; i < eanCode.length; i++) {
			digit = eanCode.charAt(i);
			if (ValidChars.indexOf(digit) == -1) return false;
		}

		// Add five 0 if the code has only 8 digits
		if (eanCode.length == 8 ) eanCode = "00000" + eanCode;
		// Check for 13 digits otherwise
		else if (eanCode.length != 13) return false;

		// Get the check number
		originalCheck = eanCode.substring(eanCode.length - 1);
		eanCode = eanCode.substring(0, eanCode.length - 1);

		// Add even numbers together
		even = Number(eanCode.charAt(1)) +
		Number(eanCode.charAt(3)) +
		Number(eanCode.charAt(5)) +
		Number(eanCode.charAt(7)) +
		Number(eanCode.charAt(9)) +
		Number(eanCode.charAt(11));
		// Multiply this result by 3
		even *= 3;

		// Add odd numbers together
		odd = Number(eanCode.charAt(0)) +
		Number(eanCode.charAt(2)) +
		Number(eanCode.charAt(4)) +
		Number(eanCode.charAt(6)) +
		Number(eanCode.charAt(8)) +
		Number(eanCode.charAt(10));

		// Add two totals together
		total = even + odd;

		// Calculate the checksum
		// Divide total by 10 and store the remainder
		checksum = total % 10;
		// If result is not 0 then take away 10
		if (checksum != 0) {
			checksum = 10 - checksum;
		}

		// Return the result
		return checksum == originalCheck;
	}

    utils.arrayUnique = function(array) {
        var unique = [];
        for (var i = 0; i < array.length; i++) {
            if (unique.indexOf(array[i]) == -1) {
                unique.push(array[i]);
            }
        }

        return unique;
    };

}(window.ENTER));