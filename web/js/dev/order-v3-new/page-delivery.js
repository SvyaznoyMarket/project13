;(function($) {

    ENTER.OrderV3 = ENTER.OrderV3 || {};

    try {
        console.log('Model', $.parseJSON($('#initialOrderModel').html()));


    } catch (e) {
    }

    var
        body          = document.getElementsByTagName('body')[0],
        $body         = $(body),
        $orderWrapper = $('.js-order-wrapper'),
        $inputs       = $('.js-order-ctrl__input'),
        $offertaPopup = $('.js-order-oferta-popup').eq(0),
        useNodeMQ     = $('#page-config').data('value')['useNodeMQ'],
        ws_client     = null,
        validator     = null,
        commentOpenClass = 'opened',
        dropboxOpenedClass = 'is-open',
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
            position: 'fixed',
            zIndex: 200,
            top: '50%', // Top position relative to parent
            left: '50%' // Left position relative to parent
        }) : null,
        changeDelivery = function changeDeliveryF (block_name, delivery_method_token) {
            sendChanges('changeDelivery', {'block_name': block_name, 'delivery_method_token': delivery_method_token});
            ENTER.utils.analytics.setAction('checkout_option', {
                'step': 2,
                'option' : delivery_method_token == 'self' ? 'самовывоз' : 'доставка'
            });
            $body.trigger('trackGoogleEvent', ['Checkout', 'Option'])
        },
        changeDate = function changeDateF (block_name, timestamp) {
            sendChanges('changeDate', {'block_name': block_name, 'date': timestamp})
        },
        changePoint = function changePointF (block_name, id, token) {
            sendChanges('changePoint', {'block_name': block_name, 'id': id, 'token': token})
        },
        deletePoint = function(block_name) {
            sendChanges('deletePoint', {'block_name': block_name})
        },
        changeInterval = function changeIntervalF(block_name, interval) {
            sendChanges('changeInterval', {'block_name': block_name, 'interval': interval})
        },
        changeProductQuantity = function changeProductQuantityF(block_name, ui, quantity) {
            sendChanges('changeProductQuantity', {'block_name': block_name, 'ui': ui, 'quantity': quantity})
        },
        moveProductToFavorite = function(block_name, ui) {
            sendChanges('moveProductToFavorite', {'block_name': block_name, 'ui': ui})
        },
        stashOrder = function(block_name) {
            sendChanges('stashOrder', {'block_name': block_name})
        },
        changePaymentMethod = function changePaymentMethodF(block_name, method, isActive) {
            var params = {'block_name': block_name};
            params[method] = isActive;
            sendChanges('changePaymentMethod', params)
        },
        applyDiscount = function applyDiscountF(block_name, number) {
            var pin = $('[data-block_name=' + block_name + ']').find('.jsCertificatePinInput').val();
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
                    $('[data-block_name='+block_name+']').find('.jsCertificatePinField').show();
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

            if ($.inArray(action, ['stashOrder', 'moveProductToFavorite']) != -1 && !ENTER.config.userInfo.user.isLogined) {
                var redirectTo = location.href;
                redirectTo = ENTER.utils.setURLParam('action', action, redirectTo);
                redirectTo = ENTER.utils.setURLParam('params', JSON.stringify(params), redirectTo);
                ENTER.auth.open(redirectTo, redirectTo, true);
                return;
            }

            var hideContent = true,

                before = function() {
                    if (hideContent) $orderWrapper.fadeOut(500);
                    if (spinner) spinner.spin(body)
                },

                done = function( data ) {
                    console.info('done callback', data);

                    if (!data.result) {
                        console.log('no data.result');
                        return;
                    }

                    if (data.result.redirectUrl) {
                        console.info('REDIRECT', data.result.redirectUrl);
                        location.href = data.result.redirectUrl;
                        return;
                    }

                    if (data.result.OrderDeliveryModel) {
                        console.log("Model:", data.result.OrderDeliveryModel);
                    }

                    $('.jsNewPoints').remove(); // иначе неправильно работает биндинг
                    $offertaPopup.remove();
                    $orderWrapper.empty().html(data.result.page);
                    $offertaPopup = $('.js-order-oferta-popup').eq(0);

                    if ($orderWrapper.find('.jsAddressRootNode').length > 0) {
                        $.each($orderWrapper.find('.jsAddressRootNode'), function(i,val){
                            ko.applyBindings(ENTER.OrderV3.address, val);
                        });
                        if (typeof ENTER.OrderV3.constructors.smartAddressInit == 'function') ENTER.OrderV3.constructors.smartAddressInit();
                    }

                    // Новый самовывоз
                    ENTER.OrderV3.koModels = {};
                    $.each($orderWrapper.find('.jsNewPoints'), function(i,val) {
                        var pointData = $.parseJSON($(this).find('script.jsMapData').html()),
                            points = new ENTER.DeliveryPoints(pointData.points, ENTER.OrderV3.map, pointData.enableFitsAllProducts);
                        ENTER.OrderV3.koModels[$(this).data('id')] = points;
                        ko.applyBindings(points, val);
                    });

                    // Попап с сообщением о минимальной сумма заказа
                    $orderWrapper.find('.jsMinOrderSumPopup').lightbox_me({
                        closeClick: false,
                        closeEsc: false,
                        centered: true
                    });

                    $inputs = $('.js-order-ctrl__input');
                    $.each($inputs, lblPosition);

                    after();

                    if (data.result.needAuth) {
                        if ($.inArray(action, ['stashOrder', 'moveProductToFavorite']) != -1) {
                            var redirectTo = location.href;
                            redirectTo = ENTER.utils.setURLParam('action', action, redirectTo);
                            redirectTo = ENTER.utils.setURLParam('params', JSON.stringify(params), redirectTo);
                            ENTER.auth.open(redirectTo, redirectTo, true);
                        } else {
                            ENTER.auth.open();
                        }
                    }
                },

                after = function() {
                    $orderWrapper.fadeIn(500);

                    if (!$orderWrapper.find('.js-order-undo-container').length) {
                        if (spinner) spinner.stop();
                    }

                    bindMask();
                };

            // SITE-5575
            if (-1 !== $.inArray(action, ['changeDate', 'changeInterval'])) hideContent = false;

            if ( useNodeMQ ) {
                console.log(ws_client);
                ws_client.send({
                    data: {
                        'action' : action,
                        'params' : params || {}
                    },
                    done: done,
                    fail: function( error ) {
                        console.log(error);
                        after();
                    },
                    beforeSend: before
                });
            } else {
                $.ajax({
                    url: ENTER.utils.generateUrl('orderV3.delivery'),
                    type: 'POST',
                    data: {
                        'action' : action,
                        'params' : params || {}
                    },
                    beforeSend: before
                }).fail(function(jqXHR){
                    var response = $.parseJSON(jqXHR.responseText);
                    if (response && response.result) {
                        console.error(response.result);
                    }

                    after();
                }).done(done);
            }

        },
        /**
         * Функция отображения карты
         * @param $elem - попап
         */
        showMap = function($elem) {
            var $currentMap = $elem.find('.js-order-map').first(),
                $parent = $elem.parent(),
                mapOptions = ENTER.OrderV3.mapOptions,
                map = ENTER.OrderV3.map;

            if (typeof map.getType == 'function') {

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
                $.each(ENTER.OrderV3.koModels[$elem.data('id')].availablePoints(), function(i, point){
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

        showOfertaPopup = function showOfertaPopupF() {
            $offertaPopup.lightbox_me();
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
        lblPosition = function lblPosition() {
            var $this = $(this),
                $label = $this.parent().find('.js-order-ctrl__txt');
            //console.log($label);
            if ($this.is(":focus") || ($this.val() !== '') ) {
                $label.addClass('top');
            } else {
                $label.removeClass('top');
            }
        },
        bindMask = function() {
            var
                $inputs        = $('.js-order-ctrl__input'),
                $phoneInput    = $('.js-order-phone'),
                $emailInput    = $('.js-order-email'),
                $agreeCheckbox = $('.jsAcceptAgreement'),
                $address       = $('.js-order-deliveryAddress'),

                validationConfig = {
                    fields: [{
                        fieldNode: $agreeCheckbox,
                        require: true,
                        errorMsg: null
                    }],
                    callbackError: function( field, error ) {
                        var
                            parent = field.fieldNode.parent();
                        console.warn('===== custom callbackError', field.fieldNode.parent());
                        parent.addClass('error');
                        parent.find('.js-order-ctrl__txt').html(error);
                    },
                    callbackValid: function( field ) {},
                    unmarkField: function( field ) {
                        console.log('custom unmarkField callback ');
                        var
                            parent = field.fieldNode.parent();

                        parent.removeClass('error');
                        parent.find('.order-ctrl__txt').html(field.fieldNode.data('text-default'));
                    }
                };

            // Validator
            $phoneInput.length && validationConfig.fields.push({
                fieldNode: $phoneInput,
                require: !!$phoneInput.attr('required'),
                validBy: 'isPhone',
                validateOnChange: true,
                errorMsg: 'Введите телефон'
            });

            $address.length && $address.each(function() {
                var
                    $self = $(this);

                validationConfig.fields.push({
                    fieldNode: $self,
                    require: !!$self.attr('required'),
                    validateOnChange: true,
                    errorMsg: $self.attr('data-text-default')
                });
            });

            $emailInput.length && validationConfig.fields.push({
                fieldNode: $emailInput,
                require: !!$emailInput.attr('required'),
                validBy: 'isEmail',
                validateOnChange: true,
                errorMsg: 'Введите email'
            });

            if ( validationConfig.fields.length ) {
                validator = new FormValidator(validationConfig);
            }

            // masks
            $.map($inputs, function(elem, i) {
                if (typeof $(elem).data('mask') !== 'undefined') $(elem).mask($(elem).data('mask'));
            });
        }
    ;

    // клик по крестику на всплывающих окнах
    $orderWrapper.on('click', '.jsCloseFl', function(e) {
        e.stopPropagation();
        $(this).closest('.popupFl').hide();
        e.preventDefault();
    });

    $orderWrapper.on('click', '.jsAddressRootNode', function() {
        ENTER.OrderV3.address.inputFocus(true);
        $(this).find('.jsSmartAddressInput').focus();
    });

    $orderWrapper.on('blur', '.jsSmartAddressInput', function() {
        ENTER.OrderV3.address.inputFocus(false);
    });

    // клик по "изменить дату" и "изменить место"
    $orderWrapper.on('click', '.orderCol_date, .js-order-changePlace-link', function(e) {
        e.preventDefault();

        var elemId = $(this).data('content');
        $('.popupFl').hide();

        if ($(this).hasClass('js-order-changePlace-link')) {
            showMap($(this).closest('.jsOrderRow').find('.jsNewPoints'));
            $body.trigger('trackUserAction', ['10 Место_самовывоза_Доставка_ОБЯЗАТЕЛЬНО']);
        } else {
            $(elemId).show();
            $body.trigger('trackUserAction', ['11 Срок_доставки_Доставка']);
        }
    });

    $orderWrapper.on('click', '.js-order-deletePlace', function(e) {
        e.preventDefault();
        deletePoint($(this).closest('.jsOrderRow').data('block_name'));
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
    $orderWrapper.on('click', '.jsShowDiscountForm', function(e) {
        e.stopPropagation();
        $(this).hide().parent().next().show();
    });

    // клик по способу доставки
    $orderWrapper.on('click', '.orderCol_delivrLst li', function() {
        var $elem = $(this);
        if (!$elem.hasClass('orderCol_delivrLst_i-act')) {
            changeDelivery($elem.closest('.orderRow').data('block_name'), $elem.data('delivery_method_token'));

        }
    });

    // клик по способу доставки
    $orderWrapper.on('click', '.jsDeliveryChange:not(.active)', function() {
        var $elem = $(this);
        changeDelivery($elem.closest('.jsOrderRow').data('block_name'), $elem.data('delivery_method_token'));

    });

    // клик по дате в календаре
    $orderWrapper.on('click', '.celedr_col', function(){
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

    // интервалы доставки
    $orderWrapper.dropbox({
        cssSelectors: {
            container: '.js-order-delivery-interval-dropbox-container',
            opener: '.js-order-delivery-interval-dropbox-opener',
            content: '.js-order-delivery-interval-dropbox-content',
            item: '.js-order-delivery-interval-dropbox-item'
        },
        htmlClasses: {
            item: {
                hover: 'order-ctrl__custom-select-item_hover'
            },
            container: {
                opened: dropboxOpenedClass
            }
        },
        onClick: function(e) {
            changeInterval(e.$item.closest('.jsOrderRow').data('block_name'), e.$item.data('value'));
        }
    });

    // клик по ссылке "Применить" у товара
    $orderWrapper.on('click', '.jsChangeProductQuantity', function(e){
        var $this = $(this),
            quantity = $this.parent().find('input').val();
        changeProductQuantity($this.data('block_name'), $this.data('ui'), quantity);
        e.preventDefault();
    });

    $orderWrapper.on('click', '.js-order-stash', function(e) {
        e.preventDefault();
        stashOrder($(this).attr('data-block_name'));
    });

    $orderWrapper.on('click', '.js-order-undo-apply', function(e) {
        e.preventDefault();
        sendChanges('undo');
    });

    $orderWrapper.on('click', '.js-order-undo-close, .js-order-undo-overlay', function(e) {
        e.preventDefault();

        var
            $undoContainer = $('.js-order-undo-container'),
            redirectUrl = $undoContainer.attr('data-redirect-url');

        if (redirectUrl) {
            console.info('REDIRECT', redirectUrl);
            location.href = redirectUrl;
        } else {
            $undoContainer.fadeOut(500, function() {
                $undoContainer.remove();
            });

            if (spinner) {
                spinner.stop();
            }
        }
    });

    $orderWrapper.dropbox({
        cssSelectors: {
            container: '.js-order-product-actions-dropbox-container',
            opener: '.js-order-product-actions-dropbox-opener',
            content: '.js-order-product-actions-dropbox-content',
            item: '.js-order-product-actions-dropbox-item'
        },
        htmlClasses: {
            item: {
                hover: 'order-ctrl__custom-select-item_hover'
            }
        },
        onClick: function(e) {
            switch (e.$item.attr('data-action')) {
                case 'favorite':
                    moveProductToFavorite(e.$item.attr('data-block_name'), e.$item.attr('data-product-ui'));
                    break;
                case 'delete':
                    changeProductQuantity(e.$item.attr('data-block_name'), e.$item.attr('data-product-ui'), 0);
                    break;
            }
        }
    });

    // клик по безналичному методу оплаты
    $orderWrapper.on('change', '.jsCreditCardPayment', function(){
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name');
        if ($this.is(':checked')) $body.trigger('trackUserAction', ['13_1 Оплата_банковской_картой_Доставка']);
        changePaymentMethod(block_name, 'by_credit_card', $this.is(':checked'))
    });

    // клик по "купить в кредит"
    $orderWrapper.on('change', '.jsCreditPayment', function() {
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name');
        if ($this.is(':checked')) $body.trigger('trackUserAction', ['13_2 Оплата_в_кредит_Доставка']);
        changePaymentMethod(block_name, 'by_online_credit', $(this).is(':checked'))
    });

    // сохранение комментария
    !function() {
        var timer = null;
        $orderWrapper.on('input', '.jsOrderV3CommentField', function() {
            var $comment = $(this);
            if (timer) {
                clearTimeout(timer);
            }

            timer = setTimeout(function() {
                $.ajax({
                    url: ENTER.utils.generateUrl('orderV3.delivery'),
                    type: 'POST',
                    data: {
                        action: 'changeOrderComment',
                        params: {
                            comment: $comment.val()
                        }
                    }
                });
            }, 400);
        });
    }();

    // клик по "Дополнительные пожелания"
    $orderWrapper.on('click', '.jsOrderV3Comment', function(){
        var $comment = $('.jsOrderV3CommentField');
        $comment.toggle();

        if ($comment.css('display') == 'none') {
            $(this).removeClass(commentOpenClass);
        } else {
            $(this).addClass(commentOpenClass);
        }
    });

    // применить скидку
    $orderWrapper.on('click', '.jsApplyDiscount-1509', function(e){
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
    $orderWrapper.on('click', '.jsDeleteDiscount', function(e){
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name'),
            number = $this.data('value');
        deleteDiscount(block_name, number);
        e.preventDefault();
    });

    $orderWrapper.on('click', '.jsDeleteCertificate', function(){
        var block_name = $(this).closest('.orderRow').data('block_name');
        deleteCertificate(block_name);
    });

    // клик по "Я ознакомлен и согласен..."
    $orderWrapper.on('click', '.jsAcceptTerms', function(){
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
                    $offertaPopup.find('.orderOferta_tl:first').html(data.content || '');
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

    $orderWrapper.dropbox({
        cssSelectors: {
            container: '.js-order-payment-methods-dropbox-container',
            opener: '.js-order-payment-methods-dropbox-opener',
            content: '.js-order-payment-methods-dropbox-content',
            item: '.js-order-payment-methods-dropbox-item'
        },
        htmlClasses: {
            item: {
                hover: 'order-ctrl__custom-select-item_hover'
            },
            container: {
                opened: dropboxOpenedClass
            }
        },
        onClick: function(e) {
            var
                $el = e.$item.find('.js-order-paymentMethod'),
                params = $el.data('value')
            ;

            console.info({'$el': $el, 'data': params});

            sendChanges('changePaymentMethod', params);

            if ($el.data('online')) {
                $body.trigger('trackGoogleEvent', ['Воронка_новая_v2', '13_3 Способы_оплаты_Доставка', 'Картой_курьеру']);
            }
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

    //$.each($inputs, lblPosition);
    $(document).ready(function(){
        $.each($inputs, lblPosition);
    });

    $body.on('focus blur input', '.js-order-ctrl__input', function(e){

        if(e.type == 'input'){
            setTimeout(function(){
                $.each($inputs, lblPosition);
            }, 300);
        }else{
            $.each($inputs, lblPosition);
        }
    });

    $body.on('click', '.js-order-ctrl__txt', function(){

        var $this = $(this),
            input = $this.siblings('.js-order-ctrl__input');

        if($this.hasClass('top')) return false;

        input.focus();
        $.each($inputs, lblPosition);
    });


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

        if (newVal <= min) {
            $('.js-edit-quant-decrease').attr('disabled', 'disabled');
        } else {
            $('.js-edit-quant-decrease').removeAttr('disabled');
        }
    });

    //закрытие алертов к заказу
    $body.on('click','.js-order-err-close',function(){
        $(this).closest('.order-error').hide();
    });
	$body.on('click','.js-order-overlay',function(){
        $(this).remove();
    });

    // Адреса
    !function() {
        function saveAddress($addressBlocks) {
            if (!$addressBlocks.length) {
                return;
            }

            var $firstAddressBlock = $($addressBlocks[0]);

            $.ajax({
                url: ENTER.utils.generateUrl('orderV3.delivery'),
                type: 'POST',
                data: {
                    'action': 'changeAddress',
                    'params': {
                        // сохраняем улицу в формате "Название + сокращенный тип" для автосогласования в 1С
                        street: $firstAddressBlock.find('.js-order-deliveryAddress[data-field="street"]').val(),
                        building: $firstAddressBlock.find('.js-order-deliveryAddress[data-field="building"]').val(),
                        apartment: $firstAddressBlock.find('.js-order-deliveryAddress[data-field="apartment"]').val(),
                        kladr_id: $firstAddressBlock.attr('data-kladr-id'),
                        kladrZipCode: $firstAddressBlock.attr('data-kladr-zip-code'),
                        kladrStreet: $firstAddressBlock.attr('data-kladr-street'),
                        kladrStreetType: $firstAddressBlock.attr('data-kladr-street-type'),
                        kladrBuilding: $firstAddressBlock.attr('data-kladr-building'),
                        isSaveAddressChecked: $firstAddressBlock.find('.js-order-saveAddress').prop('checked') ? '1' : '',
                        isSaveAddressDisabled: $firstAddressBlock.find('.js-order-saveAddress').attr('disabled') ? '1' : ''
                    }
                }
            }).fail(function(jqXHR) {
                var response = $.parseJSON(jqXHR.responseText);
                if (response.result) {
                    console.error(response.result);
                }
            }).done(function(data) {
                if (data.result && data.result.OrderDeliveryModel) {
                    console.log("Saved address:", data.result.OrderDeliveryModel.user_info.address);
                }
            })
        }

        // автокомплит адреса
        $body.on('focus', '.js-order-deliveryAddress[data-field="street"], .js-order-deliveryAddress[data-field="building"]', function() {

            var $el = $(this),
                kladrId = $el.closest('.jsSmartAddressBlock').attr('data-kladr-id'),
                cityKladrId = ENTER.utils.kladr.getCityIdFromKladrId(kladrId),
                streetKladrId = ENTER.utils.kladr.getStreetIdFromKladrId(kladrId),
                $addressBlocks = $('.jsSmartAddressBlock');

            $el.myAutocomplete({
                source: function(request, responseCallback) {
                    var
                        parentKladrQuery = null,
                        field = $el.attr('data-field');

                    if (field == 'street' && cityKladrId) {
                        parentKladrQuery = {
                            type: $.kladr.type.street,
                            parentType: 'city',
                            parentId: cityKladrId
                        };
                    } else if (field == 'building' && streetKladrId) {
                        parentKladrQuery = {
                            type: $.kladr.type.building,
                            parentType: 'street',
                            parentId: streetKladrId
                        };
                    }

                    if (parentKladrQuery) {
                        var query = $.extend({}, {limit: 10, name: request.term}, parentKladrQuery);
                        console.log('[КЛАДР] запрос: ', query);
                        $.kladr.api(query, function(data) {
                            console.log('[КЛАДР] ответ', data);
                            responseCallback($.map(data, function(elem) {
                                return {
                                    label: field == 'street' ? elem.name + ' ' + elem.typeShort + '.' : elem.name,
                                    value: elem
                                };
                            }));
                        });
                    }
                },
                minLength: 1,
                select: function(event, ui) {
                    $addressBlocks.find('.js-order-deliveryAddress[data-field=' + $el.attr('data-field') + ']').val(ui.item.label);

                    $addressBlocks.attr('data-kladr-id', ui.item.value.id);

                    if (ui.item.value.contentType == 'street') {
                        $addressBlocks.attr('data-kladr-zip-code', '');
                        $addressBlocks.attr('data-kladr-street', ui.item.value.name);
                        $addressBlocks.attr('data-kladr-street-type', ui.item.value.typeShort);
                        $addressBlocks.attr('data-kladr-building', '');

                        $addressBlocks.find('.js-order-deliveryAddress[data-field="building"]').val('');
                    } else if (ui.item.value.contentType == 'building') {
                        $addressBlocks.attr('data-kladr-zip-code', ui.item.value.zip);
                        $addressBlocks.attr('data-kladr-building', ui.item.value.name);

                        $addressBlocks.find('.js-order-saveAddress').removeAttr('disabled');
                    }

                    saveAddress($addressBlocks);
                    return false;
                },
                focus: function(event, ui) {
                    this.value = ui.item.label;
                    event.preventDefault(); // without this: keyboard movements reset the input to ''
                    event.stopPropagation(); // without this: keyboard movements reset the input to ''
                },
                change: function(event, ui) {
                },
                renderMenu: function(ul) {
                    if ($el.attr('data-field') == 'street') {
                        ul.addClass('ui-autocomplete-street');
                    } else {
                        ul.addClass('ui-autocomplete-house-or-apartment');
                    }
                },
                messages: {
                    noResults: '',
                    results: function() {
                    }
                }
            });
        });

        // Обработка полей адреса
        !function() {
            var timers = {};
            $body.on('input', '.js-order-deliveryAddress', function() {
                var $input = $(this);

                var
                    $addressBlocks = $('.jsSmartAddressBlock'),
                    field = $input.attr('data-field'),
                    kladrId = $addressBlocks.attr('data-kladr-id');

                $addressBlocks.find('.js-order-deliveryAddress[data-field=' + field + ']').val($input.val());

                if (field == 'street') {
                    var cityKladrId = ENTER.utils.kladr.getCityIdFromKladrId(kladrId);
                    if (cityKladrId) {
                        $addressBlocks.attr('data-kladr-id', cityKladrId);
                    }

                    $addressBlocks.attr('data-kladr-zip-code', '');
                    $addressBlocks.attr('data-kladr-street', '');
                    $addressBlocks.attr('data-kladr-street-type', '');
                    $addressBlocks.attr('data-kladr-building', '');

                    $addressBlocks.find('.js-order-saveAddress').removeAttr('checked').attr('disabled', 'disabled');
                } else if (field == 'building') {
                    var streetKladrId = ENTER.utils.kladr.getStreetIdFromKladrId(kladrId);
                    if (streetKladrId) {
                        $addressBlocks.attr('data-kladr-id', streetKladrId);
                    }

                    $addressBlocks.attr('data-kladr-zip-code', '');
                    $addressBlocks.attr('data-kladr-building', '');

                    $addressBlocks.find('.js-order-saveAddress').removeAttr('checked').attr('disabled', 'disabled');
                }

                if (timers[field]) {
                    clearTimeout(timers[field]);
                }

                timers[field] = setTimeout(function() {
                    saveAddress($addressBlocks);
                }, 400);
            });
        }();

        $orderWrapper.dropbox({
            cssSelectors: {
                container: '.js-order-user-address-container',
                opener: '.js-order-user-address-opener',
                content: '.js-order-user-address-content',
                item: '.js-order-user-address-item'
            },
            htmlClasses: {
                item: {
                    hover: 'order-ctrl__custom-select-item_hover'
                },
                container: {
                    opened: dropboxOpenedClass
                }
            },
            onClick: function(e) {
                var $addressBlocks = $('.jsSmartAddressBlock');

                $addressBlocks.attr('data-kladr-id', e.$item.attr('data-kladr-id'));
                $addressBlocks.attr('data-kladr-zip-code', e.$item.attr('data-zip-code'));
                $addressBlocks.attr('data-kladr-street', e.$item.attr('data-street'));
                $addressBlocks.attr('data-kladr-street-type', e.$item.attr('data-street-type'));
                $addressBlocks.attr('data-kladr-building', e.$item.attr('data-building'));

                $addressBlocks.find('.js-order-deliveryAddress[data-field="street"]').val(e.$item.attr('data-street') + ' ' + e.$item.attr('data-street-type') + '.');
                $addressBlocks.find('.js-order-deliveryAddress[data-field="building"]').val(e.$item.attr('data-building'));
                $addressBlocks.find('.js-order-deliveryAddress[data-field="apartment"]').val(e.$item.attr('data-apartment'));
                $addressBlocks.find('.js-order-saveAddress').removeAttr('checked').attr('disabled', 'disabled');

                $inputs.blur();

                saveAddress($addressBlocks);
            }
        });

        $body.on('click', '.js-order-saveAddress', function(e) {
            var $addressBlocks = $('.jsSmartAddressBlock');

            if ($(this).prop('checked')) {
                $addressBlocks.find('.js-order-saveAddress').attr('checked', 'checked');
            } else {
                $addressBlocks.find('.js-order-saveAddress').removeAttr('checked');
            }

            saveAddress($addressBlocks);
        });
    }();

    $orderWrapper.dropbox({
        cssSelectors: {
            container: '.js-order-discount-enterprize-container',
            opener: '.js-order-discount-enterprize-opener',
            content: '.js-order-discount-enterprize-content',
            item: '.js-order-discount-enterprize-item'
        },
        htmlClasses: {
            item: {
                hover: 'order-ctrl__custom-select-item_hover'
            }
        },
        onClick: function(e) {
            var couponNumber = e.$item.attr('data-coupon-number');

            if (couponNumber) {
                applyDiscount(e.$item.attr('data-block_name'), couponNumber);
            }
        }
    });

    $body.on('click', '.js-order-discount-opener', function(e) {
        e.preventDefault();
        $(this).closest('.js-order-discount-container').find('.js-order-discount-content').toggle();
    });

    $body.on('click', '[form="js-orderForm"]', function(e) {
        var
            $el        = $(this),
            $form      = $el.attr('form') && $('#' + $el.attr('form')),
            formResult = { errors: [] },
            valid      = true
        ;
        console.info($el, $form, formResult);

        try {
            if ($form.length) {

                validator && validator.validate({
                    onInvalid: function( err ) {
                        valid = false;
                    },
                    onValid: function() {
                        $form.submit();
                    }
                });

                return false;
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
        var
            $el = $(this),
            newVal = $el.val().replace(/[^0-9]+/g, '');

        if (newVal < 1) {
            newVal = 1;
        }

        $el.val(newVal);
    });

    // console.log(ENTER.config.pageConfig.useNodeMQ);
    // console.log(!!window.WebSocket);
    // console.log(ENTER.config.pageConfig.currentRoute === 'orderV3.delivery');
    if ( ENTER.config.pageConfig.useNodeMQ && !!window.WebSocket && ENTER.config.pageConfig.currentRoute === 'orderV3.delivery' ) {
        console.info('start...');

        ws_client = new WS_Client(function() {
            useNodeMQ = true;
            sendChanges();
        }, function() {
            useNodeMQ = false;
            sendChanges();
        });
    }

    !function() {
        var query = $.deparam(location.search);
        if (query.action && $.inArray(query.action, ['stashOrder', 'moveProductToFavorite']) != -1) {
            sendChanges(query.action, JSON.parse(query.params));

            var newUrl = location.href;
            newUrl = ENTER.utils.setURLParam('action', null, newUrl);
            newUrl = ENTER.utils.setURLParam('params', null, newUrl);

            history.replaceState({}, document.title, newUrl);
        }
    }();
})(jQuery);