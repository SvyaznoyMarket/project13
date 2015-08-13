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
            'underscore'
        ],
        module
    );
}(
    this.modules,
    function( provide, ymaps, $, _ ) {
        'use strict';

        var
            /**
             * @classdesc   Конструктор карты с точками
             * @memberOf    module:createMap~
             * @constructs  CreateMap
             *
             * @param       {Object}    options
             * @param       {String}    options.nodeId          Идентификатор DOM элемента в котором необходимо отобразить карту
             * @param       {Array}     options.points          Массив точек которые необходимо отобразить на карте
             * @param       {String}    options.baloonTemplate  Шаблон всплывашки отображаемой при клике на точку на карте
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
                this.template = options.baloonTemplate;
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

                tmpPlacemark = new ymaps.Placemark(
                    // координаты точки
                    [
                        currPoint.latitude,
                        currPoint.longitude
                    ],

                    // данные для шаблона
                    _.extend({}, currPoint, {

                    }),

                    // оформление метки на карте
                    {
                        iconLayout: 'default#image',
                        iconImageHref: currPoint.icon || '/images/application/marker.png',
                        iconImageSize: [28, 39],
                        iconImageOffset: [-14, -39]
                    }
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
