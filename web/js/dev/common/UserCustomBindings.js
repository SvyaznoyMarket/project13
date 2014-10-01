;(function($) {
	var buyButtonBinding = function(element, valueAccessor) {
		var cart = ko.unwrap(valueAccessor()),
			$elem = $(element),
			productId = $elem.data('group'),
			inCart = false;

		$.each(cart, function(i,val){
			if (val.id == productId) {
				$elem.text('В корзине').addClass('mBought').attr('href','/cart');
				inCart = true;
				return false;
			}
		});

		if (!inCart && $elem.hasClass('mBought')) {
			$elem.html('Купить').removeClass('mBought').attr('href', ENTER.utils.generateUrl('cart.product.set', {productId: productId}));
		}
	};

	var buySpinnerBinding = function(element, valueAccessor) {
		var cart = ko.unwrap(valueAccessor()),
			$elem = $(element);
		$elem.removeClass('mDisabled').find('input').attr('disabled', false);
		$.each(cart, function(i,val){
			if (val.id == $elem.data('product-id')) {
				$elem.addClass('mDisabled');
				$elem.find('input').val(val.quantity).attr('disabled', true);
			}
		})
	};

	/* TODO эта херня уже нуждается в рефакторинге :) */
	var compareButtonBinding = function(element, valueAccessor) {
		var compare = ko.unwrap(valueAccessor()),
			$elem = $(element),
			productId = $elem.data('id'),
			categoryId = $elem.data('category-id'),
			comparableProducts;

		var inCompare = false;
		$.each(compare, function(i,val) {
			if (val.id == productId) {
				inCompare = true;
				$elem
					.addClass('btnCmpr-act')
					.find('a.btnCmpr_lk').addClass('btnCmpr_lk-act').attr('href', ENTER.utils.generateUrl('compare.delete', {productId: productId}))
					.find('span').text('Убрать из сравнения');

				return false;
			}
		});

		if (!inCompare) {
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
	};

	/* TODO и эта херня уже нуждается в рефакторинге :) */
	var compareListBinding = function(element, valueAccessor) {
		var compare = ko.unwrap(valueAccessor()),
			$elem = $(element),
			productId = $elem.data('id');

		if ($.grep(compare, function(val){ return val.id == productId}).length > 0) {
			$elem.addClass('btnCmprb-act').attr('href', ENTER.utils.generateUrl('compare.delete', {productId: productId}));
		} else {
			$elem.removeClass('btnCmprb-act').attr('href', ENTER.utils.generateUrl('compare.add', {productId: productId}));
		}
	};

	ko.bindingHandlers.compareListBinding = {
		init: compareListBinding,
		update: compareListBinding
	};

	ko.bindingHandlers.buyButtonBinding = {
		init: buyButtonBinding,
		update: buyButtonBinding
	};

	ko.bindingHandlers.buySpinnerBinding = {
		init: buySpinnerBinding,
		update: buySpinnerBinding
	};

	ko.bindingHandlers.compareButtonBinding = {
		init: compareButtonBinding,
		update: compareButtonBinding
	};

}(jQuery));