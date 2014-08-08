/**
 * @requires jQuery, tmpl, ymaps
 */
;(function($){

    var $container = $('.bMapShops').first(),
        cities = $container.find('.bMapShops__eMapCityList_city'),
        markers = $('#map-markers').data('content'),
        render = tmpl,
        availableShops, map;

    if (!$.isArray(markers) || markers.length === 0) return;

    // Доступные магазины
    availableShops = $.grep(markers, function(elem) { return elem.is_reconstruction }, true);

    // Спрячем города без магазинов
    cities.each(function(){
        var id = $(this).attr('ref');
        if ($.grep(availableShops, function(elem) { return elem.region_id == id }).length === 0) $(this).remove();
    });

    // Клик по городу
    $container.on('click', '.bMapShops__eMapCityList_city', function(e) {

        var $this = $(this),
            $shopsContainer = $this.find('ul'),
            id = $this.attr('ref'),
            shops = $.grep(availableShops, function(elem) { return elem.region_id == id }),
            shopsHTML = '';

        if (shops.length > 0) {
            if ($this.hasClass('chosedCity')) {
                $this.removeClass('chosedCity');
                $shopsContainer.hide();
                cities.show();
                $container.trigger('cityClose')
            } else {
                cities.hide().removeClass('chosedCity');
                $this.addClass('chosedCity').show();
                if ($shopsContainer.find('li').length == 0)  { // если не было рендера
                    $.each(shops, function(i, val) { shopsHTML += render('shopInCity', val)});
                    $shopsContainer.html(shopsHTML)
                }
                $shopsContainer.show();
                $container.trigger('cityOpen', [id])
            }
        }

        e.stopPropagation();

    });

    $container.on('click', '.shopInCity', function(e){
        $container.trigger('shopClick', [$(this).attr('ref')]);
        e.stopPropagation();
    });

    // Карта
    ymaps.ready(function () {

        var yandexClusterer = new ymaps.Clusterer({
                hasBaloon: false,
                hasHint: false,
                minClusterSize: 3
            });

        $.each(availableShops, function(i, elem) {
            var point = new ymaps.Placemark([elem.latitude, elem.longitude]);
            yandexClusterer.add(point);
        });

        map = new ymaps.Map("region_map-container", {
            center: [55.76, 37.64],
            zoom: 10
        });

        map.geoObjects.add(yandexClusterer);
        map.setBounds(yandexClusterer.getBounds());

        // событие открытия списка магазинов
        $container.on('cityOpen', function(e, regionId) {
            var shops = $.grep(availableShops, function(elem) { return elem.region_id == regionId }),
                yandexGeoObjectCollection = new ymaps.GeoObjectCollection(),
                objects = map.geoObjects;

            $.each(shops, function(i, elem) {
                var point = new ymaps.Placemark([elem.latitude, elem.longitude]);
                yandexGeoObjectCollection.add(point);
            });

            objects.removeAll();
            objects.add(yandexGeoObjectCollection);

            if (objects.get(0).getLength() > 1) {
                map.setBounds(yandexGeoObjectCollection.getBounds())
            } else {
                console.log(objects.get(0).get(0).geometry.getCoordinates());
                map.setCenter(objects.get(0).get(0).geometry.getCoordinates(), 14)
            }

        });

        // событие закрытия списка магазинов
        $container.on('cityClose', function(){
            map.geoObjects.removeAll();
            map.geoObjects.add(yandexClusterer);
            map.setBounds(yandexClusterer.getBounds());
        });

        // клик по магазину в списке
        $container.on('shopClick', function(e, shopId) {
            var shop = $.grep(availableShops, function(elem) {return elem.id == shopId})[0];
            map.setCenter([shop.latitude, shop.longitude], 16)
        })

    });

}(jQuery));
