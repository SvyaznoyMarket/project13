define(
    [
        'jquery', 'underscore',
        'module/widget'
    ],
    function ($, _) {

        var $body = $('body'),

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
    }
);