/**
 * Обработчик для кнопок купить
 *
 * @author		Zaytsev Alexandr
 * @requires	printPrice
 * @param		{event} e
 */
var BuyButton = function(e){
	e.stopPropagation();

	var button = $(this);

	if (button.hasClass('disabled')) {
		return false;
	}
	if (button.hasClass('active')) {
		return false;
	}

	var url = button.attr('href');

	var addToCart = function(data) {
		if (data.success) {
			button.addClass('mBought');
			button.html('В корзине');
			// kissAnalytics(data);
			// sendAnalytics(button);
			
			if (blackBox) {
				var basket = data.data;
				var product = data.result.product;
				var tmpitem = {
					'title': product.name,
					'price' : printPrice(product.price),
					'imgSrc': 'need image link',
					'totalQuan': basket.full_quantity,
					'totalSum': printPrice(basket.full_price),
					'linkToOrder': basket.link,
				};
				blackBox.basket().add(tmpitem);
			}
		}
	};
	$.get(url, addToCart);
	return false;
}

$('.jsBuyButton').live('click', BuyButton);


/**
 * ADD Analytics for buyButton!!!
 */