/**
 * @module      enter.points.popup.view
 * @version     0.1
 *
 * @requires    enter.ui.BasePopup
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.points.popup.view',
        [
            'jQuery',
            'enter.ui.BasePopup',
            'jquery.ui',
            'Mustache'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BasePopup, jQueryUI, mustache ) {
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
                POINTS_WRAPPER: 'js-pointpopup-points-wrapper',
                PICK_POINT: 'js-pointpopup-pick-point',
                SEARCH: 'js-pointpopup-search',
                AUTOCOMPLETE: 'js-pointpopup-autocomplete',
                AUTOCOMPLETE_WRAPPER: 'js-pointpopup-autocomplete-wrapper',
                AUTOCOMPLETE_ITEM: 'js-pointpopup-autocomplete-item',
                FILTER_OVERLAY: 'need_class_here', /** @todo НУЖЕН ОВЕРЛЭЙ! */
                POINT_FILTER: 'js-point-filter',
                POINT_FILTER_PARAM: 'js-point-filter-param',
                POINT_OPENER: 'js-point-filter-opener',
                POINT_FILTER_OPEN: 'open',
                POINT_FILTER_ACITVE: 'active',
                POINTS_BALOON_CHOOSE_BTN: 'js-map-point-choose'
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
                BALOON: $('').html(),
                AUTOCOMPLETE: $('#js-pointpopup-autocomplete-template').html(),
            },

            $BODY = $('body'),

            index = 0;

        // Lazy load Yandex Maps
        modules.require(['ymaps'], function( ymaps ) {});

        provide(BasePopup.extend(/** @lends module:enter.points.popup.view~PointsPopup */{

             /**
             * @classdesc   Представление окна c выбором точки самовывоза
             * @memberOf    module:enter.points.popup.view~
             * @augments    module:enter.ui.BasePopup
             * @constructs  PointsPopup
             */
            initialize: function( options ) {
                var
                    self      = this,
                    uniqIndex = 'ORDER_POPUP_' + (index++);

                console.info('module:enter.points.popup.view~PointsPopup#initialize');

                this.render();

                this.mapContainer = this.$el.find('.' + CSS_CLASSES.MAP_CONTAINER);
                this.mapId        = uniqIndex;

                this.mapContainer.attr('id', uniqIndex);

                this.subViews = {
                    pointsWrapper: this.$el.find('.' + CSS_CLASSES.POINTS_WRAPPER),
                    autocomplete: this.$el.find('.' + CSS_CLASSES.AUTOCOMPLETE),
                    autocompleteWrapper: this.$el.find('.' + CSS_CLASSES.AUTOCOMPLETE_WRAPPER),
                    filterOverlay: this.$el.find('.' + CSS_CLASSES.FILTER_OVERLAY),
                    pointFilters: this.$el.find('.' + CSS_CLASSES.POINT_FILTER),
                    searchInput: this.$el.find('.' + CSS_CLASSES.SEARCH),
                    pointFilterParams: this.$el.find('.' + CSS_CLASSES.POINT_FILTER_PARAM),
                    dropdowns: this.$el.find('.' + CSS_CLASSES.POINT_OPENER)
                };

                this.subViews.searchInput.autocomplete({
                    source: this.searchAddress.bind(this),
                    minLength: 2,
                    select: this.selectAutocompleteItem.bind(this),
                    appendTo: this.subViews.autocomplete,
                    open: function() {
                        console.log('open');
                        self.subViews.filterOverlay.show();
                        self.subViews.autocomplete.show();
                    },
                    close: function() {}
                });

                // Setup events
                this.events['click .' + CSS_CLASSES.PICK_POINT]               = 'pickPoint';
                // this.events['keyup .' + CSS_CLASSES.SEARCH]                   = 'searchAddress';
                this.events['click .' + CSS_CLASSES.POINT_OPENER]             = 'openPointsFilter';
                // this.events['click .' + CSS_CLASSES.AUTOCOMPLETE_ITEM]        = 'selectAutocompleteItem';
                this.events['change .' + CSS_CLASSES.POINT_FILTER_PARAM]      = 'applyFilter';
                this.events['click .' + CSS_CLASSES.POINTS_BALOON_CHOOSE_BTN] = 'pickPoint';

                this.listenTo(this.collection, 'change:shown', this.changePoints);
                this.changePoints();
                this.show();

                // Apply events
                this.delegateEvents();
            },

            events: {},

             /**
             * Выбор адреса из автокомлита
             *
             * @method      selectAutocompleteItem
             * @memberOf    module:module:enter.points.popup.view~PointsPopup#
             *
             * @param       {Object}    event
             */
            selectAutocompleteItem: function( event, ui ) {
                 var
                    bounds = ui.item.bounds;

                this.map.setCenterAndZoom(bounds);
                this.subViews.autocomplete.hide();

                return false;
            },

            /**
             * Показать фильтр пунктов выдачи
             *
             * @method      openPointsFilter
             * @memberOf    module:module:enter.points.popup.view~PointsPopup#
             *
             * @param       {Object}    event
             */
            openPointsFilter: function( event ) {
                var
                    target   = $(event.currentTarget),
                    filter   = target.parent('.' + CSS_CLASSES.POINT_FILTER),
                    isActive = filter.hasClass(CSS_CLASSES.POINT_FILTER_OPEN);

                this.subViews.pointFilters.removeClass(CSS_CLASSES.POINT_FILTER_OPEN);

                if ( !isActive ) {
                    filter.addClass(CSS_CLASSES.POINT_FILTER_OPEN);
                    this.subViews.filterOverlay.show();
                } else {
                    filter.removeClass(CSS_CLASSES.POINT_FILTER_OPEN);
                    this.subViews.filterOverlay.hide();
                }

                return false;
            },

            /**
             * Закрыть фильтр пунктов выдачи
             *
             * @method      closePointsFilter
             * @memberOf    module:module:enter.points.popup.view~PointsPopup#
             */
            closePointsFilter: function() {
                this.subViews.pointFilters.removeClass(CSS_CLASSES.POINT_FILTER_OPEN);
                this.subViews.autocomplete.hide();
                this.subViews.autocompleteWrapper.empty();
                this.subViews.filterOverlay.hide();
            },

            /**
             * Обработчик изменения фильтра. Применение фильтрации точек
             *
             * @method      applyFilter
             * @memberOf    module:module:enter.points.popup.view~PointsPopup#
             */
            applyFilter: function() {
                var
                    inputs       = this.subViews.pointFilterParams,
                    pointFilters = this.subViews.pointFilters,
                    params       = {};

                pointFilters.removeClass(CSS_CLASSES.POINT_FILTER_ACITVE);

                inputs.each(function( key ) {
                    var
                        $self = $(this),
                        value = $self.val();
                        key   = $self.attr('name');

                    if ( typeof params[key] === 'undefined' ) {
                        params[key] = [];
                    }

                    if ( $self.prop('checked') == true ) {
                        $self.parents('.' + CSS_CLASSES.POINT_FILTER).addClass(CSS_CLASSES.POINT_FILTER_ACITVE);
                        params[key].push(value);
                    }
                });

                // dropdowns.each( function() {
                //     var
                //         $self = $(this);


                //     console.log('module:module:enter.points.popup.view~PointsPopup#applyFilter', $self.find('.' + CSS_CLASSES.POINT_FILTER_PARAM + ':checked').length);
                // });

                this.collection.filterMyPoints(params);
            },

            /**
             * Показать выбранные точки доставки
             *
             * @method      changePoints
             * @memberOf    module:module:enter.points.popup.view~PointsPopup#
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

            searchAddress: function ( request, response ) {
                var
                    self       = this,
                    address    = request.term,
                    myGeocoder = this.map.getGeoCoder(address);

                if ( address === '' ) {
                    this.resetPoints();
                }

                myGeocoder.then(
                    function ( res ) {
                        var
                            searchAutocompleteList = [],
                            html;

                        console.log(address);
                        console.log(res.geoObjects.getLength());

                        res.geoObjects.each(function( obj ) {
                            var
                                val = obj.properties.get('name') + ', ' + obj.properties.get('description');
                            searchAutocompleteList.push({
                                'value': val,
                                'label': val,
                                'bounds': obj.geometry.getBounds()
                            });
                        });

                        console.log(searchAutocompleteList);
                        response( searchAutocompleteList );

                        // html = mustache.render(TEMPLATES.AUTOCOMPLETE, {bounds: searchAutocompleteList});

                        // if ( res.geoObjects.getLength() > 0 ) {
                        //     self.subViews.filterOverlay.show();
                        //     self.subViews.autocomplete.show();
                        //     self.subViews.autocompleteWrapper.html(html);
                        // }

                        self.delegateEvents();
                    },
                    function ( err ) {
                        console.log(err);
                    }
                );
            },

            /**
             * Поиск точек, соответствующих введенному адресу, формирование автокомплита поиска
             *
             * @method      searchAddress
             * @memberOf    module:module:enter.points.popup.view~PointsPopup#
             */
            // searchAddress: (function () {
            //     var
            //         timeWindow = 500, // time in ms
            //         timeout,

            //         searchAddress = function ( args ) {
            //             var
            //                 self       = this,
            //                 address    = this.subViews.searchInput.val(),
            //                 myGeocoder = this.map.getGeoCoder(address);

            //             if ( address === '' ) {
            //                 this.resetPoints();
            //             }

            //             myGeocoder.then(
            //                 function ( res ) {
            //                     var
            //                         searchAutocompleteList = [],
            //                         html;

            //                     res.geoObjects.each(function( obj ) {
            //                         searchAutocompleteList.push({
            //                             'name': obj.properties.get('name') + ', ' + obj.properties.get('description'),
            //                             'bounds': JSON.stringify(obj.geometry.getBounds())
            //                         });
            //                     });

            //                     html = mustache.render(TEMPLATES.AUTOCOMPLETE, {bounds: searchAutocompleteList});

            //                     if ( res.geoObjects.getLength() > 0 ) {
            //                         self.subViews.filterOverlay.show();
            //                         self.subViews.autocomplete.show();
            //                         self.subViews.autocompleteWrapper.html(html);
            //                     }

            //                     self.delegateEvents();
            //                 },
            //                 function ( err ) {
            //                     console.log(err);
            //                 }
            //             );
            //         };

            //     return function() {
            //         var
            //             context = this,
            //             args    = arguments;

            //         clearTimeout(timeout);

            //         timeout = setTimeout(function() {
            //             searchAddress.apply(context, args);
            //         }, timeWindow);
            //     };
            // }()),

            /**
             * Сброс выбранных по адресу точек
             *
             * @method      changePoints
             * @memberOf    module:module:enter.points.popup.view~PointsPopup#
             */
            resetPoints: function() {
                this.subViews.searchInput.val(''),
                this.subViews.autocomplete.hide();
                this.subViews.autocompleteWrapper.empty();
                this.subViews.filterOverlay.hide();
                this.changePoints();
            },

            /**
             * Выбор точки
             *
             * @method      onClose
             * @memberOf    module:module:enter.points.popup.view~PointsPopup#
             */
            pickPoint: function( event ) {
                var
                    target = $(event.currentTarget);

                if ($(event.target).hasClass('js-pointpopup-pick-point-help')) {
                    return;
                }

                this.trigger('changePoint', {
                    id: target.attr('data-id'),
                    token: target.attr('data-token')
                });

                return false;
            },

            /**
             * Обработчик закрытия окна
             *
             * @method      onClose
             * @memberOf    module:module:enter.points.popup.view~PointsPopup#
             */
            onClose: function() {
                this.destroy();
            },

            /**
             * Отрисовка точек самовывоза
             *
             * @method      renderPoints
             * @memberOf    module:module:enter.points.popup.view~PointsPopup#
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

                this.delegateEvents();
            },

            render: function() {
                console.info('module:module:enter.points.popup.view~PointsPopup#render');
                console.log(this.collection.popupData);
                var
                    html = mustache.render(TEMPLATES.POPUP, this.collection.popupData);

                this.$el = $(html);
                $BODY.append(this.$el);
            }
        }));
    }
);
