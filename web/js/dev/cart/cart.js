;(function($){

	var $body = $(document.body),
		UserModel = ENTER.UserModel,
		updateTimeoutId = false;

	/* Увеличение и уменьшение товара AJAX */
	$body.on('click', '.numerbox a', function(e){
		var $elem = $(this),
			href = $elem.attr('href');

		if (href != '') {
			e.preventDefault();
			$.ajax({
				url: href,
				success: function(data){
					if (data.success && data.product) {
						if (typeof data.product.quantity != 'undefined' && data.product.quantity > 0) {
							UserModel.productQuantityUpdate(data.product.id, data.product.quantity);
						} else {
							UserModel.removeProductByID(data.product.id);
						}
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
						UserModel.removeProductByID(data.product.id);
						$body.trigger('removeFromCart', [data.product]);
					}
				}
			})
		}

	});

	// Ручное обновление количества продукта
	$body.on('keydown', '.ajaquant', function(e){
		var $input = $(e.target),
			keyCode = e.which,
			$initialQuantity = $input.val();

		/* http://www.cambiaresearch.com/articles/15/javascript-char-codes-key-codes */
		if (keyCode > 36 && keyCode < 41) return true;

		if ((keyCode > 47 && keyCode < 58 ) || (keyCode > 95 && keyCode < 106) || keyCode == 8 || keyCode == 46) {

			if (!updateTimeoutId) updateTimeoutId = setTimeout(function(){

				if ($input.val() == '' || parseInt($input.val(), 10) == 0) {
					updateTimeoutId = false;
					return;
				}

				$.ajax({
					url: $input.data('url'),
					data: {
						quantity: $input.val()
					},
					beforeSend: function() {
						$input.attr('disabled', true)
					}
				}).done(function(resp){
					if (resp.success && resp.product) {
						UserModel.productQuantityUpdate(resp.product.id, resp.product.quantity)
					} else {
						$input.val($initialQuantity);
					}
				}).always(function(){
					updateTimeoutId = false;
					$input.attr('disabled', false).focus();
				})
			}, 500)
		} else {
			return false
		}
	});

}(jQuery));