/**
 * Аналитика просмотра карточки товара
 *
 * @requires jQuery
 */
(function() {
	var
		productInfo = $('#jsProductCard').data('value') || {},
		toKISS = {
			'Viewed Product SKU': productInfo.article,
			'Viewed Product Product Name': productInfo.name,
			'Viewed Product Product Status': productInfo.stockState
		},
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

	if ( typeof _kmq !== 'undefined' ) {
		_kmq.push(['record', 'Viewed Product', toKISS]);
	}
	if ( typeof _gaq !== 'undefined' ) {
		// GoogleAnalitycs for review click
		$( 'a.reviewLink.yandex' ).each(function() {
			$(this).one( "click", reviewsYandexClick); // переопределяем только первый клик
		});
	}
})();


/**
 * Аналитика для слайдеров
 */
(function() {
		/**
		 * Аналитика по типу слайдера
		 *
		 * @param	{Object}	productData	Данные о продукте на который произошел клик
		 */
	var trackAs = {
			accessorize: function( productData ) {
				console.log(productData);

				var toKISS = {
					'Recommended Item Clicked Accessorize Recommendation Place': 'product',
					'Recommended Item Clicked Accessorize Clicked SKU': productData.article,
					'Recommended Item Clicked Accessorize Clicked Product Name': productData.name,
					'Recommended Item Clicked Accessorize Product Position': productData.position
				};

				if ( typeof _kmq !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Accessorize', toKISS]);
				}
			},

			alsoBought: function( productData ) {
				console.log(productData);

				var toKISS = {
					'Recommended Item Clicked Also Bought Recommendation Place': 'product',
					'Recommended Item Clicked Also Bought Clicked SKU': productData.article,
					'Recommended Item Clicked Also Bought Clicked Product Name': productData.name,
					'Recommended Item Clicked Also Bought Product Position': productData.position
				};

				if ( typeof _kmq !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Also Bought', toKISS]);
				}

			},

			alsoViewed: function( productData ) {
				console.log(productData);

				var toKISS = {
					'Recommended Item Clicked Also Viewed Recommendation Place': 'product',
					'Recommended Item Clicked Also Viewed Clicked SKU': productData.article,
					'Recommended Item Clicked Also Viewed Clicked Product Name': productData.name,
					'Recommended Item Clicked Also Viewed Product Position': productData.position
				};

				if ( typeof _kmq !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Also Viewed', toKISS]);
				}
			}
		},

		sliderAnalytics = function sliderAnalytics() {
			console.info('click!');

			var sliderData = $(this).parents('.js-slider').data('slider'),
				sliderType = sliderData.type,

				productData = $(this).data('product');
			// end of vars
			
			console.log(sliderType);
			productData.position = $(this).index();

			if ( trackAs.hasOwnProperty(sliderType) ) {
				trackAs[sliderType](productData);
			}
		};
	// end of functions

	$('.js-slider').on('click', '.bSliderAction__eItem', sliderAnalytics);
}());