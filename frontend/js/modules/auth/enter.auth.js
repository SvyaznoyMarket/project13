/**
 * @module      enter.auth
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.ui.BasePopup
 * @requires    FormValidator
 * @requires    jquery.maskedinput
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.auth',
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
            CSS_CLASSES = {
                STATE_WRAPPER: 'js-state-wrapper',
                SWITCH_STATE: 'js-auth-switch-state',

                LOGIN_USERNAME_INPUT: 'js-auth-username-input',
                LOGIN_USERNAME_LABEL: 'js-auth-username-label',
                LOGIN_PASSWORD_INPUT: 'js-auth-password-input',
                LOGIN_PASSWORD_LABEL: 'js-auth-password-label',

                REG_USERNAME_INPUT: 'js-reg-username-input',
                REG_EMAIL_INPUT: 'js-reg-email-input',
                REG_PHONE_INPUT: 'js-reg-phone-input',

                INPUT_VALID: 'valid',
                INPUT_ERROR: 'error',
                FORMS: {
                    LOGIN: 'js-auth-form',
                    REG: 'js-reg-form',
                    HINT: 'js-hint-form'
                },
                STATES: {
                    REG: 'login_reg',
                    LOGIN: 'login_auth',
                    HINT: 'login_hint',
                    SUCCESS: 'login_success'
                }
            };

        provide(BasePopup.extend({
            /**
             * @classdesc   Представление окна авторизации
             * @memberOf    module:enter.auth~
             * @augments    module:BaseViewClass
             * @constructs  AuthPopupView
             */
            initialize: function( options ) {
                var
                    validationConfig;

                console.info('module:enter.auth~AuthPopupView#initialize');

                $.mask.definitions['n'] = '[0-9]';

                // Login
                this.loginFrom = {
                    form: this.$el.find('.' + CSS_CLASSES.FORMS.LOGIN),
                    usernameInput: this.$el.find('.' + CSS_CLASSES.LOGIN_USERNAME_INPUT),
                    usernameLable: this.$el.find('.' + CSS_CLASSES.LOGIN_USERNAME_LABEL),
                    passwordInput: this.$el.find('.' + CSS_CLASSES.LOGIN_PASSWORD_INPUT),
                    passwordLable: this.$el.find('.' + CSS_CLASSES.LOGIN_PASSWORD_LABEL)
                };

                // Registration
                this.regForm = {
                    form: this.$el.find('.' + CSS_CLASSES.FORMS.REG),
                    usernameInput: this.$el.find('.' + CSS_CLASSES.REG_USERNAME_INPUT),
                    emailInput: this.$el.find('.' + CSS_CLASSES.REG_EMAIL_INPUT),
                    phoneInput: this.$el.find('.' + CSS_CLASSES.REG_PHONE_INPUT)
                };

                this.regForm.phoneInput.mask('+7 (nnn) nnn-nn-nn');

                // Hint
                this.hintFrom      = {
                    form: this.$el.find('.' + CSS_CLASSES.FORMS.HINT)
                };

                // Common
                this.stateWrapper  = this.$el.find('.' + CSS_CLASSES.STATE_WRAPPER);
                this.usernameInput = this.$el.find('.' + CSS_CLASSES.USERNAME_INPUT);
                this.usernameLable = this.$el.find('.' + CSS_CLASSES.USERNAME_LABEL);
                this.passwordInput = this.$el.find('.' + CSS_CLASSES.PASSWORD_INPUT);
                this.passwordLable = this.$el.find('.' + CSS_CLASSES.PASSWORD_LABEL);

                // Setup events
                this.events['submit .' + CSS_CLASSES.FORMS.LOGIN] = 'formLoginSubmit';
                this.events['submit .' + CSS_CLASSES.FORMS.REG]   = 'formRegSubmit';
                this.events['submit .' + CSS_CLASSES.FORMS.HINT]  = 'formHintSubmit';
                this.events['click .' + CSS_CLASSES.SWITCH_STATE] = 'switchState';

                // this.events['keyup .' + CSS_CLASSES.USERNAME_INPUT + ', .' + CSS_CLASSES.PASSWORD_INPUT] = 'inputChange';

                // Init
                // if (this.usernameInput.val().length > 0) this.usernameInput.addClass(CSS_CLASSES.INPUT_VALID);
                // if (this.passwordInput.val().length > 0) this.passwordInput.addClass(CSS_CLASSES.INPUT_VALID);

                // Validator
                validationConfig = {
                    fields: [
                        {
                            fieldNode: this.loginFrom.usernameInput,
                            require: !!this.loginFrom.usernameInput.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.loginFrom.passwordInput,
                            require: !!this.loginFrom.passwordInput.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.regForm.usernameInput,
                            require: !!this.regForm.usernameInput.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.regForm.emailInput,
                            validBy: 'isEmail',
                            require: !!this.regForm.emailInput.attr('data-required'),
                            validateOnChange: true
                        },
                        {
                            fieldNode: this.regForm.phoneInput,
                            validBy: 'isPhone',
                            require: !!this.regForm.phoneInput.attr('data-required'),
                            validateOnChange: true
                        }
                    ]
                };

                this.validator = new FormValidator(validationConfig);

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.auth~AuthPopupView
             * @type        {Object}
             */
            events: {},

            /**
             * Keyup на поле ввода логина и пароля
             * @param event
             */
            inputChange: function( event ) {
                var
                    $target = $(event.target);

                if ( $target.val().length > 0 ) {
                    $target.addClass(CSS_CLASSES.INPUT_VALID).removeClass(CSS_CLASSES.INPUT_ERROR)
                } else {
                    $target.addClass(CSS_CLASSES.INPUT_ERROR).removeClass(CSS_CLASSES.INPUT_VALID)
                }
            },

            resetState: function() {
                var
                    key;

                for ( key in CSS_CLASSES.STATES ) {
                    if ( CSS_CLASSES.STATES.hasOwnProperty(key) ) {
                        this.stateWrapper.removeClass(CSS_CLASSES.STATES[key])
                    }
                }
            },

            switchState: function( event ) {
                var
                    target    = $(event.currentTarget),
                    link      = target.find('a'),
                    isReg     = this.stateWrapper.hasClass(CSS_CLASSES.STATES.REG);

                this.resetState();

                if ( isReg ) {
                    link.text('Регистрация');
                    this.stateWrapper.addClass(CSS_CLASSES.STATES.LOGIN);
                } else {
                    this.stateWrapper.addClass(CSS_CLASSES.STATES.REG);
                    link.text('Авторизация');
                }

                return false;
            },

            /**
             * Обработка авторизации
             *
             * @method      authProcessing
             * @memberOf    module:enter.auth~AuthPopupView#
             *
             * @param       {Object}        data
             */
            authProcessing: function( data ) {
                if ( data.form && data.form.error ) {
                    data.form.error.forEach(function( val ) {
                        if ( val.message ) {
                            switch ( val.field ) {
                                case 'username':
                                    this.loginFrom.usernameInput.addClass('error');
                                    this.loginFrom.usernameLable.text(val.message);
                                    break;
                                case 'password':
                                    this.loginFrom.passwordInput.addClass('error');
                                    this.loginFrom.passwordLable.text(val.message);
                                    break;
                            }
                        }
                    });
                }
            },

            /**
             * Авторизация
             *
             * @method      formLoginSubmit
             * @memberOf    module:enter.auth~AuthPopupView#
             */
            formLoginSubmit: function() {
                var
                    data = this.loginFrom.form.serialize(),
                    url  = this.from.attr('action');

                this.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: this.authProcessing.bind(this)
                });

                return false;
            },

            /**
             * Регистрация
             *
             * @method      formRegSubmit
             * @memberOf    module:enter.auth~AuthPopupView#
             */
            formRegSubmit: function() {
                var
                    data = this.regForm.form.serialize();

                console.groupCollapsed('module:enter.auth~AuthPopupView#formRegSubmit');
                console.dir(data);
                console.groupEnd();

                return false;
            },

            /**
             * Восстановление пароля
             *
             * @method      formHintSubmit
             * @memberOf    module:enter.auth~AuthPopupView#
             */
            formHintSubmit: function() {
                var
                    data = this.hintFrom.form.serialize();

                console.groupCollapsed('module:enter.auth~AuthPopupView#formHintSubmit');
                console.dir(data);
                console.groupEnd();

                return false;
            }

        }));
    }
);


