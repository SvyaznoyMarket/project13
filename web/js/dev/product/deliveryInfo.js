/**
 * Расчет доставки
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, simple_templating
 */
(function(){
	var widgetBox = $('.bWidgetBuy__eDelivery');
	var productInfo = $('#jsProductCard').data('value');

	var url = '/ajax/product/delivery'; // HARDCODE!

	var dataToSend = {
		product:[
			{'id': productInfo.id}
		]
	};

	var resFromSerever = function(res){
		if (!res.success){
			return false;
		}

		var deliveryInfo = res.product[0].delivery;
		
		for (var i = deliveryInfo.length - 1; i >= 0; i--) {
			switch (deliveryInfo[i].token){
				case 'standart':
					var standartBox = widgetBox.find('.bWidgetBuy__eDelivery-price');
					var standartData = {
						price: deliveryInfo[i].price,
						dateString: deliveryInfo[i].date.name
					};
					var template = tmpl('widget_delivery_standart', standartData);
					standartBox.html(template);
					break;

				case 'self':
					var selfBox = widgetBox.find('.bWidgetBuy__eDelivery-free');
					var selfData = {
						price: deliveryInfo[i].price,
						dateString: deliveryInfo[i].date.name
					};
					var template = tmpl('widget_delivery_self', selfData);
					selfBox.html(template);
					break;

				case 'now':
					var nowBox = widgetBox.find('.bWidgetBuy__eDelivery-now');
					var shopList = nowBox.find('.bDeliveryFreeAddress');
					if (!deliveryInfo[i].shop.length){
						return false;
					}

					for (var j = deliveryInfo[i].shop.length - 1; j >= 0; j--) {
						var shopInfo = {
							name: deliveryInfo[i].shop[j].name
						};
						var shopTmpl = tmpl('widget_delivery_shop',shopInfo);
						shopList.append(shopTmpl);
					};
					nowBox.show();
					nowBox.bind('click', function(){
						nowBox.toggleClass('mOpen');
						nowBox.toggleClass('mClose');
					})
					break;
			};
		}
	};

	$.ajax({
		type: 'POST',
		url: url,
		data: dataToSend,
		success: resFromSerever
	});
}());