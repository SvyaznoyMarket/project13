;(function($){
    var $body = $('body'),
        getForm = function getFormF(methodId, orderId, orderNumber, action) {
            var data = {
                'method' : methodId,
                'order': orderId,
                'number': orderNumber
            };
            if (typeof action !== 'undefined' && action != '') data.action = action;
            $.ajax({
                'url': '/order/getPaymentForm',
                'type': 'POST',
                'data': data,
                'success': function(data) {
                    var $form;
                    if (data.form != '') {
                        $form = $(data.form);

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
        };

    // Онлайн-оплата
    $body.on('click', '.jsOnlinePaymentPossible', function(){
        $('.jsOnlinePaymentPossible').hide();
        $('.jsOnlinePaymentBlock').show();
    });

    // клик по методу онлайн-оплаты
    $body.on('click', '.jsPaymentMethod', function(e){
        var id = $(this).data('value'),
            $order = $(this).closest('.jsOneClickCompletePage'),
            orderId = $order.data('order-id'),
            orderNumber = $order.data('order-number');
        e.preventDefault();
        getForm(id, orderId, orderNumber);
    });

})(jQuery);