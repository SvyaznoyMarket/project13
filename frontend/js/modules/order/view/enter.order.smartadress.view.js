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
            'enter.BaseViewClass',
            'kladr',
            'FormValidator'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, jKladr, FormValidator ) {
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
                HOUSE_INPUT: 'js-smartadress-house'
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

                console.info('module:enter.order.smartadress.view~OrderSmartAdress#initialize');
                console.info(this.$el);

                this.regionData = $('#region-data').data('value');
                $.kladr.url     = this.url;
                this.orderView  = options.orderView;
                this.blockName  = options.blockName;

                this.subViews = {
                    street: this.$el.find('.' + CSS_CLASSES.STREET_INPUT),
                    streetLabel: this.$el.find('.' + CSS_CLASSES.STREET_LABEL),
                    building: this.$el.find('.' + CSS_CLASSES.BUILD_INPUT),
                    house: this.$el.find('.' + CSS_CLASSES.HOUSE_INPUT)
                };

                this.subViews.street.kladr({
                    type: $.kladr.type.street,
                    parentType: $.kladr.type.city,
                    parentId: this.regionData.kladrId,
                    select: function( obj ) {
                        self.streetData = obj;
                        self.subViews.streetLabel.text(obj.type);

                        self.subViews.building.kladr({
                            type: $.kladr.type.building,
                            parentType: $.kladr.type.street,
                            parentId: self.streetData.id,
                            select: function( obj ) {
                                self.buildingData = obj;
                                self.sendChanges.call(self)
                            }
                        });
                    }
                });

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
                            fieldNode: this.subViews.house,
                            require: !!this.subViews.house.attr('data-required'),
                            validateOnChange: true
                        }
                    ]
                };

                this.validator = new FormValidator(validationConfig);

                // Setup events
                // this.events['click .' + CSS_CLASSES.]       = 'toggleCommentArea';

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

            sendChanges: function() {
                var
                    street    = this.streetData,
                    building  = this.buildingData,
                    apartment = this.subViews.house.val();

                console.log(this.streetData);
                console.log(this.buildingData);

                this.orderView.trigger('sendChanges', {
                    action: 'changeAddress',
                    data: {
                        street: street.name + ' ' + (street.typeShort == '' ? street.type : street.typeShort),
                        building: building.name,
                        apartment: apartment,
                        kladr_id: this.regionData.kladrId
                    }
                });
            }
        }));
    }
);
