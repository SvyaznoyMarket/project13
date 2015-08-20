/**
 * @module      enter.application.popup
 * @version     0.1
 *
 * @requires    enter.ui.BasePopup
 * @requires    FormValidator
 * @requires    jquery.maskedinput
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.application.popup',
        [
            'jQuery',
            'enter.ui.BasePopup',
            'FormValidator',
            'jquery.maskedinput'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BasePopup, FormValidator, jMaskedinput ) {
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
                SUBMIT_BTN: 'js-application-submit',
                EMAIL_INPUT: 'js-application-email',
                PHONE_INPUT: 'js-application-phone',
                NAME_INPUT: 'js-application-name',
                ACCEPT_CHECKBOX: 'js-application-agree',
                LOADER: 'loader'
            };

        provide(BasePopup.extend(/** @lends module:enter.ui.BasePopup~ApplicationPopupView */{

            url: '/orders/slot/create',


             /**
             * @classdesc   Представление окна c оставлением заявки на покупку.
             * @memberOf    module:enter.application.popup~
             * @augments    module:enter.ui.BasePopup
             * @constructs  ApplicationPopupView
             *
             * [Пример карточки товара]{@link /product/furniture/kuhonniy-garnitur-dekor-220-h-130-sm-cherniy-metallik-krasniy-metallik-sahara-2050301025231}
             */
            initialize: function( options ) {
                console.info('module:enter.application.popup~ApplicationPopupView#initialize');

                var
                    validationConfig;

                this.subViews = {
                    phone: this.$el.find('.' + CSS_CLASSES.PHONE_INPUT),
                    email: this.$el.find('.' + CSS_CLASSES.EMAIL_INPUT),
                    name: this.$el.find('.' + CSS_CLASSES.NAME_INPUT),
                    submit: this.$el.find('.' + CSS_CLASSES.SUBMIT_BTN),
                    acceptCheckbox: this.$el.find('.' + CSS_CLASSES.ACCEPT_CHECKBOX)
                };

                $.mask.definitions['n'] = '[0-9]';
                this.subViews.phone.mask('+7 (nnn) nnn-nn-nn');

                validationConfig = {
                    fields: [
                        {
                            fieldNode: this.subViews.email,
                            validBy: 'isEmail',
                            require: !!this.subViews.email.attr('data-required'),
                            customErr: 'Некорректно введен e-mail',
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.subViews.phone,
                            validBy: 'isPhone',
                            require: !!this.subViews.phone.attr('data-required'),
                            customErr: 'Некорректно введен телефон',
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.subViews.name,
                            require: !!this.subViews.name.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.subViews.acceptCheckbox,
                            require: !!this.subViews.acceptCheckbox.attr('data-required'),
                            customErr: 'Необходимо согласие'
                        }
                    ]
                };

                this.validator = new FormValidator(validationConfig);

                // Setup events
                this.events['click .' + CSS_CLASSES.SUBMIT_BTN] = 'submit';

                // Apply events
                this.delegateEvents();
            },

            events: {},

            /**
             * Валидация формы и отправка заявки
             *
             * @method      submit
             * @memberOf    module:enter.application.popup~ApplicationPopupView#
             */
            submit: function() {
                if ( this.subViews.submit.hasClass(CSS_CLASSES.LOADER) ) {
                    return false;
                }

                this.validator.validate({
                    onInvalid: function( err ) {
                        console.warn('is invalid');
                        console.log(err);
                    },
                    onValid: this.sendApplication.bind(this)
                });

                return false;
            },

            /**
             * Обработчик успешного создания заявки
             *
             * @method      sendApplicationSuccess
             * @memberOf    module:enter.application.popup~ApplicationPopupView#
             */
            sendApplicationSuccess: function( data ) {
                console.info('module:enter.application.popup~ApplicationPopupView#sendApplicationSuccess');
                console.dir(data);
            },

            /**
             * Обработчик ошибочного создания заявки
             *
             * @method      sendApplicationError
             * @memberOf    module:enter.application.popup~ApplicationPopupView#
             */
            sendApplicationError: function( jqXHR, textStatus, errorThrown ) {
                console.warn('module:enter.application.popup~ApplicationPopupView#sendApplicationError');
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            },

            /**
             * Отправка заявки
             *
             * @method      sendApplication
             * @memberOf    module:enter.application.popup~ApplicationPopupView#
             */
            sendApplication: function() {
                console.info('module:enter.application.popup~ApplicationPopupView#sendApplication');

                this.subViews.submit.addClass(CSS_CLASSES.LOADER)

                this.ajax({
                    type: 'POST',
                    url: this.url,
                    data: {
                        productId: this.model.get('id'),
                        phone: this.subViews.phone.val(),
                        email: this.subViews.email.val(),
                        name: this.subViews.name.val(),
                        confirm: 1
                    },
                    success: this.sendApplicationSuccess.bind(this),
                    error: this.sendApplicationError.bind(this)
                });
            }
        }));
    }
);
