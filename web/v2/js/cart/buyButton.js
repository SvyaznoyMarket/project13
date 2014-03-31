/**
 * Обработчик для кнопок купить
 *
 * @author		Zaytsev Alexandr
 * 
 * @requires	jQuery, ENTER.utils.BlackBox
 */

var ENTER = ENTER || {}; 

;(function( ENTER ) {
	

	var
		body = $('body'),
		clientCart = ENTER.config.clientCart;
	// end of vars

	
	var
		/**
		 * Добавление в корзину на сервере. Получение данных о покупке и состоянии корзины. Маркировка кнопок.
		 */
		buy = function buy() {
			var
				button = $(this),
				url = button.attr('href');
			// end of vars

			var
				addToCart = function addToCart( data ) {
					var groupBtn = button.data('group'),
						upsale = button.data('upsale') ? button.data('upsale') : null,
						product = button.parents('.jsSliderItem').data('product');
					//end of vars

					if ( !data.success ) {
						return false;
					}

					button.removeClass('mLoading');

					if ( data.product ) {
						data.product.isUpsale = product && product.isUpsale ? true : false;
						data.product.fromUpsale = upsale && upsale.fromUpsale ? true : false;
					}

					$('.jsBuyButton[data-group="'+groupBtn+'"]').html('В корзине').addClass('mBought').attr('href', '/cart');
					body.trigger('addtocart', [data]);
					body.trigger('getupsale', [data, upsale]);
					body.trigger('updatespinner',[groupBtn]);
				};
			// end of functions

			$.get(url, addToCart);

			return false;
		},

		/**
		 * Хандлер кнопки купить
		 */
		buyButtonHandler = function buyButtonHandler() {
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
		},

		/**
		 * Маркировка кнопок «Купить»
		 * см.BlackBox startAction
		 */
		markCartButton = function markCartButton() {
			var
				products = clientCart.products,
				i,
				len;
			// end of vars
			
			console.info('markCartButton');

			for ( i = 0, len = products.length; i < len; i++ ) {
				$('.'+products[i].cartButton.id).html('В корзине').addClass('mBought').attr('href','/cart');
			}
		};
	// end of functions
	

	$(document).ready(function() {
		body.bind('markcartbutton', markCartButton);
		body.on('click', '.jsBuyButton', buyButtonHandler);
		body.on('buy', '.jsBuyButton', buy);
	});
}(window.ENTER));



/**
 * Показ окна о совершенной покупке, парсинг данных от сервера, аналитика
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, printPrice, BlackBox
 * @param		{event}		event 
 * @param		{Object}	data	данные о том что кладется в корзину
 */
(function( ENTER ) {

	var
		utils = ENTER.utils,
		blackBox = utils.blackBox,
		body = $('body');
	// end of vars
	

	var
		/**
		 * Обработка покупки, парсинг данных от сервера, запуск аналитики
		 */
		buyProcessing = function buyProcessing( event, data ) {

			if ( data.redirect ) {
				console.warn('redirect');

				document.location.href = data.redirect;
			}
			else if ( blackBox ) {
				blackBox.basket().add( data );
			}
		};
	//end of functions

	body.on('addtocart', buyProcessing);

	// analytics
	body.on('addtocart', addtocartAnalytics);

}(window.ENTER));
