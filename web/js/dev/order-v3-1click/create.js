;(function($) {

    var
        $form = $('.jsOrderV3OneClickForm')
    ;


    // отслеживаем смену региона
    $form.on('submit', function(e){
        var
            $el = $(this),
            data = $el.serializeArray()
        ;

        $.post($el.attr('action'), data)
            .done(function(response) {
                if (typeof response.result !== 'undefined') {
                    $('#jsOneClickContentPage').hide();
                    $('#jsOneClickContent').append(response.result.page);

                    $('body').trigger('trackUserAction', ['3_1 Оформить_успешно']);

					// Счётчик GetIntent (BlackFriday)
					(function() {
						if (response.result.lastPartner != 'blackfridaysale') {
							return '';
						}

						$.each(response.result.orders, function(index, order) {
							var products = [];
							var revenue = 0;
							$.each(order.products, function(index, product) {
								products.push({
									id: product.id + '',
									price: product.price + '',
									quantity: parseInt(product.quantity)
								});

								revenue += parseFloat(product.price) * parseInt(product.quantity);
							});

							ENTER.counters.callGetIntentCounter({
								type: "CONVERSION",
								orderId: order.id + '',
								orderProducts: products,
								orderRevenue: revenue + ''
							});
						});
					})();

					// Счётчик RetailRocket
					(function() {
						$.each(response.result.orders, function(index, order) {
							var products = [];
							$.each(order.products, function(index, product) {
								products.push({
									id: product.id,
									qnt: product.quantity,
									price: product.price
								});
							});

							ENTER.counters.callRetailRocketCounter('order.complete', {
								transaction: order.id,
								items: products
							});
						});
					})();
                }

                var $orderContainer = $('#jsOrderV3OneClickOrder');
                if ($orderContainer.length) {
                    $.get($orderContainer.data('url')).done(function(response) {
                        $orderContainer.html(response.result.page);

						if (typeof ENTER.utils.sendOrderToGA == 'function') ENTER.utils.sendOrderToGA($('#jsOrder').data('value'));

                    });
                }
            })
            .fail(function(jqXHR){
                var response = $.parseJSON(jqXHR.responseText);

                if (response.result && response.result.errorContent) {
                    $('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
                }

                var error = (response.result && response.result.error) ? response.result.error : {};

                $('body').trigger('trackUserAction', ['3_2 Оформить_ошибка', 'Поле ошибки: '+ ((typeof error !== 'undefined') ? error.join(', ') : '')]);
            })
        ;

        e.preventDefault();
    })

})(jQuery);