+function($){
    $('.js-slider-2').goodsSlider({
        leftArrowSelector: '.goods-slider__btn--prev',
        rightArrowSelector: '.goods-slider__btn--next',
        sliderWrapperSelector: '.goods-slider__inn',
        sliderSelector: '.goods-slider-list',
        itemSelector: '.goods-slider-list__i'
    });
}(jQuery);
/**
 * Enterprize
 *
 * @author  Shaposhnik Vitaly
 */
;(function() {
	var
		mobilePhoneField = $('#user_mobile_phone'),
		bonusCardFields = $('.jsCardNumber');
	// end of vars

	var
		setMask = function setMask(field, mask) {
			if ( undefined == typeof(field) || undefined == typeof(mask) ) return;
			field.mask(mask, { placeholder: '*' });
		},

		addCardMask = function addCardMask() {
			var
				self = $(this),
				mask = self.data('mask');
			// end of vars

			if ( undefined == typeof(mask) ) {
				return;
			}

			setMask(self, mask);
		};
	// end of functions

	$.mask.definitions['x'] = '[0-9]';

	// устанавливаем маску для поля "Ваш мобильный телефон"
	//mobilePhoneField.length && mobilePhoneField.mask('8xxxxxxxxxx');

	// устанавливаем маски для карт лояльности
	bonusCardFields.length && bonusCardFields.each(addCardMask);

	$.mask.definitions['x'] = '[0-9]';
	$('.js-lk-mobilePhone, .js-lk-homePhone').mask('+7 (xxx) xxx-xx-xx', {
		autoclear: 0
	});

}());
;(function($) {
    var
        $body = $('body'),
        $mainContainer = $('#personal-container'),
        $messagePopupTemplate = $('#tpl-favorite-messagePopup'),
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
                $popup = $(Mustache.render($messagePopupTemplate.html(), {message: {title: error.name}})).appendTo($mainContainer);
                showPopup('#' + $popup.attr('id'));
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
                $popup = $(Mustache.render($messagePopupTemplate.html(), {message: {title: error.name}})).appendTo($mainContainer);
                showPopup('#' + $popup.attr('id'));
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
                $popup = $(Mustache.render($messagePopupTemplate.html(), {message: {title: error.name}})).appendTo($mainContainer);
                showPopup('#' + $popup.attr('id'));
            }

            console.error(error);
        }
    });

}(jQuery));
/**
 * Orders Page
 *
 * @author  Zhukov Roman
 */
;(function($){

    var $body = $(document.body);

    /* Скрытие/раскрытие истории заказов за год */
    $body.on('click', '.personalTable_cell_rowspan', function(){

        var $corner = $(this).find('.textCorner'),
            year = $(this).data('value'),
            $table = $('.personalTable_rowgroup_' + year);

        $table.toggle();

        if ($corner.hasClass('textCorner-open')) {
            $corner.removeClass('textCorner-open')
        } else {
            $corner.addClass('textCorner-open')
        }

    });

    /* Init */
    $('.textCorner.mOldYear').removeClass('textCorner-open');
    $('.personalTable_rowgroup.mOldYear').hide();

})(jQuery);