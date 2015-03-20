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

						try {
							if (0 === data.cart.products.length) {
								setTimeout(function() { window.location.reload(); }, 100);
							}
						} catch (error) { console.error(error);	}
					}
				}
			})
		}

	});

	// Событие добавления в корзину SITE-5289
	$body.on('addtocart', function ga_addtocart(event, data) {
		try {
			if (1 == data.cart.products.length) {
				console.info('#js-cart-firstRecommendation');
				var $container = $('#js-cart-firstRecommendation');
				if ($container.length) {
					$container.html($($container.text()));
					$container.find('.js-slider').goodsSlider();
					$container.show();
				}
			}
		} catch (error) {
			console.error(error);
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