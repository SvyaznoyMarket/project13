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

        $('#order-loader').clone().appendTo('#order-loader-holder').show()

        $.ajax({
            type: 'POST',
            async: false,
            timeout: 60000,
            url: url,
            dataType: 'json',
            data: {
                'delivery_type_id': el.val()
            },
            success: function(result) {
                var data = result.data

                DeliveryMap.data(data)
                DeliveryMap.render()

                $('#order-form-part2').show('fast')
            },
            complete: function() {
                $('#order-loader-holder').html('')
            }
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
            console.info(self.data())

            this.render()
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
            var deliveryTypeData = data.deliveryTypes[deliveryTypeHolder.data('value')]
            var itemHolder = deliveryTypeHolder.find('.order-item-holder')

            itemHolder.html('')

            $.each(deliveryTypeData.items, function(i, itemToken) {
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

            if (Object.keys(data.deliveries).length <= 1) {
                itemContainer.find('.order-item_delivery-button').remove()
            }

            itemHolder.append(itemContainer)
        }
    }

    ShopMap = {
        holder: $('#order-shop-popup'),
        open: function () {
            var self = this

            self.holder.lightbox_me({
                centered: true,
                onLoad: function() {
                    regionMap.showMarkers(self.holder.data('markers'))
                }
            })
        }
    }

    if ($('[name="order[delivery_type_id]"]:checked').length) {
        DeliveryMap.render()
        $('#order-form-part2').show('fast')
    }

})
