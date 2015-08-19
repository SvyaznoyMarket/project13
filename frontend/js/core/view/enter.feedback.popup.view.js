/**
 * @module      enter.feedback.popup.view
 * @version     0.1
 *
 * @requires    enter.ui.BasePopup
 * @requires    FormValidator
 *
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.feedback.popup.view',
        [
            'enter.ui.BasePopup',
            'FormValidator',
            'jQuery'
        ],
        module
    );
}(
    this.modules,
    function( provide, BasePopup, FormValidator, $ ) {
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
                EMAIL: 'js-feedback-email',
                TOPIC: 'js-feedback-topic',
                TEXT: 'js-feedback-text',
                SUBMIT_BTN: 'js-feedback-submit',
                SUCCESS: 'complete'
            },

            /**
             * Используемые шаблоны
             *
             * @private
             * @constant
             * @type        {Object}
             */
            TEMPLATES = {};

        provide(BasePopup.extend({
            /**
             * @classdesc   Представление попапа обратной связи сайта
             * @memberOf    module:enter.feedback.popup.view~
             * @augments    module:enter.ui.BasePopup
             * @constructs  FeedbackPopupView
             */
            initialize: function( options ) {
                var
                    validationConfig;

                console.info('enter.feedback.popup.view~FeedbackPopupView#initialize');

                this.subViews = {
                    email: this.$el.find('.' + CSS_CLASSES.EMAIL),
                    subject: this.$el.find('.' + CSS_CLASSES.TOPIC),
                    message: this.$el.find('.' + CSS_CLASSES.TEXT),
                    submit: this.$el.find('.' + CSS_CLASSES.SUBMIT_BTN)
                };

                validationConfig = {
                    fields: [
                        {
                            fieldNode: this.subViews.email,
                            require: !!this.subViews.email.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.subViews.subject,
                            require: !!this.subViews.subject.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.subViews.message,
                            require: !!this.subViews.message.attr('data-required'),
                            validateOnChange: true
                        }
                    ]
                };

                this.validator = new FormValidator(validationConfig);

                // Setup events
                this.events['click .' + CSS_CLASSES.SUBMIT_BTN] = 'submit';

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.feedback.popup.view~FeedbackPopupView
             * @type        {Object}
             */
            events: {},

            loader: {
                show: function() {},

                hide: function() {}
            },

            /**
             * Валидация формы
             *
             * @method      submit
             * @memberOf    module:enter.feedback.popup.view~FeedbackPopupView#
             */
            submit: function() {
                this.validator.validate({
                    onInvalid: function( err ) {
                        console.warn('is invalid');
                        console.log(err);
                    },
                    onValid: this.sendForm.bind(this)
                });

                return false;
            },

            prepareData: function( data ) {
                var
                    i;

                if ( data.success ) {
                    this.$el.addClass(CSS_CLASSES.SUCCESS);
                    // Success
                } else {
                    for ( i = 0; i < data.errors.length; i++ ) {
                        if ( this.subViews.hasOwnProperty(data.errors[i].field) ) {
                            this.validator._markFieldError(this.subViews[data.errors[i].field], data.errors[i].message)
                        }
                    }
                }
            },

            /**
             * Отправка формы
             *
             * @method      sendForm
             * @memberOf    module:enter.feedback.popup.view~FeedbackPopupView#
             */
            sendForm: function() {
                var $form = this.$el.find('form');
                $.ajax($form.attr('action'), {
                    method: 'POST',
                    data: $form.serialize(),
                    loader: this.loader,
                    success: this.prepareData.bind(this)
                });
                return false;
            }
        }));
    }
);
