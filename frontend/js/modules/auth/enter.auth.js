/**
 * @module      enter.auth
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.auth',
        [
            'jQuery',
            'enter.ui.BasePopup',
            'jquery.maskedinput'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BasePopup, jMaskedinput ) {
        'use strict';

        var
            CSS_CLASSES = {
                FORM: 'js-auth-form',
                STATE_WRAPPER: 'js-state-wrapper',
                SWITCH_STATE: 'js-auth-switch-state',
                USERNAME_INPUT: 'js-auth-username-input',
                USERNAME_LABEL: 'js-auth-username-label',
                PASSWORD_INPUT: 'js-auth-password-input',
                PASSWORD_LABEL: 'js-auth-password-label',
                INPUT_VALID: 'valid',
                INPUT_ERROR: 'error',
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
                console.info('module:enter.auth~AuthPopupView#initialize');

                $.mask.definitions['n'] = '[0-9]';

                this.form          = this.$el.find('.' + CSS_CLASSES.FORM);
                this.stateWrapper  = this.$el.find('.' + CSS_CLASSES.STATE_WRAPPER);
                this.usernameInput = this.$el.find('.' + CSS_CLASSES.USERNAME_INPUT);
                this.usernameLable = this.$el.find('.' + CSS_CLASSES.USERNAME_LABEL);
                this.passwordInput = this.$el.find('.' + CSS_CLASSES.PASSWORD_INPUT);
                this.passwordLable = this.$el.find('.' + CSS_CLASSES.PASSWORD_LABEL);

                $('.js-registerForm .js-phoneField').mask('+7 (nnn) nnn-nn-nn');

                // Setup events
                this.events['submit .' + CSS_CLASSES.FORM]                                               = 'formSubmit';
                this.events['click .' + CSS_CLASSES.SWITCH_STATE]                                        = 'switchState';
                this.events['keyup .' + CSS_CLASSES.USERNAME_INPUT + ', .' + CSS_CLASSES.PASSWORD_INPUT] = 'inputChange';

                // Init
                if (this.usernameInput.val().length > 0) this.usernameInput.addClass(CSS_CLASSES.INPUT_VALID);
                if (this.passwordInput.val().length > 0) this.passwordInput.addClass(CSS_CLASSES.INPUT_VALID);

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
                var $target = $(event.target);
                if ($target.val().length > 0) {
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

            authProcessing: function( data ) {
                if (data.form && data.form.error) {
                    data.form.error.forEach(function( val ) {
                        if ( val.message ) {
                            switch ( val.field ) {
                                case 'username':
                                    this.usernameInput.addClass('error');
                                    this.usernameLable.text(val.message);
                                    break;
                                case 'password':
                                    this.passwordInput.addClass('error');
                                    this.passwordLable.text(val.message);
                                    break;
                            }
                        }
                    });
                }
            },

            formSubmit: function() {
                var
                    data = this.form.serialize(),
                    url  = this.from.attr('action');

                this.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: this.authProcessing.bind(this)
                });

                return false;
            }
        }));
    }
);


