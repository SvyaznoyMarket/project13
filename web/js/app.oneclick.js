$(document).ready(function() {
	/* Custom Selectors */ 
	$('#order1click-container-new').delegate( '.bSelect', 'click', function() {
		if( $(this).hasClass('mDisabled') )
			return false
		$(this).find('.bSelect__eDropmenu').toggle()
	})
	$('#order1click-container-new').delegate( '.bSelect', 'mouseleave', function() {
		if( $(this).hasClass('mDisabled') )
			return false
		var options = $(this).find('.bSelect__eDropmenu')
		if( options.is(':visible') )
			options.hide()
	})
	
	/* View Models */
	//////////////////////////////////////////
	
	function OneCViewModel() {

			var self = this	
			self.noDelivery = ko.observable(false)
			
			self.title     = Model.jstitle
			self.price     = Model.jsprice
			self.icon      = Model.jsbimg
			self.shortcut  = Model.jsshortcut
			self.stockq    = Model.jsstock
			
			self.quantity    = ko.observable(1)
			self.quantityTxt = ko.computed(function() {
				return self.quantity() + ' шт.'
			}, this)
			self.priceTxt = ko.computed(function() {
				return printPrice( self.price )
			}, this)

			self.formStatus = ko.observable( 'typing' ) // 'process' 'error' 'sending'
			self.formStatusTxt = ko.computed( function() {
				var status = ''
				switch( self.formStatus() ) {
					case 'reserve':
						status = 'Зарезервировать'
					break				
					case 'typing':
						status = 'Отправить заказ'
					break
					case 'process':
						status = 'Проверка...'
					break
					case 'error':
						status = 'Отправить заказ нельзя'
					break
					case 'sending':
						status = 'Отправка...'
					break
				}
				return status
			}, this)
			
			self.showMap = ko.observable( false )
			self.textfields = []
			
				self.textfields.push( ko.observable({
					title: 'Имя получателя',
					name: 'order[recipient_first_name]', //UNIQUE!
					selectorid: '',
					value: '',
					valerror: false,
					regexp: /^[a-zа-я\s]+$/i
				}) )
				self.textfields.push( ko.observable({
					title: 'Телефон для связи',
					name: 'order[recipient_phonenumbers]', //UNIQUE!
					selectorid: 'phonemask',
					value: '',
					valerror: false,
					regexp: /^[()0-9\-\+\s]+$/
				}) )
			
			self.disabledSelectors = ko.observable( false )
			self.noQBar            = ko.observable( false )
			self.stableType        = ko.observable( false )
			
			// for dynModel
			self.chosenDlvr = ko.observable( {} )
			self.chosenDate = ko.observable( {} )
			self.dlvrs = ko.observableArray([])
			self.dates = ko.observableArray([])
			self.shops = ko.observableArray([])
			self.chosenShop = ko.observable( {} )
			self.pickedShop = ko.observable( {} )
				
			self.dynModel = function( Deliveries ) {				
				var chosenType = self.chosenDlvr().type
				if ( !chosenType )
					chosenType = 'self'
				self.dlvrs.removeAll()
				self.chosenDlvr({})

				for(var obj in Deliveries ) {
					self.dlvrs.push( {
						type: obj,
						name: Deliveries[obj].name,
						modeID: Deliveries[obj].modeId,
						price: Deliveries[obj].price
					})
					if( obj == chosenType )
						self.chosenDlvr( self.dlvrs()[ self.dlvrs().length - 1 ] )
				}
				if( ! ('type' in self.chosenDlvr() ) )
					self.chosenDlvr( self.dlvrs()[ 0 ] )
				
				self.dates( Deliveries[ self.chosenDlvr().type+'' ].dates.slice(0) )
				self.chosenDate( self.dates()[0] )
				
				if( selfAvailable ) {
					self.shops( Deliveries['self'].shops.slice(0) )
					self.chosenShop( self.shops()[0] )
					self.pickedShop( self.shops()[0] )
				} else {
					var leer = { address: '', regtime: '', id : 1 }
					self.chosenShop( leer )
					self.pickedShop( leer )
				}
			}
			self.dynModel( Deliveries )
			
			self.total = ko.computed(function() {
				return printPrice( self.price * self.quantity() + self.chosenDlvr().price * 1 )
			}, this)
			
			self.changeDlvr = function( argDlvr ) {
				self.chosenDlvr( argDlvr )
				self.dates.removeAll()
				//while( self.dates().length )
				//	self.dates.pop()
				for(var i=0; i< Deliveries[ argDlvr.type ].dates.length; i++ )	
					self.dates.push( Deliveries[ argDlvr.type ].dates[i] )	
				self.chosenDate( self.dates()[0] )
				if( self.showMap() )
					self.showMap( false )
			}
			
			self.plusItem = function() {
				self.quantity( self.quantity() + 1 )

				if( self.quantity() > self.stockq ) {
					self.noDelivery( true )
					return false
				}
				
				var curq =  self.quantity() * 1
				setTimeout( function(){ self.loadData( curq, 1 ) } , 500 )
				return false
			}
			self.minusItem = function() {
				if( self.quantity() == 1 )
					return false
				self.quantity( self.quantity() - 1 )

				if( self.noDelivery() )
					if( self.quantity() <= self.stockq ) {
						self.noDelivery( false )
					}
					else {
						return false
					}

				var curq =  self.quantity() * 1
				setTimeout( function(){ self.loadData( curq, -1 ) } , 500 )
				return false
			}
			
			self.preparedData = function( data ) {
				if( data.type === 'self' ) {
					self.formStatus('reserve')
					for(var i=0, l=self.dlvrs().length; i<l; i++ )
						if( self.dlvrs()[i].type == 'self' ) {
							self.chosenDlvr( self.dlvrs()[i]  )
							break
						}						
					self.disabledSelectors( true )
					self.noQBar( true )
					if( 'date' in data )
						self.chosenDate( data.date )
					if( 'shop' in data )
						self.chosenShop( data.shop )
				} else if( data.type === 'courier' ) {
					self.formStatus('reserve')
					for(var i=0, l=self.dlvrs().length; i<l; i++ )
						if( self.dlvrs()[i].type != 'self' ) {
							self.chosenDlvr( self.dlvrs()[i]  )
							break
						}	
					self.disabledSelectors( false )
					self.stableType( true )
					self.noQBar( true )
				}
			}
			
			self.loadData = function( momentq, direction ) {
				if( ( direction > 0 && self.quantity() > momentq ) || ( direction < 0 && self.quantity() < momentq ) )
					return
				var postData = {
					product_id: Model.jsitemid,
					product_quantity: momentq,
					region_id: Model.jsregionid*1
				}
				
				$.post( inputUrl, postData, function(data) {
					if( self.noDelivery() )
						return false
					if( !data.success ) {
						self.noDelivery(true)
						return false
					}
					Deliveries = data.data
					var le = 0
					for(var key in Deliveries )
						le++
					if( le === 0 ) {
						self.noDelivery(true)
						return false
					} else {
						self.noDelivery(false)
					}
					selfAvailable = 'self' in Deliveries
					self.dynModel(Deliveries)
					if( selfAvailable && typeof(mapCenter) == 'undefined') {
						mapCenter = calcMCenter( Deliveries['self'].shops )
					}						
					if( selfAvailable && ! ('regionMap' in window ) ) {
						window.regionMap = new MapWithShops( mapCenter, $('#map-info_window-container'), 'mapPopup' )
						window.regionMap.addHandler( '.shopchoose', pickStoreMVM )
					}
					
				})	
			}
			
			self.pickDate = function( item ) {
				self.chosenDate( item )
				//shops mod		
				if( selfAvailable ) {
					if( 'shopIds' in item ) 
						if( item.shopIds.length > 0 ) {
							self.shops.removeAll()// = ko.observableArray( Deliveries['self'].shops.slice(0) )
							for(var key in Deliveries['self'].shops ) {
								if( item.shopIds.indexOf( Deliveries['self'].shops[key].id*1 ) !== -1 )
									self.shops.push( Deliveries['self'].shops[key] )
							}
						}
				}
				if( self.showMap() )
					self.showMarkers()	
			}
			
			self.pickShop = function( item ) {
				self.chosenShop( item )
			}
			self.pickShopOnMap = function( shid ) {
				for(var i=0, l=self.shops().length; i<l; i++)
					if( self.shops()[i].id == shid ) {
						self.pickedShop( self.shops()[i] )
						return
					}
			}
			
			self.toggleMap = function() {
				if( self.showMap() ) { // like toggle but more precise
					$('#mapPopup').hide('blind', null, 800, function() {
						self.showMap(false)
					})
				} else {
					self.showMap(true)
					$('#mapPopup').show( 'blind', null, 1000 )
				}
				
				if( !self.showMap() )
					return
				self.showMarkers()	
			}
			
			self.turnOffMap = function() {
				self.showMap(false)
			}
			
			self.showMarkers = function() {
				var markersPull = {}
				var tmp = self.shops()//MVM.popupWithShops()
				for(var i=0, l = tmp.length; i<l; i++) {
					var key = tmp[i].id + ''
					markersPull[ key ] = {
						id: tmp[i].id,
						name: tmp[i].address,
						regtime: tmp[i].regtime,
						latitude: tmp[i].latitude,
						longitude: tmp[i].longitude
					}
				}
				window.regionMap.showMarkers( markersPull )				
			}
			
			self.chooseShopById = function( shopnum ) {
				for(var i=0, l=self.shops().length; i<l; i++) {
					if( self.shops()[i].id == shopnum ) {
						self.chosenShop( self.shops()[i] )
						break
					}
				}
			} 
			
			self.validateField = function( textfield, e ) {
				var valerror = false
				if( e.currentTarget.value.replace(/\s/g, '') == '' ||  !textfield.regexp.test( e.currentTarget.value ) ) {
					valerror = true
					self.formStatus('typing')
				}	
				if( e.currentTarget.getAttribute('id') === 'phonemask' && e.currentTarget.value.replace(/[^0-9]/g, '').length !== 11 ) {
					valerror = true
					self.formStatus('typing')
				}	
				for(var i=0, l=self.textfields.length; i<l; i++) // like indexOf
					if( self.textfields[i]().name === textfield.name ) {
						var tmp = self.textfields[i]()
						tmp.valerror = valerror
						tmp.value = e.currentTarget.value
						self.textfields[i]( tmp )
						
						break
					}
				enableHandlers()	
				return	true
			}

			self.validateForm = function() {
				if( self.noDelivery() )
					return false
				if( self.formStatus() !== 'typing' && self.formStatus() !== 'reserve' ) // double or repeated click
					return 
				//change title
				self.formStatus('process')
				
				//validate fields
				$('#oneClick input').trigger('change')
				if( self.formStatus() === 'typing' ) // validation error
					return
				
				//send ajax
				self.sendData()
				
			}
						
			self.sendData = function() {
				self.formStatus('sending')
				var postData = {
					'order[product_quantity]' : self.quantity(),
					'order[delivered_at]' : self.chosenDate().value
				}
				if( self.chosenDlvr().type == 'self' )
					postData[ 'order[shop_id]' ] = self.chosenShop().id
				for(var i=0,l=self.textfields.length; i<l; i++)
					postData[ self.textfields[i]().name + '' ] = self.textfields[i]().value
				var xhr1 =$.ajax( {
					type: 'POST',
					url: outputUrl,
					data: postData,
					success: function( data, textStatus ) {
						if( !data.success || textStatus !== 'success' ) {
							self.formStatus('typing')
							return
						}
						//process
						$('.bFast').parent().append( data.data.content )
						$('.bFast').remove()
						$('.p0').removeClass('p0')
						$('.top0').removeClass('top0')
						$('.order1click-link-new').remove()
						if( typeof(_gaq) !== 'undefined' && typeof(runAnalitics) !== 'undefined' )
							runAnalitics()
						ANALYTICS.parseAllAnalDivs( $('.jsanalytics') )
					},
					error: function( jqXHR, textStatus ) {
						self.formStatus('typing')
						return		
					}
				} )	
			}
			
	} // OCMVM
	
	/* StockViewModel */
	function StockViewModel() {

		var self = this	
		self.showMap = ko.observable(false)
		
		self.title     = Model.jstitle
		self.price     = Model.jsprice
		self.icon      = Model.jssimg
		self.shortcut  = Model.jsshortcut
		self.region    = Model.jsregion
		self.today = ko.observable(true)
		
		self.priceTxt = ko.computed(function() {
			return printPrice( self.price )
		}, this)
		
		
		//dyn
		self.shops = Deliveries['self'].shops.slice(0)
		
		self.todayShops = []
		self.tomorrowShops = []
		self.activeCourier = Deliveries.length > 1
		
		parseDateShop = function( numbers, label ) {
			var out = []
levup:			for(var i=0, l=numbers.length; i<l; i++)
				for(var j=0, l=self.shops.length; j<l; j++)
					if( self.shops[j].id == numbers[i] ) {
						var tmp = {}
						for (var prop in self.shops[j] ) {
							tmp[prop] = self.shops[j][prop]
						}
						tmp['lbl'] = label
						out.push( tmp )
						continue levup
					}
			return out	
		}
		//find today index
		var tind = -1
		for(var i=0, l=Deliveries['self'].dates.length; i<l; i++)
			if( Deliveries['self'].dates[i].value === currentDate ) {
				tind = i
				break
			} else {
				if( Date.parse( currentDate ) == Date.parse( Deliveries['self'].dates[i].value ) ) {
					tind = i
					break
				}
			}
			
		if( tind < 0 ) {
			self.tomorrowShops = parseDateShop( Deliveries['self'].dates[ tind + 1 ].shopIds, 'tmr' )
		} else {
			self.todayShops = parseDateShop( Deliveries['self'].dates[ tind ].shopIds, 'td' )
			if( Deliveries['self'].dates.length > tind + 1 )
				self.tomorrowShops = parseDateShop( Deliveries['self'].dates[ tind + 1 ].shopIds, 'tmr' )			
		}

		self.pickedShop = ko.observable( self.todayShops[0] )
		self.selectedS = ko.observable( {} )
		var ending = 'ах'
		if( self.todayShops.length % 10 === 1 )
			ending = 'е'
		self.todayH2 = 'Можно <span class="mLft">сегодня</span> в '+ self.todayShops.length + ' магазин'+ ending +':'
		if( self.tomorrowShops.length % 10 === 1 )
			ending = 'е'
		else
			ending = 'ах'			
		self.tomorrowH2 = ( self.todayShops.length > 0 ) ? 'или' : 'Можно'
		self.tomorrowH2 += ' <span class="mRt">завтра</span> в '+ self.tomorrowShops.length + ' магазин'+ ending +':'
		
		self.toggleView = function( flag ) {		
			self.showMap( flag )
			if( flag )
				self.showMarkers()	
			return false
		}
		
		self.toggleTerm = function( flag ) {
			self.today( flag )
			window.regionMap.hideInfobox()
			self.showMarkers()
			return false
		}
		
		self.chooseShop = function( item, today ) {
			self.selectedS( item )
			self.today( today )
		}
		
		self.chooseShopById = function( shopnum ) {
			for(var i=0, l=self.shops.length; i<l; i++) {
				if( self.shops[i].id == shopnum ) {
					self.selectedS( self.shops[i] )
					break
				}
			}
			self.reserveItem()
		}

		self.pickShopOnMap = function( shid ) {
			for(var i=0, l=self.shops.length; i<l; i++)
				if( self.shops[i].id == shid ) {
					self.pickedShop( self.shops[i] )
					return
				}
		}

		self.showMarkers = function() {
			var markersPull = {}
			var tmp = self.today() ? self.todayShops : self.tomorrowShops
			for(var i=0, l = tmp.length; i<l; i++) {
				var key = tmp[i].id + ''
				markersPull[ key ] = {
					id: tmp[i].id,
					name: tmp[i].address,
					regtime: tmp[i].regtime,
					latitude: tmp[i].latitude,
					longitude: tmp[i].longitude
				}
			}
			window.regionMap.showMarkers( markersPull )				
		}
		
		self.reserveItem = function() {
			var MVMinterface = {
				type: 'self',
				date: self.today() ? Deliveries['self'].dates[ tind ] : Deliveries['self'].dates[ tind + 1 ],
				shop: self.selectedS()
			}
			OC_MVM.preparedData( MVMinterface )
			$('#order1click-container-new').lightbox_me( { } )
			return false
		}	
		
		self.onlyCourier = function() {
			var MVMinterface = {
				type: 'courier'
			}
			OC_MVM.preparedData( MVMinterface )
			$('#order1click-container-new').lightbox_me( { } )
			return false
		}
			
	} //StockViewModel	
			
	/////////////////////////////////////////
	
	/* Inputs */
	function enableHandlers() {
		if( typeof( $.mask ) !== 'undefined' ) {
			$.mask.definitions['n'] = "[()0-9\ \-]"
			$("#phonemask").mask("+7 nnnnnnnnnnnnnnnnn", { placeholder: " ", maxlength: 10 } )
		}
	}
	/* One Click Order */
	if( $('.order1click-link-new').length ) {
		
		var Model = $('.order1click-link-new').data('model')	
		var inputUrl = $('.order1click-link-new').attr('link-input')		
		var outputUrl = $('.order1click-link-new').attr('link-output')		
		Deliveries = { // zaglushka
			'self': {
				modeId: 4,
				name: 'Доставка',
				price: 400,
				dates: [ {value: '10-02-2012', name: '10 февраля'}, {value: '11-02-2012', name: '11 февраля'} ]

			}
		}
		
		var selfAvailable = false  //'self' in Deliveries
		
		/* Load Data from Server */
		oneClickIsReady = false
		var postData = {
			product_id: Model.jsitemid,
			product_quantity: 1,
			region_id: Model.jsregionid*1
		}
		
		$.post( inputUrl, postData, function(data) {
			if( !data.success || data.data.length === 0 ) {
				OC_MVM = new OneCViewModel() 
				ko.applyBindings( OC_MVM, $('#order1click-container-new')[0] ) // this way, Lukas!
				OC_MVM.noDelivery( true )
				$('.order1click-link-new').remove()
				return false
			}
			Deliveries = data.data
			selfAvailable = 'self' in Deliveries
			if( selfAvailable ) {
				mapCenter = calcMCenter( Deliveries['self'].shops )
			}			
			OC_MVM = new OneCViewModel() 
			ko.applyBindings( OC_MVM, $('#order1click-container-new')[0] ) // this way, Lukas!
			
				
			if( selfAvailable ) {
				window.regionMap = new MapWithShops( mapCenter, $('#map-info_window-container'), 'mapPopup', updateIW )
				window.regionMap.addHandler( '.shopchoose', pickStoreMVM )
			}
			oneClickIsReady = true
			enableHandlers()
		})

		function pickStoreMVM( node ) {	
			var shopnum = $(node).parent().find('.shopnum').text()
			window.regionMap.closeMap( OC_MVM.turnOffMap )
			OC_MVM.chooseShopById( shopnum )
		}
		function updateIW( marker ) {
			if( typeof(OC_MVM) !== 'undefined' )
				OC_MVM.pickShopOnMap( marker.id )
		}
				
		$('.order1click-link-new').bind('click', function(e) { // button 'Купить в один клик'
			e.preventDefault()
			if( !oneClickIsReady )
				return
			if( typeof(_gaq) !== 'undefined' )
				_gaq.push(['_trackEvent', 'QuickOrder', 'Open'])
			$('#order1click-container-new').lightbox_me({
				centered: true,
				onClose: function() {
					if( 'regionMap' in window )
						window.regionMap.closeMap( OC_MVM.turnOffMap )
				}
			})
		})
		
	} // One Click Order

	/* Page 'Where to buy?' , Stock Map */
	
	if( $('#stockBlock').length ) {
		var Model     = $('#stockmodel').data('value')
		var inputUrl  = $('#stockmodel').attr('link-input')
		var outputUrl = $('#stockmodel').attr('link-output')
		var selfAvailable = false
		var currentDate = (new Date()).toISOString().substr(0,10)

		/* Load Data from Server */
		var postData = {
			product_id: Model.jsitemid,
			product_quantity: 1,
			region_id: Model.jsregionid*1
		}
		
		$.post( inputUrl, postData, function(data) {
			if( !data.success ) {
				//SHOW WARNING, NO MVM
				$('.bOrderPreloader').hide()
				$('#noDlvr').show()
				return false
			}

			Deliveries = data.data
			var le = 0
			for(var key in Deliveries)
				le++
			Deliveries.length = le	
			if( 'currentDate' in data )
				if( data.currentDate != '' )
					currentDate = data.currentDate
	
			$('.bOrderPreloader').hide()	
			if( le === 0 ) {
				//SHOW WARNING, NO MVM
				$('#noDlvr').show()
				return false
			}

			selfAvailable = 'self' in Deliveries
			if( !selfAvailable ) {
				//SHOW WARNING, NO SELF DELIVERY
				$('#noDlvr').show()
				return false				
			}
			if( Date.parse( Deliveries['self'].dates[0].value ) !== Date.parse( currentDate ) &&
				Date.parse( Deliveries['self'].dates[0].value ) !== Date.parse( currentDate ) + 1000*60*60*24 ) {
				//SHOW WARNING, NO TODAY AND TOMORROW DELIVERY
				$('#noDlvr').show()
				return false				
			}

			if( selfAvailable ) {
				mapCenter = calcMCenter( Deliveries['self'].shops )
			}			
			MVM = new StockViewModel() 
			ko.applyBindings( MVM , $('#stockCntr')[0] ) // this way, Lukas!
			
			OC_MVM = new OneCViewModel() 
			ko.applyBindings( OC_MVM, $('#order1click-container-new')[0] ) // this way, Lukas!
			enableHandlers()

			if( selfAvailable ) {
				window.regionMap = new MapWithShops( mapCenter, $('#infowindowforstock'), 'stockmap', updateIW )
				window.regionMap.addHandler( '.shopchoose', pickStoreMVM )
			}			

			$('#stockBlock').show()
		})	
		
		function pickStoreMVM( node ) {	
			var shopnum = $(node).parent().find('.shopnum').text()
			MVM.chooseShopById( shopnum )
		}
		function updateIW( marker ) {
			if( typeof(MVM) !== 'undefined' )
				MVM.pickShopOnMap( marker.id )
		}		
	} // Page 'Where to buy?'
});	