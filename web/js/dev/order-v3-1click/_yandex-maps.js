(function($) {
	ENTER.OrderV31Click.functions.initYandexMaps = function(){
		var E = ENTER.OrderV31Click,
			$mapContainer = $('#yandex-map-container');

		var init = function() {

            console.log('Init yandex maps');

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
                    $.each(ENTER.OrderV31Click.koModels, function(i,val){
                        val.latitudeMin(bounds[0][0]);
                        val.latitudeMax(bounds[1][0]);
                        val.longitudeMin(bounds[0][1]);
                        val.longitudeMax(bounds[1][1]);
                    });
                }
            });

		};

		if ($mapContainer.length) ymaps.ready(init);
	};
})(jQuery);