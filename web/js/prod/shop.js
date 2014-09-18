/**
 * @requires jQuery, tmpl, ymaps
 */
;(function($){

    var $container = $('.bMapShops').first(),
        cities = $container.find('.bMapShops__eMapCityList_city'),
        markers = $('#map-markers').data('content'),
        render = tmpl,
        enterPlacemark = {
            iconLayout: 'default#image',
            iconImageHref: '/images/map/marker-shop.png',
            iconImageSize: [28, 39],
            iconImageOffset: [-14, -39]
        },
        availableShops, map;

    if ($('#region_map-container').length == 1) {

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

        // Карта со всеми магазинами

        ymaps.ready(function () {

            var yandexClusterer = new ymaps.Clusterer({
                    hasBaloon: false,
                    hasHint: false,
                    minClusterSize: 3,
                    preset: 'islands#orangeClusterIcons'
                });

            $.each(availableShops, function (i, elem) {
                var point = new ymaps.Placemark([elem.latitude, elem.longitude], {
                    balloonContent: '<h3>' + elem.name + '</h3><span>' + elem.regtime + '</span><br><a href="' + elem.link + '" class="bGrayButton shopchoose">Перейти к магазину</a>'
                }, enterPlacemark);
                yandexClusterer.add(point);
            });

            map = new ymaps.Map("region_map-container", {
                center: [55.76, 37.64],
                zoom: 10
            });

            map.geoObjects.add(yandexClusterer);
            map.setBounds(yandexClusterer.getBounds());

            // событие открытия списка магазинов
            $container.on('cityOpen', function (e, regionId) {
                var shops = $.grep(availableShops, function (elem) {
                        return elem.region_id == regionId
                    }),
                    yandexGeoObjectCollection = new ymaps.GeoObjectCollection(),
                    objects = map.geoObjects;

                $.each(shops, function (i, elem) {
                    var point = new ymaps.Placemark([elem.latitude, elem.longitude], {
                        balloonContent: '<h3>' + elem.address + '</h3><span>' + elem.regtime + '</span><br><a href="' + elem.link + '" class="bGrayButton shopchoose">Перейти к магазину</a>'
                    }, enterPlacemark);
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
            $container.on('cityClose', function () {
                map.geoObjects.removeAll();
                map.geoObjects.add(yandexClusterer);
                map.setBounds(yandexClusterer.getBounds());
            });

            // клик по магазину в списке
            $container.on('shopClick', function (e, shopId) {
                var shop = $.grep(availableShops, function (elem) {
                    return elem.id == shopId
                })[0];
                map.setCenter([shop.latitude, shop.longitude], 16)
            })

        });
    }

    // карта для одного магазина
    if ($('#map-container').length == 1) {

        ymaps.ready(function () {

            var lat = $('input[name=shop\\[latitude\\]]').val(),
                lon = $('input[name=shop\\[longitude\\]]').val();

            map = new ymaps.Map("map-container", {
                center: [lat, lon],
                zoom: 16
            });

            map.geoObjects.add(new ymaps.Placemark([lat, lon], {}, enterPlacemark))

        });

        $('.bMap').on('click', '.bMap__eContainer', function(){
            var $container = $('#map-container'),
                isImage = $(this).hasClass('map-image-link'),
                isMap = $(this).hasClass('map-google-link');

            if (isImage) {
                $container.find('.ymaps-map').hide();
                if ($container.find('img').length == 0) {
                    $container.append($('<img />', { "src": $(this).find('img').data('value'), 'width': $container.width() }));
                } else {
                    $container.find('img').attr('src', $(this).find('img').data('value'));
                }
            }

            if (isMap) {
                $container.find('.ymaps-map').show();
            }
        });

    }

}(jQuery));
