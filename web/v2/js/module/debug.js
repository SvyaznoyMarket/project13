define(
    [
        'jquery', 'underscore', 'mustache'
    ],
    function (
        $, _, mustache
    ) {
        var $body = $('body'),
            $debug = $('.js-debug')
        ;

        $('.js-debug-link').on('click', function(e) {
            e.preventDefault();

            var $el = $(e.currentTarget)
            ;

            $debug.data('opened', true);
            $debug.find('.js-widget').slideDown(200);
            $el.hide();
        });

        $debug.on('click', function(e) {
            e.stopPropagation();
        });

        $body.on('click', function(e) {
            if (true === $debug.data('opened')) {
                $debug.data('opened', false);
                $debug.find('.js-widget').hide();
                $('.js-debug-link').show();
            }
        });

        $debug.find('.js-widget').each(function(i, el) {
            var $widget = $(el);

            $widget.trigger('render', $widget.data('value'));
        });
    }
);