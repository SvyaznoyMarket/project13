;(function($) {

    ENTER.OrderV3 = ENTER.OrderV3 || {};

    try {
        console.log('Model', $.parseJSON($('#initialOrderModel').html()));
    } catch (e) {
    }

    var body = document.getElementsByTagName('body')[0],
        $body = $(body),
        $orderContent = $('#js-order-content'),
        $inputs = $('.js-order-ctrl__input'),
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
        changeProductQuantity = function changeProductQuantityF(block_name, id, ui, quantity) {
            sendChanges('changeProductQuantity', {'block_name': block_name, 'id': id, 'ui': ui, 'quantity': quantity})
        },
        changePaymentMethod = function changePaymentMethodF(block_name, method, isActive) {
            var params = {'block_name': block_name};
            params[method] = isActive;
            sendChanges('changePaymentMethod', params)
        },
        changeAddress = function changeAddressF(params) {
            sendChanges('changeAddress', params)
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

            var hideContent = true;

            if (-1 !== $.inArray(action, ['changeDate', 'changeInterval', 'changeOrderComment'])) hideContent = false;

            $.ajax({
                type: 'POST',
                data: {
                    'action' : action,
                    'params' : params
                },
                beforeSend: function() {
                    if (hideContent) $orderContent.fadeOut(500);
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

                //console.log("Query: %s", data.result.OrderDeliveryRequest);
                console.log("Model:", data.result.OrderDeliveryModel);

                $('.jsNewPoints').remove(); // иначе неправильно работает биндинг

                $orderContent.empty().html(data.result.page);
				if ($orderContent.find('.jsAddressRootNode').length > 0) {
					$.each($orderContent.find('.jsAddressRootNode'), function(i,val){
						ko.applyBindings(ENTER.OrderV3.address, val);
					});
					if (typeof ENTER.OrderV3.constructors.smartAddressInit == 'function') ENTER.OrderV3.constructors.smartAddressInit();
				}

                // Новый самовывоз
                ENTER.OrderV3.koModels = [];
                $.each($orderContent.find('.jsNewPoints'), function(i,val) {
                    var pointData = $.parseJSON($(this).find('script.jsMapData').html()),
                        points = new ENTER.DeliveryPoints(pointData.points, ENTER.OrderV3.map);
                    ENTER.OrderV3.koModels.push(points);
                    ko.applyBindings(points, val);
                });

                // Попап с сообщением о минимальной сумма заказа
                $orderContent.find('.jsMinOrderSumPopup').lightbox_me({
                    closeClick: false,
                    closeEsc: false,
                    centered: true
                })

            }).always(function(){
                $orderContent.stop(true, true).fadeIn(200);
                if (spinner) spinner.stop();

                bindMask();
            });

        },
        log = function logF(data){
            $.ajax({
                "type": 'POST',
                "data": data,
                "url": '/order/log'
            })
        },
        /**
         * Функция отображения карты
         * @param $elem - попап
         */
        showMap = function($elem) {
            var $currentMap = $elem.find('.js-order-map').first(),
                $parent = $elem.parent(),
                mapData = $.parseJSON($currentMap.next().html()), // не очень хорошо
                mapOptions = ENTER.OrderV3.mapOptions,
                map = ENTER.OrderV3.map;

            if (mapData && typeof map.getType == 'function') {

                $elem.lightbox_me({
                    centered: true,
                    closeSelector: '.jsCloseFl',
                    onClose: function(){ $parent.append($elem) } // возвращаем элемент на место
                });

                if (!$elem.is(':visible')) $elem.show();

                map.geoObjects.removeAll();
                map.setCenter([mapOptions.latitude, mapOptions.longitude], mapOptions.zoom);
                $currentMap.append(ENTER.OrderV3.$map.show());
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
        },
        showHideLabels = function showHideLabels() {
            var $this = $(this),
                $label = $this.parent().find('.js-order-ctrl__lbl');

            if ( $this.val() !== '' ) {
                $label.show();
            } else {
                $label.hide();
            }
        },
        bindMask = function() {
            var $inputs = $('.js-order-ctrl__input');

            $.map($inputs, function(elem, i) {
                if (typeof $(elem).data('mask') !== 'undefined') $(elem).mask($(elem).data('mask'));
            });
        },
        loadPaymentForm = function($container, url, data) {
            $container.html('Загрузка...'); // TODO: loader

            $.ajax({
                url: url,
                type: 'POST',
                data: data
            }).fail(function(jqXHR){
                $container.html('Ошибка');
            }).done(function(response){
                if (response.form) {
                    $container.html(response.form);
                }
            }).always(function(){});
        }
    ;

    // TODO change all selectors to .jsMethod

    // клик по крестику на всплывающих окнах
    $orderContent.on('click', '.jsCloseFl', function(e) {
        e.stopPropagation();
        $(this).closest('.popupFl').hide();
        e.preventDefault();
    });

	$orderContent.on('click', '.jsAddressRootNode', function() {
		ENTER.OrderV3.address.inputFocus(true);
        $(this).find('.jsSmartAddressInput').focus();
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
            showMap($(this).closest('.jsOrderRow').find('.jsNewPoints'));
            $body.trigger('trackUserAction', ['10 Место_самовывоза_Доставка_ОБЯЗАТЕЛЬНО']);
        } else {
            $(elemId).show();
            $body.trigger('trackUserAction', ['11 Срок_доставки_Доставка']);
        }

        e.preventDefault();
    });

    // клик по способу доставки
	$body.on('click', '.selShop_tab:not(.selShop_tab-act)', function(){
        var token = $(this).data('token');
            //map = $(this).parent().next();
        // переключение списка магазинов
        $('.selShop_l').hide();
        $('.selShop_l[data-token='+token+']').show();
        // переключение статусов табов
        $('.selShop_tab').removeClass('selShop_tab-act');
        $('.selShop_tab[data-token='+token+']').addClass('selShop_tab-act');
        // показ карты
        //showMap(map);
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
            changeDelivery($elem.closest('.orderRow').data('block_name'), $elem.data('delivery_method_token'));
        }
    });

    // клик по способу доставки
    $orderContent.on('click', '.jsDeliveryChange:not(.active)', function() {
        var $elem = $(this);
            changeDelivery($elem.closest('.jsOrderRow').data('block_name'), $elem.data('delivery_method_token'));

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
        var blockname = $(this).data('blockname'),
            id = $(this).data('id'),
            token = $(this).data('token');
        if (id && token) {
            $body.trigger('trackUserAction', ['10_1 Ввод_данных_Самовывоза_Доставка_ОБЯЗАТЕЛЬНО']);
            $body.children('.selShop, .lb_overlay').remove();
            changePoint(blockname, id, token);
        }
    });

    $body.on('click', '.jsOrderV3Dropbox',function(){
        $(this).siblings().removeClass('opn').find('.jsOrderV3DropboxInner').hide(); // скрываем все, кроме потомка
        $(this).find('.jsOrderV3DropboxInner').toggle(); // потомка переключаем
        $(this).hasClass('opn') ? $(this).removeClass('opn') : $(this).addClass('opn');
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
        changeProductQuantity($this.data('block_name'), $this.data('id'), $this.data('ui'), quantity);
        e.preventDefault();
    });

    // клик по ссылке "Удалить" у каунтера
    $orderContent.on('click', '.jsDeleteProduct', function(e){
        var $this = $(this);
        $('.js-order-overlay').remove();
        changeProductQuantity($this.data('block_name'), $this.data('id'), $this.data('ui'), 0);
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
    $orderContent.on('click', '.jsOrderV3Comment', function(){
        $('.orderComment_fld').toggle();
    });

    // применить скидку
    $orderContent.on('click', '.jsApplyDiscount', function(e){
        var $this = $(this),
            $orderBlock = $this.closest('.orderRow'),
            block_name = $orderBlock.data('block_name'),
            number = $this.parent().siblings('input').val().trim();

        // проверяем код PandaPay если есть совпадение маски и нет применённых дискаунтов
        if (/SN.{10}/.test(number) && $orderBlock.find('.jsOrderV3Discount').length == 0) checkPandaPay($this, number);
        // иначе стандартный вариант
        else if (number != '') applyDiscount(block_name, number);

        e.preventDefault();
    });

    // применить скидку
    $orderContent.on('click', '.jsApplyDiscount-1509', function(e){
        var
            $el = $(this),
            relations = $el.data('relation'),
            value = $el.data('value') || {}
        ;

        value['number'] = $(relations['number']).val().trim();

        // проверяем код PandaPay если есть совпадение маски и нет применённых дискаунтов
        if (/SN.{10}/.test(value['number']) && $orderBlock.find('.jsOrderV3Discount').length == 0) {
            checkPandaPay($el, value['number']); // иначе стандартный вариант
        } else if ('' != value['number']) {
            applyDiscount(value[['block_name']], value['number']);
        }

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

    $orderContent.on('click', '.jsDeleteCertificate', function(){
        var block_name = $(this).closest('.orderRow').data('block_name');
        deleteCertificate(block_name);
    });

    // клик по "Я ознакомлен и согласен..."
    $orderContent.on('click', '.jsAcceptTerms', function(){
        if (!$('.jsAcceptAgreement').is(':checked')) $body.trigger('trackUserAction', ['14 Согласен_оферта_Доставка_ОБЯЗАТЕЛЬНО']);
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
				url: ENTER.utils.setURLParam('ajax', 1, href),
				success: function(data) {
					$('.orderOferta_tl:first').html(data.content || '');
					showOfertaPopup();
				}
			})
		}
	});

	$body.on('click', '.js-oferta-tab', function(){
		tabsOfertaAction(this)
	});

    // Попап с сообщением о минимальной сумма заказа
    $('.jsMinOrderSumPopup').lightbox_me({
        closeClick: false,
        closeEsc: false,
        centered: true
    });

	// ДЛЯ АБ-ТЕСТА ПО МОТИВАЦИИ ОНЛАЙН-ОПЛАТЫ
	$body.on('click', '.jsPaymentMethodRadio', function(){
		var $this = $(this),
			block_name = $this.closest('.orderRow').data('block_name'),
			method = $this.val();
        if (method == 'by_online_credit') {
            $body.trigger('trackGoogleEvent', ['Воронка_новая_v2', '13_3 Способы_оплаты_Доставка', 'Кредит']);
            $body.trigger('trackGoogleEvent', ['Credit', 'Выбор опции', 'Оформление заказа']);
        }
        if (method == 'by_online') $body.trigger('trackGoogleEvent', ['Воронка_новая_v2', '13_3 Способы_оплаты_Доставка', 'Онлайн-оплата']);
		changePaymentMethod(block_name, method, 'true')
	});

	$body.on('change', '.jsPaymentMethodSelect', function(e){
		var $this = $(this),
			block_name = $this.closest('.orderRow').data('block_name'),
			selectedMethod = $this.find(':selected').val();
		changePaymentMethod(block_name, selectedMethod, 'true');
        console.log('[G changed', e);
        if (selectedMethod == 'by_credit_card') $body.trigger('trackGoogleEvent', ['Воронка_новая_v2', '13_3 Способы_оплаты_Доставка', 'Картой_курьеру']);
		e.preventDefault();
	});

    $body.on('change', '.js-order-paymentMethod', function(e) {
        var
            $el = $(this),
            params = $el.data('value')
        ;

        sendChanges('changePaymentMethod', params);

        if ($el.data('online')) {
            $body.trigger('trackGoogleEvent', ['Воронка_новая_v2', '13_3 Способы_оплаты_Доставка', 'Картой_курьеру']);
        }

        //e.preventDefault();
    });

    $body.on('change', '.js-order-onlinePaymentMethod', function(e) {
        var
            $el = $(this),
            url = $el.data('url'),
            data = $el.data('value'),
            relations = $el.data('relation'),
            $formContainer = relations['formContainer'] && $(relations['formContainer'])
        ;

        try {
            if (!url) {
                throw {message: 'Не задан url для получения формы'};
            }
            if (!$formContainer.length) {
                throw {message: 'Не найден контейнер для формы'};
            }

            loadPaymentForm($formContainer, url, data);
        } catch(error) { console.error(error); };

        //e.preventDefault();
    });
    $('.js-order-onlinePaymentMethod').each(function(i, el) {
        var
            $el = $(this),
            url,
            data,
            relations,
            $formContainer
        ;

        if ($el.data('checked')) {
            url = $el.data('url');
            data = $el.data('value');
            relations = $el.data('relation');
            $formContainer = relations['formContainer'] && $(relations['formContainer']);

            loadPaymentForm($formContainer, url, data);
        }
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

        $body.trigger('trackGoogleEvent', [{
            'eventCategory': 'Воронка_' + oldRegion,
            'eventAction': '8 Регион_Доставка',
            'eventLabel': 'Было: ' + oldRegion + ', Стало: ' + newRegion,
            'hitCallback': link
        }]);

    });

    $body.on('change', '.jsDeliveryMapFilters input', function(){
        var type = $(this).data('type'),
            val = $(this).next().find('span').text();
        $body.trigger('trackGoogleEvent', ['pickup_ux', 'filter', type + '_' + val]);
    });

    $body.on('click', '.jsMapDeliveryList .jsChangePoint', function(){
        $body.trigger('trackGoogleEvent', ['pickup_ux', 'list_point', 'выбор'])
    });

    $.each($inputs, showHideLabels);
    $body.on('keyup', '.js-order-ctrl__input', showHideLabels);

    //показать блок редактирования товара - новая версия
    $body.on('click', '.js-show-edit',function(){
        $(this).hide();
        $(this).parent().find('.js-edit').show();
    });
    //изменение кол-ва товара - новая версия
    $body.on('click','.js-edit-quant',function(){
        var $this = $(this),
            $input = $this.parent().find('.js-quant'),
            min = $input.data('min'),
            delta = $this.data('delta'),
            newVal = parseInt($input.val()) + parseInt(delta);
        if (newVal >= min){
            $input.val(newVal);
        }

    });
    //вызов попапа подтверждения удаления товара из заказа
    $body.on('click','.js-del-popup-show',function(){
        var $this = $(this);
            $this.parent().find('.js-del-popup').show();
		$body.append("<div class='order-popup__overlay js-order-overlay'></div>");
    });
    $body.on('click','.js-del-popup-close',function(){
        var $this = $(this);
        $this.closest('.js-del-popup').hide();
		$('.js-order-overlay').remove();
    });
    //закрытие алертов к заказу
    $body.on('click','.js-order-err-close',function(){
        $(this).closest('.order-error').hide();
    });
	$body.on('click','.js-order-overlay',function(){
		$body.find('.js-del-popup').hide();
		$(this).remove();
	});

    // авктокомплит адерса
    $body.on('focus', '.js-order-deliveryAddress', function() {

        var $el = $(this),
            type = $el.data('field'), // тип поля адреса (улица, дом)
            relations = $el.data('relation'),
            parentKladrId = $el.data('parent-kladr-id'),
            $container = $(relations['container']),
            $inputFields = $container.find('input.js-order-deliveryAddress');

        function autoCompleteRequest (request, response) {
            if (getParent() !== false) {
                var query = $.extend({}, { limit: 10, name: request.term }, getParent());
                console.log('[КЛАДР] запрос: ', query);
                $.kladr.api(query, function (data) {
                    console.log('[КЛАДР] ответ', data);
                    response($.map(data, function (elem) {
                        return { label: (type == 'street' ? elem.name + ' ' + elem.typeShort + '.' : elem.name)  , value: elem }
                    }))
                });
            }
        }

        function getParent() {
            var result = false;
            if (type == 'street' && parentKladrId) result = { type: $.kladr.type.street, parentType: 'city', parentId: parentKladrId };
            else if (type == 'building' && parentKladrId) result = { type: $.kladr.type.building, parentType: 'street', parentId: parentKladrId };
            return result;
        }

        function save() {
            $.ajax({
                type: 'POST',
                data: {
                    'action' : 'changeAddress',
                    'params' : {
                        // сохраняем улицу в формате "Название + сокращенный тип" для автосогласования в 1С
                        street: $inputFields.eq(0).val(),
                        building: $inputFields.eq(1).val(),
                        apartment: $inputFields.eq(2).val(),
                        kladr_id: $container.data('last-kladr-id') }
                }
            }).fail(function(jqXHR){
                var response = $.parseJSON(jqXHR.responseText);
                if (response.result) {
                    console.error(response.result);
                }
            }).done(function(data){
//			console.log("Query: %s", data.result.OrderDeliveryRequest);
                console.log("Saved address:", data.result.OrderDeliveryModel.user_info.address);
            })
        }

        $el.autocomplete({
//            appendTo: '#kladrAutocomplete',
            source: autoCompleteRequest,
            minLength: 1,
            open: function( event, ui ) {
                //$('.ui-autocomplete').css({'position' : 'absolute', 'top' : 29, 'left' : 0});
            },
            select: function( event, ui ) {
                $el.val(ui.item.label);
                $inputFields.eq(1).data('parent-kladr-id', ui.item.value.id);
                $container.data('last-kladr-id', ui.item.value.id);
                save();
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

        // Сохранение дома
        $inputFields.eq(2).off().on('keyup', save);

    });

    $('#auth-block').attr('data-state', 'register').addClass('state_register');

    $body.on('click', '[form="js-orderForm"]', function(e) {
        var
            $el = $(this),
            $form = $el.attr('form') && $('#' + $el.attr('form')),
            formResult = { errors: [] }
        ;
        console.info($el, $form, formResult);

        try {
            if ($form.length) {
                $form.trigger('form.reset');

                $form.find('[required]').each(function(i, el) {
                    var $el = $(el);

                    if ($el.is(':checkbox')) {
                        !$el.is(':checked') && formResult.errors.push({message: '', field: $el.data('field')});
                    } else if ($el.is('input')) {
                        console.warn($el.data('field'));
                        !$el.val() && formResult.errors.push({message: '', field: $el.data('field')});
                    }
                });

                if (formResult.errors.length) {
                    $form.trigger('form.result', [formResult]);
                } else {
                    $form.submit();
                }

                e.preventDefault();
            } else {
                // default handler
                console.warn('form not found');
            }
        } catch (error) { console.error(); }
    });

    // jQuery masked input
    delete $.mask.definitions[9];
    $.mask.definitions['x']='[0-9]';
    $.mask.placeholder= "_";
    $.mask.autoclear = false;
    bindMask();

    $body.on('input', '.js-quant', function() {
        var $el = $(this);

        $el.val($el.val().replace(/[^0-9]+/g, ''));
    });
})(jQuery);