;(function (app, window, $, _, mustache, undefined) {
    app.initialize = function () {
        var $document = $(document),
            $body = $('body');

        $document.ajaxSuccess(function(e, xhr, settings) {
            //var response = JSON.parse(xhr.responseText);
        });

        $body.on('render', function(e) {
            e.stopPropagation();

            var userData = $body.data('user');

            console.info('render:body', userData);

            if (_.isObject(userData.buyButtons)) {
                _.each(userData.buyButtons, function(templateData, idSelector) {
                    $(idSelector).trigger('render', templateData);
                });
            }
            if (_.isObject(userData.buySpinners)) {
                _.each(userData.buySpinners, function(templateData, idSelector) {
                    $(idSelector).trigger('render', templateData);
                });
            }
        });

        // кнопка купить
        $body.on('click', '.js-buyButton', function(e) {
            e.stopPropagation();

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
            e.stopPropagation();

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
            e.stopPropagation();

            var idSelector = $(e.currentTarget).data('idSelector'),
                $el = $(idSelector);

            console.info('render:js-buyButton', $el, templateData);

            $el.replaceWith(mustache.render($('#tpl-product-buyButton').html(), templateData));
        });

        // спиннер для кнопки купить
        $body.on('click', '.js-buySpinner-inc', function(e) {
            e.stopPropagation();

            var $el = $(e.currentTarget),
                $parent = $el.parent('.js-buySpinner'),
                $target = $($parent.data('targetSelector')),
                targetDataValue = $target.data('value');

            console.info('click:js-buySpinner-inc', $el, $target);

            $target.trigger('changeProductQuantityData', targetDataValue.product.quantity + 1);
            $parent.trigger('renderValue', targetDataValue.product);

            $el.blur();
        }).on('click', '.js-buySpinner-dec', function(e) {
            e.stopPropagation();

            var $el = $(e.currentTarget),
                $parent = $el.parent('.js-buySpinner'),
                $target = $($parent.data('targetSelector')),
                targetDataValue = $target.data('value');

            console.info('click:js-buySpinner-dec', $el, $target);

            $target.trigger('changeProductQuantityData', targetDataValue.product.quantity - 1);
            $parent.trigger('renderValue', $target.data('value').product);

            $el.blur();
        }).on('change keyup', '.js-buySpinner-value', function(e) {
            e.stopPropagation();

            var $el = $(e.currentTarget),
                $parent = $el.parent('.js-buySpinner'),
                $target = $($parent.data('targetSelector'));

            console.info('change:js-buySpinner-value', $el, $target);

            var value = $el.val();
            if ('' != value) {
                $target.trigger('changeProductQuantityData', parseInt(value));
                $parent.trigger('renderValue', $target.data('value').product);
            }
        }).on('renderValue', '.js-buySpinner', function(e, product) {
            e.stopPropagation();

            var idSelector = $(e.currentTarget).data('idSelector'),
                $el = $(idSelector);

            console.info('render:js-buySpinner', $el, product);

            $el.find('.js-buySpinner-value').val(product.quantity);
        }).on('render', '.js-buySpinner', function(e, templateData) {
            e.stopPropagation();

            var idSelector = $(e.currentTarget).data('idSelector'),
                $el = $(idSelector);

            console.info('render:js-buySpinner', $el, templateData);

            $el.replaceWith(mustache.render($('#tpl-product-buySpinner').html(), templateData));
        });

        // слайдер
        $body.on('render', '.js-productSlider', function(e, templateData) {
            e.stopPropagation();

            var $el = $(e.currentTarget),
                $template = $('#tpl-product-slider');

            console.info('render:js-productSlider', $el, templateData);

            $el.replaceWith(mustache.render($template.html(), templateData, $template.data('partial')));
        });

        // контейнер слайдера
        $body.on('render', '.js-productSlider', function(e, templateData) {
            e.stopPropagation();

            var $el = $(e.currentTarget),
                $parent = $el.parents('.js-container');

            console.info('render:js-productSlider parent', $parent, templateData);

            if (templateData.count > 0) {
                $parent.show();

                // вызов скрипта слайдер items.slides.js
                $('.js-productSlider').enterSlides();
            }
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
                if (_.isObject(response.result)) {
                    $body.data('user', response.result);
                    $body.trigger('render');
                }
            });
        }

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
                _.each(response.result, function(sliderData, sliderName) {
                    var $el = $('.js-productSlider').filter('[data-name="' + sliderName + '"]');

                    console.info('slider', sliderName, sliderData, $el);

                    $el.trigger('render', sliderData);
                    $body.trigger('render');
                })
            });
        })
    };


    $(function () {
        app.initialize();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._, window.Mustache));


