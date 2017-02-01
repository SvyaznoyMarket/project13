;$(function(){
	var $body = $(document.body),
		authorized_cookie = '_authorized';

	function createUserModel(data) {
		var model = {};

		model.name = ko.observable();
		model.firstName = ko.observable();
		model.lastName = ko.observable();
		model.link = ko.observable();
		/* была ли модель обновлена данными от /ajax/userinfo */
		/* чтобы предотвратить моргание элементов, видимость которых зависит от суммы корзины, например */

		if (data.user) {
			model.name(data.user.name || '');
			model.firstName(data.user.firstName || '');
			model.lastName(data.user.lastName || '');
			model.link(data.user.link || '');
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

		model.cart().hasAvailableProducts = ko.computed(function(){
			return $.grep(model.cart().products(), function(product){ return product.isAvailable })
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
	$('.js-topbarfix, .js-topbarfixBuy, .js-WidgetBuy, .js-listing, .js-gridListing, .js-lineListing, .js-slider, .jsKnockoutCart, .js-compareProduct').each(function(){
		ko.applyBindings(ENTER.UserModel, this);
	});

    // Удаление товара из корзины (RetailRocket, etc)
    $body.on('removeFromCart', function(e, data) {
		$.each(data.setProducts, function(key, setProduct) {
			if (!setProduct.id) return;

			console.info('RetailRocket removeFromCart id = %s', setProduct.id);

			if (window.rrApiOnReady) {
				window.rrApiOnReady.push(function(){ window.rrApi.removeFromBasket(setProduct.id) });
			}

			ENTER.utils.analytics.addProduct({
				id: setProduct.id,
				name: setProduct.name,
				price: setProduct.price,
				category: setProduct.categoryName,
				brand: setProduct.brand,
				quantity: setProduct.quantity
			});

			$body.trigger('trackGoogleEvent',['Product', 'click', 'remove from cart'])

			ENTER.utils.analytics.soloway.send({
				action: 'basketProductDelete',
				product: {
					ui: setProduct.ui,
					category: {
						ui: setProduct.category ? setProduct.category.ui : ''
					}
				}
			});
		});
		ENTER.utils.analytics.setAction('remove');
    });

    // Аналитика минимальной суммы заказа для Воронежа
    $body.on('showUserCart', function(){
        if (ENTER.UserModel.minOrderSum !== false) {
            if (ENTER.UserModel.isMinOrderSumVisible()) $body.trigger('trackGoogleEvent', ['pickup', 'no', (ENTER.UserModel.minOrderSum - ENTER.UserModel.cart().sum()) + '']);
            else $body.trigger('trackGoogleEvent', ['pickup', 'yes']);
        }
    });
});
