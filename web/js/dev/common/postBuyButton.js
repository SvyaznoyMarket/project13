/**
 * @author		Zaytsev Alexandr
 */
;$(function() {
	var
		$body = $('body'),
		errorCssClass = 'textfield-err',
		popupTemplate =
			'<div class="js-postBuyButton-popup popup--request">' +
				'<a href="#" class="js-postBuyButton-popup-close popup--request__close" title="Закрыть"></a>' +

				'<form action="' + ENTER.utils.generateUrl('order.postBuy') + '" method="post">' +
					'<input type="hidden" name="productId" value="{{productId}}" />' +
					'<input type="hidden" name="sender" value="{{sender}}" />' +

					'{{#full}}' +
						'<div class="popup--request__head msg--recall">Закажите обратный звонок и уточните</div>' +
						'<ul class="recall-list">' +
							'<li>Состав мебели и техники</li>' +
							'<li>Условия доставки, сборки и оплаты</li>' +
						'</ul>' +
					'{{/full}}' +

					'{{^full}}' +
						'<div class="popup--request__head">Отправить заявку</div>' +
					'{{/full}}' +

					'<div class="js-postBuyButton-popup-errors" style="display: none;">' +
					'</div>' +

					'<div class="popup__form-group">' +
						'<label class="label-for-input">*Телефон</label>' +
						'<input type="text" name="phone" value="{{userPhone}}" placeholder="8 (___) ___-__-__" data-mask="8 (xxx) xxx-xx-xx" />' +
						'<span class="js-postBuyButton-popup-error popup__form-group__error" style="display: none">Неверный формат телефона</span>' +
					'</div>' +

					'<div class="popup__form-group">' +
						'<label class="label-for-input">E-mail</label>' +
						'<input type="text" name="email" value="{{userEmail}}" placeholder="mail@domain.com" />' +
						'<span class="js-postBuyButton-popup-error popup__form-group__error" style="display: none">Неверный формат email</span>' +
					'</div>' +

					'<div class="popup__form-group">' +
						'<label class="label-for-input">Имя</label>' +
						'<input type="text" name="name" value="{{userName}}" />' +
					'</div>' +

					'<div class="popup__form-group checkbox-group"><label><input type="checkbox" name="confirm" value="1" /><i></i> Я ознакомлен и согласен с информацией о продавце и его офертой</label></div>' +
					'<div class="popup__form-group vendor">Продавец-партнёр: {{partnerName}}</div>' +

					'<div class="btn--container">' +
						'<button type="submit" class="js-postBuyButton-popup-submitButton btn btn--submit">Отправить заявку</button>' +
					'</div>' +

					'{{#full}}' +
						'<div class="popup__form-group msg--goto-card">' +
							'<a href="{{productUrl}}" class="lnk--goto-card">Перейти в карточку товара</a>' +
						'</div>' +
					'{{/full}}' +
				'</form>' +
			'</div>',

		popupResultTemplate =
			'<div class="popup--request__head msg--send">Ваша заявка № {{orderNumber}} отправлена</div>' +
			'<div class="btn--container">' +
				'<button type="submit" class="js-postBuyButton-popup-okButton btn btn--submit">Ок</button>' +
			'</div>',

		validate = function($form){
			var isValid = true,
				$phoneInput = $('[name="phone"]', $form),
				$emailInput = $('[name="email"]', $form),
				parentClass = '.popup__form-group',
				labelClass = '.label-for-input';

			if (!/8\(\d{3}\)\d{3}-\d{2}-\d{2}/.test($phoneInput.val().replace(/\s+/g, ''))) {
				isValid = false;
				$phoneInput.addClass(errorCssClass).siblings('.js-postBuyButton-popup-error').show();
				$phoneInput.parents(parentClass).children(labelClass).addClass('lbl-error');
			} else {
				$phoneInput.removeClass(errorCssClass).siblings('.js-postBuyButton-popup-error').hide();
				$phoneInput.parents(parentClass).children(labelClass).removeClass('lbl-error');
			}

			if ($emailInput.val().length != 0 && !ENTER.utils.validateEmail($emailInput.val())) {
				isValid = false;
				$emailInput.addClass(errorCssClass).siblings('.js-postBuyButton-popup-error').show();
				$emailInput.parents(parentClass).children(labelClass).addClass('lbl-error');
			} else {
				$emailInput.removeClass(errorCssClass).siblings('.js-postBuyButton-popup-error').hide();
				$emailInput.parents(parentClass).children(labelClass).removeClass('lbl-error');
			}

			return isValid;
		};

	$body.on('click', '.js-postBuyButton', function(e) {
		e.preventDefault();

		var
			$button = $(this),
			$popup = $(Mustache.render(popupTemplate, {
				full: $button.data('full'),
				partnerName: $button.data('partner-name'),
				productUrl: $button.data('product-url'),
				productId: $button.data('product-id'),
				sender: $button.attr('data-sender'),
				userPhone: ENTER.utils.Base64.decode(ENTER.config.userInfo.user.mobile || ''),
				userEmail: ENTER.config.userInfo.user.email || '',
				userName: ENTER.config.userInfo.user.name || ''
			})),
			$form = $('form', $popup),
			$errors = $('.js-postBuyButton-popup-errors', $form);

		$popup.lightbox_me({
			centered: true,
			sticky: true,
			closeClick: false,
			closeEsc: false,
			closeSelector: '.js-postBuyButton-popup-close',
			destroyOnClose: true
		});

		$.mask.definitions['x'] = '[0-9]';
		$.mask.placeholder = "_";
		$.mask.autoclear = false;
		$.map($('input', $popup), function(elem, i) {
			var $elem = $(elem);
			if (typeof $elem.data('mask') !== 'undefined') {
				$elem.mask($elem.data('mask'));
			}
		});

		$('input', $popup).blur(function(){
			validate($form);
		});

		$('[name="phone"]', $popup).keyup(function(e){
			var val = $(e.currentTarget).val();
			if (val[val.length - 1] != '_') {
				validate($form);
			}
		});

		$form.submit(function(e) {
			e.preventDefault();

			if (!validate($form)) {
				return;
			}

			var $submitButton = $('.js-postBuyButton-popup-submitButton', $form);

			$submitButton.attr('disabled', true);
			$.ajax({
				type: 'POST',
				url: $form.attr('action'),
				data: $form.serializeArray(),
				success: function(result){
					if (result.error) {
						$errors.html(result.error).show();
						return;
					}

					$form.after($(Mustache.render(popupResultTemplate, {
						orderNumber: result.orderNumber
					})));

					$form.remove();

					$('.js-postBuyButton-popup-okButton', $popup).click(function() {
						$popup.trigger('close');
					});
				},
				fail: function(){
					$errors.html('Ошибка при создании заявки').show();
				},
				complete: function(){
					$submitButton.attr('disabled', false);
				}
			})
		});
	});
});
