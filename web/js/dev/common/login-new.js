;$(function( ENTER ) {
	var
		$body = $('body'),
		$authContent = $('.js-login-content'),
		isAuthContentInited = false,
		errorClass = 'is-error';

	if ($body.data('template') == 'login') {
		initAuthContentOnce();
	}

	$body.on('click', '.js-login-opener', function(e) {
		var
			checkUrl;

		e.preventDefault();

		initAuthContentOnce();

		var $self = $(this);
		if ($self.data('state')) {
			$authContent.trigger('changeState', [$self.data('state')]);
		}

		setTimeout(function() {
			$authContent.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {
					$authContent.find('input:first').focus();
				},
				onClose: function() {
					$authContent.trigger('changeState', ['default']);
				}
			});
		}, 250);

		checkUrl = $(this).data('checkAuthUrl');
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
	});

	function initAuthContentOnce() {
		if (isAuthContentInited) {
			return;
		}

		isAuthContentInited = true;

		// Изменение ссылок на соц. сети при выборе подписки
		!function() {
			var $subscribe = $('.js-registerForm-subscribe');

			if (!ENTER.config.userInfo.user.isSubscribedToActionChannel) {
				$subscribe.attr('checked', 'checked');
			}

			changeSocnetLinks($subscribe.length && $subscribe[0].checked);

			$subscribe.change(function(e) {
				changeSocnetLinks(e.currentTarget.checked);
			});
		}();

		// изменение состояния блока авторизации
		$authContent.on('changeState', function(e, state) {
			var
				$el = $(this)
				;

			console.info({'message': 'authBlock.changeState', 'state': state});

			if (state) {
				var
					oldClass = $el.attr('data-state') ? ('state_' + $el.attr('data-state')) : null,
					newClass = 'state_' + state // state_default, state_register
					;

				oldClass && $el.removeClass(oldClass);
				$el.addClass(newClass);
				$el.attr('data-state', state);
			}

			$('.js-resetForm, .js-authForm, .js-registerForm').trigger('clearError');
		});

		// клик по ссылкам
		$authContent.find('.js-link').on('click', function(e) {
			var
				$el = $(e.target),
				$target = $($el.data('value').target),
				state = $el.data('value').state
				;

			console.info({'$target': $target, 'state': state});
			$target.trigger('changeState', [state]);
		});

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
			});

			e.preventDefault();
		})

		// формы
		$('.js-resetForm, .js-authForm, .js-registerForm')
			// отправка форм
			.on('submit', function(e) {
				var
					$el = $(e.target),
					$submit = $el.find('[type="submit"]'),
					data = $el.serializeArray(),
					buttonTimeout
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
							errors = response.errors;

						function getFieldValue(fieldName) {
							for (var i = 0; i < data.length; i++) {
								if (data[i]['name'] == fieldName) {
									return data[i]['value'];
								}
							}

							return null;
						}

						if ($el.hasClass('js-registerForm') && getFieldValue('subscribe') && typeof _gaq != 'undefined') {
							_gaq.push(['_trackEvent', 'subscription', 'subscribe_registration']);
						}

						if ($el.hasClass('js-registerForm') && response.newUser) {
							ENTER.utils.analytics.soloway.send({
								action: 'userRegistrationComplete',
								user: {
									id: response.newUser.id
								}
							});
						}

						if (response.data && response.data.link) {
							window.location.href = response.data.link ? response.data.link : window.location.href;

							return true;
						}

						$el.trigger('clearError');

						if (!message && response.notice && response.notice.message) {
							message = response.notice.message;
						}

						if (message) {
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
	})

	// очистить ошибки
	$body.on('clearError', function() {
		var $el = $(this);

		$el.find('.js-message').html('');

		$el.find('input').each(function(i, el) {
			$el.trigger('fieldError', [{field: $(el).data('field')}]);
		});
	})

	$body.on('focus', 'input', function() {
		var $el = $(this);

		$el.closest('form').trigger('fieldError', [{field: $el.data('field')}])
	})

	function changeSocnetLinks(isSubscribe) {
		$('.js-registerForm-socnetLink').each(function(index, link) {
			var $link = $(link);
			$link.attr('href', ENTER.utils.setURLParam('subscribe', isSubscribe ? '1' : null, $link.attr('href')));
		});
	}
}(window.ENTER));