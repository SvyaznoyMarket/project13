$(document).ready(function () {
    $('#order_delivery_type_id_4').parent().css('color', '#E8303A')
        .find('div').removeClass('font11')

    function addDlvrInBill(innertxt) {
        var rubltmpl = $('<span class="rubl">p</span>')
        var dtmp = innertxt.split(',')
        var pritm = 0
        if (typeof(dtmp[1]) !== 'undefined' && dtmp[1].match(/\d+/))
            pritm = dtmp[1].match(/\d+/)[0]

        var total = $('div.cheque div.total').find('strong').text().replace(/\D+/g, '') * 1 + pritm * 1
        if ($('#dlvrbill').length) {
            total -= $('#dlvrbill').find('strong').text().replace(/\D+/g, '') * 1
            $('#dlvrbill').remove()
        }
        if (pritm) {
            var dlvrline = $('<li>').attr('id', 'dlvrbill')
                .append($('<div>').text(dtmp[0]))
                .append($('<strong>').text(printPrice(pritm) + ' ').append('<span class="rubl">p</span>'))

            $('div.cheque ul').append(dlvrline)
        }
        $('div.cheque div.total').find('strong').empty().text(printPrice(total) + ' ').append(rubltmpl)
    }

    function triggerDelivery(i) {
        if (i == 3) {
            $('.shop_block').show()
            $('.delivery_block').hide()
            $('.deliverytext').html('Представьтесь:')
            $('#delivered_at_block label').html('Выберите дату:')
        } else {
            $('.shop_block').hide()
            $('.delivery_block').show()
            $('.deliverytext').html('Кому и куда доставить:')
            $('#delivered_at_block label').html('Выберите дату доставки:')
        }
        // dirty hack
        if (i == 4) {
            $('#order_payment_method_id_1').parent().hide()
            $('#order_payment_method_id_2').parent().hide()
        } else {
            $('.checkboxlist2 li:hidden').show()
        }
        //
        $('#order_shop_id').trigger('change')
    }

    var checker = $('.order-form').find('[name="order[delivery_type_id]"]:checked')
    triggerDelivery(checker.val())
    // $('<img src="/images/ajaxnoti.gif" />').css('display', 'none').appendTo('body') //preload
    // var noti = $('<div>').html('<div><img src="/images/ajaxnoti.gif" /></br></br> Ваш заказ оформляется</div>')
    //     .attr('id', 'noti').appendTo('body')
    var scndRun = false
    $('.order-form').submit(function (e) {
        if (scndRun) // firefox fix
            return true
        e.preventDefault()
        scndRun = true
        $(this).find(':submit').val('Оформляется...')

        $('#noti').lightbox_me({
            centered:true,
            closeClick:false,
            closeEsc:false
        })
        setTimeout(function () {
            $('.order-form').trigger('submit')
        }, 500) // opera fix
    })

    $('.order-form').change(function (e) {
        var form = $(this)

        if ('order[shop_id]' == $(e.target).attr('name')) {
            var el = $(e.target).find('option:selected')
            if (!el.length)
                return
            $.post(form.data('updateFieldUrl'), {
                order:{
                    delivery_type_id:form.find('[name="order[delivery_type_id]"]:checked').val(),
                    shop_id:el.val()
                },
                field:'delivered_at'
            }, function (result) {
                if (false === result.success) {

                }
                var toupdate = form.find('[name="order[delivered_at]"]')
                toupdate.empty()
                $.each(result.data.content, function (v, n) {
                    toupdate.append('<option value="' + v + '">' + n + '</option>')
                })
                toupdate.find(':first').attr('selected', 'selected')
                toupdate.change()
            })
        }

        if ('order[region_id]' == $(e.target).attr('name')) {
            var el = $(e.target).find('option:selected')
            var formreg = $('form#region')
            formreg.attr('action', el.data('url'))
            formreg.submit()
        }

        if ('order[delivery_type_id]' == $(e.target).attr('name')) {
            var el = form.find('[name="order[delivery_type_id]"]:checked')
            if (el.length) {
                addDlvrInBill(el.next().find('strong').text())
                triggerDelivery(el.val())
                //dirty hack
                var postdt = el.val()
                //if (postdt==4) postdt=3
                //
                $.post(form.data('updateFieldUrl'), {
                    order:{
                        delivery_type_id:postdt
                    },
                    field:'delivery_period_id'
                }, function (result) {
                    if (typeof( result.success ) !== 'undefined' && result.success) {
                        var select = $('[name="order[delivery_period_id]"]'),
                            opts = result.data.content;
                        select.empty()
//                $.each(opts, function(v, n) {
//                  if (n == 'с 09:00 до 18:00') {
//                    select.append('<option value="'+v+'">'+n+'</option>')
//                  }
//                })
//                $.each(opts, function (v, n) {
//                  if (n != 'с 09:00 до 18:00') {
//                    select.append('<option value="' + v + '">' + n + '</option>')
//                  }
//                })
                        $.each(opts, function (n, v) {
                            select.append('<option value="' + v[0] + '">' + v[1] + '</option>')
                        })
                        select.find(':first').attr('selected', 'selected')
                        select.change()
                    }
                }, 'json')
            }
        }
    })

    $('#order_shop_id').trigger('change')


    $('.order_user_address').bind('change', function (e) {
        var el = $(this)

        $('[name="order[address]"]').val(el.val())
    })
    /* RETIRED
     $('#basic_register-form').bind({
     'submit': function(e) {
     e.preventDefault()

     var form = $(this)

     form.ajaxSubmit({
     'beforeSubmit': function() {
     var button = form.find('input:submit')
     button.attr('disabled', true)
     button.attr('value', 'Запоминаю...')
     },
     'success': function(response) {
     if (true !== response.success) {
     form.find('.form-content:first').html(response.data.form)
     }
     else {
     window.location = response.redirect
     }
     },
     'complete': function() {
     var button = form.find('input:submit')
     button.attr('disabled', false)
     button.attr('value', 'Запомнить меня')
     }
     })
     }
     })
     */

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
