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
                return
            }

            $.each(data.deliveryTypes[fromDeliveryTypeToken].items, function(i, token) {
                if (token == itemToken) {
                    data.deliveryTypes[fromDeliveryTypeToken].items.splice(i, 1)
                }
            })
            data.deliveryTypes[toDeliveryTypeToken].items.push(itemToken)
            self.data(data)
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

                    $.each(data.deliveryTypes[deliveryToken].items, function(i, itemToken) {
                        DeliveryMap.moveItem(itemToken, deliveryToken, toDeliveryToken)
                    })

                    if (!data.deliveryTypes[toDeliveryToken].date) {
                        var data = DeliveryMap.data()
                        data.deliveryTypes[toDeliveryToken].date = DeliveryMap.getDeliveryDate(data.deliveryTypes[toDeliveryToken]).shift()
                        DeliveryMap.data(data)
                    }

                    DeliveryMap.render()
                }

            regionMap.openMap()
        },

        onShopSelected: function() {},

        onMapClosed: function(shopId) {
            var el = $('.bBuyingLine__eRadio:checked')

            regionMap.closeMap()

            DeliveryMap.onShopSelected.apply(this, [el.val(), shopId])
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
            displayInterval = 'с '+intervals[interval].start_at+' по '+intervals[interval].end_at
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

        var form = $(this).closest('form')
        var validator = $(form.data('validator')).data('value')

        form.find('.mRed').removeClass('mRed')
        form.find('.bFormError').remove()

        var data = form.serializeArray()

        var hasError = false
        $.each(validator, function(field, message) {
            var fieldEl = form.find('[name="'+field+'"]:visible:first')

            if (
                !fieldEl.val()
                || ((fieldEl.is(':checkbox') || fieldEl.is(':radio')) && !fieldEl.attr('checked'))
            ) {
                //console.info(fieldEl)
                fieldEl.addClass('mRed')
                fieldEl.after( '<span class="bFormError mb10 pt5">'+message+'</span>' )
                hasError = true
            }
        })

        if (hasError) {
            $.scrollTo('.mRed:first', 300)
        }
    })

})



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