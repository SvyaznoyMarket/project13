;(function($){

	var $body = $(document.body);

	/* Увеличение и уменьшение товара AJAX */
	$body.on('click', '.numerbox a', function(e){
		var $elem = $(this),
			href = $elem.attr('href');

		if (href != '') {
			e.preventDefault();
			$.ajax({
				url: href,
				success: function(data){
					if (data.success && data.product && typeof data.product.quantity != 'undefined') {
						ENTER.UserModel.productQuantityUpdate(data.product.id, data.product.quantity);
					} else if (data.success && data.product && typeof data.product.quantity == 'undefined') {
						ENTER.UserModel.removeProductByID(data.product.id);
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
					if (data.success && data.product) {
						ENTER.UserModel.removeProductByID(data.product.id);
						$body.trigger('removeFromCart', [data.product]);
					}
				}
			})
		}

	})

}(jQuery));
;(function($){
	var $body = $(document.body),
		cartInfoBlock = $('.cartInfo'),
		config = ENTER.config.pageConfig,
		user = ENTER.UserModel,
		cookieKey1 = 'enter_ab_self_delivery_products_1', //cookie, где содержатся артикулы товаров, добавленных в корзину из блока рекомендаций
		cookieKey2 = 'enter_ab_self_delivery_products_2'; //cookie, где содержатся артикулы товаров, на которые перешли из блока рекомендаций

	$('.bGoodsSlider').goodsSlider();

//	if (cartInfoBlock.length > 0) ko.applyBindings(ENTER.UserModel, cartInfoBlock[0]);

	$body.on('userLogged', function(){
		if (user.cartSum() < config.selfDeliveryLimit && user.cartSum() != 0) {
			if (config.selfDeliveryTest) $body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + config.user.region.name, 'увидел', 'статичная корзина']);
			else $body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + config.user.region.name, 'не увидел', 'статичная корзина']);
		}
	});

	/* Трекинг добавления в корзину из блока рекомендаций */
	$body.on('click', '.basketLine .jsBuyButton', function(e){
		var product = $(e.target).closest('.jsSliderItem').data('product');
		if (!docCookies.hasItem(cookieKey1)) {
			docCookies.setItem(cookieKey1, product.article)
		} else {
			docCookies.setItem(cookieKey1, docCookies.getItem(cookieKey1) + ',' + product.article)
		}
		$body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + config.user.region.name, 'добавил из рекомендации', 'статичная корзина'])
	});

	/* Трекинг перехода в карточку товара из блока рекомендаций */
	$body.on('click', '.basketLine a:not(.jsBuyButton)', function(e){
		var $target = $(e.target),
			nodeName = $target.prop('nodeName'),
			href = '',
			product = $(e.target).closest('.jsSliderItem').data('product');

		if (!product) return;

		if (nodeName == 'A') href = $target.prop('href');
		if (nodeName == 'IMG') href = $target.closest('a').prop('href');

		if (!docCookies.hasItem(cookieKey2)) {
			docCookies.setItem(cookieKey2, product.article)
		} else {
			docCookies.setItem(cookieKey2, docCookies.getItem(cookieKey2) + ',' + product.article)
		}

		if (href.length != '') {
			e.preventDefault();
			$body.trigger('trackGoogleEvent',
				{	category: 'Платный_самовывоз_' + config.user.region.name,
					action:'перешел на карточку из рекомендации',
					label:'статичная корзина',
					hitCallback: function(){
						window.location.href = href;
					}
				})
		}
	})

}(jQuery));