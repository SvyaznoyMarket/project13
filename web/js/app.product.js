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
		var np = $('.bCountSet')
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
		
		$('.bCountSet__eP').click( function() {
			if( $(this).hasClass('disabled') )
				return false
			$('.bCountSet').data('hm', $('.bCountSet').data('hm')*1 + 1 )
			np.trigger('update')
			return false
		})
		$('.bCountSet__eM').click( function() {	
			if( $(this).hasClass('disabled') )
				return false		
			var hm = $('.bCountSet').data('hm')//how many
			if( hm == 1 )
				return false
			$('.bCountSet').data('hm', $('.bCountSet').data('hm')*1 - 1 )
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
		
		console.info( $('.order1click-link').data('model') )
		var Model = $('.order1click-link').data('model')
		Deliveries = [
			{
				id: 4,
				name: 'Доставка',
				price: 400,
				dates: [ {value: '10-02-2012', text: '10 февраля'}, {value: '11-02-2012', text: '11 февраля'} ]

			},
			{
				id: 2,
				name: 'Самовывоз',
				price: 0,
				dates: [ {value: '08-02-2012', text: '8 февраля'}, {value: '09-02-2012', text: '9 февраля'} ],
				shops: [
					{id:2,
					name:"г. Москва, м. Ленинский проспект, магазин  на ул. Орджоникидзе, д. 11, стр. 10",
					regime:"с 9.00 до 21.00",
					address:"м. Ленинский проспект, ул. Орджоникидзе, д. 11, стр. 10",
					latitude:"55.706488",
					longitude:"37.596997" },
					{id:3,
					name:"г. Москва, м. Киевская, магазин на ул. Б. Дорогомиловская, д. 8",
					regime:"с 9.00 до 22.00",
					address:"м. Киевская, ул. Б. Дорогомиловская, д. 8",
					latitude:"55.746197",
					longitude:"37.565389"}
				]			
			}
		]
		var sla=0, slo=0
		for(var i=0, l=Deliveries[1].shops.length ;i<l;i++) {
			sla += Deliveries[1].shops[i].latitude*1
			slo += Deliveries[1].shops[i].longitude*1		
		}
		console.info(sla/Deliveries[1].shops.length, slo/Deliveries[1].shops.length)
		
		/* ViewModel */
		function MyViewModel() {
			var self = this	
			self.title = Model.jstitle
			self.price = Model.jsprice
			self.icon  = Model.jsbimg

			self.quantity = ko.observable(2)
			self.quantityTxt = ko.computed(function() {
				return self.quantity() + ' шт.'
			}, this)
			self.total = ko.computed(function() {
				return self.price * self.quantity()
			}, this)
			
			self.dates = ko.observableArray( Deliveries[1].dates.slice(0) )
			
			self.changeDlvr = function(item, e) {
				var ind = 0
				if( e.currentTarget.value == 2 )
					ind = 1	
				else
					ind =0
				while( self.dates().length )
					self.dates.pop()
				for(var i=0; i< Deliveries[ind].dates.length; i++ )	
					self.dates.push( Deliveries[ind].dates[i] )	
			}
			
		}	// 
		MVM = new MyViewModel() 
		
		ko.applyBindings(MVM) // this way, Lukas!

	
		$('.order1click-link').bind('click', function(e) {
			e.preventDefault()
			if( typeof(_gaq) !== 'undefined' )
				_gaq.push(['_trackEvent', 'QuickOrder', 'Open'])
			$('#order1click-container').lightbox_me({
				centered: true
			})
		})
	}
    
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