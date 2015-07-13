+function(){

    modules.require(
        ['jQuery', 'jquery.slick', 'library', 'jquery.lightbox_me'],
        function($) {

            console.log('main.js');

            // Слайдер
            $('.js-banners-slider').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 1,
                slidesToScroll: 1,
                infinite: true,
                autoplay: true,
                dots: true
            });

            // Снова слайдеры
            var $sliders = $('.js-slider-goods');

            $sliders.each(function() {

                var current = $(this).data('slick-slider');

                $('.js-slider-goods-' + current).slick({
                    lazyLoad: 'ondemand',
                    dots: false,
                    infinite: false,
                    prevArrow: '.js-goods-slider-btn-prev-' + current,
                    nextArrow: '.js-goods-slider-btn-next-' + current
                })
            });

            $('.js-popup-show').on('click', function( event ) {
                current = $(this).data('popup');

                $('.js-popup-' + current ).lightbox_me({
                    closeSelector: '.js-popup-close'
                });
                event.preventDefault();
            })
        }
    );

}();