;(function (app, window, $, _, mustache, undefined) {
    app.initialize = function () {
        var $document = $(document),
            $body = $('body');

        $document.ajaxSuccess(function(e, xhr, settings) {
            //var response = JSON.parse(xhr.responseText);
        });

        // кнопка купить
        $body.on('click', '.js-buyButton', function(e) {
            var $el = $(e.currentTarget),
                data = $el.data();

            console.info('click:js-buyButton', $el, data);

            if (data.url) {
                $.post(data.url, data.value, function(response) {
                    if (_.isObject(response.result.buyButton)) {
                        $el.trigger('render', response.result.buyButton);
                        $('.js-buySpinner').filter('[data-target-selector="' + data.idSelector + '"]').trigger('render', response.result.buySpinner);
                    }
                });

                e.preventDefault();
            }
        }).on('changeProductQuantityData', '.js-buyButton', function(e, quantity) {
            var idSelector = $(e.currentTarget).data('idSelector'),
                $el = $(idSelector),
                dataValue = $el.data('value');

            console.info('changeProductQuantityData:js-buyButton', $el, quantity);

            if (!_.isFinite(quantity) || (quantity <= 0)) {
                var error = {code: 'invalid', message: 'Количество должно быть большим нуля'};

                console.info('changeProductQuantityData:js-buyButton', error, quantity, $el);

                return error;
            }

            dataValue.product.quantity = quantity;
        }).on('render', '.js-buyButton', function(e, templateData) {
            var idSelector = $(e.currentTarget).data('idSelector'),
                $el = $(idSelector);

            console.info('render:js-buyButton', $el, templateData);

            $el.replaceWith(mustache.render($('#tpl-product-buyButton').html(), templateData));
        });

        // спиннер для кнопки купить
        $body.on('click', '.js-buySpinner .js-inc', function(e) {
            var $el = $(e.currentTarget),
                $parent = $el.parent('.js-buySpinner'),
                $target = $($parent.data('targetSelector')),
                targetDataValue = $target.data('value');

            console.info('click:js-buySpinner js-inc', $el, $target);

            $target.trigger('changeProductQuantityData', targetDataValue.product.quantity + 1);
            $parent.trigger('renderValue', targetDataValue.product);

            $el.blur();
        }).on('click', '.js-buySpinner .js-dec', function(e) {
            var $el = $(e.currentTarget),
                $parent = $el.parent('.js-buySpinner'),
                $target = $($parent.data('targetSelector')),
                targetDataValue = $target.data('value');

            console.info('click:js-buySpinner js-dec', $el, $target);

            $target.trigger('changeProductQuantityData', targetDataValue.product.quantity - 1);
            $parent.trigger('renderValue', $target.data('value').product);

            $el.blur();
        }).on('change keyup', '.js-buySpinner .js-value', function(e) {
            var $el = $(e.currentTarget),
                $parent = $el.parent('.js-buySpinner'),
                $target = $($parent.data('targetSelector'));

            console.info('change:js-buySpinner js-value', $el, $target);

            var value = $el.val();
            if ('' != value) {
                $target.trigger('changeProductQuantityData', parseInt(value));
                $parent.trigger('renderValue', $target.data('value').product);
            }
        }).on('renderValue', '.js-buySpinner', function(e, product) {
            var idSelector = $(e.currentTarget).data('idSelector'),
                $el = $(idSelector);

            console.info('render:js-buySpinner', $el, product);

            $el.find('.js-value').val(product.quantity);
        }).on('render', '.js-buySpinner', function(e, templateData) {
            var idSelector = $(e.currentTarget).data('idSelector'),
                $el = $(idSelector);

            console.info('render:js-buySpinner', $el, templateData);

            $el.replaceWith(mustache.render($('#tpl-product-buySpinner').html(), templateData));
        });


        // запрос инфы по пользователю
        var config = _.extend({
                user: {
                    infoCookie: null,
                    infoUrl: null
                }
            }, $body.data('config'));

        console.info('config', config);

        var hasUserInfo = ('1' === $.cookie(config.user.infoCookie)),
            userInfoUrl = config.user.infoUrl;

        console.info('hasUserInfo', hasUserInfo);

        if (hasUserInfo && userInfoUrl) {
            $.post(userInfoUrl).done(function(response) {
                if (_.isObject(response.result.buyButtons)) {
                    _.each(response.result.buyButtons, function(templateData, idSelector) {
                        $(idSelector).trigger('render', templateData);
                    });
                }
                if (_.isObject(response.result.buySpinners)) {
                    _.each(response.result.buySpinners, function(templateData, idSelector) {
                        $(idSelector).trigger('render', templateData);
                    });
                }
            });
        }
    };


    $(function () {
        app.initialize();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._, window.Mustache));