;(function($) {
	ko.bindingHandlers.buyButtonBinding = {
		update: function(element, valueAccessor) {
			var cart = ko.unwrap(valueAccessor()),
				$elem = $(element),
				productId = $elem.data('product-id'),
				productUi = $elem.data('product-ui'),
				productUrl = $elem.data('data-product-url'),
				inShopStockOnly = $elem.data('in-shop-stock-only'),
				inShopShowroomOnly = $elem.data('in-shop-showroom-only'),
				isBuyable = $elem.data('is-buyable'),
				statusId = $elem.data('status-id'),
				noUpdate = $elem.data('noUpdate'),
				isSlot = $elem.data('is-slot'),
				sender = $elem.data('sender'),
				sender2 = $elem.data('sender2')
				;

			if (sender && typeof sender == 'object') {
				sender = {sender: sender};
			} else {
				sender = {};
			}

			if (sender2 && typeof sender2 == 'string') {
				sender2 = {sender2: sender2};
			} else {
				sender2 = {};
			}

			if (typeof isBuyable != 'undefined' && !isBuyable) {
				$elem
					.text(typeof inShopShowroomOnly != 'undefined' && inShopShowroomOnly ? 'На витрине' : 'Нет')
					.addClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('js-orderButton jsBuyButton')
					.attr('href', '#');
			} else if (typeof statusId != 'undefined' && 5 == statusId) { // SITE-2924
				$elem
					.text('Нет')
					.addClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('js-orderButton jsBuyButton')
					.attr('href', '#');
			} else if (typeof isSlot != 'undefined' && isSlot) {
				$elem
					.text('Как купить?')
					.removeClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('btn btn--slot js-orderButton js-slotButton')
					.attr('href', '#');
			} else if (typeof inShopStockOnly != 'undefined' && inShopStockOnly && ENTER.config.pageConfig.user.region.forceDefaultBuy) { // Резерв товара
				$elem
					.text('Купить')
					.removeClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('js-orderButton jsOneClickButton')
					.removeClass('jsBuyButton')
					.attr('href', productUrl + '#one-click');
			} else if ($elem.hasClass('mBought')) {
				$elem
					.text('Купить')
					.removeClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('js-orderButton jsBuyButton')
					.attr('href', ENTER.utils.router.generateUrl('cart.product.setList', $.extend({products: [{ui: productUi, quantity: '+1', up: '1'}]}, sender, sender2)));
			}
		}
	};

	ko.bindingHandlers.compareButtonBinding = {
		update: function(element, valueAccessor) {
			var compare = ko.unwrap(valueAccessor()),
				$elem = $(element),
				productId = $elem.data('id'),
				typeId = $elem.data('type-id'),
                activeLinkClass = 'product-card-tools__lk--active',
                buttonText = 'Сравнить',
				comparableProducts;

			var location = '';
			if (ENTER.config.pageConfig.location.indexOf('listing') != -1) {
				location = 'listing';
			} else if (ENTER.config.pageConfig.location.indexOf('product') != -1) {
				location = 'product';
			}

			if (ENTER.utils.getObjectWithElement(compare, 'id', productId)) {
				$elem
					.addClass('btnCmpr-act')
					.find('.jsCompareLink').addClass(activeLinkClass).attr('href', ENTER.utils.router.generateUrl('compare.delete', {productId: productId}))
					.find('span').text('Убрать из сравнения');
			} else {
				$elem
					.removeClass('btnCmpr-act')
					.find('.jsCompareLink').removeClass(activeLinkClass).attr('href', ENTER.utils.router.generateUrl('compare.add', {productId: productId, location: location}))
					.find('span').text(buttonText);
			}
	
			// массив продуктов, которые можно сравнить с данным продуктом
			comparableProducts = $.grep(compare, function(val){ return typeId == val.typeId; });
	
			if (comparableProducts.length > 1) {
				$elem.find('.btnCmpr_more').show().find('.btnCmpr_more_qn').text(comparableProducts.length);
			} else {
				$elem.find('.btnCmpr_more').hide();
			}
		}
	};
	
	ko.bindingHandlers.compareListBinding = {
		update: function(element, valueAccessor) {
			var compare = ko.unwrap(valueAccessor()),
				$elem = $(element),
				productId = $elem.data('id');

			var location = '';
			if (ENTER.config.pageConfig.location.indexOf('listing') != -1) {
				location = 'listing';
			} else if (ENTER.config.pageConfig.location.indexOf('product') != -1) {
				location = 'product';
			}

			if (ENTER.utils.getObjectWithElement(compare, 'id', productId)) {
				$elem.addClass('btnCmprb-act').attr('href', ENTER.utils.router.generateUrl('compare.delete', {productId: productId}));
			} else {
				$elem.removeClass('btnCmprb-act').attr('href', ENTER.utils.router.generateUrl('compare.add', {productId: productId, location: location}));
			}
		}
	};
}(jQuery));