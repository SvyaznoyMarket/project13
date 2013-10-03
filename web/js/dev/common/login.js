/**
 * Окно входа на сайт
 *
 * @author      Shaposhnik Vitaly
 * @requires    jQuery
 */


var Login = new (function () {

    var register = false, // корректность регистрационных данных
        firstNameInput = $('#register_first_name'), // поле "Ваше имя" на форме регистрации
        mailPhoneInput = $('#register_username'),
        regBtn = $('#register-form .bigbutton');
    // end of vars


    /**
     * Проверяем как e-mail
     */
    this.checkEmail = function checkEmail() {
        if ( mailPhoneInput.hasClass('registerPhone') ) {
            return false;
        }
        else {
            return true;
        }
    };


    /**
     * Дисейблим кнопку регистрации
     */
    this.registerBtnDisable = function registerBtnDisable() {
        register = false;
        regBtn.addClass('mDisabled');
    };


    /**
     * Енейблим кнопку регистрации
     */
    this.registerBtnEnable = function registerBtnEnable() {
        register = true;
        regBtn.removeClass('mDisabled');
    };


    /**
     * Переключение типов проверки
     */
    this.registerAnotherWay = function registerAnotherWay() {
        this.registerWayChange();
        mailPhoneInput.val('');
        this.registerBtnDisable();
    };


    /**
     * Изменение типа проверки
     */
    this.registerWayChange = function registerWayChange() {
        var label = $('.registerAnotherWay'),
            btn = $('.registerAnotherWayBtn'),
            registerPhonePH = $('.registerPhonePH');

        if ( this.checkEmail() ) {
            label.html('Ваш мобильный телефон:');
            btn.html('Ввести e-mail');
            mailPhoneInput.attr('maxlength', 10);
            mailPhoneInput.addClass('registerPhone');
            registerPhonePH.show();
        }
        else {
            label.html('Ваш e-mail:');
            btn.html('У меня нет e-mail');
            mailPhoneInput.removeAttr('maxlength');
            mailPhoneInput.removeClass('registerPhone');
            registerPhonePH.hide();
        }
    };


    /**
     * Клик ...
     */
    this.registerBtnClick = function registerBtnClick() {
        if ( !register ) {
            return false;
        }

        if ( typeof(_gaq) !== 'undefined' ) {
            var type = ( this.checkEmail() ) ? 'email' : 'mobile';

            _gaq.push(['_trackEvent', 'Account', 'Create account', type]);
        }
    };


    /**
     * Проверка заполненности инпутов
     */
    this.checkInputs = function checkInputs( e ) {
        if ( this.checkEmail() ) {
            // проверяем как e-mail
            if ( (mailPhoneInput.val().search('@') !== -1) && (firstNameInput.val().length > 0) ) {
                this.registerBtnEnable();
            }
            else {
                this.registerBtnDisable();
            }
        }
        else {
            // проверяем как телефон
            if ( ((e.which >= 96) && (e.which <= 105)) || ((e.which >= 48) && (e.which <= 57)) || (e.which === 8) ) {
                //если это цифра или бэкспэйс
            }
            else {
                // если это не цифра
                var clearVal = mailPhoneInput.val().replace(/\D/g, '');
                mailPhoneInput.val(clearVal);
            }

            if ( (mailPhoneInput.val().length === 10) && (firstNameInput.val().length > 0) ) {
                this.registerBtnEnable();
            }
            else {
                this.registerBtnDisable();
            }
        }
    };


    /**
     * Authorization process
     */
    this.openAuth = function openAuth() {
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
    this.formSubmit = function formSubmit( e, param ) {
        e.preventDefault();

        var form = $(this),
            wholemessage = form.serializeArray(),
            form_id = form.attr('id');

        var authFromServer = function authFromServer( response ) {
            if ( !response.success ) {
                form.html($(response.data.content).html());
                regEmailValid();

                return false;
            }

            if ( 'login-form' == form_id ) {
                if ( typeof(_gaq) !== 'undefined' ) {
                    var type = ( (form.find('#signin_username').val().search('@')) !== -1 ) ? 'email' : 'mobile';

                    _gaq.push(['_trackEvent', 'Account', 'Log in', type, window.location.href]);
                }

                if ( typeof(_kmq) !== 'undefined' ) {
                    _kmq.push(['identify', form.find('#signin_username').val() ]);
                }
            }
            else {
                if ( typeof(_kmq) !== 'undefined' ) {
                    _kmq.push(['identify', form.find('#register_username').val() ]);
                }
            }

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

        form.find('[type="submit"]:first').attr('disabled', true).val('login-form' == form_id ? 'Вхожу...' : 'Регистрируюсь...');
        wholemessage['redirect_to'] = form.find('[name="redirect_to"]:first').val();

        $.post(form.attr('action'), wholemessage, authFromServer, 'json');
    };


    /**
     * Отображение формы "Забыли пароль"
     */
    this.forgotFormToggle = function forgotFormToggle() {
        $('#reset-pwd-form').toggle();
        $('#login-form').toggle();
        return false;
    };


    /**
     * Сабмит формы "Забыли пароль"
     */
    this.forgotFormSubmit = function forgotFormSubmit() {
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
     * Клик кнопки "Выход"
     */
    this.logoutLinkClick = function logoutLinkClick() {
        if ( typeof(_kmq) !== 'undefined' ) {
            _kmq.push(['clearIdentity']);
        }
    };


    /**
     * Инизиализация
     */
    this.init = function init() {
        // register e-mail check
        if ( !mailPhoneInput.length ) {
            return false;
        }

        $(document).on('click', '.registerAnotherWayBtn', $.proxy(this.registerAnotherWay, this));
        regBtn.on('click', this.registerBtnClick);
        mailPhoneInput.on('keyup', $.proxy(this.checkInputs, this));
        firstNameInput.on('keyup', $.proxy(this.checkInputs, this));
        $(document).on('click', '.bAuthLink', this.openAuth);
        $('#login-form, #register-form').data('redirect', true).on('submit', this.formSubmit);
        $(document).on('click', '#forgot-pwd-trigger', this.forgotFormToggle);
        $(document).on('click', '#remember-pwd-trigger', this.forgotFormToggle);
        $(document).on('submit', '#reset-pwd-form', this.forgotFormSubmit);
        $(document).on('click', '#bUserlogoutLink', this.logoutLinkClick);
        $('#signin_password').warnings();
    };
})


$(document).ready(function () {
    Login.init();
});
