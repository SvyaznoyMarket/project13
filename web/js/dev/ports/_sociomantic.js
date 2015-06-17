ANALYTICS.sociomanticJS = function () {
    $LAB.script('eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru' + (ENTER.config.pageConfig.isMobile ? '-m' : ''));
};

// финальная страница оформления заказа
ANALYTICS.sociomanticOrderCompleteJS = function() {
    var basket = {products: [], transaction: '', amount: 0.0, currency: 'RUB'},
        ordersData = $('#jsOrder').data('value');

    if (!ordersData) return;

    // пройдем по заказам
    $.each(ordersData.orders, function(i,order){
        // пройдем по продуктам
        $.each(order.products, function(ii,pr) {
            basket.products.push({identifier: pr.article + '_' + docCookies.getItem('geoshop'), amount: pr.price, currency: 'RUB', quantity: pr.quantity})
        });
        // если несколько заказов, то пишем их через дефис
        basket.transaction += i == 0 ? order.numberErp : ' - ' + order.numberErp;
        // если несколько заказов, то суммируем сумму
        basket.amount += parseInt(order.sum, 10);
    });
    window.sonar_basket = basket;
};

ANALYTICS.smanticPageJS = function() {
    (function(){
        console.log('smanticPageJS');
        var
            elem = $('#smanticPageJS'),
            prod = elem.data('prod'),
            prod_cats = elem.data('prod-cats'),
            cart_prods = elem.data('cart-prods');

        window.sonar_product = window.sonar_product || {};

        if ( prod ) {
            window.sonar_product = prod;
        }

        if ( prod_cats ) {
            window.sonar_product.category = prod_cats;
        }

        if ( cart_prods ) {
            window.sonar_basket = { products: cart_prods };
        }
    })();
};