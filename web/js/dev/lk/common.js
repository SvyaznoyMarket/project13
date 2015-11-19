+function($){
    $('.js-slider').goodsSlider({
        leftArrowSelector: '.goods-slider__btn--prev',
        rightArrowSelector: '.goods-slider__btn--next',
        sliderWrapperSelector: '.goods-slider__inn',
        sliderSelector: '.goods-slider-list',
        itemSelector: '.goods-slider-list__i'
    });
    $('.js-slider-2').goodsSlider({
        leftArrowSelector: '.goods-slider__btn--prev',
        rightArrowSelector: '.goods-slider__btn--next',
        sliderWrapperSelector: '.goods-slider__inn',
        sliderSelector: '.goods-slider-list',
        itemSelector: '.goods-slider-list__i'
    });

    $('body').on('click', '.js-user-subscribe-input', function(event) {
        var
            $el = $(this),
            data = $el.data('value'),
            isChecked = !!$el.is('checked'),
            url = isChecked ? $el.data('setUrl') : $el.data('deleteUrl')
        ;

        try {
            if (!url) {
                throw {message: 'Нет url'};
            }

            $.post(url, data).done(function(response) {
                if (!response.success) {
                }
            });
        } catch(error) { console.error(error) };
    });
}(jQuery);