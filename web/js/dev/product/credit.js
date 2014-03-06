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
					window.docCookies.setItem('credit_on', state ? 1 : 0 , 60*60, '/');
				}, 200 );
			},

			init: function() {
				var
					self = this,
					creditd = $('input[name=dc_buy_on_credit]').data('model'),
					label = $('.creditbox label');
				// end of vars

				$('input[type=radio][name=price_or_credit]').change(function( e ) {
					e.preventDefault();

					if ( !label.length ) {
						return;
					}

					label.toggleClass('checked');
					self.toggleCookie( label.hasClass('checked') );
				});

				if ( this.getState() === 1 ) {
					$('.creditbox label').addClass('checked');
				}

				creditd.count = 1;
				creditd.cart = '/cart';

				dc_getCreditForTheProduct(
					4427,
					window.docCookies.getItem('enter_auth'),
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
				if( ! window.docCookies.hasItem('credit_on') ) {
					return 0;
				}

				return window.docCookies.getItem('credit_on');
			}
		};
		
		creditBox.init();
	}
}());