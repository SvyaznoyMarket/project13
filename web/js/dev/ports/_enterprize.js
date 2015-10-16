/**
 * Аналитика на странице подтверждения email/телефона
 */
ANALYTICS.enterprizeConfirmJs = function () {
    var enterprize = $('#enterprizeConfirmJs'),
        data = enterprize.data('value');

    $body.trigger('trackGoogleEvent', ['Enterprize Token Request', 'Номер фишки', data.enter_id]);
};

/**
 * Аналитика на странице подтверждения /enterprize/complete
 */
ANALYTICS.enterprizeCompleteJs = function () {

    var enterprize = $('#enterprizeCompleteJs'),
        data = enterprize.data('value');

    $body.trigger('trackGoogleEvent', ['Enterprize Token Granted', 'Номер фишки', data.enter_id]);

    if (typeof ga != 'undefined') ga('set', '&uid', data.enter_id);
};

/**
 * Аналитика при регистрации в EnterPrize
 */
ANALYTICS.enterprizeRegAnalyticsJS = function() {
    $body.trigger('trackGoogleEvent', ['Enterprize Registration', 'true']);
};