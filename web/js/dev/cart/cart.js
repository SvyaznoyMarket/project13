;(function($){

	var kitPopupItems = ko.observableArray(),
		ajaxProducts = ko.observableArray(),
		$body = $(document.body),
		ajaxRoute = '/cart/add-product/';

	$body.on('addtocart', function(e, data) {
		if (!data.product) return;
		ajaxProducts.push(data);
		$('.basketSum__price .price').text( printPrice(data.cart.full_price) );
	});

	/* Увеличение и уменьшение товара AJAX */
	$body.on('click', '.numerbox a, .jsCartDeleteProduct', function(e){
		var $elem = $(this),
			href = $elem.attr('href');

		if (href != '') {
			e.preventDefault();
			$.ajax({
				url: href,
				success: function(data){
					console.log(data);
					if (data.success && data.product && typeof data.product.quantity != 'undefined') {
						ENTER.UserModel.productQuantityUpdate(data.product.id, data.product.quantity);
					} else if (data.success && data.product && typeof data.product.quantity == 'undefined') {
						ENTER.UserModel.removeProductByID(data.product.id);
					}
				}
			})
		}

	})

}(jQuery));