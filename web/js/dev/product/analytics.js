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

	// SITE-5772
	if (query.sender && typeof query.sender.name == 'string' && query.sender.name.indexOf('filter') == 0) {
		$('body').trigger('trackGoogleEvent', {
			category: query.sender.name,
			action: 'product',
			label: query.sender.categoryUrlPrefix
		});
	}

	if ( typeof _gaq !== 'undefined' ) {
		// GoogleAnalitycs for review click
		$( 'a.reviewLink.yandex' ).each(function() {
			$(this).one( "click", reviewsYandexClick); // переопределяем только первый клик
		});
	}

	try {
		if ('out of stock' === product.stockState) {
			$('body').trigger('trackGoogleEvent', {
				category: 'unavailable_product',
				action: $.map(product.category, function(category) { return category.name; }).join('_'),
				label: product.barcode + '_' + product.article
			});
		}
	} catch (error) { console.error(error); }

	// SITE-5466
	(function() {
		if (!ENTER.config || !ENTER.config.pageConfig || !ENTER.config.pageConfig.product) {
			return;
		}

		var
			productUi = ENTER.config.pageConfig.product.ui,
			avgScore = ENTER.config.pageConfig.product.avgScore,
			firstPageAvgScore = ENTER.config.pageConfig.product.firstPageAvgScore,
			categoryName = ENTER.config.pageConfig.product.category.name,
			$window = $(window),
			$reviews = $('.jsReviewsList')
		;

		var timer;
		function checkReviewsShowing() {
			var windowHeight = $window.height();
			if ($window.scrollTop() + windowHeight > $reviews.offset().top) {
				if (!timer) {
					timer = setTimeout(function() {
						$window.unbind('scroll', checkReviewsShowing);

						$body.trigger('trackGoogleEvent', {
							category: 'Items_review',
							action: 'All_' + avgScore + '_Top_' + firstPageAvgScore,
							label: categoryName
						});

						ENTER.utils.analytics.reviews.add(productUi, avgScore, firstPageAvgScore, categoryName);
					}, 2000);
				}
			} else {
				if (timer) {
					clearTimeout(timer);
					timer = null;
				}
			}
		}

		$window.scroll(checkReviewsShowing);
		checkReviewsShowing();
	})();
});