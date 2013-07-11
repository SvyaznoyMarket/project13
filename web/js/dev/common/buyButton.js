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
	/**
	 * KISS Аналитика для добавления в корзину
	 */
	var kissAnalytics = function(data){
		if (data.result.product){
			var productData = data.result.product
			var nowUrl = window.location.href
			var toKISS_pr = {
				'Add to Cart SKU':productData.article,
				'Add to Cart SKU Quantity':productData.quantity,
				'Add to Cart Product Name':productData.name,
				'Add to Cart Root category':productData.category[0].name,
				'Add to Cart Root ID':productData.category[0].id,
				'Add to Cart Category name':productData.category[productData.category.length-1].name,
				'Add to Cart Category ID':productData.category[productData.category.length-1].id,
				'Add to Cart SKU Price':productData.price,
				'Add to Cart Page URL':nowUrl,
				'Add to Cart F1 Quantity':productData.serviceQuantity,
			}
			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Add to Cart', toKISS_pr ])
			}
		}
		if (data.result.service){
			var serviceData = data.result.service
			var productData = data.result.product
			var toKISS_serv = {
				'Add F1 F1 Name':serviceData.name,
				'Add F1 F1 Price':serviceData.price,
				'Add F1 SKU':productData.article,
				'Add F1 Product Name':productData.name,
				'Add F1 Root category':productData.category[0].name,
				'Add F1 Root ID':productData.category[0].id,
				'Add F1 Category name':productData.category[productData.category.length-1].name,
				'Add F1 Category ID':productData.category[productData.category.length-1].id,
			}
			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Add F1', toKISS_serv ])
			}
		}
		if (data.result.warranty){
			var warrantyData = data.result.warranty
			var productData = data.result.product
			var toKISS_wrnt = {
				'Add Warranty Warranty Name':warrantyData.name,
				'Add Warranty Warranty Price':warrantyData.price,
				'Add Warranty SKU':productData.article,
				'Add Warranty Product Name':productData.name,
				'Add Warranty Root category':productData.category[0].name,
				'Add Warranty Root ID':productData.category[0].id,
				'Add Warranty Category name':productData.category[productData.category.length-1].name,
				'Add Warranty Category ID':productData.category[productData.category.length-1].id,
			}
			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Add Warranty', toKISS_wrnt ])
			}
		}
	};


	var buyProcessing = function(event, data){
		kissAnalytics(data);
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