define(
    [
        'require', 'jquery', 'underscore', 'mustache',
        'jquery.enterslide', 'jquery.photoswipe', 'module/product.card.tab'
    ],
    function (
        require, $, _, mustache
    ) {
        var $body = $('body');

        // запрос слайдеров
        var recommendedUrls = [];
        $('.js-productSlider').each(function(i, el) {
            var url = $(el).data('url');
            if (!url) return; // continue

            recommendedUrls.push(url);
        });
        recommendedUrls = _.uniq(recommendedUrls);

        _.each(recommendedUrls, function(url) {
            $.get(url).done(function(response) {
                _.each(response.result.widgets, function(templateData, widgetSelector) {
                    if (!_.isObject(templateData) || !widgetSelector) {
                        console.warn('slider', widgetSelector, templateData);
                        return;
                    }

                    var $widget = $(widgetSelector);

                    if (templateData.count <= 0 && $widget) {
                        $widget.remove();
                        return;
                    }

                    console.info('slider', templateData, $widget);

                    $widget.trigger('render', templateData);
                    $widget.parents('.js-container').show();
                    $body.trigger('render');
                });

                $('.js-productSliderList').enterslide();
            });
        });


        // direct-credit
        $creditPayment = $('.js-creditPayment');
        console.info('creditPayment', $creditPayment);
        var dataValue = $creditPayment.data('value');
        _.isObject(dataValue) && require(['module/direct-credit', 'direct-credit'], function(directCredit) {
            dataValue.product.quantity = 1;

            directCredit.getPayment(
                { partnerId: dataValue.partnerId },
                $body.data('user'),
                dataValue.product,
                function (result) {
                    var $template = $($creditPayment.data('templateSelector')),
                        $price = $($creditPayment.data('priceSelector')),
                        price = Math.ceil(result.payment);

                    $price.html(mustache.render($template.html(), {
                        shownPrice: price
                    }));

                    $creditPayment.show();
                }
            );
        });

    }
);