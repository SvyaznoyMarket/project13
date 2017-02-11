;(function($) {
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