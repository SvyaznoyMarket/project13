(function($) {

    var E = ENTER.OrderV3,
        $mapContainer = $('#yandex-map-container');

    var init = function() {

        var options = $mapContainer.data('options');

        E.map = new ymaps.Map("yandex-map-container", {
            center: [options.latitude, options.longitude],
            zoom: options.zoom,
            controls: ['zoomControl', 'fullscreenControl', 'geolocationControl', 'typeSelector']
        },{
            autoFitToViewport: 'always',
            suppressMapOpenBlock: true
        });

        E.map.controls.remove('searchControl');

        E.mapOptions = options;
        E.$map = $mapContainer;

        // храним тут модели, но неплохо бы и переделать
        E.koModels = [];

        E.map.events.add('boundschange', function (event) {
            var bounds;
            if (event.get('newBounds')) {
                bounds = event.get('target').getBounds();
                $.each(E.koModels, function(i,val){
                    val.latitudeMin(bounds[0][0]);
                    val.latitudeMax(bounds[1][0]);
                    val.longitudeMin(bounds[0][1]);
                    val.longitudeMax(bounds[1][1]);
                });
            }
        });

        E.map.geoObjects.events.add('click', function () {
            $body.trigger('trackGoogleEvent', ['pickup_ux', 'map_point', 'клик'])
        });

        E.map.geoObjects.events.add('hintopen', function () {
            $body.trigger('trackGoogleEvent', ['pickup_ux', 'map_point', 'наведение'])
        });

        $body.on('click', '.js-order-map .jsChangePoint', function(){
            $body.trigger('trackGoogleEvent', ['pickup_ux', 'map_point', 'выбор'])
        });

        $.each($('.jsNewPoints'), function(i,val) {
            var pointData = JSON.parse($(this).find('script.jsMapData').html()),
                points = new ENTER.DeliveryPoints(pointData.points, E.map);

            E.koModels.push(points);
            ko.applyBindings(points, val);

        })

    };

    if ($mapContainer.length) ymaps.ready(init);

})(jQuery);