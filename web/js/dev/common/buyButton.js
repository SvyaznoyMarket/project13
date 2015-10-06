/**
 * @author		Zaytsev Alexandr
 */
(function(ENTER) {
	var $body = $('body');

	// Обработчик для кнопок купить
	$body.on('click', '.jsBuyButton', function(e, credit) {
		var $button = $(e.currentTarget);

		if ($button.hasClass('mDisabled')) {
            e.preventDefault();
		}

		if ($button.hasClass('mBought')) {
            return;
		}

		$button.addClass('mLoading');

		var
			url = $button.attr('href'),
			sender = ENTER.utils.analytics.productPageSenders.get($button),
			sender2 = ENTER.utils.analytics.productPageSenders2.get($button)
		;

		if (sender) {
			for (var key in sender) {
				if (sender.hasOwnProperty(key)) {
					url = ENTER.utils.setURLParam('sender[' + key + ']', sender[key], url);
				}
			}
		}

		if (sender2) {
			url = ENTER.utils.setURLParam('sender2', sender2, url);
		}

        if ('on' === credit) {
            url = ENTER.utils.setURLParam('credit', 'on', url);
        }

		// Добавление в корзину на сервере. Получение данных о покупке и состоянии корзины. Маркировка кнопок.
		$.ajax({
			url: url,
			type: 'GET',
			success: function(data) {
				var
					upsale = $button.data('upsale') ? $button.data('upsale') : null,
					product = $button.parents('.jsSliderItem').data('product');

				if (data.noticePopupHtml) {
					$.enterLightboxMe.closeAll();
					$(data.noticePopupHtml).enterLightboxMe({
						centered: true,
						closeSelector: '.js-notice-popup-closer',
						closeClick: true,
						destroyOnClose: true,
						preventScroll: true
					});
				}

				if (!data.success) {
					return;
				}

				$button.removeClass('mLoading');

				data.location = $button.data('location');

				ENTER.UserModel.cart().update(data.cart);

				if (data.sender && typeof data.sender.name == 'string' && data.sender.name.indexOf('filter') == 0) {
					$('body').trigger('trackGoogleEvent', {
						category: data.sender.name,
						action: 'basket',
						label: data.sender.categoryUrlPrefix
					});
				}

				$body.trigger('addtocart', [data, upsale]);
			},
			error: function() {
				$button.removeClass('mLoading');
			}
		});

		//return false;
        e.preventDefault();
	});

	$body.on('click', '.js-buyButton-points-opener', function(e){
		e.preventDefault();

		var
			$points = $(e.currentTarget).closest('.js-buyButton-points'),
			$pointsContent = $points.find('.js-buyButton-points-content')
		;

		$.enterLightboxMe.closeAll();

		$pointsContent.enterLightboxMe({
			centered: true,
			closeSelector: '.js-buyButton-points-content-closer',
			closeClick: true,
			destroyOnClose: true,
			preventScroll: true,
			onClose: function() {
				$points.prepend($pointsContent.hide());
			}
		});
	});

	// analytics
	$body.on('addtocart', function(event, data){
		var
			/**
			 * Google Analytics аналитика добавления в корзину
			 */
			googleAnalytics = function( event, productData, sender ) {
				var
					tchiboGA = function() {
						if (typeof window.ga === "undefined" || !productData.hasOwnProperty("isTchiboProduct") || !productData.isTchiboProduct) {
							return;
						}

						console.log("TchiboGA: tchiboTracker.send event Add2Basket product [%s, %s]", productData.name, productData.article);
						ga("tchiboTracker.send", "event", "Add2Basket", productData.name, productData.article);
					};
				// end of functions

				tchiboGA();

				ENTER.utils.sendAdd2BasketGaEvent(productData.article, productData.price, productData.isOnlyFromPartner, productData.isSlot, sender ? sender.name : '');

                try {
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
			addToRetailRocket = function( event, productId ) {
				if ( typeof rcApi === 'object' ) {
					try {
						rcApi.addToBasket(productId);
					}
					catch ( err ) {}
				}
			};
		//end of functions

		try{
			if (data.setProducts) {
				console.groupCollapsed('Аналитика для набора продуктов');
				$.each(data.setProducts, function(key, setProduct) {
					googleAnalytics(event, setProduct, data.sender);
					addToRetailRocket(event, setProduct.id);
				});
				console.groupEnd();
			}
		}
		catch( e ) {
			console.warn('addtocartAnalytics error');
			console.log(e);
		}
	});
}(window.ENTER));
