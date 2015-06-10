/**
 * @author		Zaytsev Alexandr
 */
(function(ENTER) {
	var $body = $('body');

	// Обработчик для кнопок купить
	$body.on('click', '.jsBuyButton', function(e) {
		var $button = $(e.currentTarget);

        $body.trigger('TL_buyButton_clicked');

		if ( $button.hasClass('mDisabled') ) {
			//return false;
            e.preventDefault();
		}

		if ( $button.hasClass('mBought') ) {
			document.location.href($button.attr('href'));
			//return false;
            e.preventDefault();
		}

		$button.addClass('mLoading');

		var
			url = $button.attr('href'),
			sender = ENTER.utils.analytics.productPageSenders.get($button),
			sender2 = ENTER.utils.analytics.productPageSenders2.get($button)
		;

		if (sender && JSON.stringify(sender) != JSON.stringify($button.data('sender'))) {
			for (var key in sender) {
				if (sender.hasOwnProperty(key)) {
					url = ENTER.utils.setURLParam('sender[' + key + ']', sender[key], url);
				}
			}
		}

		if (sender2 && sender2 != $button.data('sender2')) {
			url = ENTER.utils.setURLParam('sender2', sender2, url);
		}

		// Добавление в корзину на сервере. Получение данных о покупке и состоянии корзины. Маркировка кнопок.
		$.ajax({
			url: url,
			type: 'GET',
			success: function(data) {
				var
					upsale = $button.data('upsale') ? $button.data('upsale') : null,
					product = $button.parents('.jsSliderItem').data('product');

				if (!data.success) {
					return;
				}

				$button.removeClass('mLoading');

				if (data.product) {
					data.product.isUpsale = product && product.isUpsale ? true : false;
					data.product.fromUpsale = upsale && upsale.fromUpsale ? true : false;
				}

				data.location = $button.data('location');

				$body.trigger('addtocart', [data, upsale]);
			},
			error: function() {
				$button.removeClass('mLoading');
			}
		});

		//return false;
        e.preventDefault();
	});

	// analytics
	$body.on('addtocart', function(event, data){
		var
			/**
			 * Google Analytics аналитика добавления в корзину
			 */
				googleAnalytics = function googleAnalytics( event, data ) {
				var productData = data.product;

				var
					tchiboGA = function() {
						if (typeof window.ga === "undefined" || !productData.hasOwnProperty("isTchiboProduct") || !productData.isTchiboProduct) {
							return;
						}

						console.log("TchiboGA: tchiboTracker.send event Add2Basket product [%s, %s]", productData.name, productData.article);
						ga("tchiboTracker.send", "event", "Add2Basket", productData.name, productData.article);
					};
				// end of functions

				if ( !productData || typeof _gaq === 'undefined' ) {
					return;
				}

				tchiboGA();

				ENTER.utils.sendAdd2BasketGaEvent(productData.article, productData.price, productData.isOnlyFromPartner, productData.isSlot, data.sender ? data.sender.name : '');

                try {
                    var sender = data.sender;
                    console.info({sender: sender});
                    if (sender && ('retailrocket' == sender.name)) {
						var rrEventLabel = '';
						if (ENTER.config.pageConfig.product) {
							if (ENTER.config.pageConfig.product.isSlot) {
								rrEventLabel = '(marketplace-slot)';
							} else if (ENTER.config.pageConfig.product.isOnlyFromPartner) {
								rrEventLabel = '(marketplace)';
							}
						}

                        $body.trigger('trackGoogleEvent',['RR_взаимодействие ' + rrEventLabel, 'Добавил в корзину', sender.position]);
                    }
                } catch (e) {
                    console.error(e);
                }
			},

			/**
			 * Обработчик добавления товаров в корзину. Рекомендации от RetailRocket
			 */
				addToRetailRocket = function addToRetailRocket( event, data ) {
				var product = data.product;


				if ( typeof rcApi === 'object' ) {
					try {
						rcApi.addToBasket(product.id);
					}
					catch ( err ) {}
				}
			};
		//end of functions

		try{
			if (data.product) {
				googleAnalytics(event, data);
				addToRetailRocket(event, data);
			}

			if (data.products) {
				console.groupCollapsed('Аналитика для набора продуктов');
				for (var i in data.products) {
					/* Google Analytics */
					googleAnalytics(event, $.extend({}, data, {product: data.products[i]}));
				}
				console.groupEnd();
			}
		}
		catch( e ) {
			console.warn('addtocartAnalytics error');
			console.log(e);
		}
	});
}(window.ENTER));
