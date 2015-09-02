/**
 * @module      enter.order.smartadress.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    kladr
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 * [About kladr]{@link https://github.com/garakh/kladrapi-jsclient}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.smartadress.view',
        [
            'jQuery',
            'underscore',
            'enter.BaseViewClass',
            'kladr',
            'lscache'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, _, BaseViewClass, jKladr, lscache ) {
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
                STREET_INPUT: 'js-smartadress-street',
                STREET_LABEL: 'js-smartadress-street-label',
                BUILD_INPUT: 'js-smartadress-build',
                APARTMENT_INPUT: 'js-smartadress-apartment'
            };

        provide(BaseViewClass.extend({
            url: 'http://kladr.enter.ru/api.php',

            /**
             * @classdesc   Представление умного поля автокомплита
             * @memberOf    module:enter.order.smartadress.view~
             * @augments    module:enter.BaseViewClass
             * @constructs  OrderSmartAdress
             */
            initialize: function( options ) {
                var
                    self         = this,
                    buildingData = lscache.get('smartadress_buildingData') || '',
                    streetData   = lscache.get('smartadress_streetData') || '',
                    apartment    = lscache.get('smartadress_apartment') || '',
                    validationConfig;

                this.regionData = $('#region-data').data('value');
                $.kladr.url     = this.url;
                this.orderView  = options.orderView;

                console.groupCollapsed('module:enter.order.smartadress.view~OrderSmartAdress#initialize');
                console.log(this.$el);
                console.log('buildingData', buildingData);
                console.log('streetData', streetData);
                console.log('apartment', apartment);
                console.groupEnd();

                this.subViews = {
                    street: this.orderView.$el.find('.' + CSS_CLASSES.STREET_INPUT),
                    streetLabel: this.orderView.$el.find('.' + CSS_CLASSES.STREET_LABEL),
                    building: this.orderView.$el.find('.' + CSS_CLASSES.BUILD_INPUT),
                    apartment: this.orderView.$el.find('.' + CSS_CLASSES.APARTMENT_INPUT)
                };

                this.validator = options.validator;

                this.subViews.street.kladr({
                    type: $.kladr.type.street,
                    parentType: $.kladr.type.city,
                    parentId: this.regionData.kladrId,
                    select: this.setStreet.bind(this)
                });

                if ( !_.isEmpty(streetData) ) {
                    this.setStreetValue(streetData);
                    this.validator._markFieldValid(this.subViews.street);
                }

                if ( !_.isEmpty(buildingData) ) {
                    this.setBuilingValue(buildingData);
                    this.validator._markFieldValid(this.subViews.building);
                }

                if ( apartment ) {
                    this.setApartmentValue(apartment);
                    this.validator._markFieldValid(this.subViews.apartment);
                }

                // Setup events
                this.subViews.street.on('blur', this.setStreet.bind(this));
                this.subViews.building.on('blur', this.setBuiling.bind(this));
                this.subViews.apartment.on('blur', this.setApartment.bind(this));

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.order.smartadress.view~OrderSmartAdress
             * @type        {Object}
             */
            events: {},

            setStreet: function( streetData ) {
                if ( _.isEmpty(streetData) ) {
                    return;
                }

                console.groupCollapsed('module:enter.order.smartadress.view~OrderSmartAdress#setStreet');

                if ( streetData instanceof $.Event ) {
                    console.warn('is $.Event');
                    console.log(this.subViews.street);
                    this.setStreetValue($(streetData.currentTarget).val());
                    return;
                } else {
                    this.setStreetValue(streetData.name);
                    this.subViews.building.kladr({
                        type: $.kladr.type.building,
                        parentType: $.kladr.type.street,
                        parentId: streetData.id,
                        select: this.setBuiling.bind(this)
                    });
                }

                // this.subViews.streetLabel.text(streetData.type);

                console.log(streetData);
                console.groupEnd();
            },

            setBuiling: function( buildingData ) {
                if ( _.isEmpty(buildingData) ) {
                    return;
                }

                console.groupCollapsed('module:enter.order.smartadress.view~OrderSmartAdress#setBuiling');

                if ( buildingData instanceof jQuery.Event ) {
                    console.warn('is $.Event');
                    this.setBuilingValue($(buildingData.currentTarget).val());
                } else {
                    console.log(buildingData);
                    this.setBuilingValue(buildingData.name);
                }

                console.groupEnd();

                return false;
            },

            setApartment: function( event ) {
                var
                    target = $(event.currentTarget),
                    val    = target.val();

                console.groupCollapsed('module:enter.order.smartadress.view~OrderSmartAdress#setApartment');
                console.log(val);
                console.groupEnd();

                this.setApartmentValue(val);
            },

            setStreetValue: function( val ) {
                console.info('setStreetValue', val);
                lscache.set('smartadress_streetData', val);
                this.subViews.street.val(val);
                // this.subViews.street.kladr('controller').setValueByName(val);
            },

            setBuilingValue: function( val ) {
                lscache.set('smartadress_buildingData', val);
                this.subViews.building.val(val);
                // this.subViews.building.kladr && this.subViews.building.kladr('controller').setValueByName(val);
            },

            setApartmentValue: function( val ) {
                this.subViews.apartment.val(val);
                lscache.set('smartadress_apartment', val);
            },

            sendChanges: function() {
                var
                    street    = this.streetData,
                    building  = this.buildingData,
                    apartment = this.subViews.apartment.val();

                console.log(this.streetData);
                console.log(this.buildingData);

                this.orderView.trigger('sendChanges', {
                    action: 'changeAddress',
                    data: {
                        street: street.name + ' ' + (street.typeShort == '' ? street.type : street.typeShort),
                        building: building.name,
                        apartment: this.apartment,
                        kladr_id: this.regionData.kladrId
                    }
                });
            }
        }));
    }
);
