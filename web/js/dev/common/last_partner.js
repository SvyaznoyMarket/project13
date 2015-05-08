;$(document).ready(function() {

    var partnerData = {
        lastPartner: '',
        cookie: []
    };

    try {
        partnerData = $.parseJSON($('#lastPartnerJSON').html());
    } catch (e) {
        console.error('Ошибка получения партнера')
    }

	// при любом клике на странице
	$(document.body).on('click', function(){
		// ставим куку last_partner на 30 дней
		if (partnerData.lastPartner) {
			docCookies.setItem(
				'last_partner',
                partnerData.lastPartner,
				60 * 60 *24 *30,
				'/'
			);
            console.info('[PARTNER] Установлен партнер %s', partnerData.lastPartner);
		}
        // и остальные куки партнеров
        $.each(partnerData.cookie, function(i,v) {
            docCookies.setItem(
                v['name'],
                v['value'],
                typeof v['time'] != 'undefined' ? v['time'] : 60 * 60 *24 *30,
                '/'
            );
            console.info('[PARTNER] Установлена кука партнера', v);
        })
	});

});