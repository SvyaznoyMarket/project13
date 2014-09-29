/**
 * White floating user bar
 *
 * 
 * @requires jQuery, ENTER.utils, ENTER.config
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	var
		utils = ENTER.utils,

		userBar = utils.extendApp('ENTER.userBar'),

		userBarFixed = userBar.userBarFixed = $('.topbarfix-fx'),
		userbarStatic = userBar.userBarStatic = $('.topbarfix-stc'),

		topBtn = userBarFixed.find('.topbarfix_upLink'),
		userbarConfig = userBarFixed.data('value'),
		body = $('body'),
		w = $(window),
		infoShowing = false,
		overlay = $('<div>').css({ position: 'fixed', display: 'none', width: '100%', height:'100%', top: 0, left: 0, zIndex: 900, background: 'black', opacity: 0.4 }),
		newOverlay = false,

		scrollTarget,
		scrollTargetOffset;
	// end of vars


	userBar.showOverlay = false;


	var
		/**
		 * Показ юзербара
		 */
		showUserbar = function showUserbar() {
			console.log('showUserbar');
			userBarFixed.slideDown();
			userbarStatic.css('visibility','hidden');
		},

		/**
		 * Скрытие юзербара
		 */
		hideUserbar = function hideUserbar() {
			console.log('hideUserbar');
			userBarFixed.slideUp();
			userbarStatic.css('visibility','visible');
		},

		/**
		 * Проверка текущего скролла
		 */
		checkScroll = function checkScroll() {
			var
				nowScroll = w.scrollTop();
			// end of vars

			if ( infoShowing ) {
				return;
			}

			if ( nowScroll >= scrollTargetOffset ) {
				showUserbar();
			}
			else {
				hideUserbar();
			}
		},

		/**
		 * Прокрутка до фильтра и раскрытие фильтров
		 */
		upToFilter = function upToFilter() {
			$.scrollTo(scrollTarget, 500);
			ENTER.catalog.filter.openFilter();

			return false;
		},

		/**
		 * Закрытие окна о совершенной покупке
		 */
		closeBuyInfo = function closeBuyInfo() {
			var
				wrap = userBarFixed.find('.topbarfix_cart'),
				wrapLogIn = userBarFixed.find('.topbarfix_log'),
				openClass = 'mOpenedPopup',
				upsaleWrap = wrap.find('.hintDd');
			// end of vars

			var
				/**
				 * Удаление выпадающей плашки для корзины
				 */
				removeBuyInfoBlock = function removeBuyInfoBlock() {
					var
						buyInfo = $('.topbarfix_cartOn');
					// end of vars

					if ( !buyInfo.length ) {
						return;
					}

					buyInfo.slideUp(300, function() {
						//checkScroll();
//						buyInfo.remove();
						infoShowing = false;
					});
				},

				/**
				 * Удаление Overlay блока
				 */
				removeOverlay = function removeOverlay() {
					overlay.fadeOut(100, function() {
						userBar.showOverlay = false;

						if ( newOverlay ) {
							newOverlay = false;

							return;
						}

						overlay.off('click');
						overlay.remove();
						userBar.showOverlay = false;
						checkScroll();
					});
				};
			// end of function

			// только BuyInfoBlock
			if ( !upsaleWrap.hasClass('mhintDdOn') ) {
				removeBuyInfoBlock();

				if ( userBar.showOverlay ) {
					removeOverlay();
				}

				return;
			}

			upsaleWrap.removeClass('mhintDdOn');
			wrapLogIn.removeClass(openClass);
			wrap.removeClass(openClass);

			if ( infoShowing ) {
				removeBuyInfoBlock();
			}

			if ( userBar.showOverlay ) {
				removeOverlay();
			}

			return false;
		},

		/**
		 * Показ окна о совершенной покупке
		 */
		showBuyInfo = function showBuyInfo( e ) {
			console.info('userbar::showBuyInfo');

			var	buyInfo = $('.topbarfix_cartOn');

			if ( !userBar.showOverlay ) {
				body.append(overlay);
				overlay.fadeIn(300);
				userBar.showOverlay = true;
			}

			if ( e ) {
				buyInfo.slideDown(300);
			}
			else {
				buyInfo.show();
			}

			showUserbar();

			infoShowing = true;

			overlay.on('click', closeBuyInfo);
		},

		/**
		 * Удаление товара из корзины
		 */
		deleteProductHandler = function deleteProductHandler() {
			console.log('deleteProductHandler click!');

			var
				btn = $(this),
				deleteUrl = btn.attr('href');
			// end of vars
			
			var
				deleteFromRutarget = function deleteFromRutarget( data ) {
					var
						region = $('.jsChangeRegion'),
						regionId = region.length ? region.data('region-id') : false,
						result,
						_rutarget = window._rutarget || [];
					// end of vars

					if ( !regionId || !data.hasOwnProperty('product') || !data.product.hasOwnProperty('id') ) {
						return;
					}

					result = {'event': 'removeFromCart', 'sku': data.product.id, 'regionId': regionId};

					console.info('RuTarget removeFromCart');
					console.log(result);
					_rutarget.push(result);
				},

				deleteFromLamoda = function deleteFromLamoda( data ) {
					if ('undefined' == typeof(JSREObject) || !data.hasOwnProperty('product') || !data.product.hasOwnProperty('id') ) {
						return;
					}

					console.info('Lamoda removeFromCart');
					console.log('product_id=' + data.product.id);
					JSREObject('cart_remove', data.product.id);
				},

				deleteFromRetailRocket = function deleteFromRetailRocket( data ) {
					if ( !data.hasOwnProperty('product') || !data.product.hasOwnProperty('id') ) {
						return;
					}

					console.info('RetailRocket removeFromCart');
					console.log('product_id=' + data.product.id);
					window.rrApiOnReady.push(function(){ window.rrApi.removeFromBasket(data.product.id) });
				},

				deleteProductAnalytics = function deleteProductAnalytics( data ) {
					if ('undefined' == typeof(data) ) {
						return;
					}

					deleteFromRetailRocket(data);
					deleteFromRutarget(data);
					deleteFromLamoda(data);
				},

				authFromServer = function authFromServer( res, data ) {
					console.warn( res );
					if ( !res.success ) {
						console.warn('удаление не получилось :(');

						return;
					}

					// аналитика
					deleteProductAnalytics(res);

					ENTER.UserModel.cart.remove(function(item){ return item.id == res.product.id});

					//показываем корзину пользователя при удалении товара
					if ( ENTER.UserModel.cart().length !== 0 ) {
						showBuyInfo();
					}

					//скрываем оверлей, если товаров в корзине нет
					if ( ENTER.UserModel.cart().length == 0 ) {
						overlay.fadeOut(300, function() {
							overlay.off('click');
							overlay.remove();

							userBar.showOverlay = false;
						});
						infoShowing = false;
						checkScroll();
					}

					//возвращаем кнопку - Купить
					var
						addUrl = res.product.addUrl;
						addBtnBuy = res.product.cartButton.id;
					// end of vars
					
					$('.'+addBtnBuy).html('Купить').removeClass('mBought').attr('href', addUrl);
				};

			$.ajax({
				type: 'GET',
				url: deleteUrl,
				success: authFromServer
			});

			return false;
		},

		/**
		 * Обновление блока с рекомендациями "С этим товаром также покупают"
		 *
		 * @param	{Object}	event	Данные о событии
		 * @param	{Object}	data	Данные о покупке
		 * @param	{Object}	upsale
		 */
		showUpsell = function showUpsell( event, data, upsale ) {
			console.info('userbar::showUpsell');

			var
				cartWrap = userBarFixed.find('.topbarfix_cart'),
				upsaleWrap = cartWrap.find('.hintDd'),
				slider;
			// end of vars

			var
				responseFromServer = function responseFromServer( response ) {
				console.log(response);

				if ( !response.success ) {
					return;
				}

				console.info('Получены рекомендации "С этим товаром также покупают" от RetailRocket');

				upsaleWrap.find('.bGoodsSlider').remove();

				slider = $(response.content)[0];
				upsaleWrap.append(slider);
				upsaleWrap.addClass('mhintDdOn');
				$(slider).goodsSlider();

				ko.applyBindings(ENTER.UserModel, slider);

				// показываем overlay для блока рекомендаций
				body.append(overlay);
				newOverlay = true;
				overlay.fadeIn(300);
				overlay.on('click', closeBuyInfo);
//				checkScroll();
				userBar.showOverlay = true;

                if ( !data.product ) return;

				if ( !data.product.article ) {
					console.warn('Не получен article продукта');

					return;
				}

				console.log('Трекинг товара при показе блока рекомендаций');

				// Retailrocket. Показ товарных рекомендаций
				if ( response.data ) {
					try {
						rrApi.recomTrack(response.data.method, response.data.id, response.data.recommendations);
					} catch( e ) {
						console.warn('showUpsell() Retailrocket error');
						console.log(e);
					}
				}

				// google analytics
				typeof _gaq == 'function' && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_shown', data.product.article]);
				// Kissmetrics
				typeof _kmq == 'function' && _kmq.push(['record', 'cart recommendation shown', {'SKU cart rec shown': data.product.article}]);
			};
			//end functions

			console.log(upsale);

			if ( !upsale.url ) {
                console.log('if upsale.url');
				return;
			}

			$.ajax({
				type: 'GET',
				url: upsale.url,
				success: responseFromServer
			});
		},

		/**
		 * Обработчик клика по товару из списка рекомендаций
		 */
		upsaleProductClick = function upsaleProductClick() {
			var
				product = $(this).parents('.jsSliderItem').data('product');
			//end of vars

			if ( !product.article ) {
				console.warn('Не получен article продукта');

				return;
			}

			console.log('Трекинг при клике по товару из списка рекомендаций');
			// google analytics
			_gaq && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_clicked', product.article]);
			// Kissmetrics
			_kmq && _kmq.push(['record', 'cart recommendation clicked', {'SKU cart rec clicked': product.article}]);

			//window.docCookies.setItem('used_cart_rec', 1, 1, 4*7*24*60*60, '/');
		};
	// end of functions


	console.info('Init userbar module');
	console.log(userbarConfig);

	body.on('click', '.jsUpsaleProduct', upsaleProductClick);
	body.on('getupsale', showUpsell);


	userbarStatic.on('click', '.jsCartDelete', deleteProductHandler);


	if ( userBarFixed.length ) {
		body.on('addtocart', showBuyInfo);
		userBarFixed.on('click', '.jsCartDelete', deleteProductHandler);
		scrollTarget = $(userbarConfig.target);

		if ( topBtn.length ) {
			topBtn.on('click', upToFilter);
		}

		if ( scrollTarget.length ) {
			scrollTargetOffset = scrollTarget.offset().top + userBarFixed.height() - scrollTarget.height();
			w.on('scroll', checkScroll);
		}
	}
	else {
		overlay.remove();
		overlay = false;
	}

}(window.ENTER));
