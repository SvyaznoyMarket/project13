ANALYTICS.flocktoryScriptJS = function(siteId) {

    var userconfig = $('.js-userConfig').data('value') || {},
        $dataLayerDiv = $('.js-flocktory-data-layer');

    // Дополняем data layer дополнительным данными о пользователе
    if ($dataLayerDiv.length) {
        if (userconfig.user && userconfig.user.email) {
            $dataLayerDiv.attr('data-fl-user-email', userconfig.user.email)
        }
        if (userconfig.user && userconfig.user.name) {
            $dataLayerDiv.attr('data-fl-user-name', userconfig.user.name)
        }
        if (userconfig.user && !userconfig.user.isEnterprizeMember) {
            $dataLayerDiv.attr('data-fl-action', 'precheckout').attr('data-fl-spot', 'no_enterprize_reg')
        }
    }

    console.log('flocktory precheckout data', $dataLayerDiv.data());

    /** Событие добавления/удаления корзины */
    $body.on('addtocart removeFromCart', function flocktoryCartEvent(event, data) {

        if ($.isArray(data.setProducts)) {
            $.each(data.setProducts, function (i,product) {

                var action,
                    item = {
                        id: product.id,
                        price: product.price,
                        count: Math.abs(product.quantityDelta)
                };

                action = (product.quantityDelta > 0 ? 'addToCart' : 'removeFromCart');

                window.flocktory.push([action, {
                    item: item
                }]);

            });
        }
    });

    // загружаем скрипт flocktory
    $LAB.script('//api.flocktory.com/v2/loader.js?site_id=' + siteId);
};

ANALYTICS.flocktoryCompleteOrderJS = function() {
    var data = $('#flocktoryCompleteOrderJS').data('value');
    window.flocktory = window.flocktory || [];
    console.info('Flocktory data', data);
    window.flocktory.push(['postcheckout', data]);
};