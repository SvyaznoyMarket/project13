/**
 * Подписка на снижение цены
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery, jQuery.placeholder plugin, jQuery.emailValidate plugin
 */
;(function(){
	var lowPriceNotifer = function() {
		var notiferWrapper = $('.priceSale'),
            subscribeWrapper = $('.bSubscibeWrapper'),
			notiferButton = $('.jsLowPriceNotifer'),
			submitBtn = $('.bLowPriceNotiferPopup__eSubmitEmail'),
			input = $('.bLowPriceNotiferPopup__eInputEmail'),
			notiferPopup = $('.bLowPriceNotiferPopup'),
			error = $('.bLowPriceNotiferPopup__eError'),
			subscribe = $('.jsSubscribe');
		// end of vars

			/**
			 * Скрыть окно подписки на снижение цены
			 */
		var lowPriceNitiferHide = function lowPriceNitiferHide() {
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
             userLogged = function userLogin(event, userInfo) {
                if ( userInfo ) {
                    if ( userInfo.isSubscribed ) {
                        subscribeWrapper.hide();
                    }
                    if ( userInfo.name ) {
                        notiferWrapper.show();
                    }
                }
            },

			/**
			 * Показать окно подписки на снижение цены
			 */
			lowPriceNitiferShow = function lowPriceNitiferShow() {
				notiferPopup.fadeIn(300);
				notiferPopup.find('.close').bind('click', lowPriceNitiferHide);

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
						error.show().html(res.error.message);
					}

					return false;
				}

				lowPriceNitiferHide();
				notiferPopup.remove();
				notiferButton.remove();
			},

			/**
			 * Проверка чекбокса "Акции и суперпредложения"
			 */
			checkSubscribe = function checkSubscribe() {
				if ( subscribe.length && subscribe.is(':checked') ) {
					return true;
				}

				return false;
			}

			/**
			 * Отправка данных на сервер
			 */
			lowPriceNotiferSubmit = function lowPriceNotiferSubmit() {
				var submitUrl = submitBtn.data('url');
				
				submitUrl += encodeURI('?email=' + input.val() + '&subscribe=' + (checkSubscribe() ? 1 : 0));
				$.get( submitUrl, resFromServer);

				return false;
			};
		// end of functions

		
		submitBtn.bind('click', lowPriceNotiferSubmit);
		notiferButton.bind('click', lowPriceNitiferShow);
		$('body').bind('userLogged', userLogged);
	};


	$(document).ready(function() {
		if ($('.jsLowPriceNotifer').length){
			lowPriceNotifer();
		}
	});
}());