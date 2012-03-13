$(document).ready(function(){

	/* Model Simulation */
	orderModel = {
		Rapid : {
			addCost  : 290, 
			dlvrDate : '12 декабря',
			dlvrTime : 'с 9:00 до 14:00',
			vcalend : [ 
				{ sw: 0, state: 'dis', dv: '10 декабря', dhtml: '10 <span>Пн</span>'},
				{ sw: 0, state: 'dis', dv: '11 декабря', dhtml: '11 <span>Вт</span>'}, 
				{ sw: 0, state: 'act', dv: '12 декабря', dhtml: '12 <span>Ср</span>'},
				{ sw: 0, state: 'act', dv: '13 декабря', dhtml: '13 <span>Чт</span>'}, 
				{ sw: 0, state: 'act', dv: '14 декабря', dhtml: '14 <span>Пт</span>'},
				{ sw: 0, state: 'act', dv: '15 декабря', dhtml: '15 <span>Сб</span>'}, 
				{ sw: 0, state: 'act', dv: '16 декабря', dhtml: '16 <span>Вс</span>'},
				{ sw: 1, state: 'act', dv: '17 декабря', dhtml: '17 <span>Пн</span>'},
				{ sw: 1, state: 'act', dv: '18 декабря', dhtml: '18 <span>Вт</span>'},			
				{ sw: 1, state: 'act', dv: '19 декабря', dhtml: '19 <span>Ср</span>'},
				{ sw: 1, state: 'act', dv: '20 декабря', dhtml: '20 <span>Чт</span>'}, 
				{ sw: 1, state: 'act', dv: '21 декабря', dhtml: '21 <span>Пт</span>'},
				{ sw: 1, state: 'act', dv: '22 декабря', dhtml: '22 <span>Сб</span>'}, 
				{ sw: 1, state: 'act', dv: '23 декабря', dhtml: '23 <span>Вс</span>'}
			],
			schedule : [ 'с 9:00 до 14:00', 'с 14:00 до 18:00', 'с 18:00 до 21:00' ],
			products : [
				{ moveable: true, dlvr: [{ txt: 'В доставку', lbl: 'delay'}, { txt: 'В самовывоз', lbl: 'selfy'}],
					price: '999', title: 'Чайник со свистком 2,5 л', hm: '2', img: '/images/z_img2.png',
					locs: [ 1, 2 ] },
				{ moveable: false, 
					price: '1 400', title: 'Мягкая игрушка Fancy &laquo;Медведь Топа&raquo;', hm: '3', img: '/images/z_img1.png',
					locs: [ 1, 2 ] },
				{ moveable: true, dlvr: [{ txt: 'В доставку', lbl: 'delay'}, { txt: 'В самовывоз', lbl: 'selfy'}],
					price: '4 495', title: 'Смартфон Samsung Wave 525 черный', hm: '1', img: '/images/z_img8.png',
					locs: [ 2 ] }            
			]
		} , //Rapid
		Delay : {
			addCost  : 700, 
			dlvrDate : '22 декабря',
			dlvrTime : 'с 9:00 до 14:00',
			vcalend : [ 
				{ sw: 0, state: 'dis', dv: '20 декабря', dhtml: '20 <span>Пн</span>'},
				{ sw: 0, state: 'dis', dv: '21 декабря', dhtml: '21 <span>Вт</span>'}, 
				{ sw: 0, state: 'act', dv: '22 декабря', dhtml: '22 <span>Ср</span>'},
				{ sw: 0, state: 'act', dv: '23 декабря', dhtml: '23 <span>Чт</span>'}, 
				{ sw: 0, state: 'act', dv: '24 декабря', dhtml: '24 <span>Пт</span>'},
				{ sw: 0, state: 'act', dv: '25 декабря', dhtml: '25 <span>Сб</span>'}, 
				{ sw: 0, state: 'act', dv: '26 декабря', dhtml: '26 <span>Вс</span>'},
				{ sw: 1, state: 'act', dv: '27 декабря', dhtml: '27 <span>Пн</span>'},
				{ sw: 1, state: 'act', dv: '28 декабря', dhtml: '28 <span>Вт</span>'},			
				{ sw: 1, state: 'act', dv: '29 декабря', dhtml: '29 <span>Ср</span>'},
				{ sw: 1, state: 'act', dv: '30 декабря', dhtml: '30 <span>Чт</span>'}, 
				{ sw: 1, state: 'act', dv: '31 декабря', dhtml: '31 <span>Пт</span>'},
				{ sw: 1, state: 'act', dv: '01 января', dhtml: '01 <span>Сб</span>'}, 
				{ sw: 1, state: 'act', dv: '02 января', dhtml: '02 <span>Вс</span>'}
			],
			schedule : [ 'с 9:00 до 14:00', 'с 14:00 до 18:00', 'с 18:00 до 21:00' ],
			products : [
				{ moveable: false, price: '24 190', title: 'Угловой диван-кровать Вика 32 ', hm: '1', img: '/images/z_img3.png' },
				{ moveable: false, price: '1 900', title: 'Утилизация демонтированной мебели', hm: '2', img: '/images/z_img4.png' }
			]
		} , //Delay
		Selfy : {
			addCost  : 0, 
			dlvrDate : '13 декабря',
			vcalend : [ 
				{ sw: 0, state: 'dis', dv: '10 декабря', dhtml: '10 <span>Пн</span>'},
				{ sw: 0, state: 'dis', dv: '11 декабря', dhtml: '11 <span>Вт</span>'}, 
				{ sw: 0, state: 'act', dv: '12 декабря', dhtml: '12 <span>Ср</span>'},
				{ sw: 0, state: 'act', dv: '13 декабря', dhtml: '13 <span>Чт</span>'}, 
				{ sw: 0, state: 'act', dv: '14 декабря', dhtml: '14 <span>Пт</span>'},
				{ sw: 0, state: 'act', dv: '15 декабря', dhtml: '15 <span>Сб</span>'}, 
				{ sw: 0, state: 'act', dv: '16 декабря', dhtml: '16 <span>Вс</span>'},
				{ sw: 1, state: 'act', dv: '17 декабря', dhtml: '17 <span>Пн</span>'},
				{ sw: 1, state: 'act', dv: '18 декабря', dhtml: '18 <span>Вт</span>'},			
				{ sw: 1, state: 'act', dv: '19 декабря', dhtml: '19 <span>Ср</span>'},
				{ sw: 1, state: 'act', dv: '20 декабря', dhtml: '20 <span>Чт</span>'}, 
				{ sw: 1, state: 'act', dv: '21 декабря', dhtml: '21 <span>Пт</span>'},
				{ sw: 1, state: 'act', dv: '22 декабря', dhtml: '22 <span>Сб</span>'}, 
				{ sw: 1, state: 'act', dv: '23 декабря', dhtml: '23 <span>Вс</span>'}
			],
			shops: [
				{ shid: 1, title: 'м. Белорусская, магазин на ул. Грузинский вал, д. 31', fromto: 'с 9.00 до 22.00', products: [
					{ moveable: false, price: '9 900', title: 'Сноуборд Salomon Salvatore Sanchez ', hm: '1', img: '/images/z_img6.png',
						locs: [ 1, 2, 3 ] },
					{ moveable: false, price: '3 900', title: 'Сноубордические ботинки Head Classic', hm: '2', img: '/images/z_img7.png',
						locs: [ 1, 2 ] }
				] },
				{ shid: 2, title: 'м. Ленинский проспект, ул. Орджоникидзе, д. 11, стр. 10', fromto: 'с 9.00 до 22.00', products: [
					{ moveable: false, price: '800', title: 'Фигурка South Park Cartman Talking Wacky Wobbeler', hm: '2', img: '/images/z_img9.png',
						locs: [ 2 ]}
				] },
				{ shid: 3, title: 'м. Киевская, ул. Б. Дорогомиловская, д. 8', fromto: 'с 9.00 до 22.00', products: [
					{ moveable: false, price: '4 490', title: 'Смартфон Samsung Wave 525 черный ', hm: '2', img: '/images/z_img8.png',
						locs: [ 3 ]}
				] },
				{ shid: 220, title: 'новый магаз', fromto: 'с 9.00 до 22.00' , products: [] }
			]
		}, //Selfy
		allshops: [
			{ shid: 1, title: 'м. Белорусская, магазин на ул. Грузинский вал, д. 31', fromto: 'с 9.00 до 22.00',
				latitude: 55.775004, longitude: 37.581675, markerImg: '' },
			{ shid: 2, title: 'м. Ленинский проспект, ул. Орджоникидзе, д. 11, стр. 10', fromto: 'с 9.00 до 22.00',
				latitude: 55.706488, longitude: 37.596997, markerImg: '' },
			{ shid: 3, title: 'м. Киевская, ул. Б. Дорогомиловская, д. 8', fromto: 'с 9.00 до 22.00',
				latitude: 55.746197, longitude: 37.565389, markerImg: '' },
			{ shid: 220, title: 'новый магаз', fromto: 'с 9.00 до 22.00',
				latitude: 55.851993, longitude: 37.442905, markerImg: '' }
		]
	}
	
	/* ViewModel */
	function MyViewModel() {
		var self = this
					
		function customCal( papa, cd, dd, ct, sch ) {
			var me = this
			me.papa = papa
			me.curDate  = ko.observable( cd )
			me.dates    =  dd 
			me.weeknum  = ko.observable( false )
			me.cWeek = function(d, e){
				if( ! $(e.currentTarget).hasClass('mDisabled') ) {
					me.weeknum(  ! me.weeknum() )
					//me.papa.find('.bBuyingDates li.jsdate').toggleClass('erased')
				}
			}
			me.pickDate = function( dateit, e ) {
				if( $(e.currentTarget).hasClass('bBuyingDates__eDisable') )
					return false
				me.curDate( dateit.dv )
				me.papa.find('.bBuyingDatePopup').css({'left': $(e.target).position().left }).show()
			}
			if( typeof(ct) !== 'undefined' ) {
				me.curTime  = ko.observable( ct )
				me.schedule = ko.observableArray( sch )				
				me.pickTime = function( timeit ) {
					me.curTime( timeit )
				}
			}
		}

		self.addCost  = orderModel.Rapid.addCost
		self.bitems   = ko.observableArray( orderModel.Rapid.products )

		self.RapidCalend = new customCal( $('.rapid'), orderModel.Rapid.dlvrDate , orderModel.Rapid.vcalend, orderModel.Rapid.dlvrTime, orderModel.Rapid.schedule)

		self.addCost_D  = orderModel.Delay.addCost
		self.bitems_D   = ko.observableArray( orderModel.Delay.products )

		self.DelayCalend = new customCal( $('.delay'), orderModel.Delay.dlvrDate , orderModel.Delay.vcalend, orderModel.Delay.dlvrTime, orderModel.Delay.schedule)

		self.shops      = ko.observableArray( orderModel.Selfy.shops )

		self.SelfyCalend = new customCal( $('.selfy'), orderModel.Selfy.dlvrDate , orderModel.Selfy.vcalend )
		
		for(var b=0, lb= self.bitems().length; b<lb; b++) {
			if( typeof( self.bitems()[b].dlvr ) !== 'undefined' )
			for(var d=0, ld= self.bitems()[b].dlvr.length; d<ld; d++){
				var tmpd = self.bitems()[b].dlvr[d]
				var vedro = self.bitems()[b].dlvr[d].lbl
				self.bitems()[b].dlvr[d].lbl = ko.observable( vedro )
				tmpd.txt = ko.computed( function(){
					switch( this.lbl() ) {
						case 'delay':
							return 'В доставку ' + self.DelayCalend.curDate()
						break
						case 'rapid':
							return 'В доставку ' + self.RapidCalend.curDate()
						break
						case 'selfy':
							return 'В самовывоз ' + self.SelfyCalend.curDate()
						break
					}
				}, tmpd)
			}	
		}	
		
		for(var s=0, l= self.shops().length; s<l; s++)
			self.shops()[s].products = ko.observableArray( orderModel.Selfy.shops[s].products )
			
		self.shifting = function( line, sender, target, e ) {
			$(e.currentTarget).parent().parent().find('.mBacket').trigger('click') // hack

			switch( target.lbl() ) {
			case "delay":
				self.bitems_D.unshift( line )
			break
			case "rapid":
				self.bitems.unshift( line )
			break
			case "selfy":
ull:				for(var i=0, li=line.locs.length; i < li; i++) {
					for(var j=0, lj=self.shops().length; j < lj; j++) {
						if( line.locs[i] === self.shops()[j].shid ) {
							self.shops()[j].products.unshift( line )
							break ull //up level loop
						}	
					}
				}
			break
			}
			var ind = line.dlvr.indexOf( target )
			line.dlvr[ ind ].lbl ( sender )
		}
		
		self.totalPrice = ko.computed(function() {
			var tp = self.addCost
			for(var i=0; i<self.bitems().length; i++)
				tp += self.bitems()[i].price.replace(/\D/g,'')*1
			return printPrice( tp )
		}, this)
		
		self.totalPrice_D = ko.computed(function() {
			var tp = self.addCost_D
			for(var i=0; i<self.bitems_D().length; i++)
				tp += self.bitems_D()[i].price.replace(/\D/g,'')*1
			return printPrice( tp )
		}, this)

		self.totalPrice_S = ko.computed(function() {
			var tp = 0
			for(var i=0; i<self.shops().length; i++)
				for(var j=0; j<self.shops()[i].products().length; j++)
				tp += self.shops()[i].products()[j].price.replace(/\D/g,'')*1
			return printPrice( tp )
		}, this)

		self.totalSum = ko.computed(function() {
			var tp = self.totalPrice().replace(/\D/g,'')*1 + self.totalPrice_D().replace(/\D/g,'')*1 + self.totalPrice_S().replace(/\D/g,'')*1
			return printPrice( tp )
		}, this)
		
		self.removeIt = function(item) {
			self.bitems.remove(item)
			self.bitems_D.remove(item)
		}
		
		self.removeFromShop = function( shop, item ) {
			var ind = self.shops.indexOf( shop )
			self.shops()[ind].products.remove(item)
			//if( !self.shops()[ind].products().length )
				//self.shops.remove( shop )
		}
		
		self.allshops = orderModel.allshops
		
		self.productforPopup = ko.observable( {
			moveable: false, price: '9 900', 
			title: 'Сноуборд Salomon Salvatore Sanchez ', hm: '1', img: '/images/z_img6.png',
			locs: [ 20, 10 ] 
		} )
		
		self.popupWithShops = ko.observableArray([])
		
		var shopSender = null
		var movingItem = null
		
		self.shiftingInShops = function( shopReciever ) {
			if( typeof(shopReciever) === 'object' )
				shopReciever = shopReciever.shid
//console.info('rec ', movingItem)
//console.info( self.shops.indexOf( shopSender ), shopReciever )
			self.shops()[ self.shops.indexOf( shopSender ) ].products.remove( movingItem )
			for(var i=0, l=self.shops().length; i<l; i++) {
				if( self.shops()[i].shid == shopReciever ) {
					self.shops()[i].products.push( movingItem )
					break
				}	
			}
			$('.mMapPopup').trigger('close')
		}
		
		self.fillPopupWithShops = function( shop, item ) {
			shopSender = shop
			movingItem = item
			self.productforPopup(item)
			var double_popupWithShops = self.popupWithShops.slice(0)
			var doublelocs = item.locs.slice(0)
sloop:			for(var s=0, ls=double_popupWithShops.length; s<ls; s++) {
				for(var i=0, l=item.locs.length; i<l; i++) {
					var shid = item.locs[i]
//console.info(s)
					if( double_popupWithShops[s].shid == shid ) {
						doublelocs[i] = 0
						continue sloop
					}	
				}
//console.info(s,double_popupWithShops[s])
				self.popupWithShops.remove( double_popupWithShops[s] )
			}
//console.info('doublelocs', doublelocs)
locsloop:		for(var i=0, l=doublelocs.length; i<l; i++) {
				if(  !doublelocs[i] ) continue
				for(var asi=0, asl=self.allshops.length; asi<asl; asi++) {
					if( self.allshops[asi].shid == doublelocs[i] ) {
//console.info( asi )
						var shopitem = self.allshops[asi]
						shopitem.markerImg = ko.observable( )
						self.popupWithShops.push( self.allshops[asi] )
						continue locsloop
					}
				}
			}
			for(var i=0, l=self.popupWithShops().length; i<l; i++) {
				self.popupWithShops()[i].markerImg ( '/images/marker_'+(i+1)+'.png' )
			}
			double_popupWithShops = null
			doublelocs = null
		} // fillPopupWithShops
 
	}
	MVM = new MyViewModel() //global TODO local
	
	ko.applyBindings(MVM) // this way, Lukas!
	
	
	/* JQUERY handlers */
	
	$('body').delegate('.bImgButton', 'mouseenter', function(){
		var tipData = {cssl: '-64px', tiptext: 'tip'}
		if( $(this).hasClass('mMap') )
			tipData = {cssl: '-64px', tiptext: 'Выбрать другой магазин'}
		if( $(this).hasClass('mBacket') )
			tipData = {cssl: '-36px', tiptext: 'Удалить товар'}
		if( $(this).hasClass('mArrows') )
			tipData = {cssl: '-87px', tiptext: 'Поместить товар в другой заказ'}
		$(this).html( tmpl("tip_tmpl", tipData) )
	})
	
	$('body').delegate('.bImgButton', 'mouseleave', function(){ 
		$(this).empty()		
	})
	
	$('body').delegate('.bButtonPopup', 'mouseleave', function(){
		$(this).hide()
	})
	
	$('body').delegate('.bBuyingDatePopup', 'mouseleave', function(){
		$(this).hide()
	})	
	
	$('body').delegate('.mArrows', 'click', function(e){
		e.preventDefault()
		$(this).parent().find('.bButtonPopup').show()
		return false
	})		
	$('body').delegate('.mMap', 'click', function(e){
		e.preventDefault()
		// TODO проверка загруженности карты
		var markersPull = {}
		var tmp = MVM.popupWithShops()
		for(var i=0, l = tmp.length; i<l; i++) {
			var key = tmp[i].shid + ''
			markersPull[ key ] = {
				id: tmp[i].shid,
				name: tmp[i].title,
				regtime: tmp[i].fromto,
				latitude: tmp[i].latitude,
				longitude: tmp[i].longitude,
				markerImg: tmp[i].markerImg()
			}
		}
//console.info(markersPull)
		$('.mMapPopup').lightbox_me({
			centered: true,
			onLoad: function() {
				regionMap.showMarkers( markersPull )
			}
		})
		
	}) //.mMap click
	
	
	function MapWithShops( center, infoWindowTemplate, DOMid ) {
//console.info( arguments )
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
			disableAutoPan: false,
			maxWidth: 280
		  })
		}

		this.showInfobox = function( marker ) {
			var item = self.markers[marker.id]
			marker.setVisible(false) // hides marker
			$.each( infoWindowTemplate.find('[data-name]'), function(i, el) {
				el.innerHTML = item[$(el).data('name')]
			})
			
			self.infoWindow.setContent( infoWindowTemplate.prop('innerHTML') )
			self.infoWindow.setPosition( marker.position )
			self.infoWindow.open( self.mapWS )
			google.maps.event.addListener( self.infoWindow, 'closeclick', function() { marker.setVisible(true) })
		}

		this.showMarkers = function( markers ) {
			$.each( self.markers, function(i, item) {
//console.info( item.ref, item.name)
				 if( typeof( item.ref ) !== 'undefined' )
					item.ref.setMap(null)
			})
			self.markers = markers
			google.maps.event.trigger( self.mapWS, 'resize' )
			self.mapWS.setCenter( self.positionC )
			$.each( markers, function(i, item) {
//console.info(item)
				var marker = new google.maps.Marker({
				  position: new google.maps.LatLng(item.latitude, item.longitude),
				  map: self.mapWS,
				  title: item.name,
				  icon: item.markerImg,
				  id: item.id
				})
				google.maps.event.addListener(marker, 'click', function() { self.showInfobox(this) })
				self.markers[marker.id].ref = marker
			})
		}
		
		/* main() */
		create()
		
	} // object MapWithShops

	window.regionMap = new MapWithShops( $('#map-center').data('content'),
									  $('#map-info_window-container'), 'mapPopup' )
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
//console.info('pickME ', node)
		MVM.shiftingInShops( $(node).parent().find('.shopnum').text() )
	}
	
	/* other form handlers */
	$('body').delegate('.bBuyingLine label', 'click', function() {
		if( $(this).find('input').attr('type') == 'radio' ) {
			var thatName = $('.mChecked input[name="'+$(this).find('input').attr('name')+'"]')
			if( thatName.length ) {
				thatName.each( function(i, item) {
					$(item).parent('label').removeClass('mChecked')
				})
			}
		}
		
		$(this).addClass('mChecked')
	})	

	$('body').delegate('.bBuyingLine input:radio, .bBuyingLine input:checkbox', 'click', function(e) {
console.info( e.target, e.currentTarget, e.timeStamp, $(this).attr('checked') )
		e.stopPropagation()
	})	

});	