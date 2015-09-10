/**
 *  Модель для точек самовывоза
 **/

;(function($, ko){

    var balloonTemplate =
        '<table class="pick-point-list"><tbody><tr class="pick-point-item clearfix" ><td class="pick-point-item__logo">'+
        '<img src="{{ icon }}" class="pick-point-item__img" >'+
        '<span class="pick-point-item__name">{{ listName }}</span>'+
        '</td><td class="pick-point-item__addr">'+
        '{{# subway }}' +
        '<div class="pick-point-item__metro" style="background: {{ subway.line.color }};">'+
        '<div class="pick-point-item__metro-inn">{{ subway.name }}</div></div>'+
        '{{/ subway }}'+
        '<div class="pick-point-item__addr-name">{{ address }}</div>'+
        '<div class="pick-point-item__time">{{ regtime }}</div></td>'+
        '<td class="pick-point-item__info pick-point-item__info--nobtn">'+
        '<div class="pick-point-item__date" data-bind="text: humanNearestDay">{{ humanNearestDay }}</div>'+
        '<div class="pick-point-item__price"><span >{{ humanCost }}</span> {{# showRubles }}<span class="rubl">p</span></div>{{/ showRubles }}'+
        '</td></tr></tbody></table>',
        productUi = $('#product-info').data('ui');

    ENTER.DeliveryPoints = function DeliveryPointsF (points, mapParam) {

        var self = this,
            pointsBounds,
            searchAutocompleteListClicked = false,
            map = ENTER.OrderV3 ? ENTER.OrderV3.map : ENTER.OrderV31Click.map,
            $body = $(document.body);

        if (mapParam) map = mapParam;

        self.searchInput = ko.observable();
        self.searchAutocompleteList = ko.observableArray();
        self.searchAutocompleteListVisible = ko.observable(false);
        self.searchAutocompleteListClicked = false; //
        self.enableAutocompleteListVisible = function(){self.searchAutocompleteListVisible(true)};
        self.disableAutocompleteListVisible = function(){self.searchAutocompleteListVisible(false)};
        self.limitedSearchInput = ko.computed(self.searchInput).extend({throttle: 500});

        self.limitedSearchInput.subscribe(function(text) {

            var extendValue = 0.5,
                extendedBounds = [[pointsBounds[0][0] - extendValue, pointsBounds[0][1] - extendValue],[pointsBounds[1][0] + extendValue, pointsBounds[1][1] + extendValue]];

            if (typeof window.ymaps == 'undefined' || text.length == 0) return;

            self.searchAutocompleteList.removeAll();
            self.searchAutocompleteListVisible(false);

            if (searchAutocompleteListClicked) {
                searchAutocompleteListClicked = false;
                return;
            }

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

        self.autocompleteNavigation = function(data, e){
            var keycode = e.which,
                $elements = $('.jsDeliverySuggestLi'),
                $list = $('.deliv-suggest__list'),
                activeClass = 'deliv-suggest__i--active',
                index = $elements.index($elements.filter('.'+activeClass));

            $elements.removeClass(activeClass);

            switch (keycode) {
                case 13: // Enter key
                    if (index > -1) {
                        self.autocompleteItemClick($elements.eq(index).data('element'));
                        return false;
                    }
                    break;
                case 38: // up key
                    if (index == -1) index = self.searchAutocompleteList.length;
                    $elements.eq(index - 1).addClass(activeClass);
                    $list.scrollTo('.' + activeClass);
                    break;
                case 40: // down key
                    $elements.eq(index + 1).addClass(activeClass);
                    $list.scrollTo('.' + activeClass);
                    break
            }

            return true;
        };

        self.clearSearchInput = function(){
            self.searchInput('');
            self.searchAutocompleteList.removeAll();
            map.setBounds(map.geoObjects.getBounds());
        };

        self.autocompleteItemClick = function(val) {
            $body.trigger('trackGoogleEvent', ['pickup_ux', 'search', val.name]);
            map.setCenter(val.bounds[0], 14);
            searchAutocompleteListClicked = true;
            self.searchInput(val.name);
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
            return self.choosenDates().length == 1 ? self.choosenDates()[0] : 'Дата';
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
                if (dates.length && dates.indexOf(point.humanNearestDay) == -1) return false;
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
            map.geoObjects.each(function(geoObject){
                if (arr.length == 0) {
                    geoObject.options.set('visible', true)
                } else {
                    geoObject.options.set('visible', $.inArray(geoObject.properties.get('enterToken'), arr) !== -1)
                }
            });
        });


        self.setMapCenter = function (point) {
            console.log(point);
            var bounds = $.isArray(point.bounds) && point.bounds.length == 2 ? point.bounds[0] : [point.latitude, point.longitude];
            map.setCenter(bounds, 14)
        };

        /* INIT */

        console.log('Init DeliveryPointsModel with ', {points: points, mapParam: mapParam});

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

    ENTER.Placemark = function(point, visible, buyButtonClass) {

        var visibility = typeof visible == 'undefined' ? true : visible,
            balloonContent, placemark;

        if (!buyButtonClass) buyButtonClass = 'jsChangePoint';

        // Для шаблона
        if (point.cost == 0) {
            point.humanCost = 'Бесплатно';
            point.showRubles = false;
        } else {
            point.humanCost = point.cost;
            point.showRubles = true;
        }

        if (!point.latitude || !point.longitude) throw 'Не указаны координаты точки';

        balloonContent = Mustache.render(balloonTemplate, point);

        // кнопка "Выбрать магазин"
        // показываем только на странице продукта
        if (point.showBaloonBuyButton) balloonContent += $('<button />', {
                'text':'Выбрать',
                'class': 'btn-type btn-type--buy ' + buyButtonClass,
                'style': 'display: block',
                'data-id': point.id,
                'data-token': point.token,
                'data-blockname': point.orderToken,
                'data-product-ui': productUi
            }
        )[0].outerHTML;

        placemark = new ymaps.Placemark([point.latitude, point.longitude], {
            // balloonContentHeader: point.name,
            balloonContentBody: balloonContent,
            hintContent: point.name,
            enterToken: point.token // Дополняем собственными свойствами
        }, {
            balloonMaxWidth: 390,
            iconLayout: 'default#image',
            iconImageHref: point.marker.iconImageHref,
            iconImageSize: point.marker.iconImageSize,
            iconImageOffset: point.marker.iconImageOffset,
            visible: visibility,
            zIndex: point.token == 'shops' ? 1000 : 0
        });

        // Максимальная ширина балуна
        //placemark.balloon.set('maxWidth', 100);

        return placemark;
    };

})(jQuery, ko);
