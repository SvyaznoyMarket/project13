;(function( ENTER ) {
	var constructors = ENTER.constructors,
		registerMailPhoneField = $('.jsRegisterUsername'),
		body = $('body'),
		authBlock = $('#auth-block'),

		/**
		 * Конфигурация валидатора для формы логина
		 * @type {Object}
		 */
			signinValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsSigninUsername'),
					require: true,
					customErr: 'Не указан логин'
				},
				{
					fieldNode: $('.jsSigninPassword'),
					require: true,
					customErr: 'Не указан пароль'
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
					fieldNode: $('.jsRegisterFirstName'),
					require: true,
					customErr: 'Не указано имя'
				},
				{
					fieldNode: registerMailPhoneField,
					validBy: 'isEmail',
					require: true,
					customErr: 'Некорректно введен e-mail'
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
					fieldNode: $('.jsForgotPwdLogin'),
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

			this.form = null; // текущая форма

			body.on('click', '.registerAnotherWayBtn', $.proxy(this.registerAnotherWay, this));
			body.on('click', '.bAuthLink', this.openAuth);
			$('.jsLoginForm, .jsRegisterForm, .jsResetPwdForm').data('redirect', true).on('submit', $.proxy(this.formSubmit, this));
			body.on('click', '.jsForgotPwdTrigger, .jsRememberPwdTrigger', this.forgotFormToggle);
			body.on('click', '#bUserlogoutLink', this.logoutLinkClickLog);
		}


		/**
		 * Показ сообщений об ошибках при оформлении заказа
		 *
		 * @param   {String}    msg     Сообщение которое необходимо показать пользователю
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.showError = function( msg, callback ) {
			var error = $('ul.error_list', this.form);
			// end of vars

			if ( callback !== undefined ) {
				callback();
			}

			if ( error.length ) {
				error.html('<li>' + msg + '</li>');
			}
			else {
				$('.bFormLogin__ePlaceTitle', this.form).after($('<ul class="error_list" />').append('<li>' + msg + '</li>'));
			}

			return false;
		};

		/**
		 * Обработка ошибок формы
		 *
		 * @param   {Object}    formError   Объект с полем содержащим ошибки
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.formErrorHandler = function( formError ) {
			var validator = this.getFormValidator(),
				field = $('[name="' + this.getFormName() + '[' + formError.field + ']"]');
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
		 *
		 * @this   {Login}
		 * @public
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
					this.showError(res.error.message, function() {
						document.location.href = res.redirect;
					});

					return;
				}

				// очищаем блок с глобальными ошибками
				if ( $('ul.error_list', this.form).length ) {
					$('ul.error_list', this.form).html('');
				}
				//this.showError(res.error.message);

				for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
					formError = res.form.error[i];
					console.warn(formError);

					if ( formError.field !== 'global' && formError.message !== null ) {
						$.proxy(this.formErrorHandler, this)(formError);
					}
					else if ( formError.field === 'global' && formError.message !== null ) {
						this.showError(formError.message);
					}
				}

				return false;
			}
		};

		/**
		 * Проверяем как e-mail
		 *
		 * @return  {Boolean}   Выбрано ли поле e-mail в качестве регистрационных данных
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.checkEmail = function() {
			return registerMailPhoneField.hasClass('jsRegisterPhone') ? false : true;
		};

		/**
		 * Переключение типов проверки
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.registerAnotherWay = function() {
			var label = $('.registerAnotherWay'),
				btn = $('.registerAnotherWayBtn');
			// end of vars

			registerMailPhoneField.val('');

			if ( this.checkEmail() ) {
				label.html('Ваш мобильный телефон:');
				btn.html('Ввести e-mail');
				registerMailPhoneField.attr('maxlength', 10).addClass('jsRegisterPhone');
				registerValidator.setValidate( registerMailPhoneField, {validBy: 'isPhone', customErr: 'Некорректно введен телефон'} );

				// устанавливаем маску для поля "Ваш мобильный телефон"
				$.mask.definitions['n'] = '[0-9]';
				registerMailPhoneField.mask('+7 (nnn) nnn-nn-nn');
			}
			else {
				label.html('Ваш e-mail:');
				btn.html('У меня нет e-mail');
				registerMailPhoneField.removeAttr('maxlength').removeClass('jsRegisterPhone');
				registerValidator.setValidate( registerMailPhoneField, {validBy: 'isEmail', customErr: 'Некорректно введен e-mail'} );

				// убераем маску с поля "Ваш мобильный телефон"
				registerMailPhoneField.unmask();
			}

			return false;
		};

		/**
		 * Authorization process
		 *
		 * @public
		 */
		Login.prototype.openAuth = function() {
			authBlock.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {
					authBlock.find('input:first').focus();
				}
			});

			return false;
		};


		/**
		 * Изменение значения кнопки сабмита при отправке ajax запроса
		 *
		 * @param btn Кнопка сабмита
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.submitBtnLoadingDisplay = function( btn ) {
			if ( btn.length ) {
				var value1 = btn.val(),
					value2 = btn.data('loading-value');
				// end of vars

				btn.attr('disabled', (btn.attr('disabled') === 'disabled' ? false : true)).val(value2).data('loading-value', value1);
			}

			return false;
		}

		/**
		 * Валидатор формы
		 *
		 * @return  {Object}   Валидатор для текущей формы
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.getFormValidator = function() {
			return eval(this.getFormName() + 'Validator');
		}

		/**
		 * Получить название формы
		 *
		 * @return {string} Название текущей формы
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.getFormName = function() {
			return (this.form.hasClass('jsLoginForm'))
				? 'signin'
				: (this.form.hasClass('jsRegisterForm') ? 'register' : (this.form.hasClass('jsResetPwdForm') ? 'forgot' : ''));
		}

		/**
		 * Сабмит формы регистрации или авторизации
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.formSubmit = function( e, param ) {
			e.preventDefault();
			this.form = $(e.target);

			var formData = this.form.serializeArray();

			var authFromServer = function( response ) {
				if ( response.error !== null ) {
					if ( Login.serverErrorHandler.hasOwnProperty(response.error.code) ) {
						console.log('Есть обработчик');
						$.proxy(Login.serverErrorHandler[response.error.code], this)(response);
					}
					else {
						console.log('Стандартный обработчик');
						Login.serverErrorHandler['default'](response);
					}

					this.submitBtnLoadingDisplay( this.form.find('[type="submit"]:first') );

					return false;
				}

				$.proxy(this.formSubmitLog, this)( this.form );

				if ( this.form.data('redirect') ) {
					if ( response.data.link ) {
						window.location = response.data.link;
					}
					else {
						this.form.unbind('submit');
						this.form.submit();
					}
				}
				else {
					authBlock.trigger('close');
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

			this.submitBtnLoadingDisplay( this.form.find('[type="submit"]:first') );

			formData.push({name: 'redirect_to', value: location.href});
			$.post(this.form.attr('action'), formData, $.proxy(authFromServer, this), 'json');

			return false;
		};

		/**
		 * Отображение формы "Забыли пароль"
		 *
		 * @public
		 */
		Login.prototype.forgotFormToggle = function() {
			if ( $('.jsResetPwdForm').is(':visible') ) {
				$('.jsResetPwdForm').hide();
				$('.jsLoginForm').show();
			}
			else {
				$('.jsResetPwdForm').show();
				$('.jsLoginForm').hide();
			}

			return false;
		};

		/**
		 * Логирование при сабмите формы регистрации или авторизации
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.formSubmitLog = function() {
			var type = '';
			// end of vars

			if ( 'signin' === this.getFormName() ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					type = ( (this.form.find('.jsSigninUsername').val().search('@')) !== -1 ) ? 'email' : 'mobile';
					_gaq.push(['_trackEvent', 'Account', 'Log in', type, window.location.href]);
				}

				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['identify', this.form.find('.jsSigninUsername').val() ]);
				}
			}
			else if ( 'register' === this.getFormName() ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					type = ( this.checkEmail() ) ? 'email' : 'mobile';
					_gaq.push(['_trackEvent', 'Account', 'Create account', type]);
				}

				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['identify', this.form.find('.jsRegisterUsername').val() ]);
				}
			}
			else if ( 'forgot' === this.getFormName() ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					type = ( (this.form.find('.jsForgotPwdLogin').val().search('@')) !== -1 ) ? 'email' : 'mobile';
					_gaq.push(['_trackEvent', 'Account', 'Forgot password', type]);
				}
			}
		};

		/**
		 * Логирование при клике на ссылку выхода
		 *
		 * @public
		 */
		Login.prototype.logoutLinkClickLog = function() {
			if ( typeof(_kmq) !== 'undefined' ) {
				_kmq.push(['clearIdentity']);
			}
		};

		return Login;
	}());


	$(document).ready(function() {
		login = new ENTER.constructors.Login();
	});

}(window.ENTER));