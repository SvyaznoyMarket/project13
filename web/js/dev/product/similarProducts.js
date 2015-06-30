;$(function() {
	$('.js-product-similarProducts-link').on('click', function() {
		$('body').trigger('trackGoogleEvent', {
			category: 'RR_взаимодействие',
			action: 'Перешел на карточку товара',
			label: 'SEO'
		});
	});
});