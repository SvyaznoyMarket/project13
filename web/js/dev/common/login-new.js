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

            // отправка форм
            $('.js-resetForm, .js-authForm, js-registerForm').on('submit', function(e) {
                var
                    $el = $(e.target)
                    ;

                console.info($el);

                e.preventDefault();
            });
        };
    ;

	$(document).ready(function() {
		init();
	});

}(window.ENTER));