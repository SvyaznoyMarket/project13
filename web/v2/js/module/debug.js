define(
    [
        'jquery', 'underscore', 'mustache',
        'module/widget', 'jquery.scrollTo'
    ],
    function (
        $, _, mustache
    ) {
        var $document = $(document),
            $body = $('body'),
            $debug = $('.js-debug'),

            handleResponse = function(e, xhr, options, error) {
                try {
                    var response = JSON.parse(xhr.responseText),
                        $template = $('#tpl-debug-container').html()
                        ;

                    if (response && response.debug) {
                        var templateData = response.debug || {};

                        templateData.status = 200 === xhr.status ? false : { code: xhr.status, text: xhr.statusText };

                        var $widget = $(mustache.render($template, templateData));

                        $widget.appendTo($debug);
                        if ($debug.data('opened')) {
                            $widget.slideDown(200);
                        }
                    }
                } catch (error) {
                    console.error(error);
                }
            }
        ;

        $document.ajaxComplete(handleResponse);

        $('.js-debug-link').on('click', function(e) {
            e.stopPropagation();

            var $el = $(e.target)
            ;

            $debug.data('opened', true);
            $el.hide();
            $debug.find('.js-widget').slideDown(200);

            e.preventDefault();
        });

        $body.on('click', '.js-debug-container-link', function(e) {
            e.stopPropagation();

            var $el = $(e.target);

            $el.blur();

            $debug.find('.js-debug-container-content').slideUp(100);

            if ($el.length) {
                var $content = $($el.data('contentSelector'));
                $content.is(':hidden') && $content.slideDown(200);
            }

            e.preventDefault();
        });

        $body.on('click', function(e) {
            var $el = $(e.target)
            ;

            if (!$el.closest('.js-debug').length) {
                $debug.data('opened', false);
                $debug.find('.js-widget').slideUp(100, function() {
                    $('.js-debug-link').show();
                });
            }
        });

        $body.on('click', '.js-debug-tab', function(e) {
            e.stopPropagation();

            var $el = $(e.target);

            $el.parents('.js-widget').find('.js-debug-tab').each(function(i, el) {
                var $el = $(el);

                $($el.attr('href')).hide();
            });

            $($el.attr('href')).show();

            e.preventDefault();
        });

        $body.on('click', '.js-debug-query', function(e) {
            e.stopPropagation();

            var $el = $(e.target)
                $parent = $el.parents('.js-widget');

            $parent.find('.js-debug-tab-query').click();
            setTimeout(function() {
                $parent.find('.js-debug-tab-content-query').scrollTo($($el.attr('href')), {duration: 'fast'});
            }, 200);

            e.preventDefault();
        });

        $debug.find('.js-widget').each(function(i, el) {
            var $widget = $(el);

            $widget.trigger('render', $widget.data('value'));
        });
    }
);