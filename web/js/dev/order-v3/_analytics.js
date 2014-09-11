;(function($) {

    var body = $(document.body),
        _gaq = window._gaq,
        region = '', // TODO

        sendAnalytic = function sendAnalyticF (category, action, label, value) {
        var lbl = label || '',
            act = action || '';

        if (category && category.data) {
            if (category.data.step) act = category.data.step;
            if (category.data.action) lbl = category.data.action;
        }

        if (typeof ga === 'undefined') ga = window[window['GoogleAnalyticsObject']]; // try to assign ga

        // sending
        if (typeof _gaq === 'object') _gaq.push(['_trackEvent', 'воронка_' + region, act, lbl]);
        if (typeof ga === 'function') ga('send', 'event', 'воронка_' + region, act, lbl);

        // log to console
        console.log('[Google Analytics] Step "%s" sended with action "%s" for воронка_%s', act, lbl, region);
    };

    // common listener for triggering from another files or functions
    body.on('trackUserAction.orderV3Tracking', sendAnalytic);

    // SITE-4330 Пометка аудиторий для эксперимента
    // Код  _gaq.push(['_setCustomVar']); необходимо выполнять до кода _gaq.push(['_trackPageview']);
    /*if (typeof _gaq === 'object') {
        _gaq.push(['_setCustomVar', 11, 'Order_Experiment', 'New', 2 ]);
        console.log('[Google Analytics] SetCustomVar 11 Order_Experiment New 2');
    }*/

})(jQuery);