+function(){

    modules.require(
        ['jQuery', 'jquery.slick', 'library'],
        function($) {

            console.log('main.js');

            // Слайдер
            $('.js-banners-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                infinite: true,
                dots: true
            });

            // Снова слайдеры
            var $sliders = $('.js-slider-goods');

            $sliders.each(function() {

                var current = $(this).data('slick-slider');

                $('.js-slider-goods-' + current).slick({
                    dots: false,
                    infinite: false,
                    prevArrow: '.js-goods-slider-btn-prev-' + current,
                    nextArrow: '.js-goods-slider-btn-next-' + current
                })
            });
        }
    );

}();