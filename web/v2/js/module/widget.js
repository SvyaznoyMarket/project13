define(
    [
        'jquery', 'underscore', 'mustache',
    ],
    function (
        $, _, mustache
    ) {
        var $body = $('body'),

            renderBody = function(e) {
                e.stopPropagation();

                var widgets = $body.data('widget');

                console.info('render:body', widgets);

                if (_.isObject(widgets)) {
                    _.each(widgets, function(templateData, selector) {
                        if (!selector) {
                            console.warn('widget', selector, templateData);
                            return; // continue
                        }
                        console.info('widget', selector, templateData);

                        $(selector).trigger('render', templateData);
                    });
                }
            },

            renderWidget = function(e, templateData) {
                e.stopPropagation();

                var $el = $(e.target),
                    $template = $($el.data('templateSelector'));

                console.info('render', $template, $el, templateData);

                //$el.replaceWith(mustache.render($template.html(), templateData, $template.data('partial')));
                $el.html(
                    $(mustache.render($template.html(), templateData, $template.data('partial'))).html()
                );
            };


        $body.on('render', renderBody);
        $body.on('render', '.js-widget', renderWidget);
    }
);