/**
 * Обработчик для кнопок купить
 *
 * @author		Zaytsev Alexandr
 * @requires	printPrice
 * @param		{event} e
 */
;(function(){
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
					var basket = data.cart;
					var product = data.product;
					var tmpitem = {
						'title': product.name,
						'price' : printPrice(product.price),
						'imgSrc': product.img,
						'productLink': product.link,
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
	};

	/**
	 * Маркировка кнопок «Купить»
	 * см.BlackBox startAction
	 * 
	 * @param	{event}		event          
	 * @param	{Object}	markActionInfo Данные полученые из Action
	 */
	var markCartButton = function(event, markActionInfo){
		for (var i = 0, len = markActionInfo.button.length; i < len; i++){
			$('#'+markActionInfo.button[i].id).html('В корзине').addClass('mBought');
		}
	};
	$("body").bind('markcartbutton', markCartButton);
	
	$(document).ready(function() {
		$('.jsBuyButton').live('click', BuyButton);
	});

	/**
	 * ADD Analytics for buyButton!!!
	 */
}());