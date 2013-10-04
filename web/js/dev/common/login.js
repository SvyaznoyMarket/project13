;(function( ENTER ) {
    var constructors = ENTER.constructors;
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

        var signinUserNameField = $('#signin_username'),
            signinPasswordField = $('#signin_password'),
            registerFirstNameField = $('#register_first_name'),
            registerMailPhoneField = $('#register_username'),
            //regBtn = $('#register-form .bigbutton'),

            /**
             * Конфигурация валидатора для формы логина
             * @type {Object}
             */
            loginValidationConfig = {
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
                        customErr: 'Не указан пароль',
                        validateOnChange: true
                    }
                ]
            },
            loginValidator = new FormValidator(loginValidationConfig),

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
            registerValidator = new FormValidator(registerValidationConfig);
        // end of vars


        function Login() {
            // enforces new
            if ( !(this instanceof Login) ) {
                return new Login();
            }
            // constructor body

            this.formId = '';// id текущей формы

            if ( !registerMailPhoneField.length ) {
                return false;
            }

            $(document).on('click', '.registerAnotherWayBtn', $.proxy(this._registerAnotherWay, this));
            $(document).on('click', '#register_first_name, #register_username', $.proxy(this._checkInputs, this));
            $(document).on('click', '.bAuthLink', this._openAuth);
            $('#login-form, #register-form').data('redirect', true).on('submit', $.proxy(this._formSubmit, this));
            $(document).on('click', '#forgot-pwd-trigger', this._forgotFormToggle);
            $(document).on('click', '#remember-pwd-trigger', this._forgotFormToggle);
            $(document).on('submit', '#reset-pwd-form', this._forgotFormSubmit);
            $(document).on('click', '#bUserlogoutLink', this._logoutLinkClickLog);
        }


        /**
         * Показ сообщений об ошибках при оформлении заказа
         *
         * @param   {String}    msg     Сообщение которое необходимо показать пользователю
         */
        Login.prototype._showError = function ( msg/*, callback*/ ) {
            console.log(msg);
        };

        /**
         * Обработка ошибок формы
         *
         * @param   {Object}    formError   Объект с полем содержащим ошибки
         */
        Login.prototype._formErrorHandler = function ( formError ) {
            console.warn('Ошибка в поле');

            console.log('***************************');
            console.log( formError );
            console.log('***************************');


//            var field = $('[name="order['+formError.field+']"]');
//
//            var clearError = function clearError() {
//                validator._unmarkFieldError($(this));
//            };
//
//            validator._markFieldError(field, formError.message);
//            field.bind('focus', clearError);
        };

        /**
         * Обработка ошибок из ответа сервера
         */
        Login.serverErrorHandler = {
            'default': function( res ) {
                console.log('Обработчик ошибки');

                if ( res.error && res.error.message ) {
                    this._showError(res.error.message, function() {
                        document.location.href = res.redirect;
                    });

                    return;
                }

                document.location.href = res.redirect;
            },

            0: function( res ) {
                console.warn('Обработка ошибок формы');

                var formError = null;

                if ( res.redirect ) {
                    Login._showError(res.error.message, function(){
                        document.location.href = res.redirect;
                    });

                    return;
                }

                this._showError(res.error.message);

                for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
                    formError = res.form.error[i];
                    console.warn(formError);
                    //Login._formErrorHandler(formError);
                }
            }
        };

        /**
         * Проверяем как e-mail
         */
        Login.prototype._checkEmail = function() {
            return registerMailPhoneField.hasClass('registerPhone') ? false : true;
        };

        /**
         * Переключение типов проверки
         */
        Login.prototype._registerAnotherWay = function () {
            var label = $('.registerAnotherWay'),
                btn = $('.registerAnotherWayBtn'),
                registerPhonePH = $('.registerPhonePH');

            registerMailPhoneField.val('');

            if ( this._checkEmail() ) {
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
//            this._registerBtnDisable();
        };

        /**
         * Проверка заполненности инпутов
         */
        Login.prototype._checkInputs = function ( e ) {

            if ( this._checkEmail() ) {
                // проверяем как e-mail
//                if ( (registerMailPhoneField.val().search('@') !== -1) && (registerFirstNameField.val().length > 0) ) {
//                    //this.registerBtnEnable();
//                }
//                else {
//                    //this.registerBtnDisable();
//                }
            }
            else {
                // проверяем как телефон
                if ( ((e.which >= 96) && (e.which <= 105)) || ((e.which >= 48) && (e.which <= 57)) || (e.which === 8) ) {
                    //если это цифра или бэкспэйс
                }
                else {
                    // если это не цифра
                    var clearVal = registerMailPhoneField.val().replace(/\D/g, '');
                    registerMailPhoneField.val(clearVal);
                }

//                if ( (registerMailPhoneField.val().length === 10) && (registerFirstNameField.val().length > 0) ) {
//                    //this.registerBtnEnable();
//                }
//                else {
//                    //this.registerBtnDisable();
//                }
            }
        };

        /**
         * Authorization process
         */
        Login.prototype._openAuth = function () {
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
         * Сабмит формы регистрации или авторизации
         */
        Login.prototype._formSubmit = function ( e, param ) {
            var form = $(e.target),
                wholemessage = form.serializeArray();
            // end of vars

            Login.formId = form.attr('id');

            e.preventDefault();

            var authFromServer = function ( response ) {
                if ( !response.success ) {
                    //form.html($(response.data.content).html());
                    //regEmailValid();
                    //loginValidator._unmarkFieldError(signinPasswordField);
console.log(response);
                    console.log(Login.formId+'++++++++++');
//                    Login.prototype._showError( response.error.message );

                    if ( Login.serverErrorHandler.hasOwnProperty(response.error.code) ) {
                        console.log('Есть обработчик');
                        Login.serverErrorHandler[response.error.code](response);
//                        $.proxy(Login.serverErrorHandler[response.error.code](response), this);

                    }
                    else {
                        console.log('Стандартный обработчик');

                        Login.serverErrorHandler['default'](response);
                    }

                    form.find('[type="submit"]:first').attr('disabled', false).val('login-form' == Login.formId ? 'Войти' : 'Регистрация');

                    return false;
                }

                Login.prototype._formSubmitLog( form );

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

            form.find('[type="submit"]:first').attr('disabled', true).val('login-form' == Login.formId ? 'Вхожу...' : 'Регистрируюсь...');
            wholemessage['redirect_to'] = form.find('[name="redirect_to"]:first').val();

            $.post(form.attr('action'), wholemessage, $.proxy(authFromServer, this), 'json');
        };

        /**
         * Отображение формы "Забыли пароль"
         */
        Login.prototype._forgotFormToggle = function () {
            $('#reset-pwd-form').toggle();
            $('#login-form').toggle();
            return false;
        };

        /**
         * Сабмит формы "Забыли пароль"
         */
        Login.prototype._forgotFormSubmit = function () {
            var form = $(this);

            Login.formId = form.attr('id');

            var ajaxSuccess = function ajaxSuccess( resp ) {
                if ( resp.success ) {
                    if ( typeof(_gaq) !== 'undefined' ) {
                        var type = ( (form.find('input.text').val().search('@')) !== -1 ) ? 'email' : 'mobile';

                        _gaq.push(['_trackEvent', 'Account', 'Forgot password', type]);
                    }
                    //$('#reset-pwd-form').hide();
                    //$('#login-form').show();
                    //alert('Новый пароль был вам выслан по почте или смс');
                    var resetForm = $('#reset-pwd-form > div');

                    resetForm.find('input').remove();
                    resetForm.find('.pb5').remove();
                    resetForm.find('.error_list').html('Новый пароль был вам выслан по почте или смс!');
                }
                else {
                    var txterr = ( resp.error !== '' ) ? resp.error : 'Вы ввели неправильные данные';

                    form.find('.error_list').text( txterr );
                    form.find('.whitebutton').removeAttr('disabled');
                }

            }

            form.find('.error_list').html('Запрос отправлен. Идет обработка...');
            form.find('.whitebutton').attr('disabled', 'disabled');

            $.post(form.prop('action'), form.serializeArray(), ajaxSuccess, 'json');

            return false;
        };

        /**
         * Логирование при сабмите формы регистрации или авторизации
         *
         * @param formId
         * @private
         */
        Login.prototype._formSubmitLog = function ( form ) {
            if ( 'login-form' == Login.formId ) {
                if ( typeof(_gaq) !== 'undefined' ) {
                    var type = ( (form.find('#signin_username').val().search('@')) !== -1 ) ? 'email' : 'mobile';

                    _gaq.push(['_trackEvent', 'Account', 'Log in', type, window.location.href]);
                }

                if ( typeof(_kmq) !== 'undefined' ) {
                    _kmq.push(['identify', form.find('#signin_username').val() ]);
                }
            }
            else if ( 'register-form' == Login.formId ) {
                if ( typeof(_gaq) !== 'undefined' ) {
                    var type = ( this._checkEmail() ) ? 'email' : 'mobile';
                    _gaq.push(['_trackEvent', 'Account', 'Create account', type]);
                }

                if ( typeof(_kmq) !== 'undefined' ) {
                    _kmq.push(['identify', form.find('#register_username').val() ]);
                }
            }
        };

        /**
         * Логирование при клике на ссылку выхода
         */
        Login.prototype._logoutLinkClickLog = function () {
            if ( typeof(_kmq) !== 'undefined' ) {
                _kmq.push(['clearIdentity']);
            }
        };


        return Login;
    }());


    $(document).ready(function () {
        login = new ENTER.constructors.Login();
    });

}(window.ENTER));


