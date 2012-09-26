$(document).ready(function () {    
    $('.auth-link').bind('click', function (e) {
        e.preventDefault()

        var link = $(this)

        $('#login-form, #register-form').data('redirect', false)
        $('#auth-block').lightbox_me({
            centered:true,
            onLoad:function () {
                $('#auth-block').find('input:first').focus()
            },
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
        })
    }) 
    ;   
    ( function () {
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
     })();


    ;
    ( function () {
        var j_count = $('.timer')
        if (!j_count.length)
            return false
        var interval = window.setInterval(sec5run, 1000)
        var secs = j_count.html().replace(/\D/g, '') * 1

        function sec5run() {
            if (secs === 1) {
                clearInterval(interval)
                $('.form').submit()
            }
            secs -= 1
            j_count.html(secs)
        }
    })();

    /* Credit Widget */
    //window.onbeforeunload = function (){ return false }    // DEBUG    
    
    var creditWidget = $('#credit-widget').data('value')
    if( ! 'widget' in creditWidget )
        return
    if( creditWidget.widget === 'direct-credit' ) {
//console.info('direct-credit')
        $LAB.script( 'JsHttpRequest.js' )
        .script( 'http://direct-credit.ru/widget/script_utf.js' )
        .wait( function() { 
            // fill cart
            for(var i = creditWidget.vars.items.length - 1; i >= 0; i--) {
                var item = creditWidget.vars.items[i]
                dc_getCreditForTheProduct(
                    '4427',
                    creditWidget.vars.number,
                    'addProductToBuyOnCredit',
                    {
                        name : item.articul,
                        count: item.quantity,
                        articul: item.articul,
                        price: item.price,
                        type: item.type
                    },
                    function(result){
                        openWidget()
                    }
                )
            }
            
            function openWidget() {
                dc_getCreditForTheProduct(
                    '4427', 
                    creditWidget.vars.number ,// session
                    'orderProductToBuyOnCredit',
                    { order_id: creditWidget.vars.number }
                )
            }
        })
    }

    var backURL = 'http://' + window.location.hostname

    if( creditWidget.widget === 'kupivkredit' ) {
//console.info('kupivkredit')
        var callback_close = function(decision) {
            setTimeout(function(){
                document.location = backURL
            }, 3000)
            // var result = ''
            // switch(decision) {
            //     case 'ver':
            //         result = 'Ваша заявка предварительно одобрена.'
            //         break
            //     case 'agr':
            //         result = 'Ваша заявка одобрена! Поздравляем!'
            //         break
            //     case 'rej':
            //         result = 'К сожалению, заявка отклонена банком.'
            //         break
            //     case '':
            //         result = 'Вы не заполнили заявку до конца'
            //         break
            //     default:
            //         result = 'Ваша заявка находится на рассмотрении'
            //         break
            // }
            // alert(result)
        }

        var callback_decision = function(decision) {
            //console.info( 'Пришел статус: ' + decision )
        }
        
        $LAB.script( 'https://www.kupivkredit.ru/widget/vkredit.js')
        .wait( function() {
            var vkredit = new VkreditWidget(1, creditWidget.vars.sum,  {
                order: creditWidget.vars.order,
                sig: creditWidget.vars.sig,
                callbackUrl: window.location.href,
                onClose: callback_close, 
                onDecision: callback_decision 
            })
            vkredit.openWidget()
        })
        
    }


});
