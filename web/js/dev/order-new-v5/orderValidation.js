/**
 * Валидация формы. Отправка на сервер. Аналитика
 */
;(function( global ){
	var orderValidator = {},
		subwayArray = $('#metrostations').data('name'),

		// form fields
		firstNameField = $('#order_recipient_first_name'),
		lastNameField = $('#order_recipient_last_name'),
		emailField = $('#order_recipient_email'),
		phoneField = $('#order_recipient_phonenumbers'),
		subwayField = $('#order_address_metro'),
		metroIdFiled = $('#order_subway_id'),
		streetField = $('#order_address_street'),
		buildingField = $('#order_address_building'),
		paymentRadio = $('.jsCustomRadio[name="order[payment_method_id]"]'),
		orderAgreed = $('#order_agreed'),

		// complete button
		orderCompleteBtn = $('#completeOrder'),

		/**
		 * Конфигурация валидатора
		 * @type {Object}
		 */
		validationConfig = {
			fields: [
				{
					fieldNode: firstNameField,
					require: true,
					customErr: 'Введите имя получателя',
					validateOnChange: true
				},
				{
					fieldNode: lastNameField,
					require: true,
					customErr: 'Введите фамилию получателя',
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
					require: true,
					customErr: 'Некорректно введен телефон',
					validateOnChange: true
				},
				{
					fieldNode: orderAgreed,
					require: true,
					customErr: 'Необходимо согласие',
				},
				{
					fieldNode: paymentRadio,
					require: true,
					customErr: 'Необходимо выбрать метод оплаты'
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

	var showError = function showError( msg ) {
		var content = '<div class="popupbox width290">' +
				'<div class="font18 pb18"> '+msg+'</div>'+
				'</div>' +
				'<p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>',
			block = $('<div>').addClass('popup').html(content);
		// end of vars
		
		block.appendTo('body');

		var errorPopupCloser = function() {
			block.trigger('close');
			block.remove();

			return false;
		};

		block.lightbox_me({
			centered:true,
			onClose: errorPopupCloser
		});

		block.find('.closePopup').bind('click', errorPopupCloser);
	};

	var formErrorHandler = function formErrorHandler( formError ) {
		console.warn('Ошибка в поле');
		var field = $('[name="order['+formError.field+']"]');

		orderValidator.setValidate( field, {
			require: true,
			customErr: formError.message,
			validateOnChange: true
		});

		orderValidator._markFieldError(field, formError.message);
	};
	
	var serverErrorHandler = {
		0: function( res ) {
			var formError = null;

			showError(res.error.message);

			for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
				formError = res.form.error[i];
				console.warn(formError);
				formErrorHandler(formError);
			}

			$.scrollTo($('.mError').eq(0), 500, {offset:-15});
		},
		
		743: function( res ) {
			showError(res.error.message);
		}
	};

		/**
		 * Обработка ответа от сервера
		 *
		 * @param	{Object}	res		Ответ сервера
		 */
	var processingResponse = function processingResponse( res ) {
			console.info('данные отправлены. получен ответ от сервера');
			global.OrderModel.blockScreen.unblock();
			console.log(res);

			if ( !res.success ) {
				console.log('ошибка оформления заказа');
				serverErrorHandler[res.error.code](res);

				return false;
			}

			document.location.href = res.redirect;
		},

		/**
		 * Подготовка данных для отправки на сервер
		 * Отправка данных
		 */
		preparationData = function preparationData() {
			var currentDeliveryBox = null,
				parts = [],
				dataToSend = [],
				tmpPart = {},
				orderForm = $('#order-form');
			// end of vars
			
			global.OrderModel.blockScreen.block('Ваш заказ оформляется');

			/**
			 * Перебираем блоки доставки
			 */
			console.info('Перебираем блоки доставки');
			for (var i = global.OrderModel.deliveryBoxes().length - 1; i >= 0; i--) {
				tmpPart = {};
				currentDeliveryBox = global.OrderModel.deliveryBoxes()[i];
				console.log(currentDeliveryBox);

				tmpPart = {
					deliveryMethod_token: currentDeliveryBox.state,
					date: currentDeliveryBox.choosenDate().value,
					interval: [
						currentDeliveryBox.choosenInterval().start,
						currentDeliveryBox.choosenInterval().end
					],
					point_id: currentDeliveryBox.choosenPoint().id,
					products : []
				};

				for ( var j = currentDeliveryBox.products.length - 1; j >= 0; j-- ) {
					tmpPart.products.push(currentDeliveryBox.products[j].id);
				}

				parts.push(tmpPart);
			}

			dataToSend = orderForm.serializeArray();
			dataToSend.push({ name: 'order[delivery_type_id]', value: global.OrderModel.choosenDeliveryTypeId });
			dataToSend.push({ name: 'order[part]', value: JSON.stringify(parts) });

			console.log(dataToSend);

			$.ajax({
				url: orderForm.attr('action'),
				timeout: 120000,
				type: "POST",
				data: dataToSend,
				success: processingResponse
			});
		},

		/**
		 * Обработчик нажатия на кнопку завершения заказа
		 */
		orderCompleteBtnHandler = function orderCompleteBtnHandler() {
			console.info('завершить оформление заказа');

			orderValidator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);

					$.scrollTo(err[err.length - 1].fieldNode, 500, {offset:-15});
				},
				onValid: preparationData
			});

			return false;
		},

		/**
		 * Обработчик изменения в поле выбора станции метро
		 * Проверка корректности заполнения поля
		 */
		subwayChange = function subwayChange() {
			for ( var i = subwayArray.length - 1; i >= 0; i-- ) {
				if ( subwayField.val() === subwayArray[i].label ) {
					return;
				}
			}

			subwayField.val('');
		},

		/**
		 * Изменение типа доставки в одом из блоков
		 * 
		 * @param	{Event}		event				Данные о событии
		 * @param	{Boolean}	hasHomeDelivery		Есть ли блок с доставкой домой
		 */
		orderDeliveryChangeHandler = function orderDeliveryChangeHandler( event, hasHomeDelivery ) {
			if ( hasHomeDelivery ) {
				// Добавялем поле ввода улицы в список валидируемых полей
				orderValidator.setValidate( streetField, {
					require: true,
					customErr: 'Не введено название улицы',
					validateOnChange: true
				});

				// Добавялем поле ввода номера дома в список валидируемых полей
				orderValidator.setValidate( buildingField, {
					require: true,
					customErr: 'Не введен номер дома',
					validateOnChange: true
				});

				if ( subwayArray !== undefined ) {
					// Добавлем валидацию поля метро
					orderValidator.setValidate( subwayField , {
						fieldNode: subwayField,
						customErr: 'Не выбрана станция метро',
						require: true,
						validateOnChange: true
					});
				}
			}
			else {
				// Удаляем поле ввода улицы из списка валидируемых полей
				orderValidator.setValidate( streetField, {
					require: false
				});

				// Удаляем поле ввода номера дома из списка валидируемых полей
				orderValidator.setValidate( buildingField, {
					require: false
				});

				if ( subwayArray !== undefined ) {
					// Удаляем поле метро из списка валидируемых полей
					orderValidator.setValidate( subwayField , {
						require: false
					});
				}
			}

			console.info('Изменен тип доставки');
			console.log(orderValidator);
		};
	// end of functions
	

	phoneField.mask("(999) 999-99-99");

	/**
	 * AB-test
	 * Обязательное поле e-mail
	 */
	if ( global.docCookies.getItem('emails') ) {
		console.log('AB TEST: e-mail require');

		orderValidator.setValidate( emailField , {
			require: true
		});
	}

	if ( subwayArray !== undefined ) {
		subwayField.autocomplete(subwayAutocompleteConfig);
		subwayField.bind('change', subwayChange);
	}


	/**
	 * Подстановка значений в поля
	 */
	var defaultValueToField = function defaultValueToField( fields ) {
		var fieldNode = null;

		console.info('defaultValueToField');
		for ( var field in fields ) {
			console.log('поле '+field);
			if ( fields[field] ) {
				console.log('для поля есть значение '+fields[field]);
				fieldNode = $('input[name="'+field+'"]');

				// поле текстовое	
				if ( fieldNode.attr('type') === 'text' ) {
					fieldNode.val( fields[field] );
				}

				// радио кнопка
				if ( fieldNode.attr('type') === 'radio' ) {
					fieldNode.filter('[value="'+fields[field]+'"]').attr('checked', 'checked');
				}
			}
		}
	};
	defaultValueToField($('#jsOrderForm').data('value'));

	$('body').bind('orderdeliverychange', orderDeliveryChangeHandler);
	orderCompleteBtn.bind('click', orderCompleteBtnHandler);
}(this));