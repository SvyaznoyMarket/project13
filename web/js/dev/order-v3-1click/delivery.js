;(function($) {
	var
		body = document.getElementsByTagName('body')[0],
		$body = $(body);

	//console.log('Model', $('#initialOrderModel').data('value'));
	ENTER.OrderV31Click.functions.initDelivery = function(buyProducts, shopId) {
		var $orderContent = $('#js-order-content'),
			$popup = $('#jsOneClickContent'),
			spinnerClass = 'spinner-new',
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
				var
					block_name = $('.jsOneClickOrderRow').data('block_name'),
					pin = $('.jsCertificatePinInput').val()
				;
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
			checkPandaPay = function checkPandaPayF($button, number) {
				var errorClass = 'cuponErr',
					$message = $('<div />', { 'class': 'jsPandaPayMessage' });

				// блокируем кнопку отправки
				$button.attr('disabled', true).css('opacity', '0.5');
				// удаляем старые сообщения
				$('.' + errorClass).remove();

				$.ajax({
					url: 'http://pandapay.ru/api/promocode/check',
					data: {
						format: 'jsonp',
						code: number
					},
					dataType: 'jsonp',
					jsonp: 'callback',
					success: function(resp) {
						if (resp.error) {
							$message.addClass(errorClass).text(resp.message).insertBefore($button.parent());
						}
						else if (resp.success) {
							$message.addClass(errorClass).css('color', 'green').text('Промокод принят').insertBefore($button.parent());
							docCookies.setItem('enter_panda_pay', number, 60 * 60, '/'); // на час ставим этот промокод
							$button.remove(); // пока только так... CORE-2738
						}
					}
				}).always(function(){
					$button.attr('disabled', false).css('opacity', '1');
				});
			},
			sendChanges = function sendChangesF (action, params) {
				console.info('Sending action "%s" with params:', action, params);

				if (shopId) {
					params.shopId = shopId
				}

				$.ajax({
					url: ENTER.utils.generateUrl('orderV3OneClick.delivery'),
					type: 'POST',
					data: {
						action : action,
						params : params,
						products: buyProducts,
						update: 1
					},
					beforeSend: function() {
						$popup.addClass(spinnerClass);
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
                        ENTER.OrderV31Click.koModels = {};
                        $.each($orderContent.find('.jsNewPoints'), function(i,val) {
                            var pointData = $.parseJSON($(this).find('script.jsMapData').html()),
                                points = new ENTER.DeliveryPoints(pointData.points, ENTER.OrderV31Click.map);
                            ENTER.OrderV31Click.koModels[$(this).data('id')] = points;
                            ko.applyBindings(points, val);
                        })

					}).always(function(){
						$popup.removeClass(spinnerClass);
					});

			},
			showMap = function(elem) {
				var $currentMap = elem.find('.js-order-map').first(),
					mapOptions = ENTER.OrderV31Click.mapOptions,
					map = ENTER.OrderV31Click.map;

				$('.js-map-spinner').hide();

				if (typeof map.getType == 'function') {

					if (!elem.is(':visible')) elem.show();

					map.geoObjects.removeAll();
					map.setCenter([mapOptions.latitude, mapOptions.longitude], mapOptions.zoom);
					$currentMap.append(ENTER.OrderV31Click.$map.show());
					map.container.fitToViewport();
                    // добавляем точки на карту
                    $.each(ENTER.OrderV31Click.koModels[elem.data('id')].availablePoints(), function(i, point){
                        try {
							if (point.geoObject) {
								map.geoObjects.add(point.geoObject);
							}
                        } catch (e) {
                            console.error('Ошибка добавления точки на карту', e, point);
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

				}

			},
			chooseDelivery = function(){
				var token = $(this).data('token'),
					$map = $(this).closest('.jsNewPoints').first();
				// переключение списка магазинов
				$('.selShop_l').hide();
				$('.selShop_l[data-token='+token+']').show();
				// переключение статусов табов
				$('.selShop_tab').removeClass('selShop_tab-act');
				$('.selShop_tab[data-token='+token+']').addClass('selShop_tab-act');
				// показ карты
				//showMap($map);
			},
			choosePoint = function() {
				var id = $(this).data('id'),
					token = $(this).data('token');
				if (id && token) {
					$body.trigger('trackUserAction', ['2_2 Ввод_данных_Самовывоза|Доставки']);
					$body.children('.selShop').remove();
					//$body.children('.lb_overlay')[1].remove();
					changePoint($('.jsOneClickOrderRow').data('block_name'), id, token);
				}
			}
		;

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
			var $elem = $('.jsNewPoints[data-order-id="' + $(this).data('order-id') + '"]');
			e.stopPropagation();
			$('.popupFl').hide();

			if ($(this).hasClass('js-order-changePlace-link')) {
				$elem.lightbox_me({
					centered: true,
					closeSelector: '.jsCloseFl',
					removeOtherOnCreate: false
				});
				showMap($elem);
				$body.trigger('trackUserAction', ['2_1 Место_самовывоза|Адрес_доставки']);

				// клик по способу доставки
				//$elem.off('click', '.selShop_tab:not(.selShop_tab-act)', chooseDelivery);
				//$elem.on('click', '.selShop_tab:not(.selShop_tab-act)', chooseDelivery);

				// клик по списку точек самовывоза
				//$body.on('click', '.jsChangePoint', choosePoint);
				$elem.on('click', '.jsChangePoint', choosePoint);
			} else {
				$($(this).data('content')).show();
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
				changeDelivery($(this).closest('.orderRow').data('block_name'), $(this).data('delivery_method_token'));
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

		// применить скидку
		$body.on('click', '.jsApplyDiscount-1509', function(e){
			var
				$el = $(this),
				relations = $el.data('relation'),
				value = $el.data('value') || {}
			;
			console.info('value', value);

			value['number'] = $(relations['number']).val().trim();

			if ('' != value['number']) {
				applyDiscount(value[['block_name']], value['number']);
			}

			e.preventDefault();
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