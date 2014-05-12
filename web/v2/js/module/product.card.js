define(
    [
        'require', 'jquery', 'underscore', 'mustache',
        'module/config',
        'module/widget',
        'jquery.enterslide', 'jquery.photoswipe',
        'module/product.card.tab',
    ],
    function (
        require, $, _, mustache,
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

            addProductToCart = function(e) {
                e.stopPropagation();

                var $el = $(e.currentTarget),
                    data = $el.data(),
                    $widget = $($el.data('widgetSelector'));

                console.info('click:js-buyButton', $el, $widget, data);

                if (data.url) {
                    $.post(data.url, data.value, function(response) {
                        if (_.isObject(response.result.widgets)) {
                            _.each(response.result.widgets, function(templateData, selector) {
                                if (!selector) {
                                    return;
                                }

                                $(selector).trigger('render', templateData);
                            });
                        }
                    });

                    e.preventDefault();
                }
            },

            changeProductQuantity = function(e, quantity) {
                e.stopPropagation();

                var idSelector = $(e.currentTarget),
                    $el = $(idSelector),
                    dataValue = $el.data('value');

                console.info('changeProductQuantityData:js-buyButton', $el, quantity);

                if (!_.isFinite(quantity) || (quantity <= 0)) {
                    var error = {code: 'invalid', message: 'Количество должно быть большим нуля'};

                    console.info('changeProductQuantityData:js-buyButton', error, quantity, $el);

                    return error;
                }

                dataValue.product.quantity = quantity;
            },

            incSpinnerValue = function(e) {
                e.stopPropagation();

                var $el = $(e.currentTarget),
                    $widget = $($el.data('widgetSelector')),
                    $target = $($el.data('buttonSelector')),
                    targetDataValue = $target.data('value');

                console.info('click:js-buySpinner-inc', $el, $target);

                if (targetDataValue) {
                    $target.trigger('changeProductQuantityData', targetDataValue.product.quantity + 1);
                    $widget.trigger('renderValue', targetDataValue.product);
                }

                $el.blur();
            },

            decSpinnerValue = function(e) {
                e.stopPropagation();

                var $el = $(e.currentTarget),
                    $widget = $($el.data('widgetSelector')),
                    $target = $($el.data('buttonSelector')),
                    targetDataValue = $target.data('value');

                console.info('click:js-buySpinner-dec', $el, $target);

                if (targetDataValue) {
                    $target.trigger('changeProductQuantityData', targetDataValue.product.quantity - 1);
                    $widget.trigger('renderValue', targetDataValue.product);
                }

                $el.blur();
            },

            changeSpinnerValue = function(e) {
                e.stopPropagation();

                var $el = $(e.currentTarget),
                    $widget = $($el.data('widgetSelector')),
                    $target = $($el.data('buttonSelector')),
                    targetDataValue = $target.data('value');

                console.info('change:js-buySpinner-value', $el, $target);

                var value = $el.val();
                if ('' != value) {
                    $target.trigger('changeProductQuantityData', parseInt(value));

                    if (targetDataValue) {
                        $widget.trigger('renderValue', targetDataValue.product);
                    }
                }
            },

            renderSpinnerValue = function(e, product) {
                e.stopPropagation();

                var $el = $(e.currentTarget);

                console.info('render:js-buySpinner', $el, product);

                $el.find('.js-buySpinner-value').val(product.quantity);
            };


        // кнопка купить
        $body
            .on('click', '.js-buyButton', addProductToCart)
            .on('changeProductQuantityData', '.js-buyButton', changeProductQuantity);

        // спиннер для кнопки купить
        $body
            .on('click', '.js-buySpinner-inc', incSpinnerValue)
            .on('click', '.js-buySpinner-dec', decSpinnerValue)
            .on('change keyup', '.js-buySpinner-value', changeSpinnerValue)
            .on('renderValue', '.js-buySpinner', renderSpinnerValue);



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
        if (_.isObject(dataValue)) {
            require(['direct-credit'], function() {
                dc_getCreditForTheProduct(
                    dataValue.partnerId,
                    $body.data('user').sessionId,
                    'getPayment',
                    {
                        price: dataValue.product.price,
                        count: 1,
                        type: dataValue.product.type
                    },
                    function(result) {
                        console.info('dc_getCreditForTheProduct', result);
                        if (!'payment' in result || (result.payment <= 0)) {
                            return;
                        }

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

    }
);