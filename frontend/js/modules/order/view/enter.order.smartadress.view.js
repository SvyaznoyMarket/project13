/**
 * @module      enter.order.smartadress.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    kladr
 * @requires    FormValidator
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.smartadress.view',
        [
            'jQuery',
            'underscore',
            'enter.BaseViewClass',
            'kladr',
            'FormValidator',
            'lscache'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, _, BaseViewClass, jKladr, FormValidator, lscache ) {
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
                    self = this,
                    validationConfig;

                this.regionData   = $('#region-data').data('value');
                $.kladr.url       = this.url;
                this.orderView    = options.orderView;
                this.blockName    = options.blockName;
                this.buildingData = lscache.get('smartadress_buildingData') || {};
                this.streetData   = lscache.get('smartadress_streetData') || {};
                this.apartment    = lscache.get('smartadress_apartment') || '';

                console.groupCollapsed('module:enter.order.smartadress.view~OrderSmartAdress#initialize');
                console.log('buildingData', this.buildingData);
                console.log('streetData', this.streetData);
                console.log('apartment', this.apartment);
                console.groupEnd();

                this.subViews = {
                    street: this.$el.find('.' + CSS_CLASSES.STREET_INPUT),
                    streetLabel: this.$el.find('.' + CSS_CLASSES.STREET_LABEL),
                    building: this.$el.find('.' + CSS_CLASSES.BUILD_INPUT),
                    apartment: this.$el.find('.' + CSS_CLASSES.APARTMENT_INPUT)
                };

                validationConfig = {
                    fields: [
                        {
                            fieldNode: this.subViews.street,
                            require: !!this.subViews.street.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.subViews.building,
                            require: !!this.subViews.building.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.subViews.apartment,
                            require: !!this.subViews.apartment.attr('data-required'),
                            validateOnChange: true
                        }
                    ]
                };

                this.validator = new FormValidator(validationConfig);

                if ( !_.isEmpty(this.streetData) ) {
                    this.setStreet(this.streetData);
                    this.validator._markFieldValid(this.subViews.street);
                }

                if ( !_.isEmpty(this.buildingData) ) {
                    this.setBuiling(this.buildingData);
                    this.validator._markFieldValid(this.subViews.building);
                }

                if ( this.apartment ) {
                    this.subViews.apartment.val(this.apartment);
                    this.validator._markFieldValid(this.subViews.apartment);
                }

                this.subViews.street.kladr({
                    type: $.kladr.type.street,
                    parentType: $.kladr.type.city,
                    parentId: this.regionData.kladrId,
                    select: this.setStreet.bind(this)
                });

                // Setup events
                this.events['blur .' + CSS_CLASSES.APARTMENT_INPUT] = 'setApartment';

                this.listenTo(this, 'sendChanges', this.sendChanges);

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
                lscache.set('smartadress_streetData', streetData);
                this.subViews.streetLabel.text(streetData.type);
                this.subViews.street.val(streetData.name);

                this.streetData = streetData;

                console.groupCollapsed('module:enter.order.smartadress.view~OrderSmartAdress#setStreet');
                console.dir(streetData);
                console.dir(lscache.get('smartadress_streetData'));
                console.groupEnd();

                this.subViews.building.kladr({
                    type: $.kladr.type.building,
                    parentType: $.kladr.type.street,
                    parentId: this.streetData.id,
                    select: this.setBuiling.bind(this)
                });
            },

            setBuiling: function( buildingData ) {
                this.subViews.building.val(buildingData.name);
                lscache.set('smartadress_buildingData', buildingData);

                console.groupCollapsed('module:enter.order.smartadress.view~OrderSmartAdress#setBuiling');
                console.dir(buildingData);
                console.dir(lscache.get('smartadress_buildingData'));
                console.groupEnd();

                if ( !_.isEqual(this.buildingData, buildingData) ) {
                    console.warn('module:enter.order.smartadress.view~OrderSmartAdress#setBuiling = buildings data isnt equal');
                    this.buildingData = buildingData;
                    this.sendChanges.call(this);
                }
            },

            setApartment: function( event ) {
                var
                    target = $(event.currentTarget),
                    val    = target.val();

                console.groupCollapsed('module:enter.order.smartadress.view~OrderSmartAdress#setApartment');
                console.log(val);
                console.groupEnd();

                if ( this.apartment !== val ) {
                    this.apartment = val;
                    lscache.set('smartadress_apartment', val);
                    this.sendChanges();
                }
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
