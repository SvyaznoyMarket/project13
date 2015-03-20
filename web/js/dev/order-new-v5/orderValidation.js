/**
 * Валидация формы. Отправка на сервер. Аналитика
 */
;(function ( window, document, $, ENTER ) {
	console.info('orderValidation.js init');

	var
		utils = ENTER.utils,
		
		orderValidator = {},
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
		bonusCardNumber = $('#bonus-card-number'),
		paymentRadio = $('.jsCustomRadio[name="order[payment_method_id]"]'),
		qiwiPhone = $('#qiwi-phone'),
		orderAgreed = $('#order_agreed'),

		// complete button
		orderCompleteBtn = $('#completeOrder'),


		// analytics data
		ajaxStart = null,
		ajaxStop = null,
		ajaxDelta = null,

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
					customErr: 'Необходимо согласие'
				},
				{
					fieldNode: bonusCardNumber,
					customErr: 'Некорректно введен номер карты лояльности'
				}
			]
		},

		subwayAutocompleteConfig = {
			source: subwayArray,
			appendTo: '#metrostations',
			minLength: 2,
			select : function( event, ui ) {
				metroIdFiled.val(ui.item.val);
			}
		};
	// end of vars
	
	console.log(ENTER.OrderModel);
	console.log('orderValidation:: vars initd');

	orderValidator = new FormValidator(validationConfig);
	utils.orderValidator = orderValidator;

	var
		/**
		 * Показ сообщений об ошибках при оформлении заказа
		 * 
		 * @param	{String}	msg		Сообщение которое необходимо показать пользователю
		 */
		showError = function showError( msg, callback ) {
			var
				content = '<div class="popupbox width290">' +
					'<div class="font18 pb18"> '+msg+'</div>'+
					'</div>' +
					'<p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>',
				block = $('<div>').addClass('popup').html(content);
			// end of vars
			
			block.appendTo('body');

			var
				errorPopupCloser = function() {
					block.trigger('close');
					block.remove();

					if ( callback !== undefined ) {
						callback();
					}

					return false;
				};
			// end of functions

			block.lightbox_me({
				centered:true,
				onClose: errorPopupCloser
			});

			block.find('.closePopup').bind('click', errorPopupCloser);
		},

		/**
		 * Обработка ошибок формы
		 * 
		 * @param	{Object}	formError	Объект с полем содержащим ошибки
		 */
		formErrorHandler = function formErrorHandler( formError ) {
			console.warn('Ошибка в поле');
			
			var field = $('[name="order['+formError.field+']"]');

			var clearError = function clearError() {
				orderValidator._unmarkFieldError($(this));
			};

			orderValidator._markFieldError(field, formError.message);
			field.bind('focus', clearError);
		},
	
		/**
		 * Обработка ошибок из ответа сервера
		 */
		serverErrorHandler = {
			'default': function( res ) {
				console.log('Обработчик ошибки');

				if ( 'undefined' === typeof(res.redirect) ) {
					res.redirect = '/cart';
				}

				if ( res.error && res.error.message ) {
					showError(res.error.message, function() {
						if ( 0 !== res.redirect ) {
							// Если в ответе точно 0, значит ошибка валидации — не редиректим,
							// предоставляем возможность изменить выбор и жизнь
							document.location.href = res.redirect;
						}
					});

					return;
				}

				document.location.href = res.redirect;
			},

			0: function( res ) {
				console.warn('Обработка ошибок формы');

				var formError = null;

				if ( res.redirect ) {
					showError(res.error.message, function(){
						document.location.href = res.redirect;
					});

					return;
				}

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
		},

		/**
		 * Аналитика завершения заказа
		 */
		completeAnalytics = function completeAnalytics() {
			if ( typeof _gaq !== 'undefined') {
				for ( var i = ENTER.OrderModel.deliveryBoxes().length - 1; i >= 0; i-- ) {
					_gaq.push(['_trackEvent', 'Order card', 'Completed', 'выбрана '+ENTER.OrderModel.choosenDeliveryTypeId+' доставят '+ENTER.OrderModel.deliveryBoxes()[i].state]);
				}

				_gaq.push(['_trackEvent', 'Order complete', ENTER.OrderModel.deliveryBoxes().length, ENTER.OrderModel.orderDictionary.products.length]);
				_gaq.push(['_trackTiming', 'Order complete', 'DB response', ajaxDelta]);
			}

            $(document.body).trigger('trackUserAction', ['9 Завершение - успех']);
		},

		/**
		 * Обработка ответа от сервера
		 *
		 * @param	{Object}	res		Ответ сервера
		 */
		processingResponse = function processingResponse( res ) {
			console.info('данные отправлены. получен ответ от сервера');
			
			ajaxStop = new Date().getTime();
			ajaxDelta = ajaxStop - ajaxStart;

			console.log(res);

			if ( !res.success ) {
				console.log('ошибка оформления заказа');
                $(document.body).trigger('trackUserAction', ['8 Завершение - ошибка', res.error.code]);

				utils.blockScreen.unblock();

				if ( serverErrorHandler.hasOwnProperty(res.error.code) ) {
					console.log('Есть обработчик');

					serverErrorHandler[res.error.code](res);
				}
				else {
					console.log('Стандартный обработчик');

					serverErrorHandler['default'](res);
				}

				return false;
			}

			completeAnalytics();

			if ( ENTER.OrderModel.paypalECS() && !orderCompleteBtn.hasClass('mConfirm') ) {
				console.info('PayPal ECS включен. Заказ оформлен. Необходимо удалить выбранные параметры из cookie');

				window.docCookies.removeItem('chDate_paypalECS', '/');
				window.docCookies.removeItem('chTypeBtn_paypalECS', '/');
				window.docCookies.removeItem('chPoint_paypalECS', '/');
				window.docCookies.removeItem('chTypeId_paypalECS', '/');
				window.docCookies.removeItem('chStetesPriority_paypalECS', '/');
			}

			document.location.href = res.redirect;
		},

		/**
		 * Подготовка данных для отправки на сервер
		 * Отправка данных
		 */
		preparationData = function preparationData() {
			var
				currentDeliveryBox = null,
				choosePoint,
				parts = [],
				dataToSend = [],
				tmpPart = {},
				i, j,
				orderForm = $('#order-form');
			// end of vars
			
			utils.blockScreen.block('Ваш заказ оформляется');
			dataToSend = orderForm.serializeArray();

			/**
			 * Перебираем блоки доставки
			 */
			console.info('Перебираем блоки доставки');
			for ( i = ENTER.OrderModel.deliveryBoxes().length - 1; i >= 0; i-- ) {
				tmpPart = {};
				currentDeliveryBox = ENTER.OrderModel.deliveryBoxes()[i];
				choosePoint = currentDeliveryBox.choosenPoint();
				console.log('currentDeliveryBox:');
				console.log(currentDeliveryBox);

				tmpPart = {
					deliveryMethod_token: currentDeliveryBox.state,
					date: currentDeliveryBox.choosenDate().value,
					interval: [
						( currentDeliveryBox.choosenInterval() ) ? currentDeliveryBox.choosenInterval().start : '',
						( currentDeliveryBox.choosenInterval() ) ? currentDeliveryBox.choosenInterval().end : ''
					],
					point_id: choosePoint.id,
					products : [],
                    deliveryPrice : currentDeliveryBox.deliveryPrice
				};

				console.log('choosePoint:');
				console.log(choosePoint);

				if ( 'self_partner_pickpoint' === currentDeliveryBox.state ) {
					console.log('Is PickPoint!');

					// Передаём на сервер корректный id постамата, не id точки, а номер постамата
					tmpPart.point_id = choosePoint['number'];

					// В качестве адреса доставки необходимо передавать адрес постамата,
					// так как поля адреса при заказе через pickpoint скрыты
					/*orderForm.find('#order_address_street').val( choosePoint['street'] );
					orderForm.find('#order_address_building').val( choosePoint['house'] );
					orderForm.find('#order_address_number').val('');
					orderForm.find('#order_address_apartment').val('');
					orderForm.find('#order_address_floor').val('');*/ // old

					/* Передаём сразу без лишней сериализации и действий с формами
					 * и не в dataToSend, а в массив parts, отдельным полем,
					 * т.к. может быть разный адрес у разных пикпойнтов
					 * */
					// parts.push( {pointAddress: choosePoint['street'] + ' ' + choosePoint['house']} );
					tmpPart.point_address = {
						street:	choosePoint['street'],
						house:	choosePoint['house']
					};
					tmpPart.point_name = choosePoint.point_name; // нужно передавать в ядро
				}

				for ( j = currentDeliveryBox.products.length - 1; j >= 0; j-- ) {
					tmpPart.products.push(currentDeliveryBox.products[j].id);
				}

				console.log('tmpPart:');
				console.log(tmpPart);

				parts.push(tmpPart);
			}

			dataToSend.push({ name: 'order[delivery_type_id]', value: ENTER.OrderModel.choosenDeliveryTypeId });
			dataToSend.push({ name: 'order[part]', value: JSON.stringify(parts) });

			console.log('dataToSend:');
			console.log(dataToSend);

			ajaxStart = new Date().getTime();

			$.ajax({
				url: orderForm.attr('action'),
				timeout: 120000,
				type: 'POST',
				data: dataToSend,
				success: processingResponse,
				statusCode: {
					500: function() {
						showError('Не удалось создать заказ. Попробуйте позднее. 500');
					},
					504: function() {
						showError('Не удалось создать заказ. Попробуйте позднее. 504');
					}
				}
			});
		},

		/**
		 * Обработчик нажатия на кнопку завершения заказа
		 */
		orderCompleteBtnHandler = function orderCompleteBtnHandler() {
			console.info('Завершить оформление заказа');

			/**
			 * Для акции «подари жизнь» валидация полей на клиенте не требуется
			 */
			if ( ENTER.OrderModel.lifeGift() ) {
				preparationData();

				return false;
			}

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
	
	$.mask.definitions['n'] = '[0-9]';
	bonusCardNumber.mask('2 98nnnn nnnnnn', {
		placeholder: '*'
	});
	qiwiPhone.mask('(nnn) nnn-nn-nn');
	phoneField.mask('(nnn) nnn-nn-nn');

	/**
	 * AB-test
	 * Обязательное поле e-mail
	 */
	if ( window.docCookies.getItem('emails') ) {
		console.log('AB TEST: e-mail require');

		orderValidator.setValidate( emailField , {
			require: true
		});
	}


	/**
	 * Подстановка значений в поля
	 */
	var defaultValueToField = function defaultValueToField( fields ) {
		var
			fieldNode = null,
			field;
		// end of vars

		console.groupCollapsed('Подстановка значений в поля defaultValueToField()');
		for ( field in fields ) {
			console.log('поле '+field);
			
			if ( fields[field] ) {
				console.log('для поля есть значение '+fields[field]);
				fieldNode = $('input[name="'+field+'"]');
				var fieldType = fieldNode.attr('type');

				// радио кнопка
				if ( fieldType === 'radio' ) {
					fieldNode.filter('[value="'+fields[field]+'"]').attr('checked', 'checked').trigger('change');
					
					continue;
				}


				// поле текстовое
				if ( $.inArray(fieldType, ['text', 'password', 'color', 'date', 'datetime', 'datetime-local', 'email', 'number', 'range', 'search', 'tel', 'time', 'url', 'month', 'week']) != -1 ) {
					fieldNode.val( fields[field] );
				}
			}
		}
        console.groupEnd();
	};
	defaultValueToField($('#jsOrderForm').data('value'));


	/**
	 * Включение автокомплита метро
	 * Подстановка станции метро по id
	 */
	if ( subwayArray !== undefined ) {
		subwayField.autocomplete(subwayAutocompleteConfig);
		subwayField.bind('change', subwayChange);

		for ( var i = subwayArray.length - 1; i >= 0; i-- ) {
			if ( parseInt(metroIdFiled.val(), 10) === subwayArray[i].val ) {
				subwayField.val(subwayArray[i].label);

				break;
			}
		}
	}

	$('body').bind('orderdeliverychange', orderDeliveryChangeHandler);
	orderCompleteBtn.bind('click', orderCompleteBtnHandler);

	console.log('orderValidation.js inited');

}(this, this.document, this.jQuery, this.ENTER));