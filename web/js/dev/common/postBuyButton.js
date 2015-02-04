/**
 * @author		Zaytsev Alexandr
 */
;$(function() {
	var
		$body = $('body'),
		errorCssClass = 'textfield-err',
		popupTemplate =
			'<div class="js-postBuyButton-popup">' +
				'<a href="#" class="js-postBuyButton-popup-close" title="Закрыть">Закрыть</a>' +

				'<form action="' + ENTER.utils.generateUrl('order.postBuy') + '" method="post">' +
					'<input type="hidden" name="productId" value="{{productId}}" />' +

					'{{#full}}' +
						'<h1>Закажите обратный звонок и уточните</h1>' +
						'<ul>' +
							'<li>Состав мебели и техники</li>' +
							'<li>Условия доставки, сборки и оплаты</li>' +
						'</ul>' +
					'{{/full}}' +

					'{{^full}}' +
						'<h1>Отравить заявку</h1>' +
					'{{/full}}' +

					'<div class="js-postBuyButton-popup-errors" style="display: none;">' +
					'</div>' +

					'<p>' +
						'<span>*Телефон</span>' +
						'<input type="text" name="phone" value="{{userPhone}}" placeholder="8 (___) ___-__-__" data-mask="8 (xxx) xxx-xx-xx" />' +
						'<span class="js-postBuyButton-popup-error" style="display: none">Неверный формат телефона</span>' +
					'</p>' +

					'<p>' +
						'<span>E-mail</span>' +
						'<input type="text" name="email" value="{{userEmail}}" placeholder="mail@domain.com" />' +
						'<span class="js-postBuyButton-popup-error" style="display: none">Неверный формат email</span>' +
					'</p>' +
		
					'<p>' +
						'<span>Имя</span>' +
						'<input type="text" name="name" value="{{userName}}" />' +
					'</p>' +

					'<p><label><input type="checkbox" name="confirm" value="1" /> Я ознакомлен и согласен с информацией о продавце и его офертой</label></p>' +
					'<p>Продавец-партнёр: {{partnerName}}</p>' +

					'<button type="submit" class="js-postBuyButton-popup-submitButton">Отправить заявку</button>' +

					'{{#full}}' +
						'<p><a href="{{productUrl}}">Перейти в карточку товара</a></p>' +
					'{{/full}}' +
				'</form>' +
			'</div>',

		popupResultTemplate =
			'<div>' +
				'<p>Ваша заявка № {{orderNumber}} отправлена</p>' +
				'<button type="submit" class="js-postBuyButton-popup-okButton">Ок</button>' +
			'</div>',

		validate = function($form){
			var isValid = true,
				$phoneInput = $('[name="phone"]', $form),
				$emailInput = $('[name="email"]', $form);

			if (!/8\(\d{3}\)\d{3}-\d{2}-\d{2}/.test($phoneInput.val().replace(/\s+/g, ''))) {
				isValid = false;
				$phoneInput.addClass(errorCssClass).siblings('.js-postBuyButton-popup-error').show();
			} else {
				$phoneInput.removeClass(errorCssClass).siblings('.js-postBuyButton-popup-error').hide();
			}

			if ($emailInput.val().length != 0 && !ENTER.utils.validateEmail($emailInput.val())) {
				isValid = false;
				$emailInput.addClass(errorCssClass).siblings('.js-postBuyButton-popup-error').show();
			} else {
				$emailInput.removeClass(errorCssClass).siblings('.js-postBuyButton-popup-error').hide();
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
				userPhone: ENTER.utils.Base64.decode(ENTER.config.userInfo.user.mobile || ''),
				userEmail: ENTER.config.userInfo.user.email || '',
				userName: ENTER.config.userInfo.user.name || ''
			})),
			$form = $('form', $popup),
			$errors = $('.js-postBuyButton-popup-errors', $form);

		$popup.lightbox_me({
			centered: true,
			sticky: false,
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
