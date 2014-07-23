(function() {
    var
        init = function() {
            $('.js-order-map').each(function(i, el) {
                var $el = $(el),
                    dataValue = $el.data('value')
                ;

                console.info('js-order-map', $el);

                var map = new ymaps.Map($el.attr('id'), {
                    center: [dataValue.latitude, dataValue.longitude],
                    zoom: dataValue.zoom
                });

                for (i = 0; i < dataValue.points.length; i++) {
                    var point = dataValue.points[i];

                    if (!point.latitude || !point.longitude) continue;

                    var placemark = new ymaps.Placemark([point.latitude, point.longitude], {
                        balloonContentHeader: point.name,
                        balloonContentBody: 'Адрес: ' + point.address + '',
                        hintContent: point.name
                    });

                    map.geoObjects.add(placemark);
                }
            });
        }
    ;


    $LAB.script('//api-maps.yandex.ru/2.1/?lang=ru_RU').wait(function() {
        ymaps.ready(init);
    });
})();