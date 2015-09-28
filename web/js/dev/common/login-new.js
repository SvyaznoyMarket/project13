;$(function( ENTER ) {
	var
		$authBlock = $('#auth-block'),
		isFirstOpen = true
	;

	function changeSocnetLinks(isSubscribe) {
		$('.js-registerForm-socnetLink').each(function(index, link) {
			var $link = $(link);
			$link.attr('href', ENTER.utils.setURLParam('subscribe', isSubscribe ? '1' : null, $link.attr('href')));
		});
	}

	$('body').on('click', '.bAuthLink', function(e) {
		e.preventDefault();

		if (isFirstOpen) {
			isFirstOpen = false;

			var $subscribe = $('.js-registerForm-subscribe');

			if (!ENTER.config.userInfo.user.isSubscribedToActionChannel) {
				$subscribe.attr('checked', 'checked');
			}

			changeSocnetLinks($subscribe.length && $subscribe[0].checked);

			$subscribe.change(function(e) {
				changeSocnetLinks(e.currentTarget.checked);
			});

			// изменение состояния блока авторизации
			$authBlock.on('changeState', function(e, state) {
				var
					$el = $(this)
					;

				console.info({'message': 'authBlock.changeState', 'state': state});

				if (state) {
					var
						oldClass = $el.attr('data-state') ? ('state_' + $el.attr('data-state')) : null,
						newClass = 'state_' + state
						;

					oldClass && $el.removeClass(oldClass);
					$el.addClass(newClass);
					$el.attr('data-state', state);
				}

				$('.js-resetForm, .js-authForm, .js-registerForm').trigger('clearError');
			});

			// клик по ссылкам
			$authBlock.find('.js-link').on('click', function(e) {
				var
					$el = $(e.target),
					$target = $($el.data('value').target),
					state = $el.data('value').state
					;

				console.info({'$target': $target, 'state': state});
				$target.trigger('changeState', [state]);
			});

			// формы
			$('.js-resetForm, .js-authForm, .js-registerForm')
				// отправка форм
				.on('submit', function(e) {
					var
						$el = $(e.target),
						data = $el.serializeArray()
                    ;

                    $el.find('[type="submit"]').attr('disabled', 'disabled');

					$.post($el.attr('action'), data)
                        .done(function(response) {
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

                            if (response.data && response.data.link) {
                                window.location.href = response.data.link ? response.data.link : window.location.href;

                                return true;
                            }

                            $el.trigger('clearError');

                            var message = response.message;
                            if (!message && response.notice && response.notice.message) {
                                message = response.notice.message;
                            }

                            if (message) {
                                $el.find('.js-message').html(message);
                            }

                            response.form && response.form.error && $.each(response.form.error, function(i, error) {
                                console.warn(error);

                                $el.trigger('fieldError', [error]);
                            });
					    })
                        .always(function() {
                            $el.find('[type="submit"]').removeAttr('disabled');
                        })
                    ;

					e.preventDefault();
				})

				.on('fieldError', function(e, error) {
					var
						$el = $(e.target),
						$field = $el.find('[name*="' + error.field + '"]')
						;

					if ($field.length) {
						$field.prev('.js-fieldError').remove();
						if (error.message) {
							$field.before('<div class="js-fieldError bErrorText"><div class="bErrorText__eInner">' + error.message + '</div></div>');
						}
					}
				})

				// очистить ошибки
				.on('clearError', function() {
					var $el = $(this);

					$el.find('.js-message').html('');

					$el.find('input').each(function(i, el) {
						$el.trigger('fieldError', [{field: $(el).attr('name')}]);
					});
				})

				.on('focus', 'input', function() {
					var $el = $(this);

					$el.closest('form').trigger('fieldError', [{field: $el.attr('name')}])
				})
			;

			$.mask.definitions['n'] = '[0-9]';
			$('.js-registerForm .js-phoneField').mask('+7 (nnn) nnn-nn-nn');
		}

		$authBlock.lightbox_me({
			centered: true,
			autofocus: true,
			onLoad: function() {
				$authBlock.find('input:first').focus();
			},
			onClose: function() {
				$authBlock.trigger('changeState', ['default']);
			}
		});
	});
}(window.ENTER));