/**
 * Аналитика просмотра карточки товара
 *
 * @requires jQuery
 */
(function() {
	var
		productInfo = $('#jsProductCard').data('value') || {},
	// end of vars

		reviewsYandexClick = function ( e ) {
			console.log('reviewsYandexClick');
			var
				link = this //, url = link.href
			;

			if ( 'undefined' !==  productInfo.article ) {
				_gaq.push(['_trackEvent', 'YM_link', productInfo.article]);
				e.preventDefault();
				if ( 'undefined' !== link ) {
					setTimeout(function () {
						//document.location.href = url; // не подходит, нужно в новом окне открывать
						link.click(); // эмулируем клик по ссылке
					}, 500);
				}
			}
		};
	// end of functions and vars
	
	if ( !$('#jsProductCard').length ) {
		return false;
	}

	if ( typeof _gaq !== 'undefined' ) {
		// GoogleAnalitycs for review click
		$( 'a.reviewLink.yandex' ).each(function() {
			$(this).one( "click", reviewsYandexClick); // переопределяем только первый клик
		});
	}
})();
