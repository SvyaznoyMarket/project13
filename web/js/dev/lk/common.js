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
            url = $el.data('url'),
            data = $el.data('value')
        ;
        console.info('ok');

        try {
            if (!url) {
                throw {message: 'Нет url'};
            }

            data.subscribe.is_confirmed = !$el.is('checked');

            $.post(url, data).done(function(response) {
                console.info('response');
            });
        } catch(error) { console.error(error) };
    });
}(jQuery);