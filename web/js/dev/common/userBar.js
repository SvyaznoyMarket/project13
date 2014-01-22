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
		config = ENTER.config,
		utils = ENTER.utils,
		clientCart = config.clientCart,

		userBar = utils.extendApp('ENTER.userBar'),

		userBarFixed = userBar.userBarFixed = $('.fixedTopBar.mFixed'),
		userbarStatic = userBar.userBarStatic = $('.fixedTopBar.mStatic'),

		topBtn = userBarFixed.find('.fixedTopBar__upLink'),
		userbarConfig = userBarFixed.data('value'),
		body = $('body'),
		w = $(window),
		infoShowing = false,
		overlay = $('<div>').css({ position: 'fixed', display: 'none', width: '100%', height:'100%', top: 0, left: 0, zIndex: 900, background: 'black', opacity: 0.4 }),

		scrollTarget,
		scrollTargetOffset;
	// end of vars
	
	
	userBar.showOverlay = false;


	var
		/**
		 * Показ юзербара
		 */
		showUserbar = function showUserbar() {
			userBarFixed.slideDown();
			userbarStatic.css('visibility','hidden');
		},

		/**
		 * Скрытие юзербара
		 */
		hideUserbar = function hideUserbar() {
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
		 * Обновление данных пользователя
		 *
		 * @param	{Object}	event	Данные о событии
		 * @param	{Object}	data	Данные пользователя
		 */
		updateUserInfo = function updateUserInfo( event, data ) {
			console.info('userbar::updateUserInfo');
			console.log(data);

			var
				userWrap = userBarFixed.find('.fixedTopBar__logIn'),
				userWrapStatic = userbarStatic.find('.fixedTopBar__logIn'),
				template = $('#userbar_user_tmpl'),
				partials = template.data('partial'),
				html;
			// end of vars

			if ( !( data && data.name && data.link ) ) {
				return;
			}

			html = Mustache.render(template.html(), data, partials);

			userWrapStatic.removeClass('mLogin');
			userWrap.removeClass('mLogin');
			userWrapStatic.html(html);
			userWrap.html(html);
		},

		/**
		 * Показ окна о совершенной покупке
		 */
		showBuyInfo = function showBuyInfo( e ) {
			console.info('userbar::showBuyInfo');

			var
				wrap = userBarFixed.find('.fixedTopBar__cart'),
				wrapLogIn = userBarFixed.find('.fixedTopBar__logIn'),
				template = $('#buyinfo_tmpl'),
				partials = template.data('partial'),
				openClass = 'mOpenedPopup',
				dataToRender = {},
				buyInfo,
				html;
			// end of vars

			dataToRender.products = utils.cloneObject(clientCart.products);
			dataToRender.showTransparent = !!( dataToRender.products.length > 3 );

			var
				/**
				 * Закрытие окна о совершенной покупке
				 */
				closeBuyInfo = function closeBuyInfo() {
					var
						upsaleWrap = wrap.find('.hintDd');
					// end of vars

					upsaleWrap.removeClass('mhintDdOn');
					wrapLogIn.removeClass(openClass);
					wrap.removeClass(openClass);

					buyInfo.slideUp(300, function() {
						//checkScroll();
						buyInfo.remove();

						infoShowing = false;
					});

					if ( !userBar.showOverlay ) {
						return;
					}
					
					overlay.fadeOut(300, function() {
						overlay.off('click');
						overlay.remove();
						checkScroll();

						userBar.showOverlay = false;
					});

					return false;
				};
			// end of function


			dataToRender.products.reverse();
			console.log(dataToRender);

			html = Mustache.render(template.html(), dataToRender, partials);
			buyInfo = $(html).css({ left: -129 });
			
			buyInfo.find('.cartList__item').eq(0).addClass('mHover');
			wrapLogIn.addClass(openClass);
			wrap.addClass(openClass);
			wrap.append(buyInfo);

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
				authFromServer = function authFromServer( res, data ) {
					console.warn( res );
					if ( !res.success ) {
						console.warn('удаление не получилось :(');

						return;
					}

					utils.blackBox.basket().deleteItem(res);

					//показываем корзину пользователя при удалении товара
					if ( clientCart.products.length !== 0 ) {
						showBuyInfo();
					}

					//скрываем оверлоу, если товаров в корзине нет
					if ( clientCart.products.length == 0 ) {
						overlay.fadeOut(300, function() {
							overlay.off('click');
							overlay.remove();

							userBar.showOverlay = false;
						});
						infoShowing = false;
						console.log('clientCart is empty');
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
		 * Обновление данных о корзине
		 * WARNING! перевести на Mustache
		 * 
		 * @param	{Object}	event	Данные о событии
		 * @param	{Object}	data	Данные корзины
		 */
		updateBasketInfo = function updateBasketInfo( event, data ) {
			console.info('userbar::updateBasketInfo');
			console.log(data);
			console.log(clientCart);

			var
				cartWrap = userBarFixed.find('.fixedTopBar__cart'),
				cartWrapStatic = userbarStatic.find('.fixedTopBar__cart'),
				template = $('#userbar_cart_tmpl'),
				partials = template.data('partial'),
				html;
			// end of vars

			console.log('vars inited');

			data.hasProducts = false;
			data.showTransparent = false;

			if ( !(data && data.quantity && data.sum ) ) {
				console.warn('data and data.quantuty and data.sum not true');

				var
					template = $('#userbar_cart_empty_tmpl');
					partials = template.data('partial'),
				// end of vars

				html = Mustache.render(template.html(), data, partials);

				cartWrap.addClass('mEmpty');
				cartWrapStatic.addClass('mEmpty');
				cartWrapStatic.html(html);
				cartWrap.html(html);

				return;
			}

			if ( clientCart.products.length !== 0 ) {
				data.hasProducts = true;
				data.products = utils.cloneObject(clientCart.products);
				data.products.reverse();
			}

			if ( clientCart.products.length > 3 ) {
				data.showTransparent = true;
			}

			data.sum = printPrice( data.sum );
			html = Mustache.render(template.html(), data, partials);

			cartWrapStatic.removeClass('mEmpty');
			cartWrap.removeClass('mEmpty');
			cartWrapStatic.html(html);
			cartWrap.html(html);
			
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
				cartWrap = userBarFixed.find('.fixedTopBar__cart'),
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

				if ( !data.product.article ) {
					console.warn('Не получен article продукта');

					return;
				}

				console.log('Трекинг товара при показе блока рекомендаций');
				// google analytics
				_gaq && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_shown', data.product.article]);
				// Kissmetrics
				_kmq && _kmq.push(['record', 'cart recommendation shown', {'SKU cart rec shown': data.product.article}]);
			};
			//end functions

			console.log(upsale);

			if ( !upsale.url ) {
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
	body.on('userLogged', updateUserInfo);
	body.on('basketUpdate', updateBasketInfo);
	body.on('addtocart', showBuyInfo);
	body.on('getupsale', showUpsell);


	userBarFixed.on('click', '.jsCartDelete', deleteProductHandler);
	userbarStatic.on('click', '.jsCartDelete', deleteProductHandler);


	if ( userBarFixed.length ) {
		scrollTarget = $(userbarConfig.target);

		if ( topBtn.length ) {
			topBtn.on('click', upToFilter);
		}

		if ( scrollTarget.length ) {
			scrollTargetOffset = scrollTarget.offset().top + userBarFixed.height();
			w.on('scroll', checkScroll);
		}
	}

}(window.ENTER));
