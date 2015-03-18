/**
 *  Модель для точек самовывоза
 **/

;(function($, ko){

    var DeliveryPointsModel;

    DeliveryPointsModel = function DeliveryPointsF (points) {

        var self = this,
            pointsBounds;

        self.searchInput = ko.observable();
        self.limitedSearchInput = ko.computed(self.searchInput).extend({throttle: 500});
        self.limitedSearchInput.subscribe(function(text) {
            var map = ENTER.OrderV3.map,
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

        /* Текст для дропдауна */
        self.pointsText = ko.computed(function(){
            switch (self.choosenTokens().length) {
                case 0:
                    return 'Все точки';
                case 1:
                    return '1 точка'; // TODO значение точки e.g. "Магазин"
                default:
                    return self.choosenTokens().length + ' точки';
            }
        });

        self.datesText = ko.computed(function(){

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

        /* INIT */

        $.each(points, function(token, pointsArr) {
            $.each(pointsArr, function(index, point){
                self.availablePoints.push(point);
                if (typeof pointsBounds == 'undefined') pointsBounds = [[point.latitude, point.latitude], [point.longitude, point.longitude]];
                else {
                    if (point.latitude < pointsBounds[0][0]) pointsBounds[0][0] = point.latitude;
                    if (point.latitude > pointsBounds[1][0]) pointsBounds[1][0] = point.latitude;
                    if (point.longitude < pointsBounds[0][1]) pointsBounds[0][1] = point.longitude;
                    if (point.longitude > pointsBounds[1][1]) pointsBounds[1][1] = point.longitude;
                }
            });
            console.log('Point bounds', pointsBounds);
        });

        return self;

    };

    ENTER.DeliveryPoints = DeliveryPointsModel;


})(jQuery, ko);
