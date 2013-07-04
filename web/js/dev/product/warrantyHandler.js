;(function(){
	var addWarranty = function(el){
		var url = el.data('set-url');
		var resFromServer = function(res){
			if (!res.success){
				return false;
			}
			console.log(res);
			// if (blackBox) {
			// 	var basket = data.cart;
			// 	var product = data.product;
			// 	var tmpitem = {
			// 		'title': product.name,
			// 		'price' : printPrice(product.price),
			// 		'imgSrc': product.img,
			// 		'productLink': product.link,
			// 		'totalQuan': basket.full_quantity,
			// 		'totalSum': printPrice(basket.full_price),
			// 		'linkToOrder': basket.link,
			// 	};
			// 	blackBox.basket().add(tmpitem);
			// }
		};
		$.ajax({
			type: 'GET',
			url: url,
			success: resFromServer
		});
	};

	var delWarranty = function(el){
		var url = el.data('delete-url');
		var resFromServer = function(res){
			if (!res.success){
				return false;
			}
			console.log(res);
			
			if (blackBox) {
				var basket = res.cart;
				var tmpitem = {
					'cartQ': basket.full_quantity,
					'cartSum' : printPrice(basket.full_price)
				};
				blackBox.basket().update(tmpitem);
			}
		};
		$.ajax({
			type: 'GET',
			url: url,
			success: resFromServer
		});
	};


	$('.jsCustomRadio').customRadio({
		onChecked: addWarranty,
		onUncheckedGroup: delWarranty
	});
}());