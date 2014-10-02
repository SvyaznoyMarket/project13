;(function($) {
	$(function(){
		var $body = $(document.body),
			userInfoURL = ENTER.config.pageConfig.userUrl + '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000),
			startTime, endTime, spendTime;

		ENTER.UserModel = new function(){
			var self = this;

			self.name = ko.observable();
			self.firstName = ko.observable();
			self.lastName = ko.observable();
			self.link = ko.observable();

			self.cart = ko.observableArray();
			self.compare = ko.observableArray();

			self.isProductInCompare = function(elem){
				console.log('isProductInCompare', elem);
				return $.grep(self.compare, function(val){return val.id == $(elem).data('id')}).length == 0
			};

			self.cartProductQuantity = ko.computed(function(){
				return self.cart().length;
			});

			self.update = function(data) {
				if (data.user) {
					if (data.user.name) self.name(data.user.name);
					if (data.user.firstName) self.firstName(data.user.firstName);
					if (data.user.lastName) self.lastName(data.user.lastName);
					if (data.user.link) self.link(data.user.link);
				}
				if (data.cartProducts && $.isArray(data.cartProducts)) {
					$.each(data.cartProducts, function(i,val){ self.cart.unshift(val) });
				}
				if (data.compare) {
					$.each(data.compare, function(i,val){ self.compare.push(val) })
				}
			}
		};

		// Биндинги на нужные элементы
		// Топбар, кнопка Купить на странице продукта, листинги, слайдер аксессуаров
		$('.js-topbarfix, .js-WidgetBuy, .js-listing, .js-jewelListing, .js-gridListing, .js-accessorize, .js-enterprize').each(function() {ko.applyBindings(ENTER.UserModel, this) });

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

				var authorized_cookie = '_authorized';
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

		$('.jsCompareLink, .jsCompareList').on('click', function(e){
			var url = this.href,
				productId = $(this).data('id');

			if ($(this).hasClass('jsCompareList')) {
				url = $(this).hasClass('btnCmprb-act') ? ENTER.utils.generateUrl('compare.delete', {productId: productId}) : ENTER.utils.generateUrl('compare.add', {productId: productId});
			}

			e.preventDefault();
			$.ajax({
				url: url,
				success: function(data) {
					if (data.compare) {
						ENTER.UserModel.compare.removeAll();
						$.each(data.compare, function(i,val){ ENTER.UserModel.compare.push(val) })
					}
				}
			})
		});
	});
}(jQuery));
