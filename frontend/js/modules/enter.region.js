+function($){

    // Первоначальная загрузка

    var $body = $(document.body), $popup, $slider, module = {};

    module.show = function(){
        $popup.lightbox_me({
            closeSelector: '.js-popup-close'
        });
    };

    module.init = function(){

        $popup = $('.js-popup-region');

        // Слайдер
        $slider = $popup.find('.js-slider-goods-region-list');
        $slider.slick($slider.data('slick'));

        // Показываем остальные города
        $('.js-region-show-more-cities').on('click', function(e){
            e.preventDefault();
            $('.js-region-more-cities-wrapper').toggleClass('show');
            $slider.slick('reinit'); // потому что слайдер изначально не может выставить правильные css-значения для скрытого div-а
        })
    };

    $.get('/region/init')
        .done(function (res) {
            if (res.result) {
                $body.append($(res.result));
                module.init();
                module.show();
            }
        });

    // Переопределение модуля
    modules.define('enter.region', ['jQuery'], function(provide){
        provide(module)
    })

}(jQuery);