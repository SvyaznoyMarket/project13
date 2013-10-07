;(function( ENTER ) {
    var constructors = ENTER.constructors,
        signinUserNameField = $('.jsSigninUsername'),
        signinPasswordField = $('.jsSigninPassword'),
        registerFirstNameField = $('.jsRegisterFirstName'),
        registerMailPhoneField = $('.jsRegisterUsername'),
        forgotPwdLoginField = $('.jsForgotPwdLogin'),

        /**
         * Конфигурация валидатора для формы логина
         * @type {Object}
         */
            signinValidationConfig = {
            fields: [
                {
                    fieldNode: signinUserNameField,
                    require: true,
                    customErr: 'Не указан логин',
                    validateOnChange: true
                },
                {
                    fieldNode: signinPasswordField,
                    require: true,
                    customErr: 'Не указан пароль'
                    //validateOnChange: true
                }
            ]
        },
        signinValidator = new FormValidator(signinValidationConfig),

        /**
         * Конфигурация валидатора для формы регистрации
         * @type {Object}
         */
            registerValidationConfig = {
            fields: [
                {
                    fieldNode: registerFirstNameField,
                    require: true,
                    customErr: 'Не указано имя',
                    validateOnChange: true
                },
                {
                    fieldNode: registerMailPhoneField,
                    validBy: 'isEmail',
                    require: true,
                    customErr: 'Некорректно введен e-mail',
                    validateOnChange: true
                }
            ]
        },
        registerValidator = new FormValidator(registerValidationConfig),

        /**
         * Конфигурация валидатора для формы регистрации
         * @type {Object}
         */
            forgotPwdValidationConfig = {
            fields: [
                {
                    fieldNode: forgotPwdLoginField,
                    require: true,
                    customErr: 'Не указан email или мобильный телефон',
                    validateOnChange: true
                }
            ]
        },
        forgotValidator = new FormValidator(forgotPwdValidationConfig);
    // end of vars


    /**
     * Класс по работе с окном входа на сайт
     *
     * @author  Shaposhnik Vitaly
     *
     * @this    {Login}
     *
     * @constructor
     */
    constructors.Login = (function() {
        'use strict';

        function Login() {
            // enforces new
            if ( !(this instanceof Login) ) {
                return new Login();
            }
            // constructor body

            this.formId = ''; // id текущей формы
            this.formName = ''; // название текущей формы

            if ( !registerMailPhoneField.length ) {
                return false;
            }

            $(document).on('click', '.registerAnotherWayBtn', $.proxy(this.registerAnotherWay, this));
            $(document).on('keyup', '.jsRegisterFirstName, .jsRegisterUsername', $.proxy(this.checkInputs, this));
            $(document).on('click', '.bAuthLink', this.openAuth);
            $('#login-form, #register-form, #reset-pwd-form').data('redirect', true).on('submit', $.proxy(this.formSubmit, this));
            $(document).on('click', '.jsForgotPwdTrigger, .jsRememberPwdTrigger', this.forgotFormToggle);
            $(document).on('click', '#bUserlogoutLink', this.logoutLinkClickLog);
        }


        /**
         * Показ сообщений об ошибках при оформлении заказа
         *
         * @param   {String}    msg     Сообщение которое необходимо показать пользователю
         */
        Login.prototype.showError = function ( msg, callback ) {
            var error = $('#' + this.formId + ' ul.error_list');
            // end of vars

            if ( callback !== undefined ) {
                callback();
            }

            if (error.length) {
                error.html('<li>' + msg + '</li>');
            }
            else {
                $('#' + this.formId + ' .bFormLogin__ePlaceTitle').after($('<ul class="error_list" />').append('<li>' + msg + '</li>'));
            }

            return false;
        };

        /**
         * Обработка ошибок формы
         *
         * @param   {Object}    formError   Объект с полем содержащим ошибки
         */
        Login.prototype.formErrorHandler = function ( formError ) {
            var validator = eval(this.formName + 'Validator'),
                field = $('[name="' + this.formName + '[' + formError.field + ']"]');
            // end of vars

            var clearError = function clearError() {
                validator._unmarkFieldError($(this));
            };
            // end of functions

            console.warn('Ошибка в поле');

            validator._markFieldError(field, formError.message);
            field.bind('focus', clearError);

            return false;
        };

        /**
         * Обработка ошибок из ответа сервера
         */
        Login.serverErrorHandler = {
            'default': function( res ) {
                console.log('Обработчик ошибки');

                if ( res.error && res.error.message ) {
                    this.showError(res.error.message, function() {
                        document.location.href = res.redirect;
                    });

                    return false;
                }

                document.location.href = res.redirect;
            },

            0: function( res ) {
                var formError = null;
                // end of vars

                console.warn('Обработка ошибок формы');

                if ( res.redirect ) {
                    this.showError(res.error.message, function(){
                        document.location.href = res.redirect;
                    });

                    return;
                }

                //this.showError(res.error.message);

                for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
                    formError = res.form.error[i];
                    console.warn(formError);

                    if ( formError.field !== 'global' && formError.message !== null ) {
                        $.proxy(this.formErrorHandler, this)(formError);
                    }
                    else if ( formError.field == 'global' && formError.message !== null ) {
                        this.showError(formError.message);
                    }
                }

                return false;
            }
        };

        /**
         * Проверяем как e-mail
         */
        Login.prototype.checkEmail = function() {
            return registerMailPhoneField.hasClass('registerPhone') ? false : true;
        };

        /**
         * Переключение типов проверки
         */
        Login.prototype.registerAnotherWay = function () {
            var label = $('.registerAnotherWay'),
                btn = $('.registerAnotherWayBtn'),
                registerPhonePH = $('.registerPhonePH');
            // end of vars

            registerMailPhoneField.val('');

            if ( this.checkEmail() ) {
                label.html('Ваш мобильный телефон:');
                btn.html('Ввести e-mail');
                registerMailPhoneField.attr('maxlength', 10);
                registerMailPhoneField.addClass('registerPhone');
                registerPhonePH.show();
                registerValidator.setValidate( registerMailPhoneField, {validBy: 'isPhone', customErr: 'Некорректно введен телефон'} );
            }
            else {
                label.html('Ваш e-mail:');
                btn.html('У меня нет e-mail');
                registerMailPhoneField.removeAttr('maxlength');
                registerMailPhoneField.removeClass('registerPhone');
                registerPhonePH.hide();
                registerValidator.setValidate( registerMailPhoneField, {validBy: 'isEmail', customErr: 'Некорректно введен e-mail'} );
            }

            return false;
        };

        /**
         * Проверка заполненности инпутов
         */
        Login.prototype.checkInputs = function ( e ) {
            if ( !this.checkEmail() ) {
                // проверяем как телефон
                if ( ((e.which >= 96) && (e.which <= 105)) || ((e.which >= 48) && (e.which <= 57)) || (e.which === 8) ) {
                    //если это цифра или бэкспэйс
                }
                else {
                    // если это не цифра
                    var clearVal = registerMailPhoneField.val().replace(/\D/g, '');
                    registerMailPhoneField.val(clearVal);
                }
            }

            return false;
        };

        /**
         * Authorization process
         */
        Login.prototype.openAuth = function () {
            $('#auth-block').lightbox_me({
                centered: true,
                autofocus: true,
                onLoad: function () {
                    $('#auth-block').find('input:first').focus();
                }
            });

            return false;
        };


        /**
         * Изменение значения кнопки сабмита при отправке ajax запроса
         * @param btn Кнопка сабмита
         */
        Login.prototype.submitBtnLoadingDisplay = function ( btn ) {
            if ( btn.length ) {
                var value1 = btn.val(),
                    value2 = btn.data('loading-value'),
                    disabled = btn.attr('disabled') != undefined ? btn.attr('disabled') : true;
                // end of vars

                btn.attr('disabled', (disabled ? false : true)).val(value2).data('loading-value', value1);
            }

            return false;
        }

        /**
         * Сабмит формы регистрации или авторизации
         */
        Login.prototype.formSubmit = function ( e, param ) {
            var form = $(e.target),
                wholemessage = form.serializeArray();
            // end of vars

            e.preventDefault();

            this.formId = form.attr('id');
            this.formName = (this.formId == 'login-form')
                    ? 'signin'
                    : (this.formId == 'register-form' ? 'register' : (this.formId == 'reset-pwd-form' ? 'forgot' : ''));

            var authFromServer = function ( response ) {
                if ( !response.success ) {
                    if ( Login.serverErrorHandler.hasOwnProperty(response.error.code) ) {
                        console.log('Есть обработчик');
                        $.proxy(Login.serverErrorHandler[response.error.code], this)(response);
                    }
                    else {
                        console.log('Стандартный обработчик');
                        Login.serverErrorHandler['default'](response);
                    }

                    this.submitBtnLoadingDisplay( form.find('[type="submit"]:first') );

                    return false;
                }

                $.proxy(this.formSubmitLog, this)( form );

                if ( form.data('redirect') ) {
                    if ( response.data.link ) {
                        window.location = response.data.link;
                    }
                    else {
                        form.unbind('submit');
                        form.submit();
                    }
                }
                else {
                    $('#auth-block').trigger('close');
                    PubSub.publish('authorize', response.user);
                }

                //for order page
                if ( $('#order-form').length ) {
                    $('#user-block').html('Привет, <strong><a href="' + response.data.link + '">' + response.data.user.first_name + '</a></strong>');
                    $('#order_recipient_first_name').val(response.data.user.first_name);
                    $('#order_recipient_last_name').val(response.data.user.last_name);
                    $('#order_recipient_phonenumbers').val(response.data.user.mobile_phone.slice(1));
                    $('#qiwi_phone').val(response.data.user.mobile_phone.slice(1));
                }
            };

            this.submitBtnLoadingDisplay( form.find('[type="submit"]:first') );
            wholemessage['redirect_to'] = form.find('[name="redirect_to"]:first').val();
            $.post(form.attr('action'), wholemessage, $.proxy(authFromServer, this), 'json');

            return false;
        };

        /**
         * Отображение формы "Забыли пароль"
         */
        Login.prototype.forgotFormToggle = function () {
            $('#reset-pwd-form').toggle();
            $('#login-form').toggle();

            return false;
        };

        /**
         * Логирование при сабмите формы регистрации или авторизации
         *
         * @param formId
         * @private
         */
        Login.prototype.formSubmitLog = function ( form ) {
            if ( 'login-form' == this.formId ) {
                if ( typeof(_gaq) !== 'undefined' ) {
                    var type = ( (form.find('.jsSigninUsername').val().search('@')) !== -1 ) ? 'email' : 'mobile';
                    _gaq.push(['_trackEvent', 'Account', 'Log in', type, window.location.href]);
                }

                if ( typeof(_kmq) !== 'undefined' ) {
                    _kmq.push(['identify', form.find('.jsSigninUsername').val() ]);
                }
            }
            else if ( 'register-form' == this.formId ) {
                if ( typeof(_gaq) !== 'undefined' ) {
                    var type = ( this.checkEmail() ) ? 'email' : 'mobile';
                    _gaq.push(['_trackEvent', 'Account', 'Create account', type]);
                }

                if ( typeof(_kmq) !== 'undefined' ) {
                    _kmq.push(['identify', form.find('.jsRegisterUsername').val() ]);
                }
            }
            else if ( 'reset-pwd-form' == this.formId ) {
                if ( typeof(_gaq) !== 'undefined' ) {
                    var type = ( (form.find('.jsForgotPwdLogin').val().search('@')) !== -1 ) ? 'email' : 'mobile';
                    _gaq.push(['_trackEvent', 'Account', 'Forgot password', type]);
                }
            }
        };

        /**
         * Логирование при клике на ссылку выхода
         */
        Login.prototype.logoutLinkClickLog = function () {
            if ( typeof(_kmq) !== 'undefined' ) {
                _kmq.push(['clearIdentity']);
            }

            return false;
        };

        return Login;
    }());


    $(document).ready(function () {
        login = new ENTER.constructors.Login();
    });

}(window.ENTER));