/**
 * Валидация формы. Отправка на сервер. Аналитика
 */
;(function( global ){
	var orderValidator = {},

		// form fields
		firstNameField = $('#order_recipient_first_name'),
		emailField = $('#order_recipient_email'),
		phoneField = $('#order_recipient_phonenumbers'),
		streetField = $('#order_address_street'),
		buildingField = $('#order_address_building'),

		// complete button
		orderCompleteBtn = $('#completeOrder'),

		validationConfig = {
			fields: [
				{
					fieldNode: firstNameField,
					require: true,
					customErr: 'Не введено имя',
					validateOnChange: true
				},
				{
					fieldNode: emailField,
					validBy: 'isEmail',
					customErr: 'Некорректно введен e-mail',
					validateOnChange: true
				},
				{
					fieldNode: phoneField,
					// validBy: 'isPhone',
					require: true,
					customErr: 'Некорректно введен телефон',
					validateOnChange: true
				},
				{
					fieldNode: streetField,
					require: true,
					customErr: 'Не введено название улицы',
					validateOnChange: true
				},
				{
					fieldNode: buildingField,
					require: true,
					customErr: 'Не введен номер дома',
					validateOnChange: true
				}
			]
		};
	// end of vars
	
	orderValidator = new FormValidator(validationConfig);
	
	var orderComplete = function( e ) {
			console.info('завершить оформление заказа');
			e.preventDefault();

			orderValidator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);

					$.scrollTo(err[err.length - 1].fieldNode, 500, {offset:-50});
				},
				onValid: function() {
					console.info('valid');
				}
			});

			return false;
	};
	
	phoneField.mask("(999) 999-99-99");
    phoneField.val(phoneField.val());

	orderCompleteBtn.bind('click', orderComplete);
}(this));