/**
 * Подписка на снижение цены
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery, jQuery.placeholder plugin, jQuery.emailValidate plugin
 */
;(function(){
	var lowPriceNotifer = function(){
		var notiferButton = $('.jsLowPriceNotifer');
		var submitBtn = $('.bLowPriceNotiferPopup__eSubmitEmail');
		var input = $('.bLowPriceNotiferPopup__eInputEmail');
		var notiferPopup = $('.bLowPriceNotiferPopup');
		var error = $('.bLowPriceNotiferPopup__eError');

		var lowPriceNitiferHide = function(){
			notiferPopup.fadeOut(300);
			return false;
		};

		var lowPriceNitiferShow = function(){
			notiferPopup.fadeIn(300);
			notiferPopup.find('.close').bind('click', lowPriceNitiferHide);
			return false;
		};

		var lowPriceNitiferSubmit = function(){
			if (submitBtn.hasClass('mDisabled')){
				error.show().html('Неправильный email');
				return false;
			}

			var submitUrl = submitBtn.data('url');
			submitUrl += encodeURI('?email='+input.val());

			var resFromServer = function(res){
				if (!res.success){
					input.addClass('red');
					if (res.error.message){
						error.show().html(res.error.message);
					}
					return false;
				}

				lowPriceNitiferHide();
				notiferPopup.remove();
				notiferButton.remove();
			};
			$.get( submitUrl, resFromServer);

			return false;
		};

		input.placeholder().emailValidate({
			onValid: function(){
				submitBtn.removeClass('mDisabled');
				error.hide();
			},
			onInvalid: function(){
				submitBtn.addClass('mDisabled');
			}
		});
		submitBtn.bind('click', lowPriceNitiferSubmit);
		notiferButton.bind('click', lowPriceNitiferShow);
	};

	$(document).ready(function() {
		if ($('.jsLowPriceNotifer').length){
			lowPriceNotifer();
		}
	});
}());