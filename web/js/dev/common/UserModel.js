;$(function(){
	var $body = $(document.body),
		region = ENTER.config.pageConfig.user.region.name,
		userInfoURL = ENTER.config.pageConfig.userUrl.addParameterToUrl('ts', new Date().getTime() + Math.floor(Math.random() * 1000)),
		authorized_cookie = '_authorized',
		startTime, endTime, spendTime;

	/* Модель продукта в корзине */
	function createCartModel(cart) {
		var model = {};
		$.each(cart, function(key, value){
			model[key] = value;
		});

		model.quantity = ko.observable(cart.quantity);
		return model;
	}

	function createUserModel(){
		var model = {};

		model.name = ko.observable();
		model.firstName = ko.observable();
		model.lastName = ko.observable();
		model.link = ko.observable();
		model.isEnterprizeMember = ko.observable();
		/* была ли модель обновлена данными от /ajax/userinfo */
		/* чтобы предотвратить моргание элементов, видимость которых зависит от суммы корзины, например */
		model.isUpdated = ko.observable(false);

		model.cart = ko.observableArray();
		model.cartSum = ko.computed(function(){
			var sum = 0;
			$.each(model.cart(), function(i,val){ sum += val.price * val.quantity()});
			return sum;
		});
		model.compare = ko.observableArray();

		model.isProductInCompare = function(elem){
			console.log('isProductInCompare', elem);
			return $.grep(model.compare, function(val){return val.id == $(elem).data('id')}).length == 0
		};

		model.update = function(data) {
			if (data.user) {
				if (data.user.name) model.name(data.user.name);
				if (data.user.firstName) model.firstName(data.user.firstName);
				if (data.user.lastName) model.lastName(data.user.lastName);
				if (data.user.link) model.link(data.user.link);
				if (data.user.isEnterprizeMember) model.isEnterprizeMember(data.user.isEnterprizeMember);
			}
			if (data.cartProducts && $.isArray(data.cartProducts)) {
				$.each(data.cartProducts, function(i,val){ model.cart.unshift(createCartModel(val)) });
			}
			if (data.compare) {
				$.each(data.compare, function(i,val){ model.compare.push(val) })
			}
			model.isUpdated(true);
			$body.trigger('userModelUpdate')
		};

		/* Обновление количества продукта */
		model.productQuantityUpdate = function(product_id, count) {
			$.each(model.cart(), function(i,val){
				if (product_id == val.id) val.quantity(count)
			})
		};

		/* Удаление продукта по ID */
		model.removeProductByID = function(product_id) {
			model.cart.remove(function(item) { return item.id == product_id });
		};

		/* АБ-тест платного самовывоза */
		model.infoIconVisible = ko.observable(false);
		model.infoBlock_1Visible = ko.computed(function(){
			return ENTER.config.pageConfig.selfDeliveryTest && ENTER.config.pageConfig.selfDeliveryLimit > model.cartSum();
		});
		model.infoBlock_2Visible = ko.computed(function(){
			return ENTER.config.pageConfig.selfDeliveryTest && ENTER.config.pageConfig.selfDeliveryLimit <= model.cartSum() && docCookies.hasItem('enter_ab_self_delivery_view_info');
		});

		return model;
	}

	ENTER.UserModel = createUserModel();

	// Биндинги на нужные элементы
	// Топбар, кнопка Купить на странице продукта, листинги, слайдер аксессуаров
	$('.js-topbarfix, .js-topbarfixBuy, .js-WidgetBuy, .js-listing, .js-jewelListing, .js-gridListing, .js-lineListing, .js-slider, .jsKnockoutCart').each(function(){
		ko.applyBindings(ENTER.UserModel, this);
	});

	// Обновление данных о пользователе и корзине
	/*
	$.ajax({
		url: userInfoURL,
		beforeSend: function(){
			startTime = new Date().getTime();
		},
		success: function(data){
			...
		}
	});
	*/
	(function(){
		var data = $('.js-userbar-userbar').data('user-config');
		ENTER.UserModel.update(data);
		if (typeof ga == 'function') {
			ga('send', 'timing', 'userInfo', 'Load User Info', spendTime);
			console.log('[Google Analytics] Send user/info timing: %s ms', spendTime)
		}

		ENTER.config.userInfo = data;

		if (!docCookies.hasItem(authorized_cookie)) {
			if (data && data.user && typeof data.user.id != 'undefined') {
				docCookies.setItem(authorized_cookie, 1, 60*60, '/'); // on
			} else {
				docCookies.setItem(authorized_cookie, 0, 60*60, '/'); // off
			}
		}

		$body.trigger('userLogged', [data]);
	})();

	$body.on('catalogLoadingComplete', function(){
		$('.js-listing, .js-jewelListing').each(function(){
			ko.cleanNode(this);
			ko.applyBindings(ENTER.UserModel, this);
		});
	});

	$body.on('addtocart', function(event, data) {
		if ( data.redirect ) {
			console.warn('redirect');
			document.location.href = data.redirect;
		} else {

			ENTER.UserModel.cart.removeAll();
			$.each(data.cart.products, function(key, value){
				ENTER.UserModel.cart.unshift(createCartModel(value));
			});
		}
	});

    // Удаление товара из корзины (RetailRocket, etc)
    $body.on('removeFromCart', function(e, product) {
        if (!product.id) return;
        console.info('RetailRocket removeFromCart id = %s', product.id);
        if (window.rrApiOnReady) window.rrApiOnReady.push(function(){ window.rrApi.removeFromBasket(product.id) });
    });

	/* SITE-4472 Аналитика по АБ-тесту платного самовывоза и рекомендаций из корзины */
	$body.on('mouseover', '.btnBuy-inf', function(){
		if (!docCookies.hasItem('enter_ab_self_delivery_view_info')) {
			docCookies.setItem('enter_ab_self_delivery_view_info', true);
			if (ENTER.UserModel.cartSum() < ENTER.config.pageConfig.selfDeliveryLimit) $body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + region, 'увидел всплывашку платный самовывоз', 'всплывающая корзина']);
			if (ENTER.UserModel.cartSum() >= ENTER.config.pageConfig.selfDeliveryLimit) $body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + region, 'самовывоз бесплатно', 'всплывающая корзина']);
		}
		ENTER.UserModel.infoIconVisible(false);
	});

	$body.on('showUserCart', function(e){
		var $target = $(e.target);

		if (ENTER.config.pageConfig.selfDeliveryTest && ENTER.UserModel.infoIconVisible()) $body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + region, 'увидел подсказку', 'всплывающая корзина']);
		else if (ENTER.config.pageConfig.selfDeliveryTest && !ENTER.UserModel.infoIconVisible()) $body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + region, 'не увидел подсказку', 'всплывающая корзина']);

		/* Если человек еще не наводил на иконку в всплывающей корзине */
		if (ENTER.config.pageConfig.selfDeliveryTest) {
			if (!docCookies.hasItem('enter_ab_self_delivery_view_info') && ENTER.UserModel.cartSum() < ENTER.config.pageConfig.selfDeliveryLimit) {
				ENTER.UserModel.infoIconVisible(true);
			}
		}

		if (ENTER.config.pageConfig.selfDeliveryTest && ENTER.UserModel.infoBlock_2Visible() && !ENTER.UserModel.infoIconVisible()) {
			$body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + region, 'самовывоз бесплатно', 'всплывающая корзина']);
		}
	});

	$body.on('userModelUpdate', function(e) {
		if (ENTER.config.pageConfig.selfDeliveryTest) {
			if (!docCookies.hasItem('enter_ab_self_delivery_view_info') && ENTER.UserModel.cartSum() < ENTER.config.pageConfig.selfDeliveryLimit) {
				ENTER.UserModel.infoIconVisible(true);
			}
		}
	});

	$body.on('click', '.jsAbSelfDeliveryLink', function(e){
		var href = e.target.href;
		if (href) {
			e.preventDefault();
			$body.trigger('trackGoogleEvent',
				{	category: 'Платный_самовывоз_' + region,
					action:'добрать товар',
					label:'всплывающая корзина',
					hitCallback: function(){
						window.location.href = href;
					}
				})
		}
	});

});
