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
                SUBMIT_BTN: 'js-feedback-submit'
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
                    topic: this.$el.find('.' + CSS_CLASSES.TOPIC),
                    text: this.$el.find('.' + CSS_CLASSES.TEXT),
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
                            fieldNode: this.subViews.topic,
                            require: !!this.subViews.topic.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.subViews.text,
                            require: !!this.subViews.text.attr('data-required'),
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
                    success: function(data){
                        console.log('SEND', data)
                    }
                });
                return false;
            }
        }));
    }
);
