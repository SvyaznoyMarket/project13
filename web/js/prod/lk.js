+function($){
    $('.js-slider-2').goodsSlider({
        leftArrowSelector: '.goods-slider__btn--prev',
        rightArrowSelector: '.goods-slider__btn--next',
        sliderWrapperSelector: '.goods-slider__inn',
        sliderSelector: '.goods-slider-list',
        itemSelector: '.goods-slider-list__i'
    });
    $('.personal-favorit__price-change').on('click',function(){
        $(this).toggleClass('on');
    });
    $('.personal-favorit__stock').on('click',function(){
        $(this).toggleClass('on');
    });
    $('.js-fav-popup-show').on('click',function(){
        var popup = $(this).data('popup');

        $('body').append('<div class="overlay"></div>');
        $('.overlay').data('popup', popup).show();
        $('.'+popup).show();
    });
    $('body').on('click','.overlay',function(){
      var popup = $(this).data('popup');
       $('.'+popup).hide();
        $('.overlay').remove();
    });
    $('.js-fav-all').change( function(){

        var list = $(this).closest('.personal__favorits').find('.personal-favorit__checkbox'),
            val = !!$(this).attr('checked');

        $(list).each(function(){
           $(this).attr('checked', val);
        });
    });
    $('.popup-closer').on('click',function(){
       $(this).parent().hide();
        $('.overlay').remove();
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