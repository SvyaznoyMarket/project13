/**
 * Валидация формы. Отправка на сервер. Аналитика
 */
;(function( global ){
	var orderValidator = {},
		subwayArray = $('#metrostations').data('name'),

		// form fields
		firstNameField = $('#order_recipient_first_name'),
		emailField = $('#order_recipient_email'),
		phoneField = $('#order_recipient_phonenumbers'),
		subwayField = $('#order_address_metro'),
		metroIdFiled = $('#order_subway_id'),
		streetField = $('#order_address_street'),
		buildingField = $('#order_address_building'),
		orderAgreed = $('#order_agreed'),

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
				},
				{
					fieldNode: orderAgreed,
					require: true,
					customErr: 'Необходимо согласие',
				}
			]
		},

		subwayAutocompleteConfig = {
			source: subwayArray,
			appendTo: '#metrostations',
			minLength: 2,
			select : function(event, ui ) {
				metroIdFiled.val(ui.item.val);
			}
		};
	// end of vars
	
	orderValidator = new FormValidator(validationConfig);
	
	/**
	 * Обработчик нажатия на кнопку завершения заказа
	 */
	var orderComplete = function orderComplete() {
			console.info('завершить оформление заказа');

			orderValidator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);

					$.scrollTo(err[err.length - 1].fieldNode, 500, {offset:-15});
				},
				onValid: function() {
					console.info('valid');
				}
			});

			return false;
	};

	/**
	 * Обработчик изменения в поле выбора станции метро
	 * Проверка корректности заполнения поля
	 */
	var subwayChange = function subwayChange() {
		for (var i = subwayArray.length - 1; i >= 0; i--) {
			if ( subwayField.val() === subwayArray[i].label ) {
				return;
			}
		}

		subwayField.val('');
	};

	var orderDeliveryChangeHandler = function orderDeliveryChangeHandler( event, hasHomeDelivery ) {
		if ( hasHomeDelivery ) {
			// Добавлем валидацию поля метро
			orderValidator.addFieldToValidate({
				fieldNode: subwayField,
				require: true,
				validateOnChange: true
			});
		}
		else {
			// Удаляем поле метро из списка валидируемых полей
			orderValidator.removeFieldToValidate( subwayField );
		}
		console.info('Изменен тип доставки');
		console.log(orderValidator);
	};
	
	phoneField.mask("(999) 999-99-99");
	phoneField.val(phoneField.val());

	if ( subwayArray !== undefined ) {
		console.log('метро существует');
		subwayField.autocomplete(subwayAutocompleteConfig);
		subwayField.bind('change', subwayChange);

		orderValidator.addFieldToValidate({
			fieldNode: subwayField,
			require: true,
			validateOnChange: true
		});
	}

	$('body').bind('orderdeliverychange', orderDeliveryChangeHandler)
	orderCompleteBtn.bind('click', orderComplete);
}(this));