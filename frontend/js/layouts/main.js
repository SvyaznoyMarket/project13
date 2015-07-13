+function(){

    // TODO вынести в отдельный файл
    window.onload = function(){
        var debugPanel = document.querySelector('.jsOpenDebugPanelContent');
        if (debugPanel) {
            debugPanel.addEventListener('click', function(){
                if (modules.getState('enter.debug') == 'NOT_RESOLVED') {
                    modules.require('enter.debug', function(){})
                }
            });
        }
    };

    modules.require(
        ['jQuery', 'jquery.slick', 'library'],
        function($) {

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

            /**
             * lightbox не всегда нужен, поэтому запросим его только в случае необходимости
             */
            $('.js-popup-show').on('click', function( event ) {

                var current = $(this).data('popup');
                event.preventDefault();

                modules.require('jquery.lightbox_me', function(){
                    $('.js-popup-' + current ).lightbox_me({
                        closeSelector: '.js-popup-close'
                    });
                });

            })
        }
    );

}();