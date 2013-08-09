(function() {

    var trackEmailChange = function() {
        var subscriptionCheckbox = $('#order_recipient_email').parent().find('p.subscribe .bSubscibe');
        var emailField = $('#order_recipient_email');
        if ( emailField.val() && emailField.val().isEmail() && subscriptionCheckbox.hasClass('hf') ) {
            subscriptionCheckbox.removeClass('hf');
            subscriptionCheckbox.css('visibility','visible');
        } else if ( !emailField.val() || emailField.val() && !emailField.val().isEmail() && !subscriptionCheckbox.hasClass('hf') ) {
            subscriptionCheckbox.addClass('hf');
            subscriptionCheckbox.css('visibility','hidden');
        }
    }

    $(document).ready(function() {
        // for GA variables
        var items_num = 0; // количество элементов в заказе (если в заказе 10 лопат и 1 совок, то логируем 11 элементов)
        var suborders_num = 0; // количество подзаказов в заказе
        var nowDelivery = 0; // выбранный тип доставки
        /* ---------------------------------------------------------------------------------------- */
        /* COMMON DESIGN, BEHAVIOUR ONLY */
        /* Custom Selectors */
        // $('body').delegate( '.bSelect', 'click', function() {
        //     if( $(this).hasClass('mDisabled') )
        //         return false
        //     $(this).find('.bSelect__eDropmenu').toggle()
        // })
        // $('body').delegate( '.bSelect', 'mouseleave', function() {
        //     if( $(this).hasClass('mDisabled') )
        //         return false
        //     var options = $(this).find('.bSelect__eDropmenu')
        //     if( options.is(':visible') )
        //         options.hide()
        // })

        /*  Custom Checkboxes */
        $('body').delegate('.bBuyingLine label', 'click', function() {
            // e.stopPropagation()
            if( $(this).find('input').attr('type') === 'radio' ) {
                var thatName = $('.mChecked input[name="'+$(this).find('input').attr('name')+'"]');
                if( thatName.length ) {
                    thatName.each( function(i, item) {
                        $(item).parent('label').removeClass('mChecked');
                    });
                }
                $(this).addClass('mChecked');
                return;
            }

            if( $(this).find('input').attr('type') === 'checkbox' ) {
                $(this).toggleClass('mChecked');
            }

        });

        $('body').delegate('.bBuyingLine input:radio, .bBuyingLine input:checkbox', 'click', function(e) {
            e.stopPropagation();
        });

        $('body').delegate('input[name="order[payment_method_id]"]', 'click', function() {
            $('.innerType').hide();
            $(this).parent().parent().find('.innerType').show();
        });

        /* Sertificate */
        if( $('.orderFinal__certificate').length ) {

            var code = $(".cardNumber"),
                pin = $(".cardPin"),
                sfields = $("#sertificateFields"),
                urlCheck = '/certificate-check';

            var SertificateCard = (function() {

                var paymentWithCard = $('#paymentWithCard').text()*1,
                    checked = false,
                    processTmpl = 'processBlock';

                function getCode() {
                    return code.val().replace(/[^0-9]/g,'');
                }
                function getPIN() {
                    return pin.val().replace(/[^0-9]/g,'');
                }
                function getParams() {
                    return { code: getCode() , pin: getPIN() };
                }
                function isActive() {
                    if( checked && ( getCode() !== '' ) && getCode().length === 14 && ( getPIN() !== '' ) && getPIN().length === 4) {
                        return true;
                    }
                    return false;
                }

                function checkCard() {
                    setProcessingStatus( 'orange', 'Проверка по номеру карты' );
                    $.post( urlCheck, getParams(), function( data ) {
                        if( !('success' in data) ) {
                            return false;
                        }
                        if( !data.success ) {
                            var err = ( typeof(data.error) !== 'undefined' ) ? data.error : 'ERROR';
                            setProcessingStatus( 'red', err );
                            return false;
                        }
                        setProcessingStatus( 'green', data.data );
                    });
                    // pin.focus()
                }
                function setProcessingStatus( status, data ) {
                    var blockProcess = $('.process').first();
                    if( !blockProcess.hasClass('picked') ) {
                        blockProcess.remove();
                    }
                    var options = { typeNum: status };
                    switch( status ) {
                        case 'orange':
                            options.text = data;
                            checked = false;
                            break;
                        case 'red':
                            options.text = 'Произошла ошибка: ' + data;
                            checked = false;
                            break;
                        case 'green':
                            if( 'activated' in data ) {
                                options.text = 'Карта '+ data.code + ' на сумму ' + data.sum + ' активирована!';
                            } else {
                                options.text = 'Карта '+ data.code + ' имеет номинал ' + data.sum;
                            }
                            checked = true;
                            break;
                    }
                    sfields.after( tmpl( processTmpl, options) );
                    if( typeof( data['activated'] ) !== 'undefined' ) {
                        $('.process').first().addClass('picked');
                    }
                }

                return {
                    checkCard: checkCard,
                    setProcessingStatus: setProcessingStatus,
                    isActive: isActive,
                    getCode: getCode,
                    getPIN: getPIN
                };
            })(); // object SertificateCard , singleton

            // code.mask("999 999 999 9999 9", { completed: function(){ pin.focus() }, placeholder: "*" } )
            code.bind('keyup',function(e){
                if ( ((e.which >= 48) && (e.which <= 57)) || (e.which === 8) ) {//если это цифра или бэкспэйс
                    if( pin.val().length === 4) {
                        SertificateCard.checkCard();
                    }
                } else {
                    //если это не цифра
                    var clearVal = $(this).val().replace(/\D/g,'');
                    $(this).val(clearVal);
                }
            });
            pin.bind('focusout',function(e){
                SertificateCard.checkCard();
            });
            pin.mask("9999", { completed: SertificateCard.checkCard, placeholder: "*" } );

            // $.mockjax({
            //   url: '/certificate-check',
            //   responseTime: 1000,
            //   responseText: {
            //     success: true,
            //     data: { sum: 1000, code: '3432432' }
            //   }
            // })

        }

        /* Credit */
        if( $('.bankWrap').length ) {
            var banks = $('.bankWrap .bSelect').data('value');
            var docs  = $('.bankWrap > .creditHref');
            var select = $('.bankWrap .bSelect');
            var chSelect = function(){
                var thisId = $("option:selected", select).attr('ref');
                $('.bankWrap .bSelectWrap_eText').text( banks[ thisId ].name );
                $('input[name="order[credit_bank_id]"]').val( thisId );
                docs.find('a').attr('href', banks[ thisId ].href );
                docs.find('span').text('(' + banks[ thisId ].name + ')' );
            };
            for( var id in banks ) {
                var option = $('<option>').attr('ref', id).addClass('bSelect_eItem').text( banks[id].name );
                select.append( option );
            }
            $("option", select).eq(0).attr('selected','selected');
            chSelect();
            select.change(chSelect);
            // $('.bankWrap > .bSelect').append( options )

            DirectCredit.init( $('#tsCreditCart').data('value'), $('#creditPrice') );
        }

        /* Auth Link */
        PubSub.subscribe( 'authorize', function( m, d ) {
            $('#order_recipient_first_name').val( d.first_name );
            $('#order_recipient_last_name').val( d.last_name );
            $('#order_recipient_phonenumbers').val( d.phonenumber + '' );
            $('#qiwi_phone').val( d.phonenumber + '' );
            $('#user-block').hide();
        });

        $('.auth-link').bind('click', function (e) {
            e.preventDefault();

            var link = $(this);

            $('#login-form, #register-form').data('redirect', false);
            $('#auth-block').lightbox_me({
                centered:true,
                onLoad:function () {
                    $('#auth-block').find('input:first').focus();
                }
            });
        });
        /* Address Fields */
        // region changer (handler) describes in another file, its common call
        // $("#order_recipient_phonenumbers").focusin(function(){
        //     $(this).attr('maxlength','11')
        //     $(this).bind('keyup',function(e){
        //         if ( ((e.which>=96)&&(e.which<=105))||((e.which>=48)&&(e.which<=57))||(e.which==8) ){//если это цифра или бэкспэйс
        //             //
        //         }
        //         else{
        //             //если это не цифра
        //             var clearVal = $(this).val().replace(/\D/g,'')
        //             $(this).val(clearVal)
        //         }
        //     })
        // })

        if( typeof( $.mask ) !== 'undefined' ) {
            // $.mask.definitions['n'] = "[()0-9\ \-]"
            // $("#order_recipient_phonenumbers").mask("8nnnnnnnnnnnnnnnnn", { placeholder: " ", maxlength: 10 } )
            // var predefPhone = document.getElementById('order_recipient_phonenumbers').getAttribute('value')
            //       if( predefPhone && predefPhone != '' )
            //           $('#order_recipient_phonenumbers').val( predefPhone + '       ' )
            //       else
            //           $("#order_recipient_phonenumbers").val('8')

            $("#order_recipient_phonenumbers").mask("(999) 999-99-99");
            $("#order_recipient_phonenumbers").val($("#order_recipient_phonenumbers").val());

            $("#qiwi_phone").mask("(999) 999-99-99");
            $("#qiwi_phone").val($("#qiwi_phone").val());

            $.mask.definitions['*'] = "[0-9*]";
            $("#order_sclub_card_number").mask("* ****** ******", { placeholder: "*" } );
            if( $("#order_sclub_card_number")[0].getAttribute('value') ) {
                $("#order_sclub_card_number").val( $("#order_sclub_card_number")[0].getAttribute('value') );
            }
            $("#order_sclub_card_number").blur( function() {
                if( $(this).val() === "* ****** ******" ) {
                    $(this).trigger('unmask').val('');
                    $(this).focus( function() {
                        $("#order_sclub_card_number").mask("* ****** ******", { placeholder: "*" } );
                    });
                }
            });
        }

        // $('#addressField').find('input').placeholder()
        $('.placeholder-input').focus(function(e) {
            var el = $(e.target);
            if (!el.prev('.placeholder').hasClass('mRed')) {
                el.prev('.placeholder').css('border-color', '#FFA901');
            }
        }).focusout(function(e) {
                var el = $(e.target);
                if (!el.prev('.placeholder').hasClass('mRed')) {
                    el.prev('.placeholder').css('border-color', '#DDDDDD');
                }
            });

        $('.placeholder').click(function(e) {
            $(this).next('.placeholder-input').focus();
        });

        var ubahn = [];
        if ($('#metrostations').length) {
            ubahn = $('#metrostations').data('name');
            if ( $('#order_subway_id').val().length ){
                var metroId = $('#order_subway_id').val()*1;
                for (var i in ubahn){
                    if ( (ubahn[i].val*1) === metroId) {
                        $('#order_address_metro').val(ubahn[i].label);
                    }
                }
            }
        }

        $( "#order_address_metro" )
            .autocomplete({
                source: ubahn,
                appendTo: '#metrostations',
                minLength: 2,
                select : function(event, ui ) {
                    $("#order_subway_id").val(ui.item.val);
                }
            })
            .change( function() {
                for(var i=0, l= ubahn.length; i<l; i++) {
                    if( $(this).val() === ubahn[i].label ) {
                        return true;
                    }
                }
                $(this).val('');
            });

        /* Processing Block */
        window.BlockScreen = function( text ) {
            $('<img src="/images/ajaxnoti.gif" />').css('display', 'none').appendTo('body'); //preload
            var noti = $('<div>').addClass('noti').html('<div><img src="/images/ajaxnoti.gif" /></br></br> '+ text +'</div>');
            noti.appendTo('body');
            this.block = function() {
                if( noti.is(':hidden') ) {
                    noti.lightbox_me({
                        centered:true,
                        closeClick:false,
                        closeEsc:false
                    });
                }
            };
            this.unblock = function() {
                noti.trigger('close');
            };
            this.bye = function() {
                noti.find('img').remove();
            };
        };
        Blocker = new BlockScreen('Ваш заказ оформляется');

        // общая сумма заказа
        var totalSum = 0;

        var handlePaymentMethods = function( sum ) {
            if ( sum > $('.mPayMethods').data('max-sum-online') ) {
                // webmoney
                $('#payment_method_11-field').hide();
                 // qiwi
                $('#payment_method_12-field').hide();
            }
            else {
                $('#payment_method_11-field').show();
                $('#payment_method_12-field').show();
            }
        }

        /* ---------------------------------------------------------------------------------------- */
        /* PUBSUB HANDLERS */
        /* Glue for architecure */
        PubSub.subscribe( 'DeliveryChanged', function( m, data ) {
            // $('#dlvrTypes .selectShop').show()

            if( data.type === 'courier') {
                $('#order-submit').removeClass('disable');
                $('#order-form').show();
                $('#addressField').show();
            } else {
                $('#addressField').hide();
                $('#order-form').hide();
                $('#order-submit').addClass('disable');
            }

            if( data.boxQuantity > 1 ) {
                // block payment options
                $('#payTypes > div').hide();
                $('#payment_method_1-field').show();
                $('#payment_method_2-field').show();
            } else {
                $('#payTypes > div').show();
                // $('#payment_method_5-field').show();
                // $('#payment_method_6-field').show();
            }

            handlePaymentMethods( totalSum );
        });

        PubSub.subscribe( 'ShopSelected', function( m, data ) {
            $('#orderMapPopup').trigger('close');
            $('#order-form').show();
            $('#order-submit').removeClass('disable');
            handlePaymentMethods( totalSum );
        });

        /* ---------------------------------------------------------------------------------------- */
        /* KNOCKOUT STUFF, MVVM PATTERN */
        var Model = $('#order-delivery_map-data').data('value');
        // Check Consistency TODO

        // analitycs
        var items_num = 0;
        var price = 0;
        var totalPrice = 0;
        var totalQuan = 0;
        var f1total = 0;
        var warrTotal = 0;

        $.each(Model.items, function(i, product){
            items_num += product.quantity;
            price += product.price;
            totalPrice += product.total;
            totalQuan += product.quantity;
            f1total += product.serviceQ;
            warrTotal += product.warrantyQ;
        });

        var toKISS = {
            'Checkout Step 1 SKU Quantity':totalQuan,
            'Checkout Step 1 SKU Total':price,
            'Checkout Step 1 F1 Quantity':f1total,
            'Checkout Step 1 Warranty Quantity':warrTotal,
            'Checkout Step 1 F1 Total':totalPrice - price,
            'Checkout Step 1 Order Total':totalPrice,
            'Checkout Step 1 Order Type':'cart order'
        };

        if ( typeof(_gaq) !== 'undefined' ) {
            _gaq.push(['_trackEvent', 'New order', 'Items', items_num]);
        }
        if ( typeof(_kmq) !== 'undefined' ) {
            _kmq.push(['record', 'Checkout Step 1', toKISS]);
        }

        function OrderModel() {
            var self = this;

            function thereIsExactPropertie( list, propertie, value ) {
                for ( var ind = 0, le = list.length; ind < le; ind++ ) {
                    if ( list[ind][ propertie ] === value ) {
                        return true;
                    }
                }

                return false;
            }

            function getIntervalsFromData( list, propertie, value ) { // here 'interval' mean Model , date linked
                var out = [];

                for ( var ind = 0, le = list.length; ind < le; ind++ ) {
                    if ( list[ind][ propertie ] === value ) {
                        for ( var key in list[ind].intervals ) {
                            out.push( 'c ' + list[ind].intervals[key].start_at + ' по '+ list[ind].intervals[key].end_at );
                        }
                        return out;
                    }
                    // return false
                }
                return false;
            }

            function buildTightInterval( edges ) {
                var tightInterval = edges[0];
                if( edges.length > 1 ) {
                    for(var i=1, l=edges.length; i<l; i++) {
                        if( edges[i][0] > tightInterval[0] ) {
                            tightInterval[0] = edges[i][0];
                        }
                        if( edges[i][1] < tightInterval[1] ) {
                            tightInterval[1] = edges[i][1];
                        }
                    }
                }
                return tightInterval;
            }

            function getMonday( pseudoMonday ) {
                var first = new Date( pseudoMonday );
                if( first.getDay() !== 1 ) {
                    //add before
                    var dbefore = (first.getDay()) ? first.getDay()*1 - 1 : 6;
                    first.setTime( first.getTime()*1 - dbefore*24*60*60*1000 );
                }
                return first;
            }

            function getSunday( pseudoSunday ) {
                var last = new Date( pseudoSunday );
                if( last.getDay() !== 0 ) {
                    //add after
                    last.setTime( last.getTime()*1 + (7 - last.getDay())*24*60*60*1000 );
                }
                return last;
            }

            self.cssForDate = $('.order-delivery_date').css('display');

            // Unavailables
            self.stolenItems = ko.observableArray([])
            // self.unavailable = ko.observable( false )
            if( Model.unavailable.length ) {

            }

            self.showForm = ko.observable( false );
            self.dlvrCourierEnable = ko.observable( false );
            self.dlvrShopEnable = ko.observable( false );

            // Boxes
            self.chosenBox = ko.observable(null);
            self.step2 = ko.observable( false );
            self.dlvrBoxes = ko.observableArray([]);

            function reTimestamping(date){//date is string
                var y = date.substr(0,4)*1;
                var m = date.substr(5,2)*1;
                var d = date.substr(8,2)*1;
                var newDate = new Date (y, (m-1), d);
                var timestamp = Date.parse(newDate);
                return timestamp;
            }

            function calculateDates( box ) {

                // Algorithm for Dates Compilation
                // divided into 4 steps:
                box.caclDates = [];
                var bid = box.token;
                // 0) There are some intervals
                var edges = [];
                for(var i=0, l=box.itemList().length; i<l; i++) {
                    //re-timestamping
                    for (var j in box.itemList()[i].deliveries[bid].dates){
                        box.itemList()[i].deliveries[bid].dates[j].timestamp = reTimestamping(box.itemList()[i].deliveries[bid].dates[j].value);
                    }
                    var dates = box.itemList()[i].deliveries[bid].dates;
                    edges.push( [ dates[0], dates[ dates.length - 1 ] ] );
                }
                // 1) Build Tight Interval
                var tightInterval = buildTightInterval( edges );
                // 2) Make Additional Dates
                var first = getMonday( tightInterval[0].timestamp );
                var last = getSunday( tightInterval[1].timestamp );
                //console.info( 'Interval edges for ', bid, ' :', first, last )


                // 3) Make Dates By T Interval
                var doweeks = ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'];
                var nweeks = 1;

                while ( first.getTime()*1 <= last.getTime() * 1 ) {
                    var linerDate = {
                        dayOfWeek: doweeks[ first.getDay() * 1 ],
                        day: first.getDate()*1,
                        tstamp: first.getTime()*1,
                        week: nweeks,
                        enable: ko.observable( false )
                    };

                    if( !first.getDay() ) {
                        nweeks++;
                    }

                    box.caclDates.push( linerDate );
                    linerDate = null;
                    first.setTime( first.getTime()*1 + 24*60*60*1000 );
                }

                box.nweeks = nweeks-1;
                // 4) Loop
                

up:             for( var linedate in box.caclDates ) { // Loop for T Interval
                    var dates = [];

                    for ( var i = 0, l = box.itemList().length; i < l; i++ ) { // Loop for all intervals
                        var bid = box.token;

                        dates = box.itemList()[i].deliveries[bid].dates;

                        if ( ! thereIsExactPropertie( dates, 'timestamp', box.caclDates[linedate].tstamp ) ) {


                            box.caclDates[linedate].enable( false );

                            continue up;
                        }

                        box.caclDates[linedate].enable( true );
                    }
                    //add intervals ATTENTION : NO COMPILATION FOR INTERVALS

                    var intervals = getIntervalsFromData( dates, 'timestamp', box.caclDates[linedate].tstamp );

                    box.caclDates[linedate].intervals = intervals;

                }

            } // fn calculateDates

            function addBox ( type, token, items, shop ) {
                var box = {}; //Model.deliveryTypes[tkn]
                box.type = type;
                box.token = token;
                box.curWeek = ko.observable(1);
                box.itemList = ko.observableArray([]);
                for( var prdct in items ) {
                    box.itemList.push( Model.items[ items[prdct] ] );
                }
                box.shop = ko.observable( shop );
                // console.info(box.curWeek())
                calculateDates(box);
                // Calc Chosen Date

                box.chosenDate = ko.observable(0);
                box.chosenInterval = ko.observable('none');
                box.currentIntervals = ko.observableArray([]);
                // console.log(box.caclDates)
                var i = 0;

                for( var linedate in box.caclDates ) { // Chosen Date is the first enabled
                    i++;
                   
                    if ( box.caclDates[linedate].enable() ) {
                        box.chosenDate( box.caclDates[linedate].tstamp );
                        box.chosenInterval( box.caclDates[linedate].intervals[0] );
                        for( var key in box.caclDates[linedate].intervals ) {
                            box.currentIntervals.push( box.caclDates[linedate].intervals[key] );
                        }
                        break;
                    }
                    if (i === 7) {
                        i = 0;
                        box.curWeek( box.curWeek() + 1 );
                    }

                }
                // console.info(box.chosenDate( box.caclDates[linedate].tstamp ) )
                box.dlvrPrice  = ko.computed(function() {
                    var out = 0;
                    var bid = this.token;
                    for(var i=0, l=this.itemList().length; i<l; i++) {
                        var itemDPrice = this.itemList()[i].deliveries[bid].price;
                        if( itemDPrice > out ) {
                            out = itemDPrice;
                        }
                    }
                    return out;
                }, box);

                box.supplied = ko.computed(function(){
                    var out = 0;
                    var bid = this.token;
                    for(var i=0, l=this.itemList().length; i<l; i++) {
                        if (this.itemList()[i].deliveries[bid].isSupplied) {
                            return this.itemList()[i].deliveries[bid].isSupplied;
                        }
                    }
                    return out;
                }, box);

                box.totalPrice  = ko.computed(function() {
                    var out = 0;
                    for(var i=0, l=this.itemList().length; i<l; i++) {
                        out += this.itemList()[i].total;
                    }
                    out += this.dlvrPrice()*1;
                    return out;
                }, box);
                self.dlvrBoxes.push( box );
            } // mth addBox

            function fillUpBoxesFromModel() {
                self.dlvrBoxes.removeAll();
                for( var tkn in Model.deliveryTypes ) {
                    if( Model.deliveryTypes[tkn].items.length ) {
                        addBox ( Model.deliveryTypes[tkn].type, Model.deliveryTypes[tkn].token, Model.deliveryTypes[tkn].items, Model.deliveryTypes[tkn].shop );
                    }
                }
            }
            fillUpBoxesFromModel();

            self.shopsInPopup = ko.observableArray( [] );

            function fillUpShopsFromModel() {
                self.shopsInPopup.removeAll();
                for( var key in Model.deliveryTypes ){
                    if (Model.deliveryTypes[key].shop){
                        self.shopsInPopup.push( Model.deliveryTypes[key].shop );
                    }
                }
            }

            fillUpShopsFromModel();

            self.chosenShop = ko.observable(null);

            self.shopButtonEnable = ko.observable( false );

            self.changeWeek = function( direction, data, e ) {
                if( direction > 0 ) {
                    if( data.nweeks === data.curWeek() ) {
                        return;
                    }
                    data.curWeek( data.curWeek() + 1 );
                }
                if( direction < 0 ) {
                    if( data.curWeek() === 1 ) {
                        return;
                    }
                    data.curWeek( data.curWeek() - 1 );
                }
            };

            self.clickDate = function( box, d, e ) {
                if( !d.enable() ) {
                    return;
                }
                var prevDay = box.chosenDate();
                box.chosenDate( d.tstamp );
                var nowDay = box.chosenDate();
                var delta = (nowDay - prevDay)/60/60/24/1000;
                if (typeof(_gaq) !== 'undefined') {
                    _gaq.push(['_trackEvent', 'Order card', 'Date changed', delta]);
                }
                box.currentIntervals.removeAll();
                for( var key in d.intervals ) {
                    box.currentIntervals.push( d.intervals[key] );
                }
                if( !$.inArray( box.chosenInterval(), box.currentIntervals() ) ) {
                    box.chosenInterval( box.currentIntervals()[0] );
                }
            };

            self.clickInterval = function( box, d, e ) {
                box.chosenInterval( d );
            };

            self.deleteItem = function( box, d, e ) {
                // ajax del
                $.get( d.deleteUrl, function(){
                    // Analitycs

                    toKISS_del = {
                        'Checkout Step 1 SKU Quantity':d.quantity,
                        'Checkout Step 1 SKU Total':d.price,
                        'Checkout Step 1 F1 Quantity':d.serviceQ,
                        'Checkout Step 1 Warranty Quantity':d.warrantyQ,
                        'Checkout Step 1 F1 Total':d.total - d.price,
                        'Checkout Step 1 Order Total':box.totalPrice() - d.total
                    };

                    if ( typeof(_kmq) !== 'undefined' ) {
                        _kmq.push(['set', toKISS_del]);
                    }

                    if ( typeof(_gaq) !== 'undefined' ) {
                        _gaq.push(['_trackEvent', 'Order card', 'Item deleted']);
                    }
                    // drop from box
                    box.itemList.remove( d );

                    if ( !box.itemList().length ) {
                        self.dlvrBoxes.remove( box );
                    }

        l2:         for ( var i in Model.deliveryTypes ) {
                        var tmpDlvr = Model.deliveryTypes[i];

                        for ( var j=0, l=tmpDlvr.items.length; j<l; j++) {
                            if ( tmpDlvr.items[j] === d.token ) {
                                tmpDlvr.items.splice( j, 1 );
                                break l2;
                            }
                        }
                    }
                    // console.info(Model);
                    // check if no items in boxes
                    if( !self.dlvrBoxes().length ) {
                        // refresh page -> server redirect to empty cart
                        document.location.reload();
                    }
                    else {
                        handlePaymentMethods( self.totalSum() )
                    }

                } );
            };

            self.totalSum = ko.computed( function() {
                var out = 0;
                for(var i=0, l = self.dlvrBoxes().length; i<l; i++) {
                    out += self.dlvrBoxes()[i].totalPrice() * 1;
                }
                return out;
            }, this);

            self.pickCourier = function() {
                nowDelivery = 'standart';
                fillUpBoxesFromModel();
                self.step2( true );
                self.shopButtonEnable( false );
                var data = {
                    'type': 'courier',
                    'boxQuantity': self.dlvrBoxes().length
                };
                totalSum = self.totalSum();
                PubSub.publish( 'DeliveryChanged', data );
            };

            self.pickShops = function() {
                nowDelivery = 'self';
                self.step2( false );
                self.shopButtonEnable( true );
                var data = {
                    'type': 'shops',
                    'boxQuantity': self.dlvrBoxes().length
                };
                PubSub.publish( 'DeliveryChanged', data );
            };

            self.showShopPopup = function( box, d, e ) {
                self.chosenBox( box );
                var shopIds = []; // all the shops for chosen box
                for( var i=0, l=box.itemList().length; i<l; i++ ) {
                    var itemDlvrs = box.itemList()[i].deliveries;
                    for( var key in itemDlvrs ) {
                        if( key.match('self_')) {
                            shopIds.push( key.replace('self_','')*1);
                        }
                    }
                }

                fillUpShopsFromModel();
                for( var j=0; j<self.shopsInPopup().length; ) {
                    if( $.inArray( self.shopsInPopup()[j].id , shopIds ) === -1 ){
                        self.shopsInPopup.remove( self.shopsInPopup()[j] );
                    } else {
                        j++;
                    }
                }
            };

            self.showAllShops = function() {
                self.shopsInPopup.removeAll();
                for( var key in Model.deliveryTypes ){
                    if (Model.deliveryTypes[key].shop){
                        self.shopsInPopup.push( Model.deliveryTypes[key].shop );
                    }
                }
            };

            self.selectShop = function( d ) {
                if( self.step2() ) {
                    /* Select Shop in Box */
                    var newboxes = [{ shop: self.chosenBox().token.replace('self_','') , items: [] }, { shop: d.id , items: [] } ]
                    // remove items, which has picked shop
    upi:            for( var item = 0, boxitems = self.chosenBox().itemList(); item < boxitems.length;  ) { //TODO refact
                        for( var dl in boxitems[item].deliveries ){
                            if( dl === 'self_'+d.id ) {
                                newboxes[1].items.push( boxitems[item].token )
                                self.chosenBox().itemList.remove( boxitems[item] )
                                continue upi
                            }
                        }
                        newboxes[0].items.push( boxitems[item].token )
                        self.chosenBox().itemList.remove( boxitems[item] )
                        item++
                    }
                            
                    // create new box for such items and for old box
                    for( var nbox in newboxes ) {
                        if( newboxes[nbox].items.length > 0 ) {
                            var argshop = Model.shops[ newboxes[nbox].shop ];
                            addBox ( 'self', 'self_'+newboxes[nbox].shop, newboxes[nbox].items, argshop );
                        }
                    }
                    // clear this box if it should be
                    if( ! self.chosenBox().itemList().length ) { // always
                        self.dlvrBoxes.remove( self.chosenBox() );
                    }

                } else {
                    /* Select Shop at Zero Step */
                    // pushing into box items which have selected shop
                    var selectedShopBoxShops = [ { shop: d.id, items: [] } ];

                    for( var box in self.dlvrBoxes() ) {
                        var procBox = self.dlvrBoxes()[box];

                        for ( var item = 0; item < procBox.itemList().length; ) {
                            if ( 'self_'+d.id in procBox.itemList()[item].deliveries ) {
                                // if ( procBox.itemList()[item].deliveries['self_'+d.id].dates.length > 1 ) {
                                    selectedShopBoxShops[0].items.push( procBox.itemList()[item].token );
                                // }
                                // else { // items which are 'one day' reserve-only
                                        // selectedShopBoxShops.push( { shop: d.id, items: [ procBox.itemList()[item].token ] } );
                                // }
                                procBox.itemList.remove( procBox.itemList()[item] )
                            }
                            else {
                                item++;
                            }
                        }
                    }
    // console.info(selectedShopBoxShops);
                    // separate 'courier-only' from self-available
                    // get self-available as a hash
                    var data = {},
                        tmpv = [];
                    for( var box in self.dlvrBoxes() ) {
                        var procBox = self.dlvrBoxes()[box];
    // console.info(procBox.itemList());
                        for( var item =0, list = procBox.itemList(); item < list.length;  ) {
                            tmpv = [];
                            for( var tkn in list[item].deliveries ) {
                                if( list[item].deliveries[ tkn ].token.match( 'self_' ) ) {
                                    tmpv.push( list[item].deliveries[ tkn ].token.replace( 'self_', '' ) );
                                }
                            }
    // console.info( list[item].token , tmpv );
                            data[ list[item].token +'' ] = tmpv;
                            if( !tmpv.length ) {
                                item++;
                            } else {
                                procBox.itemList.remove( procBox.itemList()[item] );
                            }
                        }
                    }
                    // distributive algorithm
                    var newboxes = DA( data );
                    for(var i=0, l=selectedShopBoxShops.length; i<l; i++) {
                        if( selectedShopBoxShops[i].items.length > 0 ) {
                            newboxes.push( selectedShopBoxShops[i] );
                        }
                    }
    // console.info( newboxes );
                    // build new self-boxes
                    for(var tkn in newboxes ) {
                        var argshop = Model.shops[ newboxes[tkn].shop ];
                        addBox ( 'self', 'self_'+newboxes[tkn].shop , newboxes[tkn].items, argshop );
                    }
                    // drop empty boxes
                    for( var box =0; box < self.dlvrBoxes().length;  ) {
                        if( ! self.dlvrBoxes()[box].itemList().length ) {
                            self.dlvrBoxes.remove( self.dlvrBoxes()[box] );
                        } else {
                            box++;
                        }
                    }

                    // interface
                    self.shopButtonEnable(false);
                    var data = {
                        'type': 'shops',
                        'boxQuantity': self.dlvrBoxes().length
                    };
                    for( var box in self.dlvrBoxes() ) {
                        if( self.dlvrBoxes()[box].type === 'standart' ) {
                            data.type = 'courier';
                            break;
                        }
                    }
                    totalSum = self.totalSum();
                    PubSub.publish( 'DeliveryChanged', data );
                }

                self.chosenShop( d );
                self.step2(true);
                totalSum = self.totalSum();
                PubSub.publish( 'ShopSelected', d );
            }

            self.printDate = function( tstamp ) {
                var d = new Date( tstamp );
                var rusMN = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
                return d.getDate() + ' ' + rusMN[ d.getMonth() ];
            };

            function formateDate( tstamp ) {

                var raw = new Date(tstamp),
                    m = raw.getMonth()+1,
                    d = raw.getDate()
                return raw.getFullYear() + '-' + ( m > 9 ? m : '0' + m ) + '-' + ( d > 9 ? d : '0' + d );
            }
            self.getServerModel = function() {
                var ServerModel = {
                    deliveryTypes: {}
                };

                    for( var tkn in self.dlvrBoxes() ) {

                    var dlvr = self.dlvrBoxes()[tkn];

                    var dates = dlvr.itemList()[0].deliveries[dlvr.token].dates;
                    
                    var newToken = dlvr.token;
                    var newType = dlvr.type;
                    var newId = Model.deliveryTypes[ dlvr.token ].id;

                    for (var i = 0, len = dates.length; i < len; i++){
                        if ( (dates[i].timestamp === dlvr.chosenDate()) && dates[i].isNow){
                            newToken = 'now'+dlvr.token.replace('self','');
                            newType = 'now';
                            newId = 4;
                        }
                    }

                    var data = {
                        id: newId,
                        token: newToken,
                        type: newType,
                        date: formateDate( dlvr.chosenDate() ),
                        interval: dlvr.chosenInterval().match(/\d{2}:\d{2}/g).join(','),
                        shop: {
                            id: dlvr.token.replace('self_','')
                        }
                    };
                    var boxitems = [];
                    for( var i in dlvr.itemList() ) {
                        boxitems.push( dlvr.itemList()[i].token );
                    }
                    data.items = boxitems;
                    ServerModel.deliveryTypes[ newToken + '_' + formateDate( dlvr.chosenDate() ) + '_' + dlvr.itemList()[0].id ] = data;
                }
                return ServerModel;
            };

            // set delivery types on the top
            self.showForm(true);
            var endTimePreOrder = new Date().getTime();
            var timeSpentPreOrder = endTimePreOrder - startTime;
            // console.info(timeSpent)
            if (typeof(_gaq) !== 'undefined') {
                _gaq.push(['_trackTiming', 'New order', 'JS response', timeSpentPreOrder])
            }
            for( var tkn in Model.deliveryTypes ) {
                if( Model.deliveryTypes[tkn].type === 'standart' ) {
                    self.dlvrCourierEnable(true);
                } else {
                    self.dlvrShopEnable(true);
                }
            }
            if( self.dlvrCourierEnable() && ! self.dlvrShopEnable() ) {
                self.pickCourier();
            }
            if( ! self.dlvrCourierEnable() && self.dlvrShopEnable() ) {
                self.pickShops();
            }

        } // OrderModel object


        function keyMaxLong( hash ) {
            var max = 0;
            var keyM = 'none';
            for(var key in hash ) {
                if( hash[key].length > max ) {
                    max = hash[key].length;
                    keyM = key;
                }
            }
            return keyM;
        }

        function DA( data ) {
    // console.info('DA b');
            // e.g. data = {
            //  'pr1' : [ '13', '2' ],
            //  'pr2' : [ '13', '2' ],
            //  'pr3' : [ '3', '2' ],
            //  'pr4' : [ '14' ]
            // };
            var out = [];
            while( true ) {
                var shop_items = {},
                    le = 0;
                for( var tkn in data ) {
                    for( var i=0, l=data[tkn].length; i<l; i++ ) {
                        if( !shop_items[ data[tkn][i] ] ) {
                            shop_items[ data[tkn][i] ] = [tkn];
                            le++;
                        } else {
                            shop_items[ data[tkn][i] ].push(tkn);
                        }
                    }
                }
                if( !le ) {
                    break
                }
    // console.info(shop_items);

                var keyMax = keyMaxLong( shop_items );
                out.push( { 'shop': keyMax, 'items': shop_items[ keyMax ] } );
                for( var tkn in shop_items[ keyMax ] ) {
                    data[ shop_items[ keyMax ][tkn] ] = [];
                }


            }
    // console.info(out, data)      
    // console.info('DA e')     
            return out
        }
        /* ---------------------------------------------------------------------------------------- */
        /*  Send Data */
        var form = $('#order-form');
        var sended = false;
        var broken = 0;

        function markError( field, mess ) {
            broken++;
            $('body').delegate('input[name="'+field+'"]', 'change', function() {
                broken--;
                $('input[name="'+field+'"]').removeClass('mRed');
                $('input[name="'+field+'"]').prev('.placeholder').removeClass('mRed');
                var line = $('input[name="'+field+'"]').closest('.bBuyingLine');
                if( !line.find('.mRed').length ) {
                    line.find('.bFormError').remove();
                }
            })
            var node = $('input[name="'+field+'"]:first');
            if( node.hasClass('mRed') ) {
                return;
            }
            switch( node.attr('type') ) {
                case 'text':
                    node.addClass('mRed');
                    node.prev('.placeholder').addClass('mRed');
                    var dd = node.parent().parent();
                    if( !dd.find('.bFormError').length ) {
                        dd.append( '<span class="bFormError mb10 pt5">'+mess+'</span>' ); // AWARE: CUSTOM
                    }
                    break;
                default: // radio, checkbox
                    node.addClass('mRed');
                    node.prev('.placeholder').addClass('mRed');
                    node.parent().parent().parent().append( '<span class="bFormError mb10 pt5">'+mess+'</span>' ); // AWARE: CUSTOM
                    break;
            }
        }

        function printErrors( errors ) {
            for( var inp in errors ) {
                markError( inp, errors[inp] );
            }
            if( broken > 0 ) {
                $.scrollTo( '.mRed:first' , 500 );
            }
        }

        $('#order-submit').click( function(e) {
            broken = 0;
            e.preventDefault();

            if( sended ) {
                return; // form is currently processing
            }

            if( $(this).hasClass('disable')) { // form isnot active - delivery should be chosen
                return false;
            }

            // Validation
            var serArray = form.serializeArray();

            var fieldsToValidate = $('#order-validator').data('value');
            flds:   for( field in fieldsToValidate ) {
                if( !form.find('[name="'+field+'"]:visible').length ) {
                    continue;
                }

                if (field=='order[recipient_phonenumbers]'){
                    var phoneVal = $('#order_recipient_phonenumbers').val();
                    if ( phoneVal.length < 10){
                        markError(field, 'Маловато цифр');
                    }
                }

                if (field=='order[qiwi_phone]'){
                    var phoneVal = $('#qiwi_phone').val();
                    if ( phoneVal.length < 10){
                        markError(field, 'Маловато цифр');
                    }
                }

                if (field=='order[recipient_email]') {
                    var emailVal = $('#order_recipient_email').val();

                    if( !emailVal && !$('input[name="order[one_click]"]').length ) {
                        if( $('#order_recipient_email').hasClass('abtestRequired') ) {
                            markError(field, 'Укажите ваш e-mail');
                        }
                    } else if ( (emailVal.length > 0) && (emailVal.search('@') == -1)){
                        markError(field, 'Некорректный e-mail');
                    }
                }

                for( var i = 0, l = serArray.length; i < l; i++ ) {
                    if( serArray[i].name === field ) {
                        if( (serArray[i].value == '') && (field != 'order[recipient_email]') ) {
                            markError( field, fieldsToValidate[field] ); // cause is empty
                        }

                        continue flds;
                    }
                }
                markError( field, fieldsToValidate[field] ); // cause not in serArray
            }

            if( broken > 0 ) {
                $.scrollTo( '.mRed:first' , 500 );
                return;
            }

            // Show Rounder
            var button = $(this);
            button.text('Оформляется...');
            Blocker.block();


            var showOrderAlert = function(msg, redirect){
                var id = 'orderAlert';
                var block = '<div id="'+id+'" class="popup">' +
                    '<div class="popupbox width290">' +
                    '<div class="font18 pb18"> '+msg+'</div>'+
                    '</div>' +
                    '<p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>'
                '</div> ';
                $('body').append( $(block) );
                $('#'+id).lightbox_me({
                    centered: true,
                    closeSelector: ".closePopup",
                    onClose: function(){
                        window.location = redirect
                    }
                });
            };

            // Prepare Data & Send
            sended = true
            var toSend = form.serializeArray()
            var type_id = $('label.mChecked input[name="order[delivery_type_id]"]').val()
            if( !type_id ) {
                type_id = $('input[name="order[delivery_type_id]"]').val()
            }
            toSend.push( { name: 'order[delivery_type_id]', value: type_id })
            toSend.push( { name: 'delivery_map', value: JSON.stringify( MVM.getServerModel() )  } )//encodeURIComponent

            if( typeof(SertificateCard) !== 'undefined' ) {
                if( SertificateCard.isActive() ) {
                    toSend.push( { name: 'order[card]', value: SertificateCard.getCode() })
                    toSend.push( { name: 'order[pin]', value: SertificateCard.getPIN() })
                }
            }
            var startAjaxOrderTime = new Date().getTime()

            $.ajax({
                url: form.attr('action'),
                timeout: 120000,
                type: "POST",
                data: toSend,
                success: function( data ) {
                    sended = false;
                    if( !data.success ) {
                        Blocker.unblock();
                        button.text('Завершить оформление');
                        if( 'errors' in data ){
                            printErrors( data.errors );
                        }
                        // TODO display data.error info
                        return;
                    }

                    // analitycs
                    if (typeof(_gaq) !== 'undefined') {
                        for (var i in MVM.getServerModel().deliveryTypes){
                            var tmpLog = 'выбрана '+nowDelivery+' доставят '+MVM.getServerModel().deliveryTypes[i].type;
                            _gaq.push(['_trackEvent', 'Order card', 'Completed', tmpLog]);
                            suborders_num++;
                        }
                        _gaq.push(['_trackEvent', 'Order complete', suborders_num, items_num]);
                        var endAjaxOrderTime = new Date().getTime();
                        var AjaxOrderSpent = endAjaxOrderTime - startAjaxOrderTime;
                        _gaq.push(['_trackTiming', 'Order complete', 'DB response', AjaxOrderSpent]);
                    }

                    // var phoneNumber = '8' + $('#order_recipient_phonenumbers').val().replace(/\D/g, "");
                    // var emailVal = $('#order_recipient_email').val();

                    // /**
                    //  * Стоимость доставки
                    //  * @type {Number}
                    //  */
                    // var dlvr_total = 0;

                    // $.each(MVM.dlvrBoxes(), function(i, product){
                    //     dlvr_total += product.dlvrPrice()
                    // });

                    // /**
                    //  * количество товаров
                    //  * @type {Number}
                    //  */
                    // var itemQ = 0,
                    // /**
                    //  * Стоимость всех товаров
                    //  * @type {Number}
                    //  */
                    //     itemT = 0,
                    // /**
                    //  * Количество услуг
                    //  * @type {Number}
                    //  */
                    //     servQ = 0,
                    // /**
                    //  * Стоимость всех услуг
                    //  * @type {Number}
                    //  */
                    //     servT = 0,
                    // /**
                    //  * Количество расширенных гарантий
                    //  * @type {Number}
                    //  */
                    //     warrQ = 0,
                    // /**
                    //  * Стоимость всех расширенных гарантий
                    //  * @type {Number}
                    //  */
                    //     warrT = 0;
                    // //end of vars

                    // for ( var tkn in MVM.dlvrBoxes() ) {
                    //     var dlvr = MVM.dlvrBoxes()[tkn];

                    //     for ( var i in dlvr.itemList() ) {
                    //         itemQ += dlvr.itemList()[i].quantity;
                    //         itemT += dlvr.itemList()[i].price;
                    //         servQ += dlvr.itemList()[i].serviceQ;
                    //         servT += dlvr.itemList()[i].serviceTotal;
                    //         warrQ += dlvr.itemList()[i].warrantyQ;
                    //         warrT += dlvr.itemList()[i].warrantyTotal;
                    //     }
                    // }

                    // var toKISS_complete = {
                    //     'Checkout Complete Order ID':data.orderNumber,
                    //     'Checkout Complete SKU Quantity':itemQ,
                    //     'Checkout Complete SKU Total':itemT,
                    //     'Checkout Complete F1 Quantity':servQ,
                    //     'Checkout Complete F1 Total':servT,
                    //     'Checkout Complete Warranty Quantity':warrQ,
                    //     'Checkout Complete Warranty Total':warrT,
                    //     'Checkout Complete Order Subtotal':itemT + servT + warrT,
                    //     'Checkout Complete Delivery Total':parseInt(dlvr_total),
                    //     'Checkout Complete Order Total':MVM.totalSum(),
                    //     'Checkout Complete Order Type':'cart order',
                    //     'Checkout Complete Delivery':nowDelivery,
                    //     'Checkout Complete Payment':data.paymentMethodId
                    // };

                    // if ( (typeof(_kmq) !== 'undefined') && (KM !== 'undefined') ) {
                    //     _kmq.push(['alias', phoneNumber, KM.i()]);
                    //     _kmq.push(['alias', emailVal, KM.i()]);
                    //     _kmq.push(['identify', phoneNumber]);
                    //     _kmq.push(['record', 'Checkout Complete', toKISS_complete]);
                    // }
                    

                    var newTkn = 0;

                    // for sociomantic
                    // https://jira.enter.ru/browse/SITE-1475
                    window.sonar_basket = {
                        products: [],
                        transaction: data.orderNumber,
                        amount: MVM.totalSum(),
                        currency:'RUB'
                    };

                    for ( newTkn in MVM.dlvrBoxes() ) {
                        var dlvr = MVM.dlvrBoxes()[newTkn];

                        for ( var i in dlvr.itemList() ) {
                            var item = dlvr.itemList()[i];

                            var toSociomantic = {
                                identifier: item.article+'_'+window.docCookies.getItem('geoshop'),
                                amount: item.price,
                                currency: 'RUB',
                                quantity: item.quantity
                            }

                            window.sonar_basket.products.push(toSociomantic);

                            if ( (typeof(_kmq) !== 'undefined') && (KM !== 'undefined') ) {
                                var toKISS_pr =  {
                                    'Checkout Complete SKU':item.article,
                                    'Checkout Complete SKU Quantity':item.quantity,
                                    'Checkout Complete SKU Price':item.price,
                                    'Checkout Complete F1 Quantity':item.serviceQ,
                                    'Checkout Complete F1 Total':item.serviceTotal,
                                    'Checkout Complete Warranty Quantity':item.warrantyQ,
                                    'Checkout Complete Warranty Total':item.warrantyTotal,
                                    'Checkout Complete Parent category':item.parent_category,
                                    'Checkout Complete Category name':item.category,
                                    '_t':KM.ts() + newTkn + i,
                                    '_d':1
                                };

                                _kmq.push(['set', toKISS_pr]);
                            }
                        }
                    }

                    if ( typeof(yaCounter10503055) !== 'undefined' ) {
                        yaCounter10503055.reachGoal('\orders\complete');
                    }

                    // Sociomantic
                    // https://jira.enter.ru/browse/SITE-1475
                    var sociomanticUrl = ( 'https:' === document.location.protocol ? 'https://' : 'http://' )+'eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru';

                    // перезагрузка страницы только после загрузки скрипта sociomantic
                    $LAB.script( sociomanticUrl ).wait(function() {
                        Blocker.bye();
                        if ( data.action.alert !== undefined ) {
                            showOrderAlert(data.action.alert.message, data.data.redirect);
                        }
                        else if ( 'redirect' in data.data ) {
                            window.location = data.data.redirect;
                        }
                    });
                },
                error: function() {
                    button.text('Попробовать еще раз')
                    Blocker.unblock()
                    sended = false
                }
            })

        })

        /* ---------------------------------------------------------------------------------------- */
        /* MAIN() */

    // console.info( 'MODEL ', Model )
        MVM = new OrderModel()
        ko.applyBindings( MVM , $('#MVVM')[0] )
        /* ---------------------------------------------------------------------------------------- */
        /* MAP REDESIGN */
        var shopList      = $('#mapPopup_shopInfo'),
            infoBlockNode = $('#map-info_window-container')
        //deprecated: shopsStack    = $('#order-delivery_map-data').data().value.shops

        function getShopsStack() {
            MVM.showAllShops();
            var shopsStack = {}
            for( var sh in MVM.shopsInPopup() ){
                shopsStack[ MVM.shopsInPopup()[sh].id ] = MVM.shopsInPopup()[sh]
            }
            return shopsStack
        }

        /* Shop Popup */
        $('#OrderView').delegate('.selectShop','click', function() {
            $('#orderMapPopup').lightbox_me({
                centered: true,
                onLoad: function() {
                    $('#mapPopup').empty()
                    shops = getShopsStack()
                    if (!$.isEmptyObject(shops)){
                        loadMap('yandex', shops)
                    }
                }
            })
            return false
        } )

        var hoverTimer = { 'timer': null, 'id': 0 }

        shopList.delegate('li', 'hover', function() {
            var id = $(this).attr('ref')//$(this).data('id')
            if( hoverTimer.timer ) {
                clearTimeout( hoverTimer.timer )
            }

            if( id && id != hoverTimer.id) {
                hoverTimer.id = id
                hoverTimer.timer = setTimeout( function() {
                    window.regionMap.showInfobox( id )
                }, 350)
            }
        })

        function updateI( marker ) {
            infoBlockNode.html( tmpl( 'mapInfoBlock', marker ))
            hoverTimer.id = marker.id
        }

        function ShopChoosed( node ) {
            var shopnum = $(node).parent().find('.shopnum').text()
            var shop = Model.shops[shopnum]
            MVM.selectShop( shop )
        }

        function loadMap(vendor, shopsStack){
            MapInterface.ready( vendor, {
                yandex: $('#mapInfoBlock'),
                google: $('#map-info_window-container')
            } )
            var mapCenter = calcMCenter( shopsStack )
            var mapCallback = function() {
                window.regionMap.showMarkers( shopsStack )
                window.regionMap.addHandler( '.shopchoose', ShopChoosed )
            }
            MapInterface.init( mapCenter, 'mapPopup', mapCallback, updateI )
        }

        $('#order_recipient_email').bind('keyup change load ready', trackEmailChange);
    })
}());
