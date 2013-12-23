/**
 * Всплывающая синяя плашка с предложением о подписке
 * Срабатывает при возникновении события showsubscribe.
 * см.BlackBox startAction
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, FormValidator, docCookies
 * 
 * @param		{event}		event
 * @param		{Object}	subscribe			Информация о подписке
 * @param		{Boolean}	subscribe.agreed	Было ли дано согласие на подписку в прошлый раз
 * @param		{Boolean}	subscribe.show		Показывали ли пользователю плашку с предложением о подписке
 */
;(function() {
	var
		lboxCheckSubscribe = function lboxCheckSubscribe( event, subscribe ) {

		var
			notNowShield = $('.bSubscribeLightboxPopupNotNow'),
			subPopup = $('.bSubscribeLightboxPopup'),
			input = $('.bSubscribeLightboxPopup__eInput'),
			submitBtn = $('.bSubscribeLightboxPopup__eBtn' ),
			inputValidator = new FormValidator({
				fields: [
					{
						fieldNode: input,
						customErr: 'Неправильный емейл',
						required: true,
						email: true,
						validBy: 'isEmail'
					}
				]
			} ),
			runValidation = function runValidation() {
				inputValidator.validate({
					onInvalid: function( err ) {
						console.log('Email is invalid');
						console.log(err);
					},
					onValid: function() {
						console.log('Email is valid');
					}
				});
			};
		// end of vars
		

		var
			subscribing = function subscribing() {
				var
					email = input.val(),
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
					window.docCookies.setItem('subscribed', 1, 157680000, '/');

					setTimeout(function() {
						subPopup.slideUp(300);
					}, 3000);

					// analytics
					if ( typeof _gaq !== 'undefined' ) {
						_gaq.push(['_trackEvent', 'Account', 'Emailing sign up', 'Page top']);
					}

					// subPopup.append('<iframe src="https://track.cpaex.ru/affiliate/pixel/173/'+email+'/" height="1" width="1" frameborder="0" scrolling="no" ></iframe>');
				});

				return false;
			},

			subscribeNow = function subscribeNow() {
				subPopup.slideDown(300);

				submitBtn.bind('click', subscribing);

				$('.bSubscribeLightboxPopup__eNotNow').bind('click', function() {
					var url = $(this).data('url');

					subPopup.slideUp(300, subscribeLater);
					window.docCookies.setItem('subscribed', 0, 157680000, '/');
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

		if ( !subscribe.show ) {
			if ( !subscribe.agreed ) {
				subscribeLater();
			}

			return false;
		}
		else {
			subscribeNow();
		}

		input.bind('keyup', runValidation);
	};

	$('body').bind('showsubscribe', lboxCheckSubscribe);
}());