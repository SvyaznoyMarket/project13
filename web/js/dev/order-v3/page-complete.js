;(function($){
    var body = document.getElementsByTagName('body')[0],
        $body = $(body),
        $orderContent = $('.orderCnt'),
        $jsOrder = $('#jsOrder'),
		region = $('.jsRegion').data('value'),
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
            })

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
            $order = $(this).closest('.orderLn'),
            orderId = $order.data('order-id'),
            orderNumber = $order.data('order-number');
        switch (id) {
            case 5:
                getForm(5, orderId, orderNumber);
                $body.trigger('trackUserAction', ['17_2 Оплатить_онлайн_Онлайн_Оплата']);
                break;
            case 8:
                getForm(8, orderId, orderNumber);
                $body.trigger('trackUserAction', ['17_3 Оплатить_онлайн_Электронный счёт PSB_Оплата']);
                break;
            case 13:
                getForm(13, orderId, orderNumber);
                $body.trigger('trackUserAction', ['17_1 Оплатить_онлайн_PayPal_Оплата']);
                break;
        }
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

    $orderContent.on('click', '.jsCreditButton', function(e){
        $(this).siblings('.jsCreditList').show();
        e.preventDefault();
        e.stopPropagation();
    });

    $orderContent.on('click', '.jsCreditList li', function(e){
        var bankProviderId = $(this).data('bank-provider-id'),
            bank_id = $(this).data('value'),
            creditData = $(this).parent().siblings('.credit-widget').data('value'),
            order_number_erp = $(this).closest('.orderLn').data('order-number-erp');
//        e.preventDefault();
        e.stopPropagation();
        $(this).parent().hide();
        showCreditWidget(bankProviderId, creditData, order_number_erp, bank_id);
    });

    $(body).on('click', function(){
        if (window.location.pathname == '/order/complete') $('.popupFl').hide();
    });

    if (/order\/complete/.test(window.location.href)) {
        $body.trigger('trackUserAction', ['16 Вход_Оплата_ОБЯЗАТЕЛЬНО']);
    }

    if ($jsOrder.length != 0) {
		ENTER.utils.sendOrderToGA($jsOrder.data('value'));
		ENTER.utils.analytics.reviews.clean(); // Должна вызываться, как мы договорились с Захаровым Николаем Викторовичем, лишь при оформлении заказа через обычное оформление заказа (не через одноклик или слоты).
    }

}(jQuery));