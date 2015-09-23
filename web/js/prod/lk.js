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
		bonusCardFields = $('.jsCardNumber'),
        $input = $('.js-input-control:not(always-show)');
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
        },
        showHideLabels = function showHideLabels() {
            var $this = $(this),
                val = $this.val(),
                $label = $this.parent().find('.js-label-control');

            if (val == ''){
                $label.hide();
            } else {
                $label.show();
            }
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

    $.each($input, showHideLabels);
    $input.on('keyup',showHideLabels);
}());
;(function($) {
    var
        $body = $('body'),
        $mainContainer = $('#personal-container'),
        $messagePopupTemplate = $('#tpl-favorite-messagePopup'),
        $createPopupTemplate = $('#tpl-favorite-createPopup'),
        $movePopupTemplate = $('#tpl-favorite-movePopup'),
        $deletePopupTemplate = $('#tpl-favorite-deletePopup'),
        $deleteFavoritePopupTemplate = $('#tpl-favorite-deleteFavoritePopup'),
        $shareProductPopupTemplate = $('#tpl-favorite-shareProductPopup'),
        $productCheckboxes = $('.personal-favorit__checkbox').not('.js-fav-all'),

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

    $body.on('click', '.personal-favorit__stock', function() {
        $(this).toggleClass('on');
    });

    $productCheckboxes.on('change', function( event ) {
        var
            $this      = $(this),
            checkAll   = $this.closest('.personal__favorits').find('.js-fav-all'),
            list       = $this.closest('.personal__favorits').find('.personal-favorit__checkbox').not('.js-fav-all'),
            allChecked = true;

        list.each(function( i ) {
            allChecked = !!$(this).prop('checked');

            return allChecked;
        });

        checkAll.attr('checked', allChecked);
    });

    $body.on('change', '.js-fav-all', function() {

        var
            list = $(this).closest('.personal__favorits').find('.personal-favorit__checkbox'),
            val = !!$(this).attr('checked')
        ;

        $(list).each(function(){
            $(this).prop('checked', val);
        });
    });

    // подписатьсяна уведомления о товаре
    $body.on('click', '.js-notification-link', function(e) {
        var $el = $(this);

        try {
            $el.toggleClass('on');
            $.post($el.attr('href'));

            e.preventDefault();
        } catch(error) { console.error(error); }
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

    // удалить список
    $body.on('click', '.js-favorite-deleteFavoritePopup', function() {
        var
            $el = $(this),
            data = $el.data(),
            templateValue = data.value
        ;

        try {
            $popup = $(Mustache.render($deleteFavoritePopupTemplate.html(), templateValue)).appendTo($mainContainer);
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
            product,
            productBarcodes = [],
            productNames = [],
            shareUrl
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
                product = $(el).data('product');
                productBarcodes.push(product.barcode);
                productNames.push(product.name);
            });
            templateValue.productUis = productBarcodes.join(',');
            templateValue.countMessage = productBarcodes.length + ' ' + ENTER.utils.numberChoice(productBarcodes.length, ['товаром', 'товарами', 'товарами'])
            templateValue.productNames = productNames.join(', ');

            shareUrl = 'http://www.enter.ru/products/set/' + productBarcodes.join(',');
            templateValue.twitter = {
                url: ENTER.utils.shareLink.twitter(shareUrl, '')
            };
            templateValue.facebook = {
                url: ENTER.utils.shareLink.facebook(shareUrl, '')
            };
            templateValue.vkontakte = {
                url: ENTER.utils.shareLink.vkontakte(shareUrl, '')
            };
            templateValue.googleplus = {
                url: ENTER.utils.shareLink.googleplus(shareUrl, '')
            };
            templateValue.odnoklassniki = {
                url: ENTER.utils.shareLink.odnoklassniki(shareUrl, '')
            };
            templateValue.mailru = {
                url: ENTER.utils.shareLink.mailru(shareUrl, '')
            };
            templateValue.mail = {
                url: ENTER.utils.shareLink.mail(shareUrl, '', '', '')
            };
            console.info(templateValue);

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

    // поделиться в соцсети
    $body.on('click', '.js-shareLink', function(e) {
        var
            $el = $(this),
            url = $el.attr('href')
        ;

        if (0 === url.indexOf('http')) {
            e.stopPropagation();
            window.open(url, '', 'toolbar=0,status=0,width=626,height=436');
            e.preventDefault();
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

    $body.on('click', '.js-orderContainer-link', function() {
        var
            $el = $(this),
            relations = $el.data('relation'),
            $container = relations['container'] && $(relations['container'])
        ;

        try {
            if (!$container.length) {
                throw {message: 'Не найден контейнер'};
            }

            $container.toggleClass('expanded');
        } catch (error) { console.error(error); }
    });

    /* Init */
    $('.textCorner.mOldYear').removeClass('textCorner-open');
    $('.personalTable_rowgroup.mOldYear').hide();

})(jQuery);