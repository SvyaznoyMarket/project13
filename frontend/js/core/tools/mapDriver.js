/**
 * @module      createMap
 * @version     0.1
 *
 * @requires    ymaps
 * @requires    jQuery
 * @requires    underscore
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'createMap',
        [
            'ymaps',
            'jQuery',
            'underscore',
            'Mustache'
        ],
        module
    );
}(
    this.modules,
    function( provide, ymaps, $, _, Mustache ) {
        'use strict';

        var
            BALLOON_TEMPLATE =
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

            /**
             * @classdesc   Конструктор карты с точками
             * @memberOf    module:createMap~
             * @constructs  CreateMap
             *
             * @param       {Object}    options
             * @param       {String}    options.nodeId          Идентификатор DOM элемента в котором необходимо отобразить карту
             * @param       {Array}     options.points          Массив точек которые необходимо отобразить на карте
             * @param       {String}    options.baloonTemplate  Mustache шаблон всплывашки отображаемой при клике на точку на карте
             * @param       {String}    options.clusterer       Необходима кластеризация
             */
            CreateMap = function CreateMap( options ) {
                // enforces new
                if ( !(this instanceof CreateMap) ) {
                    return new CreateMap(options);
                }
                // constructor body

                console.info('CreateMap');

                this.points   = options.points;
                this.template = options.baloonTemplate || BALLOON_TEMPLATE;
                this.center   = this.calcCenter();
                this.$nodeId  = $('#'+options.nodeId);

                if ( !this.$nodeId.length || this.$nodeId.width() === 0 || this.$nodeId.height() === 0 || this.$nodeId.is('visible') === false ) {
                    console.warn('Do you have a problem with init map?');

                    // console.log(this.$nodeId.width());
                    // console.log(this.$nodeId.height());
                    // console.log(this.$nodeId.is('visible'));
                }

                this.mapWS = new ymaps.Map(options.nodeId, {
                    controls: [],
                    center: [this.center.latitude, this.center.longitude],
                    zoom: 10
                });

                // this.mapWS.controls.add('zoomControl');
                // this.mapWS.controls.remove('searchControl');

                if ( options.clusterer ) {
                    this.clusterer = new ymaps.Clusterer({
                        minClusterSize: 3,
                        preset: 'islands#orangeClusterIcons'
                    });
                }

                // this.showMarkers();
            };


        /**
         * Расчет центра карты для исходного массива точек
         *
         * @memberOf    module:createMap~CreateMap
         * @method      calcCenter
         * @public
         */
        CreateMap.prototype.calcCenter = function() {
            var
                latitude  = 0,
                longitude = 0,
                l         = 0,
                i         = 0,
                mapCenter = {};

            for ( i = this.points.length - 1; i >= 0; i-- ) {
                if ( !this.points[i].latitude || !this.points[i].longitude ) {
                    continue;
                }

                latitude  += this.points[i].latitude * 1;
                longitude += this.points[i].longitude * 1;

                l++;
            }

            mapCenter = {
                latitude: latitude / l,
                longitude: longitude / l
            };

            return mapCenter;
        };

        CreateMap.prototype.setZoom = function() {

        };

        CreateMap.prototype.clasterize = function() {

        };

        CreateMap.prototype.setCenterAndZoom = function( bounds ) {
            var centerAndZoom = ymaps.util.bounds.getCenterAndZoom(
                    bounds,
                    this.mapWS.container.getSize(),
                    this.mapWS.options.get('projection'),
                    {
                        margin: 10
                    }
                );

            console.log(centerAndZoom);

            this.mapWS.setCenter(centerAndZoom.center, ( centerAndZoom.zoom > 17 ) ? 17 : centerAndZoom.zoom, {
                checkZoomRange: true,
                duration: 400
            });
        };

        CreateMap.prototype.getGeoCoder = function( address ) {

            return ymaps.geocode(address, {
                boundedBy: this.mapWS.geoObjects.getBounds(),
                strictBounds: true
            });
        };

        /**
         * Показать точки на карте
         *
         * @memberOf    module:createMap~CreateMap
         * @method      showMarkers
         * @public
         *
         * @param       {Array}     [points]    Точки для отображения на карте. Если не передан, используются this.points
         */
        CreateMap.prototype.showMarkers = function( points ) {
            var
                pointsCollection   = new ymaps.GeoObjectCollection(),
                pointContentLayout = ymaps.templateLayoutFactory.createClass(this.template), // layout for baloon
                currPoint          = null,
                tmpPlacemark       = null,
                self               = this,
                balloonContent     = null,
                i;

            if ( points ) {
                this.points = points;
            }

            this.mapWS.container.fitToViewport();
            this.mapWS.geoObjects.removeAll();

            for ( i = this.points.length - 1; i >= 0; i-- ) {
                currPoint = this.points[i];

                if ( !currPoint.latitude || !currPoint.longitude ) {
                    console.warn('Точка не имеет координат');

                    continue;
                }

                balloonContent = Mustache.render(this.template, currPoint);

                tmpPlacemark = new ymaps.Placemark(
                    // координаты точки
                    [
                        currPoint.latitude,
                        currPoint.longitude
                    ],

                    // данные для шаблона
                    _.extend({}, currPoint, {
                        balloonContentBody: balloonContent,
                        hintContent: currPoint.name,
                        enterToken: currPoint.token
                    }),

                    // оформление метки на карте
                    _.extend({}, {
                        iconLayout: 'default#image',
                        iconImageHref: currPoint.icon || '/images/application/marker.png',
                        iconImageSize: [28, 39],
                        iconImageOffset: [-14, -39]
                    }, currPoint.marker || {})
                );

                if ( currPoint.shown === false ) {
                    tmpPlacemark.options.set('visible', false)
                }

                if ( this.clusterer ) {
                    this.clusterer.add(tmpPlacemark);
                } else {
                    pointsCollection.add(tmpPlacemark);
                }
            }

            ymaps.layout.storage.add('my#superlayout', pointContentLayout);
            pointsCollection.options.set({
                balloonContentBodyLayout:'my#superlayout',
                balloonMaxWidth: 350
            });

            if ( this.points.length === 1 ) {
                this.mapWS.geoObjects.add(pointsCollection);
                this.mapWS.setCenter([this.points[0].latitude, this.points[0].longitude], 16, {
                    checkZoomRange: true,
                    duration: 400
                });
            } else if ( this.clusterer ) {
                this.mapWS.geoObjects.add(this.clusterer);
                this.mapWS.setBounds(this.clusterer.getBounds());
            } else {
                this.mapWS.geoObjects.add(pointsCollection);
                this.setCenterAndZoom(pointsCollection.getBounds());
            }

            this.pointsGeoQuery = ymaps.geoQuery(this.mapWS.geoObjects);
            this.pointsGeoQuery.searchInside(this.mapWS).addToMap(this.mapWS);
        };

        provide(CreateMap);
    }
);
