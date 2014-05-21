define(
    [
        'jquery', 'underscore', 'mustache',
        'module/widget'
    ],
    function (
        $, _, mustache
    ) {
        var $document = $(document),
            $body = $('body'),
            $debug = $('.js-debug')
        ;

        $document.ajaxComplete(function(e, xhr, options) {
            var response = JSON.parse(xhr.responseText),
                $template = $('#tpl-debug-container').html()
            ;

            if (response && response.debug) {
                var $widget = $(mustache.render($template, response.debug));
                $widget.appendTo($debug);
                if ($debug.data('opened')) {
                    $widget.slideDown(200);
                }
            }
        });

        $('.js-debug-link').on('click', function(e) {
            e.stopPropagation();

            var $el = $(e.currentTarget)
            ;

            $debug.data('opened', true);
            $el.hide();
            $debug.find('.js-widget').slideDown(200);

            e.preventDefault();
        });

        $body.on('click', '.js-debug-container-link', function(e) {
            e.stopPropagation();

            var $el = $(e.currentTarget);

            $el.blur();

            $debug.find('.js-debug-container-content').slideUp(100);

            if ($el.length) {
                var $content = $($el.data('contentSelector'));
                $content.is(':hidden') && $content.slideDown(200);
            }

            e.preventDefault();
        });

        $body.on('click', function(e) {
            if (true === $debug.data('opened')) {
                $debug.data('opened', false);
                $debug.find('.js-widget').slideUp(100, function() {
                    $('.js-debug-link').show();
                });
            }
        });

        $debug.find('.js-widget').each(function(i, el) {
            var $widget = $(el);

            $widget.trigger('render', $widget.data('value'));
        });
    }
);