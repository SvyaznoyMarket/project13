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

		userBar = $('.fixedTopBar'),
		body = $('body');
	// end of vars
	

	var checkScroll = function checkScroll() {

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

			var userWrap = userBar.find('.fixedTopBar__logIn'),
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

			var cartWrap = userBar.find('.fixedTopBar__cart'),
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

	
	body.on('userLogged', updateUserInfo);
	body.on('basketUpdate', updateBasketInfo);
	$(window).on('scroll', checkScroll);

}(window.ENTER));