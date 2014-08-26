;(function($){
    var $orderContent = $('.orderCnt');

    // клик по методу онлайн-оплаты
    $orderContent.on('click', '.jsPaymentMethod', function(){
        var id = $(this).data('value'),
            $order = $(this).closest('.orderLn');
        switch (id) {
            case 5: $order.find('.jsPaymentFormPSB').trigger('submit'); break;
            case 8: $order.find('.jsPaymentFormPSBInvoice').trigger('submit'); break;
            case 13:
                if (typeof $order.find('.jsPaymentFormPaypal').attr('action') != 'undefined') {
                    window.location.href = $order.find('.jsPaymentFormPaypal').attr('action');
                } else {
                    // TODO error popup
                }
                break;
        }
    });

    // клик по "оплатить онлайн"
    $orderContent.on('click', '.jsOnlinePaymentSpan', function(){
        $(this).parent().siblings('.jsOnlinePaymentList').show();
    });



}(jQuery));