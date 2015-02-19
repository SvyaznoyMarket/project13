;(function($){
    var body = document.getElementsByTagName('body')[0],
        $body = $(body),
        $orderContent = $('.orderCnt'),
        $jsOrder = $('#jsOrder'),
        region = $('.jsRegion').data('value'),
        isOnlineMotivPage = $('.jsNewOnlineCompletePage').length > 0,
        spinner = typeof Spinner == 'function' ? new Spinner({
            lines: 11, // The number of lines to draw
            length: 5, // The length of each line
            width: 8, // The line thickness
            radius: 23, // The radius of the inner circle
            corners: 1, // Corner roundness (0..1)
            rotate: 0, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            color: '#666', // #rgb or #rrggbb or array of colors
            speed: 1, // Rounds per second
            trail: 62, // Afterglow percentage
            shadow: false, // Whether to render a shadow
            hwaccel: true, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            top: '50%', // Top position relative to parent
            left: '50%' // Left position relative to parent
        }) : null,

        getForm = function getFormF(methodId, orderId, orderNumber) {
            $.ajax({
                'url': 'getPaymentForm/'+methodId+'/order/'+orderId+'/number/'+orderNumber,
                'success': function(data) {
                    var $form;
                    if (data.form != '') {
                        $form = $(data.form);
                        if (spinner) spinner.spin(body);

                        if ($form.hasClass('jsPaymentFormPaypal') && typeof $form.attr('action') != 'undefined') {
                            window.location.href = $form.attr('action');
                        } else {
							$body.append($form);
							$form.submit();
                        }
                    }
                    console.log('Payment data', data);

                }
            })
        },

        showCreditWidget = function showCreditWidgetF(bankProviderId, data, number_erp, bank_id) {

            if ( bankProviderId == 1 ) showKupiVKredit(data['kupivkredit']);
            if ( bankProviderId == 2 ) showDirectCredit(data['direct-credit']);

            $.ajax({
                type: 'POST',
                url: '/order/update-credit',
                data: {
                    number_erp: number_erp,
                    bank_id: bank_id
                }
            });

            /* При выборе варианта заявки на кредит */
            if (bank_id == 1) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19_1 Заявка_кредит_Оплата', 'Тинькофф']);
            if (bank_id == 2) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19_1 Заявка_кредит_Оплата', 'Ренесанс']);
            if (bank_id == 3) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19_1 Заявка_кредит_Оплата', 'ОТП-Банк']);

        },

        showKupiVKredit = function showKupiVKreditF(data){
            var callback_close = function(decision) { },
                callback_decision = function(decision) { },
                vkredit;

            console.log(data);

            $LAB.script( '//www.kupivkredit.ru/widget/vkredit.js')
                .wait( function() {
                    vkredit = new VkreditWidget(1, data.vars.sum,  {
                        order: data.vars.order,
                        sig: data.vars.sig,
                        callbackUrl: window.location.href,
                        onClose: callback_close,
                        onDecision: callback_decision
                    });

                    vkredit.openWidget();
                });
        },

        showDirectCredit = function showDirectCreditF(data){
            var productArr = [];

            $LAB.script( '//api.direct-credit.ru/JsHttpRequest.js' )
                .script( '//api.direct-credit.ru/dc.js' )
                .wait( function() {
                    console.info('скрипты загружены для кредитного виджета. начинаем обработку');

                    $.each(data.vars.items, function(index, elem){
                        productArr.push({
                            id: elem.articul,
                            name: elem.name,
                            price: elem.price,
                            type: elem.type,
                            count: elem.quantity
                        })
                    });


                    DCLoans(data.vars.partnerID, 'getCredit', { products: productArr, order: data.vars.number, codeTT: data.vars.region }, function(result){
                       console.log(result);
                    }, false);

            });
        };

    // клик по методу онлайн-оплаты
    $orderContent.on('click', '.jsPaymentMethod', function(){
        var id = $(this).data('value'),
            $order = $(this).closest('.orderLn').length > 0 ? $(this).closest('.orderLn') : $orderContent,
            orderId = $order.data('order-id'),
            orderNumber = $order.data('order-number');
        switch (id) {
            case 5:
                getForm(5, orderId, orderNumber);
                $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '17_1 Оплатить_онлайн_Оплата', 'Онлайн-оплата']);
                break;
            case 8:
                getForm(8, orderId, orderNumber);
                $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '17_1 Оплатить_онлайн_Оплата', 'Psb']);
                break;
            case 13:
                getForm(13, orderId, orderNumber);
                $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '17_1 Оплатить_онлайн_Оплата', 'PayPal']);
                break;
			case 14:
				getForm(14, orderId, orderNumber);
				$body.trigger('trackUserAction', ['17_1 Оплатить_онлайн_Связной_клуб_баллы']);
				break;
        }
    });

	$orderContent.on('click', '.jsOnlinePaymentPossible', function(){
		$(this).find('.jsOnlinePaymentDiscount').hide();
        $orderContent.find('.jsOnlinePaymentDiscountPayNow').show();
		//$orderContent.find('.jsOnlinePaymentBlock').show();
        $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '17 Оплатить_онлайн_вход_Оплата']);
	});

    // клик по "оплатить онлайн"
    $orderContent.on('click', '.jsOnlinePaymentSpan', function(e){
        $(this).parent().siblings('.jsOnlinePaymentList').show();
        $body.trigger('trackUserAction', ['17 Оплатить_онлайн_вход_Оплата']);
        e.stopPropagation();
    });

    $orderContent.on('click', '.jsOnlinePaymentBlock', function(e) {
        if ($(this).find('.jsOnlinePaymentList').length == 0) $(this).siblings('.jsOnlinePaymentList').show();
        else $(this).find('.jsOnlinePaymentList').show();
        if ( $(this).find('.jsCreditList').length != 0 )  $(this).find('.jsCreditList').show();
        e.stopPropagation();
    });

    // клик на кнопке "Заполнить заявку"
    $orderContent.on('click', '.jsCreditButton', function(e){
        $(this).siblings('.jsCreditList').show();
        e.preventDefault();
        e.stopPropagation();
        $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19 Заявка_кредит_Оплата']);
    });

    // клик по кредитному банку
    $orderContent.on('click', '.jsCreditList li', function(e){
        var bankProviderId = $(this).data('bank-provider-id'),
            bank_id = $(this).data('value'),
            creditData = $(this).parent().siblings('.credit-widget').data('value'),
            order_number_erp = $(this).closest('.orderLn').data('order-number-erp');

		if (typeof order_number_erp == 'undefined') order_number_erp = $orderContent.data('order-number-erp');

        /* При клике условия кредитования */
        if ( $(e.target).hasClass('jsCreditListOnlineMotivRules') ) {
            $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19_2 Условия_кредит_Оплата']);
            return true;
        }

		e.preventDefault();
        e.stopPropagation();

        if (!$(this).closest('ul').hasClass('jsCreditListOnlineMotiv')) $(this).parent().hide();
        showCreditWidget(bankProviderId, creditData, order_number_erp, bank_id);
    });

    $body.on('click', function(){
        if (window.location.pathname == '/order/complete') $('.popupFl').hide();
    });

    // выполняем данный блок только на финальной странице
    if (/order\/complete/.test(window.location.href)) {

        /* АНАЛИТИКА МОТИВАЦИИ ОНЛАЙН-ОПЛАТЫ */
        if (isOnlineMotivPage) {
            // если невозможна онлайн-оплата
            if ($('.jsGAOnlinePaymentNotPossible').length > 0) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '16 Вход_Оплата_ОБЯЗАТЕЛЬНО', 'нет онлайн оплаты']);
            // Без мотиватора
            if ($('.jsOnlinePaymentPossibleNoMotiv').length > 0) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '16 Вход_Оплата_ОБЯЗАТЕЛЬНО', 'нет мотиватора']);
            // При попадании пользователя на экран “Варианты оплаты онлайн”
            if ($('.jsOnlinePaymentBlockVisible').length > 0) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '17 Оплатить_онлайн_вход_Оплата']);
            // При попадании на экран с вариантами заявок на кредит */
            if ($('.jsCreditBlock').length > 0) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19 Заявка_кредит_Оплата']);
            // При клике на ссылку “как добраться”
            $body.on('click', '.jsCompleteOrderShowShop', function(){ $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '16_1 Как_добраться']); })
        } else {
            $body.trigger('trackUserAction', ['16 Вход_Оплата_ОБЯЗАТЕЛЬНО']);
        }

        // При успешной онлайн-оплате
        if ($('.jsOrderPaid').length > 0) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '18 Успешная_Оплата']);
    }

    if ($jsOrder.length != 0) {
		if (typeof ENTER.utils.sendOrderToGA == 'function') ENTER.utils.sendOrderToGA($jsOrder.data('value'));
    }

	$(function(){
		var data = $('.js-orderV3New-complete-subscribe').data('value');

		if (data && data.subscribe && data.email) {
			$body.trigger('trackGoogleEvent', {
				category: 'subscription',
				action: 'subscribe_order_confirmation',
				label: data.email
			});
		}
	});
}(jQuery));