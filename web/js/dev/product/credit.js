/**
 * Кредит для карточки товара
 *
 * @author		Kotov Ivan, Zaytsev Alexandr
 * @requires	jQuery, printPrice, docCookies, JsHttpRequest.js
 */
;(function() {
	if( $('.creditbox').length ) {
		var creditBoxNode = $('.creditbox'),
			priceNode = creditBoxNode.find('.creditbox__sum strong');
		// end of vars

		window.creditBox = {
			cookieTimeout: null,
			
			toggleCookie: function( state ) {
				clearTimeout( this.cookieTimeout );
				this.cookieTimeout = setTimeout( function(){
					docCookies.setItem(false, 'credit_on', state ? 1 : 0 , 60*60, '/');
				}, 200 );
			},

			init: function() {
				var self = this,
					creditd = $('input[name=dc_buy_on_credit]').data('model');
				// end of vars

				$('.creditbox label').click(function( e ) {
					var target = $(e.target);

					e.stopPropagation();

					if ( target.is('input') ) {
						return false;
					}
					
					$(this).toggleClass('checked');
					self.toggleCookie( $(this).hasClass('checked') );
				});

				if ( this.getState() === 1 ) {
					$('.creditbox label').addClass('checked');
				}

				creditd.count = 1;
				creditd.cart = '/cart';

				dc_getCreditForTheProduct(
					4427,
					docCookies.getItem('enter_auth'),
					'getPayment',
					{ price : creditd.price, count : creditd.count, type : creditd.product_type },
					function( result ) {
						if( ! 'payment' in result ){
							return;
						}
						if( result.payment > 0 ) {
							priceNode.html( printPrice( Math.ceil(result.payment) ) );
							creditBoxNode.show();
						}
					}
				);
			},
			
			getState: function() {
				if( ! docCookies.hasItem('credit_on') ) {
					return 0;
				}

				return docCookies.getItem('credit_on');
			}
		};
		
		creditBox.init();
	}
}());