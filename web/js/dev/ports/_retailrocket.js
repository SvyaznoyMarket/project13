ANALYTICS.RetailRocketJS = function() {
    console.groupCollapsed('ports.js::RetailRocketJS');

    rrPartnerId = "519c7f3c0d422d0fe0ee9775"; // rrPartnerId — по ТЗ должна быть глобальной
    rrApi = {};
    rrApiOnReady = [];
    rrApi.addToBasket = rrApi.order = rrApi.categoryView = rrApi.view = rrApi.recomMouseDown = rrApi.recomAddToCart = function() {};

    console.info('RetailRocketJS init');

    (function (d) {
        var
            ref = d.getElementsByTagName( 'script' )[0],
            apiJs,
            apiJsId = 'rrApi-jssdk';

        if ( d.getElementById( apiJsId ) ) return;
        apiJs = d.createElement( 'script' );
        apiJs.id = apiJsId;
        apiJs.async = true;
        apiJs.src = "//cdn.retailrocket.ru/content/javascript/tracking.js";
        ref.parentNode.insertBefore( apiJs, ref );
    }( document ));

    // SITE-3672. Передаем email пользователя для RetailRocket
    (function() {
        var
            rr_data = $('#RetailRocketJS').data('value'),
            email,
            cookieName;
        // end of vars

        if ( 'object' != typeof(rr_data) || !rr_data.hasOwnProperty('emailCookieName') ) {
            return;
        }

        cookieName = rr_data.emailCookieName;

        email = window.docCookies.getItem(cookieName);
        if ( !email ) {
            return;
        }

        console.info('RetailRocketJS userEmailSend');
        console.log(email);

        rrApiOnReady.push(function () {
            rrApi.setEmail(email);
        });

        window.docCookies.removeItem(cookieName, '/');
    })();

    // Вызываем счётчик для заданных в HTML коде параметров
    (function() {
        try {
            var rr_data = $('#RetailRocketJS').data('value');

            if (rr_data && rr_data.routeName && rr_data.sendData) {
                $.each(rr_data.sendData, function(index, data) {
                    ENTER.counters.callRetailRocketCounter(rr_data.routeName, data);
                });
            }
        } catch (err) {}
    })();

    console.groupEnd();
};