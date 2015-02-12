;$(function() {
	var
		$body = $('body'),
		errorCssClass = 'textfield-err',
		region = ENTER.config.pageConfig.user.region.name,
		catalogPath = ENTER.utils.getCategoryPath(),
		popupTemplate =
			'<div class="js-slotButton-popup popup--request">' +
				'<a href="#" class="js-slotButton-popup-close popup--request__close" title="Закрыть"></a>' +

				'<form action="' + ENTER.utils.generateUrl('order.slot.create') + '" method="post">' +
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

					'<div class="js-slotButton-popup-errors" style="display: none;">' +
					'</div>' +

					'<div class="popup__form-group">' +
						'<div class="input-group">' +
							'<label class="label-for-input label-phone">Телефон</label>' +
							'<input type="text" name="phone" value="{{userPhone}}" placeholder="8 (___) ___-__-__" data-mask="8 (xxx) xxx-xx-xx" class="js-slotButton-popup-phone" />' +
						'</div>' +
						'<span class="js-slotButton-popup-error popup__form-group__error" style="display: none">Неверный формат телефона</span>' +
					'</div>' +

					'<div class="popup__form-group">' +
						'<div class="input-group">' +
							'<label class="label-for-input">E-mail</label>' +
							'<input type="text" name="email" value="{{userEmail}}" placeholder="mail@domain.com" class="js-slotButton-popup-email" />' +
						'</div>' +
						'<span class="js-slotButton-popup-error popup__form-group__error" style="display: none">Неверный формат email</span>' +
					'</div>' +

					'<div class="popup__form-group">' +
						'<div class="input-group">' +
							'<label class="label-for-input">Имя</label>' +
							'<input type="text" name="name" value="{{userName}}" class="js-slotButton-popup-name" />' +
						'</div>' +
					'</div>' +

					'<div class="popup__form-group checkbox-group"><label><input type="checkbox" name="confirm" value="1" class="js-slotButton-popup-confirm" /><i></i> Я ознакомлен и согласен с информацией о продавце и его {{#partnerOfferUrl}}<a class="underline" href="{{partnerOfferUrl}}" target="_blank">{{/partnerOfferUrl}}офертой{{#partnerOfferUrl}}</a>{{/partnerOfferUrl}}</label></div>' +
					'<div class="popup__form-group vendor">Продавец-партнёр: {{partnerName}}</div>' +

					'<div class="btn--container">' +
						'<button type="submit" class="js-slotButton-popup-submitButton btn btn--submit">Отправить заявку</button>' +
					'</div>' +

					'{{#full}}' +
						'<div class="popup__form-group msg--goto-card">' +
							'<a href="{{productUrl}}" class="lnk--goto-card js-slotButton-popup-goToProduct">Перейти в карточку товара</a>' +
						'</div>' +
					'{{/full}}' +
				'</form>' +
			'</div>',

		popupResultTemplate =
			'<div class="popup--request__head msg--send">Ваша заявка № {{orderNumber}} отправлена</div>' +
			'<div class="btn--container">' +
				'<button type="submit" class="js-slotButton-popup-okButton btn btn--submit">Ок</button>' +
			'</div>',

		validate = function($form) {
			var isValid = true,
				$phoneInput = $('[name="phone"]', $form),
				$emailInput = $('[name="email"]', $form),
				parentClass = '.popup__form-group',
				labelClass = '.input-group';

			if (!/8\(\d{3}\)\d{3}-\d{2}-\d{2}/.test($phoneInput.val().replace(/\s+/g, ''))) {
				isValid = false;
				$phoneInput.addClass(errorCssClass).siblings('.js-slotButton-popup-error').show();
				$phoneInput.parents(parentClass).children(labelClass).addClass('lbl-error');
			} else {
				$phoneInput.removeClass(errorCssClass).siblings('.js-slotButton-popup-error').hide();
				$phoneInput.parents(parentClass).children(labelClass).removeClass('lbl-error');
			}

			if ($emailInput.val().length != 0 && !ENTER.utils.validateEmail($emailInput.val())) {
				isValid = false;
				$emailInput.addClass(errorCssClass).siblings('.js-slotButton-popup-error').show();
				$emailInput.parents(parentClass).children(labelClass).addClass('lbl-error');
			} else {
				$emailInput.removeClass(errorCssClass).siblings('.js-slotButton-popup-error').hide();
				$emailInput.parents(parentClass).children(labelClass).removeClass('lbl-error');
			}

			return isValid;
		};

	$body.on('click', '.js-slotButton', function(e) {
		e.preventDefault();

		var
			$button = $(this),
			sender = $button.data('sender') || {},
			productArticle = $button.data('product-article'),
			productPrice = $button.data('product-price'),
			$popup = $(Mustache.render(popupTemplate, {
				full: $button.data('full'),
				partnerName: $button.data('partner-name'),
				partnerOfferUrl: $button.data('partner-offer-url'),
				productUrl: $button.data('product-url'),
				productId: $button.data('product-id'),
				sender: $button.attr('data-sender'),
				userPhone: ENTER.utils.Base64.decode(ENTER.config.userInfo.user.mobile || ''),
				userEmail: ENTER.config.userInfo.user.email || '',
				userName: ENTER.config.userInfo.user.name || ''
			})),
			$form = $('form', $popup),
			$errors = $('.js-slotButton-popup-errors', $form),
			$phone = $('.js-slotButton-popup-phone', $form),
			$email = $('.js-slotButton-popup-email', $form),
			$name = $('.js-slotButton-popup-name', $form),
			$confirm = $('.js-slotButton-popup-confirm', $form),
			$goToProduct = $('.js-slotButton-popup-goToProduct', $form);

		$popup.lightbox_me({
			centered: true,
			sticky: true,
			closeClick: false,
			closeEsc: false,
			closeSelector: '.js-slotButton-popup-close',
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

		$phone.keyup(function(e){
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

			var $submitButton = $('.js-slotButton-popup-submitButton', $form);

			$submitButton.attr('disabled', 'disabled');
			$.ajax({
				type: 'POST',
				url: $form.attr('action'),
				data: $form.serializeArray(),
				success: function(result){
					if (result.error) {
						$errors.text(result.error).show();
						return;
					}

					$form.after($(Mustache.render(popupResultTemplate, {
						orderNumber: result.orderNumber
					})));

					$form.remove();

					$('.js-slotButton-popup-okButton', $popup).click(function() {
						$popup.trigger('close');
					});

					if (typeof ENTER.utils.sendOrderToGA == 'function' && result.orderAnalytics) {
						ENTER.utils.sendOrderToGA(result.orderAnalytics);
					}
				},
				error: function(){
					$errors.text('Ошибка при создании заявки').show();
				},
				complete: function(){
					$submitButton.removeAttr('disabled');
				}
			})
		});

		ENTER.utils.sendAdd2BasketGaEvent(productArticle, productPrice, true, true, sender.name);

		$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '1 Вход', catalogPath]);

		$phone.focus(function() {
			$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '2 Телефон', catalogPath]);
		});

		$email.focus(function() {
			$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '3 Email', catalogPath]);
		});

		$name.focus(function() {
			$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '4 Имя', catalogPath]);
		});

		$confirm.click(function(e) {
			if (e.currentTarget.checked) {
				$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '5 Оферта', catalogPath]);
			}
		});

		$goToProduct.click(function(e) {
			$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '6 Перейти в карточку', catalogPath]);
		});

		$phone.focus();
	});
});
