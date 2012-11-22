$(document).ready(function() {
	/* ---------------------------------------------------------------------------------------- */
	/* COMMON DESIGN, BEHAVIOUR ONLY */

	/* Custom Selectors */ 
	$('body').delegate( '.bSelect', 'click', function() {
		if( $(this).hasClass('mDisabled') )
			return false
		$(this).find('.bSelect__eDropmenu').toggle()
	})
	$('body').delegate( '.bSelect', 'mouseleave', function() {
		if( $(this).hasClass('mDisabled') )
			return false
		var options = $(this).find('.bSelect__eDropmenu')
		if( options.is(':visible') )
			options.hide()
	})

	/*  Custom Checkboxes */
	$('body').delegate('.bBuyingLine label', 'click', function() {
		// e.stopPropagation()
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

	$('body').delegate('input[name="order[payment_method_id]"]', 'click', function() {
		$('.innerType').hide()
		$(this).parent().parent().find('.innerType').show()
	})

	/* Sertificate */
	if( $('.orderFinal__certificate').length ) {

        var code = $(".cardNumber"),
            pin = $(".cardPin"),
            sfields = $("#sertificateFields"),

            urlCheck = '/certificate-check'

        var SertificateCard = (function() {

            var
                paymentWithCard = $('#paymentWithCard').text()*1,
                checked = false,
                processTmpl = 'processBlock'

            function getCode() {
                return code.val().replace(/[^0-9]/g,'')
            } 
            function getPIN() {
                return pin.val().replace(/[^0-9]/g,'')
            }
            function getParams() {
                return { code: getCode() , pin: getPIN() }
            }
            function isActive() {
            	if( checked && ( getCode() !== '' ) && getCode().length === 14 && ( getPIN() !== '' ) && getPIN().length === 4)
            		return true
            	return false
            }

            function checkCard() {
                setProcessingStatus( 'orange', 'Проверка по номеру карты' )
                $.post( urlCheck, getParams(), function( data ) {
                    if( ! 'success' in data )
                        return false
                    if( !data.success ) {
                        var err = ( typeof(data.error) !== 'undefined' ) ? data.error : 'ERROR'
                        setProcessingStatus( 'red', err )
                        return false
                    }
                    setProcessingStatus( 'green', data.data )
                })       
                // pin.focus()
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
                sfields.after( tmpl( processTmpl, options) )
                if( typeof( data['activated'] ) !== 'undefined' )
                    $('.process').first().addClass('picked')
            }

            return {
                checkCard: checkCard,
                setProcessingStatus: setProcessingStatus,
                isActive: isActive,
                getCode: getCode,
                getPIN: getPIN
            }
        })(); // object SertificateCard , singleton

        code.mask("999 999 999 9999 9", { completed: function(){ pin.focus() }, placeholder: "*" } )
        pin.mask("9999", { completed: SertificateCard.checkCard, placeholder: "*" } )

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
        var banks = $('.bankWrap > .bSelect').data('value')
        var docs  = $('.bankWrap > .creditHref')
        var options = $('<div>').addClass('bSelect__eDropmenu')
        for( var id in banks ) {
            var option = $('<div>').attr('ref', id).append( $('<span>').text( banks[id].name ) )
            option.click( function() {
                var thisId = $(this).attr('ref')
                $('.bankWrap > .bSelect').find('span:first').text( banks[ thisId ].name )
                $('input[name="order[credit_bank_id]"]').val( thisId )
                docs.find('a').attr('href', banks[ thisId ].href )
                docs.find('span').text('(' + banks[ thisId ].name + ')' )
            })
            options.append( option )
        }
        $('.bankWrap > .bSelect').append( options )

        DirectCredit.init( $('#tsCreditCart').data('value'), $('#creditPrice') )
    }

	/* Auth Link */
	PubSub.subscribe( 'authorize', function( m, d ) {
		$('#order_recipient_first_name').val( d.first_name )
		$('#order_recipient_last_name').val( d.last_name )
		$('#order_recipient_phonenumbers').val( d.phonenumber + '' )
		$('#user-block').hide()
	})

    $('.auth-link').bind('click', function (e) {
        e.preventDefault()

        var link = $(this)

        $('#login-form, #register-form').data('redirect', false)
        $('#auth-block').lightbox_me({
            centered:true,
            onLoad:function () {
                $('#auth-block').find('input:first').focus()
            }
        })
    })
    /* Address Fields */
    // region changer (handler) describes in another file, its common call
    $("#order_recipient_phonenumbers").focusin(function(){
    	$(this).attr('maxlength','10')
    	$(this).bind('keyup',function(e){
			if (((e.which>=48)&&(e.which<=57))||(e.which==8)){//если это цифра или бэкспэйс
				//
			}
			else{
				//если это не цифра
				var clearVal = $(this).val().replace(/\D/g,'')
				$(this).val(clearVal)
			}
		})
    })

	if( typeof( $.mask ) !== 'undefined' ) {
		// $.mask.definitions['n'] = "[()0-9\ \-]"
		// $("#order_recipient_phonenumbers").mask("8nnnnnnnnnnnnnnnnn", { placeholder: " ", maxlength: 10 } )
		// var predefPhone = document.getElementById('order_recipient_phonenumbers').getAttribute('value')
  //       if( predefPhone && predefPhone != '' )
  //           $('#order_recipient_phonenumbers').val( predefPhone + '       ' )
  //       else   
  //           $("#order_recipient_phonenumbers").val('8')
        
        $.mask.definitions['*'] = "[0-9*]"
        $("#order_sclub_card_number").mask("* ****** ******", { placeholder: "*" } )
		if( $("#order_sclub_card_number")[0].getAttribute('value') )
			$("#order_sclub_card_number").val( $("#order_sclub_card_number")[0].getAttribute('value') )
		$("#order_sclub_card_number").blur( function() {
			if( $(this).val() === "* ****** ******" ) {
				$(this).trigger('unmask').val('')
				$(this).focus( function() {
					$("#order_sclub_card_number").mask("* ****** ******", { placeholder: "*" } )
				})
			}
        })	
	}
	
	// $('#addressField').find('input').placeholder()
	$('.placeholder-input').focus(function(e) {
        var el = $(e.target)
        if (!el.prev('.placeholder').hasClass('mRed'))
        	el.prev('.placeholder').css('border-color', '#FFA901');
    }).focusout(function(e) {
        var el = $(e.target)
        if (!el.prev('.placeholder').hasClass('mRed'))
        	el.prev('.placeholder').css('border-color', '#DDDDDD')
    })

    $('.placeholder').click(function(e) {
        $(this).next('.placeholder-input').focus();
    })
	
    var ubahn = []
    if ($('#metrostations') !== 'undefined') {
        ubahn = $('#metrostations').data('name')
    }
	$( "#order_address_metro" )
		.autocomplete({
			source: ubahn,
			appendTo: '#metrostations',
			minLength: 2,
            select : function(event, ui ) {
                $("#order_subway_id").val(ui.item.val)
            }
        })
		.change( function() {
			for(var i=0, l= ubahn.length; i<l; i++)
				if( $(this).val() === ubahn[i].label )
					return true
			$(this).val('')
		})
	
	/* Processing Block */
	window.blockScreen = function( text ) {
		$('<img src="/images/ajaxnoti.gif" />').css('display', 'none').appendTo('body') //preload
		var noti = $('<div>').addClass('noti').html('<div><img src="/images/ajaxnoti.gif" /></br></br> '+ text +'</div>')
        noti.appendTo('body')
        this.block = function() {
        	if( noti.is(':hidden') )
			noti.lightbox_me({
				centered:true,
				closeClick:false,
				closeEsc:false
			})
		}
		this.unblock = function() {
			noti.trigger('close')
		}
		this.bye = function() {
			noti.find('img').remove()
		}
	}
	Blocker = new blockScreen('Ваш заказ оформляется')	

	/* ---------------------------------------------------------------------------------------- */
	/* PUBSUB HANDLERS */
	/* Glue for architecure */
	PubSub.subscribe( 'DeliveryChanged', function( m, data ) {	
		// $('#dlvrTypes .selectShop').show()	
		
		if( data.type === 'courier') {	
			$('#order-submit').removeClass('disable')
			$('#order-form').show()
			$('#addressField').show()
		} else {
			$('#addressField').hide()
			$('#order-form').hide()
			$('#order-submit').addClass('disable')
		}
		if( data.boxQuantity > 1 ) {
			// block payment options
			$('#payTypes > div').hide()
			$('#payment_method_1-field').show()
			$('#payment_method_2-field').show()
		} else {
			$('#payTypes > div').show()
			// $('#payment_method_5-field').show()
			// $('#payment_method_6-field').show()
		}

	})

	PubSub.subscribe( 'ShopSelected', function( m, data ) {
		$('#orderMapPopup').trigger('close')
		$('#order-form').show()
		$('#order-submit').removeClass('disable')
	})

	/* ---------------------------------------------------------------------------------------- */
	/* KNOCKOUT STUFF, MVVM PATTERN */

	var Model = $('#order-delivery_map-data').data('value')
	// Check Consistency TODO

	function OrderModel() {
		var self = this	

		function thereIsExactPropertie( list, propertie, value ) {								
			for(var ind=0, le = list.length; ind<le; ind++) {
				if( list[ind][ propertie ] == value )
					return true
			}
			return false
		}

		function getIntervalsFromData( list, propertie, value ) { // here 'interval' mean Model , date linked
			var out = []
			for(var ind=0, le = list.length; ind<le; ind++) {
				if( list[ind][ propertie ] == value ) {
					for( var key in list[ind].intervals )
						out.push( 'c ' + list[ind].intervals[key].start_at + ' по '+ list[ind].intervals[key].end_at )
					return out
				}
			}
			return false
		}

		function buildTightInterval( edges ) {
			var tightInterval = edges[0]
			if( edges.length > 1 )
				for(var i=1, l=edges.length; i<l; i++) {
					if( edges[i][0] > tightInterval[0] )
						tightInterval[0] = edges[i][0]
					if( edges[i][1] < tightInterval[1] )
						tightInterval[1] = edges[i][1]
				}
			return tightInterval
		}

		function getMonday( pseudoMonday ) {
			var first = new Date( pseudoMonday )
			if( first.getDay() !== 1 ) {
				//add before				
				var dbefore = (first.getDay()) ? first.getDay() - 1 : 6
				first.setTime( first.getTime()*1 - dbefore*24*60*60*1000 )
			}
			return first		
		}

		function getSunday( pseudoSunday ) {
			var last = new Date( pseudoSunday )
			if( last.getDay() !== 0 ) {
				//add after					
				last.setTime( last.getTime()*1 + (7 - last.getDay())*24*60*60*1000 )
			}			
			return last
		}

		self.cssForDate = $('.order-delivery_date').css('display')

		// Unavailables
		self.stolenItems = ko.observableArray([])
		// self.unavailable = ko.observable( false )
		if( Model.unavailable.length ) {

		}

		self.showForm = ko.observable( false )
		self.dlvrCourierEnable = ko.observable( false )
		self.dlvrShopEnable = ko.observable( false )

		// Boxes
		self.chosenBox = ko.observable(null)
		self.step2 = ko.observable( false )
		self.dlvrBoxes = ko.observableArray([])

		function calculateDates( box ) {
			// Algorithm for Dates Compilation
			// divided into 4 steps:
			box.caclDates = []
			var bid = box.token
			// 0) There are some intervals
			var edges = []		
			for(var i=0, l=box.itemList().length; i<l; i++) {
				var dates = box.itemList()[i].deliveries[bid].dates
				edges.push( [ dates[0], dates[ dates.length - 1 ] ] )
			}

			// 1) Build Tight Interval
			var tightInterval = buildTightInterval( edges )				
			
			// 2) Make Additional Dates				
			var first = getMonday( tightInterval[0].timestamp )				
			var last = getSunday( tightInterval[1].timestamp )
// console.info( 'Interval edges for ', bid, ' :', first, last )

			// 3) Make Dates By T Interval
			var doweeks = ['Вс','Пн','Вт','Ср','Чт','Пт','Сб']
			var nweeks = 1
			while( first.getTime() <= last.getTime() ) {
				var linerDate = {
					dayOfWeek: doweeks[ first.getDay() ],
					day: first.getDate(),
					tstamp: first.getTime()*1,
					week: nweeks,
					enable: ko.observable( false )
				}
				if( !first.getDay() )
					nweeks ++
				box.caclDates.push( linerDate )
				linerDate = null
				first.setTime( first.getTime()*1 + 24*60*60*1000 )
			}
			box.nweeks = nweeks-1

			// 4) Loop
up:				for( var linedate in box.caclDates ) { // Loop for T Interval
				var dates = []
				for(var i=0, l=box.itemList().length; i<l; i++) { // Loop for all intervals
					var bid = box.token
					dates = box.itemList()[i].deliveries[bid].dates
					if( ! thereIsExactPropertie( dates, 'timestamp', box.caclDates[linedate].tstamp ) ) {
						box.caclDates[linedate].enable( false )
						continue up
					}
					box.caclDates[linedate].enable( true )
				}
				//add intervals ATTENTION : NO COMPILATION FOR INTERVALS
				box.caclDates[linedate].intervals = getIntervalsFromData( dates, 'timestamp', box.caclDates[linedate].tstamp )
			}
		} // fn calculateDates

		function addBox ( type, token, items, shop ) {
			var box = {} //Model.deliveryTypes[tkn]
			box.type = type
			box.token = token
			box.curWeek = ko.observable(1)
			box.itemList = ko.observableArray([])
			for( var prdct in items ) {
				box.itemList.push( Model.items[ items[prdct] ] )
			}
			box.shop = ko.observable( shop )

			calculateDates(box)
			// Calc Chosen Date
			box.chosenDate = ko.observable(0)
			box.chosenInterval = ko.observable('none')
			box.currentIntervals = ko.observableArray([])

			for( var linedate in box.caclDates ) { // Chosen Date is the first enabled
				if( box.caclDates[linedate].enable() ) {
					box.chosenDate( box.caclDates[linedate].tstamp )
					box.chosenInterval( box.caclDates[linedate].intervals[0] )
					for( var key in box.caclDates[linedate].intervals )
						box.currentIntervals.push( box.caclDates[linedate].intervals[key] )
					break
				}
			}

			box.dlvrPrice  = ko.computed(function() {
				var out = 0
				var bid = this.token
				for(var i=0, l=this.itemList().length; i<l; i++) {
					var itemDPrice = this.itemList()[i].deliveries[bid].price
					if( itemDPrice > out )
						out = itemDPrice
				}
				return out
			}, box)

			box.totalPrice  = ko.computed(function() {	
				var out = 0
				for(var i=0, l=this.itemList().length; i<l; i++)
					out += this.itemList()[i].total
					out += this.dlvrPrice()*1
				return out
			}, box)

			self.dlvrBoxes.push( box )
		} // mth addBox

		function fillUpBoxesFromModel() {
			self.dlvrBoxes.removeAll()
			for( var tkn in Model.deliveryTypes ) {
				if( Model.deliveryTypes[tkn].items.length ) {				
					addBox ( Model.deliveryTypes[tkn].type, Model.deliveryTypes[tkn].token, Model.deliveryTypes[tkn].items, Model.deliveryTypes[tkn].shop )
				}
			}
		}
		fillUpBoxesFromModel()
		
		self.shopsInPopup = ko.observableArray( [] )

		function fillUpShopsFromModel() {
			self.shopsInPopup.removeAll()
			for( var key in Model.shops ){
				self.shopsInPopup.push( Model.shops[key] )
			}
		}

		fillUpShopsFromModel()

		self.chosenShop = ko.observable(null)

		self.shopButtonEnable = ko.observable( false )

		self.changeWeek = function( direction, data, e ) {
			if( direction > 0 ) {
				if( data.nweeks == data.curWeek() )
					return	
				data.curWeek( data.curWeek() + 1 )		
			}
			if( direction < 0 ) {
				if( data.curWeek() == 1 )
					return
				data.curWeek( data.curWeek() - 1 )		
			}
		}

		self.clickDate = function( box, d, e ) {
			if( !d.enable() ) 
				return
			box.chosenDate( d.tstamp )
			box.currentIntervals.removeAll()
			for( var key in d.intervals )
				box.currentIntervals.push( d.intervals[key] )
			if( !$.inArray( box.chosenInterval(), box.currentIntervals() ) )
				box.chosenInterval( box.currentIntervals()[0] )
		}

		self.clickInterval = function( box, d, e ) {
			box.chosenInterval( d )
		}

		self.deleteItem = function( box, d, e ) {
			// ajax del 
			$.get( d.deleteUrl, function(){ } )
			// drop from box
			box.itemList.remove( d )
			if( !box.itemList().length )
				self.dlvrBoxes.remove( box )
l2:			for(var i in Model.deliveryTypes) {
				var tmpDlvr = Model.deliveryTypes[i]
				for(var j=0, l=tmpDlvr.items.length; j<l; j++) {
					if( tmpDlvr.items[j] === d.token ) {
						tmpDlvr.items.splice( j, 1 )
						break l2
					}
				}
			}
// console.info(Model)
			// check if no items in boxes
			if( !self.dlvrBoxes().length ) {
			// refresh page -> server redirect to empty cart
				document.location.reload()
			}
		}

		self.totalSum = ko.computed( function() {
			var out = 0
			for(var i=0, l = self.dlvrBoxes().length; i<l; i++) {		
				out += self.dlvrBoxes()[i].totalPrice() * 1
			}
			return out
		}, this)

		self.pickCourier = function() {
			fillUpBoxesFromModel()
			self.step2( true )
			self.shopButtonEnable( false )
			var data = {
				'type': 'courier',
				'boxQuantity': self.dlvrBoxes().length
			}		
			PubSub.publish( 'DeliveryChanged', data )
		}

		self.pickShops = function() {
			self.step2( false )
			self.shopButtonEnable( true )
			var data = {
				'type': 'shops',
				'boxQuantity': self.dlvrBoxes().length
			}
			PubSub.publish( 'DeliveryChanged', data )
		}

		self.showShopPopup = function( box, d, e ) {
			self.chosenBox( box )
			var shopIds = [] // all the shops for chosen box
			for( var i=0, l=box.itemList().length; i<l; i++ ) {
				var itemDlvrs = box.itemList()[i].deliveries
				for( var key in itemDlvrs ) {
					if( key.match('self_') )
						shopIds.push( key.replace('self_','') )
				}
			}

			fillUpShopsFromModel()	
			for( var i=0; i<self.shopsInPopup().length; ) {
				if( $.inArray( self.shopsInPopup()[i].id , shopIds ) === -1 )
					self.shopsInPopup.remove( self.shopsInPopup()[i] )
				else
					i++
			}
		}

		self.showAllShops = function() {
			self.shopsInPopup.removeAll()
			for( var key in Model.shops ){
				self.shopsInPopup.push( Model.shops[key] )
			}
		}

		self.selectShop = function( d ) {
			
			if( self.step2() ) {
				/* Select Shop in Box */
				var newboxes = [{ shop: self.chosenBox().token.replace('self_','') , items: [] }, { shop: d.id , items: [] } ]
				// remove items, which has picked shop
upi:			for( var item=0, boxitems=self.chosenBox().itemList(); item < boxitems.length;  ) { //TODO refact
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
				
// console.info( newboxes )							
				// create new box for such items and for old box
				for( var nbox in newboxes ) {
					if( newboxes[nbox].items.length > 0 ) {
						var argshop = Model.shops[ newboxes[nbox].shop ]
						addBox ( 'self', 'self_'+newboxes[nbox].shop, newboxes[nbox].items, argshop )
					}
				}
				// clear this box if it should be
				if( ! self.chosenBox().itemList().length ) // always
					self.dlvrBoxes.remove( self.chosenBox() )

			} else {
				/* Select Shop at Zero Step */	
				// pushing into box items which have selected shop
				var selectedShopBoxShops = [ { shop: d.id, items: [] } ]

				for( var box in self.dlvrBoxes() ) {
					var procBox = self.dlvrBoxes()[box]
					for( var item =0; item < procBox.itemList().length;  ) {				
						if( 'self_'+d.id in procBox.itemList()[item].deliveries ) {
							if( procBox.itemList()[item].deliveries['self_'+d.id].dates.length > 1 )
								selectedShopBoxShops[0].items.push( procBox.itemList()[item].token )
							else // items which are 'one day' reserve-only 
								selectedShopBoxShops.push( { shop: d.id, items: [ procBox.itemList()[item].token ] } )
							procBox.itemList.remove( procBox.itemList()[item] )
						} else 
							item++
					}
				}
// console.info(selectedShopBoxShops)
				// separate 'courier-only' from self-available
				// get self-available as a hash
				var data = {},
					tmpv = []
				for( var box in self.dlvrBoxes() ) {
					var procBox = self.dlvrBoxes()[box]
// console.info(procBox.itemList())					
					for( var item =0, list = procBox.itemList(); item < list.length;  ) {
						tmpv = []
						for( tkn in list[item].deliveries ) {
							if( list[item].deliveries[ tkn ].token.match( 'self_' ) )
								tmpv.push( list[item].deliveries[ tkn ].token.replace( 'self_', '' ) )
						}
// console.info( list[item].token , tmpv )						
						data[ list[item].token +'' ] = tmpv
						if( !tmpv.length )
							item++
						else
							procBox.itemList.remove( procBox.itemList()[item] )
						
					}
					
				}
				// distributive algorithm				
				var newboxes = DA( data )
				for(var i=0, l=selectedShopBoxShops.length; i<l; i++)
					if( selectedShopBoxShops[i].items.length > 0 )
						newboxes.push( selectedShopBoxShops[i] )
// console.info( newboxes )
				// build new self-boxes
				for(var tkn in newboxes ) {
					var argshop = Model.shops[ newboxes[tkn].shop ]
					addBox ( 'self', 'self_'+newboxes[tkn].shop , newboxes[tkn].items, argshop )
				}
				// drop empty boxes
				for( var box =0; box < self.dlvrBoxes().length;  ) {
					if( ! self.dlvrBoxes()[box].itemList().length )
						self.dlvrBoxes.remove( self.dlvrBoxes()[box] )
					else
						box++
				}

				// interface
				self.shopButtonEnable(false)
				var data = {
					'type': 'shops',
					'boxQuantity': self.dlvrBoxes().length
				}	
				for( var box in self.dlvrBoxes() )
					if( self.dlvrBoxes()[box].type === 'standart' ) {
						data.type = 'courier'
						break
					}
				PubSub.publish( 'DeliveryChanged', data )
			}

			self.chosenShop( d )
			self.step2(true)
			PubSub.publish( 'ShopSelected', d )
		}

		self.printDate = function( tstamp ) {
			var d = new Date( tstamp )
			var rusMN = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря']
			return d.getDate() + ' ' + rusMN[ d.getMonth() ]
		}

		function formateDate( tstamp ) {
			var raw = new Date(tstamp),
				m = raw.getMonth()+1,
				d = raw.getDate()
			return raw.getFullYear() + '-' + ( m > 9 ? m : '0' + m ) + '-' + ( d > 9 ? d : '0' + d )
		}

		self.getServerModel = function() {
			var ServerModel = {
				deliveryTypes: {}
			}

			for( var tkn in self.dlvrBoxes() ) {

				var dlvr = self.dlvrBoxes()[tkn]
				var data = {
					id: Model.deliveryTypes[ dlvr.token ].id,
					token: dlvr.token,
					type: dlvr.type,
					date: formateDate( dlvr.chosenDate() ),
					interval: dlvr.chosenInterval().match(/\d{2}:\d{2}/g).join(','),
					shop: {
						id: dlvr.token.replace('self_','')
					}
				}
				var boxitems = []
				for( var i in dlvr.itemList() )
					boxitems.push( dlvr.itemList()[i].token )
				data.items = boxitems
				ServerModel.deliveryTypes[ dlvr.token + '_' + formateDate( dlvr.chosenDate() ) + '_' + dlvr.itemList()[0].id ] = data
			}
			return ServerModel
		}

		// set delivery types on the top
		self.showForm(true)
		for( var tkn in Model.deliveryTypes )
			if( Model.deliveryTypes[tkn].type === 'standart' )
				self.dlvrCourierEnable(true)
			else
				self.dlvrShopEnable(true)
		if( self.dlvrCourierEnable() && ! self.dlvrShopEnable() )
			self.pickCourier()
		if( ! self.dlvrCourierEnable() && self.dlvrShopEnable() )	
			self.pickShops()

	} // OrderModel object


	function keyMaxLong( hash ) {
		var max = 0
		var keyM = 'none'
		for(var key in hash )
			if( hash[key].length > max ) {
				max = hash[key].length
				keyM = key
			}
		return keyM
	}

	function DA( data ) {
// console.info('DA b')		
	// e.g. data = {
	// 	'pr1' : [ '13', '2' ],
	// 	'pr2' : [ '13', '2' ],
	// 	'pr3' : [ '3', '2' ],
	// 	'pr4' : [ '14' ]
	// }
		var out = []
		while( true ) {
			var shop_items = {},
				le = 0
			for( var tkn in data ) {
				for( var i=0, l=data[tkn].length; i<l; i++ ) {
					if( !shop_items[ data[tkn][i] ] ) {
						shop_items[ data[tkn][i] ] = [tkn]
						le++
					} else {
						shop_items[ data[tkn][i] ].push(tkn)
					}
				}
			}
			if( !le )
				break
// console.info(shop_items)
			
			var keyMax = keyMaxLong( shop_items )
			out.push( { 'shop': keyMax, 'items': shop_items[ keyMax ] } )
			for( var tkn in shop_items[ keyMax ] )
				data[ shop_items[ keyMax ][tkn] ] = []
			

		}
// console.info(out, data)		
// console.info('DA e')		
		return out
	}
	/* ---------------------------------------------------------------------------------------- */
	/*  Send Data */
	var form = $('#order-form')
	var sended = false
	var broken = 0

	function markError( field, mess ) {
		broken++
		$('body').delegate('input[name="'+field+'"]', 'change', function() {
			if( $(this).val().replace(/\s+/g,'') != '' ) {
				broken--
				$('input[name="'+field+'"]').removeClass('mRed')
				$('input[name="'+field+'"]').prev('.placeholder').removeClass('mRed')
				var line = $('input[name="'+field+'"]').closest('.bBuyingLine')
				if( !line.find('.mRed').length )
					line.find('.bFormError').remove()
			}
		})
		var node = $('input[name="'+field+'"]:first')
		if( node.hasClass('mRed') ) return
		switch( node.attr('type') ) {
			case 'text':
				node.addClass('mRed')
				node.prev('.placeholder').addClass('mRed')
				var dd = node.parent().parent()
				if( !dd.find('.bFormError').length )
					dd.append( '<span class="bFormError mb10 pt5">'+mess+'</span>' ) // AWARE: CUSTOM
			break
			default: // radio, checkbox
				node.addClass('mRed')
				node.prev('.placeholder').addClass('mRed')
				node.parent().parent().parent().append( '<span class="bFormError mb10 pt5">'+mess+'</span>' ) // AWARE: CUSTOM
			break
		}
	}

	function printErrors( errors ) {
		for( var inp in errors ) {
			markError( inp, errors[inp] )
		}
		if( broken > 0 )
			$.scrollTo( '.mRed:first' , 500 )
	}

	$('#order-submit').click( function(e) {
		e.preventDefault()
		if( sended ) return // form is currently processing
		if( $(this).hasClass('disable')) { // form isnot active - delivery should be chosen
            return false
        }

		// Validation		
		var serArray = form.serializeArray()

		var fieldsToValidate = $('#order-validator').data('value')
flds:	for( field in fieldsToValidate ) {
			if( !form.find('[name="'+field+'"]:visible').length )
				continue
			if (field=='order[recipient_phonenumbers]'){
				var phoneVal = $('#order_recipient_phonenumbers').val()
				if ( phoneVal.length < 10){
					markError(field, 'Маловато цифр');
				}
			}
			for(var i=0, l=serArray.length; i<l; i++) {
				if( serArray[i].name == field ) {
					if( serArray[i].value == '' )
						markError( field, fieldsToValidate[field] ) // cause is empty
					continue flds
				}
			}
			markError( field, fieldsToValidate[field] ) // cause not in serArray
		}

		if( broken > 0 ) {
			$.scrollTo( '.mRed:first' , 500 )
			return
		}

		// Show Rounder
		var button = $(this)
		button.text('Оформляется...')
		Blocker.block()

		// Prepare Data & Send
		sended = true		
		var toSend = form.serializeArray()
		var type_id = $('label.mChecked input[name="order[delivery_type_id]"]').val()
		if( !type_id )
			type_id = $('input[name="order[delivery_type_id]"]').val()
		toSend.push( { name: 'order[delivery_type_id]', value: type_id })
		toSend.push( { name: 'delivery_map', value: JSON.stringify( MVM.getServerModel() )  } )//encodeURIComponent
		if( typeof(SertificateCard) !== 'undefined' )
			if( SertificateCard.isActive() ) {
				toSend.push( { name: 'order[card]', value: SertificateCard.getCode() })
				toSend.push( { name: 'order[pin]', value: SertificateCard.getPIN() })
			}

		$.ajax({
			url: form.attr('action'),
			timeout: 20000,
			type: "POST",
			data: toSend,
			success: function( data ) {
				sended = false
				if( !data.success ) {
					Blocker.unblock()
					button.text('Завершить оформление')
					if( 'errors' in data )
						printErrors( data.errors )
					// TODO display data.error info
					return
				}
				Blocker.bye()
				if( 'redirect' in data.data )
					window.location = data.data.redirect
			},
            error: function() {
                button.text('Попробовать еще раз')
                Blocker.unblock()
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
	$('#OrderView').delegate( '.selectShop', 'click', function() {
		$('#orderMapPopup').lightbox_me({ 
			centered: true,
            onLoad: function() {
            	shops = getShopsStack()
        		if (!$.isEmptyObject(shops)){
     //    			$.when( loadMap(shops) ).then(function(){ 
					//     // console.log(window.regionMap)
					// 	if (window.regionMap){
					// 		window.regionMap.showMarkers( shops )
					// 	}
					// })
            		loadMap(shops)
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

    function loadMap(shopsStack){
    	$('#mapPopup').empty()
    	MapInterface.ready( 'yandex', {
			yandex: $('#mapInfoBlock'), 
			google: $('#map-info_window-container')
		} )
		var mapCenter = calcMCenter( shopsStack )
		var mapCallback = function() {
			// console.log(window.regionMap)
			if (window.regionMap){
				window.regionMap.showMarkers( shops )
			}
			window.regionMap.addHandler( '.shopchoose', ShopChoosed )
		}
		MapInterface.init( mapCenter, 'mapPopup', mapCallback, updateI )
    }
})
