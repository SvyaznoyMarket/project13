;$(function(){
	var $body = $(document.body),
		authorized_cookie = '_authorized';

	function createUserModel(data) {
		var model = {};

		model.name = ko.observable();
		model.firstName = ko.observable();
		model.lastName = ko.observable();
		model.link = ko.observable();
		model.isEnterprizeMember = ko.observable();
		/* была ли модель обновлена данными от /ajax/userinfo */
		/* чтобы предотвратить моргание элементов, видимость которых зависит от суммы корзины, например */

		if (data.user) {
			model.name(data.user.name || '');
			model.firstName(data.user.firstName || '');
			model.lastName(data.user.lastName || '');
			model.link(data.user.link || '');
			model.isEnterprizeMember(data.user.isEnterprizeMember || false);
		}

		model.cart = ko.observable({
			products: ko.observableArray(),
			sum: ko.observable(0),
			update: function(data) {
				model.cart().products.removeAll();
				if (data.products && $.isArray(data.products)) {
					$.each(data.products, function(i, product){
						model.cart().products.unshift(createCartProductModel(product));
					});
				}
				
				model.cart().sum(data.sum);
			}
		});

		model.cart().update(data.cart);

		model.compare = ko.observableArray();

		if (data.compare) {
			$.each(data.compare, function(i,val){ model.compare.push(val); })
		}

		/* АБ-тест платного самовывоза */
		model.infoIconVisible = ko.observable(false);
		model.infoBlock_1Visible = ko.computed(function(){
			return ENTER.config.pageConfig.selfDeliveryTest && ENTER.config.pageConfig.selfDeliveryLimit > model.cart().sum();
		});
		model.infoBlock_2Visible = ko.computed(function(){
			return ENTER.config.pageConfig.selfDeliveryTest && ENTER.config.pageConfig.selfDeliveryLimit <= model.cart().sum() && docCookies.hasItem('enter_ab_self_delivery_view_info');
		});

        // Минимальная стоимость заказа
        model.minOrderSum = ENTER.config.pageConfig.minOrderSum;
        model.isMinOrderSumVisible = ko.computed(function(){
            return model.minOrderSum !== false && model.minOrderSum > model.cart().sum()
        });

		return model;
	}

	function createCartProductModel(product) {
		var model = {};
		$.each(product, function(key, value){
			model[key] = value;
		});

		model.quantity = ko.observable(product.quantity);
		return model;
	}

	ENTER.UserModel = createUserModel(ENTER.config.userInfo);
	if (!docCookies.hasItem(authorized_cookie)) {
		if (ENTER.config.userInfo && ENTER.config.userInfo.user && typeof ENTER.config.userInfo.user.id != 'undefined') {
			docCookies.setItem(authorized_cookie, 1, 60*60, '/'); // on
		} else {
			docCookies.setItem(authorized_cookie, 0, 60*60, '/'); // off
		}
	}

	// Биндинги на нужные элементы
	// Топбар, кнопка Купить на странице продукта, листинги, слайдер аксессуаров
	$('.js-topbarfix, .js-topbarfixBuy, .js-WidgetBuy, .js-listing, .js-jewelListing, .js-gridListing, .js-lineListing, .js-slider, .jsKnockoutCart, .js-compareProduct').each(function(){
		ko.applyBindings(ENTER.UserModel, this);
	});

    // Удаление товара из корзины (RetailRocket, etc)
    $body.on('removeFromCart', function(e, data) {
		$.each(data.setProducts, function(key, setProduct) {
			if (!setProduct.id) return;
			console.info('RetailRocket removeFromCart id = %s', setProduct.id);
			if (window.rrApiOnReady) window.rrApiOnReady.push(function(){ window.rrApi.removeFromBasket(setProduct.id) });
			ENTER.utils.analytics.addProduct({
				id: setProduct.id,
				name: setProduct.name,
				price: setProduct.price,
				category: setProduct.categoryName,
				brand: setProduct.brand,
				quantity: setProduct.quantity
			});
			$body.trigger('trackGoogleEvent',['Product', 'click', 'remove from cart'])
		});
		ENTER.utils.analytics.setAction('remove');
    });

	/* SITE-4472 Аналитика по АБ-тесту платного самовывоза и рекомендаций из корзины */
	$body.on('mouseover', '.btnBuy-inf', function(){
		if (!docCookies.hasItem('enter_ab_self_delivery_view_info')) {
			docCookies.setItem('enter_ab_self_delivery_view_info', true);
			if (ENTER.UserModel.cart().sum() < ENTER.config.pageConfig.selfDeliveryLimit) $body.trigger('trackGoogleEvent', ['Платный_самовывоз', 'увидел всплывашку платный самовывоз', 'всплывающая корзина']);
			if (ENTER.UserModel.cart().sum() >= ENTER.config.pageConfig.selfDeliveryLimit) $body.trigger('trackGoogleEvent', ['Платный_самовывоз', 'самовывоз бесплатно', 'всплывающая корзина']);
		}
		ENTER.UserModel.infoIconVisible(false);
	});

	$body.on('showUserCart', function(e){
		if (ENTER.config.pageConfig.selfDeliveryTest && ENTER.UserModel.infoIconVisible()) $body.trigger('trackGoogleEvent', ['Платный_самовывоз', 'увидел подсказку', 'всплывающая корзина']);
		else if (ENTER.config.pageConfig.selfDeliveryTest && !ENTER.UserModel.infoIconVisible()) $body.trigger('trackGoogleEvent', ['Платный_самовывоз', 'не увидел подсказку', 'всплывающая корзина']);

		/* Если человек еще не наводил на иконку в всплывающей корзине */
		if (ENTER.config.pageConfig.selfDeliveryTest) {
			if (!docCookies.hasItem('enter_ab_self_delivery_view_info') && ENTER.UserModel.cart().sum() < ENTER.config.pageConfig.selfDeliveryLimit) {
				ENTER.UserModel.infoIconVisible(true);
			}
		}

		if (ENTER.config.pageConfig.selfDeliveryTest && ENTER.UserModel.infoBlock_2Visible() && !ENTER.UserModel.infoIconVisible()) {
			$body.trigger('trackGoogleEvent', ['Платный_самовывоз', 'самовывоз бесплатно', 'всплывающая корзина']);
		}
	});

    // Аналитика минимальной суммы заказа для Воронежа
    $body.on('showUserCart', function(){
        if (ENTER.UserModel.minOrderSum !== false) {
            if (ENTER.UserModel.isMinOrderSumVisible()) $body.trigger('trackGoogleEvent', ['pickup', 'no', (ENTER.UserModel.minOrderSum - ENTER.UserModel.cart().sum()) + '']);
            else $body.trigger('trackGoogleEvent', ['pickup', 'yes']);
        }
    });

	if (ENTER.config.pageConfig.selfDeliveryTest) {
		if (!docCookies.hasItem('enter_ab_self_delivery_view_info') && ENTER.UserModel.cart().sum() < ENTER.config.pageConfig.selfDeliveryLimit) {
			ENTER.UserModel.infoIconVisible(true);
		}
	}

	$body.on('click', '.jsAbSelfDeliveryLink', function(e){
		var href = e.target.href;
		if (href) {
			e.preventDefault();
			$body.trigger('trackGoogleEvent',
				{	category: 'Платный_самовывоз',
					action:'добрать товар',
					label:'всплывающая корзина',
					hitCallback: function(){
						window.location.href = href;
					}
				})
		}
	});
});
