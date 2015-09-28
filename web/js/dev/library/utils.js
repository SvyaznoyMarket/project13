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

				/* SITE-4472 Аналитика по АБ-тесту платного самовывоза и рекомендаций из корзины */
				if (ENTER.config.pageConfig.selfDeliveryTest && ENTER.config.pageConfig.selfDeliveryLimit > parseInt(o.paySum, 10) - o.delivery.price) productName = productName + ' (paid pickup)';

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
					'id': p.id,
					'name': productName,
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
			var data = typeof elem.tagName != 'undefined' ? $(elem).data('ecommerce') : elem;
			if (!this.isEnabled || typeof data != 'object') return;
			if (typeof additionalData != 'undefined') data = $.extend({}, data, additionalData);
			ga(action, data);
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

					productPageSender = (productPageSender[0] ? productPageSender[0][1] : null) || product.oldProductPageSender; // TODO: удалить oldProductPageSender через неделю после релиза SITE-5543

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

					productPageSender2 = (productPageSender2[0] ? productPageSender2[0][1] : null) || product.oldProductPageSender2; // TODO: удалить oldProductPageSender2 через неделю после релиза SITE-5543

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
		}
	};

}(window.ENTER));