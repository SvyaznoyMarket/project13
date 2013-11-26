/**
 * Обработчик для кнопок купить
 *
 * @author		Zaytsev Alexandr
 * 
 * @requires	jQuery, ENTER.utils.BlackBox
 */
;(function() {

	/**
	 * Добавление в корзину на сервере. Получение данных о покупке и состоянии корзины. Маркировка кнопок.
	 * 
	 * @param  {Event}	e
	 */
	var buy = function buy() {
		var button = $(this),
			url = button.attr('href');
		// end of vars

		var addToCart = function addToCart( data ) {
			var groupBtn = button.data('group');

			if ( !data.success ) {
				return false;
			}

			button.removeClass('mLoading');

			$('.jsBuyButton[data-group="'+groupBtn+'"]').html('В корзине').addClass('mBought').attr('href', '/cart');
			$('body').trigger('addtocart', [data]);
			$('body').trigger('updatespinner',[groupBtn]);
		};

		$.get(url, addToCart);

		return false;
	};

	/**
	 * Хандлер кнопки купить
	 * 
	 * @param  {Event}	e
	 */
	var buyButtonHandler = function buyButtonHandler() {
		var button = $(this),
			url = button.attr('href');
		// end of vars
		

		if ( button.hasClass('mDisabled') ) {
			return false;
		}

		if ( button.hasClass('mBought') ) {
			document.location.href(url);

			return false;
		}

		button.addClass('mLoading');
		button.trigger('buy');

		return false;
	};

	/**
	 * Маркировка кнопок «Купить»
	 * см.BlackBox startAction
	 * 
	 * @param	{event}		event          
	 * @param	{Object}	markActionInfo Данные полученые из Action
	 */
	var markCartButton = function markCartButton( event, markActionInfo ) {
		for ( var i = 0, len = markActionInfo.product.length; i < len; i++ ) {
			$('.'+markActionInfo.product[i].id).html('В корзине').addClass('mBought').attr('href','/cart');
		}
	};
	
	$(document).ready(function() {
		$('body').bind('markcartbutton', markCartButton);
		$('body').on('click', '.jsBuyButton', buyButtonHandler);
		$('body').on('buy', '.jsBuyButton', buy);
	});
}());


/**
 * Показ окна о совершенной покупке, парсинг данных от сервера, аналитика
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, printPrice, BlackBox
 * @param		{event}		event 
 * @param		{Object}	data	данные о том что кладется в корзину
 */
(function( global ) {

	var utils = global.ENTER.utils,
		blackBox = utils.blackBox;
	// end of vars
	

		/**
		 * KISS Аналитика для добавления в корзину
		 */
	var kissAnalytics = function kissAnalytics( data ) {
			var productData = data.product,
				serviceData = data.service,
				warrantyData = data.warranty,
				nowUrl = window.location.href,
				toKISS = {};
			//end of vars
			
			if ( typeof(_kmq) === 'undefined' ) {
				return;
			}

			if ( productData ) {
				toKISS = {
					'Add to Cart SKU': productData.article,
					'Add to Cart SKU Quantity': productData.quantity,
					'Add to Cart Product Name': productData.name,
					'Add to Cart Root category': productData.category[0].name,
					'Add to Cart Root ID': productData.category[0].id,
					'Add to Cart Category name': ( productData.category ) ? productData.category[productData.category.length - 1].name : 0,
					'Add to Cart Category ID': ( productData.category ) ? productData.category[productData.category.length - 1].id : 0,
					'Add to Cart SKU Price': productData.price,
					'Add to Cart Page URL': nowUrl,
					'Add to Cart F1 Quantity': productData.serviceQuantity
				};

				_kmq.push(['record', 'Add to Cart', toKISS]);
			}

			if ( serviceData ) {
				toKISS = {
					'Add F1 F1 Name': serviceData.name,
					'Add F1 F1 Price': serviceData.price,
					'Add F1 SKU': productData.article,
					'Add F1 Product Name': productData.name,
					'Add F1 Root category': productData.category[0].name,
					'Add F1 Root ID': productData.category[0].id,
					'Add F1 Category name': ( productData.category ) ? productData.category[productData.category.length - 1].name : 0,
					'Add F1 Category ID': ( productData.category ) ? productData.category[productData.category.length - 1].id : 0
				};

				_kmq.push(['record', 'Add F1', toKISS]);
			}

			if ( warrantyData ) {
				toKISS = {
					'Add Warranty Warranty Name': warrantyData.name,
					'Add Warranty Warranty Price': warrantyData.price,
					'Add Warranty SKU': productData.article,
					'Add Warranty Product Name': productData.name,
					'Add Warranty Root category': productData.category[0].name,
					'Add Warranty Root ID': productData.category[0].id,
					'Add Warranty Category name': ( productData.category ) ? productData.category[productData.category.length - 1].name : 0,
					'Add Warranty Category ID': ( productData.category ) ? productData.category[productData.category.length - 1].id : 0
				};

				_kmq.push(['record', 'Add Warranty', toKISS]);
			}
		},

		/**
		 * Google Analytics аналитика добавления в корзину
		 */
		googleAnalytics = function googleAnalytics( data ) {
			var productData = data.product;

			if ( productData ) {
				if ( typeof _gaq !== 'undefined' ){
					_gaq.push(['_trackEvent', 'Add2Basket', 'product', productData.article]);
				}
			}
		},

		/**
		 * Soloway аналитика добавления в корзину
		 */
		adAdriver = function adAdriver( data ) {
			var productData = data.product,
				offer_id = productData.id,
				category_id =  ( productData.category ) ? productData.category[productData.category.length - 1].id : 0;
			// end of vars


			var s = 'http://ad.adriver.ru/cgi-bin/rle.cgi?sid=182615&sz=add_basket&custom=10='+offer_id+';11='+category_id+'&bt=55&pz=0&rnd=![rnd]',
				d = document,
				i = d.createElement('IMG'),
				b = d.body;

			s = s.replace(/!\[rnd\]/, Math.round(Math.random()*9999999)) + '&tail256=' + escape(d.referrer || 'unknown');
			i.style.position = 'absolute';
			i.style.width = i.style.height = '0px';
			
			i.onload = i.onerror = function(){
				b.removeChild(i);
				i = b = null;
			};

			i.src = s;
			b.insertBefore(i, b.firstChild);
		},

		/**
		 * Обработчик добавления товаров в корзину. Рекомендации от RetailRocket
		 */
		addToRetailRocket = function addToRetailRocket( data ) {
			var product = data.product,
				dataToLog;
			// end of vars


			if ( typeof rcApi === 'object' ) {
				try {
					rcApi.addToBasket(product.id);
				}
				catch ( err ) {
					dataToLog = {
						event: 'rcApi.addToBasket',
						type: 'ошибка отправки данных в RetailRocket',
						err: err
					};

					utils.logError(dataToLog);
				}
			}


		},


		/**
		 * Обработка покупки, парсинг данных от сервера, запуск аналитики
		 */
		buyProcessing = function buyProcessing( event, data ) {
			var basket = data.cart,
				product = data.product,
				tmpitem = {
					'id': product.id,
					'title': product.name,
					'price' : window.printPrice(product.price),
					'priceInt' : product.price,
					'imgSrc': product.img,
					'productLink': product.link,
					'totalQuan': basket.full_quantity,
					'totalSum': window.printPrice(basket.full_price),
					'linkToOrder': basket.link
				};
			// end of vars


			kissAnalytics(data);
			googleAnalytics(data);
			adAdriver(data);
			addToRetailRocket(data);

			if ( data.redirect ) {
				console.warn('redirect');

				document.location.href = data.redirect;
			}
			else if ( blackBox ) {
				blackBox.basket().add( tmpitem );
			}
		};
	//end of vars

	$(document).ready(function() {
		$('body').bind('addtocart', buyProcessing);
	});
}(this));