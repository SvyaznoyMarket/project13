/**
 * Enterprize
 *
 * @author  Shaposhnik Vitaly
 */
;(function() {
	var
		form = $('.jsUserEditEnterprizeForm'),
		authLink = $('.jsEnterprizeAuthLink'),
		body = $('body'),
		mobilePhoneField = $('.jsMobilePhone'),

		/**
		 * Конфигурация валидатора для формы ЛК Enterprize
		 * @type {Object}
		 */
		validationConfig = {
			fields: [
				{
					fieldNode: $('.jsFirstName'),
					require: true,
					customErr: 'Не указано имя'
				},
//				{
//					fieldNode: $('.jsMiddleName'),
//					require: true,
//					customErr: 'Не указано отчество'
//				},
				{
					fieldNode: $('.jsLastName'),
					require: true,
					customErr: 'Не указана фамилия'
				},
				{
					fieldNode: mobilePhoneField,
					require: true,
					validBy: 'isPhone',
					customErr: 'Не указан номер телефона'
				},
				{
					fieldNode: $('.jsEmail'),
					require: true,
					validBy: 'isEmail',
					customErr: 'Не указан email'
				},
//				{
//					fieldNode: $('.jsCardNumber'),
//					require: true,
//					customErr: 'Не указан номер карты Связной-Клуб'
//				},
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
		validator = new FormValidator(validationConfig);
	// end of vars

	var
		/**
		 * Убераем login-попап при клике на купон
		 *
		 * @param e
		 * @param userInfo
		 */
		removeEnterprizeAuthClass = function removeEnterprizeAuthClass( e, userInfo ) {
			if ( !userInfo || !userInfo.name ) {
				return;
			}

			authLink.length && $.each(authLink, function () { $(this).removeClass('jsEnterprizeAuthLink') });
		},

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
						console.warn('Form has error');
						serverErrorHandler(response);
					}
					else {
						//
						response.notice.message && showMsg(response.notice.message, 'notice');
					}

					return false;
				};
			// end of functions

			validator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);
				},
				onValid: function() { $.post(action, formData, responseFromServer, 'json') }
			});

			// очищаем блок сообщений
			clearMsg();

			return false;
		},

		/**
		 * Добавление маски
		 */
		addMask = function addMask() {
			// устанавливаем маску для поля "Ваш мобильный телефон"
			if ( mobilePhoneField.length ) {
				$.mask.definitions['n'] = '[0-9]';
				mobilePhoneField.mask('8nnnnnnnnnn');
			}
		};
	// end of functions


	addMask();
	body.on('userLogged', removeEnterprizeAuthClass);
	body.on('submit', '.jsUserEditEnterprizeForm', formSubmit);
}());