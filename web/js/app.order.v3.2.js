$(document).ready(function() {

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

                if (el.is('data')) {
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

        getDeliveryPrice: function(deliveryType) {
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
                Templating.assign($(el), { total: printPrice(total) })
            })

            // сортировка
            var deliveryHolder = $('#order-delivery-holder')
            $.each(data.deliveryTypes, function(deliveryTypeToken, deliveryType) {
                deliveryHolder.append($('.order-delivery-holder[data-value="'+deliveryTypeToken+'"]'))
            })

            var form = $('#order-form')
            form.find('.mRed').removeClass('mRed')
            form.find('.bFormError').remove()
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
                Templating.assign($(el), { displayDate: deliveryType.displayDate })
            })
        },

        renderItem: function(itemHolder, data) {
            var self = this

            data.totalFormatted = printPrice(data.total)

            var template = $(itemHolder.data('template'))
            var itemContainer = Templating.clone(template)

            itemContainer.find('[data-assign]').each(function(i, el) {
                Templating.assign($(el), data)
            })

            if (true || Object.keys(data.deliveries).length <= 1) {
                itemContainer.find('.order-item_delivery-button').remove()
            }

            itemHolder.append(itemContainer)
        },

        renderUnavailable: function() {
            var data = this.data()
            var unavailableContainer = $('#order-unavailable')

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

            var allItems = Object.keys(data.items)
            var items = []
            $.each(data.deliveryTypes, function(deliveryTypeToken, deliveryType) {
                if (deliveryType.id == deliveryTypeId) {
                    items = deliveryType.items
                    return false
                }
            })

            return array_values(array_diff(allItems, items))
        },

        renderUndeliveredMessage: function(deliveryTypeId) {
            var undeliveredItems = this.getUndeliveredItem(deliveryTypeId)
            if (undeliveredItems.length) {
                $('#order-message').html('<span class="red">Некоторые товары не могут быть получены выбранным способом доставки.</span>')
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
                        this.render()

                        $('#order-loader-holder').html('')
                        $('#order-form-part2').show('fast')

                        DeliveryMap.renderUndeliveredMessage(deliveryTypeId)
                    }, true)
                }
                : function(deliveryTypeId, shopId) {
                    var data = DeliveryMap.data()

                    var toDeliveryToken = 'self_'+shopId

                    var unmoved = []
                    $.each(data.deliveryTypes[deliveryToken].items, function(i, itemToken) {
                        if (false == DeliveryMap.moveItem(itemToken, deliveryToken, toDeliveryToken)) {
                            unmoved.push(itemToken)
                        }
                    })

                    if (!data.deliveryTypes[toDeliveryToken].date) {
                        var data = DeliveryMap.data()
                        data.deliveryTypes[toDeliveryToken].date = DeliveryMap.getDeliveryDate(data.deliveryTypes[toDeliveryToken]).shift()
                        data.deliveryTypes[toDeliveryToken].displayDate = $('.order-delivery-holder[data-value="'+toDeliveryToken+'"]').find('.order-delivery_date[data-value="'+data.deliveryTypes[toDeliveryToken].date+'"]').data('displayValue')
                        DeliveryMap.data(data)
                    }

                    DeliveryMap.render()

                    if (unmoved.length) {
                        $('.order-delivery-holder[data-value="'+deliveryToken+'"]').effect('pulsate', {}, 500)
                        //console.info(unmoved)
                    }
                }

            regionMap.openMap()
        },

        onShopSelected: function() {},

        onMapClosed: function(shopId) {
            var el = $('.bBuyingLine__eRadio:checked')

            regionMap.closeMap()

            DeliveryMap.onShopSelected.apply(this, [el.val(), shopId])
        },

        validate: function(el, message) {
            var form = $('#order-form')
            var hasError = false

            // если группа радио и не выбрано ни одного
            if (el.is(':radio') && !el.is(':checked') && (el.length > 1)) {
                hasError = true
                showError(el.first().parent().parent(), message, false)
            }
            // если чекбокс и не выбран
            else if (el.is(':checkbox') && !el.is(':checked') && (el.length == 1)) {
                hasError = true
                showError(el.first().parent().parent(), message, false)
            }
            else if (el.is(':text') && !el.val()) {
                hasError = true
                showError(el, message, true)
            }

            return hasError
        }
    }

    window.regionMap = new MapWithShops(
        $('#map-center').data('content'),
        $('#map-info_window-container'),
        'mapPopup',
        DeliveryMap.onMapClosed
    )



    $('#order-loader-holder').html('')

    $('#order-form-part1').show()

    $('body').delegate('.bImgButton.mBacket', 'click', function(e) {
        e.preventDefault()

        if (!confirm('Удалить выбранный товар из корзины?')) {
           return
        }

        var el = $(this)
        var itemToken = el.data('token')

        $.ajax({
            async: false,
            url: el.attr('href'),
            success: function(result) {
                el.closest('.order-item-container').hide('medium', function() {
                    $(this).remove()
                })
            }
        })

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

    })

    $('body').delegate('.bBuyingLine label', 'click', function(e) {
        var target = $(e.target)
        if (!target.is('input')) {
            return
        }

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

        var el = $(this).find('input[type="radio"][data-delivery-type]')
        if (!el.length) {
            return
        }

        var url = $('#order-form').data('deliveryMapUrl')

        $('#order-form-part2').hide()
        $('.order-shop-button').hide()

        if ('self' == el.data('deliveryType')) {
            $('.order-shop-button')
                //.css('display', 'block')
                .show()
        }
        else {
            $('#order-loader').clone().appendTo('#order-loader-holder').show()

            DeliveryMap.getRemoteData(url, { deliveryTypeId: el.val()}, function(data) {
                this.render()

                $('#order-loader-holder').html('')
                $('#order-form-part2').show('fast')
            })

            DeliveryMap.renderUndeliveredMessage(el.val())
        }
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
            var data = {
                name: delivery.name,
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
        var hasInterval = (el.closest('ul[data-interval-holder]').data('intervalHolder'))

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
        if (hasInterval && (date != deliveryType.date)) {
            var intervals = DeliveryMap.getDeliveryInterval(deliveryType, date)
            var interval = Object.keys(intervals).shift()
            data.deliveryTypes[deliveryTypeToken].interval = interval
            displayInterval = intervals[interval] ? ('с '+intervals[interval].start_at+' по '+intervals[interval].end_at) : ''
            deliveryTypeHolder.find('h2 [data-assign]').each(function(i, el) {
                Templating.assign($(el), { displayInterval: displayInterval })
            })
        }
        data.deliveryTypes[deliveryTypeToken].date = date
        DeliveryMap.data(data)


        if (!hasInterval) {
            return
        }
        el.closest('.order-delivery-holder').find('.bBuyingDatePopup').remove()

        var deliveryType = DeliveryMap.data()['deliveryTypes'][deliveryTypeToken]
        var intervalHolder = $(el.closest('[data-interval-holder]').data('intervalHolder'))
        var intervalContainer = Templating.clone($(intervalHolder.data('template')))
        var intervals = DeliveryMap.getDeliveryInterval(deliveryType, date)

        var intervalElementTemplate = intervalContainer.find('.order-interval')
        $.each(intervals, function(i, interval) {
            intervalElement = intervalElementTemplate.clone()

            var value = interval.start_at+','+interval.end_at
            var displayValue = 'с '+interval.start_at+' по '+ interval.end_at
            Templating.assign(intervalElement, { value: value, date: date, deliveryType: deliveryType.token })
            $.each(intervalElement.find('[data-assign]'), function(i, el) {
                Templating.assign($(el), { name: displayValue })
            })
            if ((deliveryType.interval == value) && (deliveryType.date == date)) {
                intervalElement.addClass('bBuyingDatePopup__eOK')
            }

            intervalElement.appendTo(intervalContainer)
        })
        intervalElementTemplate.remove()

        intervalContainer.css({'left': el.position().left, 'top': el.position().top })
        intervalContainer
            .mouseenter(function() {
            clearTimeout($(this).data('timeoutId'))
        })
            .mouseleave(function() {
                var el = $(this)
                var timeoutId = setTimeout(function() {
                    el.remove()
                }, 50)

            })

        $.each(intervalContainer.find('[data-assign]'), function(i, el) {
            Templating.assign($(el), { date: displayDate })
        })

        intervalContainer.appendTo(intervalHolder)

    })

    $('body').delegate('.order-interval', 'click', function(e) {
        var el = $(this)
        var data = DeliveryMap.data()

        el.parent().find('.order-interval').each(function(i, el) {
            $(el).removeClass('bBuyingDatePopup__eOK')
        })
        el.addClass('bBuyingDatePopup__eOK')

        var date = el.data('date')
        var deliveryTypeToken = el.data('deliveryType')
        var deliveryTypeHolder = el.closest('.order-delivery-holder')
        var displayValue = el.data('value').split(',')
        displayValue = 'с '+displayValue[0]+' по '+displayValue[1]

        deliveryTypeHolder.find('h2 [data-assign]').each(function(i, el) {
            Templating.assign($(el), { displayInterval: displayValue })
        })

        data['deliveryTypes'][deliveryTypeToken].date = date
        data['deliveryTypes'][deliveryTypeToken].interval = el.data('value')

        DeliveryMap.data(data)

        setTimeout(function() {
            el.closest('.order-delivery-holder').find('.bBuyingDatePopup').hide(50, function() { $(this).remove() })
        }, 150)
    })


    if ($('.bBuyingLine__eRadio"]:checked').length) {
        DeliveryMap.render()
        $('#order-form-part2').show('fast')
    }

    $('#order-submit').click(function(e) {
        e.preventDefault()

        var button = $(this)
        var form = button.closest('form')
        var validator = $(form.data('validator')).data('value')

        form.find('.mRed').removeClass('mRed')
        form.find('.bFormError').remove()

        var hasError = false
        $.each(validator, function(field, message) {
            var el = form.find('[name="'+field+'"]:visible')
            hasError = DeliveryMap.validate(el, message)
        })

        if (hasError) {
            $.scrollTo('.bFormError:first', 300)
        }
        else {
            button.text('Оформляю заказ...')

            var data = form.serializeArray()
            data.push({ name: 'delivery_map', value: JSON.stringify(DeliveryMap.data()) })

            $.ajax({
                url: form.attr('action'),
                timeout: 60000,
                async: false,
                type: 'POST',
                data: data,
                success: function(result) {
                    var form = $('#order-form')

                    if (result.success) {
                        window.location = result.data.redirect
                    }
                    else if (result.error) {
                        if ('invalid' == result.error.code) {
                            $('#order-message').html('<span class="red">'+result.error.message+'</span>')
                            $.each(result.errors, function(k, v) {
                                var el = form.find('[name="'+k+'"]:visible')

                                showError(el, v, true)
                            })
                        }
                    }
                },
                complete: function() {
                    button.text('Завершить оформление')
                }
            })
        }
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

jQuery.effects||(function(d){d.effects={version:"1.7.3",save:function(g,h){for(var f=0;f<h.length;f++){if(h[f]!==null){g.data("ec.storage."+h[f],g[0].style[h[f]])}}},restore:function(g,h){for(var f=0;f<h.length;f++){if(h[f]!==null){g.css(h[f],g.data("ec.storage."+h[f]))}}},setMode:function(f,g){if(g=="toggle"){g=f.is(":hidden")?"show":"hide"}return g},getBaseline:function(g,h){var i,f;switch(g[0]){case"top":i=0;break;case"middle":i=0.5;break;case"bottom":i=1;break;default:i=g[0]/h.height}switch(g[1]){case"left":f=0;break;case"center":f=0.5;break;case"right":f=1;break;default:f=g[1]/h.width}return{x:f,y:i}},createWrapper:function(f){if(f.parent().is(".ui-effects-wrapper")){return f.parent()}var g={width:f.outerWidth(true),height:f.outerHeight(true),"float":f.css("float")};f.wrap('<div class="ui-effects-wrapper" style="font-size:100%;background:transparent;border:none;margin:0;padding:0"></div>');var j=f.parent();if(f.css("position")=="static"){j.css({position:"relative"});f.css({position:"relative"})}else{var i=f.css("top");if(isNaN(parseInt(i,10))){i="auto"}var h=f.css("left");if(isNaN(parseInt(h,10))){h="auto"}j.css({position:f.css("position"),top:i,left:h,zIndex:f.css("z-index")}).show();f.css({position:"relative",top:0,left:0})}j.css(g);return j},removeWrapper:function(f){if(f.parent().is(".ui-effects-wrapper")){return f.parent().replaceWith(f)}return f},setTransition:function(g,i,f,h){h=h||{};d.each(i,function(k,j){unit=g.cssUnit(j);if(unit[0]>0){h[j]=unit[0]*f+unit[1]}});return h},animateClass:function(h,i,k,j){var f=(typeof k=="function"?k:(j?j:null));var g=(typeof k=="string"?k:null);return this.each(function(){var q={};var o=d(this);var p=o.attr("style")||"";if(typeof p=="object"){p=p.cssText}if(h.toggle){o.hasClass(h.toggle)?h.remove=h.toggle:h.add=h.toggle}var l=d.extend({},(document.defaultView?document.defaultView.getComputedStyle(this,null):this.currentStyle));if(h.add){o.addClass(h.add)}if(h.remove){o.removeClass(h.remove)}var m=d.extend({},(document.defaultView?document.defaultView.getComputedStyle(this,null):this.currentStyle));if(h.add){o.removeClass(h.add)}if(h.remove){o.addClass(h.remove)}for(var r in m){if(typeof m[r]!="function"&&m[r]&&r.indexOf("Moz")==-1&&r.indexOf("length")==-1&&m[r]!=l[r]&&(r.match(/color/i)||(!r.match(/color/i)&&!isNaN(parseInt(m[r],10))))&&(l.position!="static"||(l.position=="static"&&!r.match(/left|top|bottom|right/)))){q[r]=m[r]}}o.animate(q,i,g,function(){if(typeof d(this).attr("style")=="object"){d(this).attr("style")["cssText"]="";d(this).attr("style")["cssText"]=p}else{d(this).attr("style",p)}if(h.add){d(this).addClass(h.add)}if(h.remove){d(this).removeClass(h.remove)}if(f){f.apply(this,arguments)}})})}};function c(g,f){var i=g[1]&&g[1].constructor==Object?g[1]:{};if(f){i.mode=f}var h=g[1]&&g[1].constructor!=Object?g[1]:(i.duration?i.duration:g[2]);h=d.fx.off?0:typeof h==="number"?h:d.fx.speeds[h]||d.fx.speeds._default;var j=i.callback||(d.isFunction(g[1])&&g[1])||(d.isFunction(g[2])&&g[2])||(d.isFunction(g[3])&&g[3]);return[g[0],i,h,j]}d.fn.extend({_show:d.fn.show,_hide:d.fn.hide,__toggle:d.fn.toggle,_addClass:d.fn.addClass,_removeClass:d.fn.removeClass,_toggleClass:d.fn.toggleClass,effect:function(g,f,h,i){return d.effects[g]?d.effects[g].call(this,{method:g,options:f||{},duration:h,callback:i}):null},show:function(){if(!arguments[0]||(arguments[0].constructor==Number||(/(slow|normal|fast)/).test(arguments[0]))){return this._show.apply(this,arguments)}else{return this.effect.apply(this,c(arguments,"show"))}},hide:function(){if(!arguments[0]||(arguments[0].constructor==Number||(/(slow|normal|fast)/).test(arguments[0]))){return this._hide.apply(this,arguments)}else{return this.effect.apply(this,c(arguments,"hide"))}},toggle:function(){if(!arguments[0]||(arguments[0].constructor==Number||(/(slow|normal|fast)/).test(arguments[0]))||(d.isFunction(arguments[0])||typeof arguments[0]=="boolean")){return this.__toggle.apply(this,arguments)}else{return this.effect.apply(this,c(arguments,"toggle"))}},addClass:function(g,f,i,h){return f?d.effects.animateClass.apply(this,[{add:g},f,i,h]):this._addClass(g)},removeClass:function(g,f,i,h){return f?d.effects.animateClass.apply(this,[{remove:g},f,i,h]):this._removeClass(g)},toggleClass:function(g,f,i,h){return((typeof f!=="boolean")&&f)?d.effects.animateClass.apply(this,[{toggle:g},f,i,h]):this._toggleClass(g,f)},morph:function(f,h,g,j,i){return d.effects.animateClass.apply(this,[{add:h,remove:f},g,j,i])},switchClass:function(){return this.morph.apply(this,arguments)},cssUnit:function(f){var g=this.css(f),h=[];d.each(["em","px","%","pt"],function(j,k){if(g.indexOf(k)>0){h=[parseFloat(g),k]}});return h}});d.each(["backgroundColor","borderBottomColor","borderLeftColor","borderRightColor","borderTopColor","color","outlineColor"],function(g,f){d.fx.step[f]=function(h){if(h.state==0){h.start=e(h.elem,f);h.end=b(h.end)}h.elem.style[f]="rgb("+[Math.max(Math.min(parseInt((h.pos*(h.end[0]-h.start[0]))+h.start[0],10),255),0),Math.max(Math.min(parseInt((h.pos*(h.end[1]-h.start[1]))+h.start[1],10),255),0),Math.max(Math.min(parseInt((h.pos*(h.end[2]-h.start[2]))+h.start[2],10),255),0)].join(",")+")"}});function b(g){var f;if(g&&g.constructor==Array&&g.length==3){return g}if(f=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(g)){return[parseInt(f[1],10),parseInt(f[2],10),parseInt(f[3],10)]}if(f=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(g)){return[parseFloat(f[1])*2.55,parseFloat(f[2])*2.55,parseFloat(f[3])*2.55]}if(f=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(g)){return[parseInt(f[1],16),parseInt(f[2],16),parseInt(f[3],16)]}if(f=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(g)){return[parseInt(f[1]+f[1],16),parseInt(f[2]+f[2],16),parseInt(f[3]+f[3],16)]}if(f=/rgba\(0, 0, 0, 0\)/.exec(g)){return a.transparent}return a[d.trim(g).toLowerCase()]}function e(h,f){var g;do{g=d.curCSS(h,f);if(g!=""&&g!="transparent"||d.nodeName(h,"body")){break}f="backgroundColor"}while(h=h.parentNode);return b(g)}var a={aqua:[0,255,255],azure:[240,255,255],beige:[245,245,220],black:[0,0,0],blue:[0,0,255],brown:[165,42,42],cyan:[0,255,255],darkblue:[0,0,139],darkcyan:[0,139,139],darkgrey:[169,169,169],darkgreen:[0,100,0],darkkhaki:[189,183,107],darkmagenta:[139,0,139],darkolivegreen:[85,107,47],darkorange:[255,140,0],darkorchid:[153,50,204],darkred:[139,0,0],darksalmon:[233,150,122],darkviolet:[148,0,211],fuchsia:[255,0,255],gold:[255,215,0],green:[0,128,0],indigo:[75,0,130],khaki:[240,230,140],lightblue:[173,216,230],lightcyan:[224,255,255],lightgreen:[144,238,144],lightgrey:[211,211,211],lightpink:[255,182,193],lightyellow:[255,255,224],lime:[0,255,0],magenta:[255,0,255],maroon:[128,0,0],navy:[0,0,128],olive:[128,128,0],orange:[255,165,0],pink:[255,192,203],purple:[128,0,128],violet:[128,0,128],red:[255,0,0],silver:[192,192,192],white:[255,255,255],yellow:[255,255,0],transparent:[255,255,255]};d.easing.jswing=d.easing.swing;d.extend(d.easing,{def:"easeOutQuad",swing:function(g,h,f,j,i){return d.easing[d.easing.def](g,h,f,j,i)},easeInQuad:function(g,h,f,j,i){return j*(h/=i)*h+f},easeOutQuad:function(g,h,f,j,i){return -j*(h/=i)*(h-2)+f},easeInOutQuad:function(g,h,f,j,i){if((h/=i/2)<1){return j/2*h*h+f}return -j/2*((--h)*(h-2)-1)+f},easeInCubic:function(g,h,f,j,i){return j*(h/=i)*h*h+f},easeOutCubic:function(g,h,f,j,i){return j*((h=h/i-1)*h*h+1)+f},easeInOutCubic:function(g,h,f,j,i){if((h/=i/2)<1){return j/2*h*h*h+f}return j/2*((h-=2)*h*h+2)+f},easeInQuart:function(g,h,f,j,i){return j*(h/=i)*h*h*h+f},easeOutQuart:function(g,h,f,j,i){return -j*((h=h/i-1)*h*h*h-1)+f},easeInOutQuart:function(g,h,f,j,i){if((h/=i/2)<1){return j/2*h*h*h*h+f}return -j/2*((h-=2)*h*h*h-2)+f},easeInQuint:function(g,h,f,j,i){return j*(h/=i)*h*h*h*h+f},easeOutQuint:function(g,h,f,j,i){return j*((h=h/i-1)*h*h*h*h+1)+f},easeInOutQuint:function(g,h,f,j,i){if((h/=i/2)<1){return j/2*h*h*h*h*h+f}return j/2*((h-=2)*h*h*h*h+2)+f},easeInSine:function(g,h,f,j,i){return -j*Math.cos(h/i*(Math.PI/2))+j+f},easeOutSine:function(g,h,f,j,i){return j*Math.sin(h/i*(Math.PI/2))+f},easeInOutSine:function(g,h,f,j,i){return -j/2*(Math.cos(Math.PI*h/i)-1)+f},easeInExpo:function(g,h,f,j,i){return(h==0)?f:j*Math.pow(2,10*(h/i-1))+f},easeOutExpo:function(g,h,f,j,i){return(h==i)?f+j:j*(-Math.pow(2,-10*h/i)+1)+f},easeInOutExpo:function(g,h,f,j,i){if(h==0){return f}if(h==i){return f+j}if((h/=i/2)<1){return j/2*Math.pow(2,10*(h-1))+f}return j/2*(-Math.pow(2,-10*--h)+2)+f},easeInCirc:function(g,h,f,j,i){return -j*(Math.sqrt(1-(h/=i)*h)-1)+f},easeOutCirc:function(g,h,f,j,i){return j*Math.sqrt(1-(h=h/i-1)*h)+f},easeInOutCirc:function(g,h,f,j,i){if((h/=i/2)<1){return -j/2*(Math.sqrt(1-h*h)-1)+f}return j/2*(Math.sqrt(1-(h-=2)*h)+1)+f},easeInElastic:function(g,i,f,m,l){var j=1.70158;var k=0;var h=m;if(i==0){return f}if((i/=l)==1){return f+m}if(!k){k=l*0.3}if(h<Math.abs(m)){h=m;var j=k/4}else{var j=k/(2*Math.PI)*Math.asin(m/h)}return -(h*Math.pow(2,10*(i-=1))*Math.sin((i*l-j)*(2*Math.PI)/k))+f},easeOutElastic:function(g,i,f,m,l){var j=1.70158;var k=0;var h=m;if(i==0){return f}if((i/=l)==1){return f+m}if(!k){k=l*0.3}if(h<Math.abs(m)){h=m;var j=k/4}else{var j=k/(2*Math.PI)*Math.asin(m/h)}return h*Math.pow(2,-10*i)*Math.sin((i*l-j)*(2*Math.PI)/k)+m+f},easeInOutElastic:function(g,i,f,m,l){var j=1.70158;var k=0;var h=m;if(i==0){return f}if((i/=l/2)==2){return f+m}if(!k){k=l*(0.3*1.5)}if(h<Math.abs(m)){h=m;var j=k/4}else{var j=k/(2*Math.PI)*Math.asin(m/h)}if(i<1){return -0.5*(h*Math.pow(2,10*(i-=1))*Math.sin((i*l-j)*(2*Math.PI)/k))+f}return h*Math.pow(2,-10*(i-=1))*Math.sin((i*l-j)*(2*Math.PI)/k)*0.5+m+f},easeInBack:function(g,h,f,k,j,i){if(i==undefined){i=1.70158}return k*(h/=j)*h*((i+1)*h-i)+f},easeOutBack:function(g,h,f,k,j,i){if(i==undefined){i=1.70158}return k*((h=h/j-1)*h*((i+1)*h+i)+1)+f},easeInOutBack:function(g,h,f,k,j,i){if(i==undefined){i=1.70158}if((h/=j/2)<1){return k/2*(h*h*(((i*=(1.525))+1)*h-i))+f}return k/2*((h-=2)*h*(((i*=(1.525))+1)*h+i)+2)+f},easeInBounce:function(g,h,f,j,i){return j-d.easing.easeOutBounce(g,i-h,0,j,i)+f},easeOutBounce:function(g,h,f,j,i){if((h/=i)<(1/2.75)){return j*(7.5625*h*h)+f}else{if(h<(2/2.75)){return j*(7.5625*(h-=(1.5/2.75))*h+0.75)+f}else{if(h<(2.5/2.75)){return j*(7.5625*(h-=(2.25/2.75))*h+0.9375)+f}else{return j*(7.5625*(h-=(2.625/2.75))*h+0.984375)+f}}}},easeInOutBounce:function(g,h,f,j,i){if(h<i/2){return d.easing.easeInBounce(g,h*2,0,j,i)*0.5+f}return d.easing.easeOutBounce(g,h*2-i,0,j,i)*0.5+j*0.5+f}})})(jQuery);;/*
 * jQuery UI Effects Pulsate 1.7.3
 *
 * Copyright (c) 2009 AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://docs.jquery.com/UI/Effects/Pulsate
 *
 * Depends:
 *	effects.core.js
 */
(function(a){a.effects.pulsate=function(b){return this.queue(function(){var d=a(this);var g=a.effects.setMode(d,b.options.mode||"show");var f=b.options.times||5;var e=b.duration?b.duration/2:a.fx.speeds._default/2;if(g=="hide"){f--}if(d.is(":hidden")){d.css("opacity",0);d.show();d.animate({opacity:1},e,b.options.easing);f=f-2}for(var c=0;c<f;c++){d.animate({opacity:0},e,b.options.easing).animate({opacity:1},e,b.options.easing)}if(g=="hide"){d.animate({opacity:0},e,b.options.easing,function(){d.hide();if(b.callback){b.callback.apply(this,arguments)}})}else{d.animate({opacity:0},e,b.options.easing).animate({opacity:1},e,b.options.easing,function(){if(b.callback){b.callback.apply(this,arguments)}})}d.queue("fx",function(){d.dequeue()});d.dequeue()})}})(jQuery);;