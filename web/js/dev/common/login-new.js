;(function( ENTER ) {
	var
        body = $('body'),
		authBlock = $('#auth-block'),
		resetForm = $('.js-resetForm'),
		registerForm = $('.js-registerForm'),
		loginForm = $('.js-loginForm'),

        init = function() {
            $('.js-resetLink, .js-authLink, .js-registerLink').on('click', function(e) {
                var
                    $el = $(e.target),
                    $hideContainer = $($el.data('hideContainer')),
                    $showContainer = $($el.data('showContainer')),
                    $hideLink = $($el.data('hideLink')),
                    $showLink = $($el.data('showLink'))
                ;

                console.info({showLink: $showLink});
                if ($showLink.length) {
                    $hideLink.hide();
                    $showLink.show();
                }

                $hideContainer.slideUp('fast');
                $showContainer.slideDown('fast');
            })
        };

        $('.js-resetForm, .js-authForm, js-registerForm').on('submit', function(e) {
            var
                $el = $(e.target)
            ;

            console.info($el);

            e.preventDefault();
        });
    ;

	$(document).ready(function() {
		init();
	});

}(window.ENTER));