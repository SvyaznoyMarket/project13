/**
 * Аналитика просмотра карточки товара
 *
 * @requires jQuery
 */
(function() {
	var productInfo = {},
		toKISS = {};
	// end of vars
	
	if ( !$('#jsProductCard').length ) {
		return false;
	}

	productInfo = $('#jsProductCard').data('value');
			
	toKISS = {
		'Viewed Product SKU': productInfo.article,
		'Viewed Product Product Name': productInfo.name,
		'Viewed Product Product Status': productInfo.stockState
	};

	if ( typeof _kmq !== 'undefined' ) {
		_kmq.push(['record', 'Viewed Product', toKISS]);
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
					'Recommended Item Clicked Also Bought Clicked SKU': data.article,
					'Recommended Item Clicked Also Bought Clicked Product Name': data.name,
					'Recommended Item Clicked Also Bought Product Position': data.position
				};

				if ( typeof _kmq !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Also Bought', toKISS]);
				}

			},

			alsoViewed: function( productData ) {
				console.log(productData);

				var toKISS = {
					'Recommended Item Clicked Also Viewed Recommendation Place': 'product',
					'Recommended Item Clicked Also Viewed Clicked SKU': data.article,
					'Recommended Item Clicked Also Viewed Clicked Product Name': data.name,
					'Recommended Item Clicked Also Viewed Product Position': data.position
				};

				if ( typeof _kmq !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Also Viewed', toKISS]);
				}
			}
		},

		sliderAnalytics = function sliderAnalytics() {
			console.info('click!');

			var sliderData = $(this).parents('.bGoodsSlider').data('slider'),
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

	$('.bGoodsSlider').on('click', '.bSliderAction__eItem', sliderAnalytics);
}());