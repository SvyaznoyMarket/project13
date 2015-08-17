ANALYTICS.ActionPayJS = function (data) {
    function basketEvents(pageType, setProducts) {
        if (typeof(window.APRT_SEND) === 'undefined' || typeof(setProducts) === 'undefined') {
            return false;
        }
        
        $.each(setProducts, function(key, setProduct) {
            window.APRT_SEND({
                pageType: pageType,
                currentProduct: {
                    id: setProduct.id,
                    name: setProduct.name,
                    price: setProduct.price
                }
            });
        });
    }

    $body.on('addtocart', function(event, data) {
        basketEvents(8, data.setProducts);
    });

    $body.on('removeFromCart', function(event, setProducts) {
        basketEvents(9, setProducts);
    });

    if ( typeof(data) === 'undefined' ) data = {pageType : 0};

    window.APRT_DATA = data;

    $LAB.script('//aprtx.com/code/enter/');

};