;(function($) {

    ENTER.OrderV3 = ENTER.OrderV3 || {};

    console.log('Model', $('#initialOrderModel').data('value'));

    var body = document.getElementsByTagName('body')[0],
        $orderContent = $('#js-order-content'),
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
        changePaymentMethod = function changePaymentMethodF(block_name, by_credit_card) {
            sendChanges('changePaymentMethod', {'block_name': block_name, 'by_credit_card': by_credit_card})
        },
        changeOrderComment = function changeOrderCommentF(comment){
            sendChanges('changeOrderComment', {'comment': comment})
        },
        applyDiscount = function applyDiscountF(block_name, number) {
            sendChanges('applyDiscount',{'block_name': block_name, 'number':number})
        },
        deleteDiscount = function deleteDiscountF(block_name, number) {
            sendChanges('deleteDiscount',{'block_name': block_name, 'number':number})
        },
        checkCertificate = function checkCertificateF(number){

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
            }).done(function(data) {
                console.log("Query: %s", data.result.OrderDeliveryRequest);
                console.log("Model:", data.result.OrderDeliveryModel);
                $orderContent.empty().html($(data.result.page).find('#js-order-content').html());
                ENTER.OrderV3.constructors.smartAddress();
            }).always(function(){
                $orderContent.stop(true, true).fadeIn(200);
                if (spinner) spinner.stop();
            });

        },
        showMap = function(elem, token) {
            var $currentMap = elem.find('.js-order-map').first(),
                mapData = $currentMap.data('value'),
                mapOptions = ENTER.OrderV3.mapOptions,
                map = ENTER.OrderV3.map;

            if (!token) {
                token = Object.keys(mapData.points)[0]
            }

            if (mapData) {

                console.log('Show map with token = %s', token);

                map.geoObjects.removeAll();
                map.setCenter([mapOptions.latitude, mapOptions.longitude], mapOptions.zoom);
                $currentMap.append(ENTER.OrderV3.$map);

                for (var i = 0; i < mapData.points[token].length; i++) {
                    var point = mapData.points[token][i];

                    if (!point.latitude || !point.longitude) continue;

                    var placemark = new ymaps.Placemark([point.latitude, point.longitude], {
                        balloonContentHeader: point.name,
                        balloonContentBody: 'Адрес: ' + point.address + '',
                        hintContent: point.name
                    }, {
                        iconLayout: 'default#image',
                        iconImageHref: point.marker.iconImageHref,
                        iconImageSize: point.marker.iconImageSize,
                        iconImageOffset: point.marker.iconImageOffset
                    });

                    map.geoObjects.add(placemark);
                }

                map.setBounds(map.geoObjects.getBounds());

            } else {
                console.error('No map data for token = "%s", elemId = "%s"', token, elemId, $currentMap);
            }

            if (!elem.is(':visible')) elem.show();
        };

    // TODO change all selectors to .jsMethod

    // клик по крестику на всплывающих окнах
    $orderContent.on('click', '.popupFl_clsr', function(e) {
        e.stopPropagation();
        $(this).closest('.popupFl').hide();
        e.preventDefault();
    });

    // клик по "изменить дату" и "изменить место"
    $orderContent.on('click', '.orderCol_date, .js-order-changePlace-link', function(e) {
        var elemId = $(this).data('content');
        e.stopPropagation();
        $('.popupFl').hide();
        $(elemId).show();

        if ($(this).hasClass('js-order-changePlace-link')) {
            var token = $(elemId).find('.selShop_l:first').data('token');
            // скрываем все списки точек и показываем первую
            $(elemId).find('.selShop_l').hide().first().show();
            // первая вкладка активная
            $(elemId).find('.selShop_tab').removeClass('selShop_tab-act').first().addClass('selShop_tab-act');
            showMap($(this).closest('.orderCol'), token);
        }

        e.preventDefault();
    });

    // клик по способу доставки
    $orderContent.on('click', '.selShop_tab:not(.selShop_tab-act)', function(){
        var token = $(this).data('token'),
            id = $(this).closest('.popupFl').attr('id');
        // переключение списка магазинов
        $('.selShop_l').hide();
        $('.selShop_l[data-token='+token+']').show();
        // переключение статусов табов
        $('.selShop_tab').removeClass('selShop_tab-act');
        $('.selShop_tab[data-token='+token+']').addClass('selShop_tab-act');
        // показ карты
        showMap('#'+id, token);
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
            if ($elem.data('delivery_group_id') == 1) {
                showMap($elem.parent().siblings('.selShop').first());
            } else {
                changeDelivery($(this).closest('.orderRow').data('block_name'), $(this).data('delivery_method_token'));
            }
        }
    });

    // клик по дате в календаре
    $orderContent.on('click', '.celedr_col', function(){
        var timestamp = $(this).data('value');
        if (typeof timestamp == 'number') {
            changeDate($(this).closest('.orderRow').data('block_name'), timestamp)
        }
    });

    // клик по списку точек самовывоза
    $orderContent.on('click', '.shopLst_i', function() {
        var id = $(this).data('id'),
            token = $(this).data('token');
        if (id && token) {
            changePoint($(this).closest('.orderRow').data('block_name'), id, token)
        }
    });

    // клик по выборе даты
    $orderContent.on('click', '.customSel_def', function() {
        $(this).next('.customSel_lst').show();
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
    $orderContent.on('click', '.orderCheck.mb10', function(e){
        var $this = $(this),
            $input = $this.find('input'),
            block_name = $input.data('block_name');

        // отсекаем нативный клик по label-у и ловим jQuery trigger
        if (e.target.nodeName === 'INPUT') {
            changePaymentMethod(block_name, $input.is(':checked'))
        }
    });

    // сохранение комментария
    $orderContent.on('blur', '.orderComment_fld', function(){
        changeOrderComment($(this).val());
    });

    $orderContent.on('click', '.orderComment_t', function(){
        $('.orderComment_fld').show();
    });

    $orderContent.on('click', '.jsApplyDiscount', function(e){
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name'),
            number = $this.siblings('input').val();
        // TODO mask
        // TODO checkCertificate
        // checkCertificate();
        if (number != '') applyDiscount(block_name, number);
        e.preventDefault();
    });

    $orderContent.on('click', '.jsDeleteDiscount', function(e){
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name'),
            number = $this.data('value');
        deleteDiscount(block_name, number);
        e.preventDefault();
    })

})(jQuery);