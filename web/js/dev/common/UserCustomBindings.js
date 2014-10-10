;(function($) {
	ko.bindingHandlers.buyButtonBinding = {
		update: function(element, valueAccessor) {
			var cart = ko.unwrap(valueAccessor()),
				$elem = $(element),
				productId = $elem.data('product-id'),
				inShopStockOnly = $elem.data('in-shop-stock-only'),
				inShopShowroomOnly = $elem.data('in-shop-showroom-only'),
				isBuyable = $elem.data('is-buyable'),
				statusId = $elem.data('status-id');
			
			if (typeof isBuyable != 'undefined' && !isBuyable) {
				$elem
					.text(typeof inShopShowroomOnly != 'undefined' && inShopShowroomOnly ? 'На витрине' : 'Нет')
					.addClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('jsBuyButton')
					.attr('href', '#');
			} else if (typeof statusId != 'undefined' && 5 == statusId) { // SITE-2924
				$elem
					.text('Купить')
					.addClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('jsBuyButton')
					.attr('href', '#');
			} else if (typeof inShopStockOnly != 'undefined' && inShopStockOnly && ENTER.config.pageConfig.user.region.forceDefaultBuy) {
				$elem
					.text('Резерв')
					.removeClass('mDisabled')
					.addClass('mShopsOnly')
					.removeClass('mBought')
					.removeClass('jsBuyButton')
					.attr('href', ENTER.utils.generateUrl('cart.oneClick.product.set', {productId: productId}));
			} else if (ENTER.utils.getObjectWithElement(cart, 'id', productId)) {
				$elem
					.text('В корзине')
					.removeClass('mDisabled')
					.removeClass('mShopsOnly')
					.addClass('mBought')
					.removeClass('jsBuyButton')
					.attr('href', ENTER.utils.generateUrl('cart'));
			} else if ($elem.hasClass('mBought')) {
				$elem
					.text('Купить')
					.removeClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('jsBuyButton')
					.attr('href', ENTER.utils.generateUrl('cart.product.set', {productId: productId}));
			}
		}
	};

	ko.bindingHandlers.buySpinnerBinding = {
		update: function(element, valueAccessor) {
			var cart = ko.unwrap(valueAccessor()),
				$elem = $(element);
			
			$elem.removeClass('mDisabled').find('input').attr('disabled', false);
			$.each(cart, function(key, value){
				if (this.id == $elem.data('product-id')) {
					$elem.addClass('mDisabled');
					$elem.find('input').val(value.quantity()).attr('disabled', true);
				}
			})
		}
	};

	ko.bindingHandlers.compareButtonBinding = {
		update: function(element, valueAccessor) {
			var compare = ko.unwrap(valueAccessor()),
				$elem = $(element),
				productId = $elem.data('id'),
				categoryId = $elem.data('category-id'),
				comparableProducts;
			
			if (ENTER.utils.getObjectWithElement(compare, 'id', productId)) {
				$elem
					.addClass('btnCmpr-act')
					.find('a.btnCmpr_lk').addClass('btnCmpr_lk-act').attr('href', ENTER.utils.generateUrl('compare.delete', {productId: productId}))
					.find('span').text('Убрать из сравнения');
			} else {
				$elem
					.removeClass('btnCmpr-act')
					.find('a.btnCmpr_lk').removeClass('btnCmpr_lk-act').attr('href', ENTER.utils.generateUrl('compare.add', {productId: productId}))
					.find('span').text('Добавить к сравнению');
			}
	
			// массив продуктов, которые можно сравнить с данным продуктом
			comparableProducts = $.grep(compare, function(val){ return categoryId == val.categoryId; });
	
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
	
			if (ENTER.utils.getObjectWithElement(compare, 'id', productId)) {
				$elem.addClass('btnCmprb-act').attr('href', ENTER.utils.generateUrl('compare.delete', {productId: productId}));
			} else {
				$elem.removeClass('btnCmprb-act').attr('href', ENTER.utils.generateUrl('compare.add', {productId: productId}));
			}
		}
	};
}(jQuery));