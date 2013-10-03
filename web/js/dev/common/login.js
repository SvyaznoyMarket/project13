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

        var loginNameField = $('#signin_username'),
            loginPasswordField = $('#signin_password'),
            firstNameField = $('#register_first_name'), // поле "Ваше имя" на форме регистрации
            mailPhoneField = $('#register_username'),
            regBtn = $('#register-form .bigbutton'),

            /**
             * Конфигурация валидатора для формы логина
             * @type {Object}
             */
            loginValidationConfig = {
                fields: [
                    {
                        fieldNode: loginNameField,
                        require: true,
                        customErr: 'Не указан логин',
                        validateOnChange: true
                    },
                    {
                        fieldNode: loginPasswordField,
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
                        fieldNode: firstNameField,
                        require: true,
                        customErr: 'Не указано имя',
                        validateOnChange: true
                    },
                    {
                        fieldNode: mailPhoneField,
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

            if ( !mailPhoneField.length ) {
                return false;
            }


//            $(document).on('click', '#login-form .bigbutton', this._submitBtnClick);


            $(document).on('click', '.registerAnotherWayBtn', $.proxy(this._registerAnotherWay, this));
            regBtn.on('click', this._registerBtnClickLog);
            firstNameField.on('keyup', $.proxy(this._checkInputs, this));
            mailPhoneField.on('keyup', $.proxy(this._checkInputs, this));
            $(document).on('click', '.bAuthLink', this._openAuth);
            $('#login-form, #register-form').data('redirect', true).on('submit', this._formSubmit);
            $(document).on('click', '#forgot-pwd-trigger', this._forgotFormToggle);
            $(document).on('click', '#remember-pwd-trigger', this._forgotFormToggle);
            $(document).on('submit', '#reset-pwd-form', this._forgotFormSubmit);
            $(document).on('click', '#bUserlogoutLink', this._logoutLinkClickLog);
        }


        /**
         * Дисейблим кнопку регистрации
         */
        /*this.registerBtnDisable = function registerBtnDisable() {
            register = false;
            regBtn.addClass('mDisabled');
        };*/


        /**
         * Енейблим кнопку регистрации
         */
        /*this.registerBtnEnable = function registerBtnEnable() {
            register = true;
            regBtn.removeClass('mDisabled');
        };*/


        /**
         * Проверяем как e-mail
         */
        Login.prototype._checkEmail = function() {
            if ( mailPhoneField.hasClass('registerPhone') ) {
                return false;
            }
            else {
                return true;
            }
        };

        /**
         * Переключение типов проверки
         */
        Login.prototype._registerAnotherWay = function () {
            var label = $('.registerAnotherWay'),
                btn = $('.registerAnotherWayBtn'),
                registerPhonePH = $('.registerPhonePH');

            mailPhoneField.val('');

            if ( this._checkEmail() ) {
                label.html('Ваш мобильный телефон:');
                btn.html('Ввести e-mail');
                mailPhoneField.attr('maxlength', 10);
                mailPhoneField.addClass('registerPhone');
                registerPhonePH.show();
                registerValidator.setValidate( mailPhoneField, {validBy: 'isPhone', customErr: 'Некорректно введен телефон'} );
            }
            else {
                label.html('Ваш e-mail:');
                btn.html('У меня нет e-mail');
                mailPhoneField.removeAttr('maxlength');
                mailPhoneField.removeClass('registerPhone');
                registerPhonePH.hide();
                registerValidator.setValidate( mailPhoneField, {validBy: 'isEmail', customErr: 'Некорректно введен e-mail'} );
            }
            //this._registerBtnDisable();
        };

        /**
         * Проверка заполненности инпутов
         */
        Login.prototype._checkInputs = function ( e ) {

            if ( this._checkEmail() ) {
                // проверяем как e-mail
//                if ( (mailPhoneField.val().search('@') !== -1) && (firstNameField.val().length > 0) ) {
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
                    var clearVal = mailPhoneField.val().replace(/\D/g, '');
                    mailPhoneField.val(clearVal);
                }

//                if ( (mailPhoneField.val().length === 10) && (firstNameField.val().length > 0) ) {
//                    //this.registerBtnEnable();
//                }
//                else {
//                    //this.registerBtnDisable();
//                }
            }

            /*loginValidator.setValidate( loginNameField, {
                require: true,
                customErr: 'Не введено название улицы',
                validateOnChange: true
            });
            loginValidator.setValidate( loginPasswordField, {
                require: true,
                customErr: 'Не введено название улицы',
                validateOnChange: true
            });*/

            /*loginValidator.removeFieldToValidate( loginNameField );
            loginValidator.setValidate( loginPasswordField, {
                require: true,
                customErr: 'Не введено название улицы',
                validateOnChange: true
            });*/

//            console.info('**************************************');
            /*registerValidator.validate({
                onInvalid: function( err ) {
                    console.warn('invalid');
                    console.log(err);
                },
                onValid: function () {
                    console.info('valid');
                    //$('#login-form').submit();this._formSubmit();
                }
            });*/
//            console.info('**************************************');
            //return false;

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
         * Логирование при сабмите формы регистрации или авторизации
         *
         * @param formId
         * @private
         */
        Login.prototype._formSubmitLog = function ( form, formId ) {
            if ( 'login-form' == formId ) {
                if ( typeof(_gaq) !== 'undefined' ) {
                    var type = ( (form.find('#signin_username').val().search('@')) !== -1 ) ? 'email' : 'mobile';

                    _gaq.push(['_trackEvent', 'Account', 'Log in', type, window.location.href]);
                }

                if ( typeof(_kmq) !== 'undefined' ) {
                    _kmq.push(['identify', form.find('#signin_username').val() ]);
                }
            }
            else if ( 'register-form' == formId ) {
                if ( typeof(_kmq) !== 'undefined' ) {
                    _kmq.push(['identify', form.find('#register_username').val() ]);
                }
            }
        };

        /**
         * Сабмит формы регистрации или авторизации
         */
        Login.prototype._formSubmit = function ( e, param ) {
            e.preventDefault();

            var form = $(this),
                wholemessage = form.serializeArray(),
                formId = form.attr('id');

            var authFromServer = function ( response ) {
                if ( !response.success ) {
                    //form.html($(response.data.content).html());
                    //regEmailValid();

                    /*loginValidator.validate({
                        onInvalid: function( err ) {
                            console.warn('invalid');
                            console.log(err);
                        },
                        onValid: function () {
                            console.info('valid');
                            //$('#login-form').submit();this._formSubmit();
                        }
                    });*/
                    loginValidator._markFieldError(loginNameField, 'test');
                    return false;
                }

                Login.prototype._formSubmitLog( form, formId );

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

            form.find('[type="submit"]:first').attr('disabled', true).val('login-form' == formId ? 'Вхожу...' : 'Регистрируюсь...');
            wholemessage['redirect_to'] = form.find('[name="redirect_to"]:first').val();

            $.post(form.attr('action'), wholemessage, authFromServer, 'json');
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
         * Логирование при клике на кнопку регистрации
         */
        Login.prototype._registerBtnClickLog = function () {
            //if ( !this.register ) {
            //    return false;
            //}

            if ( typeof(_gaq) !== 'undefined' ) {
                var type = ( this._checkEmail() ) ? 'email' : 'mobile';

                _gaq.push(['_trackEvent', 'Account', 'Create account', type]);
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

        /**
         * Клик кнопки логина
         */
//        Login.prototype._submitBtnClick = function () {
//            console.info('**************************************');
//            loginValidator.validate({
//                onInvalid: function( err ) {
//                    console.warn('invalid');
//                    console.log(err);
//                    return false;
//                },
//                onValid: function () {console.info('valid');$('#login-form').submit();/*this._formSubmit();*/}
//            });
//            console.info('**************************************');
//            return false;
//        };


        return Login;
    }());


    $(document).ready(function () {
        login = new ENTER.constructors.Login();
    });

}(window.ENTER));


