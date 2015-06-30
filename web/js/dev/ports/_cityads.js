/**
 * CityAds counter
 */
ANALYTICS.xcntmyAsync = function () {
    var
        elem = $('#xcntmyAsync'),
        data = elem ? elem.data('value') : false,
        page = data ? data.page : false,
    // end of vars

        init = function() {
            $LAB.script('//x.cnt.my/async/track/?r=' + Math.random())
        },

        cart = function() {
            window.xcnt_basket_products = data.productIds; 			// где XX,YY,ZZ – это ID товаров в корзине через запятую.
            window.xcnt_basket_quantity = data.productQuantities;	// где X,Y,Z – это количество соответствующих товаров (опционально).
        },

        complete = function() {
            window.xcnt_order_products = data.productIds;			// где XX,YY,ZZ – это ID товаров в корзине через запятую.
            window.xcnt_order_quantity = data.productQuantities;	// где X,Y,Z – это количество соответствующих товаров (опционально).
            window.xcnt_order_id = data.orderId;					// где XXXYYY – это ID заказа (желательно, можно  шифровать значение в MD5)
            window.xcnt_order_total = data.orderTotal;				// сумма заказа (опционально)
        },

        product = function() {
            window.xcnt_product_id = data.productId;				// где ХХ – это ID товара в каталоге рекламодателя.
        }
        ;// end of functions


    if ( 'cart' === page ) {
        cart();
    } else if ( 'order.complete' === page ) {
        complete();
    } else if ( 'product' === page ) {
        product();
    }
    init();
};
