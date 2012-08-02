$(document).ready(function() {

	window.blockScreen = function( text ) {
		$('<img src="/images/ajaxnoti.gif" />').css('display', 'none').appendTo('body') //preload
		var noti = $('<div>').addClass('noti').html('<div><img src="/images/ajaxnoti.gif" /></br></br> '+ text +'</div>')
        noti.appendTo('body')
        this.block = function() {
        	if( noti.is(':hidden') )
			noti.lightbox_me({
				centered:true,
				closeClick:false,
				closeEsc:false
			})
		}
		this.unblock = function() {
			noti.trigger('close')
		}
	}
	blockDiv = new blockScreen('Ваш заказ оформляется')
	
    Templating = {
        assign: function (el, data) {
            $.each(el.data('assign'), function(varName, callback) {
                var params = callback[1]

                if (!$.isArray(params)) {
                    params = [ params ]
                }

                $.each(params, function (i, paramName) {
                    if (paramName == '_value') {
                        params[i] = data[varName]
                    }
                })

                el[callback[0]].apply(el, params)

                if ('true' == el.attr('data-replace')) {
                    el.replaceWith(el.text())
                }
            })
        },

        clone: function(template) {
            return $(template.clone().html())
        }
    }

    DeliveryMap = {
        dataHolder: $('#order-delivery_map-data'),

        data: function() {
            if (arguments[0]) {
                this.dataHolder.data('value', arguments[0])
            }
            else {
                return this.dataHolder.data('value')
            }
        },

        getRemoteData: function(url, params, callback, async) {
            var self = this
            var async = async || false

            $('#order-submit').addClass('disable');

            $.ajax({
                type: 'POST',
                async: async,
                timeout: 60000,
                url: url,
                dataType: 'json',
                data: {
                    'delivery_type_id': params.deliveryTypeId,
                    'shop_id':          params.shopId
                },
                success: function(result) {
                    var data = result.data

                    self.data(data)

                    if ($.isFunction(callback)) {
                        callback.call(self, [data])
                    }
                },
                complete: function() {}
            })
        },

        moveItem: function(itemToken, fromDeliveryTypeToken, toDeliveryTypeToken) {
            var self = this
            var data = this.data()

            var item = data.items[itemToken]

            if ((typeof(itemToken) == 'undefined') || (-1 == $.inArray(toDeliveryTypeToken, Object.keys(item.deliveries)))) {
                //console.info(itemToken)
                return false
            }

            $.each(data.deliveryTypes[fromDeliveryTypeToken].items, function(i, token) {
                if (token == itemToken) {
                    data.deliveryTypes[fromDeliveryTypeToken].items.splice(i, 1)
                }
            })
            data.deliveryTypes[toDeliveryTypeToken].items.push(itemToken)
            self.data(data)

            return true
        },

        moveItemBlock: function(fromDeliveryTypeToken, toDeliveryTypeToken) {
            if (fromDeliveryTypeToken == toDeliveryTypeToken) {
                return []
            }

            var self = this
            var data = this.data()

            var unmoved = []

            var length = data.deliveryTypes[fromDeliveryTypeToken].items.length
            for (i = 0; i < length; i++) {
                var token = data.deliveryTypes[fromDeliveryTypeToken].items[i]
                var item = data.items[token]
                if (!array_key_exists(toDeliveryTypeToken, item.deliveries)) {
                    unmoved.push(token)
                }
                else {
                    //data.deliveryTypes[fromDeliveryTypeToken].items.splice(i, 1)
                    data.deliveryTypes[toDeliveryTypeToken].items.push(token)
                }
            }
            data.deliveryTypes[fromDeliveryTypeToken].items = unmoved

            self.data(data)

            return unmoved
        },

        getDeliveryPrice: function(deliveryType) {
            if (!deliveryType) {
                return 0
            }

            var data = this.data()

            var price = 0
            $.each(deliveryType.items, function(i, itemToken) {
                var deliveryPrice = data.items[itemToken].deliveries[deliveryType.token].price
                if (deliveryPrice > price) {
                    price = deliveryPrice
                }
            })

            return parseInt(price)
        },

        getDeliveryTotal: function(deliveryType) {
            var data = this.data()

            var total = 0
            $.each(deliveryType.items, function(i, itemToken) {
                total += data.items[itemToken].total
            })

            return parseInt(total)
        },

        getDeliveryDate: function(deliveryType) {
            var data = this.data()

            var dates = null
            $.each(deliveryType.items, function(i, itemToken) {
                var tmp = []
                $.each(data.items[itemToken].deliveries[deliveryType.token].dates, function(i, date) {
                    tmp.push(date.value)
                })

                dates = (null === dates) ? tmp : array_intersect(dates, tmp)
                //console.info(dates)
            })

            return null === dates ? [] : array_values(dates)
        },

        getDeliveryInterval: function(deliveryType, date) {
            var data = this.data()

            var intervals = {}
            $.each(deliveryType.items, function(i, itemToken) {
                $.each(data.items[itemToken].deliveries[deliveryType.token].dates, function(i, v) {
                    if (v.value == date) {
                        $.each(v.intervals, function(i, interval) {
                            intervals[interval.start_at+','+interval.end_at] = interval
                        })
                    }
                })
            })

            return intervals
        },

        render: function() {
            var self = this
            var data = this.data()

            $('#order-submit').removeClass('disable');

            $('#order-message').html('')

            // проверка на пустую корзину
            var isEmpty = true
            $.each(data.deliveryTypes, function(deliveryTypeToken, deliveryType) {
                if (deliveryType.items.length) {
                    isEmpty = false
                    return false
                }
            })
            if (isEmpty) {
                var url = $('#order-form').data('cartUrl')
                window.location = url ? url : '/'

                return false
            }

            var reload = false
            var checkItemQuantity = function() {
                var dfd = $.Deferred()

                var length = data.unavailable.length
                if (length) {
                    $.each(data.unavailable, function(i, itemToken) {
                        var item = data.items[itemToken]

                        if (!item || (0 == item.stock) || (item.type != 'product') || (item.quantity <= item.stock)) {
                            if ((i +1) == length) dfd.resolve()
                            return true
                        }

                        if (confirm('Вы заказали товар "'+item.name+'" в количестве '+item.quantity+' шт.'+"\n"+'Доступно только '+item.stock+' шт.'+"\n"+'Заказать '+item.stock+'шт?')) {
                            $.ajax({
                                url: item.deleteUrl
                            }).done(function(result) {
                                $.ajax({
                                    url: item.addUrl
                                }).done(function() {
                                    data.items[itemToken].quantity = data.items[itemToken].stock
                                    DeliveryMap.data(data)
                                    if ((i +1) == length) dfd.resolve()
                                })
                            })
                        }
                        else {
                            $.ajax({
                                url: item.deleteUrl
                            }).done(function() {
                                if ((i +1) == length) dfd.resolve()
                            })
                        }

                        reload = true
                    })

                    if (reload) {
                        $('#order-form-part2').hide()
                        $('#order-loader-holder').html('')
                        $('#order-loader').clone().appendTo('#order-loader-holder').show()
                    }
                }
                else {
                    dfd.resolve()
                }

                return dfd.promise()
            }

            $.when(checkItemQuantity()).always(function() {
                if (reload) {
                    $('#order-form-part2').hide()
                    $('#order-loader-holder').html('')
                    $('#order-loader').clone().appendTo('#order-loader-holder').show()
                    window.location.href = window.location.href+'#'+$('.bBuyingLine__eRadio:checked').attr('id')
                    window.location.reload()

                    return false
                }

                // блок для недоступных товаров
                self.renderUnavailable()

                $('.order-delivery-holder').each(function(i, deliveryTypeHolder) {
                    deliveryTypeHolder = $(deliveryTypeHolder)
                    self.renderDeliveryType(deliveryTypeHolder)

                    if (0 == data.deliveryTypes[deliveryTypeHolder.data('value')].items.length) {
                        deliveryTypeHolder.hide()
                    }
                    else {
                        deliveryTypeHolder.show()
                    }
                })

                var total = 0
                $.each(data.deliveryTypes, function(deliveryTypeToken, deliveryType) {
                    total += (self.getDeliveryTotal(deliveryType) + self.getDeliveryPrice(deliveryType))
                })

                $('.order-total-container').find('[data-assign]').each(function(i, el) {
                    Templating.assign($(el), { total: printPrice(total), totalMessage: 1 == $('.order-delivery-holder:visible').length ? 'Сумма заказа' : 'Сумма всех заказов' })
                })

                // сортировка
                var deliveryHolder = $('#order-delivery-holder')
                $.each(data.deliveryTypes, function(deliveryTypeToken, deliveryType) {
                    deliveryHolder.append($('.order-delivery-holder[data-value="'+deliveryTypeToken+'"]'))
                })

                var form = $('#order-form')
                form.find('.mRed').removeClass('mRed')
                form.find('.bFormError').remove()

                DeliveryMap.checkAddressFiled()
            })
        },

        renderDeliveryType: function(deliveryTypeHolder) {
            var self = this
            var data = this.data()
            var deliveryType = data.deliveryTypes[deliveryTypeHolder.data('value')]
            var itemHolder = deliveryTypeHolder.find('.order-item-holder')

            itemHolder.html('')

            // стоимость доставки
            var priceHolder = deliveryTypeHolder.find('.order-delivery_price')
            priceHolder.html('')
            var price = self.getDeliveryPrice(deliveryType)
            if (price > 0) {
                var priceContainer = Templating.clone($(priceHolder.data('template')))

                priceContainer.find('[data-assign]').each(function(i, el) {
                    Templating.assign($(el), { price: price })
                })
                priceContainer.appendTo(priceHolder)
            }
            else {
                priceHolder.html('Бесплатно')
            }

            // общая стоимость
            var totalHolder = deliveryTypeHolder.find('.order-delivery_total-holder')
            totalHolder.html('')
            var total = self.getDeliveryTotal(deliveryType) + self.getDeliveryPrice(deliveryType)
            var totalContainer = Templating.clone($(totalHolder.data('template')))

            totalContainer.find('[data-assign]').each(function(i, el) {
                Templating.assign($(el), { total: printPrice(total), name: 'Итого' + ('self' != deliveryType.type ? ' с доставкой' : '') })
            })
            totalContainer.appendTo(totalHolder)

            // доступность дат
            var dates = self.getDeliveryDate(deliveryType)
            $.each(deliveryTypeHolder.find('.order-delivery_date'), function(i, el) {
                var el = $(el)
                var value = el.data('value')
                var exists = -1 !== $.inArray(value, dates)

                el.removeClass('bBuyingDates__eDisable').removeClass('bBuyingDates__eEnable').removeClass('bBuyingDates__eCurrent')
                if (deliveryType.date == value)
                {
                    el.addClass('bBuyingDates__eCurrent')
                }
                if (exists) {
                    el.addClass('bBuyingDates__eEnable')
                }
                else {
                    el.addClass('bBuyingDates__eDisable')
                }
            })

            $.each(deliveryType.items, function(i, itemToken) {
                self.renderItem(itemHolder, data.items[itemToken])
            })

            deliveryTypeHolder.find('h2 [data-assign]').each(function(i, el) {
                Templating.assign($(el), { displayDate: deliveryTypeHolder.find('.order-delivery_date[data-value="'+deliveryType.date+'"]').data('displayValue') })
            })

            if (true || deliveryType.date) {
                deliveryTypeHolder.find('.order-delivery_date').removeClass('bBuyingDates__eCurrent')
                deliveryTypeHolder.find('.order-delivery_date[data-value="'+deliveryType.date+'"]').addClass('bBuyingDates__eCurrent')
            }

            // интервалы
            var intervals = DeliveryMap.getDeliveryInterval(deliveryType, deliveryType.date)
            var intervalHolder = $(deliveryTypeHolder.find('[data-interval-holder]').data('intervalHolder'))
            var intervalElementTemplate = Templating.clone($(intervalHolder.data('template')))

            intervalHolder.html('')

            $.each(intervals, function(i, interval) {
                intervalElement = intervalElementTemplate.clone()

                var value = interval.start_at+','+interval.end_at
                var displayValue = 'с '+interval.start_at+' по '+ interval.end_at
                $.each(intervalElement.find('[data-assign]'), function(i, el) {
                    Templating.assign($(el), { name: displayValue, value: value, date: deliveryType.date, deliveryType: deliveryType.token })
                })

                intervalElement.appendTo(intervalHolder)
            })

            if (!deliveryTypeHolder.find('.bSelect [data-event="onSelect"]').text())
            {
                var interval = data.deliveryTypes[deliveryType.token].interval
                if (interval) {
                    deliveryTypeHolder.find('.bSelect [data-event="onSelect"]').text('с '+interval.split(',')[0]+' по '+interval.split(',')[1])
                }
            }

            // активность кнопки "Другой магазин"
            var button = deliveryTypeHolder.find('.order-shop-button');
            if (button.length) {
               if (1 == data.deliveryTypes[deliveryType.token].items.length) {
                   var shopQuantity = 0
                   var item = data.items[data.deliveryTypes[deliveryType.token].items[0]]
                   $.each(item.deliveries, function(k, v) {
                       if (0 == k.indexOf('self_')) {
                           shopQuantity++
                       }
                   })

                   if (1 == shopQuantity) {
                       button.replaceWith('<span class="red" style="font: 12px Tahoma,sans-serif"><br />доступен только в этом магазине</span>')
                   }
               }
            }
        },

        renderItem: function(itemHolder, data) {
            var self = this

            data.totalFormatted = printPrice(data.total)

            var template = $(itemHolder.data('template'))
            var itemContainer = Templating.clone(template)

            itemContainer.find('[data-assign]').each(function(i, el) {
                Templating.assign($(el), data)
            })

            if (true || (Object.keys(data.deliveries).length <= 1)) {
                itemContainer.find('.order-item_delivery-button').remove()
            }

            itemHolder.append(itemContainer)
        },

        renderUnavailable: function() {
            var data = this.data()
            var unavailableContainer = $('#order-unavailable')

            unavailableContainer.find('.order-item-holder').html('')

            if (data.unavailable.length) {
                $.each(data.unavailable, function(i, itemToken) {
                    var item = data.items[itemToken]
                    if (!item) return false
                    var itemContainer = Templating.clone($('#order-item-template'))

                    item.totalFormatted = printPrice(item.total)

                    itemContainer.find('[data-assign]').each(function(i, el) {
                        Templating.assign($(el), item)
                    })

                    unavailableContainer.find('.order-item-holder').append(itemContainer)
                })

                unavailableContainer.show('fast')
            }
            else {
                unavailableContainer.hide('fast')
            }
        },

        getUndeliveredItem: function(deliveryTypeId) {
            var data = this.data()

            var currentDeliveryType = {}

            var allItems = Object.keys(data.items)
            var items = []
            $.each(data.deliveryTypes, function(token, deliveryType) {
                if (deliveryType.id == deliveryTypeId) {
                    currentDeliveryType = deliveryType
                    return false
                }
            })

            $.each(data.deliveryTypes, function(token, deliveryType) {
                if (((currentDeliveryType.type == 'standart') && (deliveryType.type == 'standart')) || (deliveryType.token == currentDeliveryType.token)) {
                    $.each(deliveryType.items, function (i, item) {
                        items.push(item)
                    })
                }
            })

            return array_values(array_diff(allItems, items))
        },

        renderUndeliveredMessage: function(deliveryTypeId) {
            var undeliveredItems = this.getUndeliveredItem(deliveryTypeId)
            if (undeliveredItems.length) {
                var message = 'Некоторые товары не могут быть получены выбранным способом доставки.'

                if (1 == undeliveredItems.length) {
                    if ($('.bBuyingLine__eRadio[data-delivery-type="self"]:checked')) {
                        var message = 'Товара нет в наличии в выбранном магазине.'

                        var itemId = undeliveredItems.shift()
                        var shopQuantity = 0
                        var item = DeliveryMap.data().items[itemId]
                        $.each(item.deliveries, function(k, v) {
                            if (0 == k.indexOf('self_')) {
                                shopQuantity++
                            }
                        })
                        if (1 == shopQuantity) {
                            var message = item.name + ' есть в наличии только в одном магазине.'
                        }
                    }
                    else {
                        var message = 'Невозможно доставить товар.'
                    }
                }
                $('#order-message').html('<span class="red">'+message+'</span>')
            }
            else {
                $('#order-message').html('<span>Отличный выбор!</span>')
            }
        },

        openShopMap: function(deliveryToken) {
            this.onShopSelected =
                !deliveryToken
                ? function(deliveryTypeId, shopId) {
                    $('#order-form-part2').hide()
                    $('#order-loader').clone().appendTo('#order-loader-holder').show()

                    var url = $('#order-form').data('deliveryMapUrl')
                    DeliveryMap.getRemoteData(url, { deliveryTypeId: deliveryTypeId, shopId: shopId }, function(data) {

                        $('#order-loader-holder').html('')
                        $('#order-form-part2').show('fast')

                        this.render()

                        DeliveryMap.renderUndeliveredMessage(deliveryTypeId)
                        DeliveryMap.onDeliveryBlockChange()
                    }, true)
                }
                : function(deliveryTypeId, shopId) {
                    var data = DeliveryMap.data()

                    var toDeliveryToken = 'self_'+shopId

                    var unmoved = DeliveryMap.moveItemBlock(deliveryToken, toDeliveryToken)
                    /*
                    var unmoved = []
                    $.each(data.deliveryTypes[deliveryToken].items, function(i, itemToken) {
                        if (false == DeliveryMap.moveItem(itemToken, deliveryToken, toDeliveryToken)) {
                            unmoved.push(itemToken)
                        }
                    })
                    */

                    if (!data.deliveryTypes[toDeliveryToken].date) {
                        var data = DeliveryMap.data()
                        data.deliveryTypes[toDeliveryToken].date = DeliveryMap.getDeliveryDate(data.deliveryTypes[toDeliveryToken]).shift()
                        data.deliveryTypes[toDeliveryToken].displayDate = $('.order-delivery-holder[data-value="'+toDeliveryToken+'"]').find('.order-delivery_date[data-value="'+data.deliveryTypes[toDeliveryToken].date+'"]').data('displayValue')
                        DeliveryMap.data(data)
                    }

                    DeliveryMap.render()

                    if (unmoved.length) {
                        $('.order-delivery-holder[data-value="'+deliveryToken+'"]').find('.delivery-message').html('<div class="red">Невозможно переместить товары</div>')
                        $('.order-delivery-holder[data-value="'+deliveryToken+'"]').effect('pulsate', { times: 2 }, 1000, function() {
                            $(this).find('.delivery-message').html('')
                        })
                    }

                    DeliveryMap.onDeliveryBlockChange()
                }

            openMap()
        },

        onShopSelected: function() {},

        onMapClosed: function(shopId) {
           
            var el = $('.bBuyingLine__eRadio:checked')
 
            regionMap.closePopupMap( function() { $('.mMapPopup').trigger('close') } )
            if( typeof(shopId) === 'object' )
                shopId = shopId.id
            DeliveryMap.onShopSelected.apply(DeliveryMap, [el.val(), shopId])
            //DeliveryMap.onShopSelected.apply(this, [el.val(), shopId])
            //console.info('onMapClosed', el.val(), shopId)
        },

        validate: function(el, message) {
            //console.info(el);
            var form = $('#order-form')
            var isValid = true

            if ((-1 !== $.inArray(el.attr('id'), ['order_address_street', 'order_address_number'])) && !$('.bBuyingLine__eRadio[data-delivery-type="standart"]:checked').length) {
                //console.info(1);
                return true
            }

            if ((-1 !== $.inArray(el.attr('id'), ['order_address_street', 'order_address_number'])) && !el.val()) {
                //console.info(2);
                isValid = false
                if (!$('#addressField').find('dd .bFormError').length) {
                    showError($('#addressField').find('dd > div:first'), message, true)
                }
            }
            // если группа радио и не выбрано ни одного
            else if (el.is(':radio') && !el.is(':checked') && (el.length > 1)) {
                //console.info(3);
                isValid = false
                showError(el.first().parent().parent(), message, false)
            }
            // если чекбокс и не выбран
            else if (el.is(':checkbox') && !el.is(':checked') && (el.length == 1)) {
                //console.info(4);
                isValid = false
                showError(el.first().parent().parent(), message, false)
            }
            else if (el.is(':text') && !el.val()) {
                //console.info(5);
                isValid = false
                showError(el, message, true)
            }

            return isValid
        },

        onDeliveryBlockChange: function() {
            //console.info('onDeliveryBlockChange')
            if (1 == $('.order-delivery-holder:visible').length) {
                $('#payment_method_online-field').show()
            }
            else {
                $('#payment_method_online-field').hide()
                $('#payment_method_online-field').find('input').attr('checked', false)
                $('#payment_method_online-field').find('.mChecked').removeClass('mChecked')
            }
        },

        checkAddressFiled: function() {
            if ($('.order-delivery-holder[data-type="standart"]:visible').length > 0) {
                $('#addressField').show()
            }
            else {
                $('#addressField').hide()
            }
        }
    }

/* <! -- MAP REDESIGN */
    var shopList      = $('#mapPopup_shopInfo'),
        infoBlockNode = $('#map-info_window-container'),
        shopsStack    = $('#order-delivery_map-data').data().value.shops

    function renderShopInfo (marker) {
        var tpl = tmpl( 'elementInShopList', marker)
        shopList.append(tpl)
    }

    for( var i in shopsStack )
        renderShopInfo( shopsStack[i] )
    
    function openMap() {
        $('.mMapPopup').lightbox_me({
            centered: true,
            onLoad: function() {
                window.regionMap.showMarkers( shopsStack )
            }
        })
    }

    shopList.delegate('li', 'click', function() {
        DeliveryMap.onMapClosed( $(this).data('id') )
    })

    var hoverTimer = { 'timer': null, 'id': 0 }

    shopList.delegate('li', 'hover', function() {
        
        var id = $(this).data('id')
        if( hoverTimer.timer ) {
            clearTimeout( hoverTimer.timer )
        }
        
        if( id && id != hoverTimer.id) {
            hoverTimer.id = id
            hoverTimer.timer = setTimeout( function() {            
                window.regionMap.showInfobox( id )
            }, 500)
        }
    })

    function updateI( marker ) {
        infoBlockNode.html( tmpl( 'mapInfoBlock', marker ))
        hoverTimer.id = marker.id   
    }

    function ShopChoosed( node ) {
        var shopnum = $(node).parent().find('.shopnum').text()
        DeliveryMap.onMapClosed( shopnum )
    }

    window.regionMap = new MapWithShops(
        calcMCenter( shopsStack ),
        infoBlockNode,
        'mapPopup',
        updateI
    )

    window.regionMap.addHandler( '.shopchoose', ShopChoosed )

    window.regionMap.addHandlerMarker( 'mouseover', function( marker ) {        
        window.regionMap.showInfobox( marker.id )
    })

/* MAP REDESIGN --> */

    $('#order-loader-holder').html('')

    $('#order-form-part1').show()

    $('body').delegate('.mBacket', 'click', function(e) {
        e.preventDefault()

        var el = $(this)
        var itemToken = el.data('token')
        var onSuccess = function() {
            $(this).remove()

            var data = DeliveryMap.data()
            $.each(data['deliveryTypes'], function(i, deliveryType) {
                $.each(deliveryType.items, function(ii, token) {
                    if (token == itemToken) {
                        data.deliveryTypes[deliveryType.token].items.splice(ii, 1)
                        delete data.items[token]

                    }
                })
            })

            var i = $.inArray(itemToken, data.unavailable)
            if (-1 !== i) {
                data.unavailable.splice(i, 1)
            }


            DeliveryMap.data(data)
            DeliveryMap.render()
            DeliveryMap.onDeliveryBlockChange()
        }

        $.ajax({
            async: false,
            url: el.attr('href'),
            success: function(result) {
                el.closest('.order-item-container').hide('medium', onSuccess)
            }
        })



    })

    $('body').delegate('.bBuyingLine label', 'click', function(e) {
        var target = $(e.target)
        if (!target.is('input')) {
            return
        }

        var hadPicked = $(this).hasClass('mChecked')

        if( $(this).find('input').attr('type') == 'radio' ) {
            var thatName = $('.mChecked input[name="'+$(this).find('input').attr('name')+'"]')
            if( thatName.length ) {
                thatName.each( function(i, item) {
                    $(item).parent('label').removeClass('mChecked')
                })
            }
            $(this).addClass('mChecked')
        }

        if( $(this).find('input').attr('type') == 'checkbox' ) {
            $(this).toggleClass('mChecked')
        }
        if( hadPicked ) 
            return
        var el = $(this).find('input[type="radio"][data-delivery-type]')
        if (!el.length) {
            return
        }

        $('#order-submit').addClass('disable');

        var url = $('#order-form').data('deliveryMapUrl')

        $('#order-form-part2').hide()
        $('.order-shop-button:first').hide()

        if ('self' == el.data('deliveryType')) {
            var shops = DeliveryMap.data().shops
            if (1 == Object.keys(shops).length) {
                var shopId = Object.keys(shops).shift()
                var deliveryTypeId = el.val()

                $('#order-form-part2').hide()
                $('#order-loader').clone().appendTo('#order-loader-holder').show()

                var url = $('#order-form').data('deliveryMapUrl')
                DeliveryMap.getRemoteData(url, { deliveryTypeId: deliveryTypeId, shopId: shopId }, function(data) {

                    $('#order-loader-holder').html('')
                    $('#order-form-part2').show('fast')

                    this.render()

                    DeliveryMap.renderUndeliveredMessage(deliveryTypeId)
                }, true)
            }
            else {
                $('.order-shop-button:first').show()
            }

            //$('#addressField').hide()
        }
        else {
            $('#order-loader').clone().appendTo('#order-loader-holder').show()

            //$('#addressField').show()

            DeliveryMap.getRemoteData(url, { deliveryTypeId: el.val() }, function(data) {
                $('#order-loader-holder').html('')
                $('#order-form-part2').show('fast')

                this.render()
            })


            DeliveryMap.renderUndeliveredMessage(el.val())
        }

        DeliveryMap.onDeliveryBlockChange()
    })

    $('body').delegate('.order-shop-button', 'click', function(e) {
        e.preventDefault()

        DeliveryMap.openShopMap($(this).data('delivery'))
    })

    $('body').delegate('.order-item_delivery-button', 'click', function(e) {
        e.preventDefault()

        var data = DeliveryMap.data()
        var el = $(this)
        var popup = Templating.clone($(el.data('template')))
        var item = data.items[el.data('value')]

        var fromDeliveryTypeToken = null
        $.each(data.deliveryTypes, function(deliveryTypeToken, deliveryType) {
            $.each(deliveryType.items, function(i, itemToken) {
                if (itemToken == item.token) {
                    fromDeliveryTypeToken = deliveryTypeToken
                }
            })
        })

        var deliveryTemplate = popup.find('a')
        $.each(item.deliveries, function(deliveryToken, delivery) {
            if (delivery.token == fromDeliveryTypeToken) return

            var deliveryEl = deliveryTemplate.clone()
            var date = DeliveryMap.data().deliveryTypes[delivery.token].date
            var data = {
                name: delivery.name + (date ? (' ' + date) : ''),
                route: JSON.stringify({ item: item.token, from: fromDeliveryTypeToken, to: delivery.token })
            }
            Templating.assign(deliveryEl, data)
            deliveryEl.insertAfter(deliveryTemplate)
        })
        deliveryTemplate.remove()

        popup.appendTo(el.parent())
        popup.bind({
            mouseleave: function() { $(this).remove() },
            click: function(e) {
                el = $(e.target)
                if (el.is('a')) {
                    var route = el.data('value')
                    DeliveryMap.moveItem(route.item, route.from, route.to)
                    $(this).remove()

                    // проверка на пустую дату
                    var data = DeliveryMap.data()
                    if (!data.deliveryTypes[route.to].date) {
                        data.deliveryTypes[route.to].date = data.items[route.item].deliveries[route.to].dates[0].value
                        DeliveryMap.data(data)
                    }

                    DeliveryMap.render()
                }
            }
        })
        popup.show()
    })

    $('body').delegate('.order-delivery_date-control', 'click', function() {
        var el = $(this)

        if ($(el).hasClass('mDisabled')) {
            return
        }

        var parent = el.parent()
        var weekNum = parseInt(el.data('value'))

        if (parent.find('.order-delivery_date[data-week="'+weekNum+'"]').length) {
            parent.find('.order-delivery_date').hide()
            parent.find('.order-delivery_date[data-week="'+weekNum+'"]').show()
        }


        var prevEl = parent.find('.order-delivery_date-control[data-direction="prev"]')
        if (parent.find('.order-delivery_date[data-week="'+(weekNum - 1)+'"]').length) {
            prevEl.data('value', weekNum - 1)
            prevEl.removeClass('mDisabled')
        }
        else {
            prevEl.addClass('mDisabled')
        }

        var nextEl = parent.find('.order-delivery_date-control[data-direction="next"]')
        if (parent.find('.order-delivery_date[data-week="'+(weekNum + 1)+'"]').length) {
            nextEl.data('value', weekNum + 1)
            nextEl.removeClass('mDisabled')
        }
        else {
            nextEl.addClass('mDisabled')
        }
    })

    $('body').delegate('.order-delivery_date', 'click', function(e) {
        var el = $(this)

        if (el.hasClass('bBuyingDates__eDisable')) {
            return
        }

        var deliveryTypeHolder = el.closest('.order-delivery-holder')
        var deliveryTypeToken = el.data('value')
        var displayDate = el.data('displayValue')

        var date = el.data('value')
        var displayDate = el.data('displayValue')
        var deliveryTypeHolder = el.closest('.order-delivery-holder')
        var deliveryTypeToken = deliveryTypeHolder.data('value')
        var deliveryType = DeliveryMap.data()['deliveryTypes'][deliveryTypeToken]

        el.parent().find('.order-delivery_date')
            .removeClass('bBuyingDates__eCurrent')
            .addClass('bBuyingDates__eEnable')

        el.removeClass('bBuyingDates__eEnable').addClass('bBuyingDates__eCurrent')

        deliveryTypeHolder.find('h2 [data-assign]').each(function(i, el) {
            Templating.assign($(el), { displayDate: displayDate })
        })


        var data = DeliveryMap.data()
        data.deliveryTypes[deliveryTypeToken].date = date
        DeliveryMap.data(data)
    })

    $('body').delegate( '.bSelect', 'click', function() {
        if( $(this).hasClass('mDisabled') )
            return false
        $(this).find('.bSelect__eDropmenu').toggle()
    })
    $('body').delegate( '.bSelect', 'mouseleave', function() {
        if( $(this).hasClass('mDisabled') )
            return false
        var options = $(this).find('.bSelect__eDropmenu')
        if( options.is(':visible') )
            options.hide()
    })
    $('body').delegate('.order-interval', 'click', function() {
        var el = $(this)
        var data = DeliveryMap.data()

        el.closest('.bSelect').find('[data-event="onSelect"]').text(el.text())
        var elData = el.find('[data-value]').data()

        data.deliveryTypes[elData.deliveryType].interval = elData.value
    })

    if ($('.bBuyingLine__eRadio:checked').length) {
        DeliveryMap.render()
        $('#order-form-part2').show('fast')

        DeliveryMap.checkAddressFiled()
    }
    else if (window.location.hash) {
        $(window.location.hash).click()
    }

    $('#order-submit').data('locked', false).data('text', $('#order-submit').text()).click(function(e) {
        e.preventDefault()

        var button = $(this)
        var form = button.closest('form')
        var validator = $(form.data('validator')).data('value')

        if (button.data('locked') || button.hasClass('disable')) {
            return false
        }

        form.find('.mRed').removeClass('mRed')
        form.find('.bFormError').remove()

        var hasError = false
        $.each(validator, function(field, message) {
            var el = form.find('[name="'+field+'"]:visible')
            if (el.length && !DeliveryMap.validate(el, message)) {
                hasError = true
            }
        })
		
        if (hasError) {
            $.scrollTo('.bFormError:first', 300)
        }
        else {
            button.text('Оформляю заказ...')
            blockDiv.block()

            var data = form.serializeArray()
            data.push({ name: 'delivery_map', value: JSON.stringify(DeliveryMap.data()) })

            button.data('locked', true)

            $.ajax({
                url: form.attr('action'),
                timeout: 60000,
                async: true,
                type: 'POST',
                data: data,
                success: function(result) {
                    var form = $('#order-form')

                    if (result.success) {
                        button.text('Готово!')
                        button.attr('disabled', true)
                        window.location = result.data.redirect
                    }
                    else if (result.error) {
                        if ('invalid' == result.error.code) {

                            form.find('.mRed').removeClass('mRed')
                            form.find('.bFormError').remove()

                            $('#order-message').html('<span class="red">'+result.error.message+'</span>')
                            $.each(result.errors, function(k, v) {
                                var el = form.find('[name="'+k+'"]:visible')

                                showError(el, v, true)
                            })
                            button.text('Завершить оформление')
                            blockDiv.unblock()
                        }
                        else {
                            alert(result.error.message);
                            window.location.reload();
                        }
                    }
                },
                error: function() {
                    button.text(button.data('text'))
                    blockDiv.unblock()
                },
                complete: function() {
                    button.data('locked', false)
                }
            })
        }
    })

	PubSub.subscribe( 'authorize', function( m, d ) {
		$('#order_recipient_first_name').val( d.first_name )
		$('#order_recipient_last_name').val( d.last_name )
		$('#order_recipient_phonenumbers').val( d.phonenumber + '       ' )
		$('#user-block').hide()
	})

    $('.auth-link').bind('click', function (e) {
        e.preventDefault()

        var link = $(this)

        $('#login-form, #register-form').data('redirect', false)
        $('#auth-block').lightbox_me({
            centered:true,
            onLoad:function () {
                $('#auth-block').find('input:first').focus()
            }
            /*,
            onClose:function () {
                $.get(link.data('updateUrl'), function (response) {
                    if (true === response.success) {
                        var form = $('.order-form')
                        $('#user-block').replaceWith(response.data.content)

                        $.each(response.data.fields, function (name, value) {
                            var field = form.find('[name="' + name + '"]')
                            if (field.val().length < 2) {
                                field.val(value)
                            }
                        })
                    }
                })
            }
            */
        })
    })

    if( typeof( $.mask ) !== 'undefined' ) {
		$.mask.definitions['n'] = "[()0-9\ \-]"
		$("#order_recipient_phonenumbers").mask("8nnnnnnnnnnnnnnnnn", { placeholder: " ", maxlength: 10 } )
        $("#order_recipient_phonenumbers").val('8')
        
        $.mask.definitions['*'] = "[0-9*]"
        $("#order_sclub_card_number").mask("* ****** ******", { placeholder: "*" } )
		if( $("#order_sclub_card_number")[0].getAttribute('value') )
			$("#order_sclub_card_number").val( $("#order_sclub_card_number")[0].getAttribute('value') )
		$("#order_sclub_card_number").blur( function() {
			if( $(this).val() === "* ****** ******" ) {
				$(this).trigger('unmask').val('')
				$(this).focus( function() {
					$("#order_sclub_card_number").mask("* ****** ******", { placeholder: "*" } )
				})
			}
        })	
	}
	
	//$('#addressField').find('input').placeholder()

    $('.placeholder-input').focus(function(e) {
        var el = $(e.target)
        el.prev('.placeholder').css('border-color', '#FFA901');
    }).focusout(function(e) {
        var el = $(e.target)
        el.prev('.placeholder').css('border-color', '#DDDDDD')
    })

    $('.placeholder').click(function(e) {
        $(this).next('.placeholder-input').focus();
    })
	
	var ubahn = [ 'Авиамоторная', 'Автозаводская','Академическая','Александровский сад','Алексеевская','Алтуфьево','Аннино','Арбатская (Арбатско-Покровская линия)','Арбатская (Филевская линия','Аэропорт',
'Бабушкинская','Багратионовская','Баррикадная','Бауманская','Беговая','Белорусская','Беляево','Бибирево','Библиотека имени Ленина','Битцевский парк','Борисовская',
'Боровицкая','Ботанический сад','Братиславская','Бульвар адмирала Ушакова','Бульвар Дмитрия Донского','Бунинская аллея','Варшавская',
'ВДНХ','Владыкино','Водный стадион','Войковская','Волгоградский проспект','Волжская','Волоколамская','Воробьевы горы','Выставочная','Выхино','Деловой центр',
'Динамо','Дмитровская','Добрынинская','Домодедовская','Достоевская','Дубровка','Жулебино','Зябликово','Измайловская','Калужская','Кантемировская','Каховская','Каширская','Киевская',
'Китай-город','Кожуховская','Коломенская','Комсомольская','Коньково','Красногвардейская','Краснопресненская','Красносельская','Красные ворота','Крестьянская застава',
'Кропоткинская','Крылатское','Кузнецкий мост','Кузьминки','Кунцевская','Курская','Кутузовская','Ленинский проспект','Лубянка',
'Люблино','Марксистская','Марьина роща','Марьино','Маяковская','Медведково','Международная','Менделеевская','Митино',
'Молодежная','Мякинино','Нагатинская','Нагорная','Нахимовский проспект','Новогиреево','Новокузнецкая','Новослободская','Новоясеневская','Новые Черемушки','Октябрьская',
'Октябрьское поле','Орехово','Отрадное','Охотныйряд','Павелецкая','Парк культуры','Парк Победы','Партизанская',
'Первомайская','Перово','Петровско-Разумовская','Печатники','Пионерская','Планерная','Площадь Ильича','Площадь Революции','Полежаевская',
'Полянка','Пражская','Преображенская площадь','Пролетарская','Проспект Вернадского','Проспект Мира','Профсоюзная','Пушкинская',
'Речной вокзал','Рижская','Римская','Рязанский проспект','Савеловская','Свиблово','Севастопольская','Семеновская','Серпуховская',
'Славянский бульвар','Смоленская (Арбатско-Покровская линия)','Смоленская (Филевская линия)','Сокол','Сокольники','Спортивная',
'Сретенский бульвар','Строгино','Студенческая','Сухаревская','Сходненская','Таганская','Тверская','Театральная','Текстильщики','ТеплыйСтан','Тимирязевская',
'Третьяковская','Трубная','Тульская','Тургеневская','Тушинская','Улица 1905года','Улица Академика Янгеля','Улица Горчакова','Улица Подбельского','Улица Скобелевская','Улица Старокачаловская','Университет','Филевский парк','Фили',
'Фрунзенская','Царицыно','Цветной бульвар','Черкизовская','Чертановская','Чеховская','Чистые пруды','Чкаловская','Шаболовская','Шипиловская',
'Шоссе Энтузиастов','Щелковская','Щукинская','Электрозаводская','Юго-Западная','Южная','Ясенево'
		]
	$( "#order_address_metro" )
		.autocomplete({
			source: ubahn,
			appendTo: '#metrostations',
			minLength: 2
		})
		.change( function() {
			for(var i=0, l= ubahn.length; i<l; i++)
				if( $(this).val() === ubahn[i] )
					return true
			$(this).val('')
		})
})


function showError(el, message, border) {
    if (border) {
        el.addClass('mRed')
    }
    el.before( '<span class="bFormError mb10 pt5">'+message+'</span>' )
}


function array_intersect(arr1) {
    // Returns the entries of arr1 that have values which are present in all the other arguments
    //
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/array_intersect    // +   original by: Brett Zamir (http://brett-zamir.me)
    // %        note 1: These only output associative arrays (would need to be
    // %        note 1: all numeric and counting from zero to be numeric)
    // *     example 1: $array1 = {'a' : 'green', 0:'red', 1: 'blue'};
    // *     example 1: $array2 = {'b' : 'green', 0:'yellow', 1:'red'};    // *     example 1: $array3 = ['green', 'red'];
    // *     example 1: $result = array_intersect($array1, $array2, $array3);
    // *     returns 1: {0: 'red', a: 'green'}
    var retArr = {},
        argl = arguments.length,        arglm1 = argl - 1,
        k1 = '',
        arr = {},
        i = 0,
        k = '';
    arr1keys: for (k1 in arr1) {
        arrs: for (i = 1; i < argl; i++) {
            arr = arguments[i];
            for (k in arr) {                if (arr[k] === arr1[k1]) {
                if (i === arglm1) {
                    retArr[k1] = arr1[k1];
                }
                // If the innermost loop always leads at least once to an equal value, continue the loop until done                    continue arrs;
            }
            }
            // If it reaches here, it wasn't found in at least one array, so try next value
            continue arr1keys;        }
    }

    return retArr;
}

function array_diff (arr1) {
    // Returns the entries of arr1 that have values which are not present in any of the others arguments.
    //
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/array_diff    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Sanjoy Roy
    // +    revised by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: array_diff(['Kevin', 'van', 'Zonneveld'], ['van', 'Zonneveld']);
    // *     returns 1: {0:'Kevin'}
    var retArr = {},
    argl = arguments.length,
        k1 = '',
        i = 1,
        k = '',
        arr = {};

    arr1keys: for (k1 in arr1) {
        for (i = 1; i < argl; i++) {
            arr = arguments[i];
            for (k in arr) {
                if (arr[k] === arr1[k1]) {
                    // If it reaches here, it was found in at least one array, so try next value
                    continue arr1keys;
                }            }
            retArr[k1] = arr1[k1];
        }
    }
    return retArr;
}

function array_values (input) {
    // Return just the values from the input array
    //
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/array_values    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      improved by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: array_values( {firstname: 'Kevin', surname: 'van Zonneveld'} );
    // *     returns 1: {0: 'Kevin', 1: 'van Zonneveld'}
    var tmp_arr = [],        key = '';

    if (input && typeof input === 'object' && input.change_key_case) { // Duck-type check for our own array()-created PHPJS_Array
        return input.values();
    }
    for (key in input) {
        tmp_arr[tmp_arr.length] = input[key];
    }
    return tmp_arr;
}

function array_key_exists (key, search) {
    // Checks if the given key or index exists in the array
    //
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/array_key_exists    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Felix Geisendoerfer (http://www.debuggable.com/felix)
    // *     example 1: array_key_exists('kevin', {'kevin': 'van Zonneveld'});
    // *     returns 1: true
    // input sanitation
    if (!search || (search.constructor !== Array && search.constructor !== Object)) {
        return false;
    }

    return key in search;
}

/* Object.keys for IE */
if (!Object.keys) {
  Object.keys = (function () {
    var hasOwnProperty = Object.prototype.hasOwnProperty,
        hasDontEnumBug = !({toString: null}).propertyIsEnumerable('toString'),
        dontEnums = [
          'toString',
          'toLocaleString',
          'valueOf',
          'hasOwnProperty',
          'isPrototypeOf',
          'propertyIsEnumerable',
          'constructor'
        ],
        dontEnumsLength = dontEnums.length

    return function (obj) {
      if (typeof obj !== 'object' && typeof obj !== 'function' || obj === null) throw new TypeError('Object.keys called on non-object')

      var result = []

      for (var prop in obj) {
        if (hasOwnProperty.call(obj, prop)) result.push(prop)
      }

      if (hasDontEnumBug) {
        for (var i=0; i < dontEnumsLength; i++) {
          if (hasOwnProperty.call(obj, dontEnums[i])) result.push(dontEnums[i])
        }
      }
      return result
    }
  })()
};