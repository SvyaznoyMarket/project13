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
		userUrl = config.pageConfig.userUrl,
		utils = ENTER.utils,

		userbar = $('.fixedTopBar.mFixed'),
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
				template = $('#userbar_user_tmpl'),
				partials = template.data('partial'),
				html;
			// end of vars

			if ( !( data && data.name && data.link ) ) {
				return;
			}

			userWrap.removeClass('mLogin');
			html = Mustache.render(template.html(), data, partials);
			userWrap.html(html);
		},

		/**
		 * Показ окна о совершенной покупке
		 *
		 * @param	{Object}	event	Данные о событии
		 * @param	{Object}	data	Данные о покупке
		 */
		showBuyInfo = function showBuyInfo( event, data ) {
			console.info('userbar::showBuyInfo');
			console.log(data);

			var
				wrap = userbar.find('.fixedTopBar__cart'),
				overlay = $('<div>').css({ position: 'fixed', display: 'none', width: '100%', height:'100%', top: 0, left: 0, zIndex: 900, background: 'black', opacity: .4 }),
				dataToRender = data.product,
				template = $('#buyinfo_tmpl'),
				partials = template.data('partial'),
				// tId,
				buyInfo,
				html;
			// end of vars


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

					return false;
				};
			// end of function

			dataToRender.price = printPrice( dataToRender.price );

			html = Mustache.render(template.html(), data.product, partials);
			buyInfo = $(html).css({ left: -129 });
			
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

			var
				cartWrap = userbar.find('.fixedTopBar__cart'),
				template = $('#userbar_cart_tmpl'),
				partials = template.data('partial'),
				html;
			// end of vars

			if ( !(data && data.quantity && data.sum ) ) {
				return;
			}

			data.sum = printPrice( data.sum );
			html = Mustache.render(template.html(), data, partials);

			cartWrap.removeClass('mEmpty');
			cartWrap.html(html);
		},

		/**
		 * Обновление блока с рекомендациями "С этим товаром также покупают"
		 */
			updateAlsoBoughtInfo = function updateAlsoBoughtInfo() {
			console.info('userbar::updateAlsoBoughtInfo');

			var responseFromServer = function ( response ){
				if ( response.success ) {
					console.info('Получены рекомендации "С этим товаром также покупают" от RetailRocket');
					//console.log(response.content);
				}
			};
			//end functions

			$.post(userbarConfig.ajaxAlsoBoughtUrl, responseFromServer);
		};
	// end of functions





	if ( userbar.length ) {
		console.info('Init userbar module');
		console.log(userbarConfig);

		scrollTarget = $(userbarConfig.target);

		body.on('userLogged', updateUserInfo);
		body.on('basketUpdate', updateBasketInfo);
		body.on('addtocart', showBuyInfo);
		body.on('addtocart', updateAlsoBoughtInfo);

		if ( topBtn.length ) {
			topBtn.on('click', upToFilter);
		}

		if ( scrollTarget.length ) {
			scrollTargetOffset = scrollTarget.offset().top + userbar.height();
			w.on('scroll', checkScroll);
		}
	}

}(window.ENTER));