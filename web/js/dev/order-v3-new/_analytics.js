;(function($) {

    var body = $(document.body),
        _gaq = window._gaq,
        region = $('.jsRegion').data('value'),
        saleAnalyticsData = $('.jsOrderSaleAnalytics').data('value'),
        value,
        i,

        sendAnalytic = function sendAnalyticF (category, action, label, value) {
        var lbl = label || '',
            act = action || '';

        if (category && category.data) {
            if (category.data.step) act = category.data.step;
            if (category.data.action) lbl = category.data.action;
        }

        if (typeof ga === 'undefined') ga = window[window['GoogleAnalyticsObject']]; // try to assign ga

        // sending
        if (typeof _gaq === 'object') _gaq.push(['_trackEvent', 'Воронка_новая_v2_' + region, act, lbl]);
        if (typeof ga === 'function') ga('send', 'event', 'Воронка_новая_v2_' + region, act, lbl);

        // log to console
        if (typeof ga !== 'function') console.warn('Нет объекта ga');
        if (typeof ga === 'function' && typeof ga.getAll == 'function' && ga.getAll().length == 0) console.warn('Не установлен трекер для ga');
        console.log('[Google Analytics] Send event: category: "Воронка_новая_v2_%s", action: "%s", label: "%s"', region, act, lbl);
    };

    // common listener for triggering from another files or functions
    body.on('trackUserAction.orderV3Tracking', sendAnalytic);

    // TODO вынести инициализацию трекера из ports.js
    if (typeof ga === 'function' && typeof ga.getAll == 'function' && ga.getAll().length == 0) {
        ga( 'create', 'UA-25485956-5', 'enter.ru' );
    }

    if (saleAnalyticsData) {
        $.each(saleAnalyticsData, function(i, value) {
            if ('object' === typeof value) {
                $('body').trigger('trackGoogleEvent', value);
            }
        })
    }

})(jQuery);