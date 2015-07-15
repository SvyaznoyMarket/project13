/**
 * Кредит для карточки товара
 *
 * @author		Kotov Ivan, Zaytsev Alexandr
 * @requires	jQuery, printPrice, docCookies, JsHttpRequest.js
 */
;(function() {

	var creditBoxNode = $('.creditbox');

	if( creditBoxNode.length > 0 ) {

		var	$body = $(document.body),
			priceNode = creditBoxNode.find('.creditbox__sum strong');

		window.creditBox = {

			init: function() {

				var	creditd = $('input[name=dc_buy_on_credit]').data('model'),
					label = creditBoxNode.find('label');

				$('.jsProductCreditRadio').on('change', function() {
					var status = $(this).val(), // 'on'|'off'
						$link = $('.js-WidgetBuy .jsBuyButton');

					if ($link.length > 0) {
                        $link.attr('href', ENTER.utils.setURLParam('credit', status, $link.attr('href')));
                        $link.attr('href', ENTER.utils.setURLParam('sender2', 'credit', $link.attr('href')));
                    }
				});

				if (typeof window.dc_getCreditForTheProduct == 'function') dc_getCreditForTheProduct(
					4427,
					window.docCookies.getItem('enter'),
					'getPayment',
					{ price : creditd.price, count : 1, type : creditd.product_type },
					function( result ) {
                        console.info('dc_getCreditForTheProduct.result', result);
						if( ! 'payment' in result ){
							return;
						}
						if( result.payment > 0 ) {
							priceNode.html( printPrice( Math.ceil(result.payment) ) );
							creditBoxNode.show();
						}
					}
				);
			}

		};

		if (ENTER.config.userInfo) {
			if ($.isArray(ENTER.config.userInfo.cartProducts)) {
				var product_id = $('#jsProductCard').data('value')['id'];
				$.each(ENTER.config.userInfo.cartProducts, function(i, val) {
					if (val['isCredit'] && val['id'] == product_id) {
						$('#creditinput').attr('checked', true).trigger('change')
					}
				})
			}
		}

        $body.on('click', '.jsProductCreditRadio', function(){
            $body.trigger('trackGoogleEvent', ['Credit', 'Выбор опции', 'Карточка товара']);
        });
		
		creditBox.init();
	}
	else {

		var	productDesc = $('.bProductDesc');

		if ( productDesc.length && !productDesc.hasClass('mNoCredit') ) {
			productDesc.addClass('mNoCredit'); // добавим класс, дабы скрыть кредитный чекбокс
		}
	}
}());