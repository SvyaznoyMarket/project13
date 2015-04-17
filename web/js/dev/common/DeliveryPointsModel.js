/**
 *  Модель для точек самовывоза
 **/

;(function($, ko){

    ENTER.DeliveryPoints = function DeliveryPointsF (points) {

        var self = this,
            pointsBounds,
            map = ENTER.OrderV3 ? ENTER.OrderV3.map : ENTER.OrderV31Click.map;

        self.searchInput = ko.observable();
        self.searchAutocompleteList = ko.observableArray();
        self.searchAutocompleteListVisible = ko.observable(false);
        self.enableAutocompleteListVisible = function(){self.searchAutocompleteListVisible(true)};
        self.disableAutocompleteListVisible = function(){self.searchAutocompleteListVisible(false)};
        self.limitedSearchInput = ko.computed(self.searchInput).extend({throttle: 500});
        self.limitedSearchInput.subscribe(function(text) {

            var extendValue = 0.5,
                extendedBounds = [[pointsBounds[0][0] - extendValue, pointsBounds[0][1] - extendValue],[pointsBounds[1][0] + extendValue, pointsBounds[1][1] + extendValue]];

            if (typeof window.ymaps == 'undefined' || text.length == 0) return;

            self.searchAutocompleteList.removeAll();
            self.searchAutocompleteListVisible(false);

            ymaps.geocode(text, { boundedBy: extendedBounds, strictBounds: true }).then(
                function(res){
                    res.geoObjects.each(function(obj){
                        self.searchAutocompleteList.push({
                            'name' : obj.properties.get('name') + ', ' + obj.properties.get('description'),
                            'bounds' : obj.geometry.getBounds()
                        })
                    });
                    self.searchAutocompleteListVisible(true);
                },
                function(err){
                    console.warn('Geocode error', err)
                }
            )
        });
        self.clearSearchInput = function(){
            self.searchInput('');
            self.searchAutocompleteList.removeAll();
        };
        self.setMapCenter = function(val) {
            map.setCenter(val.bounds[0], 14);
            self.searchAutocompleteListVisible(false);
        };

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
                return self.choosenCosts()[0] == 0 ? 'Бесплатно' : self.choosenCosts()[0] + '&nbsp;<span class="rubl">p</span>';
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

        $.each(points, function(index, point) {
            self.availablePoints.push(point);
            if (typeof pointsBounds == 'undefined') pointsBounds = [[point.latitude, point.longitude], [point.latitude, point.longitude]];
            else {
                if (point.latitude < pointsBounds[0][0]) pointsBounds[0][0] = point.latitude;
                if (point.latitude > pointsBounds[1][0]) pointsBounds[1][0] = point.latitude;
                if (point.longitude < pointsBounds[0][1]) pointsBounds[0][1] = point.longitude;
                if (point.longitude > pointsBounds[1][1]) pointsBounds[1][1] = point.longitude;
            }
        });

        window.map = self;

        return self;

    };

    ENTER.Placemark = function(point, visible) {

        var visibility = typeof visible == 'undefined' ? true : visible,
            balloonContent = '<b>Адрес:</b> ' + point.address,
            placemark;

        balloonContent = '<table class="pickup-list">'+
                                '<tbody>'+
                                    '<tr class="pickup-item jsChangePoint clearfix" data-bind="attr: { "data-id": $data.id, "data-token": $data.token, "data-blockname": $data.orderToken }" data-id="68" data-token="shops" data-blockname="selftype134">'+
                                        '<td class="pickup-item__logo">'+
'                                            <img src="/images/deliv-logo/enter.png" class="pickup-item__img" data-bind="attr: { src: icon }">'+
'                                            <span class="pickup-item__name" data-bind="text: listName">Магазин Enter</span>'+
'                                        </td>'+
'                                        <td class="pickup-item__addr">'+
'                                            <!-- ko if: $.isArray(subway) -->'+
'                                            <div class="pickup-item__metro" style="background: rgb(255, 216, 3);" data-bind="style: { background: subway[0].line.color }">'+
'                                               <div class="pickup-item__metro-inn" data-bind="text: subway[0].name">Новогиреево</div>'+
'                                            </div>'+
'                                            <!-- /ko -->'+
'                                            <div class="pickup-item__addr-name" data-bind="text: address">Свободный пр-кт, д.&nbsp;33</div>'+
'                                            <div class="pickup-item__time" data-bind="text: regtime">'+point.regtime+'</div>'+
'                                        </td>'+
'                                        <td class="pickup-item__info pickup-item__info--nobtn">'+
'                                            <div class="pickup-item__date" data-bind="text: humanNearestDay">Послезавтра</div>'+
'                                            <div class="pickup-item__price"><span data-bind="text: cost == 0 ? "Бесплатно" : cost ">Бесплатно</span> <span class="rubl" data-bind="visible: cost != 0" style="display: none;">p</span></div>'+
'                                        '+
'                                        </td>'+
'                                    </tr>'+
'                                </tbody>'+
'                            </table>';

        if (!point.latitude || !point.longitude) throw 'Не указаны координаты точки';

        // if (point.regtime) balloonContent += '<br /> <b>Время работы:</b> ' + point.regtime;

        // кнопка "Выбрать магазин"
        balloonContent += $('<button />', {
                'text':'Купить',
                'class': 'btn-type btn-type--buy jsChangePoint',
                'style': 'display: block',
                'data-id': point.id,
                'data-token': point.token,
                'data-blockname': point.orderToken
            }
        )[0].outerHTML;

        placemark = new ymaps.Placemark([point.latitude, point.longitude], {
            // balloonContentHeader: point.name,
            balloonContentBody: balloonContent,
            hintContent: point.name,
            enterToken: point.token // Дополняем собственными свойствами
        }, {
            balloonMaxWidth: 428,
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
