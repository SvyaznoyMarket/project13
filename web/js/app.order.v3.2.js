$(document).ready(function() {

    $('body').delegate('.bBuyingLine label', 'click', function() {
        if( $(this).find('input').attr('type') == 'radio' ) {
            var thatName = $('.mChecked input[name="'+$(this).find('input').attr('name')+'"]')
            if( thatName.length ) {
                thatName.each( function(i, item) {
                    $(item).parent('label').removeClass('mChecked')
                })
            }
            $(this).addClass('mChecked')
            return
        }

        if( $(this).find('input').attr('type') == 'checkbox' ) {
            $(this).toggleClass('mChecked')
        }

    })

    $('body').delegate('.bBuyingLine input:radio, .bBuyingLine input:checkbox', 'click', function(e) {
        e.stopPropagation()
    })

    $('body').delegate('.bBuyingLine label', 'click', function() {
        var el = $(this).find('input[type="radio"]')
        var url = $('#order-form').data('deliveryMapUrl')

        $('#order-form-part2').hide()
        $('.order-shop-button').hide()

        if ('self' == el.data('deliveryType')) {
            $('.order-shop-button').show('medium')
        }
        else {
            $('#order-loader').clone().appendTo('#order-loader-holder').show()

            DeliveryMap.getRemoteData(url, { deliveryTypeId: el.val()}, function(data) {
                this.render()

                $('#order-loader-holder').html('')
                $('#order-form-part2').show('fast')
            })
        }
    })

    $('body').delegate('.order-shop-button', 'click', function(e) {
        e.preventDefault()

        DeliveryMap.openShopMap(function(data) {
            console.info(data)
        })
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

        var weekNum = el.data('value')

        el.parent().find('.order-delivery_date').hide()
        el.parent().find('.order-delivery_date[data-week="'+weekNum+'"]').show()

        el.parent().find('.order-delivery_date-control').each(function(i, el) {
            $(el).removeClass('mDisabled')
        })
        el.addClass('mDisabled')
    })

    $('body').delegate('.order-delivery_date', 'click', function() {
        var el = $(this)

        if (!el.hasClass('bBuyingDates__eDisable')) {
            el.removeClass('bBuyingDates__eEnable')
            el.parent().find('.order-delivery_date')
                .removeClass('bBuyingDates__eCurrent')
                .addClass('bBuyingDates__eEnable')

            el.removeClass('bBuyingDates__eEnable').addClass('bBuyingDates__eCurrent')
        }
    })

    $('body').delegate('ul[data-interval-holder] .order-delivery_date', 'mouseenter', function(e) {
        var el = $(this)
        var intervalHolder = $(el.closest('[data-interval-holder]').data('intervalHolder'))
        var intervalContainer = Templating.clone($(intervalHolder.data('template')))

        el.closest('.order-delivery-holder').find('.bBuyingDatePopup').remove()

        var date = el.data('value')
        var displayDate = el.data('displayValue')
        var deliveryTypeToken = el.closest('.order-delivery-holder').data('value')
        var deliveryType = DeliveryMap.data()['deliveryTypes'][deliveryTypeToken]
        var intervals = DeliveryMap.getDeliveryInterval(deliveryType, date)

        var intervalElementTemplate = intervalContainer.find('.order-interval')
        $.each(intervals, function(i, interval) {
            intervalElement = intervalElementTemplate.clone()

            Templating.assign(intervalElement, { value: interval.start_at+','+interval.end_at, date: date, deliveryType: deliveryType.token })
            $.each(intervalElement.find('[data-assign]'), function(i, el) {
                Templating.assign($(el), { name: 'с '+interval.start_at+' по '+ interval.end_at })
            })
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
            Templating.assign($(el), {
                date: displayDate
            })
        })

        intervalContainer.appendTo(intervalHolder)
    })
    $('body').delegate('ul[data-interval-holder] .order-delivery_date', 'mouseleave', function(e) {
        var el = $(this)
        var intervalHolder = $(el.closest('[data-interval-holder]').data('intervalHolder'))
    })

    $('body').delegate('.order-interval', 'click', function(e) {
        var el = $(this)

        el.parent().find('.order-interval').removeClass('bBuyingDatePopup__eOK')
        el.addClass('bBuyingDatePopup__eOK')
        var date = el.data('date')
        var deliveryTypeToken = el.data('deliveryType')

        $('.order-delivery-holder[data-value="'+deliveryTypeToken+'"]').find('.order-delivery_date[data-value="'+date+'"]').click()
    })

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

        getRemoteData: function(url, params, callback) {
            var self = this

            $.ajax({
                type: 'POST',
                async: false,
                timeout: 60000,
                url: url,
                dataType: 'json',
                data: {
                    'delivery_type_id': params.deliveryTypeId
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
            $.each(data.deliveryTypes[fromDeliveryTypeToken].items, function(i, token) {
                if (token == itemToken) {
                    data.deliveryTypes[fromDeliveryTypeToken].items.splice(i, 1)
                }
            })
            data.deliveryTypes[toDeliveryTypeToken].items.push(itemToken)
            self.data(data)

            this.render()
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

            return price
        },

        getDeliveryTotal: function(deliveryType) {
            var data = this.data()

            var total = 0
            $.each(deliveryType.items, function(i, itemToken) {
                total += data.items[itemToken].total
            })

            return total
        },

        getDeliveryInterval: function(deliveryType, date) {
            var data = this.data()

            var intervals = {}
            $.each(deliveryType.items, function(i, itemToken) {
                $.each(data.items[itemToken].deliveries[deliveryType.token].dates, function(i, v) {
                    if (v.value == date) {
                        $.each(v.intervals, function(i, interval) {
                            intervals[interval.start_at+'-'+interval.end_at] = interval
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
            var total = self.getDeliveryTotal(deliveryType)
            var totalContainer = Templating.clone($(totalHolder.data('template')))

            totalContainer.find('[data-assign]').each(function(i, el) {
                Templating.assign($(el), { total: printPrice(total), name: 'Итого' + ('self' == deliveryType.type ? ' с доставкой' : '') })
            })
            totalContainer.appendTo(totalHolder)

            // доступность дат
            var dates = []
            $.each(deliveryType.items, function(i, itemToken) {
                $.each(data.items[itemToken].deliveries[deliveryType.token].dates, function(i, date) {
                    dates.push(date.value)
                })
            })
            $.each(deliveryTypeHolder.find('.order-delivery_date'), function(i, el) {
                var el = $(el)
                var value = el.data('value')
                var exists = -1 !== $.inArray(value, dates)

                if (exists) {
                    el.removeClass('bBuyingDates__eDisable')
                    el.addClass('bBuyingDates__eEnable')
                }
                else {
                    el.removeClass('bBuyingDates__eEnable')
                    el.addClass('bBuyingDates__eDisable')
                }
            })

            $.each(deliveryType.items, function(i, itemToken) {
                self.renderItem(itemHolder, data.items[itemToken])
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

        openShopMap: function(callback) {
            $('.mMapPopup').lightbox_me({
                centered: true,
                onLoad: function() {
                    //regionMap.showMarkers( markersPull )
                },
                onClose: function() {
                    callback.call(this, [{ shopId: null }])
                }
            })
        }
    }

    if ($('[name="order[delivery_type_id]"]:checked').length) {
        DeliveryMap.render()
        $('#order-form-part2').show('fast')
    }

})
