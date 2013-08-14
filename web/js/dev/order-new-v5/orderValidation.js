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
		paymentRadio = $('.jsCustomRadio[name="order[payment_type_id]"]'),
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
				// {
				// 	fieldNode: paymentRadio,
				// 	require: true,
				// 	customErr: 'Необходимо выбрать метод оплаты'
				// },
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
		 * Блокер экрана
		 *
		 * @param	{Object}		noti		Объект jQuery блокера экрана
		 * @param	{Function}		block		Функция блокировки экрана. На вход принимает текст который нужно отобразить в окошке блокера
		 * @param	{Function}		unblock		Функция разблокировки экрана. Объект окна блокера удаляется.
		 */
	var blockScreen = {
			noti: null,
			block: function( text ) {
				console.warn('block screen');

				if ( this.noti ) {
					this.unblock();
				}

				this.noti = $('<div>').addClass('noti').html('<div><img src="/images/ajaxnoti.gif" /></br></br> '+ text +'</div>');
				this.noti.appendTo('body');

				this.noti.lightbox_me({
					centered:true,
					closeClick:false,
					closeEsc:false
				});
			},

			unblock: function() {
				console.warn('unblock screen');

				this.noti.trigger('close');
				this.noti.remove();
			}
		},

		/**
		 * Обработка ответа от сервера
		 *
		 * @param	{Object}	res		Ответ сервера
		 */
		processingResponse = function processingResponse( res ) {
			console.info('данные отправлены. получен ответ от сервера');
			console.log(res);

			blockScreen.unblock();
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
			
			blockScreen.block('Ваш заказ оформляется');

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
					interval: 'с '+currentDeliveryBox.choosenInterval().start+' до '+currentDeliveryBox.choosenInterval().end,
					point_id: currentDeliveryBox.choosenPoint().id,
					products : []
				};

				for (var j = currentDeliveryBox.products.length - 1; j >= 0; j--) {
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
			for (var i = subwayArray.length - 1; i >= 0; i--) {
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

				if ( subwayArray !== undefined ) {
					// Добавлем валидацию поля метро
					orderValidator.setValidate( subwayField , {
						fieldNode: subwayField,
						customErr: 'Не выбрана станция метро',
						require: true
					});
				}
			}
			else {

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

	$('body').bind('orderdeliverychange', orderDeliveryChangeHandler);
	orderCompleteBtn.bind('click', orderCompleteBtnHandler);
}(this));