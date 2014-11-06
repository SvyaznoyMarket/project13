/**
 * Enterprize
 *
 * @author  Shaposhnik Vitaly
 */
;(function(ENTER) {
	var
		form = $('.jsEnterprizeForm'),
		body = $('body'),
		mobilePhoneField = $('.jsMobile'),
		authBlock = $('#enterprize-auth-block'),

		/**
		 * Конфигурация валидатора для формы ЛК Enterprize
		 * @type {Object}
		 */
		validationConfig = {
			fields: [
				{
					fieldNode: $('.jsName'),
					require: true,
					customErr: 'Не указано имя'
				},
				{
					fieldNode: mobilePhoneField,
					require: true,
					validBy: 'isPhone',
					customErr: 'Не указан мобильный телефон'
				},
				{
					fieldNode: $('.jsEmail'),
					require: true,
					validBy: 'isEmail',
					customErr: 'Не указан email'
				},
				{
					fieldNode: $('.jsAgree'),
					require: true,
					customErr: 'Необходимо согласие'
				},
				{
					fieldNode: $('.jsSubscribe'),
					require: true,
					customErr: 'Необходимо согласие'
				}
			]
		},
		validator = new FormValidator(validationConfig),

		/**
		 * Конфигурация валидатора для формы логина
		 * @type {Object}
		 */
		signinValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsSigninUsername', authBlock),
					require: true,
					customErr: 'Не указан логин'
				},
				{
					fieldNode: $('.jsSigninPassword', authBlock),
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
		forgotPwdValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsForgotPwdLogin', authBlock),
					require: true,
					customErr: 'Не указан email или мобильный телефон',
					validateOnChange: true
				}
			]
		},
		forgotValidator = new FormValidator(forgotPwdValidationConfig);
	// end of vars

	var
		/**
		 * Очистка блока сообщений
		 */
		clearMsg = function clearMsg() {
			$('ul.red').length && $('ul.red').html('');
			$('ul.green').length && $('ul.green').html('');
		},

		/**
		 * Показ сообщений
		 *
		 * @param   {String}    msg     Сообщение которое необходимо показать пользователю
		 * @param   {String}    type    Тип сообщения. Ожидаемое значение 'error' или 'notice'
		 */
		showMsg = function showMsg( msg, type ) {
			var
				type = type ? type : 'error',
				msgClass = 'error' === type ? 'red' : ('notice' === type ? 'green' : null),
				msgBlock = $('ul.' + msgClass);
			// end of vars

			if ( !msgClass ) {
				return;
			}

			if ( msgBlock.length ) {
				msgBlock.html('<li>' + msg + '</li>');
			}
			else {
				form.prepend($('<ul class="' + msgClass + '" />').append('<li>' + msg + '</li>'));
			}

			return false;
		},

		/**
		 * Обработчик ошибок формы
		 *
		 * @param   {Object}    formError   Объект с полем содержащим ошибки
		 */
		formErrorHandler = function formErrorHandler( formError ) {
			var
				field = $('[name="user[' + formError.field + ']"]');
			// end of vars

			var
				clearError = function clearError() {
					validator._unmarkFieldError($(this));
				};
			// end of functions

			console.warn('Ошибка в поле');

			validator._markFieldError(field, formError.message);
			field.bind('focus', clearError);

			return false;
		},

		/**
		 * Обработчик ошибок из ответа от сервера
		 *
		 * @param res
		 */
		serverErrorHandler = function serverErrorHandler( res ) {
			var
				formError = null,
				i;
			// end of vars

			console.warn('Обработка ошибок формы');

			for ( i = res.form.error.length - 1; i >= 0; i-- ) {
				formError = res.form.error[i];

				if ( !formError.message ) {
					continue;
				}

				console.warn(formError);

				if ( formError.field === 'global' ) {
					showMsg(formError.message);
				}
				else {
					formErrorHandler(formError);
				}
			}

			return false;
		},

		/**
		 * Обработчик сабмита формы ЛК Enterprize
		 *
		 * @param e
		 */
		formSubmit = function formSubmit( e ) {
			e.preventDefault();

			var
				formData = $(this).serializeArray(),
				action = $(this).attr('action');
			// end of vars

			var
				/**
				 * Обработчик ответа от сервера
				 * @param response
				 */
				responseFromServer = function responseFromServer( response ) {
					if ( response.error ) {
						if ( response.needAuth ) {
							openAuth();
						}

						console.warn('Form has error');
						serverErrorHandler(response);

						return false;
					}

					if ( response.data.link !== undefined ) {
						window.location.href = response.data.link;
					}
					else if ( response.notice.message ) {
						showMsg(response.notice.message, 'notice');
					}

					return false;
				};
			// end of functions

			$.post(action, formData, responseFromServer, 'json');

			// очищаем блок сообщений
			clearMsg();

			return false;
		},

		epHintPopup = function() {
			console.log('hint');

			var 
				btnHintPopup = $('.js-ep-btn-hint-popup'),

				hintPopup = $('.js-ep-hint-popup'),
				hintPopupClose = hintPopup.find('.js-ep-hint-popup-close');
			// end of vars

			var 
				showHintPopup = function showHintPopup() {
					console.log('hint show');
					hintPopup.fadeIn(100);

					return false;
				},

				closeHintPopup = function closeHintPopup() {
					hintPopup.fadeOut(100);

					return false;
				};
			// end of functions

			btnHintPopup.on('click', showHintPopup);

			hintPopupClose.on('click', closeHintPopup);
		},

		/**
		 * Открыть окно авторизации
		 */
		openAuth = function() {
			ENTER.utils.signinValidationConfig = signinValidationConfig;
			ENTER.utils.signinValidator = signinValidator;
			ENTER.utils.forgotPwdValidationConfig = forgotPwdValidationConfig;
			ENTER.utils.forgotValidator = forgotValidator;

			var
				/**
				 * При закрытии попапа убераем ошибки с полей
				 */
				removeErrors = function() {
					var
						validators = ['signin', 'forgot'],
						validator,
						config,
						self,
						i, j;
					// end of vars

					for (j in validators) {
						validator = eval('ENTER.utils.' + validators[j] + 'Validator');
						config = eval('ENTER.utils.' + validators[j] + 'ValidationConfig');

						if ( !config || !config.fields || !validator ) {
							continue;
						}

						for (i in config.fields) {
							self = config.fields[i].fieldNode;
							self && validator._unmarkFieldError(self);
						}
					}
				};
			// end of functions

			authBlock.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {
					authBlock.find('input:first').focus();
				},
				onClose: removeErrors
			});

			return false;
		};
	// end of functions

	// устанавливаем маску для поля "Ваш мобильный телефон"
	$.mask.definitions['n'] = '[0-9]';
	mobilePhoneField.length && mobilePhoneField.mask('8 (nnn) nnn-nn-nn');

	body.on('submit', '.jsEnterprizeForm', formSubmit);
	body.on('click', '.jsEnterprizeAuthLink', openAuth);

	// Подключение слайдера товаров
	if ( $('.bGoodsSlider').length ) {
		$('.bGoodsSlider').goodsSlider();
	}

	$(document).ready(function() {
		if ( $('.epHintPopup').length ) {
			epHintPopup();
		}
	});

	// Открываем информационный попап
//	if ( infoBlock.length ) {
//		openInfoBlock();
//	}

}(window.ENTER));