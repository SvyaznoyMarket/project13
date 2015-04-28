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

		userBarFixed = userBar.userBarFixed = $('.js-topbar-fixed'),
		userbarStatic = userBar.userBarStatic = $('.js-topbar-static'),

		emptyCompareNoticeElements = {},
		emptyCompareNoticeShowClass = 'topbarfix_cmpr_popup-show',

		topBtn = userBarFixed.find('.js-userbar-upLink'),
		userbarConfig = userBarFixed.data('value'),
		$body = $('body'),
		w = $(window),
		buyInfoShowing = false,
		overlay = $('<div>').css({ position: 'fixed', display: 'none', width: '100%', height:'100%', top: 0, left: 0, zIndex: 900, background: 'black', opacity: 0.4 }),

		scrollTarget,
		filterTarget,
		showWhenFullCartOnly = userbarConfig && userbarConfig.showWhenFullCartOnly;
	// end of vars

	userBar.showOverlay = false;

	/**
	 * Показ юзербара
	 */
	function showUserbar(disableAnimation, onOpen) {
		$.each(emptyCompareNoticeElements, function(){
			this.removeClass(emptyCompareNoticeShowClass);
		});

		if (disableAnimation) {
			userBarFixed.show(0, onOpen || function(){});
		} else {
			userBarFixed.addClass('fadeIn');
		}

		if (userBarFixed.length) {
			userbarStatic.css('visibility','hidden');
		}
	}

	/**
	 * Скрытие юзербара
	 */
	function hideUserbar() {
		userBarFixed.removeClass('fadeIn');
		userbarStatic.css('visibility','visible');
	}

	/**
	 * Проверка текущего скролла
	 */
	function checkScroll(hideOnly) {
		if ( buyInfoShowing ) {
			return;
		}

		if (scrollTarget && scrollTarget.length && w.scrollTop() >= scrollTarget.offset().top && !hideOnly && (!showWhenFullCartOnly || ENTER.UserModel.cart().length)) {
			showUserbar();
		}
		else {
			hideUserbar();
		}
	}

	/**
	 * Прокрутка до фильтра и раскрытие фильтров
	 */
	function upToFilter() {
		$.scrollTo(filterTarget, 500);
		ENTER.catalog.filter.openFilter();

		return false;
	}

	/**
	 * Закрытие окна о совершенной покупке
	 */
	function closeBuyInfo() {
		var
			wrap = userBarFixed.find('.topbarfix_cart'),
			wrapLogIn = userBarFixed.find('.topbarfix_log'),
			openClass = 'mOpenedPopup',
			upsaleWrap = wrap.find('.hintDd');
		// end of vars

		$body.trigger('closeBuyInfo');

		/**
		 * Удаление выпадающей плашки для корзины
		 */
		function removeBuyInfoBlock() {
			var
				buyInfo = $('.topbarfix_cartOn');
			// end of vars

			if ( !buyInfo.length ) {
				return;
			}

			buyInfo.slideUp(300, function() {
				buyInfo.removeAttr('style');
			});
		}

		/**
		 * Удаление Overlay блока
		 */
		function removeOverlay() {
			if (!overlay || !userBar.showOverlay) {
				checkScroll();
				return;
			}

			overlay.fadeOut(100, function() {
				overlay.off('click');
				overlay.remove();
				userBar.showOverlay = false;
				buyInfoShowing = false;
				checkScroll();
			});
		}
		// end of function

		setTimeout(function() {
			userBarFixed.removeClass('fadeIn shadow-false');
		}, 100);

		// только BuyInfoBlock
		if ( !upsaleWrap.hasClass('mhintDdOn') ) {
			removeBuyInfoBlock();
			removeOverlay();
			return;
		}

		upsaleWrap.removeClass('mhintDdOn');
		wrapLogIn.removeClass(openClass);
		wrap.removeClass(openClass);

		removeBuyInfoBlock();
		removeOverlay();
		return false;
	}

	/**
	 * Показ окна о совершенной покупке
	 */
	function showBuyInfo( e, data, upsale ) {
		console.info('userbar::showBuyInfo');

		$body.trigger('showBuyInfo');

		$.each(emptyCompareNoticeElements, function(){
			this.removeClass(emptyCompareNoticeShowClass);
		});

		userBarFixed.addClass('fadeIn shadow-false');

		var	buyInfo = $('.topbarfix_cartOn');

		if ( !userBar.showOverlay && overlay ) {
			$body.append(overlay);
			overlay.fadeIn(300);
			userBar.showOverlay = true;
			overlay.on('click', closeBuyInfo);
		}

		if ( e ) {
			buyInfo.slideDown(300);
		}
		else {
			buyInfo.show();
		}

		showUserbar(true);
		if (upsale) {
			showUpsell(data, upsale);
		}

		buyInfoShowing = true;
		$(document.body).trigger('showUserCart');
	}

	/**
	 * Удаление товара из корзины
	 */
	function deleteProductHandler() {
		console.log('deleteProductHandler click!');

		var btn = $(this);
		// end of vars

		$.ajax({
			type: 'GET',
			url: btn.attr('href'),
			success: function( res, data ) {
				console.warn( res );
				if ( !res.success ) {
					console.warn('удаление не получилось :(');

					return;
				}

				ENTER.UserModel.removeProductByID(res.product.id);

				// Удаляем товар на странице корзины
				$('.js-basketLineDeleteLink-' + res.product.id).click();

				if (ENTER.UserModel.cart().length == 0) {
					closeBuyInfo();
				} else {
					showBuyInfo();
				}

				$body.trigger('removeFromCart', [res.product]);
			}
		});

		return false;
	}

	/**
	 * Обновление блока с рекомендациями "С этим товаром покупают"
	 *
	 * @param	{Object}	data	Данные о покупке
	 * @param	{Object}	upsale
	 */
	function showUpsell( data, upsale ) {
		console.info('userbar::showUpsell');

		var
			cartWrap = userBarFixed.find('.topbarfix_cart'),
			upsaleWrap = cartWrap.find('.hintDd'),
			slider;
		// end of vars

		function responseFromServer( response ) {
			console.log(response);

			if ( !response.success || !userBar.showOverlay ) {
				return;
			}

			console.info('Получены рекомендации "С этим товаром покупают" от RetailRocket');

			upsaleWrap.find('.js-slider').remove();

			slider = $(response.content)[0];
			upsaleWrap.append(slider);
			upsaleWrap.addClass('mhintDdOn');
			$(slider).goodsSlider();

			ko.applyBindings(ENTER.UserModel, slider);

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
		}

		console.log(upsale);

		if ( !upsale.url ) {
			console.log('if upsale.url');
			return;
		}

		var
			url = upsale.url,
			sender2 = '';

		if (ENTER.config.pageConfig.product) {
			if (ENTER.config.pageConfig.product.isSlot) {
				sender2 = 'slot';
			} else if (ENTER.config.pageConfig.product.isOnlyFromPartner) {
				sender2 = 'marketplace';
			}
		}

		if (sender2) {
			url = ENTER.utils.setURLParam('sender2', sender2, url);
		}

		$.ajax({
			type: 'GET',
			url: url,
			success: responseFromServer
		});
	}

	/**
	 * Обработчик клика по товару из списка рекомендаций
	 */
	function upsaleProductClick() {
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

		//window.docCookies.setItem('used_cart_rec', 1, 1, 4*7*24*60*60, '/');
	}

	function showEmptyCompareNotice(e, emptyCompareNoticeName, $userbar) {
		e.stopPropagation();
		if (!emptyCompareNoticeElements[emptyCompareNoticeName]) {
			var element = $('.js-compare-popup', $userbar);

			$('.js-compare-popup-closer', element).click(function() {
				element.removeClass(emptyCompareNoticeShowClass);
			});

			$('.js-topbarfixLogin, .js-topbarfixNotEmptyCart', $userbar).mouseover(function() {
				element.removeClass(emptyCompareNoticeShowClass);
			});

			$('html').click(function() {
				element.removeClass(emptyCompareNoticeShowClass);
			});

			$(element).click(function(e) {
				e.stopPropagation();
			});

			$(document).keyup(function(e) {
				if (e.keyCode == 27) {
					element.removeClass(emptyCompareNoticeShowClass);
				}
			});

			emptyCompareNoticeElements[emptyCompareNoticeName] = element;
		}

		if (!$('.mhintDdOn').length){
			emptyCompareNoticeElements[emptyCompareNoticeName].addClass(emptyCompareNoticeShowClass);
		}

	}

	console.info('Init userbar module');
	console.log(userbarConfig);

	userBar.show = showUserbar;

	$body.on('click', '.jsUpsaleProduct', upsaleProductClick);
	$body.on('click', '.jsCartDelete', deleteProductHandler);

	$('.js-noProductsForCompareLink', userBarFixed).click(function(e) { showEmptyCompareNotice(e, 'fixed', userBarFixed); });
	$('.js-noProductsForCompareLink', userbarStatic).click(function(e) { showEmptyCompareNotice(e, 'static', userbarStatic); });

	if ( userBarFixed.length ) {
		if (window.location.pathname !== '/cart') $body.on('addtocart', showBuyInfo);
		scrollTarget = $(userbarConfig.target);

		if (userbarConfig.filterTarget) {
			filterTarget = $(userbarConfig.filterTarget);
		} else {
			filterTarget = scrollTarget;
		}

		if ( topBtn.length ) {
			topBtn.on('click', upToFilter);
		}

		if ( scrollTarget.length ) {
			w.on('scroll', function(){ checkScroll(); });
		} else {
			w.on('scroll', function(){ checkScroll(true); });
		}

		// Если showWhenFullCartOnly = true, то проверку надо выполнять лишь после того, как станут доступны данные корзины (которые становятся доступны после userLogged)
		$body.on('userLogged', function(){
			checkScroll();
		});
	}
	else {
		overlay.remove();
		overlay = false;
	}

}(window.ENTER));
