ANALYTICS.flocktoryScriptJS = function(siteId) {

    var userconfig = $('.js-userConfig').data('value') || {},
        $dataLayerDiv = $('.js-flocktory-data-layer');

    // Дополняем data layer дополнительным данными о пользователе
    if ($dataLayerDiv.length) {
        if (userconfig.user && userconfig.user.email) {
            $dataLayerDiv.data('fl-user-email', userconfig.user.email)
        }
        if (userconfig.user && userconfig.user.name) {
            $dataLayerDiv.data('fl-user-name', userconfig.user.name)
        }
        if (userconfig.user && !userconfig.user.isEnterprizeMember) {
            $dataLayerDiv.data('fl-action', 'precheckout').data('fl-spot', 'no_enterprize_reg')
        }
    }

    console.log('flocktory precheckout data', $dataLayerDiv.data());

    /** Событие добавления/удаления корзины */
    $body.on('addtocart removeFromCart', function flocktoryCartEvent(event, data) {

        var action = (event.type == 'addtocart' ? 'addToCart' : 'removeFromCart');

        if ($.isArray(data.setProducts)) {
            $.each(data.setProducts, function (i,product) {

                var item = {
                    id: product.id,
                    price: product.price
                };

                if (action == 'addToCart') item.count = product.quantity;

                window.flocktory.push([action, {
                    item: item
                }]);

            });
        } else if (action == 'removeFromCart') {

            var itemR = {
                id: data[0].id,
                price: data[0].price
            };

            window.flocktory.push([action, {
                item: itemR
            }]);
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