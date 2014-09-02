;(function( ENTER ) {
	var
		$authBlock = $('#auth-block'),

        init = function() {
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
            $('.js-resetForm, .js-authForm, js-registerForm')
                // отправка форм
                .on('submit', function(e) {
                    var
                        $el = $(e.target),
                        data = $el.serializeArray()
                    ;

                    // очистить ошибки
                    $el.find('input').each(function(i, el) {
                        $el.trigger('fieldError', [{field: $(el).attr('name')}]);
                    });

                    $.post($el.attr('action'), data).done(function(response) {
                        if (!response.error) {
                            window.location.href = (response.data && response.data.link) ? response.data.link : window.location.href;

                            return true;
                        }

                        response.form && response.form.error && $.each(response.form.error, function(i, error) {
                            console.warn(error);

                            if ('global' == error.field) {
                            } else {
                                $el.trigger('fieldError', [error]);
                                console.info(error);
                            }
                        });
                    });

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
                .on('focus', 'input', function() {
                    var
                        $el = $(this)
                    ;

                    $el.closest('form').trigger('fieldError', [{field: $el.attr('name')}])
                })
            ;
        };
    ;

	$(document).ready(function() {
		init();
	});

}(window.ENTER));