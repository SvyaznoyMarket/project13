/**
 * Google Analytics steps tracking
 *
 * @author Zhukov Roman
 * @requires jQuery
 */

;(function(){
    var // variables
        w = window,
        _gaq = w._gaq || [],
        ga = w[w['GoogleAnalyticsObject']],
        $ = w.jQuery,
        body = $(document.body),
        region = $('#jsDeliveryAddress').data('value')['regionName'],

        // functions
        sendAnalytic = function sendAnalyticF (event, step, action) {
            var act = action || '',
                st = step || '',
				oneClickOrder = ENTER.config.pageConfig.currentRoute == 'order.oneClick.new',
				categoryPrefix = 'воронка_';

			if (oneClickOrder) categoryPrefix += 'старый_1_клик_';

            if (event && event.data) {
                if (event.data.step) st = event.data.step;
                if (event.data.action) act = event.data.action;
            }

            if (typeof ga === 'undefined') ga = window[window['GoogleAnalyticsObject']]; // try to assign ga

            // sending
            if (typeof _gaq === 'object') _gaq.push(['_trackEvent', categoryPrefix + region, st, act]);
            if (typeof ga === 'function') ga('send', 'event', categoryPrefix + region, st, act);

            // log to console
            console.log('[Google Analytics] Step "%s" sended with action "%s" for %s', st, act, categoryPrefix + region);
        };

    console.log('[Init] Google Analytics Tracking');

    // common listener for triggering from another files or functions
    body.on('trackUserAction.orderTracking', sendAnalytic);

    body.on('click.orderTracking', 'a.bBackCart', function(e) {
        if ( $(e.target).hasClass('mOrdeRead') ) body.trigger('trackUserAction', ['3 Редактировать товары']);
        else body.trigger('trackUserAction', ['1_3 Доставка, ушел в корзину']);
    });

    body.on('click.orderTracking', 'a#auth-link', {step: '4_0 Авторизация'}, sendAnalytic);

    body.on('focusin.orderTracking', function(e) {
        var $target = $(e.target);
        switch ( $target.attr('id') ) {
            case 'order_recipient_first_name':
                body.trigger('trackUserAction', ['4_1 Имя']); break;
            case 'order_recipient_last_name':
                body.trigger('trackUserAction', ['4_2 Фамилия']); break;
            case 'order_recipient_email':
                body.trigger('trackUserAction', ['4_3 Email']); break;
            case 'order_recipient_phonenumbers':
                body.trigger('trackUserAction', ['4_4 телефон']);
                for (var i in ENTER.OrderModel.deliveryBoxes()) {
                    if (/standart/.test(ENTER.OrderModel.deliveryBoxes()[i].state)) {
                        body.trigger('trackUserAction', ['4_5_1 ЛД Доставка - Адрес']);
                        break;
                    }
                }
                break;
            case 'order_address_street':
                body.trigger('trackUserAction', ['4_5_2 ЛД Доставка - Улица']); break;
            case 'order_address_building':
                body.trigger('trackUserAction', ['4_5_3 ЛД Доставка - Дом']); break;
            case 'order_address_metro':
                body.trigger('trackUserAction', ['4_5_4 ЛД Доставка - Метро']); break;
            case 'bonus-card-number':
                body.trigger('trackUserAction', ['5 Связной-клуб']); break;
        }
    });

    body.on('click.orderTracking', '.mPayMethods .bCustomLabel', function(e){
        body.trigger('trackUserAction', ['6 Тип оплаты', $(e.target).text()]);
    });

    body.on('click.orderTracking', '.mRules .bCustomLabel', function(e) {
        var $target = $(e.target);
        if ($target.hasClass('bCustomLabel')) body.trigger('trackUserAction', ['7 Согласие']);
        if ($target.attr('href') == '/terms') body.trigger('trackUserAction', ['7_1 Условия']);
        if ($target.attr('href') == '/legal') body.trigger('trackUserAction', ['7_2 Право']);
    });

    // Time interval change
    body.on('focus', 'select.bSelect', function() {
        var oldIndex = $(this).prop('selectedIndex');
        $(this).off('blur').on('blur', function(){
            var diff = oldIndex - $(this).prop('selectedIndex');
            if (diff == 0) body.trigger('trackUserAction', ['1_4_2 Смена времени', 'оставил']);
            else body.trigger('trackUserAction', ['1_4_2 Смена времени', 'сменил']);
        })
    });

    // initial trigger
    body.trigger('trackUserAction', ['0 Вход'])

})();