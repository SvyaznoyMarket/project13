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