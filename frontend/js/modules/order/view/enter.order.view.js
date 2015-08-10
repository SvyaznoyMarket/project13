/**
 * @module      enter.order.view
 * @version     0.1
 *
 * @requires    enter.BaseViewClass
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
            'jquery.maskedinput'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, Backbone, BaseViewClass ) {
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

                // маски для полей ввода
                $.mask.autoclear = false;
                this.$el.find('input').each(function(i,elem){
                    if ($(elem).data('mask')) $(elem).mask($(elem).data('mask'));
                    if ($(elem).val().length > 0) $(elem).addClass(CSS_CLASSES.valid);
                });

                this.events['keyup input']  = 'validateForm';
                this.events['submit form']  = 'formSubmit';

            },

            events: {},

            validateForm: function(event){

                var $phoneField = $('.' + CSS_CLASSES.phoneField),
                    $emailField = $('.' + CSS_CLASSES.emailField),
                    elements = event ? [ $(event.target) ] : [$emailField, $phoneField],
                    valid = true, phone;

                function validateEmail(email) {
                    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
                    return re.test(email);
                }

                // Проверка длины поля
                $.each(elements,function (i,v){
                    if (v.val().length > 0) {
                        v.addClass(CSS_CLASSES.valid).removeClass(CSS_CLASSES.error)
                    } else {
                        valid = false;
                        v.addClass(CSS_CLASSES.error).removeClass(CSS_CLASSES.valid)
                    }
                });

                if (event && $(event.target).hasClass(CSS_CLASSES.emailField)) {
                    if (!validateEmail($emailField.val())) {
                        valid = false;
                        $emailField.addClass(CSS_CLASSES.error).removeClass(CSS_CLASSES.valid)
                    } else {
                        $emailField.addClass(CSS_CLASSES.valid).removeClass(CSS_CLASSES.error)
                    }
                }

                if (!event) {
                    phone = $phoneField.val().replace(/[^0-9]/g, '');
                    if (phone.length !== 11) {
                        valid = false;
                        $phoneField.addClass(CSS_CLASSES.error).removeClass(CSS_CLASSES.valid)
                    } else {
                        $phoneField.addClass(CSS_CLASSES.valid).removeClass(CSS_CLASSES.error)
                    }

                    if (!validateEmail($emailField.val())) {
                        valid = false;
                        $emailField.addClass(CSS_CLASSES.error).removeClass(CSS_CLASSES.valid)
                    } else {
                        $emailField.addClass(CSS_CLASSES.valid).removeClass(CSS_CLASSES.error)
                    }
                }

                return valid;
            },

            formSubmit: function(event) {
                if (!this.validateForm()) {
                    event.preventDefault();
                    return false;
                } else {
                    return true;
                }
            }

        }));
    }
);
