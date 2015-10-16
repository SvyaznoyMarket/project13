;(function($) {

    var body = $(document.body),
        _gaq = window._gaq,

        sendAnalytic = function sendAnalyticF (category, action, label, value) {
			var lbl = label || '',
				act = action || '';

			if (category && category.data) {
				if (category.data.step) act = category.data.step;
				if (category.data.action) lbl = category.data.action;
			}

			if (typeof ga === 'undefined') ga = window[window['GoogleAnalyticsObject']]; // try to assign ga

			// sending
			if (typeof _gaq === 'object') _gaq.push(['_trackEvent', 'Воронка_1 клик', act, lbl]);
			if (typeof ga === 'function') ga('send', 'event', 'Воронка_1 клик', act, lbl);

		};

    // common listener for triggering from another files or functions
    body.on('trackUserAction.orderV3Tracking', sendAnalytic);

})(jQuery);