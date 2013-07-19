/**
 * Всплывающая синяя плашка с предложением о подписке
 * Срабатывает при возникновении события showsubscribe.
 * см.BlackBox startAction
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, jQuery.emailValidate, docCookies
 * 
 * @param		{event}		event
 * @param		{Object}	subscribe			Информация о подписке
 * @param		{Boolean}	subscribe.agreed	Было ли дано согласие на подписку в прошлый раз
 * @param		{Boolean}	subscribe.show		Показывали ли пользователю плашку с предложением о подписке
 */
;(function() {
	var lboxCheckSubscribe = function lboxCheckSubscribe( event, subscribe ) {

		var notNowShield = $('.bSubscribeLightboxPopupNotNow'),
			subPopup = $('.bSubscribeLightboxPopup'),
			input = $('.bSubscribeLightboxPopup__eInput'),
			submitBtn = $('.bSubscribeLightboxPopup__eBtn');
		// end of vars
		

		var subscribing = function subscribing() {
				var email = input.val(),
					url = $(this).data('url');
				//end of vars
				
				if ( submitBtn.hasClass('mDisabled') ) {
					return false;
				}

				$.post(url, {email: email}, function( res ) {
					if( !res.success ) {
						return false;
					}
					
					subPopup.html('<span class="bSubscribeLightboxPopup__eTitle mType">Спасибо! подтверждение подписки отправлено на указанный e-mail</span>');
					docCookies.setItem(false, 'subscribed', 1, 157680000, '/');

					if( typeof(_gaq) !== 'undefined' ) {
						_gaq.push(['_trackEvent', 'Account', 'Emailing sign up', 'Page top']);
					}

					setTimeout(function() {
						subPopup.slideUp(300);
					}, 3000);
				});

				return false;
			},

			subscribeNow = function subscribeNow() {
				subPopup.slideDown(300);

				submitBtn.bind('click', subscribing);

				$('.bSubscribeLightboxPopup__eNotNow').bind('click', function() {
					var url = $(this).data('url');

					subPopup.slideUp(300, subscribeLater);
					docCookies.setItem(false, 'subscribed', 0, 157680000, '/');
					$.post(url);

					return false;
				});
			},

			subscribeLater = function subscribeLater() {
				notNowShield.slideDown(300);
				notNowShield.bind('click', function() {
					$(this).slideUp(300);
					subscribeNow();
				});
			};
		//end of functions

		input.placeholder();

		input.emailValidate({
			onValid: function() {
				input.removeClass('mError');
				submitBtn.removeClass('mDisabled');
			},
			onInvalid: function() {
				submitBtn.addClass('mDisabled');
				input.addClass('mError');
			}
		});

		if ( !subscribe.show ) {
			if ( !subscribe.agreed ) {
				subscribeLater();
			}

			return false;
		}
		else {
			subscribeNow();
		}
	};

	$("body").bind('showsubscribe', lboxCheckSubscribe);
}());