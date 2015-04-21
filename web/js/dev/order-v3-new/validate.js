;(function($) {

    var $body = $(document.body),
        $orderContent = $('.orderCnt'),
        $errorBlock = $orderContent.find('#OrderV3ErrorBlock'),
        $pageNew = $('.jsOrderV3PageNew'),
        $pageDelivery = $('.jsOrderV3PageDelivery'),
        $validationErrors = $('.jsOrderValidationErrors'),
        errorClass = 'textfield-err',
		cancelInputBlur = false,
        mnogoRuCookie = docCookies.getItem('enter_mnogo_ru'),
        validateEmail = function validateEmailF(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email) && !/[а-яА-Я]/.test(email);
        },
		validateMnogoRu = function validateMnogoRuF(val){
			return val.length == 0 || /\d{4}\s\d{4}/.test(val)
		},
        validate = function validateF(){
			var error = [],
				$phoneInput = $('[name=user_info\\[phone\\]]'),
				$emailInput = $('[name=user_info\\[email\\]]'),
				$bonusCardInput =  $('[name=user_info\\[bonus_card_number\\]]'),
				$mnogoRuInput = $('.jsOrderV3MnogoRuCardField'),
				$subscribeInput = $('.jsOrderV3SubscribeCheckbox'),
				phone = $phoneInput.val().replace(/\s+/g, '');

			if (!/\+7\(\d{3}\)\d{3}-\d{2}-\d{2}/.test(phone)) {
				error.push('Неверный формат телефона');
				$phoneInput.addClass('textfield-err').siblings('.errTx').show();
			} else {
				$phoneInput.removeClass('textfield-err').siblings('.errTx').hide();
			}

			if (($subscribeInput.is(':checked') || $emailInput.hasClass('jsOrderV3EmailRequired')) && $emailInput.val().length == 0) {
				error.push('Не указан email');
				$emailInput.addClass('textfield-err').siblings('.errTx').text('Не указан email').show();
			} else if ($emailInput.val().length != 0 && !validateEmail($emailInput.val())) {
				error.push('Неверный формат E-mail');
				$emailInput.addClass('textfield-err').siblings('.errTx').text('Неверный формат email').show();
			} else {
				$emailInput.removeClass('textfield-err').siblings('.errTx').hide();
			}

			if ($bonusCardInput.length > 0) $bonusCardInput.mask($bonusCardInput.data('mask')); // еще раз, т.к. событие blur и последующий validate проскакивает раньше обновления значения инпута плагином
			if ($mnogoRuInput.length > 0) $mnogoRuInput.mask($mnogoRuInput.data('mask')); // еще раз, т.к. событие blur и последующий validate проскакивает раньше обновления значения инпута плагином

			if ($bonusCardInput.length > 0 && $bonusCardInput.val().length != 0 && !ENTER.utils.checkEan($bonusCardInput.val())) {
				error.push('Неверный код карты лояльности');
				$bonusCardInput.addClass(errorClass).siblings('.errTx').show();
			} else {
				$bonusCardInput.removeClass('textfield-err').siblings('.errTx').hide();
			}

			// Много.ру
			if ($mnogoRuInput.length > 0 && !validateMnogoRu($mnogoRuInput.val())) {
				error.push('Неверный код карты Много.ру');
				$mnogoRuInput.addClass(errorClass).siblings('.errTx').show();
			} else {
				$mnogoRuInput.removeClass('textfield-err').siblings('.errTx').hide();
			}

			return error;
		};

    if ($validationErrors.length) {
        console.warn('Validation errors', $validationErrors);
    }

	/* Проверяем форму при потере фокуса любого input */
	$pageNew.on('blur', 'input', function(){
		if (cancelInputBlur) {
			return;
		}

		validate();
	});

    // PAGE NEW

    // проверка телефона и email
    $pageNew.find('form').on('submit', function (e) {
		var error = validate(),
			$mnogoRuInput = $('.jsOrderV3MnogoRuCardField');
        if (error.length != 0) {
            e.preventDefault();
            $body.trigger('trackUserAction', ['6_2 Далее_ошибка_Получатель', 'Поле ошибки: '+ error.join(', ')])
        } else {
			// запоминаем значение номера карты Много.ру
			if ($mnogoRuInput) docCookies.setItem('enter_mnogo_ru', $mnogoRuInput.val(), 31536e3, '/');
            console.log('mnogo.ru', docCookies.getItem('enter_mnogo_ru'))
		}
    });

	// SITE-5292
	$pageNew.on('mousedown keydown', '.jsOrderV3SubscribeLabel, .jsOrderV3SubscribeCheckbox', function(){
		cancelInputBlur = true;
		$pageNew.one('mouseup keyup', function() {
			setTimeout(function() {
				cancelInputBlur = false;
				$('input', $pageNew).blur();
			}, 0);
		});
	});

	$pageNew.on('change', '.jsOrderV3SubscribeCheckbox', function(){
		if (!$(this).is(':checked')) $body.trigger('trackGoogleEvent', ['Email_checkout', 'unsubscribe', 'email']);
	});

	$pageNew.on('blur', '.jsOrderV3EmailField', function(){
		if (cancelInputBlur) {
			return;
		}

		var $this = $(this);
		validateEmail($this.val())
			? $body.trigger('trackGoogleEvent', ['Email_checkout', 'success_validation', 'email'])
			: $body.trigger('trackGoogleEvent', ['Email_checkout', 'fail_prevalidation', 'email'])
	});

	// добавляем сохраненное значение карты Много.ру
	if ($pageNew && mnogoRuCookie != null && mnogoRuCookie != 'undefined') {
		$pageNew.find('.jsOrderV3MnogoRuCardField').val(mnogoRuCookie);
		$pageNew.find('.jsMnogoRuSpan').text(mnogoRuCookie);
	}

    // PAGE DELIVERY

	// SITE-5275
	$pageDelivery.on('click', '.jsAcceptTerms', function(e){
		$(e.currentTarget).parent().removeClass('accept-err');
	});

    $pageDelivery.on('click', '.orderCompl_btn', function(e){
        var error = [],
			$agreement = $('.jsAcceptAgreement'),
			$form = $(this).closest('form'),
			send15_3 = false,
			partnerOrders = $('.jsPartnerOrder');

        if (!$agreement.is(':checked')) {
            error.push('Необходимо согласие с информацией о продавце и его офертой');
			$agreement.parent().addClass('accept-err')
        } else {
			$agreement.parent().removeClass('accept-err')
		}

        // Проверяем заказы от партнеров
		partnerOrders.each(function(){
			// Доставка
			if ($(this).find('.orderCol_delivrLst_i-act').text().indexOf('Доставка') != -1) {
				if (!ENTER.OrderV3.address || !ENTER.OrderV3.address.buildingName()) {
					$('.jsSmartAddressBlock').addClass('orderCol_delivrIn-err');
					error.push('Укажите адрес доставки');
				} else {
					$('.jsSmartAddressBlock').removeClass('orderCol_delivrIn-err');
				}
			}
			// Самовывоз
			$(this).find('.orderCol_addrs_tx').each(function(i,val){
				if ($(val).text().replace(/\s+/, '').length == 0) {
					$(this).closest('.orderCol_delivrIn-empty').addClass('orderCol_delivrIn-err');
					error.push('Укажите адрес самовывоза');
				}
			});
		});

		e.preventDefault();

        if (error.length != 0) {
            $errorBlock = $orderContent.find('#OrderV3ErrorBlock'); // TODO не очень хорошее поведение
            $body.trigger('trackUserAction', ['15_2 Оформить_ошибка_Доставка', 'Поле ошибки: '+error.join(', ')]);
        } else {

			// Два условия, по которым мы должны отправить событие 15_3
			if ( $('.orderCol_addrs_fld').length > 0 && ENTER.OrderV3.address.buildingName() == "") send15_3 = true;
			if ( $('.orderCol_delivrIn-empty:not(.jsSmartAddressBlock)').length > 0 ) send15_3 = true;

			if (send15_3) $body.trigger('trackUserAction', ['15_3 Оформить_успешно_КЦ']);

            $body.trigger('trackUserAction', ['15_1 Оформить_успешно_Доставка_ОБЯЗАТЕЛЬНО']);
			$(this).attr('disabled', true); // блокируем кнопку "Отправить"
			setTimeout(function() {	$form.submit(); }, 1000 ); // быстрая обертка для отправки аналитики, иногда не успевает отправляться
        }

    });

}(jQuery));