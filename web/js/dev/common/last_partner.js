;$(document).ready(function(){
	// при любом клике на странице
	$(document.body).on('click', function(){
		var last_p = window.last_partner_second_click;
		// ставим куку last_partner на 30 дней, если есть переменная window.last_partner_second_click
		if (typeof last_p != 'undefined') {
			docCookies.setItem(
				'last_partner',
				last_p,
				60 * 60 *24 *30,
				'/'
			);
		}
	})
});