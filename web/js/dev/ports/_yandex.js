ANALYTICS.yandexOrderComplete = function() {
    try {
        var orderData = $('#jsOrder').data('value');
        if (typeof window.yandexCounter == 'undefined' || orderData == undefined) return;
        $.each(orderData.orders, function (index, order) {
            window.yandexCounter.reachGoal('ORDERCOMPLETE', {
                order_id: order.number,
                order_price: order.sum,
                currency: "RUR",
                goods: $.map(order.products, function (product) {
                    return {
                        id: product.id,
                        name: product.name,
                        price: product.price,
                        quantity: product.quantity
                    }
                })
            })
        });
        console.info('yandexOrderComplete reachGoal successfully sended');
    } catch (e) {
        console.error('yandexOrderComplete error', e);
    }
};
