/**
 * Аналитика просмотра карточки товара
 *
 * @requires jQuery
 */
$(function() {
	var $productCardData = $('#jsProductCard');
	if (!$productCardData.length || $('body').data('template') != 'product_card') {
		return;
	}

	var
		product = $productCardData.data('value') || {},
		query = $.deparam((location.search || '').slice(1)),
		reviewsYandexClick = function ( e ) {
			console.log('reviewsYandexClick');
			var
				link = this //, url = link.href
			;

			if ( 'undefined' !==  product.article ) {
				_gaq.push(['_trackEvent', 'YM_link', product.article]);
				e.preventDefault();
				if ( 'undefined' !== link ) {
					setTimeout(function () {
						//document.location.href = url; // не подходит, нужно в новом окне открывать
						link.click(); // эмулируем клик по ссылке
					}, 500);
				}
			}
		};

	ENTER.utils.analytics.productPageSenders.add(product.ui, query.sender);
	ENTER.utils.analytics.productPageSenders2.add(product.ui, query.sender2);

	if ( typeof _gaq !== 'undefined' ) {
		// GoogleAnalitycs for review click
		$( 'a.reviewLink.yandex' ).each(function() {
			$(this).one( "click", reviewsYandexClick); // переопределяем только первый клик
		});
	}

	try {
		if ('out of stock' === product.stockState) {
			$('body').trigger('trackGoogleEvent', {
				action: 'unavailable_product',
				category: $.map(product.category, function(category) { return category.name; }).join('_'),
				label: product.barcode + '_' + product.article
			});
		}
	} catch (error) { console.error(error); }
});