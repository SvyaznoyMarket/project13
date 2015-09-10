;$(function() {
	var
		userBar = ENTER.utils.extendApp('ENTER.userBar'),

		$fixedUserBar = $('.js-topbar-fixed'),
		$staticUserBar = $('.js-topbar-static'),

		$upLink = $fixedUserBar.find('.js-userbar-upLink'),
		$body = $('body'),
		$window = $(window),
		$overlay = $('<div>').css({position: 'fixed', display: 'none', width: '100%', height:'100%', top: 0, left: 0, zIndex: 900, background: 'black', opacity: 0.4}),
		$scrollTarget,
		$filterTarget,

		userBarConfig = $fixedUserBar.data('value') || {},
		emptyCompareNoticeElements = {},
		emptyCompareNoticeShowClass = 'topbarfix_cmpr_popup-show',

		isFullFixedUserBarOpened = false,
		isOverlayShowed = false
	;

	userBar.$fixedUserBar = $fixedUserBar;
	userBar.$staticUserBar = $staticUserBar;
	userBar.openFixedUserBar = openFixedUserBar;

	function openFixedUserBar() {
		$.each(emptyCompareNoticeElements, function(){
			this.removeClass(emptyCompareNoticeShowClass);
		});

		$fixedUserBar.addClass('fadeIn');
	}

	function closeFixedUserBar() {
		$fixedUserBar.removeClass('fadeIn');
		$staticUserBar.css('visibility','visible');
	}

	function checkScroll(hideOnly) {
		// https://jira.enter.ru/browse/UX-3053?focusedCommentId=165671&page=com.atlassian.jira.plugin.system.issuetabpanels:comment-tabpanel#comment-165671
		var top = $staticUserBar.offset().top - $window.scrollTop();
		top = top > 0 ? top + 'px' : 0;
		$fixedUserBar.css('top', top);
		$overlay.css('top', top);

		if (isFullFixedUserBarOpened) {
			return;
		}

		if ($scrollTarget && $scrollTarget.length && $window.scrollTop() >= $scrollTarget.offset().top && !hideOnly && (!userBarConfig.showWhenFullCartOnly || ENTER.UserModel.cart().products().length)) {
			openFixedUserBar();
		} else {
			closeFixedUserBar();
		}
	}

	function openFullFixedUserBar(useAnimation, data, upsale) {
		$body.trigger('openFullFixedUserBar');

		$.each(emptyCompareNoticeElements, function(){
			this.removeClass(emptyCompareNoticeShowClass);
		});

		$fixedUserBar.addClass('shadow-false');

		if (!isOverlayShowed && $overlay) {
			$body.append($overlay);
			$overlay.fadeIn(300);
			isOverlayShowed = true;
			$overlay.on('click', function(e) {
				e.preventDefault();
				closeFullFixedUserBar();
			});
		}

		if (useAnimation) {
			$('.js-topbar-fixed .topbarfix_cartOn').slideDown(300);
		} else {
			$('.js-topbar-fixed .topbarfix_cartOn').show();
		}

		openFixedUserBar();

		if (upsale) {
			showUpsell(data, upsale);
		}

		isFullFixedUserBarOpened = true;

		// TODO:
		/*
		if ( useAnimation ) {
			$('.js-topbar-static .topbarfix_cartOn').slideDown(300);
		}
		else {
			$('.js-topbar-static .topbarfix_cartOn').show();
		}
		*/

		$(document.body).trigger('showUserCart');
	}

	function closeFullFixedUserBar() {
		var
			$wrap = $fixedUserBar.find('.topbarfix_cart'),
			$wrapLogIn = $fixedUserBar.find('.topbarfix_log'),
			$upsaleWrap = $wrap.find('.hintDd'),
			openClass = 'mOpenedPopup'
		;

		$body.trigger('closeFullFixedUserBar');

		/**
		 * Удаление выпадающей плашки для корзины
		 */
		function removeBuyInfoBlock() {
			var $buyInfo = $('.topbarfix_cartOn');

			if (!$buyInfo.length) {
				return;
			}

			$buyInfo.slideUp(300, function() {
				$buyInfo.removeAttr('style');
			});
		}

		function removeOverlay() {
			if (!$overlay || !isOverlayShowed) {
				checkScroll();
				return;
			}

			$overlay.fadeOut(100, function() {
				$overlay.off('click');
				$overlay.remove();
				isOverlayShowed = false;
				isFullFixedUserBarOpened = false;
				checkScroll();
			});
		}

		setTimeout(function() {
			$fixedUserBar.removeClass('shadow-false');
		}, 100);

		// только BuyInfoBlock
		if (!$upsaleWrap.hasClass('mhintDdOn')) {
			removeBuyInfoBlock();
			removeOverlay();
			return;
		}

		$upsaleWrap.removeClass('mhintDdOn');
		$wrapLogIn.removeClass(openClass);
		$wrap.removeClass(openClass);

		$('.js-topbarfixLogin').removeClass('blocked');

		removeBuyInfoBlock();
		removeOverlay();
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
			cartWrap = $fixedUserBar.find('.topbarfix_cart'),
			upsaleWrap = cartWrap.find('.hintDd'),
			slider;

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
			success: function (response) {
				console.log(response);

				if ( !response.success || !isOverlayShowed ) {
					return;
				}

				console.info('Получены рекомендации "С этим товаром покупают" от RetailRocket');

				upsaleWrap.find('.js-slider, .js-slider-2').remove();
				$('.js-topbarfixLogin').addClass('blocked');

				slider = $(response.content);

				upsaleWrap.append(slider);
				upsaleWrap.addClass('mhintDdOn');

				if (slider.hasClass('js-slider-2')) {
					slider.eq(0).goodsSlider({
						leftArrowSelector: '.goods-slider__btn--prev',
						rightArrowSelector: '.goods-slider__btn--next',
						sliderWrapperSelector: '.goods-slider__inn',
						sliderSelector: '.goods-slider-list',
						itemSelector: '.goods-slider-list__i'

					});
				} else {
					slider.eq(0).goodsSlider();
				}

				ko.applyBindings(ENTER.UserModel, slider[0]);

				if ( !data.setProducts || !data.setProducts.length ) return;

				if ( !data.setProducts[0].article ) {
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
				typeof _gaq == 'function' && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_shown', data.setProducts[0].article]);
			}
		});
	}

	function showEmptyCompareNotice(e, emptyCompareNoticeName, $userbar) {
		e.stopPropagation();
		if (!emptyCompareNoticeElements[emptyCompareNoticeName]) {
			var element = $('.js-compare-popup', $userbar);

			$('.js-compare-popup-closer', element).click(function() {
				element.removeClass(emptyCompareNoticeShowClass);
			});

			$('.js-topbarfixLogin-opener, .js-topbarfixNotEmptyCart', $userbar).mouseover(function() {
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
	console.log(userBarConfig);

	// Клик по товару из рекомендаций
	$body.on('click', '.jsUpsaleProduct', function() {
		var
			product = $(this).parents('.jsSliderItem').data('product');
		//end of vars

		if ( !product.article ) {
			console.warn('Не получен article продукта');

			return;
		}

		console.log('Трекинг при клике по товару из списка рекомендаций');
		_gaq && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_clicked', product.article]);

		//window.docCookies.setItem('used_cart_rec', 1, 1, 4*7*24*60*60, '/');
	});

	$body.on('click', '.jsCartDelete', function(e) {
		e.preventDefault();
		var $this = $(e.currentTarget);

		$.ajax({
			type: 'GET',
			url: $this.attr('href'),
			success: function(data) {
				console.warn( data );
				if ( !data.success ) {
					console.warn('удаление не получилось :(');

					return;
				}

				ENTER.UserModel.cart().update(data.cart);

				if (ENTER.UserModel.cart().products().length == 0) {
					closeFullFixedUserBar();
				} else {
					openFullFixedUserBar(false, {setProducts: [{article: $this.data('product-article')}]}, {url: '/ajax/upsale/' + $this.data('product-id'), fromUpsale: false});
				}

				$body.trigger('removeFromCart', [data.setProducts]);
			}
		});
	});

	$('.js-noProductsForCompareLink', $fixedUserBar).click(function(e) { showEmptyCompareNotice(e, 'fixed', $fixedUserBar); });
	$('.js-noProductsForCompareLink', $staticUserBar).click(function(e) { showEmptyCompareNotice(e, 'static', $staticUserBar); });

	if ($fixedUserBar.length) {
		if (window.location.pathname !== '/cart') {
			$body.on('addtocart', openFullFixedUserBar);
		}

		$scrollTarget = $(userBarConfig.target);

		if (userBarConfig.filterTarget) {
			$filterTarget = $(userBarConfig.filterTarget);
		} else {
			$filterTarget = $scrollTarget;
		}

		$upLink.on('click', function(e) {
			e.preventDefault();
			$.scrollTo($filterTarget, 500);
			ENTER.catalog.filter.open();
		});

		if ( $scrollTarget.length ) {
			$window.on('scroll', function(){ checkScroll(); });
		} else {
			$window.on('scroll', function(){ checkScroll(true); });
		}

		checkScroll();
	} else {
		$overlay.remove();
		$overlay = false;
	}
});
