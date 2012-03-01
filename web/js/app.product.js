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
		np.data('hm', np.find('span').text().replace(/\D/g,'') )
		
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

	lkmv = new likemovie('#photobox', api, product_3d_small, product_3d_big )
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
            html += '<div>&mdash; Можем доставить '+formatDateText(row.text)+formatPrice(row.price)+'</div>';
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
			
			 
			/* RETIRED
			$(this).ajaxSubmit({
				beforeSubmit: function() {
					var button = $('#order1click-form').find('input:submit')
					button.attr('disabled', true)
					button.val('Оформляю заказ...')
				},
				success: function( response ) {
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
				},
				error: function() {
					var button = $('#order1click-form').find('input:submit')
					button.attr('disabled', false)
					button.val('Попробовать еще раз')
				}
			})
			*/
		})

    //}
    
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
						'<div class="title">Привет, из какого ты города?</div>'+				
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