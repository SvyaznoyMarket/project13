(function($) {
    var $body = $(document.body),
        $orderContent = $('.orderCnt'),
        $inputs = $orderContent.find('input'),
        analyticsInputs = [];

    // jQuery masked input
	delete $.mask.definitions[9];
    $.mask.definitions['x']='[0-9]';
    $.mask.placeholder= "_";
	$.mask.autoclear = false;
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
		$elem.find('.bonusCnt_tx_code .brb-dt').hide();
    });

	/* Подсказки бонусных карт */

	$body.on('mouseenter', '.jsShowBonusCardHint', function(){
		$(this).parent().siblings('.bonusCnt_popup').show()
	});

	$body.on('mouseleave', '.jsShowBonusCardHint', function(){
		$(this).parent().siblings('.bonusCnt_popup').hide()
	});

	$('.jsOrderV3BonusCardField').each(function(i,elem){
		$(elem).closest('.bonusCnt-v2').find('.bonusCnt_tx_code .brb-dt').eq(i).text($(elem).val())
	});

    // АНАЛИТИКА

    $body.on('focus', '.jsOrderV3PhoneField', function(){
        if ($.inArray(this, analyticsInputs) == -1) {
            $body.trigger('trackUserAction',['1 Телефон_Получатель_ОБЯЗАТЕЛЬНО']);
            analyticsInputs.push(this);
        }
    });

    $body.on('focus', '.jsOrderV3EmailField', function(){
        if ($.inArray(this, analyticsInputs) == -1) {
            $body.trigger('trackUserAction',['2 Email_Получатель']);
            analyticsInputs.push(this);
        }
    });

    $body.on('focus', '.jsOrderV3NameField', function(){
        if ($.inArray(this, analyticsInputs) == -1) {
            $body.trigger('trackUserAction', ['3 Имя_Получатель_ОБЯЗАТЕЛЬНО']);
            analyticsInputs.push(this);
        }
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

	// Если стоит галка на чекбоксе
	if ($('.jsOrderV3SubscribeCheckbox').is(':checked')) {
		docCookies.setItem('enter_wanna_subscribe', true, 0, '/'); // ставим куку на сессию
	}

	// Меняем куку по изменению "Подписаться на рассылку"
	$body.on('change', '.jsOrderV3SubscribeCheckbox', function(){
		docCookies.setItem('enter_wanna_subscribe', $(this).is(':checked'), 0, '/');
	});

	$('.jsOrderV3PhoneField').focus();

})(jQuery);