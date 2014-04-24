(function (app, window, $, _, mustache, undefined) {
    app.initialize = function () {
        var $document = $(document),
            $body = $('body');

        $document.ajaxSuccess(function(e, xhr, settings) {
            //var response = JSON.parse(xhr.responseText);
        });

        $body.on('render', function(e) {
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
                if (_.isObject(response.result) && _.isObject(response.result.widgets)) {
                    $body.data('widget', response.result.widgets);
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


        // автоподстановка регионов
        var $regionInput = $('#js-region-input');
        $regionInput.autocomplete({
            autoFocus: true,
            appendTo: '#js-region-autocomplete',
            source: function(request, response) {
                $.ajax({
                    url: $regionInput.data('url'),
                    dataType: 'json',
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response($.map(data.result, function(item) {
                            return {
                                label: item.name,
                                value: item.name,
                                url: item.url
                            };
                        }));
                    }
                });
            },
            minLength: 3,
            select: function(e, ui) {
                console.info('select:regionInput', e, ui);
            },
            open: function() {
                //$(this).removeClass('ui-corner-all').addClass('ui-corner-top');
                $('.ui-autocomplete').css({'left' : 0, 'top' : '5px', 'width' : '100%'});
            },
            close: function() {
                //$(this).removeClass('ui-corner-top').addClass('ui-corner-all');
            },
            messages: {
                noResults: '',
                results: function(amount) {
                    return '';
                }
            }
        });
    };


    $(function () {
        app.initialize();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._, window.Mustache));


