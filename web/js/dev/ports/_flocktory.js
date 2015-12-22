ANALYTICS.flocktoryScriptJS = function(siteId) {
    /** Событие добавления/удаления корзины */
    $body.on('addtocart removeFromCart', function(event, data) {
        if ($.isArray(data.setProducts)) {
            $.each(data.setProducts, function (i,product) {
                ENTER.utils.analytics.flocktory.send({
                    action: product.quantityDelta > 0 ? 'addToCart' : 'removeFromCart',
                    item: {
                        id: product.id,
                        price: product.price,
                        count: Math.abs(product.quantityDelta)
                    }
                });
            });
        }
    });

    // загружаем скрипт flocktory
    $LAB.script('//api.flocktory.com/v2/loader.js?site_id=' + siteId);
};