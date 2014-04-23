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
                _.each(userData.buyButtons, function(templateData, widgetSelector) {
                    $(widgetSelector).trigger('render', templateData);
                });
            }
            if (_.isObject(userData.buySpinners)) {
                _.each(userData.buySpinners, function(templateData, widgetSelector) {
                    $(widgetSelector).trigger('render', templateData);
                });
            }
        });

        $body.on('render', '.js-widget', function(e, templateData) {
            e.stopPropagation();

            var $el = $(e.currentTarget),
                $template = $($el.data('templateSelector'));

            console.info('render', $template, $el, templateData);

            //$el.replaceWith(mustache.render($template.html(), templateData, $template.data('partial')));
            $el.html(
                $(mustache.render($template.html(), templateData, $template.data('partial'))).html()
            );
        });

        // кнопка купить
        $body.on('click', '.js-buyButton', function(e) {
            e.stopPropagation();

            var $el = $(e.currentTarget),
                data = $el.data(),
                $widget = $($el.data('widgetSelector'));

            console.info('click:js-buyButton', $el, $widget, data);

            if (data.url) {
                $.post(data.url, data.value, function(response) {
                    if (_.isObject(response.result.buyButton)) {
                        $widget.trigger('render', response.result.buyButton);

                        if (data.spinnerWidgetSelector) {
                            $(data.spinnerWidgetSelector).trigger('render', response.result.buySpinner);
                        }
                    }
                });

                e.preventDefault();
            }
        }).on('changeProductQuantityData', '.js-buyButton', function(e, quantity) {
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
        });

        // спиннер для кнопки купить
        $body.on('click', '.js-buySpinner-inc', function(e) {
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
        }).on('click', '.js-buySpinner-dec', function(e) {
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
        }).on('change keyup', '.js-buySpinner-value', function(e) {
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
        }).on('renderValue', '.js-buySpinner', function(e, product) {
            e.stopPropagation();

            var $el = $(e.currentTarget);

            console.info('render:js-buySpinner', $el, product);

            $el.find('.js-buySpinner-value').val(product.quantity);
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
                _.each(response.result, function(sliderData) {
                    if (!_.isObject(sliderData) || !sliderData.widgetId) {
                        console.warn('slider', sliderData);
                        return;
                    }

                    var $widget = $('.' + sliderData.widgetId); // TODO: исправить

                    if (sliderData.count <= 0 && $widget) {
                        $widget.remove();
                        return;
                    }

                    console.info('slider', sliderData, $widget);

                    $widget.trigger('render', sliderData);
                    console.warn($widget.parents('.js-container'));
                    $widget.parents('.js-container').show();
                    $body.trigger('render');
                });

                $('.js-productSliderList').enterslide();
            });
        })
    };


    $(function () {
        app.initialize();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._, window.Mustache));


