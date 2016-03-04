;$(function( ENTER ) {
	var
		$body = $('body'),
		$authContent = $('.js-login-content'),
		isAuthContentInited = false,
		errorClass = 'is-error',
		noticeTimer;

	if ($body.data('template') == 'login') {
		initAuthContentOnce();
	}

	ENTER.auth.open = function(redirectToAfterLogin, redirectToAfterRegister, loginAfterRegister, checkUrl) {
		initAuthContentOnce();

		setTimeout(function() {
			$authContent.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {
					var $field;

					if (redirectToAfterLogin) {
						$field = $authContent.find('.js-authForm-redirectTo');
						$field.data('prev-value', $field.val()).val(redirectToAfterLogin);

						$authContent.find('.js-socialAuth').each(function() {
							var $field = $(this);
							$field.data('prev-href', $field.attr('href')).attr('href', ENTER.utils.setURLParam('redirect_to', redirectToAfterLogin, $field.attr('href')));
						});
					}

					if (redirectToAfterRegister) {
						$field = $authContent.find('.js-registerForm-redirectTo');
						$field.data('prev-value', $field.val()).val(redirectToAfterRegister);
					}

					if (loginAfterRegister) {
						$field = $authContent.find('.js-registerForm-loginAfterRegister');
						$field.data('prev-value', $field.val()).val(1);
					}

					$authContent.find('input.js-register-new-field:first').focus();
				},
				onClose: function() {
					$authContent.find('.js-authForm-redirectTo, .js-registerForm-redirectTo, .js-registerForm-loginAfterRegister').each(function() {
						var $field = $(this);
						$field.val($field.data('prev-value')).data('prev-value', '');
					});

					$authContent.find('.js-socialAuth').each(function() {
						var $field = $(this);
						$field.attr('href', $field.data('prev-href')).data('prev-href', '');
					});
				}
			});
		}, 250);

		if (checkUrl) {
			$.get(checkUrl).done(function(response) {
				if (response.redirect) {
					if ((typeof response.redirect === 'string') && (~response.redirect.indexOf('http'))) {
						window.location.href = response.redirect;
					} else {
						window.location.reload(true);
					}
				}
			});
		}
	};

	$body.on('click', '.js-login-opener', function(e) {
		e.preventDefault();
		ENTER.auth.open($(this).attr('href'), null, false, $(this).data('check-auth-url'));
	});

	function initAuthContentOnce() {
		if (isAuthContentInited) {
			return;
		}

		isAuthContentInited = true;

		$('.js-forgotButton').on('click', function(e) {
			var
				$el = $(this),
				url = $el.data('url'),
				relations = $el.data('relation'),
				$field = (relations && relations['field']) ? $(relations['field']) : null;

			if (!url) {
				throw {message: 'Не задан url'};
			}

			if (!$field || !$field.length) {
				throw {message: 'Не найдено поле username'};
			}

			$.post(
				url,
				{
					forgot: {
						username: $field.val()
					}
				}
			).done(function(response) {
				var 
					errors = response.errors;

				errors && $.each(errors, function(i, errors) {
					$el.trigger('fieldError', [errors]);
				});

				if (!errors) {
					$('.js-authForm').find('[data-field="password"]').val('');

					$('.js-resetForm').addClass('is-active');
					$('.js-reset-email-message').html($('.js-login').val());

					noticeTimer = setTimeout(function(){
						$('.js-authContainer').removeClass('is-active');
					}, 3000);
				}
			});

			e.preventDefault();
		});

		// формы
		$('.js-resetForm, .js-authForm, .js-registerForm')
			// отправка форм
			.on('submit', function(e) {
				var
					$el = $(e.target),
					$submit = $el.find('[type="submit"]'),
					data = $el.serializeArray(),
					buttonTimeout,
					usernameValue
				;

				try {
					$submit.attr('disabled', 'disabled');
					if ($submit.data('loading-value')) {
						buttonTimeout = setTimeout(function() { $submit.val($submit.data('loading-value')); }, 250)
					}
				} catch (error) { console.error(error); }

				$.post($el.attr('action'), data)
					.done(function(response) {
						var
							message = response.message,
							errors = response.errors,
							duplicateField;

						if ($el.hasClass('js-registerForm') && response.newUser) {
							ENTER.utils.analytics.soloway.send({
								action: 'userRegistrationComplete',
								user: {
									id: response.newUser.id
								}
							});
						}

						if (!message && response.notice && response.notice.message) {
							message = response.notice.message;
						}


						if ($el.hasClass('js-registerForm') && response.notice) {
							var classNew = 'is-active',
								userLogin = $('.js-login');

							if ('duplicate' === response.notice.code) {
								duplicateField = response.notice.field || 'email';
								classNew = 'is-error';

								usernameValue = $el.find('[data-field="' + duplicateField + '"]').val();
								if (usernameValue) {
									userLogin.val(usernameValue);
								}

							} else {
								$('.js-user-good-name').html($('.js-register-new-field-name').val());
								userLogin.val($('.js-register-new-field-email').val());
							}

							userLogin.trigger('focus');
							$('.js-register-good').addClass(classNew);
							$('.js-registerTxt').html(message);


							noticeTimer = setTimeout(function() {
								$('.js-authContainer').removeClass('is-error');
							}, 3000);
						}

						if (response.data && response.data.link) {
							window.location.href = response.data.link ? response.data.link : window.location.href;

							return true;
						}

						$el.trigger('clearError');



						if (message && !$el.hasClass('js-registerForm')) {
							$el.find('.js-message').html(message);
						}

						errors && $.each(errors, function(i, errors) {
							$el.trigger('fieldError', [errors]);
						});
					})
					.always(function() {
						$submit.removeAttr('disabled');
						try {
							if (buttonTimeout) {
								clearTimeout(buttonTimeout);
							}
							if ($submit.data('value')) {
								$submit.val($submit.data('value'));
							}
						} catch (error) { console.info(error); }
					})
				;

				e.preventDefault();
			})
		;

		$.mask.definitions['n'] = '[0-9]';
		$('.js-registerForm .js-phoneField').mask('+7 (nnn) nnn-nn-nn');
	}

	// маркировка полей с ошибками
	$body.on('fieldError', function(e, errors) {
    	var
    		$el = $(e.target),
    		$field = $('.js-register-new-field[data-field="' + errors.field + '"]');

    	if ( $field.length ) {
            $field.removeClass(errorClass);
    		$field.prev('.js-field-error').remove();
    		if ( errors.message ) {
                $field.addClass(errorClass);
                $field.before('<div class="field-error js-field-error">' + errors.message + '</div>');
            }
        }
	});

	// очистить ошибки
	$body.on('clearError', function() {
		var $el = $(this);

		$el.find('.js-message').html('');

		$el.find('input').each(function(i, el) {
			$el.trigger('fieldError', [{field: $(el).data('field')}]);
		});
	});

	$body.on('focus', 'input', function() {
		var $el = $(this);

		$el.closest('form').trigger('fieldError', [{field: $el.data('field')}])
	});

	$body.on('click', '.js-authForm-close', function(){
		var $this = $(this),
			container = $this.closest('.js-authContainer');

		console.log(container);

		if(container.hasClass('is-active')){
			container.removeClass('is-active');
			clearTimeout(noticeTimer);
		}else if(container.hasClass('is-error')){
			container.removeClass('is-error');
			clearTimeout(noticeTimer);
		}
	});

	$body.on('click', function(e){
		var resetBtn = $('.js-resetBtn');

		if ($(e.target).closest('.js-password-container').length){
			resetBtn.show();
		}else{
			resetBtn.hide();
		}
	});
}(window.ENTER));
