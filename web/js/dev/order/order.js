(function(){
    

    $(document).ready(function () {
        /* order final analytics*/
        if (($('body').attr('data-template')=='order_complete')&&(typeof(orderAnalyticsRun) !== 'undefined')){
            orderAnalyticsRun()
        }
        if ($('.socnet-ico-list-link').length){
            $('.socnet-ico-list-link').bind('click', function(){
                var type = $(this).data('type')
                if (typeof(_gaq) !== 'undefined') {
                    _gaq.push(['_trackEvent', 'SMM', 'Complete order', type]);
                }
            });
        }
        /* sertificate */
        if( $('.orderFinal__certificate').length ) {
            var code = $(".cardNumber"),
                pin = $(".cardPin"),
                form = $(".orderFinal__certificate form"),
                button = $('#sendCard'),

                urlCheck = '/certificate-check',
                urlActivate = '/certificate-activate'

            var SertificateCard = (function() {

                var
                    paymentWithCard = $('#paymentWithCard').text()*1,
                    checked = false,
                    processTmpl = 'processBlock'

                function setPaymentSum( delta ) {
                    if( delta > paymentWithCard )
                        paymentWithCard = 0
                    else
                        paymentWithCard -= delta
                    $('#paymentWithCard').text( paymentWithCard )
                }

                function prepareNewCard() {
                    code.val('')
                    pin.val('')
                    button.addClass('mDisabled')
                    checked = false
                }
                function getCode() {
                    return code.val().replace(/[^0-9]/g,'')
                } 
                function getPIN() {
                    return pin.val().replace(/[^0-9]/g,'')
                }
                function getParams() {
                    return { code: getCode() , pin: getPIN() }
                }
                function activateButton() {
    // console.info('activateButton', getCode(), getPIN())                    
                    if( checked && ( getCode() !== '' ) && getCode().length === 14 && ( getPIN() !== '' ) && getPIN().length === 4) {
                        button.removeClass('mDisabled')
                    }
                }
                function checkForStars( v ) {
                    if( v.match(/\*/) )
                         button.addClass('mDisabled')
                }
                function checkCard() {
                    setProcessingStatus( 'orange', 'Проверка по номеру карты' )
                    $.post( urlCheck, { code: '23846829634' }, function( data ) {
                        if( ! 'success' in data )
                            return false
                        if( !data.success ) {
                            var err = ( typeof(data.error) !== 'undefined' ) ? data.error : 'ERROR'
                            setProcessingStatus( 'red', err )
                            return false
                        }
                        setProcessingStatus( 'green', data.data )
                    })       
                    activateButton()
                    pin.focus()
                }
                function setProcessingStatus( status, data ) {    
                    var blockProcess = $('.process').first()
                    if( !blockProcess.hasClass('picked') ) 
                        blockProcess.remove()
                    var options = { typeNum: status }
                    switch( status ) {
                        case 'orange':   
                            options.text = data 
                            checked = false
                            break
                        case 'red':
                            options.text = 'Произошла ошибка: ' + data
                            checked = false
                            break
                        case 'green':
                            if( 'activated' in data ) 
                                options.text = 'Карта '+ data.code + ' на сумму ' + data.sum + ' активирована!'
                            else
                                options.text = 'Карта '+ data.code + ' имеет номинал ' + data.sum
                            checked = true
                            break
                    }
                    form.after( tmpl( processTmpl, options) )
                    if( typeof( data['activated'] ) !== 'undefined' )
                        $('.process').first().addClass('picked')
                    activateButton()
                }

                return {
                    activateButton: activateButton,
                    checkCard: checkCard,
                    setProcessingStatus: setProcessingStatus,
                    setPaymentSum: setPaymentSum,
                    prepareNewCard: prepareNewCard,
                    getParams: getParams,
                    checkForStars: checkForStars
                }
            })(); // object SertificateCard , singleton

            code.mask("999 999 999 9999 9", { completed: SertificateCard.checkCard, placeholder: "*" } )
            pin.mask("9999", { completed: SertificateCard.activateButton, placeholder: "*" } )
            code.bind('keyup', function() {
                SertificateCard.checkForStars( $(this).val() )
            })
            pin.bind('keyup', function() {
                SertificateCard.checkForStars( $(this).val() )
            })
           
            button.bind('click', function(e) {
                e.preventDefault()
                if( $(this).hasClass('mDisabled') )
                    return false
                SertificateCard.setProcessingStatus( 'orange', 'Минутку, активация карты...' )
                
                $.get( urlActivate, SertificateCard.getParams(), function( data ) {
                    if( ! 'success' in data )
                        return false
                    if( !data.success ) {
                        SertificateCard.setProcessingStatus( 'red', data.error )
                        return false
                    }
                    data.data.activated = true
                    SertificateCard.setProcessingStatus( 'green', data.data )
                    SertificateCard.setPaymentSum( data.data.sum*1 )
                    SertificateCard.prepareNewCard()
                })       
                return false
            })

            // $.mockjax({
            //   url: '/certificate-check',
            //   responseTime: 1000,
            //   responseText: {
            //     success: true,
            //     data: { sum: 1000, code: '3432432' }
            //   }
            // })
            $.mockjax({
              url: '/certificate-activate',
              responseTime: 1000,
              responseText: {
                success: true,
                error: 'alredy activated',
                data: { sum: 1000, code: '3432432' }
              }
            })
            
        }

        /* */
        
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
            // var dfd = $.Deferred()
            var orderErrPopup = function( txt, delUrl, addUrl ) {
                var id = 'tmpErrPopup'+Math.floor(Math.random()*22)
                var block = '<div id="'+id+'" class="popup">' +
                                '<div class="popupbox width290">' +
                                    '<div class="font18 pb18"> Непредвиденная ошибка</div>'+
                                '</div>' +
                                '<p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>'
                            '</div> '
                $('body').append( $(block) )
                $.each(txt, function(i, item){
                    $('#'+id).find('.popupbox').append('<div class="font18 pb18"> ' +item+ '</div>')
                })
                $('#'+id).lightbox_me({
                  centered: true,
                  closeSelector: ".closePopup",
                  onClose: function(){
                        var sendData = function(item, i){
                            if(item[i]){
                                var url = item[i]+''
                                $.ajax({
                                    url: url
                                }).then(function(res){
                                    i++
                                    sendData(item, i)
                                })
                            }
                            else{
                                window.location.reload()
                            }

                        }
                        $.merge(delUrl, addUrl)
                        sendData(delUrl, 0)
                    }
                })
            }
            var txt = []
            var delUrl = []
            var addUrl = []
            var length = $('#product_errors').data('value').length
            if (length) {
                var checkItemQuantity = function() {
                    $.each($('#product_errors').data('value'), function(i, item) {
                        if (item.product.deleteUrl) delUrl.push(item.product.deleteUrl)
                        if (item.product.addUrl) addUrl.push(item.product.addUrl)
                        if (708 == item.code) {
                            if (item.quantity_available > 0) {
                                if (typeof(_gaq) !== 'undefined') 
                                    _gaq.push(['_trackEvent', 'Errors', 'User error', 'Нет нужного количества товаров'])
                                txt.push('Вы заказали товар '+item.product.name+' в количестве '+item.product.quantity+' шт. <br/ >Доступно только '+item.quantity_available+' шт.<br/ >Будет заказано '+item.quantity_available+'шт')
                                // delUrl.push(item.product.deleteUrl)
                                addUrl.push(item.product.addUrl)
                                
                            }
                            else {
                                if (typeof(_gaq) !== 'undefined') 
                                    _gaq.push(['_trackEvent', 'Errors', 'User error', 'Нет товара для выбранного способа доставки'])
                                txt.push('Товара ' + item.product.name + ' нет в наличии для выбранного способа доставки.<br/>Товар будет удален из корзины.')
                                // delUrl.push(item.product.deleteUrl)
                            }
                        }
                        else {
                            if (typeof(_gaq) !== 'undefined') 
                                _gaq.push(['_trackEvent', 'Errors', 'User error', 'Товар недоступен для продажи'])
                            txt.push('Товар ' + item.product.name + ' недоступен для продажи.<br/>Товар будет удален из корзины.')
                            // delUrl.push(item.product.deleteUrl)
                        }
                    })
                    orderErrPopup(txt, delUrl, addUrl)
                }
                checkItemQuantity()
            }

         })();


        ;
        ( function () {
            var j_count = $('.timer');
            if (!j_count.length) {
                return false
            }
            var interval = window.setInterval(sec5run, 1000);
            var secs = j_count.html().replace(/\D/g, '') * 1;

            var clearPaymentUrl = function(form) {
                $.ajax({
                    type: 'POST',
                    url: form.data('clear-payment-url'),
                    async: false,
                    data: {},
                    success: function() {}
                });
            };

            var sec5run = function() {
                if (secs === 1) {
                    if( $('form.paymentUrl').length ) {
                        clearPaymentUrl($('form.paymentUrl'));
                    }
                    clearInterval(interval);
                    // $('.form').submit();
                }
                secs -= 1;
                j_count.html(secs);
            };
        })();

        /* Credit Widget */
        //window.onbeforeunload = function (){ return false }    // DEBUG    
        if( ! $('#credit-widget').length )
            return
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
                        { order_id: creditWidget.vars.number,
                        region: creditWidget.vars.region }
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

}());
