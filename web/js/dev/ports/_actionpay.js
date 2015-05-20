ANALYTICS.ActionPayJS = function (data) {

    var basketEvents = function ( pageType, product ) {

            var aprData = {pageType: pageType};

            if ( typeof(window.APRT_SEND) === 'undefined' || typeof(product) === 'undefined' ) {
                return false;
            }

            aprData.currentProduct = {
                id: product.id,
                name: product.name,
                price: product.price
            };
            window.APRT_SEND(aprData);
        },
        addToBasket = function (event, data) {
            basketEvents(8, data.product);
        },
        remFromBasket = function (event, product) {
            basketEvents(9, product);
        };

    $body.on('addtocart', addToBasket);
    $body.on('removeFromCart', remFromBasket);

    if ( typeof(data) === 'undefined' ) data = {pageType : 0};

    window.APRT_DATA = data;

    $LAB.script('//aprtx.com/code/enter/');

};