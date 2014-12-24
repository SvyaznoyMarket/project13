;(function($) {

    ENTER.OrderV3 = ENTER.OrderV3 || {};

    console.log('Model', $('#initialOrderModel').data('value'));

    var body = document.getElementsByTagName('body')[0],
        $body = $(body),
        $orderContent = $('#js-order-content'),
        comment = '',
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
                    console.log('Сертификат найден');
                    $('[data-block_name='+block_name+']').find('.cuponPin').show();
                } else if (data.error_code == 743) {
                    // 743 - Сертификат не найден
                    sendChanges('applyDiscount',{'block_name': block_name, 'number':code})
                }
            }).always(function(data){
                console.log('Certificate check response',data);
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
            $.ajax({
                type: 'POST',
                data: {
                    'action' : action,
                    'params' : params
                },
                beforeSend: function() {
                    $orderContent.fadeOut(500);
                    if (spinner) spinner.spin(body)
                }
            }).fail(function(jqXHR){
                var response = $.parseJSON(jqXHR.responseText);
                if (response.result) {
                    console.error(response.result);
                }
                if (response.result.redirect) {
                    window.location.href = response.result.redirect;
                }
            }).done(function(data) {
                console.log("Query: %s", data.result.OrderDeliveryRequest);
                console.log("Model:", data.result.OrderDeliveryModel);
                $orderContent.empty().html($(data.result.page).find('#js-order-content').html());
				if ($orderContent.find('.jsAddressRootNode').length > 0) {
					$.each($orderContent.find('.jsAddressRootNode'), function(i,val){
						ko.applyBindings(ENTER.OrderV3.address, val);
					});
					if (typeof ENTER.OrderV3.constructors.smartAddressInit == 'function') ENTER.OrderV3.constructors.smartAddressInit();
				}
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
                mapData = $currentMap.data('value'),
                mapOptions = ENTER.OrderV3.mapOptions,
                map = ENTER.OrderV3.map;

            if (!token) {
                token = Object.keys(mapData.points)[0];
                $currentMap.siblings('.selShop_l').hide();
                $currentMap.siblings('.selShop_l[data-token='+token+']').show();
            }

            if (mapData) {

                if (!elem.is(':visible')) elem.show();

                map.geoObjects.removeAll();
                map.setCenter([mapOptions.latitude, mapOptions.longitude], mapOptions.zoom);
                $currentMap.append(ENTER.OrderV3.$map.show());
                map.container.fitToViewport();

                for (var i = 0; i < mapData.points[token].length; i++) {
                    var point = mapData.points[token][i],
                        balloonContent = 'Адрес: ' + point.address;

                    if (!point.latitude || !point.longitude) continue;

                    if (point.regtime) balloonContent += '<br /> Время работы: ' + point.regtime;

                    // кнопка "Выбрать магазин"
                    balloonContent += '<br />' + $('<button />', {
                        'text':'Выбрать магазин',
                        'class': 'btnLightGrey jsChangePoint',
                        'data-id': point.id,
                        'data-token': token
                        }
                    )[0].outerHTML;

                    var placemark = new ymaps.Placemark([point.latitude, point.longitude], {
                        balloonContentHeader: point.name,
                        balloonContentBody: balloonContent,
                        hintContent: point.name
                    }, {
                        iconLayout: 'default#image',
                        iconImageHref: point.marker.iconImageHref,
                        iconImageSize: point.marker.iconImageSize,
                        iconImageOffset: point.marker.iconImageOffset
                    });

                    map.geoObjects.add(placemark);
                }

                if (map.geoObjects.getLength() === 1) {
                    map.setCenter(map.geoObjects.get(0).geometry.getCoordinates(), 15);
                } else {
                    map.setBounds(map.geoObjects.getBounds());
                }

            } else {
                console.error('No map data for token = "%s"', token,  elem);
            }
		},

		showOfertaPopup = function showOfertaPopupF() {
			$('.js-order-oferta-popup').lightbox_me();
		},

		tabsOfertaAction = function tabsOfertaActionF(that) {
			var $self = $(that),
				tabContent = $('.js-tab-oferta-content'),
				tab_id = $(that).attr('data-tab');

			$('.js-oferta-tab').removeClass('orderOferta_tabs_i-cur');
			tabContent.removeClass('orderOferta_tabcnt-cur');

			$self.addClass('orderOferta_tabs_i-cur');
			$("#"+tab_id).addClass('orderOferta_tabcnt-cur');
		};

    // TODO change all selectors to .jsMethod

    // клик по крестику на всплывающих окнах
    $orderContent.on('click', '.jsCloseFl', function(e) {
        e.stopPropagation();
        $(this).closest('.popupFl').hide();
        e.preventDefault();
    });

	$orderContent.on('click', '.jsAddressRootNode', function() {
		$(this).find('.jsSmartAddressInput').focus();
		ENTER.OrderV3.address.inputFocus(true);
	});

	$orderContent.on('blur', '.jsSmartAddressInput', function() {
		ENTER.OrderV3.address.inputFocus(false);
	});

    // клик по "изменить дату" и "изменить место"
    $orderContent.on('click', '.orderCol_date, .js-order-changePlace-link', function(e) {
        var elemId = $(this).data('content');
        e.stopPropagation();
        $('.popupFl').hide();

        if ($(this).hasClass('js-order-changePlace-link')) {
            var token = $(elemId).find('.selShop_l:first').data('token');
            // скрываем все списки точек и показываем первую
            $(elemId).find('.selShop_l').hide().first().show();
            // первая вкладка активная
            $(elemId).find('.selShop_tab').removeClass('selShop_tab-act').first().addClass('selShop_tab-act');
            $(elemId).lightbox_me({
                centered: true,
                closeSelector: '.jsCloseFl',
            });
            showMap($(elemId), token);
            $body.trigger('trackUserAction', ['10 Место_самовывоза_Доставка_ОБЯЗАТЕЛЬНО']);
        } else {
            $(elemId).show();
            log({'action':'view-date'});
            $body.trigger('trackUserAction', ['11 Срок_доставки_Доставка']);
        }

        e.preventDefault();
    });

    // клик по способу доставки
	$body.on('click', '.selShop_tab:not(.selShop_tab-act)', function(){
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
            $body.trigger('trackUserAction', ['11_1 Срок_Изменил_дату_Доставка']);
            changeDate($(this).closest('.orderRow').data('block_name'), timestamp)
        }
    });

    // клик по списку точек самовывоза
    $body.on('click', '.jsChangePoint', function() {
        var id = $(this).data('id'),
            token = $(this).data('token');
        if (id && token) {
            $body.trigger('trackUserAction', ['10_1 Ввод_данных_Самовывоза_Доставка_ОБЯЗАТЕЛЬНО']);
            $body.children('.selShop, .lb_overlay').remove();
            changePoint($(this).closest('.selShop').data('block_name'), id, token);
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

    // клик по ссылке "Применить" у каунтера
    $orderContent.on('click', '.jsChangeProductQuantity', function(e){
        var $this = $(this),
            quantity = $this.parent().find('input').val();
        changeProductQuantity($this.data('block_name'), $this.data('id'), quantity);
        e.preventDefault();
    });

    // клик по ссылке "Удалить" у каунтера
    $orderContent.on('click', '.jsDeleteProduct', function(e){
        var $this = $(this);
        changeProductQuantity($this.data('block_name'), $this.data('id'), 0);
        e.preventDefault();
    });

    // клик по безналичному методу оплаты
    $orderContent.on('change', '.jsCreditCardPayment', function(){
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name');
        if ($this.is(':checked')) $body.trigger('trackUserAction', ['13_1 Оплата_банковской_картой_Доставка']);
        changePaymentMethod(block_name, 'by_credit_card', $this.is(':checked'))
    });

    // клик по "купить в кредит"
    $orderContent.on('change', '.jsCreditPayment', function() {
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name');
        if ($this.is(':checked')) $body.trigger('trackUserAction', ['13_2 Оплата_в_кредит_Доставка']);
        changePaymentMethod(block_name, 'by_online_credit', $(this).is(':checked'))
    });

    // сохранение комментария
    $orderContent.on('blur focus', '.orderComment_fld', function(){
        if (comment != $(this).val()) {
            comment = $(this).val();
            changeOrderComment($(this).val());
        }
    });

    // клик по "Дополнительные пожелания"
    $orderContent.on('click', '.orderComment_t', function(){
        $('.orderComment_fld').show();
    });

    // применить скидку
    $orderContent.on('click', '.jsApplyDiscount', function(e){
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name'),
            number = $this.parent().siblings('input').val();
        // TODO mask
        if (number != '') applyDiscount(block_name, number);
        e.preventDefault();
    });

    // удалить скидку
    $orderContent.on('click', '.jsDeleteDiscount', function(e){
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name'),
            number = $this.data('value');
        deleteDiscount(block_name, number);
        e.preventDefault();
    });

    // клик по "хочу быстрее"
    $orderContent.on('click', '.jsWanna', function(){
        var span = '<span style="margin: 5px 0 17px 10px; display: inline-block; color: #878787;">Спасибо за участие в опросе.</span>';
        $(span).insertAfter($(this));
        $(this).hide();
        window.docCookies.setItem('enter_order_v3_wanna', 1, 0, '/order');
        $body.trigger('trackUserAction', ['1_2 Срок_Хочу_быстрее_Доставка']);
        log({'action':'wanna'});
    });

    $orderContent.on('click', '.jsDeleteCertificate', function(){
        var block_name = $(this).closest('.orderRow').data('block_name');
        deleteCertificate(block_name);
    });

    // клик по "Я ознакомлен и согласен..."
    $orderContent.on('click', '.jsAcceptTerms', function(){
        $body.trigger('trackUserAction', ['14 Согласен_оферта_Доставка_ОБЯЗАТЕЛЬНО']);
    });

	/* Оферта */
	$body.on('click', '.js-order-oferta-popup-btn', function(e){
		var href = $(this).data('value');
		e.preventDefault();
		if (href != '') {
			console.log('OLD href', href);
			if (window.location.host != 'www.enter.ru') href = href.replace(/^.*enter.ru/, ''); /* для работы на demo-серверах */
			console.log('NEW href', href);
			$.ajax({
				url: href,
				success: function(data) {
					$('.orderOferta_tl:first').html($(data).find('.entry-content').html());
					showOfertaPopup();
				}
			})
		}
	});

	$body.on('click', '.js-oferta-tab', function(){
		tabsOfertaAction(this)
	});

	// ДЛЯ АБ-ТЕСТА ПО МОТИВАЦИИ ОНЛАЙН-ОПЛАТЫ
	$body.on('click', '.jsPaymentMethodRadio', function(){
		var $this = $(this),
			block_name = $this.closest('.orderRow').data('block_name'),
			method = $this.val();
		changePaymentMethod(block_name, method, 'true')
	});

	$body.on('change', '.jsPaymentMethodSelect', function(e){
		var $this = $(this),
			block_name = $this.closest('.orderRow').data('block_name'),
			selectedMethod = $this.find(':selected').val();
		changePaymentMethod(block_name, selectedMethod, 'true');
		e.preventDefault();
	});

    // АНАЛИТИКА

    if (/order\/delivery/.test(window.location.href)) {
        $body.trigger('trackUserAction', ['6_1 Далее_успешно_Получатель_ОБЯЗАТЕЛЬНО']); // TODO перенести в validate.js
        $body.trigger('trackUserAction', ['7 Вход_Доставка_ОБЯЗАТЕЛЬНО', 'Количество заказов: ' + $('.orderRow').length]);
    }

    // отслеживаем смену региона
    $body.on('click', 'a.jsChangeRegionAnalytics', function(e){
        var newRegion = $(this).text(),
            oldRegion = $('.jsRegion').data('value'),
            link = $(this).attr('href');

        e.preventDefault();
        // TODO вынести как функцию с проверкой существования ga и немедленным вызовом hitCallback в остуствии ga и трекера
        ga('send', 'event', {
            'eventCategory': 'Воронка_' + oldRegion,
            'eventAction': '8 Регион_Доставка',
            'eventLabel': 'Было: ' + oldRegion + ', Стало: ' + newRegion,
            'hitCallback': function() {
                window.location.href = link;
            }
        });

    })

})(jQuery);