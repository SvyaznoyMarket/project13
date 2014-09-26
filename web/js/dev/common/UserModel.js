;(function($) {

	var $body = $(document.body),
		userInfoURL = ENTER.config.pageConfig.userUrl + '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000),

		User = function(){

			var self = this;

			self.name = ko.observable();
			self.link = ko.observable();
			self.hasEnterprizeCoupon = ko.observable(false);

			self.cart = ko.observableArray();
			self.compare = ko.observableArray();

			self.isProductInCompare = function(elem){
				console.log('isProductInCompare', elem);
				return $.grep(self.compare, function(val){return val.id == $(elem).data('id')}).length == 0
			};

			self.cartProductQuantity = ko.computed(function(){
				return self.cart().length;
			});

			self.ajaxUserInfo = function(data) {
				if (data.user) {
					if (data.user.name) self.name(data.user.name);
					if (data.user.link) self.link(data.user.link);
					if (data.user.hasEnterprizeCoupon) self.hasEnterprizeCoupon(true);
				}
				if (data.cartProducts && $.isArray(data.cartProducts)) {
					$.each(data.cartProducts, function(i,val){ self.cart.unshift(val) });
				}
				if (data.compare) {
					$.each(data.compare, function(i,val){ self.compare.push(val) })
				}
			}

		},
		startTime, endTime, spendTime;

	ENTER.UserModel = new User();

	// Биндинги на нужные элементы
	// Топбар, кнопка Купить на странице продукта, листинги
	$('.topbarfix, .bWidgetBuy, .bListing').each(function() {ko.applyBindings(ENTER.UserModel, this) });

	// Обновление данных о пользователе и корзине
	$.ajax({
		url: userInfoURL,
		beforeSend: function(){
			startTime = new Date().getTime();
		},
		success: function(data){
			endTime = new Date().getTime();
			spendTime = endTime - startTime;
			ENTER.UserModel.ajaxUserInfo(data);
			if (typeof ga == 'function') {
				ga('send', 'timing', 'userInfo', 'Load User Info', spendTime);
				console.log('[Google Analytics] Send user/info timing: %s ms', spendTime)
			}
		}
	});

	$('.jsCompareLink, .jsCompareList').on('click', function(e){
		var url = this.href;
		if ($(this).hasClass('jsCompareList')) {
			url = $(this).hasClass('btnCmprb-act') ? '/compare/delete-product/'+$(this).data('id') : '/compare/add-product/'+$(this).data('id');
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
	})

}(jQuery));
