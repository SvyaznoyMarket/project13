(function($) {

    var E = ENTER.OrderV3;

    var init = function() {

            var mapContainer = $('#yandex-map-container'),
                options = mapContainer.data('options');

            E.map = new ymaps.Map("yandex-map-container", {
                center: [options.latitude, options.longitude],
                zoom: options.zoom
            });

            E.mapOptions = options;
            E.$map = mapContainer.detach().css('display', 'block');

        };

    if ($('#yandex-map-container').length) {
        $LAB.script('//api-maps.yandex.ru/2.1/?lang=ru_RU').wait(function () {
            ymaps.ready(init);
        })
    }

})(jQuery);