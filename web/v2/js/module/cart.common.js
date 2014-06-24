define(
    [
        'jquery', 'underscore', 'module/config',
        'module/widget'
    ],
    function ($, _, config) {

        var $body = $('body'),

            addProductToCart = function(e) {
                e.stopPropagation();

                var $el = $(e.target),
                    data = $el.data(),
                    $widget = $($el.data('widgetSelector'))
                ;

                console.info('addProductToCart', $el, $widget, data);

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

            deleteProductFromCart = function(e) {
                e.stopPropagation();

                var $el = $(e.target),
                    data = $el.data(),
                    $spinnerWidget = $($el.data('spinnerSelector'))
                ;

                console.info('deleteProductFromCart', $el, data);

                if (data.url) {
                    $.post(data.url, data.value, function(response) {
                        if (response.redirect && (-1 !== response.redirect.indexOf('/'))) {
                            window.location.href = response.redirect;
                        }

                        if (_.isObject(response.result) && _.isObject(response.result.widgets)) {
                            _.each(response.result.widgets, function(templateData, selector) {
                                if (!selector) {
                                    return;
                                }

                                $(selector).trigger('render', templateData);
                            });
                        }

                        if ($el.data('parentContainerSelector')) {
                            var $parentContainer = $el.parents($el.data('parentContainerSelector'));
                            if ($parentContainer.length) {
                                $parentContainer.slideUp(300, function() {
                                    $parentContainer.remove();
                                });
                            }
                        }
                    });

                    e.preventDefault();

                    if ($spinnerWidget.length) {
                        var timer = $spinnerWidget.data('timer');

                        try {
                            clearTimeout(timer);
                        } catch (error) {
                            console.warn(error);
                        }
                    }
                }
            },

            changeProductQuantity = function(e, quantity) {
                e.stopPropagation();

                var idSelector = $(e.target),
                    $el = $(idSelector),
                    dataValue = $el.data('value'),
                    $widget = $($el.data('widgetSelector')),
                    timer = parseInt($widget.data('timer'))
                ;

                console.info('changeProductQuantity', $el, quantity);

                if (_.isFinite(timer) && (timer > 0)) {
                    try {
                        clearTimeout(timer);
                    } catch (error) {
                        console.warn(error);
                    }

                    timer = setTimeout(function() { addProductToCart(e); }, 600);

                    $widget.data('timer', timer);
                }

                if (!_.isFinite(quantity) || (quantity <= 0) || (quantity > 999)) {
                    var error = {code: 'invalid', message: 'Неверное количество товара'};

                    console.info('changeProductQuantityData:js-buyButton', error, quantity, $el);

                    return error;
                }

                dataValue.product.quantity = quantity;

                // FIXME: осторожно, гкод
                if ($el.hasClass('js-quickBuyButton')) {
                    var url = $el.attr('href').replace(/\d+$/, quantity);
                    $el.attr('href', url);
                }
            },

            incSpinnerValue = function(e) {
                e.stopPropagation();

                var $el = $(e.target),
                    $widget = $($el.data('widgetSelector')),
                    $target = $($el.data('buttonSelector')),
                    targetDataValue = $target.data('value');

                console.info('incSpinnerValue', $el, $target, $widget);

                if (targetDataValue) {
                    $target.trigger('changeProductQuantityData', targetDataValue.product.quantity + 1);
                    $widget.trigger('renderValue', targetDataValue.product);
                }

                $el.blur();
            },

            decSpinnerValue = function(e) {
                e.stopPropagation();

                var $el = $(e.target),
                    $widget = $($el.data('widgetSelector')),
                    $target = $($el.data('buttonSelector')),
                    targetDataValue = $target.data('value');

                console.info('decSpinnerValue', $el, $target);

                if (targetDataValue) {
                    $target.trigger('changeProductQuantityData', targetDataValue.product.quantity - 1);
                    $widget.trigger('renderValue', targetDataValue.product);
                }

                $el.blur();
            },

            changeSpinnerValue = function(e) {
                e.stopPropagation();

                var $el = $(e.target),
                    $widget = $($el.data('widgetSelector')),
                    $target = $($el.data('buttonSelector')),
                    targetDataValue = $target.data('value');

                console.info('changeSpinnerValue', $el, $target);

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

                var $el = $(e.target);

                console.info('renderSpinnerValue', $el, product);

                $el.find('.js-buySpinner-value').val(product.quantity);
            },

            setCredit = function(e) {
                e.stopPropagation();

                var $el = $(e.target);

                if ($el.is(':checked')) {
                    $.cookie(config.credit.cookieName, 1, {expires: 7});
                } else {
                    $.removeCookie(config.credit.cookieName);
                }

                e.preventDefault();
            },

            removeCredit = function(e) {
                e.stopPropagation();

                $.removeCookie(config.credit.cookieName);

                e.preventDefault();
            },

            initCredit = function($elements) {
                if (1 == $.cookie(config.credit.cookieName)) {
                    $elements.attr('checked', true);
                } else {
                    $elements.removeAttr('checked');
                }
            }
        ;


        // кнопка купить
        $body
            .on('click', '.js-buyButton', addProductToCart)
            .on('changeProductQuantityData', '.js-buyButton', changeProductQuantity)
            .on('changeProductQuantityData', '.js-quickBuyButton', changeProductQuantity)
            .on('click', '.js-deleteButton', deleteProductFromCart)

        // спиннер для кнопки купить
        $body
            .on('click dblclick contextmenu', '.js-buySpinner-inc', incSpinnerValue)
            .on('click dblclick contextmenu', '.js-buySpinner-dec', decSpinnerValue)
            .on('change keyup', '.js-buySpinner-value', changeSpinnerValue)
            .on('renderValue', '.js-buySpinner', renderSpinnerValue)
            .on('changeProductQuantityData', '.js-buySpinner-value', changeProductQuantity)

        // купить в кредит
        $body
            .on('change', '.js-creditButton', setCredit)
            .on('change', '.js-creditButton-remove', removeCredit)

        initCredit($('.js-creditButton'));

        return {
            initCredit: function() {
                initCredit($('.js-creditButton'));
            }
        }
    }
);