/* Новая страница /delivery со всеми точками самовывоза */
+function($){

    var $mapContainer = $('#jsDeliveryMap'),
        map;

    if ($mapContainer.length == 0) return ;

    // инициализация карты
    ymaps.ready(function(){
        map = new ymaps.Map("jsDeliveryMap", {
            center: [68, 68],
            zoom: 11,
            controls: ['geolocationControl', 'zoomControl']
        },{
            autoFitToViewport: 'always'
        });

    });

}(jQuery);