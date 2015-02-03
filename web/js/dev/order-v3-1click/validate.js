;(function($) {

	ENTER.OrderV31Click.functions.initValidate = function() {
		var $body = $(document.body),
			$orderContent = $('.orderCnt'),
			$pageNew = $('#jsOneClickContentPage'),
			$validationErrors = $('.jsOrderValidationErrors'),
			$form = $('.jsOrderV3OneClickForm'),
			errorClass = 'textfield-err',
			validateEmail = function validateEmailF(email) {
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(email);
			},
			validate = function validateF(){
				var error = [],
					$phoneInput = $('[name=user_info\\[mobile\\]]'),
					$emailInput = $('[name=user_info\\[email\\]]'),
					$deliveryMethod = $('.orderCol_delivrLst_i-act span'),
					phone = $phoneInput.val().replace(/\s+/g, '');

				if (!/8\(\d{3}\)\d{3}-\d{2}-\d{2}/.test(phone)) {
					error.push('Неверный формат телефона');
					$phoneInput.addClass(errorClass).siblings('.errTx').show();
				} else {
					$phoneInput.removeClass(errorClass).siblings('.errTx').hide();
				}

				if ($emailInput.val().length != 0 && !validateEmail($emailInput.val())) {
					error.push('Неверный формат E-mail');
					$emailInput.addClass(errorClass).siblings('.errTx').show();
				} else {
					$emailInput.removeClass(errorClass).siblings('.errTx').hide();
				}

				/*if ($deliveryMethod.text() == 'Самовывоз' && $('.orderCol_addrs_tx').text().replace(/\s/g, '') == '') {
					error.push('Не выбран адрес доставки или самовывоза');
				}

				if ($deliveryMethod.text() == 'Доставка') {
					if (!ENTER.OrderV31Click.address || !ENTER.OrderV31Click.address.building.name) {
						error.push('Не выбран адрес доставки или самовывоза');
					}
				}*/

				return error;
			};

		if ($validationErrors.length) {
			console.warn('Validation errors', $validationErrors);
		}

		$pageNew.on('blur', 'input',function(){
			validate()
		});

		$form.on('submit', function(e){

			var	$el = $(this),
				$submitBtn = $el.find('.orderCompl_btn'),
				data = $el.serializeArray(),
				error = validate(),
				errorHtml = '';

			if (error.length != 0) {
				console.warn('Ошибки валидации', error);
				$.each(error, function(i,val){ errorHtml += val + '<br/>'});
				$('#OrderV3ErrorBlock').html(errorHtml).show();
				e.preventDefault();
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
					}

					var $orderContainer = $('#jsOrderV3OneClickOrder');
					if ($orderContainer.length) {
						$.get($orderContainer.data('url')).done(function(response) {
							$orderContainer.html(response.result.page);

							if (typeof ENTER.utils.sendOrderToGA == 'function') ENTER.utils.sendOrderToGA($('#jsOrder').data('value'));

						});
					}
				})
				.fail(function(jqXHR){
					var response = $.parseJSON(jqXHR.responseText);

					if (response.result && response.result.errorContent) {
						$('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
					}

					var error = (response.result && response.result.error) ? response.result.error : {};

					$('body').trigger('trackUserAction', ['3_2 Оформить_ошибка', 'Поле ошибки: '+ ((typeof error !== 'undefined') ? error.join(', ') : '')]);
				})
			;

			e.preventDefault();
		})
	};


}(jQuery));