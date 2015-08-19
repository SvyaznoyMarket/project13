/**
 * @module      enter.order.view
 * @version     0.1
 *
 * @requires    App
 * @requires    Backbone
 * @requires    enter.BaseViewClass
 * @requires    jquery.maskedinput
 * @requires    FormValidator
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.view',
        [
            'App',
            'Backbone',
            'enter.BaseViewClass',
            'jquery.maskedinput',
            'FormValidator'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, Backbone, BaseViewClass, jMaskedInput, FormValidator ) {
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
                valid: 'valid',
                error: 'error',
                phoneField: 'js-order-phone',
                emailField: 'js-order-email',
                nameField: 'js-order-name'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление оформления заказа
             * @memberOf    module:enter.order.view~
             * @augments    module:BaseViewClass
             * @constructs  OrderView
             */
            initialize: function( options ) {
                var
                    validationConfig;

                // маски для полей ввода
                $.mask.autoclear = false;

                this.$el.find('input').each(function( i,elem ) {
                    if ($(elem).data('mask')) $(elem).mask($(elem).data('mask'));
                    if ($(elem).val().length > 0) $(elem).addClass(CSS_CLASSES.valid);
                });

                this.subViews = {
                    email: this.$el.find('.' + CSS_CLASSES.emailField),
                    phone: this.$el.find('.' + CSS_CLASSES.phoneField),
                    name: this.$el.find('.' + CSS_CLASSES.nameField)
                };

                validationConfig = {
                    fields: [
                        {
                            fieldNode: this.subViews.email,
                            require: !!this.subViews.email.attr('data-required'),
                            validBy: 'isEmail',
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.subViews.phone,
                            validBy: 'isPhone',
                            require: !!this.subViews.phone.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.subViews.name,
                            require: !!this.subViews.name.attr('data-required'),
                            validateOnChange: true
                        }
                    ]
                };

                this.validator = new FormValidator(validationConfig);

                // this.events['keyup input']  = 'validateForm';
                this.events['submit form']  = 'formSubmit';

                // Apply events
                this.delegateEvents();
            },

            events: {},

            formSubmit: function(event) {
                var
                    valid = false;

                this.validator.validate({
                    onInvalid: function( err ) {
                        valid = false;
                    },
                    onValid: function() {
                        valid = true;
                    }
                });

                return valid;
            }

        }));
    }
);
