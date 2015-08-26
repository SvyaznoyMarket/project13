/**
 * @module      deliveryPageView
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    deliveryPage.points.collection
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'deliveryPageView',
        [
            'jQuery',
            'underscore',
            'enter.BaseViewClass',
            'deliveryPage.points.collection',
            'Mustache',
            'jquery.scrollTo'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, _, BaseViewClass, PointsCollection, mustache, jqueryScrollTo ) {
        'use strict';

        var
            POINTS = JSON.parse($('#points').html()),

            /**
             * Используемые CSS классы
             *
             * @private
             * @constant
             * @type        {Object}
             */
            CSS_CLASSES = {
                PARTNERS_FILTER: 'jsPartnerListItem',
                PARTNERS_FILTER_ACTIVE: 'active',
                SEARCH: 'js-pointpopup-search',
                MAP_CONTAINER: 'js-pointpopup-map-container',
                POINTS_WRAPPER: 'js-pointpopup-points-wrapper',
                POINTS_ITEM: 'jsPointListItem',
                AUTOCOMPLETE: 'js-pointpopup-autocomplete',
                AUTOCOMPLETE_WRAPPER: 'js-pointpopup-autocomplete-wrapper',
                AUTOCOMPLETE_ITEM: 'js-pointpopup-autocomplete-item',
                PICK_POINT: 'js-pointpopup-pick-point',
                PICK_POINT_ACTIVE: 'current'
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
                BALOON: $('').html(),
                AUTOCOMPLETE: $('#js-pointpopup-autocomplete-template').html(),
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление страницы доставки
             * @memberOf    module:deliveryPageView~
             * @augments    module:enter.BaseViewClass
             * @constructs  DeliveryPageView
             */
            initialize: function( options ) {
                console.info('deliveryPageView~DeliveryPageView#initialize');

                this.subViews = {
                    pointsWrapper: this.$el.find('.' + CSS_CLASSES.POINTS_WRAPPER),
                    autocomplete: this.$el.find('.' + CSS_CLASSES.AUTOCOMPLETE),
                    autocompleteWrapper: this.$el.find('.' + CSS_CLASSES.AUTOCOMPLETE_WRAPPER),
                    searchInput: this.$el.find('.' + CSS_CLASSES.SEARCH),
                    pointsItems: this.$el.find('.' + CSS_CLASSES.POINTS_ITEM),
                    partnerFilter: this.$el.find('.' + CSS_CLASSES.PARTNERS_FILTER),
                    pickPointItems: this.$el.find('.' + CSS_CLASSES.PICK_POINT)
                };

                // Setup events
                this.events['click .' + CSS_CLASSES.PARTNERS_FILTER]   = 'filterByPartner';
                this.events['keyup .' + CSS_CLASSES.SEARCH]            = 'searchAddress';
                this.events['click .' + CSS_CLASSES.AUTOCOMPLETE_ITEM] = 'selectAutocompleteItem';
                this.events['click .' + CSS_CLASSES.PICK_POINT]        = 'pointClick';

                this.collection = new PointsCollection();
                _.each(POINTS, this.collection.add.bind(this.collection));
                console.log(this.collection);

                this.listenTo(this.collection, 'change:shown', this.changePoints);

                // Get all points
                // this.readPoints();

                // Render points
                this.changePoints();

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:deliveryPageView~DeliveryPageView
             * @type        {Object}
             */
            events: {},

            /**
             * Выбор адреса из автокомлита
             *
             * @method      selectAutocompleteItem
             * @memberOf    module:deliveryPageView~DeliveryPageView#
             *
             * @param       {Object}    event
             */
            selectAutocompleteItem: function( event ) {
                var
                    target = $(event.currentTarget),
                    bounds = target.data('bounds');

                this.subViews.searchInput.val(target.text()),
                this.map && this.map.setCenterAndZoom(bounds);
                this.subViews.autocomplete.hide();
                this.subViews.autocompleteWrapper.empty();
            },

            /**
             * Фильтрация по партнеру
             *
             * @method      filterByPartner
             * @memberOf    module:deliveryPageView~DeliveryPageView#
             *
             * @param       {jQuery.Event}      event
             */
            filterByPartner: function( event ) {
                console.info('module:deliveryPageView~DeliveryPageView#filterByPartner');
                var
                    target   = $(event.currentTarget),
                    isActive = target.hasClass(CSS_CLASSES.PARTNERS_FILTER_ACTIVE),
                    params   = { partner: [] },
                    self     = this;

                if ( isActive ) {
                    target.removeClass(CSS_CLASSES.PARTNERS_FILTER_ACTIVE);
                } else {
                    target.addClass(CSS_CLASSES.PARTNERS_FILTER_ACTIVE);
                }

                this.subViews.partnerFilter.filter('.' + CSS_CLASSES.PARTNERS_FILTER_ACTIVE).each(function() {
                    var
                        el      = $(this),
                        partner = el.attr('data-value');

                    params.partner.push(partner);
                });

                console.log(params);

                this.collection.filterMyPoints(params);

                return false;
            },

            /**
             * Показать выбранные точки доставки
             *
             * @method      changePoints
             * @memberOf    module:deliveryPageView~DeliveryPageView#
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
                                    nodeId: 'jsDeliveryMap',
                                    points: points,
                                    baloonTemplate: TEMPLATES.BALOON
                                });

                                self.map.showMarkers(points);
                                self.map.mapWS.events.add('boundschange', self.renderPoints.bind(self));
                                self.map.mapWS.geoObjects.events.add(['click'], self.mapPointClick.bind(self));
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
             * Клик по балуну на карте
             *
             * @method      mapPointClick
             * @memberOf    module:deliveryPageView~DeliveryPageView#
             *
             * @param       {Ymaps.Event}       event
             */
            mapPointClick: function( event ) {
                var
                    pointProp = event.get('target').properties.getAll(),
                    uid       = pointProp.uid,
                    listItem = $('#point_uid_' + uid);

                this.subViews.pickPointItems.removeClass(CSS_CLASSES.PICK_POINT_ACTIVE);
                listItem.addClass(CSS_CLASSES.PICK_POINT_ACTIVE);

                this.subViews.pointsWrapper.scrollTo(listItem, 100, {offset: {top: -100}});
            },

            /**
             * Клик по точке из списка
             *
             * @method      pointClick
             * @memberOf    module:deliveryPageView~DeliveryPageView#
             *
             * @param       {jQuery.Event}       event
             */
            pointClick: function( event ) {
                var
                    target = $(event.currentTarget),
                    uid    = target.attr('data-uid'),
                    model  = this.collection.get(uid);

                this.map.mapWS.setCenter([model.get('latitude'), model.get('longitude')], 15);

                return false;
            },

            /**
             * Поиск точек, соответствующих введенному адресу, формирование автокомплита поиска
             *
             * @method      searchAddress
             * @memberOf    module:deliveryPageView~DeliveryPageView#
             */
            searchAddress: (function () {
                var
                    timeWindow = 500, // time in ms
                    timeout,

                    searchAddress = function () {
                        var
                            self       = this,
                            address    = this.subViews.searchInput.val(),
                            myGeocoder = this.map.getGeoCoder(address);

                        if ( address === '' ) {
                            this.resetPoints();
                        }

                        myGeocoder.then(
                            function ( res ) {
                                var
                                    searchAutocompleteList = [],
                                    html;

                                res.geoObjects.each(function( obj ) {
                                    searchAutocompleteList.push({
                                        'name': obj.properties.get('name') + ', ' + obj.properties.get('description'),
                                        'bounds': JSON.stringify(obj.geometry.getBounds())
                                    });
                                });

                                html = mustache.render(TEMPLATES.AUTOCOMPLETE, {bounds: searchAutocompleteList});

                                if ( res.geoObjects.getLength() > 0 ) {
                                    self.subViews.autocomplete.show();
                                    self.subViews.autocompleteWrapper.html(html);
                                }

                                self.delegateEvents();
                            },
                            function ( err ) {
                                console.log(err);
                            }
                        );
                    };

                return function() {
                    var
                        context = this,
                        args    = arguments;

                    clearTimeout(timeout);

                    timeout = setTimeout(function() {
                        searchAddress.apply(context, args);
                    }, timeWindow);
                };
            }()),

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
            }
        }));
    }
);
