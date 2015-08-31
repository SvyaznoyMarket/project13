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
        $body = $('body')
    ;

    $body.on('click', '.personal-favorit__price-change', function() {
        $(this).toggleClass('on');
    });
    $body.on('click', '.personal-favorit__stock', function() {
        $(this).toggleClass('on');
    });
    $body.on('click', '.js-fav-popup-show', 'click',function() {
        var popup = $(this).data('popup');

        $('body').append('<div class="overlay"></div>');
        $('.overlay').data('popup', popup).show();
        $('.'+popup).show();
    });
    $body.on('click', '.overlay',function() {
        var popup = $(this).data('popup');
        $('.' + popup).hide();
        $('.overlay').remove();
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
    $body.on('click', '.popup-closer', function() {
        $(this).parent().hide();
        $('.overlay').remove();
    });

    $body.on('click', '.js-favorite-showMovePopup', function() {
        var
            $el = $(this),
            data = $el.data('action'),
            $container = data.container && $(data.container),
            $target = data.target && $(data.target),
            $productInputs,
            productUis = []
        ;

        try {
            if (!$container.length) {
                throw {name: 'Контейнер не найден'};
            }
            $productInputs = $container.find('input[data-type="product"]:checked');
            if (!$productInputs.length) {
                throw {name: 'Товары не выбраны'};
            }
            $productInputs.each(function(i, el) {
                productUis.push($(el).val());
            });

            if (!$target.length) {
                throw {name: 'Целевой элемент не найден'};
            }
            $formInput = $target.find('input[name="productUis"]');
            if (!$formInput.length) {
                throw {name: 'Инпут формы не найден'};
            }
            $formInput.val(productUis.join(','));
        } catch (error) { console.error(error); }
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