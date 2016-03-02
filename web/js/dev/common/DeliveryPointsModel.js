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
        '<div class="{{# humanNearestDay }}pick-point-item__date{{/ humanNearestDay }}" data-bind="text: humanNearestDay">{{ humanNearestDay }}</div>'+
        '<div class="pick-point-item__price"><span >{{ humanCost }}</span> {{# showRubles }}<span class="rubl">p</span></div>{{/ showRubles }}'+
        '</td></tr></tbody></table>',
        productUi = $('#product-info').data('ui');

    ENTER.DeliveryPoints = function DeliveryPointsF (points, mapParam, enableFitsAllProducts, buyButtonClass) {

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
            console.warn('autocompleteNavigation', e);
            var keycode = e.which,
                $elements = $('.pick-point-suggest__i'),
                $list = $('.pick-point-suggest__list'),
                activeClass = 'pick-point-suggest__i-act',
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
        self.fitsAllProducts = ko.observable(Boolean(enableFitsAllProducts));
        /* Список выбранной цены доставки */
        self.choosenCosts = ko.observableArray([]);
        /* Список выбранных дат */
        self.choosenDates = ko.observableArray([]);
        /* Координаты границ карты */
        self.latitudeMin = ko.observable();
        self.latitudeMax = ko.observable();
        self.longitudeMin = ko.observable();
        self.longitudeMax = ko.observable();

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

        /**
         * Функция определения нахождения точки в границах карты
         */
        self.isPointInBounds = function(point){
            return self.latitudeMin() < point.latitude && self.longitudeMin() < point.longitude && self.latitudeMax() > point.latitude && self.longitudeMax() > point.longitude;
        };

        self.setMapCenter = function (point) {
            var bounds = $.isArray(point.bounds) && point.bounds.length == 2 ? point.bounds[0] : [point.latitude, point.longitude];
            map.setCenter(bounds, 14)
        };

        /* INIT */

        console.log('Init DeliveryPointsModel with ', {points: points, mapParam: mapParam});

        $.each(points, function(index, point) {
            try {
                point.geoObject = new ENTER.Placemark(point, buyButtonClass);
            } catch (e) {}

            self.availablePoints.push(point);
            if (typeof pointsBounds == 'undefined') pointsBounds = [[point.latitude, point.longitude], [point.latitude, point.longitude]];
            else {
                if (point.latitude < pointsBounds[0][0]) pointsBounds[0][0] = point.latitude;
                if (point.latitude > pointsBounds[1][0]) pointsBounds[1][0] = point.latitude;
                if (point.longitude < pointsBounds[0][1]) pointsBounds[0][1] = point.longitude;
                if (point.longitude > pointsBounds[1][1]) pointsBounds[1][1] = point.longitude;
            }
        });

        // Список точек с учетом фильтрации
        // Размещаем после self.availablePoints.push, чтобы избежать множественных вызовов self.points
        self.points = ko.computed(function(){
            var tokens = self.choosenTokens(),
                costs = self.choosenCosts(),
                dates = self.choosenDates(),
                fitsAllProducts = self.fitsAllProducts(),
                arr;

            /* Фильтруем */
            arr = $.grep( self.availablePoints(), function(point) {
                var result = true;
                if (tokens.length && tokens.indexOf(point.token) == -1) {
                    /* Если не попадает в список выбранных токенов */
                    result = false;
                } else if (costs.length && costs.indexOf(point.cost) == -1) {
                    /* Если не попадает в список выбранной цены доставки */
                    result = false;
                } else if (dates.length && dates.indexOf(point.humanNearestDay) == -1) {
                    /* Если не попадает в список выбранных дат */
                    result = false;
                } else if (fitsAllProducts && !point.fitsAllProducts) {
                    result = false;
                } else {
                    /* В итоге проверяем на попадание в видимые границы карты */
                    result = self.isPointInBounds(point);
                }

                if (point.geoObject) {
                    point.geoObject.options.set('visible', result);
                }

                return result;
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

        window.map = self;

        return self;

    };

    ENTER.Placemark = function(point, buyButtonClass) {

        var balloonContent, placemark;

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
            enterToken: point.token, // Дополняем собственными свойствами
            fitsAllProducts: point.fitsAllProducts // Дополняем собственными свойствами
        }, {
            balloonMaxWidth: 390,
            iconLayout: 'default#image',
            iconImageHref: point.marker.iconImageHref,
            iconImageSize: point.marker.iconImageSize,
            iconImageOffset: point.marker.iconImageOffset,
            visible: true,
            zIndex: point.token == 'shops' ? 1000 : 0
        });

        // Максимальная ширина балуна
        //placemark.balloon.set('maxWidth', 100);

        return placemark;
    };

})(jQuery, ko);
