;$(document).ready(function(){
	// при любом клике на странице
	$(document.body).on('click', function(){
		// ставим куку last_partner на 30 дней, если есть кука last_partner_sec_click
		// осторожно, названия кук захардкожены :(
		if (docCookies.hasItem('last_partner_sec_click')) {
			docCookies.setItem(
				'last_partner',
				docCookies.getItem('last_partner_sec_click'),
				60 * 60 *24 *30,
				'/'
			);
		}
	})
});