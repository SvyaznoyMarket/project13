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

    $('js-epInfoSlide').goodsSlider({
        leftArrowSelector: '.js-ep-info__product-prev',
        rightArrowSelector: '.js-ep-info__product-next',
        sliderWrapperSelector: '.js-ep-info__product-slide',
        sliderSelector: '.js-ep-info__product-list',
        itemSelector: '.js-ep-info__product-item'
    });

}(jQuery);