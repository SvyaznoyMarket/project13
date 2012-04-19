$(document).ready(function() {
	/* Rating */
	if( $('#rating').length ) {
		var iscore = $('#rating').next().html().replace(/\D/g,'')
		$('#rating span').remove()
		$('#rating').raty({
		  start: iscore,
		  showHalf: true,
		  path: '/css/skin/img/',
		  readOnly: $('#rating').data('readonly'),
		  starHalf: 'star_h.png',
		  starOn: 'star_a.png',
		  starOff: 'star_p.png',
		  hintList: ['плохо', 'удовлетворительно', 'нормально', 'хорошо', 'отлично'],
		  click: function( score ) {
		  		$.getJSON( $('#rating').attr('data-url').replace('score', score ) , function(data){
		  			if( data.success === true && data.data.rating ) {
		  				$.fn.raty.start( data.data.rating ,'#rating' )
		  				$('#rating').next().html( data.data.rating )
		  			}
		  		})
		  		$.fn.raty.readOnly(true, '#rating')
		  	}
		})
	}
	
	/* Product Counter */
	if( $('.bCountSet').length ) {
		var np = $('.goodsbarbig .bCountSet')
		var l1 = np.parent().find('.link1')
		var l1href = l1.attr('href')
		var l1cl = $('a.order1click-link')
		var l1clhref = l1cl.attr('href')
		np.data('hm', np.first().find('span').text().replace(/\D/g,'') )
		
		np.bind('update', function() {
			var hm = $(this).data('hm')
			np.find('span').text( hm + '  шт.')
			l1.attr('href', l1href + '/' +  hm )
			l1cl.attr('href', l1clhref + '&quantity=' + hm )
		})
		
		$('.bCountSet__eP', np).click( function() {
			if( $(this).hasClass('disabled') )
				return false
			np.data('hm', np.data('hm')*1 + 1 )
			np.trigger('update')
			return false
		})
		$('.bCountSet__eM', np).click( function() {	
			if( $(this).hasClass('disabled') )
				return false		
			var hm = np.data('hm')//how many
			if( hm == 1 )
				return false
			np.data('hm', np.data('hm')*1 - 1 )
			np.trigger('update')
			return false
		})		
	}
	
	/* Icons */
	$('.viewstock').bind( 'mouseover', function(){
		var trgtimg = $('#stock img[ref="'+$(this).attr('ref')+'"]')
		var isrc    = trgtimg.attr('src')
		var idu    = trgtimg.attr('data-url')
		if( trgtimg[0].complete ) {
			$('#goodsphoto img').attr('src', isrc)
			$('#goodsphoto img').attr('href', idu)
		}
	})

	/* Media library */
	//var lkmv = null
	var api = {
		'makeLite' : '#turnlite',
		'makeFull' : '#turnfull',
		'loadbar'  : '#percents',
		'zoomer'   : '#bigpopup .scale',
		'rollindex': '.scrollbox div b',
		'propriate': ['.versioncontrol','.scrollbox']
	}
	
	if( typeof( product_3d_small ) !== 'undefined' && typeof( product_3d_big ) !== 'undefined' )
		lkmv = new likemovie('#photobox', api, product_3d_small, product_3d_big )
	if( $('#bigpopup').length )
		var mLib = new mediaLib( $('#bigpopup') )

	$('.viewme').click( function(){
		if( mLib )
			mLib.show( $(this).attr('ref') , $(this).attr('href'))
		return false
	})
	
	/* Delivery Block */
    var formatDateText = function(txt){
      txt = txt.replace('сегодня', '<b>сегодня</b>');
      txt = txt.replace(' завтра', ' <b>завтра</b>');
      return txt;
    }
    var formatPrice = function(price){
      if (typeof price === 'undefined' || price === null) {
        return '';
      }
      if (price > 0) {
        return ', '+price+' руб.'
      } else {
        return ', бесплатно.'
      }
    }
    var delivery_cnt = $('.delivery-info');
    if (delivery_cnt.length) {
      var coreid = delivery_cnt.prop('id').replace('product-id-', '');
      $.post(delivery_cnt.data().calclink, {ids:[coreid]}, function(data){
        if (!data[coreid]) return;
        data = data[coreid].deliveries;
        var html = '<h4>Как получить заказ?</h4><ul>', i, row;
        for (i in data) {
          row = data[i];
          if (row.object.core_id == 3) {
            html += '<li><h5>Можно заказать сейчас и самостоятельно забрать в магазине '+formatDateText(row.text)+'</h5><div>&mdash; <a target="blank" href="'+delivery_cnt.data().shoplink+'">В каких магазинах ENTER можно забрать?</a></div></li>';
            data.splice(i, 1);
          }
        }
        if (data.length > 0) {
          html += '<li><h5>Можно заказать сейчас с доставкой</h5>';
          for (i in data) {
            row = data[i];
            if (row.object.core_id == 2) {
              html += '<div>&mdash; Можем доставить '+formatDateText(row.text)+formatPrice(row.price)+'</div>';
              data.splice(i, 1);
            }
          }
          for (i in data) {
            row = data[i];
            if (row.object.core_id == 1) {
            	html += '<div>&mdash; Можем доставить '+formatDateText(row.text)+formatPrice(row.price)+'</div>';
            	data.splice(i, 1);
            }
          }
          html += '</li>';
        }
        html += '</ul>';
        delivery_cnt.html(html);
      }, 'json');
    }
    
	/* Some handlers */
    $('.bDropMenu').each( function() {
		var jspan  = $(this).find('span:first')
		var jdiv   = $(this).find('div')
		jspan.css('display','block')
		if( jspan.width() + 60 < jdiv.width() )
			jspan.width( jdiv.width() - 70)
		else
			jdiv.width( jspan.width() + 70)
	})
	
    $('.product_rating-form').live({
        'form.ajax-submit.prepare': function(e, result) {
            $(this).find('input:submit').attr('disabled', true)
        },
        'form.ajax-submit.success': function(e, result) {
            if (true == result.success) {
                $('.product_rating-form').effect('highlight', {}, 2000)
            }
        }
    })

    $('.product_comment-form').live({
        'form.ajax-submit.prepare': function(e, result) {
            $(this).find('input:submit').attr('disabled', true)
        },
        'form.ajax-submit.success': function(e, result) {
            $(this).find('input:submit').attr('disabled', false)
            if (true == result.success) {
                $($(this).data('listTarget')).replaceWith(result.data.list)
                $.scrollTo('.' + result.data.element_id, 500, {
                    onAfter: function() {
                        $('.' + result.data.element_id).effect('highlight', {}, 2000);
                    }
                })
            }
        }
    })

    $('.product_comment_response-link').live({
        'content.update.prepare': function(e) {
            $('.product_comment_response-block').html('')
        },
        'content.update.success': function(e) {
            $('.product_comment_response-block').find('textarea:first').focus()
        }
    })
	
	/* One Click Order */
	if( $('.order1click-link').length ) {
		
console.info( 'MODEL: ', $('.order1click-link').data('model') )
		var Model = $('.order1click-link').data('model')
		Deliveries = {
			'courier': {
				id: 4,
				name: 'Доставка',
				price: 400,
				dates: [ {value: '10-02-2012', text: '10 февраля'}, {value: '11-02-2012', text: '11 февраля'} ]

			},
			'self' : {
				id: 2,
				name: 'Самовывоз',
				price: 0,
				dates: [ {value: '08-02-2012', shopIds: [2,3], text: '8 февраля'}, {value: '09-02-2012', shopIds: [2], text: '9 февраля'} ],
				shops: [
					{id:2,
					name:"г. Москва, м. Ленинский проспект, магазин  на ул. Орджоникидзе, д. 11, стр. 10",
					regtime:"с 9.00 до 21.00",
					address:"м. Ленинский проспект, ул. Орджоникидзе, д. 11, стр. 10",
					addressTxt:"м. Ленинский проспект, ул. Орджоникидзе, д. 11...",
					latitude:"55.706488",
					longitude:"37.596997" },
					{id:3,
					name:"г. Москва, м. Киевская, магазин на ул. Б. Дорогомиловская, д. 8",
					regtime:"с 9.00 до 22.00",
					address:"м. Киевская, ул. Б. Дорогомиловская, д. 8",
					addressTxt:"м. Киевская, ул. Б. Дорогомиловская, д. 8",
					latitude:"55.746197",
					longitude:"37.565389"}
				]			
			}
		}
console.info( 'Deliveries: ', Deliveries )		
		
		var sla=0, slo=0
		for(var i=0, l=Deliveries['self'].shops.length ;i<l;i++) {
			sla += Deliveries['self'].shops[i].latitude*1
			slo += Deliveries['self'].shops[i].longitude*1		
		}
		var mapCenter = {
			latitude  : sla/Deliveries['self'].shops.length,
			longitude : slo/Deliveries['self'].shops.length
		}
		
		/* ViewModel */
		function MyViewModel() {
			var self = this	
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
			
			self.dlvrs = []
			for(var obj in Deliveries ) {
				self.dlvrs.push( {
					type: obj,
					name: Deliveries[obj].name
				})
				if( obj == 'self' )
					self.chosenDlvr( self.dlvrs[ self.dlvrs.length - 1 ] )
			}
			
			self.total = ko.computed(function() {
				return printPrice( self.price * self.quantity() + Deliveries[ self.chosenDlvr().type ].price * 1 )
			}, this)
			
			self.changeDlvr = function( argDlvr ) {		
				self.chosenDlvr( argDlvr )
				self.dates.removeAll()
				//while( self.dates().length )
				//	self.dates.pop()
				for(var i=0; i< Deliveries[ argDlvr.type ].dates.length; i++ )	
					self.dates.push( Deliveries[ argDlvr.type ].dates[i] )	
				self.chosenDate( self.dates()[0] )
			}
			
			self.plusItem = function() {
				self.quantity( self.quantity() + 1 )
				return false
			}
			self.minusItem = function() {
				if( self.quantity() > 1 )
					self.quantity( self.quantity() - 1 )
				return false
			}
			
			self.dates = ko.observableArray( Deliveries['self'].dates.slice(0) )
			self.chosenDate = ko.observable( self.dates()[0] )
			self.pickDate = function( item ) {
				self.chosenDate( item )
				//shops mod
console.info(item)				
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
			
			self.shops = ko.observableArray( Deliveries['self'].shops.slice(0) )
			self.chosenShop = ko.observable( self.shops()[0] )
			self.pickedShop = ko.observable( self.shops()[0] )
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
			self.shopChoose = function() {
console.info(arguments)			
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
				regionMap.showMarkers( markersPull )				
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
				name: 'fio', //UNIQUE!
				value: 'Иван',
				valerror: false,
				regexp: /^[a-zа-я\s]+$/i
			}) )
			self.textfields.push( ko.observable({
				title: 'Телефон для связи',
				name: 'phone', //UNIQUE!
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
				if( self.formStatus() !== 'typing' ) // double or repeated click
					return 
				//change title
				self.formStatus('process')
				
				//validate fields
				$('#oneClick input').trigger('change')
console.info( 'CHECK', self.formStatus()	)
				if( self.formStatus() === 'typing' ) // validation error
					return
				//form request
				
				//send ajax
				setTimeout( function(){ self.formStatus('sending') }, 500)
			}
		}	// 
		MVM = new MyViewModel() 
		
		ko.applyBindings(MVM) // this way, Lukas!

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
	
		window.regionMap = new MapWithShops( mapCenter, $('#map-info_window-container'), 'mapPopup' )
		
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
		
		$('#order1click-container').delegate( '.bSelect', 'click', function() {	// custom selectors
			$(this).find('.bSelect__eDropmenu').toggle()
		})
	
		$('.order1click-link').bind('click', function(e) { // button 'Купить в один клик'
			e.preventDefault()
			if( typeof(_gaq) !== 'undefined' )
				_gaq.push(['_trackEvent', 'QuickOrder', 'Open'])
			$('#order1click-container').lightbox_me({
				centered: true,
				onClose: function() {
					MVM.showMap(false)
				}
			})
		})
		
	} // One Click Order
    
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

});