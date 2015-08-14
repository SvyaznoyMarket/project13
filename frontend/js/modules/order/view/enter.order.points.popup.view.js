/**
 * @module      enter.order.points.popup.view
 * @version     0.1
 *
 * @requires    enter.ui.BasePopup
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.points.popup.view',
        [
            'jQuery',
            'enter.ui.BasePopup',
            'Mustache'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BasePopup, mustache ) {
        'use strict';

        var
            /**
             * Используемые CSS классы
             *
             * @private
             * @constant
             * @type        {Object}
             */
            CSS_CLASSES = {
                MAP_CONTAINER: 'js-pointpopup-map-container',
                POINTS_WRAPPER: 'js-pointpopup-points-wrapper'
            },

            /**
             * Используемые шаблоны
             *
             * @private
             * @constant
             * @type        {Object}
             */
            TEMPLATES = {
                POINT: $('#js-point-template').html(),
                POPUP: $('#js-points-popup-template').html(),
                BALOON: $('').html()
            },

            $BODY = $('body'),

            index = 0;

        // Lazy load Yandex Maps
        modules.require(['ymaps'], function( ymaps ) {});

        provide(BasePopup.extend(/** @lends module:enter.ui.BasePopup~OrderPointsPopup */{

             /**
             * @classdesc   Представление окна c выбором точки самовывоза
             * @memberOf    module:enter.order.points.popup.view~
             * @augments    module:enter.ui.BasePopup
             * @constructs  OrderPointsPopup
             */
            initialize: function( options ) {
                var
                    uniqIndex = 'ORDER_POPUP_' + (index++);

                console.info('module:enter.order.points.popup.view~OrderPointsPopup#initialize');

                this.render();

                this.mapContainer = this.$el.find('.' + CSS_CLASSES.MAP_CONTAINER);
                this.mapId        = uniqIndex;
                this.orderView    = options.orderView;
                this.blockName    = options.blockName;

                this.mapContainer.attr('id', uniqIndex);

                this.subViews = {
                    pointsWrapper: this.$el.find('.' + CSS_CLASSES.POINTS_WRAPPER)
                };

                // Setup events
                // this.events['click .' + CSS_CLASSES.] = '';

                this.listenTo(this.collection, 'change:shown', this.changePoints);
                this.changePoints();
                this.show();

                // Apply events
                this.delegateEvents();
            },

            events: {},

            /**
             * Показать выбранные точки доставки
             *
             * @method      changePoints
             * @memberOf    module:module:enter.ui.BasePopup~OrderPointsPopup#
             */
            changePoints: (function () {
                var
                    timeWindow = 300, // time in ms
                    timeout,

                    changePoints = function ( args ) {
                         var
                            points = this.collection.toJSON(),
                            self   = this;

                        if ( !this.map ) {
                            console.log('load map ....');
                            modules.require(['createMap'], function( СreateMap ) {
                                self.map = СreateMap({
                                    nodeId: self.mapId,
                                    points: points,
                                    baloonTemplate: TEMPLATES.BALOON
                                });

                                self.map.showMarkers(points);
                                self.map.mapWS.events.add('boundschange', self.renderPoints.bind(self));
                                self.renderPoints();
                            });

                            return;
                        } else {
                            this.map.showMarkers(points);
                        }

                        this.renderPoints();
                    };

                return function() {
                    var
                        context = this,
                        args    = arguments;

                    clearTimeout(timeout);

                    timeout = setTimeout(function() {
                        changePoints.apply(context, args);
                    }, timeWindow);
                };
            }()),

            /**
             * Обработчик закрытия окна
             *
             * @method      onClose
             * @memberOf    module:module:enter.ui.BasePopup~OrderPointsPopup#
             */
            onClose: function() {
                this.destroy();
            },

            /**
             * Отрисовка точек самовывоза
             *
             * @method      renderPoints
             * @memberOf    module:module:enter.ui.BasePopup~OrderPointsPopup#
             */
            renderPoints: function() {
                var
                    self           = this,
                    visibleObjects = this.map.pointsGeoQuery.searchInside(this.map.mapWS).addToMap(this.map.mapWS),
                    html           = '',
                    sortable       = [];

                visibleObjects.each(function( plm ) {
                    sortable.push(plm.properties.get(0));
                });

                sortable.sort( function(firstId, secondId) {
                    return firstId.id - secondId.id;
                });

                html = mustache.render(TEMPLATES.POINT, {point: sortable});

                // Оставшиеся объекты будем удалять с карты.
                this.map.pointsGeoQuery.remove(visibleObjects).removeFromMap(this.map.mapWS);
                this.subViews.pointsWrapper.empty().html(html);
            },

            render: function() {
                var
                    html = mustache.render(TEMPLATES.POPUP);

                this.$el = $(html);
                $BODY.append(this.$el);
            }
        }));
    }
);
