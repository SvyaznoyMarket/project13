;(function(w) {
    w.ENTER.OrderV31Click = {
        info: {},
        functions: {},
        map: {},
        mapOptions: {},
        $map: {},
        kladrCityId: 0,
		koModels: []
    };
}(window));
;(function($) {

    var body = $(document.body),
        _gaq = window._gaq,

        sendAnalytic = function sendAnalyticF (category, action, label, value) {
			var lbl = label || '',
				act = action || '';

			if (category && category.data) {
				if (category.data.step) act = category.data.step;
				if (category.data.action) lbl = category.data.action;
			}

			if (typeof ga === 'undefined') ga = window[window['GoogleAnalyticsObject']]; // try to assign ga

			// sending
			if (typeof _gaq === 'object') _gaq.push(['_trackEvent', 'Воронка_1 клик', act, lbl]);
			if (typeof ga === 'function') ga('send', 'event', 'Воронка_1 клик', act, lbl);

		};

    // common listener for triggering from another files or functions
    body.on('trackUserAction.orderV3Tracking', sendAnalytic);

})(jQuery);
;(function(w,ko,$) {

    var address;

	ENTER.OrderV31Click.functions.initAddress = function(){
        var kladrConfig = $('#kladr-config').data('value'),
            region = $('#page-config').data('value').user.region;
    
        function AddressModel () {
    
            var self = this,
                streetTypeDefault = 'Улица';
    
            // сокращенные названия улиц из КЛАДРА (для автосогласования в 1C)
            self.streetShortToLong = {
                'пр-кт': 'Проспект',
                'ш': 'Шоссе',
                'ул.': 'Улица',
                'ул': 'Улица',
                'пер': 'Переулок',
                'пл': 'Площадь',
                'дор': 'Дорога'
            };
    
            self.cityName = ko.observable('');
            self.cityId = ko.observable(0);
            self.streetName = ko.observable('');
            self.streetType = ko.observable(streetTypeDefault);
            self.streetTypeShort = ko.observable('');
            self.streetId = ko.observable(0);
            self.buildingName = ko.observable('');
            self.buildingId = ko.observable(0);
            self.apartmentName = ko.observable('');
    
            self.inputFocus = ko.observable(true);
            self.inputPrefix = ko.computed(function(){
                if (self.streetName() == '') return 'Улица:';
                else if (self.buildingName() == '') return 'дом:';
                else return 'квартира:';
            });
    
            // return {type, parentType, parentId} or false
            self.getParent = function(){
                var result = false;
                if (self.cityId() != 0 && self.inputPrefix() == 'Улица:') result = { type: $.kladr.type.street, parentType: 'city', parentId: self.cityId() };
                else if (self.streetId() != 0  && self.inputPrefix() == 'дом:') result = { type: $.kladr.type.building ,parentType: 'street', parentId: self.streetId() };
                return result;
            };
    
            self.update = function(val) {
                // обработка ручного ввода
                if (typeof val == 'string') {
                    if (self.streetName() == '') {
                        self.streetName(val);
                        self.streetTypeShort('');
                    }
                    else if (self.buildingName() == '') self.buildingName(val);
                    else if (self.apartmentName() == '') self.apartmentName(val);
                }
                // обработка автодополнения
                else if (typeof val == 'object') {
                    if (val.contentType == 'street') {
                        self.streetName(val.name).streetId(val.id).streetType(val.type).streetTypeShort(val.typeShort)
                    }
                    else if (val.contentType == 'building') {
                        self.buildingName(val.name).buildingId(val.id)
                    }
                }
            };
    
            self.clearCity = function() { self.cityName('').cityId(0); return self };
            self.clearStreet = function() { self.streetName('').streetType(streetTypeDefault).streetId(0); return self};
            self.clearBuilding = function() { self.buildingName('').buildingId(0); return self };
            self.clearApartment = function() { self.apartmentName(''); return self };
    
            return self;
    
        }
    
        function saveAddress(address) {
            $.ajax({
                type: 'POST',
                url: '/order-1click/delivery',
                data: {
                    'action' : 'changeAddress',
                    'params' : {
                        // сохраняем улицу в формате "Название + сокращенный тип" для автосогласования в 1С
                        street: address.streetName() + ' ' + (address.streetTypeShort() == '' ? address.streetType() : address.streetTypeShort()),
                        building: address.buildingName(),
                        apartment: address.apartmentName(),
                        kladr_id: address.buildingId() != 0 ? address.buildingId() : address.streetId() != 0 ? address.streetId() : address.cityId() != 0 ? address.cityId() : ''
                    },
                    products: JSON.parse($('#js-order-content').data('param')).products,
                    update: 1
                }
            }).fail(function(jqXHR){
                    var response = $.parseJSON(jqXHR.responseText);
                    if (response.result) {
                        console.error(response.result);
                    }
                })
        }
    
        function autoCompleteRequest (request, response) {
            if (address.getParent() !== false) {
                var query = $.extend({}, { limit: 10, name: request.term }, address.getParent());
    //			if (spinner) spinner.spin($('.kladr_spinner')[0]);
                console.log('[КЛАДР] запрос: ', query);
                $.kladr.api(query, function (data) {
                    console.log('[КЛАДР] ответ', data);
    //				if (spinner) spinner.stop();
                    response($.map(data, function (elem) {
                        return { label: formatStreetName(elem) , value: elem }
                    }))
                });
            }
        }
    
        function formatStreetName(elem) {
            var name = elem.name,
                typeShort = elem.typeShort,
                dot = '';
            if ($.inArray(typeShort, ['ул', 'пер', 'пл', 'дор']) != -1) dot = '.';
            if (elem.contentType === 'street') {
                return name + ' ' +  typeShort + dot;
            } else {
                return name;
            }
        }
    
        // чтобы вызывать функцию после AJAX-запросов, копируем её в глобальную переменную
        ENTER.OrderV31Click.functions.smartAddressInit = function() {
            var $input = $('.jsSmartAddressInput'),
                bindingNode = $('.jsAddressRootNode'),
                initKladrQuery = $.extend(kladrConfig, {'limit': 1, type: $.kladr.type.city, name: region.name});
    
            // jQuery-ui autocomplete from КЛАДР
            $input.autocomplete({
    //            appendTo: '#kladrAutocomplete',
                source: autoCompleteRequest,
                minLength: 1,
                open: function( event, ui ) {
                    $('.ui-autocomplete').css({'position' : 'absolute', 'top' : 29, 'left' : 0});
                },
                select: function( event, ui ) {
                    this.value = '';
                    $input.val('');
                    address.update(ui.item.value);
                    saveAddress(address);
                    return false;
                },
                focus: function( event, ui ) {
                    this.value = ui.item.label;
                    event.preventDefault(); // without this: keyboard movements reset the input to ''
                    event.stopPropagation(); // without this: keyboard movements reset the input to ''
                },
                change: function( event, ui ) {
                },
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });
    
            // Обработка event-ов на поле ввода
            $input.on({
                keypress: function(e){
                    // Нажатие ENTER означает ручной ввод улицы, дома, квартиры
                    if (e.which == 13) {
                        e.preventDefault();
                        if ($(this).val().length > 0) {
                            address.update($(this).val()); // обновляем
                            $input.val(''); // очищаем поля ввода
                            $input.autocomplete('close'); // скрываем автокомплит
                            saveAddress(address);
                        }
                    }
                },
                keydown: function(e){
                    // Обработка Backspace
                    var key = e.keyCode || e.charCode;
                    if (key === 8 && $(this).val().length === 0) {
                        if (address.inputPrefix() == 'дом:') {
                            $input.val(address.streetName());
                            address.clearBuilding().clearStreet();
                        }
                        if (address.inputPrefix() == 'квартира:') {
                            $input.val(address.buildingName());
                            address.clearApartment().clearBuilding();
                        }
                        e.preventDefault();
                    }
                },
                blur: function(){
                    address.update($(this).val()); // обновляем
                    $input.val(''); // очищаем поле ввода
                    saveAddress(address);
                }
            });
    
            // клик по блоку (улица, дом, квартира) в адресе
            $('.jsSmartAddressEditField').on('click', function(){
                var dataType = $(this).data('type');
                if (dataType && typeof address[dataType] == 'function') {
                    $input.val(address[dataType]()); // записываем значение в поле ввода
                    if (dataType == 'apartmentName') address.clearApartment();
                    if (dataType == 'buildingName') address.clearBuilding().clearApartment();
                    if (dataType == 'streetName') {
                        address.clearStreet().clearBuilding().clearApartment();
                        if (address.streetTypeShort() != '') $input.val($input.val() + ' ' + address.streetTypeShort()); // дописываем сокращенное название
                    }
                }
            });

            if (region.kladrId) {
                address.cityId(region.kladrId)
            } else if (kladrConfig && region.name) {
                $.kladr.api(initKladrQuery, function (data){
                    var id = data.length > 0 ? data[0].id : 0;
                    if (id==0) console.error('КЛАДР не определил город, конфигурация запроса: ', initKladrQuery);
                    else address.cityId(data[0].id);
                })
            }
    
            if (bindingNode.length > 0) ko.applyBindings(address, bindingNode[0]);
    
        };
    
        // начинаем отсюдова
    
        if (typeof address == 'undefined') address = new AddressModel();
    
        ENTER.OrderV31Click.address = address;
        ENTER.OrderV31Click.functions.smartAddressInit();
	};

}(window, ko, jQuery));
(function($) {
	ENTER.OrderV31Click.functions.initYandexMaps = function(){
		var E = ENTER.OrderV31Click,
			$mapContainer = $('#yandex-map-container');

		var init = function() {

            console.log('Init yandex maps');

			var options = $mapContainer.data('options');

			E.map = new ymaps.Map("yandex-map-container", {
				center: [options.latitude, options.longitude],
				zoom: options.zoom,
                controls: ['zoomControl', 'fullscreenControl', 'geolocationControl', 'typeSelector']
			},{
				autoFitToViewport: 'always',
                suppressMapOpenBlock: true
			});

			E.map.controls.remove('searchControl');

			E.mapOptions = options;
			E.$map = $mapContainer;

            // храним тут модели, но неплохо бы и переделать
            E.koModels = [];

            E.map.events.add('boundschange', function (event) {
                var bounds;
                if (event.get('newBounds')) {
                    bounds = event.get('target').getBounds();
                    $.each(ENTER.OrderV31Click.koModels, function(i,val){
                        val.latitudeMin(bounds[0][0]);
                        val.latitudeMax(bounds[1][0]);
                        val.longitudeMin(bounds[0][1]);
                        val.longitudeMax(bounds[1][1]);
                    });
                }
            });

		};

		if ($mapContainer.length) ymaps.ready(init);
	};
})(jQuery);
;(function($){
    var $body = $('body'),
        getForm = function getFormF(methodId, orderId, orderNumber, action) {
            var data = {
                'method' : methodId,
                'order': orderId,
                'number': orderNumber
            };
            if (typeof action !== 'undefined' && action != '') data.action = action;
            $.ajax({
                'url': '/order/getPaymentForm',
                'type': 'POST',
                'data': data,
                'success': function(data) {
                    var $form;
                    if (data.form != '') {
                        $form = $(data.form);

                        if ($form.hasClass('jsPaymentFormPaypal') && typeof $form.attr('action') != 'undefined') {
                            window.location.href = $form.attr('action');
                        } else {
                            $body.append($form);
                            $form.submit();
                        }
                    }
                    console.log('Payment data', data);

                }
            })
        };

    // Онлайн-оплата
    $body.on('click', '.jsOnlinePaymentPossible', function(){
        $('.jsOnlinePaymentPossible').hide();
        $('.jsOnlinePaymentBlock').show();
    });

    // клик по методу онлайн-оплаты
    $body.on('click', '.jsPaymentMethod', function(e){
        var id = $(this).data('value'),
            $order = $(this).closest('.jsOneClickCompletePage'),
            orderId = $order.data('order-id'),
            orderNumber = $order.data('order-number');
        e.preventDefault();
        getForm(id, orderId, orderNumber);
    });

})(jQuery);
;(function($) {
	var
		body = document.getElementsByTagName('body')[0],
		$body = $(body);

	//console.log('Model', $('#initialOrderModel').data('value'));
	ENTER.OrderV31Click.functions.initDelivery = function() {
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
                        ENTER.OrderV31Click.koModels = [];
                        $.each($orderContent.find('.jsNewPoints'), function(i,val) {
                            var pointData = $.parseJSON($(this).find('script.jsMapData').html()),
                                points = new ENTER.DeliveryPoints(pointData.points, ENTER.OrderV31Click.map);
                            ENTER.OrderV31Click.koModels.push(points);
                            ko.applyBindings(points, val);
                        })

					}).always(function(){
						$popup.removeClass(spinnerClass);
					});

			},
			log = function logF(data){
				$.ajax({
					"type": 'POST',
					"data": data,
					"url": '/order/log'
				})
			},
			showMap = function(elem) {
				var $currentMap = elem.find('.js-order-map').first(),
                    mapData = $.parseJSON($currentMap.next().html()), // не очень хорошо
					mapOptions = ENTER.OrderV31Click.mapOptions,
					map = ENTER.OrderV31Click.map;

				if (mapData && typeof map.getType == 'function') {

					if (!elem.is(':visible')) elem.show();

					map.geoObjects.removeAll();
					map.setCenter([mapOptions.latitude, mapOptions.longitude], mapOptions.zoom);
					$currentMap.append(ENTER.OrderV31Click.$map.show());
					map.container.fitToViewport();

                    // добавляем точки на карту
                    $.each(mapData.points, function(i, point){
                        try {
                            map.geoObjects.add(new ENTER.Placemark(point, true));
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
			var $elem = $(this).parent().parent().next();
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
;(function($) {

	ENTER.OrderV31Click.functions.initValidate = function() {
		var $pageNew = $('#jsOneClickContentPage'),
			$validationErrors = $('.jsOrderValidationErrors'),
			$form = $('.jsOrderV3OneClickForm'),
			errorClass = 'textfield-err',
			validateEmail = function validateEmailF(email) {
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(email) && !/[а-яА-Я]/.test(email);
			},
			validate = function validateF(){
				var isValid = true,
					$phoneInput = $('[name=user_info\\[mobile\\]]'),
					$emailInput = $('[name=user_info\\[email\\]]'),
					$deliveryMethod = $('.orderCol_delivrLst_i-act span'),
					phone = $phoneInput.val().replace(/\s+/g, '');

				if (!/\+7\(\d{3}\)\d{3}-\d{2}-\d{2}/.test(phone)) {
					isValid = false;
					$phoneInput.addClass(errorClass).siblings('.errTx').show();
				} else {
					$phoneInput.removeClass(errorClass).siblings('.errTx').hide();
				}

				if ($emailInput.hasClass('jsOrderV3EmailRequired') && $emailInput.val().length == 0) {
                    $emailInput.addClass('textfield-err').siblings('.errTx').text('Не указан email').show();
                    isValid = false;
                } else if ($emailInput.val().length != 0 && !validateEmail($emailInput.val())) {
                    $emailInput.addClass('textfield-err').siblings('.errTx').text('Неверный формат email').show();
                    isValid = false;
				} else {
					$emailInput.removeClass(errorClass).siblings('.errTx').hide();
				}

				/*if ($deliveryMethod.text() == 'Самовывоз' && $('.orderCol_addrs_tx').text().replace(/\s/g, '') == '') {
				 error.push('Не выбран адрес доставки или самовывоза');
				 }

				 if ($deliveryMethod.text() == 'Доставка') {
				 if (!ENTER.OrderV31Click.address || !ENTER.OrderV31Click.address.building.name) {
				 error.push('Не выбран адрес доставки или самовывоза');
				 }
				 }*/

				return isValid;
			};

		if ($validationErrors.length) {
			console.warn('Validation errors', $validationErrors);
		}

		$pageNew.on('blur', 'input', function(){
			validate()
		}).on('keyup', '.jsOrderV3PhoneField', function(){
            var val = $(this).val();
            if (val[val.length-1] != '_') validate();
        });

		$form.on('submit', function(e){
			e.preventDefault();

			var	$el = $(this),
				$submitBtn = $el.find('.orderCompl_btn'),
				data = $el.serializeArray();

			if (!validate()) {
				return;
			}

			$.ajax({
				type: 'POST',
				url: $el.attr('action'),
				data: data,
				beforeSend: function(){
					$submitBtn.attr('disabled', true)
				}
			})
				.always(function(){
					$submitBtn.attr('disabled', false)
				})
				.done(function(response) {
					if (typeof response.result !== 'undefined') {
						$('#jsOneClickContentPage').hide();
						$('#jsOneClickContent').append(response.result.page);

						$('body').trigger('trackUserAction', ['3_1 Оформить_успешно']);

						// Счётчик GetIntent (BlackFriday)
						(function() {
							if (response.result.lastPartner != 'blackfridaysale') {
								return '';
							}

							$.each(response.result.orders, function(index, order) {
								var products = [];
								var revenue = 0;
								$.each(order.products, function(index, product) {
									products.push({
										id: product.id + '',
										price: product.price + '',
										quantity: parseInt(product.quantity)
									});

									revenue += parseFloat(product.price) * parseInt(product.quantity);
								});

								ENTER.counters.callGetIntentCounter({
									type: "CONVERSION",
									orderId: order.id + '',
									orderProducts: products,
									orderRevenue: revenue + ''
								});
							});
						})();

						/* Hubrus order complete code */
						(function(){
							var product, orderId;
							if (response.result.lastPartner != 'hubrus' || !window.smartPixel1) return;
							product = response.result.orders[0].products[0];
							orderId = response.result.orders[0].id;
							smartPixel1.trackState('oneclick_complete', {
								cart_items: [{
									price: product.price,
									id: product.id
								}],
								order_id: orderId
							});
						})();

						/* AdvMaker */
						(function(){
							if (response.result.lastPartner != 'advmaker') return;
							$.get('http://am15.net/s2s.php', {
								'ams2s': docCookies.get('ams2s'),
								'orders': response.result.orders[0].id
							});
						})();

						// Счётчик RetailRocket
						(function() {
							$.each(response.result.orders, function(index, order) {
								var products = [];
								$.each(order.products, function(index, product) {
									products.push({
										id: product.id,
										qnt: product.quantity,
										price: product.price
									});
								});

								ENTER.counters.callRetailRocketCounter('order.complete', {
									transaction: order.id,
									items: products
								});
							});
						})();

						if (response.result.orderAnalytics) {
							ENTER.utils.sendOrderToGA(response.result.orderAnalytics);
						}
					}
				})
				.fail(function(jqXHR){
					var response = $.parseJSON(jqXHR.responseText);

					if (response.result && response.result.errorContent) {
						$('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
					}

					var error = (response.result && response.result.error) ? response.result.error : [];

					$('body').trigger('trackUserAction', ['3_2 Оформить_ошибка', 'Поле ошибки: '+ ((typeof error !== 'undefined') ? error.join(', ') : '')]);
				})
			;
		})
	};


}(jQuery));