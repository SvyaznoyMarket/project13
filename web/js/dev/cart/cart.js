;(function($){

	var kitPopupItems = ko.observableArray(),
		ajaxProducts = ko.observableArray(),
		$body = $(document.body);

	$('.product_kit-data').on('click', function(e){
		var items = $(this).data('value');
		kitPopupItems.removeAll(); // clear
		$.each(items, function(i, val){ kitPopupItems.push(val) });
		$('#kitPopup').lightbox_me({
			centered: true
		});
		e.preventDefault();
	});

	ko.applyBindings({ kitPopupItems: kitPopupItems	}, $('#kitPopup')[0]);

	ko.applyBindings({ ajaxProducts: ajaxProducts }, $('.jsKnockoutCart')[0]);

	$body.on('addtocart', function(e, data) {
		if (!data.product) return;
		ajaxProducts.push(data);
		$('.basketSum__price .price').text( printPrice(data.cart.full_price) );
	});

}(jQuery));