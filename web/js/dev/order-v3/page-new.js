(function($) {
    var $body = $(document.body),
        $orderContent = $('.orderCnt'),
        $inputs = $orderContent.find('input');

    // jQuery masked input
    $.mask.definitions['x']='[0-9]';
    $.mask.placeholder= "_";
    $.map($inputs, function(elem, i) {
        if (typeof $(elem).data('mask') !== 'undefined') $(elem).mask($(elem).data('mask'));
    });

    // переключение бонусных карт
    $orderContent.on('click', '.bonusCnt_i', function(e) {
        e.stopPropagation();

        var $elem = $(this),
            eq = $elem.data('eq'),
            $cardsDescriptions = $('.bonusCnt_it');

        if ($elem.hasClass('bonusCnt_i-act')) return;

        $('.bonusCnt_i').removeClass('bonusCnt_i-act');
        $elem.addClass('bonusCnt_i-act');
        $cardsDescriptions.hide().eq(eq).show();
    });

    // АНАЛИТИКА

    $body.on('focus', '.jsOrderV3PhoneField', function(){
        $body.trigger('trackUserAction',['1 Телефон_Получатель_ОБЯЗАТЕЛЬНО'])
    });

    $body.on('focus', '.jsOrderV3EmailField', function(){
        $body.trigger('trackUserAction',['2 Email_Получатель'])
    });

    $body.on('focus', '.jsOrderV3NameField', function(){
        $body.trigger('trackUserAction',['3 Имя_Получатель_ОБЯЗАТЕЛЬНО'])
    });

    $body.on('focus', '.jsOrderV3BonusCardField', function(){
        $body.trigger('trackUserAction',['4 Начислить_баллы_Получатель'])
    });

    $body.on('click', '.jsOrderV3AuthLink', function(){
        $body.trigger('trackUserAction',['5 Войти_с_паролем_Получатель'])
    });

    if (/orde(r|rs)\/new/.test(window.location.href)) {
        $body.trigger('trackUserAction', ['1 Вход_Получатель_ОБЯЗАТЕЛЬНО']);
    }

})(jQuery);