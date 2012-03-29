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
            url: url,
            dataType: 'json',
            data: {
                'delivery_type_id': el.val()
            },
            success: function(result) {
                var data = result.data
                console.info(data);

                DeliveryMap.data(data)
                DeliveryMap.render()

                $('#order-form-part2').show('fast')
            },
            complete: function() {
                $('#order-loader-holder').html('')
            }
        })
    })


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

        moveItem: function(itemToken, fromDeliveryToken, toDeliveryToken) {
            var data = this.data

            var item = data.items[itemToken]
            delete data.deliveryTypes[fromDeliveryToken].items[itemToken]
            data.deliveryTypes[toDeliveryToken].items[itemToken] = item.id

            this.render()
        },

        render: function() {
            var self = this

            $('.order-delivery-holder').each(function(i, deliveryTypeHolder) {
                self.renderDeliveryType($(deliveryTypeHolder))
            })
        },

        renderDeliveryType: function(deliveryTypeHolder) {
            var self = this
            var data = this.data

            deliveryTypeHolder.find('.order-item-holder').each(function(i, itemHolder) {
                self.renderItem($(itemHolder))
            })
        },

        renderItem: function(itemHolder) {
            var self = this
            var data = this.data

            var template = $(itemHolder.data('template'))
            console.info(template)
        }
    }


})
