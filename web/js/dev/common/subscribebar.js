/**
 * Всплывающая синяя плашка с предложением о подписке
 *
 * @requires	jQuery, FormValidator, docCookies
 */
;(function() {
	var $body = $('body');

	$('.js-subscribebar-email').placeholder();

	$('.js-subscribebar-subscribeButton').click(function(e) {
		e.preventDefault();
		var
			$subscribebar = $(e.currentTarget).closest('.js-subscribebar'),
			$emailInput = $('.js-subscribebar-email', $subscribebar),
			$emailError,
			email = $emailInput.val();

		function removeEmailError() {
			if ($emailError) {
				$emailError.off('click', emailErrorHandler);
				$emailError.remove();

				$emailInput.off('focus', emailInputHandler);
				$emailInput.removeClass('mError');
			}
		}

		function emailErrorHandler() {
			removeEmailError();
			$emailInput.focus();
		}

		function emailInputHandler() {
			removeEmailError();
		}

		$.post(ENTER.utils.generateUrl('subscribe.create'), {email: email, error_msg: 'Вы уже подписаны на рассылку!'}, function(res) {
			if (850 == res.code || 619 == res.code) {
				removeEmailError();

				$emailError = $('<div class="bErrorText"><div class="bErrorText__eInner">' + res.data + '</div></div>').insertBefore($emailInput);
				$emailError.click(emailErrorHandler);

				$emailInput.focus(emailInputHandler);
				$emailInput.addClass('mError');
			} else {
				$subscribebar.html('<div class="sbscrBar_lbl">' + res.data + '</div>');

				setTimeout(function() {
					$body.trigger('bodybar-hide');
				}, 3000);

				if (res.success) {
					docCookies.setItem('subscribed', 1, 157680000, '/');

					if (typeof _gaq != 'undefined') {
						var pageCode;
						if ('/' == location.pathname) {
							pageCode = 1;
						} else if (0 == location.pathname.indexOf('/catalog/')) {
							pageCode = 2;
						} else if (0 == location.pathname.indexOf('/product/')) {
							pageCode = 3;
						} else {
							pageCode = 4;
						}

						_gaq.push(['_trackEvent', 'subscription', 'subscribe_form', email, pageCode]);
					}

					// subPopup.append('<iframe src="https://track.cpaex.ru/affiliate/pixel/173/'+email+'/" height="1" width="1" frameborder="0" scrolling="no" ></iframe>');
				}
			}
		});
	});
}());