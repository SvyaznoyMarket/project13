$(document).ready(function() {
/* One Click Order */
	if( $('.order1click-link-new').length ) {
		
console.info( 'MODEL: ', $('.order1click-link-new').data('model') )
		var Model = $('.order1click-link-new').data('model')
		Deliveries = {
			'courier': {
				modeId: 4,
				name: 'Доставка',
				price: 400,
				dates: [ {value: '10-02-2012', name: '10 февраля'}, {value: '11-02-2012', name: '11 февраля'} ]

			}/*,
			'self' : {
				modeId: 2,
				name: 'Самовывоз',
				price: 0,
				dates: [ {value: '08-02-2012', shopIds: [2,3], name: '8 февраля'}, {value: '09-02-2012', shopIds: [2], name: '9 февраля'} ],
				shops: [
					{id:2,
					name:"г. Москва, м. Ленинский проспект, магазин  на ул. Орджоникидзе, д. 11, стр. 10",
					regtime:"с 9.00 до 21.00",
					address:"м. Ленинский проспект, ул. Орджоникидзе, д. 11, стр. 10",
					latitude:"55.706488",
					longitude:"37.596997" },
					{id:3,
					name:"г. Москва, м. Киевская, магазин на ул. Б. Дорогомиловская, д. 8",
					regtime:"с 9.00 до 22.00",
					address:"м. Киевская, ул. Б. Дорогомиловская, д. 8",
					latitude:"55.746197",
					longitude:"37.565389"}
				]			
			}*/
		}
console.info( 'Deliveries: ', Deliveries )		
		
		var selfAvailable = 'self' in Deliveries
		
		/* ViewModel */
		function MyViewModel() {
console.info('IN DEL ', Deliveries)	
			
			var self = this	
			self.noDelivery = ko.observable(false)
			
			self.title = Model.jstitle
			self.price = Model.jsprice
			self.icon  = Model.jsbimg
			self.shortcut  = Model.jsshortcut
			
			self.quantity = ko.observable(1)
			self.quantityTxt = ko.computed(function() {
				return self.quantity() + ' шт.'
			}, this)
			self.priceTxt = ko.computed(function() {
				return printPrice( self.price )
			}, this)
			
			self.chosenDlvr = ko.observable( {} )
			
			self.dlvrs = ko.observableArray([])
			for(var obj in Deliveries ) {
				self.dlvrs.push( {
					type: obj,
					name: Deliveries[obj].name,
					modeID: Deliveries[obj].modeId,
					price: Deliveries[obj].price
				})
				if( obj == 'self' )
					self.chosenDlvr( self.dlvrs()[ self.dlvrs().length - 1 ] )
			}
console.info( self.chosenDlvr() )			
			if( ! ('type' in self.chosenDlvr() ) )
				self.chosenDlvr( self.dlvrs()[ 0 ] )			
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
				self.loadData()
				return false
			}
			self.minusItem = function() {
				if( self.quantity() > 1 )
					self.quantity( self.quantity() - 1 )
				return false
			}
			self.loadData = function() {
				var postData = {
					product_id: Model.jsitemid,
					product_quantity: 100,
					region_id: Model.jsregionid*1
				}
				
				$.post( inputUrl, postData, function(data) {
					if( !data.success )
						return false
					//self.loaded(true)
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
					if( selfAvailable ) {
						
					}
				})	
			}
			
			self.dates = ko.observableArray( Deliveries[ self.chosenDlvr().type+'' ].dates.slice(0) )
			self.chosenDate = ko.observable( self.dates()[0] )
			self.pickDate = function( item ) {
				self.chosenDate( item )
				//shops mod		
				if( selfAvailable ) {
					if( 'shopIds' in item ) 
						if( item.shopIds.length > 0 ) {
							self.shops.removeAll()// = ko.observableArray( Deliveries['self'].shops.slice(0) )
							for(var key in Deliveries['self'].shops ) {
								console.info( Deliveries['self'].shops[key], item.shopIds.indexOf( Deliveries['self'].shops[key].id ))
								if( item.shopIds.indexOf( Deliveries['self'].shops[key].id ) !== -1 )
									self.shops.push( Deliveries['self'].shops[key] )
							}
						}
				}
				if( self.showMap() )
					self.showMarkers()	
			}
			if( selfAvailable ) {
				self.shops = ko.observableArray( Deliveries['self'].shops.slice(0) )
				self.chosenShop = ko.observable( self.shops()[0] )
				self.pickedShop = ko.observable( self.shops()[0] )
			} else {
				self.shops = ko.observableArray([])
				var leer = { address: '', regtime: '', id : 1 }
				self.chosenShop = ko.observable( leer )
				self.pickedShop = ko.observable( leer )
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
			
			self.showMap = ko.observable( false )
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
			
			self.formStatus = ko.observable( 'typing' ) // 'process' 'error' 'sending'
			self.formStatusTxt = ko.computed( function() {
				var status = ''
				switch( self.formStatus() ) {
					case 'typing':
						status = 'Отправить заказ'
					break
					case 'process':
						status = 'Проверка...'
					break
					case 'error':
						status = 'Произошла ошибка!'
					break
					case 'sending':
						status = 'Отправка...'
					break
				}
				return status
			}, this)
			
			self.textfields = []
			self.textfields.push( ko.observable({
				title: 'Имя получателя',
				name: 'order[recipient_first_name]', //UNIQUE!
				value: '',
				valerror: false,
				regexp: /^[a-zа-я\s]+$/i
			}) )
			self.textfields.push( ko.observable({
				title: 'Телефон для связи',
				name: 'order[recipient_phonenumbers]', //UNIQUE!
				value: '',
				valerror: false,
				regexp: /^[0-9\-\+\s]+$/
			}) )
			
			self.validateField = function( textfield, e ) {
console.info( textfield, e.currentTarget.value, textfield.regexp.test( e.currentTarget.value ) )
				var valerror = false
				if( e.currentTarget.value.replace(/\s/g, '') == '' ||  !textfield.regexp.test( e.currentTarget.value ) ) {
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
				return	
			}

			self.validateForm = function() {
console.info('validateForm')	
				if( self.noDelivery() )
					return false
				if( self.formStatus() !== 'typing' ) // double or repeated click
					return 
				//change title
				self.formStatus('process')
				
				//validate fields
				$('#oneClick input').trigger('change')
//console.info( 'CHECK', self.formStatus()	)
				if( self.formStatus() === 'typing' ) // validation error
					return
				//form request
				
				//send ajax
//				setTimeout( function(){ self.formStatus('sending') }, 500)
				self.sendData()
			}
			
						
			self.sendData = function() {
				self.formStatus('sending')
				var outputUrl = 'none'
				var postData = {
					'order[product_quantity]' : self.quantity(),
					'order[delivered_at]' : self.chosenDate().value
//					delivery: self.chosenDlvr().modeID					
				}
				if( self.chosenDlvr().type == 'self' )
					postData[ 'order[shop_id]' ] = self.chosenShop().id
				for(var i=0,l=self.textfields.length; i<l; i++)
					postData[ self.textfields[i]().name + '' ] = self.textfields[i]().value
console.info( postData)
/*				$.post( outputUrl, postData, function() {
					
				})
*/				
			}
		}	// 
		
		
		/* Load Data from Server */
		oneClickIsReady = false
		var inputUrl = $('.order1click-link-new').attr('link-input')
		var postData = {
			product_id: Model.jsitemid,
			product_quantity: 1,
			region_id: Model.jsregionid*1
		}
		
		$.post( inputUrl, postData, function(data) {
			//self.loaded(true)
			Deliveries = data.data
			selfAvailable = 'self' in Deliveries
			if( selfAvailable ) {
				var sla=0, slo=0
				for(var i=0, l=Deliveries['self'].shops.length ;i<l;i++) {
					sla += Deliveries['self'].shops[i].latitude*1
					slo += Deliveries['self'].shops[i].longitude*1		
				}
				var mapCenter = {
					latitude  : sla/Deliveries['self'].shops.length,
					longitude : slo/Deliveries['self'].shops.length
				}
			}			
			
			MVM = new MyViewModel() 
			ko.applyBindings(MVM) // this way, Lukas!
				
			if( selfAvailable )
				window.regionMap = new MapWithShops( mapCenter, $('#map-info_window-container'), 'mapPopup' )			
			oneClickIsReady = true
		})

		/* MAP */
		function MapWithShops( center, infoWindowTemplate, DOMid ) {
			var self = this
			self.mapWS = null
			self.infoWindow = null
			self.positionC = null
			self.markers = []
	
			function create() {
				self.positionC = new google.maps.LatLng(center.latitude, center.longitude)			
				var options = {
					zoom: 11,
					center: self.positionC,
					scrollwheel: false,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
					}
				}
				self.mapWS = new google.maps.Map( document.getElementById( DOMid ), options )
				self.infoWindow = new google.maps.InfoWindow({
					maxWidth: 400,
					disableAutoPan: false
				})
			}
	
			this.showInfobox = function( marker ) {
				var item = self.markers[marker.id]
				MVM.pickShopOnMap( marker.id )
				marker.setVisible(false) // hides marker
	
				self.infoWindow.setContent( infoWindowTemplate.prop('innerHTML') )
				self.infoWindow.setPosition( marker.position )
				self.infoWindow.open( self.mapWS )
				google.maps.event.addListener( self.infoWindow, 'closeclick', function() { marker.setVisible(true) })
			}
	
			this.showMarkers = function( markers ) {
				$.each( self.markers, function(i, item) {
					 if( typeof( item.ref ) !== 'undefined' )
						item.ref.setMap(null)
				})
				self.markers = markers
				google.maps.event.trigger( self.mapWS, 'resize' )
				self.mapWS.setCenter( self.positionC )
				$.each( markers, function(i, item) {
					var marker = new google.maps.Marker({
					  position: new google.maps.LatLng(item.latitude, item.longitude),
					  map: self.mapWS,
					  title: item.name,
					  //icon: item.markerImg,
					  icon: '/images/marker.png',
					  id: item.id
					})
					google.maps.event.addListener(marker, 'click', function() { self.showInfobox(this) })
					self.markers[marker.id].ref = marker
				})
			}
	
			this.closeMap = function( markers ) {
				self.infoWindow.close()
				$('#mapPopup').hide('blind', null, 800, function() {
					MVM.showMap(false) // hides map
				})
			}
	
			/* main() */
			create()
	
		} // object MapWithShops

		
		var mapContainer = $('#mapPopup')
		mapContainer.delegate('.shopchoose', 'click', function(e) { //desktops
			e.preventDefault()
			pickStore( e.target )
		})	
		function handleStart(e) {
			e.preventDefault()
			if( e.target.className.match('shopchoose') )
				pickStore( e.target )
		}
		mapContainer[0].addEventListener("touchstart", handleStart  , false) //touch devices
	
		function pickStore( node ) {
			var shopnum = $(node).parent().find('.shopnum').text()
			window.regionMap.closeMap()
			for(var i=0, l=MVM.shops().length; i<l; i++) {
				if( MVM.shops()[i].id == shopnum )
					MVM.chosenShop( MVM.shops()[i] )
			}
		}
		
		// MAP end 
		
		$('#order1click-container-new').delegate( '.bSelect', 'click', function() {	// custom selectors
			$(this).find('.bSelect__eDropmenu').toggle()
		})

		$('.order1click-link-new').bind('click', function(e) { // button 'Купить в один клик'
			e.preventDefault()
			if( !oneClickIsReady )
				return
			if( typeof(_gaq) !== 'undefined' )
				_gaq.push(['_trackEvent', 'QuickOrder', 'Open'])
			$('#order1click-container-new').lightbox_me({
				centered: true,
				onClose: function() {
					MVM.showMap(false)
				}
			})
		})
		
	} // One Click Order
	
	
	/* Page 'Where to buy?' , shops map */
	
    if( $('#gMap').length ) {
		$('#gMap').bind({
			create: function(e, center, markers, infoWindowTemplate) {
				var el = $(this)
		
				var position = new google.maps.LatLng(center.latitude, center.longitude);
				var options = {
				  zoom: 11,
				  center: position,
				  scrollwheel: false,
				  mapTypeId: google.maps.MapTypeId.ROADMAP,
				  /*
				  scaleControl: ,
				  navigationControlOptions: {
					style: google.maps.NavigationControlStyle.DEFAULT
				  },
				  */
				  mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
				  }
				  
				}
			  var map = new google.maps.Map(document.getElementById(el.attr('id')), options)
			  
			  //var infoWindow = new google.maps.InfoWindow()
			  var infoWindow = new InfoBox({ // http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/docs/examples.html
				disableAutoPan: false,
				maxWidth: 0,
				pixelOffset: new google.maps.Size(-11, -108),
				zIndex: null,
				boxStyle: {
				  opacity: 0.85,
				  width: '280px'
				},
				//closeBoxMargin: "10px 2px 2px 2px",
				closeBoxURL: 'http://www.google.com/intl/en_us/mapfiles/close.gif',
				//closeBoxURL: '',
				infoBoxClearance: new google.maps.Size(1, 1),
				isHidden: false,
				pane: 'floatShadow',
				enableEventPropagation: true
			  })
			
			  var showWindow = function() {
				var item = markers[this.id]
			
				el.trigger('showMarkers')
				el.trigger('infoWindow', [ this, item ])
			  }
			
			  // set markers
			  el.data('markers', [])
			  $.each(markers, function(i, item) {
				var marker = new google.maps.Marker({
				  position: new google.maps.LatLng(item.latitude, item.longitude),
				  map: map,
				  title: item.name,
				  icon: '/images/marker.png',
				  id: item.id
				})
				google.maps.event.addListener(marker, 'click', showWindow);
				el.data('markers').push(marker)
			  })
			
			  google.maps.event.addListener(map, 'bounds_changed', function () {
				//el.data('infoWindow').close()
			  })
			  google.maps.event.addListener(map, 'click', function () {
				//el.data('infoWindow').close()
			  })
			  google.maps.event.addListener(infoWindow, 'closeclick', function () {
				el.trigger('showMarkers')
			  })
			
			  el.data('map', map)
			  el.data('infoWindow', infoWindow)
			  el.data('infoWindowTemplate', infoWindowTemplate)
			},
			move: function(e, center) {
			  var el = $(this)
			  var map = el.data('map')
			},
			infoWindow: function(e, marker, item) {
			  var el = $(this)
			  var map = el.data('map')
			  var infoWindow = el.data('infoWindow')
			  var infoWindowTemplate = el.data('infoWindowTemplate')
			  // hides marker
			  marker.setMap(null)
			  $.each(infoWindowTemplate.find('[data-name]'), function(i, el) {
				el.innerHTML = item[$(el).data('name')]
			  })
			
			  infoWindow.setContent(infoWindowTemplate.prop('innerHTML'));
			  infoWindow.open(map, marker);
			},
			showMarkers: function() {
			  var el = $(this)
			  $.each(el.data('markers'), function(i, marker) {
				if (null == marker.map) {
				  marker.setMap(el.data('map'))
				}
			  })
			}
		})
		
		var mapContainer = $('#gMap')
		
		mapContainer.trigger('create', [
			$('#map-center').data('content'),
			$('#map-markers').data('content'),
			$('#map-info_window-container')
		])
		
		mapContainer.delegate('.shopchoose', 'click', function(e) { //desktops
			pickStore( e.target )
		})	
		function handleStart(e) {
			if( e.target.className.match('shopchoose') )
				pickStore( e.target )
		}
		mapContainer[0].addEventListener("touchstart", handleStart  , false) //touch devices
		
	
		function pickStore( node ) {
			getOneClick( $(node).parent().find('.shopnum').text() )
		}
		
		$('.bInShopLine__eButton a').bind('click', function(e) {
			e.preventDefault()
			getOneClick( $(this).attr('href') )
		})  
		
		$('.bInShop__eCurrent a').click( function(){
			$.getJSON( '/region/init', function(data) { //double /* GEOIP fix */ in dash.js
				if( !data.success ) 
					return false
				// paint popup			
				var cities = data.data
				var shtorka = $('<div>').addClass('graying')
										.css( { 'opacity': '0.5'} ) //ie special							
				var cityPopup = $('<div class="bCityPopupWrap">').html(
					'<div class="hideblock bCityPopup">'+
						'<i title="Закрыть" class="close">Закрыть</i>'+
						'<div class="title">Привет! Из какого вы города?</div>'+
					'</div>'+
				'</div>')
				cityPopup.find('.close').click( function() {
					$('.graying').remove()
					$('.bCityPopupWrap').hide()
				})
				for( var ci = 0, cl = cities.length; ci < cl; ci++ ) {
					if( typeof( cities[ci].link ) === 'undefined' || typeof( cities[ci].name ) === 'undefined' )
						continue
					var cnode = $('<div>').append( $('<a>').attr( 'href', cities[ci].link ).text( cities[ci].name ) )
					if( typeof( cities[ci].is_active ) !== 'undefined' ) {
						cnode.addClass('bCityPopup__eCurrent')
						cityPopup.find('.title').after( cnode )
					} else {
						cnode.addClass('bCityPopup__eBlock')
						cityPopup.find('div:first').append( cnode )
					}
				}
				$('body').append( shtorka ).append( cityPopup )
			})		
			return false
		})
    }	
	
	function getOneClick( href ){
		$('#ajaxgoods').lightbox_me({
			centered: true,
			closeClick: false,
			closeEsc: false
		})

		$.get( href, function( response ) {
			$('#ajaxgoods').hide()
			if( typeof(response.success) !== 'undefined' && response.success ) {
				$('#order1click-form').html(response.data.form)
				if( typeof(response.data.shop) !== 'undefined' ) {
				if( typeof(response.data.shop.name) !== 'undefined' ) {
					$('.sLocation').remove()
					$('#order1click-container h2').text('Оформить и забрать в магазине')
						.after( $('<div>').addClass('pb10').addClass('sLocation')
							.html( response.data.shop.name + '. Время работы: ' + response.data.shop.regime ) )
				}
				}
				$('#order1click-container').lightbox_me({
					centered: true
				})
				cl1loaded = true
				bindCalc()
			}
		})
	}
	//if( $('.order1click-link').length ) {
		var cl1loaded = false
		$('.order1click-link').bind('click', function(e) {
			e.preventDefault()
			if( typeof(_gaq) !== 'undefined' )
				_gaq.push(['_trackEvent', 'QuickOrder', 'Open'])
			if ( !cl1loaded ) {
				getOneClick( $(this).attr('href') )
			} else {
				$('#order1click-container').lightbox_me({
					centered: true
				})
			}
		})

		function bindCalc() {
			var quant = $('#order_product_quantity').val()*1 || 1
			var pric  = Math.round( $('.b1Click__ePriceBig .price').html().replace(/\s/g,'')*1 / quant )
			function recalc( delta ) {
				if( quant == 1 && delta < 0 )
					return
				quant += delta
				var sum = printPrice( pric * quant )
				$('.c1quant').html( quant+ ' шт.')
				$('#order_product_quantity').val( quant )
				$('.b1Click__ePriceBig .price').html( sum )
			}

			$('.c1less').live( 'click', function(){ recalc(-1) })
			$('.c1more').live( 'click', function(){ recalc(1) })
		}

		$('#order1click-form').bind('submit', function(e) {
			e.preventDefault()
			var form = $(this)

			function get1ClickResult( response ) {
				if( !response.success ) {
						if( response.data ) {
							$('#order1click-form').html(response.data.form)
						}
						var button = $('#order1click-form').find('input:submit')
						button.attr('disabled', false)
						button.val('Оформить заказ')
						if( !$('#warn').length ) {
							var warn = $('<span id="warn" style="color:red">').html('Не удалось оформить заказ. Приносим свои извинения! Повторите попытку или обратитесь с заказом в контакт cENTER&nbsp;8&nbsp;(800)&nbsp;700&nbsp;00&nbsp;09')
							$('.bFormB2').before( warn )
						}
					} else {
						if( response.data ) {
							$('#order1click-container').find('h2').html(response.data.title)
							$('#order1click-form').replaceWith(response.data.content)
							if( runAnalitics )
								runAnalitics()
						}
					}			
			}

			var button = form.find('input:submit')
			button.attr('disabled', true)
			button.val('Оформляю заказ...')

			var wholemessage = form.serializeArray()
			$.ajax({
				type: 'POST',
				url: form.attr('action'),
				data: wholemessage,
				success: get1ClickResult
			})

		})

    //}
});	