;(function ( ENTER ) {
	var $body = $(document.body),
		utils = ENTER.utils;
	// end of vars

	utils.trim = function(string) {
		return ((string || '') + '').replace(/^\s+|\s+$/g, '');
	};

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
		var result = new RegExp('[\\?&]' + utils.escapeRegexp(encodeURIComponent(paramName)) + '=([^&#]*)').exec(url);
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
	 * Извлекает строку запроса из URL адреса и преобразует её в объект (который и возвращает).
	 * @param {string} url
	 * @returns {{}}
	 */
	utils.parseUrlParams = function(url) {
		var
			result = {},
			params = url.replace(/^[^?]*\?|\#.*$/g, '').split('&');

		for (var i = 0; i < params.length; i++) {
			var param = params[i].split('=');

			if (!param[0]) {
				param[0] = '';
			}

			if (!param[1]) {
				param[1] = '';
			}

			param[0] = decodeURIComponent(param[0]);
			param[1] = decodeURIComponent(param[1]);

			result[param[0]] = param[1];
		}

		return result;
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
			if (url.indexOf('{' + paramName + '}') != -1) {
				url = url.replace('{' + paramName + '}', paramValue);
			} else {
				var params = {};
				params[paramName] = paramValue;
				url += (url.indexOf('?') == -1 ? '?' : '&') + $.param(params);
			}
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

	utils.validateEmail = function(email) {
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
	};

    utils.arrayUnique = function(array) {
        var unique = [];
        for (var i = 0; i < array.length; i++) {
            if (unique.indexOf(array[i]) == -1) {
                unique.push(array[i]);
            }
        }

        return unique;
    };

	utils.Base64 = {_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=utils.Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=utils.Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}

	/**
	 * Приготовление и отправка данных в GA, аналитика
	 * @param orderData
	 */
	utils.sendOrderToGA = function(orderData) {

        var	oData = orderData || { orders: [] };

		console.log('[Google Analytics] Start processing orders', oData.orders);
		$.each(oData.orders, function(i,o) {
			var googleOrderTrackingData = {};
			googleOrderTrackingData.transaction = {
				'id': o.numberErp,
				'affiliation': o.is_partner ? 'Партнер' : 'Enter',
				'total': o.paySum,
				'shipping': o.delivery.price,
				'city': o.region.name
			};
			googleOrderTrackingData.products = $.map(o.products, function(p){
				var
					productName = p.name,
					labels = [];

				if (o.isSlot) {
					labels.push('marketplace-slot');
				} else if (o.is_partner) {
					labels.push('marketplace');
				}

				if (p.sender) {
					labels.push(p.sender);
				}

				if (p.sender && p.position) {
					labels.push('RR_' + p.position);
				}

                // Отслеживание покупок в кредит
                if (p.sender2 == 'credit') labels.push('Credit');

				if (labels.length) {
					productName += ' (' + labels.join(')(') + ')';
				}

				/* SITE-4472 Аналитика по АБ-тесту платного самовывоза и рекомендаций из корзины */
				if (ENTER.config.pageConfig.selfDeliveryTest && ENTER.config.pageConfig.selfDeliveryLimit > parseInt(o.paySum, 10) - o.delivery.price) productName = productName + ' (paid pickup)';

				// Аналитика по купленным товарам из рекомендаций
				// Отправляем RR_покупка не только для retailrocket товаров
				if (p.sender) {
					var rrEventLabel = '';
					// Если товар был куплен из рекомендаций с карточки товаров маркетплейс
					if (p.sender2 == 'slot') {
						rrEventLabel = '_marketplace-slot';
					} else if (p.sender2 == 'marketplace') {
						rrEventLabel = '_marketplace';
					}

					if (p.from) $body.trigger('trackGoogleEvent',['RR_покупка' + rrEventLabel,'Купил просмотренные', p.position || '']);
					else $body.trigger('trackGoogleEvent',['RR_покупка' + rrEventLabel,'Купил добавленные', p.position || '']);
				}

				if (p.inCompare) {
					(function() {
						var action;
						if (p.isSlot) {
							action = 'marketplace-slot';
						} else if (p.isOnlyFromPartner) {
							action = 'marketplace';
						} else {
							action = 'enter';
						}

						$body.trigger('trackGoogleEvent', ['Compare_покупка', action, p.compareLocation]);
					})();
				}

				return {
					'id': p.id,
					'name': productName,
					'sku': p.article,
					'category': p.category.length ? (p.category[0].name +  ' - ' + p.category[p.category.length -1].name) : '',
					'price': p.price,
					'quantity': p.quantity
				}
			});

            if (o.isCredit) {
                if ($.grep(o.products, function(product){ return product.sender2 == 'credit' }).length > 0) {
                    $body.trigger('trackGoogleEvent', ['Credit', 'Покупка', 'Карточка товара'])
                } else {
                    $body.trigger('trackGoogleEvent', ['Credit', 'Покупка', 'Оформление заказа'])
                }
            }

			console.log('[Google Analytics] Order', googleOrderTrackingData);
			$body.trigger('trackGoogleTransaction', [googleOrderTrackingData]);
		});
	};

	utils.sendAdd2BasketGaEvent = function(productArticle, productPrice, isOnlyFromPartner, isSlot, senderName) {
		if (productArticle) {
			var location;
			if (ENTER.config.pageConfig.location.indexOf('listing') != -1) {
				location = 'listing';
			} else if (ENTER.config.pageConfig.location.indexOf('product') != -1) {
				location = 'product';
			}

			if (location) {
				var actions = [];

				if (senderName == 'gift') {
					actions.push(location + '-gift');
				}

				if (typeof productPrice != 'undefined' && parseInt(productPrice, 10) < 500) {
					actions.push(location + '-500');
				}

				if (isSlot) {
					actions.push(location + '-marketplace-slot');
				} else if (isOnlyFromPartner) {
					actions.push(location + '-marketplace');
				} else {
					actions.push(location);
				}

				$body.trigger('trackGoogleEvent', ['Add2Basket', '(' + actions.join(')(') + ')', productArticle]);
			}
		}
	};

	utils.getCategoryPath = function() {
		return document.location.pathname.replace(/^\/(?:catalog|product)\/([^\/]*).*$/i, '$1');
	};

}(window.ENTER));