;(function($){
	var $body = $(document.body),
		$mobileField = $('.jsMobileField'), // поле Телефон
		$emailField = $('.jsEmailField'), // поле Email
		$agreedCheckbox = $('.jsAgreedCheckbox'), // чекбокс соглашения
		errorClass = 'textfield-err',
		validate, checkMobileField, checkEmailField, checkAgreed;

	/**
	 * Общая проверка, возвращает массив ошибок
	 * @returns {Array}
	 */
	validate = function validateF() {
		var errors = [];
		if (!checkMobileField()) errors.push('Ошибка в поле телефона');
		if (!checkEmailField()) errors.push('Ошибка в поле email');
		if (!checkAgreed()) errors.push('Необходимо согласие с условиями');
		return errors;
	};

	/**
	 * Проверка на валидность поля email
	 * @returns {boolean}
	 */
	checkMobileField = function checkMobileFieldF() {
		var isValid = /\d\s\(\d{3}\)\s\d{3}-\d{2}-\d{2}/.test($mobileField.val());
		if (!isValid) {
			$mobileField.addClass(errorClass)
		} else {
			$mobileField.removeClass(errorClass)
		}
		return isValid;
	};

	/**
	 * Проверка на валидность поля email
	 * @returns {boolean}
	 */
	checkEmailField = function checkEmailFieldF(){
		var isValid = ENTER.utils.validateEmail($emailField.val());
		if ($emailField.val() != '' && !isValid) {
			$emailField.addClass(errorClass);
			return isValid;
		} else {
			$emailField.removeClass(errorClass);
		}
		return true;
	};

	checkAgreed = function checkAgreedF() {
		var isValid = $agreedCheckbox.is(':checked'),
			errClass = 'customLabel-err';
		if (!isValid) $agreedCheckbox.siblings('label').addClass(errClass);
		else $agreedCheckbox.siblings('label').removeClass(errClass);
		return isValid;
	};

	$agreedCheckbox.on('change', function(){
		checkAgreed();
	});

	$body.find('input').on('blur', function(){
		checkEmailField();
		checkMobileField()
	});

	/* masked input */
	$.mask.definitions['x']='[0-9]';
	$.mask.placeholder= "_";
	$.mask.autoclear = false;
	$body.find('input').each(function(){
		if ($(this).data('mask')) $(this).mask($(this).data('mask'))
	});

	$body.on('submit', '.jsOrderForm', function(e){
		var errors = validate();
		e.preventDefault();
		if (errors.length > 0) {
			console.error('Ошибки в форме', errors);
			return;
		}
		$.post(window.location.href, $('.jsOrderForm').serialize())
			.done(function(data) {
				var $form;
				if (data.form) {
					$form = $(data.form);
					$body.append($form);
					if ($form.hasClass('jsPaymentFormPaypal') && typeof $form.attr('action') != 'undefined') {
						window.location.href = $form.attr('action');
					} else {
						$body.append($form);
						$form.submit();
					}
				}
			})
			.fail(function(data){
				console.error(data)
			})
			.always(function(data){
				console.log('SERVER RESP:', data)
			});
	});
}(jQuery));