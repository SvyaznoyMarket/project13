;(function($) {

	ENTER.OrderV31Click.functions.initValidate = function() {
		var $pageNew = $('#jsOneClickContentPage'),
			$validationErrors = $('.jsOrderValidationErrors'),
			$form = $('.jsOrderV3OneClickForm'),
			errorClass = 'textfield-err',

			loadPaymentForm = function($container, url, data) {
				$container.html('...'); // TODO: loader

				$.ajax({
					url: url,
					type: 'POST',
					data: data
				}).fail(function(jqXHR){
					$container.html('Ошибка');
				}).done(function(response){
					if (response.form) {
						$container.html(response.form);
					}
				}).always(function(){});
			},

			validateEmail = function validateEmailF(email) {
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(email) && !/[а-яА-Я]/.test(email);
			},

			validate = function validateF(){
				console.log('validate');
				var isValid = true,
					$phoneInput = $('[name=user_info\\[mobile\\]]'),
					$emailInput = $('[name=user_info\\[email\\]]'),
					$deliveryMethod = $('.orderCol_delivrLst_i-act span'),
					$acceptOffer = $('.jsAcceptAgreement'),
					accept = $acceptOffer.prop('checked'),
					phone = $phoneInput.val().replace(/\s+/g, '');

				if (!/\+7\(\d{3}\)\d{3}-\d{2}-\d{2}/.test(phone)) {
					isValid = false;
					$phoneInput.addClass(errorClass).siblings('.errTx').show();
				} else {
					$phoneInput.removeClass(errorClass).siblings('.errTx').hide();
				}

				if ($emailInput.hasClass('jsOrderV3EmailRequired') && $emailInput.val().length == 0) {
                    $emailInput.addClass('textfield-err').siblings('.errTx').text('Не указан email').show();
                    isValid = false;
                } else if ($emailInput.val().length != 0 && !validateEmail($emailInput.val())) {
                    $emailInput.addClass('textfield-err').siblings('.errTx').text('Неверный формат email').show();
                    isValid = false;
				} else {
					$emailInput.removeClass(errorClass).siblings('.errTx').hide();
				}

	            if ( !accept ) {
	                $acceptOffer.closest('.jsAcceptAgreementContainer').addClass('error');
	            	isValid = false;
	                return false;
	            } else {
	            	$acceptOffer.closest('.jsAcceptAgreementContainer').removeClass('error');
	            }

				/*if ($deliveryMethod.text() == 'Самовывоз' && $('.orderCol_addrs_tx').text().replace(/\s/g, '') == '') {
				 error.push('Не выбран адрес доставки или самовывоза');
				 }

				 if ($deliveryMethod.text() == 'Доставка') {
				 if (!ENTER.OrderV31Click.address || !ENTER.OrderV31Click.address.building.name) {
				 error.push('Не выбран адрес доставки или самовывоза');
				 }
				 }*/

				return isValid;
			};

		if ($validationErrors.length) {
			console.warn('Validation errors', $validationErrors);
		}

		$pageNew.on('blur', 'input[type="text"]', function(){
			validate()
		}).on('keyup', '.jsOrderV3PhoneField', function(){
            var val = $(this).val();
            if (val[val.length-1] != '_') validate();
        }).on('change', 'input[type="checkbox"], input[type="radio"]', function(){
			validate()
		});

		$form.on('submit', function(e){
			e.preventDefault();

			var	$el = $(this),
				$submitBtn = $el.find('.orderCompl_btn'),
				data = $el.serializeArray();

			if (!validate()) {
				return;
			}

			$.ajax({
				type: 'POST',
				url: $el.attr('action'),
				data: data,
				beforeSend: function(){
					$submitBtn.attr('disabled', true)
				}
			})
				.always(function(){
					$submitBtn.attr('disabled', false)
				})
				.done(function(response) {
					if (typeof response.result !== 'undefined') {
						$('#jsOneClickContentPage').hide();
						$('#jsOneClickContent').append(response.result.page);

						try {
							$('.js-order-onlinePaymentMethod').each(function(i, el) {
								var
									$el = $(el),
									url,
									data,
									relations,
									$formContainer
								;

								if ($el.data('checked')) {
									url = $el.data('url');
									data = $el.data('value');
									relations = $el.data('relation');
									$formContainer = relations['formContainer'] && $(relations['formContainer']);

									loadPaymentForm($formContainer, url, data);
								}
							});
						} catch (error) { console.error(error); }

						$('body').trigger('trackUserAction', ['3_1 Оформить_успешно']);

						// Счётчик GetIntent (BlackFriday)
						(function() {
							if (response.result.lastPartner != 'blackfridaysale') {
								return '';
							}

							$.each(response.result.orders, function(index, order) {
								var products = [];
								var revenue = 0;
								$.each(order.products, function(index, product) {
									products.push({
										id: product.id + '',
										price: product.price + '',
										quantity: parseInt(product.quantity)
									});

									revenue += parseFloat(product.price) * parseInt(product.quantity);
								});

								ENTER.counters.callGetIntentCounter({
									type: "CONVERSION",
									orderId: order.id + '',
									orderProducts: products,
									orderRevenue: revenue + ''
								});
							});
						})();

						/* Hubrus order complete code */
						(function(){
							var product, orderId;
							if (response.result.lastPartner != 'hubrus' || !window.smartPixel1) return;
							product = response.result.orders[0].products[0];
							orderId = response.result.orders[0].id;
							smartPixel1.trackState('oneclick_complete', {
								cart_items: [{
									price: product.price,
									id: product.id
								}],
								order_id: orderId
							});
						})();

						/* AdvMaker */
						(function(){
							if (response.result.lastPartner != 'advmaker') return;
							$.get('http://am15.net/s2s.php', {
								'ams2s': docCookies.get('ams2s'),
								'orders': response.result.orders[0].id
							});
						})();

						// actionpay
						if (response.result.actionpay) {
							APRT_SEND(response.result.actionpay);
						}

						// Счётчик RetailRocket
						(function() {
							$.each(response.result.orders, function(index, order) {
								var products = [];
								$.each(order.products, function(index, product) {
									products.push({
										id: product.id,
										qnt: product.quantity,
										price: product.price
									});
								});

								ENTER.counters.callRetailRocketCounter('order.complete', {
									transaction: order.id,
									items: products
								});
							});
						})();

						if (response.result.orderAnalytics) {
							ENTER.utils.sendOrderToGA(response.result.orderAnalytics);
							ENTER.utils.analytics.soloway.send({
								action: 'orderComplete',
								orders: response.result.orderAnalytics.orders
							});
						}

						// criteo
						if (
							response.result.criteoData
							&& typeof(window.criteo_q) != 'undefined'
							&& !$.isEmptyObject(response.result.criteoData)
						) {
							window.criteo_q.push(response.result.criteoData);
						}
					}
				})
				.fail(function(jqXHR){
					var response = $.parseJSON(jqXHR.responseText);

					if (response.result && response.result.errorContent) {
						$('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
					}

					var error = (response.result && response.result.error) ? response.result.error : [];

					$('body').trigger('trackUserAction', ['3_2 Оформить_ошибка', 'Поле ошибки: '+ ((typeof error !== 'undefined') ? error.join(', ') : '')]);
				})
			;
		});

		$body.on('change', '.js-order-onlinePaymentMethod', function(e) {
			var
				$el = $(this),
				url = $el.data('url'),
				data = $el.data('value'),
				relations = $el.data('relation'),
				$formContainer = relations['formContainer'] && $(relations['formContainer'])
			;

			try {
				if (!url) {
					throw {message: 'Не задан url для получения формы'};
				}
				if (!$formContainer.length) {
					throw {message: 'Не найден контейнер для формы'};
				}

				loadPaymentForm($formContainer, url, data);
			} catch(error) { console.error(error); };

			//e.preventDefault();
		});
	};

}(jQuery));