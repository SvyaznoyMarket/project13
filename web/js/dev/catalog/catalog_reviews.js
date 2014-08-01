/**
 * Обработчик для sprosikupi
 */
/*$(function() {
	if (!$('.spk-good-rating').length) {
		return;
	}

	$.getScript("//static.sprosikupi.ru/js/widget/sprosikupi.bootstrap.js");
	$('body').on('catalogLoadingComplete', function(){
		spkInit()
	});
});*/

/**
 * Обработчик для shoppilot
 */
$(function() {
	if (!$('.shoppilot-category-container').length) {
		return;
	}

	_shoppilot = window._shoppilot || [];
	_shoppilot.push(['_setStoreId', '535a852cec8d830a890000a6']);
	_shoppilot.push(['_setOnReady', function (Shoppilot) {
		function initWidgets() {
			$('.shoppilot-category-container').each(function() {
				var ratingContainer = $(this);
				(new Shoppilot.ProductWidget({
					name: 'category-product-rating',
					styles: 'product-reviews',
					product_id: ratingContainer.data('productId')
				})).appendTo(ratingContainer[0]);
			});
		}

		initWidgets();
		$('body').on('catalogLoadingComplete', initWidgets);
	}]);

	(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.async = true;
		script.src = '//ugc.shoppilot.ru/javascripts/require.js';
		script.setAttribute('data-main',
			'//ugc.shoppilot.ru/javascripts/social-apps.js');
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(script, s);
	})();
});
