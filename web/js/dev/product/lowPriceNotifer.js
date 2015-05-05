/**
 * Подписка на снижение цены
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery, jQuery.placeholder plugin
 */
;(function() {
	var lowPriceNotifer = function lowPriceNotifer() {
		var
			notiferWrapper = $('.priceSale'),
			notiferButton = $('.jsLowPriceNotifer'),
			submitBtn = $('.jsLowerPriceSubmitBtn'),
			input = $('.jsLowerPriceEmailInput'),
			notiferPopup = $('.bLowPriceNotiferPopup'),
			error = $('.jsLowerPriceError'),
			subscribe = $('.jsSubscribe');
		// end of vars

		var
			/**
			 * Скрыть окно подписки на снижение цены
			 */
			lowPriceNotifierHide = function lowPriceNotifierHide() {
				notiferPopup.fadeOut(300);

				return false;
			},

			/**
			 * Авторизованность пользователя
			 * Вызывается событием «userLogged» у body
			 *
			 * @param event
			 * @param userInfo — данные пользователя (если существуют)
			 */
			userLogged = function userLogin( event, userInfo ) {
				if ( userInfo ) {
					if( userInfo.name ) {
						// Если существует имя, значит юзер точно зарегистрирован и его данные получены
						notiferWrapper.show();
					}
					if( userInfo.email ) {
						input.val(userInfo.email);
					}
				}
			},

			/**
			 * Показать окно подписки на снижение цены
			 */
			lowPriceNotifierShow = function lowPriceNotifierShow() {
				notiferPopup.fadeIn(300);
				notiferPopup.find('.close').bind('click', lowPriceNotifierHide);

				return false;
			},

			/**
			 * Обработка ответа от сервера
			 * 
			 * @param	{Object}	res	Ответ от сервера
			 */
			resFromServer = function resFromServer( res ) {
				if ( !res.success ) {
					input.addClass('red');

					if ( res.error.message ) {
						error.hide().html(res.error.message).slideDown().delay(3000).slideUp();
					}

					return false;
				}

				if (subscribe.is(':checked')) {
					$('body').trigger('trackGoogleEvent', ['subscription', 'subscribe_price_alert', input.val()]);
				}

				lowPriceNotifierHide();
				notiferPopup.remove();
				notiferButton.remove();
			},

			/**
			 * Проверка чекбокса "Акции и суперпредложения"
			 */
			checkSubscribe = function checkSubscribe() {
				return !!(subscribe.length && subscribe.is(':checked'));
			},

			/**
			 * Отправка данных на сервер
			 */
			lowPriceNotifierSubmit = function lowPriceNotifierSubmit() {
				var submitUrl;
				console.log('click');
                submitUrl = ENTER.utils.setURLParam('email', input.val(), submitBtn.data('url'));
                submitUrl = ENTER.utils.setURLParam('subscribe', checkSubscribe() ? '1' : '0', submitUrl);
				$.get( submitUrl, resFromServer);

				return false;
			};
		// end of functions

		
		submitBtn.on('click', lowPriceNotifierSubmit);
		notiferButton.on('click', lowPriceNotifierShow);
		$('body').on('userLogged', userLogged);
	};


	$(document).ready(function() {
		if ( $('.jsLowPriceNotifer').length ){
			lowPriceNotifer();
		}
	});
}());