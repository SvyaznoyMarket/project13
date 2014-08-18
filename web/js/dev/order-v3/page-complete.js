;(function($){
    var $orderContent = $('.orderCnt');

    // клик по "оплатить онлайн"
    $orderContent.on('click', '.jsOnlinePaymentSpan', function(){
        $(this).parent().siblings('.jsOnlinePaymentList').show();
    });

}(jQuery));