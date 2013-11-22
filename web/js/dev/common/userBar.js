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
	var config = ENTER.config,
		userUrl = config.pageConfig.userUrl,
		utils = ENTER.utils,

		userbar = $('.fixedTopBar.mFixed'),
		topBtn = userbar.find('.fixedTopBar__upLink'),
		userbarConfig = userbar.data('value'),
		body = $('body'),
		w = $(window),

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

			var userWrap = userbar.find('.fixedTopBar__logIn'),
				userTmpl;
			// end of vars

			if ( !(data && data.name && data.link ) ) {
				return;
			}

			userWrap.removeClass('mLogin');
			userTmpl = tmpl('userbar_user_tmpl', data);
			userWrap.html(userTmpl);
		},

		/**
		 * Обновление данных о корзине
		 * 
		 * @param	{Object}	event	Данные о событии
		 * @param	{Object}	data	Данные корзины
		 */
		updateBasketInfo = function updateBasketInfo( event, data ) {
			console.info('userbar::updateBasketInfo');
			console.log(data);

			var cartWrap = userbar.find('.fixedTopBar__cart'),
				cartTmpl;
			// end of vars

			if ( !(data && data.quantity && data.sum ) ) {
				return;
			}

			data.sum = printPrice(data.sum);

			cartWrap.removeClass('mEmpty');
			cartTmpl = tmpl('userbar_cart_tmpl', data);
			cartWrap.html(cartTmpl);
		};
	// end of functions





	if ( userbar.length ) {
		console.info('Init userbar module');
		console.log(userbarConfig);

		scrollTarget = $(userbarConfig.target);

		body.on('userLogged', updateUserInfo);
		body.on('basketUpdate', updateBasketInfo);

		if ( topBtn.length ) {
			topBtn.on('click', upToFilter);
		}

		if ( scrollTarget.length ) {
			scrollTargetOffset = scrollTarget.offset().top + userbar.height();
			w.on('scroll', checkScroll);
		}
	}

}(window.ENTER));