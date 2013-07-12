/**
 * Расчет доставки
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, simple_templating
 */
(function(){
	if (!$('#jsProductCard').length){
		return false;
	}

	var widgetBox = $('.bWidgetBuy__eDelivery');
	var productInfo = $('#jsProductCard').data('value');

	var url = '/ajax/product/delivery'; // HARDCODE!

	var dataToSend = {
		product:[
			{'id': productInfo.id}
		]
	};

	var showAvalShop = function(){
		var popup = $('#avalibleShop');

		var position = {
			latitude: $(this).data('lat'),
			longitude: $(this).data('lng')
		};

		$('#ymaps-avalshops').css({'width':600, 'height':400});
		
		$.when(MapInterface.ready( 'yandex', {
			yandex: $('#infowindowtmpl'), 
			google: $('#infowindowtmpl')
		})).done(function(){
			MapInterface.onePoint( position, 'ymaps-avalshops' );
		});

		popup.css({'width':600, 'height':400});
		popup.lightbox_me({
			centered: true,
			onLoad: function() {
			},
			onClose: function(){
				$('#ymaps-avalshops').empty();
			}
		});

		return false;
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
					var templateStandart = tmpl('widget_delivery_standart', standartData);
					standartBox.html(templateStandart);
					break;

				case 'self':
					var selfBox = widgetBox.find('.bWidgetBuy__eDelivery-free');
					var selfData = {
						price: deliveryInfo[i].price,
						dateString: deliveryInfo[i].date.name
					};
					var templateSelf = tmpl('widget_delivery_self', selfData);
					selfBox.html(templateSelf);
					break;

				case 'now':
					var nowBox = widgetBox.find('.bWidgetBuy__eDelivery-now');
					var toggleBtn = nowBox.find('.bWidgetBuy__eDelivery-nowClick');
					var shopList = nowBox.find('.bDeliveryFreeAddress');
					if (!deliveryInfo[i].shop.length){
						break;
					}

					for (var j = deliveryInfo[i].shop.length - 1; j >= 0; j--) {
						var shopInfo = {
							name: deliveryInfo[i].shop[j].name,
							lat: deliveryInfo[i].shop[j].latitude,
							lng: deliveryInfo[i].shop[j].longitude
						};
						var templateNow = tmpl('widget_delivery_shop',shopInfo);
						shopList.append(templateNow);
					}

					var shopToggle = function(){
						nowBox.toggleClass('mOpen');
						nowBox.toggleClass('mClose');
					};

					nowBox.show();
					$('.bDeliveryFreeAddress__eLink').bind('click', showAvalShop);
					toggleBtn.bind('click', shopToggle);
					break;
			}
		}
	};

	$.ajax({
		type: 'POST',
		url: url,
		data: dataToSend,
		success: resFromSerever
	});
}());