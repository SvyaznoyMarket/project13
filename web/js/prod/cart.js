;(function($){

	var $body = $(document.body),
		UserModel = ENTER.UserModel,
		updateTimeoutId = false;

	/* Уменьшение товара AJAX */
	$body.on('click', '.jsCartNumberBoxLess', function(e){
		e.preventDefault();

		// SITE-5957 В расширенной корзине при уменьшении кол-ва до нуля товар не должен удаляться
		if (parseInt($(e.currentTarget).closest('.jsCartNumber').find('.jsCartNumberBoxInput').val(), 10) <= 1) {
			return;
		}

		var $elem = $(this),
			href = $elem.attr('href');

		if (href != '') {
			$.ajax({
				url: href,
				success: function(data){
					if (data.success) {
						UserModel.cart().update(data.cart);
					}
				}
			})
		}
	});

	/* Увеличение товара AJAX */
	$body.on('click', '.jsCartNumberBoxMore', function(e){
		e.preventDefault();

		var $elem = $(this),
			href = $elem.attr('href');

		if (href != '') {
			$.ajax({
				url: href,
				success: function(data){
					if (data.success) {
						UserModel.cart().update(data.cart);
					}
				}
			})
		}
	});

	/* Удаление продукта AJAX */
	$body.on('click', '.jsCartDeleteProduct', function(e){
		var href = $(this).attr('href');

		if (href != '') {
			e.preventDefault();
			$.ajax({
				url: href,
				success: function(data){
					if (data.success) {
						UserModel.cart().update(data.cart);
						if (data.setProducts) {
							$body.trigger('removeFromCart', [data.setProducts]);
						}

						try {
							if (0 === data.cart.products.length) {
								setTimeout(function() { window.location.reload(); }, 100);
							}
						} catch (error) { console.error(error);	}
					}
				}
			})
		}

	});

	// Событие добавления в корзину SITE-5289
	$body.on('addtocart', function(event, data) {
		try {
			if (1 == data.cart.products.length) {
				console.info('#js-cart-firstRecommendation');
				var $container = $('#js-cart-firstRecommendation');
				if ($container.length) {
					$container.html($($container.text()));
					$container.find('.js-slider').goodsSlider();
					$container.show();
				}
			}
		} catch (error) {
			console.error(error);
		}
	});

	// Ручное обновление количества продукта
	$body.on('keydown', '.jsCartNumberBoxInput', function(e){
		var $input = $(e.target),
			keyCode = e.which;

		/* http://www.cambiaresearch.com/articles/15/javascript-char-codes-key-codes */
		if (keyCode > 36 && keyCode < 41) return true;

		if ((keyCode > 47 && keyCode < 58 ) || (keyCode > 95 && keyCode < 106) || keyCode == 8 || keyCode == 46) {

			if (!updateTimeoutId) updateTimeoutId = setTimeout(function(){

				if ($input.val() == '' || parseInt($input.val(), 10) == 0) {
					updateTimeoutId = false;
					return;
				}

				var productUi = $input.data('product-ui');

				$.ajax({
					url: ENTER.utils.generateUrl('cart.product.setList', {
						products: [{ui: $input.data('product-ui'), quantity: $input.val()}]
					}),
					beforeSend: function() {
						$input.attr('disabled', true)
					}
				}).done(function(data){
					if (data.success) {
						UserModel.cart().update(data.cart);
						// Используется далее в always
						$input = $('.jsCartNumberBoxInput[data-product-ui="' + productUi + '"]');
					} else {
						$input.val($input.prop('defaultValue'));
					}

					updateTimeoutId = false;
				}).always(function(){
					updateTimeoutId = false;
					$input.attr('disabled', false).focus();
				})
			}, 500)
		} else {
			return false
		}
	});

	// Если кол-во > 0 не было введено, то сбрасываем значение на изначальное
	$body.on('blur', '.jsCartNumberBoxInput', function(e){
		var $input = $(e.target);

		if ($input.val() == '' || parseInt($input.val(), 10) == 0) {
			updateTimeoutId = false;
			$input.val($input.prop('defaultValue'));
		}
	});

    $('.js-slider-2').goodsSlider({
        leftArrowSelector: '.goods-slider__btn--prev',
        rightArrowSelector: '.goods-slider__btn--next',
        sliderWrapperSelector: '.goods-slider__inn',
        sliderSelector: '.goods-slider-list',
        itemSelector: '.goods-slider-list__i'
    });

}(jQuery));
;(function($){
	var $body = $(document.body),
		config = ENTER.config.pageConfig,
		UserModel = ENTER.UserModel,
		cookieKey1 = 'enter_ab_self_delivery_products_1', //cookie, где содержатся артикулы товаров, добавленных в корзину из блока рекомендаций
		cookieKey2 = 'enter_ab_self_delivery_products_2'; //cookie, где содержатся артикулы товаров, на которые перешли из блока рекомендаций

	$('.js-slider').goodsSlider();

//	if (cartInfoBlock.length > 0) ko.applyBindings(ENTER.UserModel, cartInfoBlock[0]);

	if (UserModel.cart().sum() < config.selfDeliveryLimit && UserModel.cart().sum() != 0) {
		if (config.selfDeliveryTest) $body.trigger('trackGoogleEvent', ['Платный_самовывоз', 'увидел', 'статичная корзина']);
		else $body.trigger('trackGoogleEvent', ['Платный_самовывоз', 'не увидел', 'статичная корзина']);
	}

	/* Трекинг добавления в корзину из блока рекомендаций */
	$body.on('click', '.basketLine .jsBuyButton', function(e){
		var product = $(e.target).closest('.jsSliderItem').data('product');
		if (!docCookies.hasItem(cookieKey1)) {
			docCookies.setItem(cookieKey1, product.article)
		} else {
			docCookies.setItem(cookieKey1, docCookies.getItem(cookieKey1) + ',' + product.article)
		}
		$body.trigger('trackGoogleEvent', ['Платный_самовывоз', 'добавил из рекомендации', 'статичная корзина'])
	});

	/* Трекинг перехода в карточку товара из блока рекомендаций */
	$body.on('click', '.basketLine a:not(.js-orderButton)', function(e){
		var $target = $(e.target),
			nodeName = $target.prop('nodeName'),
			href = '', isNewWindow,
			product = $(e.target).closest('.jsSliderItem').data('product');

		if (!product) return;

		if (nodeName == 'IMG') $target = $target.closest('a');

        href = $target.attr('a');
        isNewWindow = $target.attr('target') == '_blank';

		if (!docCookies.hasItem(cookieKey2)) {
			docCookies.setItem(cookieKey2, product.article)
		} else {
			docCookies.setItem(cookieKey2, docCookies.getItem(cookieKey2) + ',' + product.article)
		}

		if (href.length != '') {
			e.preventDefault();
			$body.trigger('trackGoogleEvent',
				{	category: 'Платный_самовывоз',
					action:'перешел на карточку из рекомендации',
					label:'статичная корзина',
					hitCallback: isNewWindow ? null : href
				})
		}
	})

}(jQuery));