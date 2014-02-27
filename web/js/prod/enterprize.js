/**
 * Enterprize
 *
 * @author  Shaposhnik Vitaly
 */
;(function() {
	var
		form = $('.jsEnterprizeForm'),
		authLink = $('.jsEnterprizeAuthLink'),
		body = $('body'),
		mobilePhoneField = $('.jsMobile'),

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
						if ( response.needAuth ) {
							$('#auth-block').lightbox_me({
								centered: true,
								autofocus: true,
								onLoad: function() {
									$('#auth-block').find('input:first').focus();
								}
							});

							return false;
						}

						console.warn('Form has error');
						serverErrorHandler(response);
					}
					else {
						if ( response.data.link !== undefined ) {
							window.location.href = response.data.link;
						}
						else if ( response.notice.message ) {
							showMsg(response.notice.message, 'notice');
						}
					}

					return false;
				};
			// end of functions

//			validator.validate({
//				onInvalid: function( err ) {
//					console.warn('invalid');
//					console.log(err);
//				},
//				onValid: function() { $.post(action, formData, responseFromServer, 'json') }
//			});
			$.post(action, formData, responseFromServer, 'json');

			// очищаем блок сообщений
			clearMsg();

			return false;
		};
	// end of functions

	// устанавливаем маску для поля "Ваш мобильный телефон"
	$.mask.definitions['n'] = '[0-9]';
	mobilePhoneField.length && mobilePhoneField.mask('8nnnnnnnnnn');

	body.on('userLogged', removeEnterprizeAuthClass);
	body.on('submit', '.jsEnterprizeForm', formSubmit);
}());