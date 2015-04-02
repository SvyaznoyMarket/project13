/**
 *  Модель для точек самовывоза
 **/

;(function($, ko){

    ENTER.DeliveryPoints = function DeliveryPointsF (points) {

        var self = this,
            pointsBounds;

        self.searchInput = ko.observable();
        self.limitedSearchInput = ko.computed(self.searchInput).extend({throttle: 500});
        self.limitedSearchInput.subscribe(function(text) {

            var map = ENTER.OrderV3 ? ENTER.OrderV3.map : ENTER.OrderV31Click.map, // TODO уже можно вынести
                extendValue = 1,
                extendedBounds = [[pointsBounds[0][0] - extendValue, pointsBounds[0][1] - extendValue],[pointsBounds[1][0] + extendValue, pointsBounds[1][1] + extendValue]];

            if (typeof window.ymaps == 'undefined' || text.length == 0) return;

            ymaps.geocode(text, { boundedBy: extendedBounds, strictBounds: true }).then(
                function(res){
                    var bounds = res.geoObjects.get(0).geometry.getBounds();
                    if (bounds) map.setCenter(bounds[0], 14);
                    //map.geoObjects.add(res.geoObjects.get(0));
                },
                function(err){
                    console.warn('Geocode error', err)
                }
            )
        });

        /* Полный список точек */
        self.availablePoints = ko.observableArray([]);
        /* Список выбранных типов точек */
        self.choosenTokens = ko.observableArray([]);
        /* Список выбранной цены доставки */
        self.choosenCosts = ko.observableArray([]);
        /* Список выбранных дат */
        self.choosenDates = ko.observableArray([]);
        /* Координаты границ карты */
        self.latitudeMin = ko.observable();
        self.latitudeMax = ko.observable();
        self.longitudeMin = ko.observable();
        self.longitudeMax = ko.observable();

        /* Текст для дропдауна с точками самовывоза */
        self.pointsText = ko.computed(function(){
            switch (self.choosenTokens().length) {
                case 0:
                    return 'Все точки';
                case 1:
                    return $.grep(self.availablePoints(), function(point){ return self.choosenTokens()[0] == point['token'] })[0]['dropdownName'];
                case 2: case 3: case 4:
                    return self.choosenTokens().length + ' точки';
                default:
                    return self.choosenTokens().length + ' точек';
            }
        });

        /* Текст для дропдауна со стоимостью */
        self.costsText = ko.computed(function(){
            if (self.choosenCosts().length == 1) {
                return self.choosenCosts()[0] == 0 ? 'Бесплатно' : self.choosenCosts()[0] + '&nbsp;<span class="rubl">р</span>';
            }
            return 'Стоимость';
        });

        /* Текст для дропдауна с датой */
        self.datesText = ko.computed(function(){
            return self.choosenDates().length == 1
                ? $.grep(self.availablePoints(), function(point){ return self.choosenDates()[0] == point['nearestDay'] })[0]['humanNearestDay']
                : 'Дата';
        });

        /* Список точек с учетом фильтрации */
        self.points = ko.computed(function(){

            var tokens = self.choosenTokens(),
                costs = self.choosenCosts(),
                dates = self.choosenDates(),
                arr;

            /* Фильтруем */
            arr = $.grep( self.availablePoints(), function(point) {
                /* Если не попадает в список выбранных токенов */
                if (tokens.length && tokens.indexOf(point.token) == -1) return false;
                /* Если не попадает в список выбранной цены доставки */
                if (costs.length && costs.indexOf(point.cost) == -1) return false;
                /* Если не попадает в список выбранных дат */
                if (dates.length && dates.indexOf(point.nearestDay) == -1) return false;
                /* В итоге проверяем на попадание в видимые границы карты */
                return self.isPointInBounds(point);
            });

            /* Сортируем */
            return arr.sort(function(a,b) {
                // сначала дата
                if (a.nearestDay != b.nearestDay) {
                    return a.nearestDay < b.nearestDay ? -1 : 1;
                }
                // потом цена
                if (a.cost != b.cost ) {
                    return a.cost - b.cost;
                }
                // TODO сначала Enter, потом Связной, потом по алфавиту
                if (a.blockName != b.blockName) {
                    return 0;
                }
                return 0;
            })
        });

        /**
         * Функция определения нахождения точки в границах карты
         */
        self.isPointInBounds = function(point){
            return self.latitudeMin() < point.latitude && self.longitudeMin() < point.longitude && self.latitudeMax() > point.latitude && self.longitudeMax() > point.longitude;
        };

        /**
         * Отображаем на карте только те точки, которые были выбраны в первом дропдауне
         */
        self.choosenTokens.subscribe(function(arr){
            var map = ENTER.OrderV3 ? ENTER.OrderV3.map : ENTER.OrderV31Click.map; // TODO уже можно вынести

            map.geoObjects.each(function(geoObject){
                if (arr.length == 0) {
                    geoObject.options.set('visible', true)
                } else {
                    geoObject.options.set('visible', $.inArray(geoObject.properties.get('enterToken'), arr) !== -1)
                }
            });
        });

        /* INIT */

        $.each(points, function(token, pointsArr) {
            $.each(pointsArr, function(index, point){
                self.availablePoints.push(point);
                if (typeof pointsBounds == 'undefined') pointsBounds = [[point.latitude, point.longitude], [point.latitude, point.longitude]];
                else {
                    if (point.latitude < pointsBounds[0][0]) pointsBounds[0][0] = point.latitude;
                    if (point.latitude > pointsBounds[1][0]) pointsBounds[1][0] = point.latitude;
                    if (point.longitude < pointsBounds[0][1]) pointsBounds[0][1] = point.longitude;
                    if (point.longitude > pointsBounds[1][1]) pointsBounds[1][1] = point.longitude;
                }
            });
        });

        //window.map = self;

        return self;

    };

    ENTER.Placemark = function(point, visible) {

        var visibility = typeof visible == 'undefined' ? true : visible,
            balloonContent = '<b>Адрес:</b> ' + point.address,
            placemark;

        if (!point.latitude || !point.longitude) throw 'Не указаны координаты точки';

        if (point.regtime) balloonContent += '<br /> <b>Время работы:</b> ' + point.regtime;

        // кнопка "Выбрать магазин"
        balloonContent += $('<button />', {
                'text':'Выбрать',
                'class': 'btnLightGrey bBtnLine btnView jsChangePoint',
                'style': 'display: block',
                'data-id': point.id,
                'data-token': point.token,
                'data-blockname': point.orderToken
            }
        )[0].outerHTML;

        placemark = new ymaps.Placemark([point.latitude, point.longitude], {
            balloonContentHeader: point.name,
            balloonContentBody: balloonContent,
            hintContent: point.name,
            enterToken: point.token // Дополняем собственными свойствами
        }, {
            balloonMaxWidth: 200,
            iconLayout: 'default#image',
            iconImageHref: point.marker.iconImageHref,
            iconImageSize: point.marker.iconImageSize,
            iconImageOffset: point.marker.iconImageOffset,
            visible: visibility
        });

        //placemark.balloon.set('maxWidth', 100);

        return placemark;
    };

})(jQuery, ko);
