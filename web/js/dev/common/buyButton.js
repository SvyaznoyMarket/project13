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

		// Добавление в корзину на сервере. Получение данных о покупке и состоянии корзины. Маркировка кнопок.
		$.ajax({
			url: $button.attr('href'),
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
			 * KISS Аналитика для добавления в корзину
			 */
				kissAnalytics = function kissAnalytics( event, data ) {
				var productData = data.product,
					serviceData = data.service,
					warrantyData = data.warranty,
					nowUrl = window.location.href,
					toKISS = {};
				//end of vars

				if ( typeof _kmq === 'undefined' ) {
					return;
				}

				if ( productData ) {
					toKISS = {
						'Add to Cart SKU': productData.article,
						'Add to Cart SKU Quantity': productData.quantity,
						'Add to Cart Product Name': productData.name,
						'Add to Cart Root category': productData.category[0].name,
						'Add to Cart Root ID': productData.category[0].id,
						'Add to Cart Category name': ( productData.category ) ? productData.category[productData.category.length - 1].name : 0,
						'Add to Cart Category ID': ( productData.category ) ? productData.category[productData.category.length - 1].id : 0,
						'Add to Cart SKU Price': productData.price,
						'Add to Cart Page URL': nowUrl,
						'Add to Cart F1 Quantity': productData.serviceQuantity
					};

					_kmq.push(['record', 'Add to Cart', toKISS]);

					productData.isUpsale && _kmq.push(['record', 'cart rec added from rec', {'SKU cart added from rec': productData.article}]);
					productData.fromUpsale && _kmq.push(['record', 'cart recommendation added', {'SKU cart rec added': productData.article}]);
				}

				if ( serviceData ) {
					toKISS = {
						'Add F1 F1 Name': serviceData.name,
						'Add F1 F1 Price': serviceData.price,
						'Add F1 SKU': productData.article,
						'Add F1 Product Name': productData.name,
						'Add F1 Root category': productData.category[0].name,
						'Add F1 Root ID': productData.category[0].id,
						'Add F1 Category name': ( productData.category ) ? productData.category[productData.category.length - 1].name : 0,
						'Add F1 Category ID': ( productData.category ) ? productData.category[productData.category.length - 1].id : 0
					};

					_kmq.push(['record', 'Add F1', toKISS]);
				}

				if ( warrantyData ) {
					toKISS = {
						'Add Warranty Warranty Name': warrantyData.name,
						'Add Warranty Warranty Price': warrantyData.price,
						'Add Warranty SKU': productData.article,
						'Add Warranty Product Name': productData.name,
						'Add Warranty Root category': productData.category[0].name,
						'Add Warranty Root ID': productData.category[0].id,
						'Add Warranty Category name': ( productData.category ) ? productData.category[productData.category.length - 1].name : 0,
						'Add Warranty Category ID': ( productData.category ) ? productData.category[productData.category.length - 1].id : 0
					};

					_kmq.push(['record', 'Add Warranty', toKISS]);
				}
			},

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

				productData.isUpsale && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_added_from_rec', productData.article]);
				productData.fromUpsale && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_added_to_cart', productData.article]);

                try {
                    var sender = data.sender;
                    console.info({sender: sender});
                    if (sender && ('retailrocket' == sender.name)) {
						var rrEventLabel = '';
						if (ENTER.config.pageConfig.product) {
							if (ENTER.config.pageConfig.product.isSlot) {
								rrEventLabel = ' (marketplace-slot)';
							} else if (ENTER.config.pageConfig.product.isOnlyFromPartner) {
								rrEventLabel = ' (marketplace)';
							}
						}

                        $body.trigger('trackGoogleEvent',['RR_Взаимодействие' + rrEventLabel, 'Добавил в корзину', sender.position]);
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
				kissAnalytics(event, data);
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
