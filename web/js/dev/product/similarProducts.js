;$(function() {
	var $body = $('body');

	/** Событие клика на товар в слайдере */
	$('.js-product-similarProducts-link').on('click', function(event) {
		try {
			$body.trigger('trackGoogleEvent', {
				category: 'RR_взаимодействие',
				action: 'Перешел на карточку товара',
				label: 'SEO',
				hitCallback: $(this).attr('href')
			});

			event.stopPropagation();

		} catch (e) { console.error(e); }
	});
});