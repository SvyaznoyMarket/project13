;(function($) {
	var
		body = document.getElementsByTagName('body')[0],
		$body = $(body);

	//console.log('Model', $('#initialOrderModel').data('value'));
	ENTER.OrderV31Click.functions.initDelivery = function() {
		var $orderContent = $('#js-order-content'),
			$popup = $('#jsOneClickContent'),
			spinner = typeof Spinner == 'function' ? new Spinner({
				lines: 11, // The number of lines to draw
				length: 5, // The length of each line
				width: 8, // The line thickness
				radius: 23, // The radius of the inner circle
				corners: 1, // Corner roundness (0..1)
				rotate: 0, // The rotation offset
				direction: 1, // 1: clockwise, -1: counterclockwise
				color: '#666', // #rgb or #rrggbb or array of colors
				speed: 1, // Rounds per second
				trail: 62, // Afterglow percentage
				shadow: false, // Whether to render a shadow
				hwaccel: true, // Whether to use hardware acceleration
				className: 'spinner', // The CSS class to assign to the spinner
				zIndex: 2e9, // The z-index (defaults to 2000000000)
				top: '50%', // Top position relative to parent
				left: '50%' // Left position relative to parent
			}) : null,
			changeDelivery = function changeDeliveryF (block_name, delivery_method_token) {
				sendChanges('changeDelivery', {'block_name': block_name, 'delivery_method_token': delivery_method_token});
			},
			changeDate = function changeDateF (block_name, timestamp) {
				sendChanges('changeDate', {'block_name': block_name, 'date': timestamp})
			},
			changePoint = function changePointF (block_name, id, token) {
				sendChanges('changePoint', {'block_name': block_name, 'id': id, 'token': token})
			},
			changeInterval = function changeIntervalF(block_name, interval) {
				sendChanges('changeInterval', {'block_name': block_name, 'interval': interval})
			},
			changeProductQuantity = function changeProductQuantityF(block_name, id, quantity) {
				sendChanges('changeProductQuantity', {'block_name': block_name, 'id': id, 'quantity': quantity})
			},
			changePaymentMethod = function changePaymentMethodF(block_name, method, isActive) {
				var params = {'block_name': block_name};
				params[method] = isActive;
				sendChanges('changePaymentMethod', params)
			},
			changeOrderComment = function changeOrderCommentF(comment){
				sendChanges('changeOrderComment', {'comment': comment})
			},
			applyDiscount = function applyDiscountF(block_name, number) {
				var pin = $('[data-block_name='+block_name+']').find('.jsCertificatePinInput').val();
				if (pin != '') applyCertificate(block_name, number, pin);
				else checkCertificate(block_name, number);
			},
			deleteDiscount = function deleteDiscountF(block_name, number) {
				sendChanges('deleteDiscount',{'block_name': block_name, 'number':number})
			},
			checkCertificate = function checkCertificateF(block_name, code){
				$.ajax({
					type: 'POST',
					url: '/certificate-check',
					data: {
						code: code,
						pin: '0000'
					}
				}).done(function(data){
						if (data.error_code == 742) {
							// 742 - Неверный пин
							//console.log('Сертификат найден');
							$('[data-block_name='+block_name+']').find('.cuponPin').show();
						} else if (data.error_code == 743) {
							// 743 - Сертификат не найден
							sendChanges('applyDiscount',{'block_name': block_name, 'number':code})
						}
					}).always(function(data){
						//console.log('Certificate check response',data);
					})
			},
			applyCertificate = function applyCertificateF(block_name, code, pin) {
				sendChanges('applyCertificate', {'block_name': block_name, 'code': code, 'pin': pin})
			},
			deleteCertificate = function deleteCertificateF(block_name) {
				sendChanges('deleteCertificate', {'block_name': block_name})
			},
			sendChanges = function sendChangesF (action, params) {
				console.info('Sending action "%s" with params:', action, params);

				if ($orderContent.data('shop')) {
					params.shopId = $orderContent.data('shop')
				}

				$.ajax({
					url: '/order-1click/delivery',
					type: 'POST',
					data: {
						action : action,
						params : params,
						products: JSON.parse($orderContent.data('param')).products,
						update: 1
					},
					beforeSend: function() {
						$orderContent.fadeOut(500);
						if (spinner) spinner.spin(body)
					}
				}).fail(function(jqXHR){
						var response = $.parseJSON(jqXHR.responseText);

						if (response.result && response.result.errorContent) {
							$('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
						}
					}).done(function(data) {
						//console.log("Query: %s", data.result.OrderDeliveryRequest);
						//console.log("Model:", data.result.OrderDeliveryModel);
						$orderContent.empty().html($(data.result.page).html());
						ENTER.OrderV31Click.functions.initAddress();
						$orderContent.find('input[name=address]').focus();

                        // Новый самовывоз
                        console.log('Applying knockout bindings');
                        ENTER.OrderV31Click.koModels = [];
                        $.each($orderContent.find('.jsNewPoints'), function(i,val) {
                            var pointData = $.parseJSON($(this).find('script.jsMapData').html()),
                                points = new ENTER.DeliveryPoints(pointData.points, ENTER.OrderV31Click.map);
                            ENTER.OrderV31Click.koModels.push(points);
                            ko.applyBindings(points, val);
                        })

					}).always(function(){
						$orderContent.stop(true, true).fadeIn(200);
						if (spinner) spinner.stop();
					});

			},
			log = function logF(data){
				$.ajax({
					"type": 'POST',
					"data": data,
					"url": '/order/log'
				})
			},
			showMap = function(elem, token) {
				var $currentMap = elem.find('.js-order-map').first(),
                    mapData = $.parseJSON($currentMap.next().html()), // не очень хорошо
					mapOptions = ENTER.OrderV31Click.mapOptions,
					map = ENTER.OrderV31Click.map;

				if (!token) {
					token = Object.keys(mapData.points)[0];
					$currentMap.siblings('.selShop_l').hide();
					$currentMap.siblings('.selShop_l[data-token='+token+']').show();
				}

				if (mapData && typeof map.getType == 'function') {

					if (!elem.is(':visible')) elem.show();

					map.geoObjects.removeAll();
					map.setCenter([mapOptions.latitude, mapOptions.longitude], mapOptions.zoom);
					$currentMap.append(ENTER.OrderV31Click.$map.show());
					map.container.fitToViewport();

                    // добавляем точки на карту
                    $.each(mapData.points, function(token){
                        for (var i = 0; i < mapData.points[token].length; i++) {
                            try {
                                map.geoObjects.add(new ENTER.Placemark(mapData.points[token][i], true));
                            } catch (e) {
                                console.error('Ошибка добавления точки на карту', e);
                            }
                        }
                    });

                    if (map.geoObjects.getLength() === 1) {
                        map.setCenter(map.geoObjects.get(0).geometry.getCoordinates(), 15);
                        map.geoObjects.get(0).options.set('visible', true);
                    } else {
                        map.setBounds(map.geoObjects.getBounds());
                        // точки становятся видимыми только при увеличения зума
                        /*map.events.once('boundschange', function(event){
                            if (event.get('oldZoom') < event.get('newZoom')) {
                                map.geoObjects.each(function(point) { point.options.set('visible', true)})
                            }
                        })*/
                    }

				} else {
					console.error('No map data for token = "%s"', token,  elem);
				}

			},
			chooseDelivery = function(){
				var token = $(this).data('token'),
					id = $(this).closest('.popupFl').attr('id');
				// переключение списка магазинов
				$('.selShop_l').hide();
				$('.selShop_l[data-token='+token+']').show();
				// переключение статусов табов
				$('.selShop_tab').removeClass('selShop_tab-act');
				$('.selShop_tab[data-token='+token+']').addClass('selShop_tab-act');
				// показ карты
				showMap($('#'+id), token);
			},
			choosePoint = function() {
				var id = $(this).data('id'),
					token = $(this).data('token');
				if (id && token) {
					$body.trigger('trackUserAction', ['2_2 Ввод_данных_Самовывоза|Доставки']);
					$body.children('.selShop').remove();
					$body.children('.lb_overlay').last().remove();
					changePoint($(this).closest('.selShop').data('block_name'), id, token);
				}
			};

        // новый самовывоз
        $body.on('click', '.jsOrderV3Dropbox',function(){
            $(this).siblings().removeClass('opn').find('.jsOrderV3DropboxInner').hide(); // скрываем все, кроме потомка
            $(this).find('.jsOrderV3DropboxInner').toggle(); // потомка переключаем
            $(this).hasClass('opn') ? $(this).removeClass('opn') : $(this).addClass('opn');
        });

		// клик по крестику на всплывающих окнах
		$orderContent.on('click', '.jsCloseFl', function(e) {
			e.stopPropagation();
			$(this).closest('.popupFl').hide();
			e.preventDefault();
		});

		// клик по "изменить дату" и "изменить место"
		$orderContent.on('click', '.orderCol_date, .js-order-changePlace-link', function(e) {
			var $elem = $($(this).data('content'));
			e.stopPropagation();
			$('.popupFl').hide();

			if ($(this).hasClass('js-order-changePlace-link')) {
				var token = $elem.find('.selShop_l:first').data('token');
				// скрываем все списки точек и показываем первую
				$elem.find('.selShop_l').hide().first().show();
				// первая вкладка активная
				$elem.find('.selShop_tab').removeClass('selShop_tab-act').first().addClass('selShop_tab-act');
				$elem.lightbox_me({
					centered: true,
					closeSelector: '.jsCloseFl',
					removeOtherOnCreate: false
				});
				showMap($elem, token);
				$body.trigger('trackUserAction', ['2_1 Место_самовывоза|Адрес_доставки']);

				// клик по способу доставки
				$elem.off('click', '.selShop_tab:not(.selShop_tab-act)', chooseDelivery);
				$elem.on('click', '.selShop_tab:not(.selShop_tab-act)', chooseDelivery);

				// клик по списку точек самовывоза
				$elem.off('click', '.jsChangePoint', choosePoint);
				$elem.on('click', '.jsChangePoint', choosePoint);
			} else {
				$elem.show();
				log({'action':'view-date'});
				//$body.trigger('trackUserAction', ['11 Срок_доставки_Доставка']);
			}

			e.preventDefault();
		});

		// клик по "Ввести код скидки"
		$orderContent.on('click', '.jsShowDiscountForm', function(e) {
			e.stopPropagation();
			$(this).hide().parent().next().show();
		});

		// клик по способу доставки
		$orderContent.on('click', '.orderCol_delivrLst li', function() {
			var $elem = $(this);
			if (!$elem.hasClass('orderCol_delivrLst_i-act')) {
				//            if ($elem.data('delivery_group_id') == 1) {
				//                showMap($elem.parent().siblings('.selShop').first());
				//            } else {
				changeDelivery($(this).closest('.orderRow').data('block_name'), $(this).data('delivery_method_token'));
				//            }
			}
		});

		// клик по дате в календаре
		$orderContent.on('click', '.celedr_col', function(){
			var timestamp = $(this).data('value');
			if (typeof timestamp == 'number') {
				//$body.trigger('trackUserAction', ['11_1 Срок_Изменил_дату_Доставка']);
				changeDate($(this).closest('.orderRow').data('block_name'), timestamp)
			}
		});

		// клик на селекте интервала
		$orderContent.on('click', '.jsShowDeliveryIntervals', function() {
			$(this).find('.customSel_lst').show();
		});

		// клик по интервалу доставки
		$orderContent.on('click', '.customSel_lst li', function() {
			changeInterval($(this).closest('.orderRow').data('block_name'), $(this).data('value'));
		});

		// АНАЛИТИКА

		$popup.on('focus', '.jsOrderV3PhoneField', function(){
			$body.trigger('trackUserAction',['1_1 Телефон'])
		});

		$popup.on('focus', '.jsOrderV3EmailField', function(){
			$body.trigger('trackUserAction',['1_3 E-mail'])
		});

		$popup.on('focus', '.jsOrderV3NameField', function(){
			$body.trigger('trackUserAction',['1_2 Имя'])
		});

		$popup.on('click', '.jsOrderOneClickClose', function(e){
			e.preventDefault();
			$(this).closest('#jsOneClickContent').trigger('close');
		});
	};
})(jQuery);