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

		userbar = $('.fixedTopBar.mFixed'),
		userbarStatic = $('.fixedTopBar.mStatic'),
		topBtn = userbar.find('.fixedTopBar__upLink'),
		userbarConfig = userbar.data('value'),
		body = $('body'),
		w = $(window),
		infoShowing = false,

		scrollTarget,
		scrollTargetOffset;
	// end of vars
	

	var
		/**
		 * Показ юзербара
		 */
		showUserbar = function showUserbar() {
			userbar.slideDown();
		},

		/**
		 * Скрытие юзербара
		 */
		hideUserbar = function hideUserbar() {
			userbar.slideUp();
		},

		/**
		 * Проверка текущего скролла
		 */
		checkScroll = function checkScroll( e ) {
			var nowScroll = w.scrollTop();

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
				userWrap = userbar.find('.fixedTopBar__logIn'),
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
		showBuyInfo = function showBuyInfo() {
			console.info('userbar::showBuyInfo');

			var
				wrap = userbar.find('.fixedTopBar__cart'),
				overlay = $('<div>').css({ position: 'fixed', display: 'none', width: '100%', height:'100%', top: 0, left: 0, zIndex: 900, background: 'black', opacity: 0.4 }),
				template = $('#buyinfo_tmpl'),
				partials = template.data('partial'),
				openClass = 'mOpenedPopup',
				dataToRender = {},
				buyInfo,
				html;
			// end of vars

			dataToRender.products = utils.cloneObject(clientCart.products);
			dataToRender.showTransparent = !!( dataToRender.products.length > 4 );

			var
				/**
				 * Закрытие окна о совершенной покупке
				 */
				closeBuyInfo = function closeBuyInfo() {

					buyInfo.slideUp(300, function() {
						infoShowing = false;
						checkScroll();
						buyInfo.remove();
					});

					overlay.fadeOut(300, function() {
						overlay.off('click');
						overlay.remove();
					});

					wrap.removeClass(openClass);

					return false;
				};
			// end of function
			

			dataToRender.products.reverse();
			console.log(dataToRender);

			html = Mustache.render(template.html(), dataToRender, partials);
			buyInfo = $(html).css({ left: -129 });
			
			buyInfo.find('.cartList__item').eq(0).addClass('mHover');
			wrap.addClass(openClass);
			wrap.append(buyInfo);
			body.append(overlay);

			buyInfo.slideDown(300);
			overlay.fadeIn(300);
			showUserbar();

			infoShowing = true;

			overlay.on('click', closeBuyInfo);
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
				cartWrap = userbar.find('.fixedTopBar__cart'),
				cartWrapStatic = userbarStatic.find('.fixedTopBar__cart'),
				template = $('#userbar_cart_tmpl'),
				partials = template.data('partial'),
				html;
			// end of vars

			console.log('vars inited');

			data.hasProducts = false;
			data.showTransparent = false;

			if ( !(data && data.quantity && data.sum ) ) {
				return;
			}

			if ( clientCart.products.length !== 0 ) {
				data.hasProducts = true;
				data.products = utils.cloneObject(clientCart.products);
				data.products.reverse();
			}

			if ( clientCart.products.length > 4 ) {
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
		 * @param	{Object}	alsoBought
		 */
		updateAlsoBoughtInfo = function updateAlsoBoughtInfo( event, data, alsoBought ) {
			console.info('userbar::updateAlsoBoughtInfo');

			var responseFromServer = function ( response ){
				console.log(response);

				if ( response.success ) {
					console.info('Получены рекомендации "С этим товаром также покупают" от RetailRocket');
				}
			};
			//end functions

			if ( alsoBought.url ) {
				$.ajax({
					type: 'GET',
					url: alsoBought.url,
					success: responseFromServer
				});
			}
		};
	// end of functions


	console.info('Init userbar module');
	console.log(userbarConfig);

	body.on('userLogged', updateUserInfo);
	body.on('basketUpdate', updateBasketInfo);
	body.on('addtocart', showBuyInfo);
	body.on('addtocart', updateAlsoBoughtInfo);

	if ( userbar.length ) {
		scrollTarget = $(userbarConfig.target);

		if ( topBtn.length ) {
			topBtn.on('click', upToFilter);
		}

		if ( scrollTarget.length ) {
			scrollTargetOffset = scrollTarget.offset().top + userbar.height();
			w.on('scroll', checkScroll);
		}
	}

}(window.ENTER));
