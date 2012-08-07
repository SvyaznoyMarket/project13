$(document).ready(function() {

	/* ---------------------------------------------------------------------------------------- */
	/* COMMON DESIGN, BEHAVIOUR ONLY */

	/* Custom Selectors */ 
	$('#OrderView').delegate( '.bSelect', 'click', function() {
		if( $(this).hasClass('mDisabled') )
			return false
		$(this).find('.bSelect__eDropmenu').toggle()
	})
	$('#OrderView').delegate( '.bSelect', 'mouseleave', function() {
		if( $(this).hasClass('mDisabled') )
			return false
		var options = $(this).find('.bSelect__eDropmenu')
		if( options.is(':visible') )
			options.hide()
	})

	/*  Custom Checkboxes */
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

	/* Auth Link */
	PubSub.subscribe( 'authorize', function( m, d ) {
		$('#order_recipient_first_name').val( d.first_name )
		$('#order_recipient_last_name').val( d.last_name )
		$('#order_recipient_phonenumbers').val( d.phonenumber + '       ' )
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
	if( typeof( $.mask ) !== 'undefined' ) {
		$.mask.definitions['n'] = "[()0-9\ \-]"
		$("#order_recipient_phonenumbers").mask("8nnnnnnnnnnnnnnnnn", { placeholder: " ", maxlength: 10 } )
        $("#order_recipient_phonenumbers").val('8')
        
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
	
	$('#addressField').find('input').placeholder()
	
	var ubahn = [ 'Авиамоторная', 'Автозаводская','Академическая','Александровский сад','Алексеевская','Алтуфьево','Аннино','Арбатская (Арбатско-Покровская линия)','Арбатская (Филевская линия','Аэропорт',
'Бабушкинская','Багратионовская','Баррикадная','Бауманская','Беговая','Белорусская','Беляево','Бибирево','Библиотека имени Ленина','Битцевский парк','Борисовская',
'Боровицкая','Ботанический сад','Братиславская','Бульвар адмирала Ушакова','Бульвар Дмитрия Донского','Бунинская аллея','Варшавская',
'ВДНХ','Владыкино','Водный стадион','Войковская','Волгоградский проспект','Волжская','Волоколамская','Воробьевы горы','Выставочная','Выхино','Деловой центр',
'Динамо','Дмитровская','Добрынинская','Домодедовская','Достоевская','Дубровка','Жулебино','Зябликово','Измайловская','Калужская','Кантемировская','Каховская','Каширская','Киевская',
'Китай-город','Кожуховская','Коломенская','Комсомольская','Коньково','Красногвардейская','Краснопресненская','Красносельская','Красные ворота','Крестьянская застава',
'Кропоткинская','Крылатское','Кузнецкий мост','Кузьминки','Кунцевская','Курская','Кутузовская','Ленинский проспект','Лубянка',
'Люблино','Марксистская','Марьина роща','Марьино','Маяковская','Медведково','Международная','Менделеевская','Митино',
'Молодежная','Мякинино','Нагатинская','Нагорная','Нахимовский проспект','Новогиреево','Новокузнецкая','Новослободская','Новоясеневская','Новые Черемушки','Октябрьская',
'Октябрьское поле','Орехово','Отрадное','Охотныйряд','Павелецкая','Парк культуры','Парк Победы','Партизанская',
'Первомайская','Перово','Петровско-Разумовская','Печатники','Пионерская','Планерная','Площадь Ильича','Площадь Революции','Полежаевская',
'Полянка','Пражская','Преображенская площадь','Пролетарская','Проспект Вернадского','Проспект Мира','Профсоюзная','Пушкинская',
'Речной вокзал','Рижская','Римская','Рязанский проспект','Савеловская','Свиблово','Севастопольская','Семеновская','Серпуховская',
'Славянский бульвар','Смоленская (Арбатско-Покровская линия)','Смоленская (Филевская линия)','Сокол','Сокольники','Спортивная',
'Сретенский бульвар','Строгино','Студенческая','Сухаревская','Сходненская','Таганская','Тверская','Театральная','Текстильщики','ТеплыйСтан','Тимирязевская',
'Третьяковская','Трубная','Тульская','Тургеневская','Тушинская','Улица 1905года','Улица Академика Янгеля','Улица Горчакова','Улица Подбельского','Улица Скобелевская','Улица Старокачаловская','Университет','Филевский парк','Фили',
'Фрунзенская','Царицыно','Цветной бульвар','Черкизовская','Чертановская','Чеховская','Чистые пруды','Чкаловская','Шаболовская','Шипиловская',
'Шоссе Энтузиастов','Щелковская','Щукинская','Электрозаводская','Юго-Западная','Южная','Ясенево'
		]
	$( "#order_address_metro" )
		.autocomplete({
			source: ubahn,
			appendTo: '#metrostations',
			minLength: 2
		})
		.change( function() {
			for(var i=0, l= ubahn.length; i<l; i++)
				if( $(this).val() === ubahn[i] )
					return true
			$(this).val('')
		})

	/* Shop Popup */
	$('#OrderView').delegate( '.selectShop', 'click', function() {
		$('.mMapPopup').lightbox_me({})
		return false
	} )
	

	/* ---------------------------------------------------------------------------------------- */
	/* PUBSUB HANDLERS */
	/* Glue for architecure */
	PubSub.subscribe( 'DeliveryChanged', function( m, data ) {		
		
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
			$('#payment_method_online-field').hide()
		} else {
			$('#payment_method_online-field').show()
		}

	})

	PubSub.subscribe( 'ShopSelected', function( m, data ) {
		$('.mMapPopup').trigger('close')
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

		// Unavailable TODO layout scheme
		self.unavailable = ko.observable( false )
		if( Model.unavailable.length )
			self.unavailable( true )

		// Boxes
		self.curWeek = ko.observable(1)
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
console.info( 'Interval edges: ', first )
			var last = getSunday( tightInterval[1].timestamp )
console.info( last )

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
			box.dlvrPrice  = ko.computed(function() {})
			box.totalPrice  = ko.computed(function() {})
			self.dlvrBoxes.push( box )
		} // mth addBox

		function setComputables() {
			for( var key in self.dlvrBoxes() ) {
				var loopBox = self.dlvrBoxes()[key]
				
				loopBox.dlvrPrice  = ko.computed(function() {
					var out = 0
					var bid = this.token
					for(var i=0, l=this.itemList().length; i<l; i++) {
						var itemDPrice = this.itemList()[i].deliveries[bid].price
						if( itemDPrice > out )
							out = itemDPrice
					}
					return out
				}, loopBox)
				
				loopBox.totalPrice = ko.computed(function() {				
					var out = 0
					for(var i=0, l=this.itemList().length; i<l; i++)
						out += this.itemList()[i].total
					out += this.dlvrPrice()*1
					return out
				}, loopBox)
			} 
		} // mth setComputables

		for( var tkn in Model.deliveryTypes ) { // filling up dlvrBoxes
			if( Model.deliveryTypes[tkn].items.length ) {				
				addBox ( Model.deliveryTypes[tkn].type, Model.deliveryTypes[tkn].token, Model.deliveryTypes[tkn].items, Model.deliveryTypes[tkn].shop )
			}
		}
		setComputables()

		self.shopsInPopup = ko.observableArray( [] )
		for( var key in Model.shops )
			self.shopsInPopup.push( Model.shops[key] )

		self.chosenShop = ko.observable(null)

		self.shopButtonEnable = ko.observable( false )

		self.changeWeek = function( direction, data, e ) {
			if( direction > 0 ) {
				if( data.nweeks == self.curWeek() )
					return	
				self.curWeek( self.curWeek() + 1 )		
			}
			if( direction < 0 ) {
				if( self.curWeek() == 1 )
					return
				self.curWeek( self.curWeek() - 1 )		
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
			// $.get( d.deleteUrl )
			// drop from box
			box.itemList.remove( d )
			if( !box.itemList().length )
				self.dlvrBoxes.remove( box )
		}

		self.totalSum = ko.computed( function() {
			var out = 0
			for(var i=0, l = self.dlvrBoxes().length; i<l; i++) {		
				out += self.dlvrBoxes()[i].totalPrice() * 1
			}
			return out
		}, this)

		self.pickCourier = function() {
			self.step2( true )
			self.shopButtonEnable( false )
			var data = {
				'type': 'courier',
				'boxQuantity': self.dlvrBoxes.length
			}
			PubSub.publish( 'DeliveryChanged', data )
		}

		self.pickShops = function() {
			self.step2( false )
			self.shopButtonEnable( true )
			var data = {
				'type': 'shops',
				'boxQuantity': self.dlvrBoxes.length
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
			for( var i=0; i<self.shopsInPopup().length; ) {
				if( $.inArray( self.shopsInPopup()[i].id , shopIds ) === -1 )
					self.shopsInPopup.remove( self.shopsInPopup()[i] )
				else
					i++
			}
		}

		self.showAllShops = function() {
			self.shopsInPopup.removeAll()
			for( var key in Model.shops )
				self.shopsInPopup.push( Model.shops[key] )
		}

		self.selectShop = function( d, e ) {
			if( self.step2() ) { // box selector handler
				// change shop for current box
				self.chosenBox().shop( d )
				// change dates and intervals ...
				// remove items, which hasnt this shop
				// create new boxes for such items
			} else {
				// pushing into box items which have selected shop
				var selectedShopBoxShops = []
				for( var box in self.dlvrBoxes() ) {
					var procBox = self.dlvrBoxes()[box]
					for( var item =0; item < procBox.itemList().length;  ) {
						if( 'self_'+d.id in procBox.itemList()[item].deliveries ) {
							selectedShopBoxShops.push( procBox.itemList()[item].token )
							procBox.itemList.remove( procBox.itemList()[item] )
						} else 
							item++
					}
				}
console.info(selectedShopBoxShops)
				// separate 'courier-only' from self-available
				// get self-available as hash
				var data = {}
				for( var box in self.dlvrBoxes() ) {
					var procBox = self.dlvrBoxes()[box]		
					for( var item =0, list = procBox.itemList(); item < list.length;  ) {
						var tmpv = []
						for( tkn in list[item].deliveries )
							if( list[item].deliveries[ tkn ].token.match( 'self_' ) )
								tmpv.push( list[item].deliveries[ tkn ].token.replace( 'self_', '' ) )
						data[ list[item].token ] = tmpv
						if( !tmpv.length )
							item++
						else
							procBox.itemList.remove( procBox.itemList()[item] )
					}
					
				}
// console.info(data)
				// distributive algorithm				
				var newboxes = DA( data )
				newboxes.push( { shop: d.id, items: selectedShopBoxShops } )
console.info( newboxes )
				// build new self-boxes
				for(var tkn in newboxes ) {
					var argshop = Model.shops[ newboxes[tkn].shop ]
					var argitems = []
					for(var i=0, l=newboxes[tkn].items.length; i<l; i++)
						argitems.push( Model.items[ newboxes[tkn].items[i] ] )
					addBox ( 'self', 'self_'+newboxes[tkn].shop, newboxes[tkn].items, argshop )
				}
				setComputables()
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
	// e.g. data = {
	// 	'pr1' : [ '13', '2' ],
	// 	'pr2' : [ '13', '2' ],
	// 	'pr3' : [ '3', '2' ],
	// 	'pr4' : [ '14' ]
	// }		
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
			var out = []
			var keyMax = keyMaxLong( shop_items )
			out.push( { shop: keyMax, items: shop_items[ keyMax ] } )
			for( var tkn in shop_items[ keyMax ] )
				data[ shop_items[ keyMax ][tkn] ] = []
			
// console.info(out, data)
		}
		return out
	}

	/* ---------------------------------------------------------------------------------------- */
	/*  Send Data */

	$('#order-submit').click( function(){
		// Validation
		// Prepare Data
		// Send
		// Show Rounder
	})

	/* ---------------------------------------------------------------------------------------- */

console.info( 'MODEL ', Model )
	MVM = new OrderModel() 
	ko.applyBindings( MVM , $('#OrderView')[0] ) 

})