/**
 * Обработчик для формы "Отзыв о товаре"
 *
 * @author		Shaposhnik Vitaly
 */
;(function() {
	var body = $('body'),
		form = $('.jsReviewForm'),
		prosField = $('.jsPros'),
		consField = $('.jsCons'),
		extractField = $('.jsExtract'),
		authorField = $('.jsAuthor'),
		authorEmailField = $('.jsAuthorEmail'),

		/**
		 * Конфигурация валидатора для формы "Отзыв о товаре"
		 * @type {Object}
		 */
			validationConfig = {
			fields: [
				{
					fieldNode: prosField,
					require: true,
					customErr: 'Не указаны достоинства'
				},
				{
					fieldNode: consField,
					require: true,
					customErr: 'Не указаны недостатки'
				},
				{
					fieldNode: extractField,
					require: true,
					customErr: 'Не указан Комментарий'
				},
				{
					fieldNode: authorField,
					require: true,
					customErr: 'Не указано имя'
				},
				{
					fieldNode: authorEmailField,
					require: true,
					customErr: 'Не указан e-mail'
				}
			]
		},
		validator = new FormValidator(validationConfig);
	//end of vars

	var openPopup = function() {
			$('.jsReviewPopup').lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {}
			});

			return false;
		},

		/**
		 * Обработчик ответа от сервера
		 */
			responseFromServer = function( response ) {
			console.log('Ответ от сервера');

//			if ( response.error ) {
//				return false;
//			}

			console.warn(response);

			if ( response.success ) {
				if (response.notice.message) {
					form.before(response.notice.message);
				}
				form.hide();
			}

			return false;
		},

		/**
		 * Сабмит формы "Отзыв о товаре"
		 */
			formSubmit = function() {
			var requestToServer = function () {
				$.post(form.attr('action'), form.serializeArray(), responseFromServer, 'json');
				console.log('Сабмит формы "Отзыв о товаре"');

				return false;
			};
			//end of functions

			validator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);
				},
				onValid: requestToServer//$.post(form.attr('action'), form.serializeArray(), responseFromServer, 'json')
			});

			return false;
		},

	/**
	 * Проверка заполненности инпутов
	 */
//		checkInputs = function () {
//			var fieldsEmpty = false,
//				submitButton = $('.jsReviewForm .jsFormSubmit');
//			//end of vars
//
//			$.each($('.jsReviewFormField'), function () {
//				if ( '' === $(this).val() ) {
//					fieldsEmpty = true;
//				}
//			});
//
//			// присутствуют незаполненные поля
//			if ( fieldsEmpty ) {
//				submitButton.addClass('mDisabled');
//			}
//			else {
//				submitButton.removeClass('mDisabled');
//			}
//
//			return false;
//		},

		/**
		 * @param  {Event} e
		 * @param  {userInfo} userInfo
		 */
			fillUserData = function ( e, userInfo ) {
			if ( userInfo ) {
				// если присутствует имя пользователя
				if ( userInfo.name ) {
					authorField.val(userInfo.name);
					authorField.parent('.jsPlace2Col').hide();
				}
				// если присутствует email пользователя
				if ( userInfo.email ) {
					authorEmailField.val(userInfo.email);
					authorEmailField.parent('.jsPlace2Col').hide();
				}
				// если присутствует и имя и email пользователя, то скрываем весь fieldset
				if ( userInfo.name && userInfo.email ) {
					authorField.parents('.jsFormFieldset').hide();
				}
			}
		};
	//end of functions


	body.on('click', '.jsReviewSend', openPopup);
	body.on('submit', '.jsReviewForm', formSubmit);

//	checkInputs();
//	body.on('keyup', '.jsReviewFormField', checkInputs);

	body.on('userLogged', fillUserData);
}());