ANALYTICS.GetIntentJS = function(){
    var data = $('#GetIntentJS').data('value');

    ENTER.counters.callGetIntentCounter({
        type: "VIEW",
        productId: data.productId,
        productPrice: data.productPrice,
        categoryId: data.categoryId
    });

    if (data.orders) {
        $.each(data.orders, function(index, order) {
            ENTER.counters.callGetIntentCounter({
                type: "CONVERSION",
                orderId: order.id,
                orderProducts: order.products,
                orderRevenue: order.revenue
            });
        });
    }
};