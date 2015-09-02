;(function($) {
    var
        $body = $('body'),
        $mainContainer = $('#personal-container'),
        $createPopupTemplate = $('#tpl-favorite-createPopup'),
        $movePopupTemplate = $('#tpl-favorite-movePopup'),
        $deletePopupTemplate = $('#tpl-favorite-deletePopup'),
        $shareProductPopupTemplate = $('#tpl-favorite-shareProductPopup'),

        showPopup = function(selector) {
            $('body').append('<div class="overlay"></div>');
            $('.overlay').data('popup', selector).show();
            $(selector).show();
        },

        hidePopup = function(selector) {
            $(selector).remove();
            $('.overlay').remove();
        }
    ;

    $body.on('click', '.overlay',function() {
        var selector = $(this).data('popup');
        hidePopup(selector);
    });
    $body.on('click', '.popup-closer', function() {
        hidePopup('#' + $(this).parent().attr('id'))
    });

    $body.on('click', '.personal-favorit__price-change', function() {
        $(this).toggleClass('on');
    });
    $body.on('click', '.personal-favorit__stock', function() {
        $(this).toggleClass('on');
    });

    $body.on('change', '.js-fav-all', function() {

        var
            list = $(this).closest('.personal__favorits').find('.personal-favorit__checkbox'),
            val = !!$(this).attr('checked')
        ;

        $(list).each(function(){
            $(this).attr('checked', val);
        });
    });

    // подписатьсяна уведомления о товаре
    $body.on('click', '.js-notification-link', function() {
        var $el = $(this);


    });

    // создать список
    $body.on('click', '.js-favorite-createPopup', function() {
        var
            $el = $(this),
            data = $el.data(),
            templateValue = data.value
        ;

        try {
            $popup = $(Mustache.render($createPopupTemplate.html(), templateValue)).appendTo($mainContainer);
            showPopup('#' + $popup.attr('id'));
        } catch (error) {
            console.error(error);
        }
    });

    // перенести товары в список
    $body.on('click', '.js-favorite-movePopup', function() {
        var
            $el = $(this),
            $popup,
            data = $el.data(),
            $container = data.container && $(data.container),
            templateValue = data.value,
            productUis = []
        ;

        try {
            if (!$container.length) {
                throw {name: 'Контейнер не найден'};
            }
            $productInputs = $container.find('input[data-type="product"]:checked');
            if (!$productInputs.length) {
                throw {name: 'Товары не выбраны', code: 'empty-product'};
            }
            $productInputs.each(function(i, el) {
                productUis.push($(el).val());
            });
            templateValue.productUis = productUis.join(',');

            $popup = $(Mustache.render($movePopupTemplate.html(), templateValue)).appendTo($mainContainer);
            showPopup('#' + $popup.attr('id'));
        } catch (error) {
            if ('empty-product' === error.code) {
                showPopup('#message-popup');
            }

            console.error(error);
        }
    });

    // удалить товары из списка
    $body.on('click', '.js-favorite-deletePopup', function() {
        var
            $el = $(this),
            $popup,
            data = $el.data(),
            $container = data.container && $(data.container),
            templateValue = data.value,
            productUis = []
        ;

        try {
            if (!$container.length) {
                throw {name: 'Контейнер не найден'};
            }
            $productInputs = $container.find('input[data-type="product"]:checked');
            if (!$productInputs.length) {
                throw {name: 'Товары не выбраны', code: 'empty-product'};
            }
            $productInputs.each(function(i, el) {
                productUis.push($(el).val());
            });
            templateValue.productUis = productUis.join(',');

            $popup = $(Mustache.render($deletePopupTemplate.html(), templateValue)).appendTo($mainContainer);
            showPopup('#' + $popup.attr('id'));
        } catch (error) {
            if ('empty-product' === error.code) {
                showPopup('#message-popup');
            }

            console.error(error);
        }
    });

    // поделится списком
    $body.on('click', '.js-favorite-shareProductPopup', function() {
        var
            $el = $(this),
            $popup,
            data = $el.data(),
            $container = data.container && $(data.container),
            templateValue = data.value,
            productUis = []
        ;

        try {
            if (!$container.length) {
                throw {name: 'Контейнер не найден'};
            }
            $productInputs = $container.find('input[data-type="product"]:checked');
            if (!$productInputs.length) {
                throw {name: 'Товары не выбраны', code: 'empty-product'};
            }
            $productInputs.each(function(i, el) {
                productUis.push($(el).val());
            });
            templateValue.productUis = productUis.join(',');
            templateValue.countMessage = productUis.length + ' ' + ENTER.utils.numberChoice(productUis.length, ['товаром', 'товарами', 'товарами'])

            $popup = $(Mustache.render($shareProductPopupTemplate.html(), templateValue)).appendTo($mainContainer);
            showPopup('#' + $popup.attr('id'));
        } catch (error) {
            if ('empty-product' === error.code) {
                showPopup('#message-popup');
            }

            console.error(error);
        }
    });

}(jQuery));