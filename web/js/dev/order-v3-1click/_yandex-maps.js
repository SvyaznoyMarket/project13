(function($) {

    var E = ENTER.OrderV3,
        $mapContainer = $('#yandex-map-container');

    var init = function() {

        var options = $mapContainer.data('options');

        E.map = new ymaps.Map("yandex-map-container", {
            center: [options.latitude, options.longitude],
            zoom: options.zoom
        },{
            autoFitToViewport: 'always'
        });

        E.map.controls.remove('searchControl')

        E.mapOptions = options;
        E.$map = $mapContainer;

        console.info(E.map);

    };

    if ($mapContainer.length) ymaps.ready(init);

})(jQuery);