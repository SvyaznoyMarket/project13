/**
 * Подписка на снижение цены
 */
;$(function() {
	var $opener = $('.js-lowPriceNotifier-opener');
	if ($opener.length) {
		var
			data = $('.js-lowPriceNotifier').data('values'),
			$popup,
			$email,
			$error,
			$subscribe;

		/**
		 * Показать окно подписки на снижение цены
		 */
		function showPopup(e) {
			e.preventDefault();

			if (!$popup) {
				$opener.after($(Mustache.render($('#tpl-lowPriceNotifier-popup').html(), {
					price: data.price,
					userOfficeUrl: data.userOfficeUrl,
					actionChannelName: data.actionChannelName,
					isSubscribedToActionChannel: ENTER.config.userInfo.user.isSubscribedToActionChannel,
					showUserEmailNotify: ENTER.config.userInfo.user.isLogined && !ENTER.config.userInfo.user.email,
					userEmail: ENTER.config.userInfo.user.email
				})));

				$popup = $('.js-lowPriceNotifier-popup');
				$email = $('.js-lowPriceNotifier-popup-email');
				$error = $('.js-lowPriceNotifier-popup-error');
				$subscribe = $('.js-lowPriceNotifier-popup-subscribe');

				$('.js-lowPriceNotifier-popup-submit').on('click', submit);
				$popup.find('.js-lowPriceNotifier-popup-close').on('click', function(e) {
					e.preventDefault();
					hidePopup();
				});
			}

			$popup.fadeIn(300);
		}

		/**
		 * Скрыть окно подписки на снижение цены
		 */
		function hidePopup() {
			$popup.fadeOut(300);
		}

		/**
		 * Отправка данных на сервер
		 */
		function submit(e) {
			e.preventDefault();

			$.get(data.submitUrl + (data.submitUrl.indexOf('?') == -1 ? '?' : '&') + 'email=' + encodeURIComponent($email.val()) + '&subscribe=' + (checkSubscribe() ? 1 : 0), function(res) {
				if ( !res.success ) {
					$email.addClass('red');

					if ( res.error.message ) {
						$error.show().html(res.error.message);
					}

					return false;
				}

				if ($subscribe[0] && $subscribe[0].checked && typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent', 'subscription', 'subscribe_price_alert', $email.val()]);
				}

				hidePopup();
				$popup.remove();
				$opener.remove();
			});
		}

		/**
		 * Проверка чекбокса "Акции и суперпредложения"
		 */
		function checkSubscribe() {
			if ($subscribe.length && $subscribe.is(':checked')) {
				return true;
			}

			return false;
		}

		$opener.on('click', showPopup);
	}
});