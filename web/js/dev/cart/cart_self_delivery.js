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