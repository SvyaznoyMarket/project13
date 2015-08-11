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
                AUTH_STATE: 'js-auth-state',
                SWITCH_STATE: 'js-auth-switch-state',
                USERNAME_INPUT: 'js-auth-username-input',
                USERNAME_LABEL: 'js-auth-username-label',
                PASSWORD_INPUT: 'js-auth-password-input',
                PASSWORD_LABEL: 'js-auth-password-label',
                INPUT_VALID: 'valid',
                INPUT_ERROR: 'error'
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
                this.authState     = this.$el.find('.' + CSS_CLASSES.AUTH_STATE);
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

            switchState: function( event ) {
                var
                    target        = $(event.currentTarget),
                    removeClasses = 'login_auth login_reg login_hint login_success',
                    regClass      = 'login_reg',
                    authClass     = 'login_auth',
                    isReg         = this.authState.hasClass(regClass);

                this.authState.removeClass(removeClasses);

                if ( isReg ) {
                    this.authState.addClass(authClass);
                } else {
                    this.authState.addClass(regClass);
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


