/**
 * Всплывающая синяя плашка с предложением о подписке
 *
 * @requires	jQuery, FormValidator, docCookies
 */
;(function() {
	var
		$body = $('body'),
		subscribebar = $('.js-subscribebar'),
		emailInput = $('.js-subscribebar-email'),
		submitBtn = $('.js-subscribebar-subscribeButton');

	emailInput.placeholder();

	submitBtn.bind('click', function(e) {
		e.preventDefault();
		var $self = $(this);
		
		(new FormValidator({
			fields: [
				{
					fieldNode: emailInput,
					customErr: 'Неверно введен email',
					require: true,
					validBy: 'isEmail'
				}
			]
		})).validate({
			onInvalid: function(err) {
				console.log('Email is invalid');
				console.log(err);
			},
			onValid: function() {
				console.log('Email is valid');
				$.post($self.data('url'), {email: emailInput.val()}, function(res) {
					if (!res.success) {
						return false;
					}
	
					subscribebar.html('<span class="bSubscribeLightboxPopup__eTitle mType">Спасибо! подтверждение подписки отправлено на указанный e-mail</span>');
					docCookies.setItem('subscribed', 1, 157680000, '/');
	
					setTimeout(function() {
						$body.trigger('bodybar-hide');
					}, 3000);
	
					// analytics
					if (typeof _gaq != 'undefined') {
						_gaq.push(['_trackEvent', 'Account', 'Emailing sign up', 'Page top']);
					}
	
					// subPopup.append('<iframe src="https://track.cpaex.ru/affiliate/pixel/173/'+email+'/" height="1" width="1" frameborder="0" scrolling="no" ></iframe>');
				});
			}
		});
	});
}());