define(
    [
        'jquery', 'underscore', 'mustache',
        'module/config'
    ],
    function (
        $, _, mustache,
        config
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

                var $el = $(e.currentTarget),
                    $template = $($el.data('templateSelector'));

                console.info('render', $template, $el, templateData);

                //$el.replaceWith(mustache.render($template.html(), templateData, $template.data('partial')));
                $el.html(
                    $(mustache.render($template.html(), templateData, $template.data('partial'))).html()
                );
            };


        $body.on('render', renderBody);
        $body.on('render', '.js-widget', renderWidget);

        $.post(config.user.infoUrl).done(function(response) {
            if (_.isObject(response.result)) {
                if (_.isObject(response.result.widgets)) {
                    $body.data('widget', response.result.widgets);
                    $body.trigger('render');
                }

                if (_.isObject(response.result.user)) {
                    $body.data('user', response.result.user);
                }
            }
        });

    }
);