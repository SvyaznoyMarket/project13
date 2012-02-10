$(document).ready(function() {

    var filterlink = $('.filter .filterlink:first');
	var filterlist = $('.filter .filterlist');
	var userag    = navigator.userAgent.toLowerCase()
	var isAndroid = userag.indexOf("android") > -1
	var isOSX     = ( userag.indexOf('ipad') > -1 ||  userag.indexOf('iphone') > -1 )
	if( isAndroid || isOSX ) {
		filterlink.click(function(){
			filterlink.hide();
			filterlist.show();
			return false
		});
	} else {
		filterlink.mouseenter(function(){
			filterlink.hide();
			filterlist.show();
		});
		filterlist.mouseleave(function(){
			filterlist.hide();
			filterlink.show();
		});
	}
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
/*$('.product_filter-block .hiddenCheckbox').change( function(e){
	console.info('checkbox: ', $(this).attr('id')+' '+$(this).attr('checked'))
	e.stopPropagation()
})*/
    $('.product_filter-block')
    /*.submit( function(e){
    	e.preventDefault()
    	console.info( $(this).serializeArray() )
    })*/
    // change
    .bind('change', function(e) {
        var el = $(e.target)

        if (el.is('input') && (-1 != $.inArray(el.attr('type'), ['radio', 'checkbox']))) {
            el.trigger('preview')
        }
    })
    // preview
    .bind('preview', function(e) {
        var el = $(e.target)
        var form = $(this)
        function disable() {
            var d = $.Deferred();
            //el.attr('disabled', true)
            return d.resolve();
        }

        function enable() {
            var d = $.Deferred();
            //el.attr('disabled', false)
            return d.promise();
        }

        function getData() {
            var d = $.Deferred();			
            form.ajaxSubmit({
                url: form.data('action-count'),
                success: d.resolve,
                error: d.reject
            })

            return d.promise();
        }

        $.when(getData())
        .then(function(result) {
            if (true === result.success) {
                $('.product_count-block').remove();
                //el.parent().find('> label').first().after('<div class="product_count-block" style="position: absolute; background: #fff; padding: 4px; opacity: 0.9; border-radius: 5px; border: 1px solid #ccc; cursor: pointer;">Найдено '+result.data+'</div>')
                switch (result.data % 10) {
                  case 1:
                    ending = 'ь';
                    break
                  case 2: case 3: case 4:
                    ending = 'и';
                    break
                  default:
                    ending = 'ей';
                    break
                }
                switch (result.data % 100) {
                  case 11: case 12: case 13: case 14:
                    ending = 'ей';
                    break
                }
                var firstli = null
                if ( el.is("div") ) //triggered from filter slider !
                	firstli = el
                else
	                firstli = el.parent().find('> label').first()
                firstli.after('<div class="filterresult product_count-block" style="display:block; padding: 4px; margin-top: -30px; cursor: pointer;"><i class="corner"></i>Выбрано '+result.data+' модел'+ending+'<br /><a>Показать</a></div>')
                $('.product_count-block')
                .hover(
                    function() {
                        $(this).stopTime('hide')
                    },
                    function() {
                        $(this).oneTime(2000, 'hide', function() {
                            $(this).remove()
                        })
                    }
                    )
                .click(function() {
                    form.submit()
                })
                .trigger('mouseout')
            }
        })
        .fail(function(error) {})
    })

	function printPrice ( val ) {
		var floatv = (val+'').split('.')
		var out = floatv[0]
		var le = floatv[0].length
		if( le > 6 ) { // billions
			out = out.substr( 0, le - 6) + ' ' + out.substr( le - 6, le - 4) + ' ' + out.substr( le - 3, le )
		} else if ( le > 3 ) { // thousands
			out = out.substr( 0, le - 3) + ' ' + out.substr( le - 3, le )
		}
		if( floatv.length == 2 )
			out += '.' + floatv[1]
		return out// + '&nbsp;'
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

			if ( !cl1loaded ) {
				getOneClick( $(this).attr('href') )
			} else {
				$('#order1click-container').lightbox_me({
					centered: true
				})
			}
		})

		function bindCalc() {
			var quant = 1
			var pric  = $('.b1Click__ePriceBig .price').html().replace(/\s/g,'')
			function recalc( delta ) {
				if( quant == 1 && delta < 0 )
					return
				quant += delta
				var sum = printPrice( pric * quant )
				$('.c1quant').html( quant+ ' шт.')
				$('#order_product_quantity').val( quant )
				$('.b1Click__ePriceBig .price').html( sum )
			}

			$('.c1less').bind( 'click', function(){ recalc(-1) })
			$('.c1more').bind( 'click', function(){ recalc(1) })
		}

		$('#order1click-form').bind('submit', function(e) {
			e.preventDefault()
			
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
	console.info( $(node).parent().find('.shopnum').text() )
			getOneClick( $(node).parent().find('.shopnum').text() )
		}
		
		$('.bInShopLine__eButton a').bind('click', function(e) {
			e.preventDefault()
			getOneClick( $(this).attr('href') )
		})    
    }

});