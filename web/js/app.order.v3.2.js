$(document).ready(function() {

    var Templating = {
        assign: function (el, name, data) {
            $.each(el.data('assign-'+name), function(k, v) {
                var value = v
                if (!$.isArray(value)) {
                    value = [ value ]
                }

                $.each(value, function (i, item) {
                    if (0 === item.indexOf('data.')) {
                        value[i] = data[item.replace('data.', '')]
                    }
                })

                el[k].apply(el, value)
            })
        },
        getDeliveryBlock: function (template, data) {
            var block = $(template.clone().html())

            block.find('[data-assign-delivery]').each(function(k, v) { Templating.assign($(v), 'delivery', data) })

            return block
        },
        getProductBlock: function (template, data) {
            var block = $(template.clone().html())

            block.find('[data-assign-product]').each(function(k, v) { Templating.assign($(v), 'product', data) })

            return block
        },
        applyDate: function(block, deliveryToken, data) {
            if (0 == data.deliveries[deliveryToken].products.length) {
                block.hide()
            }

            var dates = {}
            $.each(data.deliveries[deliveryToken].products, function(i, productId) {
                dates[productId] = {}
                $.each(data.products[productId].deliveries[deliveryToken].dates, function(i, v) {
                    dates[productId][i] = v.date
                })
            })

            dates = array_values(dates)
            console.info(dates)
            dates = array_intersect.apply(null, [dates])
            console.info(dates)
        }
    }


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

                $('#order-delivery-holder').html('')

                $.each(data.deliveries, function(deliveryToken, delivery) {
                    var deliveryBlock = Templating.getDeliveryBlock($('#order-delivery-template'), delivery)

                    $.each(delivery.products, function (i, productId) {
                        var productBlock = Templating.getProductBlock($('#order-product-template'), data.products[productId])
                        deliveryBlock.find('.order-product-holder').append(productBlock)
                    })

                    Templating.applyDate(deliveryBlock, deliveryToken, data)

                    $('#order-delivery-holder').append(deliveryBlock)
                })

                $('#order-form-part2').show('fast')
            },
            complete: function() {
                $('#order-loader-holder').html('')
            }
        })
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