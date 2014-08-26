;(function($){
    var body = document.getElementsByTagName('body')[0],
        $orderContent = $('.orderCnt'),
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
                            $(data.form).submit();
                        }
                    }
                    console.log('Payment data', data);

                }
            })
        };

    // клик по методу онлайн-оплаты
    $orderContent.on('click', '.jsPaymentMethod', function(){
        var id = $(this).data('value'),
            $order = $(this).closest('.orderLn'),
            orderId = $order.data('order-id'),
            orderNumber = $order.data('order-number');
        switch (id) {
            case 5: getForm(5, orderId, orderNumber); break;
            case 8: getForm(8, orderId, orderNumber); break;
            case 13: getForm(13, orderId, orderNumber); break;
        }
    });

    // клик по "оплатить онлайн"
    $orderContent.on('click', '.jsOnlinePaymentSpan', function(){
        $(this).parent().siblings('.jsOnlinePaymentList').show();
    });



}(jQuery));