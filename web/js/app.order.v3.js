$(document).ready(function() {
	/* Model Simulation 
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
	*/
	var orderModel = { Rapid: {}, Delay: {}, Selfy: {} }
	/* Sync Model */
	
	function getDateDM( datestring ) {
		var dd = new Date( datestring )
		var monthA = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 
		'сентября', 'октября', 'ноября', 'декабря']
		return dd.getDate() + ' ' + monthA[ dd.getMonth() ]
	}
	
	function getDateHTML( datestring ) {
		var dd = new Date( datestring )
		var weekdays = ['Вс','Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']
		return dd.getDate() + ' <span>' + weekdays[ dd.getDay() ] + '</span>'
	}	
	
	function getTimeFT( timeobj ) {
		var time_begin = timeobj.time_begin
		if( time_begin[0] == '0' )
			time_begin = time_begin.slice(1)
		return 'с ' + time_begin + ' до ' + timeobj.time_end
	}
	
	function fillSIPartly( sw, act, datestring ) {
		var item = {}
		item.sw       = sw
		item.state    = act
		item.dv       = getDateDM( datestring )
		item.dhtml    = getDateHTML( datestring )
		item.ISO      = datestring
		item.schedule = []
		return item
	}	
	
console.info( $('#delivery-map').data('value') )
	var ServerModel =  $('#delivery-map').data('value')
	function syncBlock( _sender, _receiver ) {
	
		_receiver.addCost  = _sender.price*1
		_receiver.dlvrDate = getDateDM( _sender.date_default )
		_receiver.ISODate  = _sender.date_default
		_receiver.dlvrTime = getTimeFT( _sender.date_list[0].interval[0] )
		_receiver.dlvrID   = _sender.date_list[0].interval[0].id
		_receiver.vcalend  = []
		var defaultDate = new Date( _sender.date_list[0].date )
		var lost = defaultDate.getDay()
		if( lost === 0 ) // voskresenie
			lost = 6
		else	
			lost --
		defaultDate.setDate( defaultDate.getDate() - lost )
	
		for(var i=0; i < lost; i++) {
			var scheduleItem = fillSIPartly( 0, 'dis', defaultDate.getTime() )
			_receiver.vcalend.push( scheduleItem ) 
			
			defaultDate.setDate( defaultDate.getDate() + 1 )
			scheduleItem = {}
		}
		
		for(var i=0, l= _sender.date_list.length; (i < l) && (i < 14 - lost); i++) {
			var item = _sender.date_list[i]					
			var scheduleItem = fillSIPartly(  ( i < (7 - lost) ) ? 0 : 1 , 'act', item.date )
			for(var j=0, jl = item.interval.length; j < jl; j++) {
				var intervalItem = {}
				intervalItem.id  = item.interval[j].id
				intervalItem.txt = getTimeFT( item.interval[j] )
				scheduleItem.schedule.push( intervalItem )
				intervalItem  = {}
			}

			_receiver.vcalend.push( scheduleItem ) 
			scheduleItem = {}
			item = {}
		}
	} // syncBlock function
	
	function syncProducts( _sender, _receiver ) {
		_receiver.products = []
		function grabItems( banka, is_service ) {
			for(var i=0, l = banka.length; i<l; i++) {
				var item = banka[i]
				var productItem = {}
				productItem.is_service = is_service
				productItem.id         = item.id
				productItem.title      = item.name
				productItem.moveable   = item.moveable			
				productItem.price      = ( item.price * 1 > 0 ) ? printPrice( item.price ) : '1'
				productItem.hm         = item.quantity*1
				productItem.locs       = item.moveto_shop
				productItem.img        = ( item.media_image !== null ) ? item.media_image : '/images/f1_footer_logo.png'
				productItem.dlvr       = []
				
				for(var j = 0, jl=item.moveto_mode.length; j<jl; j++) {
					switch( item.moveto_mode[j] ) {
						case 'self':
							productItem.dlvr.push( { txt: 'В самовывоз', lbl: 'selfy'} )
							break
						case 'standart_delay':
							productItem.dlvr.push( { txt: 'В доставку', lbl: 'delay'} )
							break					
						case 'standart_rapid':
							productItem.dlvr.push( { txt: 'В доставку', lbl: 'rapid'} )
							break					
					}
				}
				_receiver.products.push( productItem )
				item = {}
				productItem = {}
			}	
		}
		
		grabItems( _sender.products, false )
		if( 'services' in _sender )
			grabItems( _sender.services, true )		
	} // syncProducts function

	syncBlock( ServerModel.standart_rapid, orderModel.Rapid )
	syncProducts( ServerModel.standart_rapid, orderModel.Rapid )

	syncBlock( ServerModel.standart_delayed, orderModel.Delay )
	syncProducts( ServerModel.standart_delayed, orderModel.Delay )

	function syncShops( _sender, _receiver ) {
		_receiver.shops = []
		for(var i=0, l = _sender.shops.length; i<l; i++) {
			var item = _sender.shops[i]
			var shopItem = {}
			shopItem.shid     = item.id
			shopItem.title  = item.address
			shopItem.fromto  = item.working_time
			shopItem.latitude = item.coord_lat
			shopItem.longitude = item.coord_long
			shopItem.markerImg = ''			
			syncProducts( item, shopItem )
			
			_receiver.shops.push( shopItem )
			item = {}
			shopItem = {}
		}	
	}
	
	syncBlock( ServerModel.self, orderModel.Selfy )
	syncShops( ServerModel.self, orderModel.Selfy )

	/* ViewModel */
	function MyViewModel() {
		var self = this
		
		self.noSuchItemError = ko.observable( false )
		self.urlaftererror = ko.observable( '/' )
		self.appIsLoaded = ko.observable( false )
		self.stolenItems = ko.observableArray( [] )
		
		function customCal( papaSelector, cd, cdf, dd, ct, ctid, sch ) {
			var me = this
			me.papa = papaSelector
			me.curDate  = ko.observable( cd )
			me.curDateF = cdf // formatted ISO
			me.dates    = dd 
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
				me.curDateF = dateit.ISO
				$(me.papa).find('.bBuyingDatePopup[ref="'+dateit.dv+'"]').css({'left': $(e.target).position().left }).show()
			}
			if( typeof(ct) !== 'undefined' ) {
				me.curTime   = ko.observable( ct )
				me.curTimeId = ctid
				if( typeof(sch) !== 'undefined' ) 
					me.schedule = ko.observableArray( sch )				
				me.pickTime = function( timeit, e ) {		
					me.curTime( timeit.txt )
					me.curTimeId = timeit.id
					$(e.currentTarget).parent().parent().hide()
				}
			}
		}

		self.addCost  = orderModel.Rapid.addCost
		self.bitems   = ko.observableArray( orderModel.Rapid.products )

		self.RapidCalend = new customCal( '.rapid', orderModel.Rapid.dlvrDate, orderModel.Rapid.ISODate, orderModel.Rapid.vcalend, 
					orderModel.Rapid.dlvrTime, orderModel.Rapid.dlvrID, orderModel.Rapid.schedule)

		self.addCost_D  = orderModel.Delay.addCost
		self.bitems_D   = ko.observableArray( orderModel.Delay.products )

		self.DelayCalend = new customCal( '.delay', orderModel.Delay.dlvrDate, orderModel.Delay.ISODate, orderModel.Delay.vcalend, 
					orderModel.Delay.dlvrTime, orderModel.Delay.dlvrID, orderModel.Delay.schedule)

		self.shops      = ko.observableArray( orderModel.Selfy.shops )

		self.SelfyCalend = new customCal( '.selfy', orderModel.Selfy.dlvrDate, orderModel.Selfy.ISODate, orderModel.Selfy.vcalend )
		
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
			
		self.shifting = function( line, _sender, target, e ) {
			//$(e.currentTarget).parent().parent().find('.mBacket').trigger('click') // hack
			self.interfaceMove( _sender, line )
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
			// change lbl is complicated, side-effect
			//var ind = line.dlvr.indexOf( target )
			//line.dlvr[ ind ].lbl ( _sender )
		}
		
		self.totalPrice = ko.computed(function() {
			var tp = 0
			for(var i=0; i<self.bitems().length; i++)
				tp += self.bitems()[i].price.replace(/\D/g,'')*1
			 if( tp > 0 )
			 	tp += self.addCost	
			return printPrice( tp )
		}, this)
		
		self.totalPrice_D = ko.computed(function() {
			var tp = 0
			for(var i=0; i<self.bitems_D().length; i++)
				tp += self.bitems_D()[i].price.replace(/\D/g,'')*1
			 if( tp > 0 )
			 	tp += self.addCost_D
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

		var deleteUrls = {}
		deleteUrls.products = $('#delete-urls').data('products')
		deleteUrls.services = $('#delete-urls').data('services')	

		var orderMove = function( item ) {
			var tmpUrls = ( item.is_service ) ? deleteUrls.services : deleteUrls.products
			if( item.id in tmpUrls ) {
				$.ajax({
					url: tmpUrls[item.id],
					success: function(data) {
//console.info(data)
					}
				})
			}			
		}
		
		self.interfaceMove = function( _sender, item ) {
			switch( _sender ) {
				case 'rapid':
					self.bitems.remove(item)
					if( self.totalPrice_D() == '0' && self.totalPrice() == '0' )
						toggleDlvr( 'fromshop' )
				break
				case 'delay':
					self.bitems_D.remove(item)
					if( self.totalPrice_D() == '0' && self.totalPrice() == '0' )
						toggleDlvr( 'fromshop' )
				break
				case 'selfy':
					for(var i=0, l=self.shops().length; i<l; i++) {
						self.shops()[i].products.remove(item)
					}
					if( self.totalPrice_S() == '0' )
						toggleDlvr( 'standart' )
				break
			}		
		}
		
		self.removeIt = function( _sender, item ) {	
			orderMove( item )
			self.interfaceMove( _sender, item )
		}
		
		/* RETIRED
		self.removeFromShop = function( shop, item ) { 
			var ind = self.shops.indexOf( shop )
			self.shops()[ind].products.remove(item)
			//if( !self.shops()[ind].products().length )
				//self.shops.remove( shop )
		}
		*/
		
		self.allshops = orderModel.allshops
		
		self.showUnavailable = function() {
		var zzz= {error: "moved_items", content: { items: [10167], url:'http://ivn2.ent3.ru/catalog/appliances/stiralnie-mashini-76/' } }
			
			data = zzz
			if( data.error != 'moved_items' ) 
				return
			var stolen = data.content.items
			self.urlaftererror(data.content.url)
			self.noSuchItemError(true)
			var obsArray = []
stln:		for(var i=0, l=stolen.length; i<l; i++) {
				obsArray = self.bitems()
				for(var ind=0, le=obsArray.length; ind<le; ind++) {
					if( obsArray[ind].id == stolen[i] ) {
						var tmp = obsArray[ind]
						self.stolenItems.push( tmp )
						self.bitems.remove( tmp )
						continue stln
					}
				}
				
				obsArray = self.bitems_D()
				for(var ind=0, le=obsArray.length; ind<le; ind++) {
					if( obsArray[ind].id == stolen[i] ) {
						var tmp = obsArray[ind]
						self.stolenItems.push( tmp )
						self.bitems_D.remove( tmp )
						continue stln
					}
				}
				
				for(var j=0, lj=self.shops().length; j<lj; j++) {
					obsArray = self.shops()[j].products()
					for(var ind=0, le=obsArray.length; ind<le; ind++) {
						if( obsArray[ind].id == stolen[i] ) {
							var tmp = obsArray[ind]
							self.stolenItems.push( tmp )
							self.shops()[j].products.remove( tmp )
							continue stln
						}
					}
				}

			} // stln
			
		}
		
		self.productforPopup = ko.observable( {
			moveable: false, price: '9 900', 
			title: 'Сноуборд Salomon Salvatore Sanchez ', hm: '1', img: '/images/z_img6.png',
			locs: [ 20, 10 ] 
		} )
		
		self.popupWithShops = ko.observableArray([])
		
		var shop_sender = null
		var movingItem = null
		
		self.shiftingInShops = function( shop_receiver ) {
			if( typeof(shop_receiver) === 'object' )
				shop_receiver = shop_receiver.shid
//console.info('rec ', movingItem)
//console.info( self.shops.indexOf( shop_sender ), shop_receiver )
			self.shops()[ self.shops.indexOf( shop_sender ) ].products.remove( movingItem )
			for(var i=0, l=self.shops().length; i<l; i++) {
				if( self.shops()[i].shid == shop_receiver ) {
					self.shops()[i].products.push( movingItem )
					break
				}	
			}
		}
		
		self.shiftAndClose = function( shop_receiver ) {
			window.regionMap.closeMap()
			self.shiftingInShops( shop_receiver )
		}
		
		self.fillPopupWithShops = function( shop, item ) {
			shop_sender = shop
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
				var shopsHub = self.shops()
				for(var asi=0, asl=shopsHub.length; asi<asl; asi++) {
					if( shopsHub[asi].shid == doublelocs[i] ) {
//console.info( asi )
						var shopitem = shopsHub[asi]
						shopitem.markerImg = ko.observable( )
						self.popupWithShops.push( shopsHub[asi] )
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
	
	MVM.appIsLoaded(true)
	MVM.showUnavailable()
	
	/* JQUERY handlers */
	var agent = new brwsr()
	if( !agent.isTouch ) {
		$('body').delegate('.bImgButton', 'mouseenter', function() {
			var tipData = {cssl: '-64px', tiptext: 'tip'}
			if( $(this).hasClass('mMap') )
				tipData = {cssl: '-64px', tiptext: 'Выбрать другой магазин'}
			if( $(this).hasClass('mBacket') )
				tipData = {cssl: '-36px', tiptext: 'Удалить товар'}
			if( $(this).hasClass('mArrows') )
				tipData = {cssl: '-87px', tiptext: 'Поместить товар в другой заказ'}
			$(this).html( tmpl("tip_tmpl", tipData) )
		})
		
		$('body').delegate('.bImgButton', 'mouseleave', function() { 
			$(this).empty()		
		})
		
		$('body').delegate('.bButtonPopup', 'mouseleave', function() {
			$(this).hide()
		})
		
		$('body').delegate('.bBuyingDatePopup', 'mouseleave', function() {
			$(this).hide()
		})	
	} 
	
	$('body').delegate('.bButtonPopup__eTitle', 'click', function(e) { // TODO iOS
		$(this).hide()
	})
	
	$('body').delegate('.mArrows', 'click', function(e) {
		e.preventDefault()
		$(this).parent().find('.bButtonPopup').show()
		return false
	})		
	$('body').delegate('.mMap', 'click', function(e) {
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
	
	$('#tocontinue').click( function(e) {
		e.preventDefault()
		MVM.noSuchItemError(false)
	})
	
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
			maxWidth: 400,
			disableAutoPan: false
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
		
		this.closeMap = function( markers ) {
			self.infoWindow.close()
			$('.mMapPopup').trigger('close') // close lightbox_me
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
		MVM.shiftAndClose( $(node).parent().find('.shopnum').text() )
	}
	
	/* Other Form Handlers */
	function toggleDlvr( dlvrtitle ) {
		if( dlvrtitle === 'standart' ) {
			$('#addressField').show()
		}
		if( dlvrtitle === 'fromshop' ) {
			$('#addressField').hide()
		}
	} //toggleDlvr function
	
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
//console.info( e.target, e.timeStamp, $(this).attr('checked') )
		e.stopPropagation()
	})	
	
	/* Mail to Server */
	function syncClientServer() {
		ServerModel.standart_rapid.products   = MVM.bitems()
		ServerModel.standart_delayed.products = MVM.bitems_D()
		
		ServerModel.standart_rapid.date_default   = MVM.RapidCalend.curDateF
		ServerModel.standart_rapid.time_default   = MVM.RapidCalend.curTimeId
		ServerModel.standart_delayed.date_default = MVM.DelayCalend.curDateF
		ServerModel.standart_delayed.time_default = MVM.DelayCalend.curTimeId
		ServerModel.self.date_default             = MVM.SelfyCalend.curDateF
		
		for(var i=0, l = ServerModel.self.shops.length; i<l; i++) {
			ServerModel.self.shops[i].products = MVM.shops()[i].products().slice(0)
		}
//console.info(ServerModel)	
	} // syncClientServer function
	
	function markError( node ) {
		console.info(node)
	}
	
	var sended = false
	$('.mConfirm a.bBigOrangeButton').click( function(e) {
		e.preventDefault()
		if( sended ) return
	//	sended = true
		var form = $('#order')
		var serArray = form.serializeArray()
console.info( serArray )
		var fieldToValidate = $('#validator').data('value')
flds:	for( field in fieldToValidate ) {
			if( !form.find('[name="'+field+'"]:visible').length )
				continue
			for(var i=0, l=serArray.length; i<l; i++) {
				if( serArray[i].name == field ) {				
					if( serArray[i].value == '' )
						markError( field ) // cause is empty
					continue flds
				}
			}
			markError( field ) // cause not in serArray
		}
		
		$(this).html('Минутку...')
		syncClientServer()		
		var toSend = form.serializeArray()
		toSend.push( { name: 'products_hash', value: JSON.stringify( ServerModel )  } )//encodeURIComponent

		$.ajax({
			url: form.attr('action'),
			type: "POST",
			data: toSend,
			success: function( data ) {
				sended = false
				if( data.error == 'moved_items' ) {
					var stolen = data.content.items
					MVM.showUnavailable()
				}
				
			}
		})
	})
});	