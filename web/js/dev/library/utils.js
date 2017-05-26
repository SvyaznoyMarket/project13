;(function ( ENTER ) {
	var $body = $(document.body),
		utils = ENTER.utils;
	// end of vars

	utils.trim = function(string) {
		return ((string || '') + '').replace(/^\s+|\s+$/g, '');
	};

	utils.numberChoice = function(number, choices) {
		var cases = [2, 0, 1, 1, 1, 2];

		return choices[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[Math.min(number % 10, 5)]];
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

	utils.loadScript = function(url, isAsync) {
		var script = document.createElement('script');
		script.src = url;
		script.async = !!isAsync;
		script.type = 'text/javascript';
		document.getElementsByTagName('head')[0].appendChild(script);
	};

	utils.router = {};

	/**
	 * @param {string} routeName
	 * @param {object} [params]
	 * @return {string}
	 */
	utils.router.generateUrl = function(routeName, params) {
		params = params || {};

		if (ENTER.config.pageConfig.globalParams) {
			$.each(ENTER.config.pageConfig.globalParams, function(key, value) {
				if (typeof params[key] == 'undefined') {
					params[key] = value;
				}
			});
		}

		var anchor = '';
		if (params['#']) {
			anchor = '#' + params['#'];
			delete params['#'];
		}

		var
			urls = $.merge([], ENTER.config.pageConfig.routes[routeName].urls),
			require = ENTER.config.pageConfig.routes[routeName].require;

		if (ENTER.config.pageConfig.routes[routeName].outFilters) {
			$.each(ENTER.config.pageConfig.routes[routeName].outFilters, function(outFilterVarName, outFilterVarPattern) {
				if (typeof params[outFilterVarName] != 'undefined' && !(new RegExp('^' + outFilterVarPattern + '$')).test(params[outFilterVarName])) {
					delete params[outFilterVarName];
				}
			});
		}

		urls.sort(function(a, b) {
			var countA = a.split('{').length - 1;
			var countB = b.split('{').length - 1;

			if (countA == countB) {
				return 0;
			} else if (countA < countB) {
				return 1;
			} else {
				return -1;
			}
		});

		var url = null;
		$.each(urls, function(key, pattern) {
			if (pattern.indexOf('{') == -1) {
				url = pattern;
				return false;
			} else {
				var
					patternVarCount = pattern.split('{').length - 1,
					patternVarReplaceCount = 0,
					regexp = /\{(\w+)\}/g,
					match,
					newParams = $.extend({}, params);

				while ((match = regexp.exec(pattern)) !== null) {
					var
						patternVarName = match[1],
						patternVarLength = patternVarName.length + 2;

					if (typeof params[patternVarName] == 'undefined' || (typeof require[patternVarName] != 'undefined' && !(new RegExp('^' + require[patternVarName] + '$')).test(params[patternVarName]))) {
						return;
					}

					pattern = pattern.slice(0, regexp.lastIndex - patternVarLength) + params[patternVarName] + pattern.slice(regexp.lastIndex);
					regexp.lastIndex -= patternVarLength - params[patternVarName].length;
					delete newParams[patternVarName];
					patternVarReplaceCount++;
				}

				if (patternVarCount == patternVarReplaceCount) {
					url = pattern;
					params = newParams;
					return false;
				}
			}
		});

		if (!url) {
			return '';
		}

		$.each(params, function(key, value) {
			if (!value) {
				delete params[key];
			}
		});

		var query = $.param(params);
		if (query) {
			url += '?' + query;
		}

		url += anchor;

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

	// Формат kladr id см. в https://wiki.enter.ru/pages/viewpage.action?pageId=23334912 -> https://wiki.enter.ru/download/attachments/23334912/%D0%9A%D0%9B%D0%90%D0%94%D0%A0%20%28%D0%BE%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5%29.DOC?api=v2
	utils.kladr = {
		getCityIdFromKladrId: function(kladrId) {
			var
				cityKladrIdLength = 11,
				kladrActualityIndicator = '00';

			if (kladrId.length >= cityKladrIdLength) {
				return kladrId.slice(0, cityKladrIdLength) + kladrActualityIndicator;
			}

			return '';
		},
		getStreetIdFromKladrId: function(kladrId) {
			var
				streetKladrIdLength = 15,
				kladrActualityIndicator = '00';

			if (kladrId.length >= streetKladrIdLength) {
				return kladrId.slice(0, streetKladrIdLength) + kladrActualityIndicator;
			}

			return '';
		},
		getBuildingIdFromKladrId: function(kladrId) {
			var buildingKladrIdLength = 19;

			if (kladrId.length >= buildingKladrIdLength) {
				return kladrId.slice(0, buildingKladrIdLength);
			}

			return '';
		}
	};

	/**
	 * Приготовление и отправка данных в GA, аналитика
	 * @param orderData
	 */
	utils.sendOrderToGA = function(orderData) {

        var	oData = orderData || { orders: [] };

		console.log('[Google Analytics] Start processing orders', oData.orders);

		var productUis = [];
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

				if (p.sender) {
					// SITE-5772
					if (typeof p.sender == 'string' && p.sender.indexOf('filter') == 0) {
						$('body').trigger('trackGoogleEvent', {
							category: p.sender,
							action: 'buy',
							label: p.isFromProductCard ? 'product' : 'basket'
						});
					} else {
						// Аналитика по купленным товарам из рекомендаций
						// Отправляем RR_покупка не только для retailrocket товаров

						var rrEventLabel = '';
						// Если товар был куплен из рекомендаций с карточки товара маркетплейс
						if (p.sender2 == 'slot') {
							rrEventLabel = '_marketplace-slot';
						} else if (p.sender2 == 'marketplace') {
							rrEventLabel = '_marketplace';
						}

						if (p.from) $body.trigger('trackGoogleEvent',['RR_покупка' + rrEventLabel,'Купил просмотренные', p.position || '']);
						else $body.trigger('trackGoogleEvent',['RR_покупка' + rrEventLabel,'Купил добавленные', p.position || '']);
					}
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

				productUis.push(p.ui);

				return {
					'id': p.barcode,
					'sku': p.barcode,
					'name': productName,
					'category': $.map(p.category, function(obj) {return obj.name}).join(' / '),
					'price': p.price,
					'quantity': p.quantity,
					'brand': p.brand ? p.brand.name : ''
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

		// SITE-5466
		(function() {
			var
				action = '',
				label = '';

			var reviewProducts = ENTER.utils.analytics.reviews.get(productUis);
            console.log('localstorage', localStorage.getItem('enter.analytics.reviews'));
            console.log('products ui', productUis);
            console.log('reviewProducts', reviewProducts);

			if (reviewProducts.length) {
				for (var i = 0; i < reviewProducts.length; i++) {
					action += (i > 0 ? '_' : '') + (i + 1) + '_All_' + reviewProducts[i].avgScore + '_Top_' + reviewProducts[i].firstPageAvgScore;
					label += (i > 0 ? '_' : '') + (i + 1) + '_' + reviewProducts[i].categoryName;
				}

				$body.trigger('trackGoogleEvent', {
					category: 'Items_review_transaction',
					action: action,
					label: label
				});
			}
		})();
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

				//$body.trigger('trackGoogleEvent', ['Add2Basket', '(' + actions.join(')(') + ')', productArticle]);
				$body.trigger('trackGoogleEvent', ['Product', 'click', 'add to cart']);
			}
		}
	};

	/**
	 * SITE-5772
	 * @param {String} sort Например, price-ask
	 * @param {Object} category Данные категории вида {name: "", ancestors: [{name: ""}]}
	 */
	utils.sendSortEvent = function(sort, category) {
		// Для слайсов события в аналитику пока не шлём, т.к. для реализации событий перехода на карточку, добавления в
		// корзину и покупки необходимо добавить либо поддержку множественных sender'ов либо добавить поддержку
		// параметра sender3 (который использовать для данной аналитики). Отравку событий взаимодействия с фильтрами без
		// отправки события перехода/добавления/покупки не делает, чтобы не портить статистику по filter_old.
		if ($('.js-slice').length) {
			return;
		}

		$('body').trigger('trackGoogleEvent', {
			category: 'sort',
			action: sort,
			label: utils.getPageBusinessUnitId() + (function() {
				var result = '';

				if (!category || !category.name) {
					return [];
				}

				if (category.ancestors) {
					$.each(category.ancestors, function(key, category) {
						result += '_' + category.name;
					});
				}

				return result + '_' + category.name;
			})()
		});
	};

	utils.getPageBusinessUnitId = function() {
		return document.location.pathname.replace(/^(?:\/(?:catalog|product))?\/(slices?\/(?:[^\/]*\/)?[^\/]*|[^\/]*).*$/i, '$1');
	};

	var abstractAnalytics = {
		add: function(storageName, itemData, storageMaxLength) {
			if (!window.localStorage) {
				return;
			}

			try {
				var data = JSON.parse(localStorage.getItem(storageName)) || [];
			} catch(e) {
				data = [];
			}

			// Поскольку при переполнении переменной удаляются первые товары, важно, чтобы повторное добавление
			// товара перемещало его в конец массива
			for (var i = 0; i < data.length; i++) {
				if (data[i][0] == itemData[0]) {
					data.splice(i, 1);
					break;
				}
			}

			data.push(itemData);

			while (JSON.stringify(data).length > storageMaxLength) {
				data.shift();
			}

			localStorage.setItem(storageName, JSON.stringify(data));
		},
		get: function(storageName, itemsIdentifiers) {
			if (!window.localStorage) {
				return;
			}

			try {
				var data = JSON.parse(localStorage.getItem(storageName)) || [];
			} catch(e) {
				data = [];
			}

			var result = [];
			for (var i = 0; i < data.length; i++) {
				if (itemsIdentifiers.indexOf(data[i][0]) != -1) {
					result.push(data[i]);
				}
			}

			return result;
		},
		clean: function(storageName) {
			if (!window.localStorage) {
				return;
			}

			localStorage.removeItem(storageName);
		}
	};

	utils.analytics = {

		/**
		 * Проверка доступности трекеров
		 * @returns {boolean}
		 */
		isEnabled: function() {
			return typeof ga === 'function' && typeof ga.getAll == 'function' && ga.getAll().length != 0
		},

		/**
		 * E-commerce common helper
		 * @param action Действие
		 * @param elem Кнопка "купить" или объект для для GA (определяется по наличию свойства id)
		 * @param additionalData
		 */
		addEcommData: function(action, elem, additionalData) {
			var data = (elem && (typeof elem.tagName != 'undefined')) ? $(elem).data('ecommerce') : elem;
			if (!this.isEnabled || typeof data != 'object') return;
			if (typeof additionalData != 'undefined') data = $.extend({}, data, additionalData);
			if (this.isEnabled()) ga(action, data);
			console.log('[GA] %s', action, data)
		},

		/**
		 * E-commerce ec:addImpression helper
		 * @param elem Кнопка "купить" или объект для для GA (определяется по наличию свойства id)
		 * @param additionalData
		 */
		addImpression: function(elem, additionalData) {
			this.addEcommData('ec:addImpression', elem, additionalData);
		},

		/**
		 * E-commerce ec:addProduct helper
		 * @param elem Кнопка "купить" или объект для для GA (определяется по наличию свойства id)
		 * @param additionalData
		 */
		addProduct: function(elem, additionalData) {
			this.addEcommData('ec:addProduct', elem, additionalData)
		},

		setAction: function(action, params) {
			if (this.isEnabled()) ga('ec:setAction', action, typeof params !== 'undefined' ? params : {});
			console.log('[GA] ec:setAction %s', action, params)
		},

		// SITE-5466
		reviews: {
			add: function(productUi, avgScore, firstPageAvgScore, categoryName) {
				// Размер хранилища выбран на основе исследования из задачи ADM-457 За эталон одной записи была взята
				// запись "["00203e03-68ce-4a85-8862-8e7cd1542756",8.78,8.78,"Мобильные телефоны"]".
				return abstractAnalytics.add('enter.analytics.reviews', [productUi, avgScore, firstPageAvgScore, categoryName], 3000);
			},
			get: function(productUis) {
				var result = abstractAnalytics.get('enter.analytics.reviews', productUis);
				for (var i = 0; i < result.length; i++) {
					result[i] = {
						avgScore: result[i][1],
						firstPageAvgScore: result[i][2],
						categoryName: result[i][3]
					};
				}

				return result;
			},
			// Должна вызываться, как мы договорились с Захаровым Николаем Викторовичем, лишь при оформлении заказа
			// через обычное оформление заказа (не через одноклик или слоты).
			clean: function() {
				return abstractAnalytics.clean('enter.analytics.reviews');
			}
		},
		// SITE-5062
		productPageSenders: {
			add: function(productUi, sender) {
				// Размер хранилища выбран на основе исследования из задачи ADM-457 За эталон одной записи была взята
				// запись "["00203e03-68ce-4a85-8862-8e7cd1542756",{"name":"retailrocket","position":"ProductAccessories","type":"alsoBought","method":"CrossSellItemToItems","from":"cart_rec"}],".
				if (sender) {
					return abstractAnalytics.add('enter.analytics.productPageSenders', [productUi, sender], 2500);
				}
			},
			get: function($button) {
				var sender = $button.data('sender') || {};

				if ($('body').data('template') == 'product_card' && ($button.data('location') == 'product-card' || $button.data('location') == 'userbar')) {
					var
						product = $('#jsProductCard').data('value') || {},
						productPageSender = abstractAnalytics.get('enter.analytics.productPageSenders', [product.ui])
					;

					productPageSender = productPageSender[0] ? productPageSender[0][1] : null;

					if (productPageSender && typeof productPageSender == 'object') {
						function isSenderPresent(sender) {
							if (sender && typeof sender == 'object') {
								for (var key in sender) {
									if (sender.hasOwnProperty(key) && key != 'from' && sender[key]) {
										return true;
									}
								}
							}

							return false;
						}

						if (!isSenderPresent(sender)) {
							sender = productPageSender;
						}
					}

					if (sender && typeof sender.name == 'string' && sender.name.indexOf('filter') == 0) {
						sender.isFromProductCard = true;
					}
				}

				return sender;
			},
			// Должна вызываться, как мы договорились с Захаровым Николаем Викторовичем, лишь при оформлении заказа
			// через обычное оформление заказа (не через одноклик или слоты).
			clean: function() {
				return abstractAnalytics.clean('enter.analytics.productPageSenders');
			}
		},
		// SITE-5072
		// Используется для сохранения источников переходов, осуществлённых из рекомендаций с карточек товаров
		// маркетплейс. Используется отдельное хранилище (а не хранилище productPageSenders), т.к. данные источники
		// не должны перезатирать источники из productPageSenders.
		productPageSenders2: {
			add: function(productUi, sender2) {
				// Размер хранилища выбран на основе исследования из задачи ADM-457. За эталон одной записи была взята
				// запись "["00203e03-68ce-4a85-8862-8e7cd1542756","marketplace"],".
				if (sender2) {
					return abstractAnalytics.add('enter.analytics.productPageSenders2', [productUi, sender2], 1000);
				}
			},
			get: function($button) {
				var sender2 = $button.data('sender2') || '';

				if ($('body').data('template') == 'product_card' && ($button.data('location') == 'product-card' || $button.data('location') == 'userbar')) {
					var
						product = $('#jsProductCard').data('value') || {},
						productPageSender2 = abstractAnalytics.get('enter.analytics.productPageSenders2', [product.ui])
					;

					productPageSender2 = productPageSender2[0] ? productPageSender2[0][1] : null;

					if (productPageSender2 && !sender2) {
						sender2 = productPageSender2;
					}
				}

				return sender2;
			},
			// Должна вызываться, как мы договорились с Захаровым Николаем Викторовичем, лишь при оформлении заказа
			// через обычное оформление заказа (не через одноклик или слоты).
			clean: function() {
				return abstractAnalytics.clean('enter.analytics.productPageSenders2');
			}
		},
		soloway: {
			send: function(data) {
				if (!ENTER.config.pageConfig.analytics.soloway) {
					return;
				}

				if (!data.user) {
					data.user = {
						id: ENTER.config.userInfo.user.id
					};
				}

				var send = function(h){function k(){var a=function(d,b){if(this instanceof AdriverCounter)d=a.items.length||1,a.items[d]=this,b.ph=d,b.custom&&(b.custom=a.toQueryString(b.custom,";")),a.request(a.toQueryString(b));else return a.items[d]};a.httplize=function(a){return(/^\/\//.test(a)?location.protocol:"")+a};a.loadScript=function(a){try{var b=g.getElementsByTagName("head")[0],c=g.createElement("script");c.setAttribute("type","text/javascript");c.setAttribute("charset","windows-1251");c.setAttribute("src",a.split("![rnd]").join(Math.round(1E6*Math.random())));c.onreadystatechange=function(){/loaded|complete/.test(this.readyState)&&(c.onload=null,b.removeChild(c))};c.onload=function(){b.removeChild(c)};b.insertBefore(c,b.firstChild)}catch(f){}};a.toQueryString=function(a,b,c){b=b||"&";c=c||"=";var f=[],e;for(e in a)a.hasOwnProperty(e)&&f.push(e+c+escape(a[e]));return f.join(b)};a.request=function(d){var b=a.toQueryString(a.defaults);a.loadScript(a.redirectHost+"/cgi-bin/erle.cgi?"+d+"&rnd=![rnd]"+(b?"&"+b:""))};a.items=[];a.defaults={tail256:document.referrer||"unknown"};a.redirectHost=a.httplize("//ad.adriver.ru");return a}var g=document;"undefined"===typeof AdriverCounter&&(AdriverCounter=k());new AdriverCounter(0,h)};

				var custom = {};
				if (data.user.id) {
					custom['153'] = data.user.id;
				}

				switch (data.action) {
					case 'pageView':
						send({
							"sid": ENTER.config.pageConfig.analytics.soloway.id,
							"bt": 62,
							"custom": custom
						});
						break;
					case 'productView':
						custom["10"] = data.product.ui;
						custom["11"] = data.product.category.ui;

						send({
							"sid": ENTER.config.pageConfig.analytics.soloway.id,
							"bt": 62,
							"custom": custom
						});
						break;
					case 'orderComplete':
						$.each(data.orders, function(key, order) {
							custom["150"] = order.number;
							custom["151"] = order.sum;

							send({
								"sid": ENTER.config.pageConfig.analytics.soloway.id,
								"sz": "order",
								"bt": 62,
								"custom": custom
							});
						});
						break;
					case 'userRegistrationComplete':
						if (data.user.id) {
							custom['152'] = data.user.id;
						}
						send({
							"sid": ENTER.config.pageConfig.analytics.soloway.id,
							"sz": "regist",
							"bt": 62,
							"custom": custom
						});
						break;
					case 'basketProductAdd':
						custom["10"] = data.product.ui;
						custom["11"] = data.product.category.ui;

						send({
							"sid": ENTER.config.pageConfig.analytics.soloway.id,
							"sz": "add_basket",
							"bt": 62,
							"custom": custom
						});
						break;
					case 'basketProductDelete':
						custom["10"] = data.product.ui;
						custom["11"] = data.product.category.ui;

						send({
							"sid": ENTER.config.pageConfig.analytics.soloway.id,
							"sz": "del_basket",
							"bt": 62,
							"custom": custom
						});
						break;
				}
			}
		},
		flocktory: {
			send: function(data) {
				function send(data) {
					window.flocktory = window.flocktory || [];
					window.flocktory.push(data);
				}

				switch (data.action) {
					case 'postcheckout':
						if (!ENTER.config.pageConfig.analytics.flocktory.postcheckout || !ENTER.config.pageConfig.analytics.flocktory.postcheckout.enabled) {
							break;
						}

						var orderNumber = 0;
						$.each(data.orders, function(key, order) {
							orderNumber++;
							
							send(['postcheckout', {
								user: {
									name: $.trim(order.firstName + ' ' + order.lastName),
									email: order.email ? order.email : order.phone + '@unknown.email', // http://flocktory.com/help
									sex: order.user.sex == 1 ? 'm' : (order.user.sex == 2 ? 'f' : '')
								},
								order: {
									id: order.numberErp,
									price: order.sum,
									items: $.map(order.products, function(product) {
										return {
											id: product.id,
											title: product.name,
											price: product.price,
											image: product.images['120x120'].url,
											count: product.quantity
										};
									})
								},
								spot: orderNumber > 1 ? 'no_popup' : data.spot || ''
							}]);
						});
						break;
					default:
						send([data.action, {item: data.item}]);
						break;
				}
			}
		}
	};

	utils.paymentStart = {
		showError: function($form) {
			$form.closest('.js-order-payment-container').find('.js-order-payment-hint').addClass('error').text('Ошибка. Попробуйте обновить страницу и выполнить действие снова.');
		},
		revertError: function($formContainer) {
			$formContainer.closest('.js-order-payment-container').find('.js-order-payment-hint').removeClass('error').text('Вы будете перенаправлены на сайт платежной системы.');
		},
		bindFormSubmitHandler: function($form) {
			if ($form.attr('data-require-validation')) {
				$form.on('submit', function(e, preventCheckOrder) {
					if (!preventCheckOrder) {
						e.preventDefault();
						$.ajax({
							url: ENTER.utils.router.generateUrl('ajax.order.payment.start', {
								'paymentMethodId': $form.attr('data-payment-method-id'),
								'orderAccessToken': $form.attr('data-order-access-token')
							}),
							type: 'POST'
						}).success(function (response) {
							if (response && response.success) {
								$form.trigger('submit', [true]);
							} else {
								ENTER.utils.paymentStart.showError($form);
							}
						}).error(function () {
							ENTER.utils.paymentStart.showError($form);
						});
					}
				});
			}
		}
	};
	/*
	!function() {
		var
			$body = document.getElementsByTagName('body')[0],
			spinner,
			isInited = false;

		utils.overloadPreloader = {
			show: function() {
				if (!spinner) {
					spinner = new Spinner({
						lines: 11, // The number of lines to draw
						length: 5, // The length of each line
						width: 8, // The line thickness
						radius: 23, // The radius of the inner circle
						corners: 1, // Corner roundness (0..1)
						rotate: 0, // The rotation offset
						direction: 1, // 1: clockwise, -1: counterclockwise
						color: '#666', // #rgb or #rrggbb or array of colors
						speed: 1, // Rounds per second
						trail: 62, // Afterglow percentage
						shadow: false, // Whether to render a shadow
						hwaccel: true, // Whether to use hardware acceleration
						className: 'spinner-blackfriday', // The CSS class to assign to the spinner
						zIndex: 2e9, // The z-index (defaults to 2000000000)
						top: '50%', // Top position relative to parent
						left: '50%' // Left position relative to parent
					}).spin($body);
				}

				if (!isInited) {
					$(document).keyup(function(e) {
						if (e.keyCode == 27 && spinner) {
							spinner.stop();
							spinner = null;
						}
					});

					isInited = true;
				}
			},
			hide: function() {
				if (spinner) {
					spinner.stop();
					spinner = null;
				}
			}
		};
	}();
	*/
}(window.ENTER));