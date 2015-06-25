/* Новая страница /delivery со всеми точками самовывоза */
+function($){

    var $mapContainer = $('#jsDeliveryMap'),
        points = $.parseJSON($('#pointsJSON').html()),
        partners = $.parseJSON($('#partnersJSON').html()),
        $partnersList = $('.jsPartnerListItem'),
        activePartners = [], map;

    if ($mapContainer.length == 0) return ;

    console.log('Список точек', points);
    console.log('Список партнеров', partners);

    // инициализация карты
    ymaps.ready(function(){

        var geoColl = new ymaps.GeoObjectCollection();

        map = new ymaps.Map("jsDeliveryMap", {
            center: [68, 68],
            zoom: 11,
            controls: ['geolocationControl', 'zoomControl']
        },{
            autoFitToViewport: 'always',
            suppressMapOpenBlock: true,
            suppressObsoleteBrowserNotifier: true
        });

        // Добавляем точки в геоколлекцию
        $.each(points, function(i,point){
            geoColl.add(new ymaps.Placemark(
                [point.latitude, point.longitude],
                {
                    eUid: point.uid,
                    ePartner: point.partner
                },
                {
                    iconLayout: 'default#image',
                    iconImageHref: point.icon,
                    iconImageSize: [23,30]
                }
            ));
        });

        geoColl.events.add('click', function(e){
            var placemark = e.get('target'),
                uid = placemark.properties.get('eUid'),
                $listItem = $('#uid-'+uid);

            // выделяем в списке
            $listItem.addClass('current');
        });

        map.geoObjects.add(geoColl); // добавляем геоколлекцию на карту
        map.setBounds(geoColl.getBounds()); // устанавливаем границы карты

    });

    // Переключение партнеров
    $partnersList.on('click', function(){

        var activeClass = 'active';

        $(this).toggleClass(activeClass);

        activePartners = $.map($partnersList.filter(function(){return $(this).hasClass(activeClass)}),
            function(obj){ return $(obj).data('value')});

        if (typeof map != 'undefined') {
            map.geoObjects.each(function(GeoObjColl){
                GeoObjColl.each(function(point){
                    point.options.set('visible', activePartners.length == 0 ? true : $.inArray(point.properties.get('ePartner'), activePartners) !== -1)
                })
            });
        }

    });


}(jQuery);