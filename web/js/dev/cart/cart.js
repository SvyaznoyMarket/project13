;(function($){

	var $body = $(document.body);

	/* Увеличение и уменьшение товара AJAX */
	$body.on('click', '.numerbox a', function(e){
		var $elem = $(this),
			href = $elem.attr('href');

		if (href != '') {
			e.preventDefault();
			$.ajax({
				url: href,
				success: function(data){
					if (data.success && data.product && typeof data.product.quantity != 'undefined') {
						ENTER.UserModel.productQuantityUpdate(data.product.id, data.product.quantity);
					} else if (data.success && data.product && typeof data.product.quantity == 'undefined') {
						ENTER.UserModel.removeProductByID(data.product.id);
					}
				}
			})
		}

	});

	/* Удаление продукта AJAX */
	$body.on('click', '.jsCartDeleteProduct', function(e){
		var href = $(this).attr('href');

		if (href != '') {
			e.preventDefault();
			$.ajax({
				url: href,
				success: function(data){
					if (data.success && data.product) {
						ENTER.UserModel.removeProductByID(data.product.id);
						$body.trigger('removeFromCart', [data.product]);
					}
				}
			})
		}

	})

}(jQuery));