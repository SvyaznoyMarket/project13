$(document).ready(function() {

    if (!$('#product_errors').length) return;

    var length = $('#product_errors').data('value').length

    if (length) {
        var checkItemQuantity = function() {
            var dfd = $.Deferred()

            $.each($('#product_errors').data('value'), function(i, item) {

                if (708 == item.code) {

                    if (item.quantity_available > 0)
                    {
                        if (confirm('Вы заказали товар "'+item.product.name+'" в количестве '+item.product.quantity+' шт.'+"\n\n"+'Доступно только '+item.quantity_available+' шт.'+"\n\n"+'Заказать '+item.quantity_available+'шт?')) {
                            $.ajax({
                                url: item.product.deleteUrl
                            }).done(function(result) {
                                    $.ajax({
                                        url: item.product.addUrl
                                    }).done(function() {
                                            if ((i +1) == length) dfd.resolve()
                                    })
                                })
                        }
                        else {
                            if ((i +1) == length) dfd.resolve()
                        }
                    }
                    else {
                        if (confirm('Товара "'+item.product.name+'" нет в наличии для выбранного способа доставки.'+"\n\n"+'Удалить товар из корзины?')) {
                            $.ajax({
                                url: item.product.deleteUrl
                            }).done(function(result) {
                                if ((i +1) == length) dfd.resolve()
                            })
                        }
                        else {
                            if ($('#cart-link').data('value')) {
                                window.location = $('#cart-link').data('value');
                            }
                            dfd.reject()
                        }
                    }
                }
                else {
                //else if (800 == item.code) {
                    if (confirm('Товар "' + item.product.name + '" недоступен для продажи.'+"\n\n"+'Удалить этот товар из корзины?')) {
                        $.ajax({
                            url: item.product.deleteUrl
                        }).done(function() {
                            if ((i +1) == length) dfd.resolve()
                        })
                    }
                    else {
                        if ((i +1) == length) dfd.resolve()
                    }
                }
            })

            return dfd.promise()
        }

        $.when(checkItemQuantity()).done(function() {
            window.location.reload()
        })
    }

})
