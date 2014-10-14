;$(function(){
	var $body = $(document.body),
		userInfoURL = ENTER.config.pageConfig.userUrl + '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000),
		authorized_cookie = '_authorized',
		startTime, endTime, spendTime, $compareNotice, compareNoticeTimeout;

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

		model.cart = ko.observableArray();
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
		};

		return model;
	}

	function showCompareNotice(product) {
		var compareNoticeShowClass = 'topbarfix_cmpr_popup-show';

		if (!$compareNotice) {
			var $userbar = ENTER.userBar.userBarFixed;
			$compareNotice = $('.js-compare-addPopup', $userbar);

			$('.js-compare-addPopup-closer', $compareNotice).click(function() {
				$compareNotice.removeClass(compareNoticeShowClass);
			});

			$('.js-topbarfixLogin, .js-topbarfixNotEmptyCart', $userbar).mouseover(function() {
				$compareNotice.removeClass(compareNoticeShowClass);
			});

			$('html').click(function() {
				$compareNotice.removeClass(compareNoticeShowClass);
			});

			$($compareNotice).click(function(e) {
				e.stopPropagation();
			});

			$(document).keyup(function(e) {
				if (e.keyCode == 27) {
					$compareNotice.removeClass(compareNoticeShowClass);
				}
			});
		}

		if (compareNoticeTimeout) {
			clearTimeout(compareNoticeTimeout);
		}

		compareNoticeTimeout = setTimeout(function() {
			$compareNotice.removeClass(compareNoticeShowClass);
		}, 2000);

		$('.js-compare-addPopup-image', $compareNotice).attr('src', product.imageUrl);
		$('.js-compare-addPopup-prefix', $compareNotice).text(product.prefix);
		$('.js-compare-addPopup-webName', $compareNotice).text(product.webName);

		ENTER.userBar.show(true, function(){
			compareNotice.addClass(compareNoticeShowClass)
		});
	}

	ENTER.UserModel = createUserModel();

	// Биндинги на нужные элементы
	// Топбар, кнопка Купить на странице продукта, листинги, слайдер аксессуаров
	$('.js-topbarfix, .js-WidgetBuy, .js-listing, .js-jewelListing, .js-gridListing, .js-lineListing, .js-slider').each(function(){
		ko.applyBindings(ENTER.UserModel, this);
	});

	// Обновление данных о пользователе и корзине
	$.ajax({
		url: userInfoURL,
		beforeSend: function(){
			startTime = new Date().getTime();
		},
		success: function(data){
			endTime = new Date().getTime();
			spendTime = endTime - startTime;
			ENTER.UserModel.update(data);
			if (typeof ga == 'function') {
				ga('send', 'timing', 'userInfo', 'Load User Info', spendTime);
				console.log('[Google Analytics] Send user/info timing: %s ms', spendTime)
			}

			ENTER.config.userInfo = data;

			if (!docCookies.hasItem(authorized_cookie)) {
				if (data && null !== data.id) {
					docCookies.setItem(authorized_cookie, 1, 60*60, '/'); // on
				} else {
					docCookies.setItem(authorized_cookie, 0, 60*60, '/'); // off
				}
			}

			$body.trigger('userLogged', [data]);
		}
	});

	$body.on('catalogLoadingComplete', function(){
		$('.js-listing, .js-jewelListing').each(function(){
			ko.cleanNode(this);
			ko.applyBindings(ENTER.UserModel, this);
		});
	});

	$body.on('click', '.jsCompareLink, .jsCompareListLink', function(e){
		var url = this.href,
			productId = $(this).data('id'),
			inCompare = $(this).hasClass('btnCmprb-act');

		if ($(this).hasClass('jsCompareListLink')) {
			url = inCompare ? ENTER.utils.generateUrl('compare.delete', {productId: productId}) : ENTER.utils.generateUrl('compare.add', {productId: productId});
		}

		e.preventDefault();
		$.ajax({
			url: url,
			success: function(data) {
				if (data.compare) {
					ENTER.UserModel.compare.removeAll();
					$.each(data.compare, function(i,val){ ENTER.UserModel.compare.push(val) });

					if (!inCompare) {
						showCompareNotice(data.product);
					}
				}
			}
		})
	});

	$body.on('addtocart', function(event, data) {
		if ( data.redirect ) {
			console.warn('redirect');
			document.location.href = data.redirect;
		} else {

			var products = data.products || [],
				cart = ENTER.UserModel.cart();

			if (data.product) {
				products.push(data.product);
			}

			$.each(products, function(key, value){
				var productInCart = ENTER.utils.getObjectWithElement(cart, 'id', value.id);
				if (productInCart) {
					productInCart.quantity(value.quantity);
				} else {
					ENTER.UserModel.cart.unshift(createCartModel(value));
				}
			});
		}
	});
});
