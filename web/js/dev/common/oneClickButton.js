$(function() {
	var oneClickOpening = false;
	$('body').on('click', '.jsOneClickButton', function(e) {
		console.info('show one click form');

		e.preventDefault();

		if (oneClickOpening) {
			return;
		}

		var $button = $(e.currentTarget);
		oneClickOpening = true;

		$.ajax({
			url: ENTER.utils.generateUrl('orderV3OneClick.form', {
				productUid: $button.data('product-ui'),
				sender: ENTER.utils.analytics.productPageSenders.get($button),
				sender2: ENTER.utils.analytics.productPageSenders2.get($button)
			}),
			type: 'POST',
			dataType: 'json',
			closeClick: false,
			success: function(result) {
				if (!result.form) {
					return;
				}

				$('body').append(result.form);
				var $popup = $('#jsOneClickContent');
				if (!$popup.length) {
					return;
				}

				var
					shopId = $button.data('shop'),
					buyProducts = [
						{id: $button.data('product-id'), quantity: 1}
					];

				$('.jsProductImgPopup').trigger('close'); // закрываем окно просмотра фото в новой карточке товара

				$('.js-order-oneclick-delivery-toggle-btn').on('click', function(e) {
					var button = $(e.currentTarget),
						$toggleNote = $('.js-order-oneclick-delivery-toggle-btn-note'),
						$toggleBox = $('.js-order-oneclick-delivery-toggle');

					button.toggleClass('orderU_lgnd-tggl-cur');
					$toggleBox.toggle();
					$toggleNote.toggleClass('orderU_lgnd_tgglnote-cur');

					$('body').trigger('trackUserAction', ['2 Способ получения']);
				});

				var $orderContent = $('#js-order-content');

				$('.shopsPopup').find('.close').trigger('click'); // закрыть выбор магазинов
				$('.jsOneClickCompletePage').remove(); // удалить ранее созданный контент с оформленным заказом
				$('#jsOneClickContentPage').show();

				// mask
				$.mask.definitions['x']='[0-9]';
				$.mask.placeholder= "_";
				$.mask.autoclear= false;
				$.map($popup.find('input'), function(elem, i) {
					if (typeof $(elem).data('mask') !== 'undefined') $(elem).mask($(elem).data('mask'));
				});

				$popup.lightbox_me({
					centered: true,
					sticky: false,
					closeSelector: '.close',
					removeOtherOnCreate: false,
					closeClick: false,
					closeEsc: false,
					onLoad: function() {
						$('#OrderV3ErrorBlock').empty().hide();
						$('.jsOrderV3PhoneField').focus();
					},
					onClose: function() {
						$popup.remove();
						$('.jsOneClickForm').remove();
						$('.jsNewPoints').remove();            // удалить ранее созданные карты
						ENTER.OrderV31Click.koModels = {};
						if (ENTER.OrderV31Click.map && ENTER.OrderV31Click.map.destroy) {
							ENTER.OrderV31Click.map.destroy();
						}

						if (location.hash.indexOf('#one-click') == 0) {
							location.hash = '#.';
						}
					}
				});

				// TODO зачем делать повторный ajax запрос, если эти данные можно получить в предыдущем запросе к orderV3OneClick.form?
				$.ajax({
					url: ENTER.utils.generateUrl('orderV3OneClick.delivery'),
					type: 'POST',
					data: {
						shopId: shopId,
						products: buyProducts
					},
					dataType: 'json',
					beforeSend: function() {
						$orderContent.fadeOut(500);
					},
					closeClick: false
				}).fail(function(jqXHR){
					var response = $.parseJSON(jqXHR.responseText);

					if (response.result && response.result.errorContent) {
						$('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
					}
				}).done(function(data) {
					//console.log("Query: %s", data.result.OrderDeliveryRequest);
					//console.log("Model:", data.result.OrderDeliveryModel);

					if (data.result.warn) $('#OrderV3ErrorBlock').text(data.result.warn).show();

					var $data = $(data.result.page);

					$orderContent.empty().html($data.html());

					$.each($orderContent.find('.jsNewPoints'), function(i,val) {
						var pointData = $.parseJSON($(this).find('script.jsMapData').html()),
							points = new ENTER.DeliveryPoints(pointData.points, ENTER.OrderV31Click.map);
						ENTER.OrderV31Click.koModels[$(this).data('id')] = points;
						ko.applyBindings(points, val);
					});

					ENTER.OrderV31Click.functions.initAddress();
					$orderContent.find('input[name=address]').focus();
				}).always(function(){
					$orderContent.stop(true, true).fadeIn(200);
					$('body').trigger('trackUserAction', ['0 Вход']);
				});

				ENTER.OrderV31Click.functions.initAddress(buyProducts);
				ENTER.OrderV31Click.functions.initYandexMaps();
				ENTER.OrderV31Click.functions.initDelivery(buyProducts, shopId);
				ENTER.OrderV31Click.functions.initValidate();
			},
			complete: function() {
				oneClickOpening = false;
			}
		});
	});

	(function(){
		var matches = location.hash.match(/^\#one-click(?:\-(\d+))?$/);
		if (matches) {
			var $oneClickButton;
			if (matches[1]) {
				$oneClickButton = $('.js-oneClickButton-main[data-shop="' + matches[1] + '"]');
			} else {
				$oneClickButton = $('.js-oneClickButton-main:not([data-shop])');

				if (!$oneClickButton.length) {
					$oneClickButton = $('.js-oneClickButton-main').first();
				}
			}

			$oneClickButton.click();
		}
	})();
});