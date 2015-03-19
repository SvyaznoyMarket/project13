(function() {
	var oneClickOpening = false;
	$('body').on('click', '.jsOneClickButton-new', function(e) {
		console.info('show one click form');

		e.preventDefault();

		if (oneClickOpening) {
			return;
		}

		var
			button = $(e.currentTarget),
			$target = $('#jsOneClickContent');

		if ($target.length) {
			openPopup(false);
			init();
		} else {
			oneClickOpening = true;
			$.ajax({
				url: ENTER.utils.generateUrl('orderV3OneClick.form', {productUid: button.data('product-ui'), sender: button.data('sender'), sender2: button.data('sender2')}),
				type: 'POST',
				dataType: 'json',
				closeClick: false,
				success: function(result) {
					$('body').append(result.form);
					$target = $('#jsOneClickContent');
					openPopup(true);
					init();
				},
				complete: function() {
					oneClickOpening = false;
				}
			})
		}

		function init() {
			ENTER.OrderV31Click.functions.initAddress();
			ENTER.OrderV31Click.functions.initYandexMaps();
			ENTER.OrderV31Click.functions.initDelivery();
			ENTER.OrderV31Click.functions.initValidate();
		}

		function openPopup(removeOnClose) {
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
			$.map($('#jsOneClickContent').find('input'), function(elem, i) {
				if (typeof $(elem).data('mask') !== 'undefined') $(elem).mask($(elem).data('mask'));
			});

			if ($target.length) {
				var data = $.parseJSON($orderContent.data('param'));
				data.quantity = button.data('quantity');
				data.shopId = button.data('shop');
				$orderContent.data('shop', data.shopId);

				/*if (button.data('title')) {
					$target.find('.jsOneClickTitle').text(button.data('title'));
				}*/

				$target.lightbox_me({
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
						if (removeOnClose) {
							$target.remove();
							$('.jsOneClickForm').remove();
						}
                        $('.jsNewPoints').remove();            // удалить ранее созданные карты
                        ENTER.OrderV31Click.koModels = [];
                        ENTER.OrderV31Click.map.destroy();
					}
				});

				$.ajax({
					url: $orderContent.data('url'),
					type: 'POST',
					data: data,
					dataType: 'json',
					beforeSend: function() {
						$orderContent.fadeOut(500);
						//if (spinner) spinner.spin(body)
					},
					closeClick: false
				}).fail(function(jqXHR){
					var response = $.parseJSON(jqXHR.responseText);

					if (response.result && response.result.errorContent) {
						$('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
					}
				}).done(function(data) {
					console.log("Query: %s", data.result.OrderDeliveryRequest);
					console.log("Model:", data.result.OrderDeliveryModel);
					$orderContent.empty().html($(data.result.page).html());

                    $.each($('.jsNewPoints'), function(i,val) {
                        var E = ENTER.OrderV31Click,
                            pointData = JSON.parse($(this).find('script.jsMapData').html()),
                            points = new ENTER.DeliveryPoints(pointData.points, E.map);

                        E.koModels.push(points);
                        console.log('Apply bindings');
                        ko.applyBindings(points, val);
                    });

					ENTER.OrderV31Click.functions.initAddress();
					$orderContent.find('input[name=address]').focus();
				}).always(function(){
					$orderContent.stop(true, true).fadeIn(200);
					//if (spinner) spinner.stop();

					$('body').trigger('trackUserAction', ['0 Вход']);
				});
			}
		}
	});
})();