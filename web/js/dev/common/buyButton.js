/**
 * Обработчик для кнопок купить
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, BlackBox
 * @param		{event}		e
 * @param		{Boolean}	anyway Если true событие будет все равно выполнено
 */
;(function(){

	/**
	 * Добавление в корзину на сервере. Получение данных о покупке и состоянии корзины. Маркировка кнопок.
	 * 
	 * @param  {Event}	e
	 */
	var buy = function(e){
		var button = $(this);
		var url = button.attr('href');

		var addToCart = function(data) {
			if (!data.success) {
				return false;
			}

			var groupBtn = button.data('group');

			$('.jsBuyButton[data-group="'+groupBtn+'"]').html('В корзине').addClass('mBought').attr('href','/cart');
			$("body").trigger("addtocart", [data]);
		};

		$.get(url, addToCart);
	};

	/**
	 * Хандлер кнопки купить
	 * 
	 * @param  {Event}	e
	 */
	var BuyButtonHandler = function(e){
		e.stopPropagation();

		var button = $(this);

		if (button.hasClass('mDisabled')) {
			return false;
		}
		if (button.hasClass('mBought')) {
			var url = button.attr('href');
			document.location.href(url);
			return false;
		}

		button.trigger('buy', buy);
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
		for (var i = 0, len = markActionInfo.product.length; i < len; i++){
			$('.'+markActionInfo.product[i].id).html('В корзине').addClass('mBought').attr('href','/cart');
		}
	};
	$("body").bind('markcartbutton', markCartButton);
	
	$(document).ready(function() {
		$('.jsBuyButton').on('click', BuyButtonHandler);
		$('.jsBuyButton').on('buy', buy);
	});
}());


/**
 * Показ окна о совершенной покупке, парсинг данных от сервера, аналитика
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, printPrice, BlackBox
 * @param		{event}		event 
 * @param		{Object}	data	данные о том что кладется в корзину
 */
;(function(){
	var buyProcessing = function(event, data){
		// kissAnalytics();
		// sendAnalytics();

		if (!blackBox) {
			return false;
		}

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
	};

	$(document).ready(function() {
		$("body").bind('addtocart', buyProcessing);
	});
}());