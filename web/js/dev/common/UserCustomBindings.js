;(function($) {

	// Обновление кнопки "Купить" у продуктов
	// TODO добавить ko.applyBindings(ENTER.UserModel, context) на product-recommend

	var buyButtonBinding = function(element, valueAccessor) {
		var cart = ko.unwrap(valueAccessor()),
			$elem = $(element);
		$.each(cart, function(i,val){
			if (val.id == $elem.data('group')) $elem.text('В корзине').addClass('mBought').attr('href','/cart');
		})
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
			id = $elem.data('id'),
			categoryId = $elem.data('category-id'),
			comparableProducts;

		// ничего не модифицируем, если продукта нет в сравнении
		if ($.grep(compare, function(val){ return id == val.id; }).length == 0) {
			$elem.removeClass('btnCmpr-act')
				.find('a.btnCmpr_lk').removeClass('btnCmpr_lk-act').attr('href', $elem.data('add-url'))
				.find('span').text('Добавить к сравнению');
			return;
		}

		$.each(compare, function(i,val){
			if (val.id == id) {
				$elem.addClass('btnCmpr-act')
					.find('a.btnCmpr_lk').addClass('btnCmpr_lk-act').attr('href', $elem.data('delete-url'))
					.find('span').text('Убрать из сравнения');
			}
		});

		// массив продуктов, которые можно сравнить с данным продуктом
		comparableProducts = $.grep(compare, function(val){ return categoryId == val.categoryId; });

		if (comparableProducts.length > 1) {
			$elem.find('.btnCmpr_more').show().find('.btnCmpr_more_qn').text(comparableProducts.length);
		}
	};

	/* TODO и эта херня уже нуждается в рефакторинге :) */
	var compareListBinding = function(element, valueAccessor) {
		var compare = ko.unwrap(valueAccessor()),
			$elem = $(element);

		if ($.grep(compare, function(val){ return val.id == $elem.data('id')}).length > 0) {
			$elem.addClass('btnCmprb-act').attr('href','/compare/delete-product/'+$elem.data('id'));
		} else {
			$elem.removeClass('btnCmprb-act').attr('href','/compare/add-product/'+$elem.data('id'));
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