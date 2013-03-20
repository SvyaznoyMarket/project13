$(document).ready(function(){

	var lboxCheckSubscribe = function(data){
		if (!data.isSubscribe) 
			return false
		var subPopup = $('.bSubscribeLightboxPopup')
		var subscribeItem = $('.bSubscribeLightbox')
		subscribeItem.show()
		subscribeItem.bind('click', function(){
			subPopup.show()
		})
		subPopup.find('.close').bind('click', function(){
			subPopup.hide()
		})
	}
	
	var carturl = $('.lightboxinner .point2').attr('href')

	var shortinfo = '/user/shortinfo'
	if( !docCookies.hasItem('enter') ||  !docCookies.hasItem('enter_auth'))
		shortinfo += '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000)
	
	/* Lightbox */
	var lbox = {}
	if ( !$('.lightbox').length ) {
		$('.lightboxinner').hide()
		$.getJSON( shortinfo, function(data) {
			if( data.success ) {
				if (data.data.vitems>0) {
                    $('#topBasket').text( '('+data.data.vitems+')' )
                }
				if( data.data.name ) {
					var dtmpl={}
					dtmpl.user = data.data.name
					var show_user = tmpl('auth_tmpl', dtmpl)
          			$('#auth-link').hide()
					$('#auth-link').after(show_user)
				} else $('#auth-link').show()
			}
		})
		return false
	}
	
	ltbx = new Lightbox( $('.lightboxinner'), lbox )

	/* ---- */
	$.getJSON( shortinfo, function(data) {
			if( data.success ) {
				lbox = data.data
				ltbx.update( lbox )
				//ltbx.save()
				changeButtons( lbox )
				/* ltbx */
				var dropbx = $('div.lightboxinner > .dropbox')
				if( dropbx.length ) {
					dropbx.css('left', $('ul.lightboxmenu > li').eq(1).offset().left - $('div.lightboxinner').offset().left )
				}
				PubSub.publish( 'auth try', data.data.name );
				if( data.data.name ) {
					var dtmpl={}
					dtmpl.user = data.data.name
					var show_user = tmpl('auth_tmpl', dtmpl)
          			$('#auth-link').hide()
					$('#auth-link').after(show_user)
				} else $('#auth-link').show()

				// subscribe
				lboxCheckSubscribe(data.data)
			}

	})
	var isInCart = false
	var changeButtons = function( lbox ){
		if(!lbox || !lbox.productsInCart ) return false
		for( var tokenP in lbox.productsInCart) { // Product Card
			var bx = $('div.boxhover[ref='+ tokenP +']')
			if( bx.length ) {
				var button = $('a.link1', bx)
				button.attr('href', $('.lightboxinner .point2').attr('href') )
				button.addClass('active')	//die('click') doesnt work
			}
			bx = $('div.goodsbarbig[ref='+ tokenP +']')
			if( bx.length ) {
				var button = $('a.link1', bx)
				button.attr('href', $('.lightboxinner .point2').attr('href') )
				$('body').addClass('bought')
				button.unbind('click')//.addClass('active')
				isInCart = true
				if( lbox.servicesInCart )
				for( var tokenS in lbox.servicesInCart ) {
					if( tokenP in lbox.servicesInCart[ tokenS ] ) {
						var button = $('div.mServ[ref='+ tokenS +'] a.link1')
						if( button.length ) {
							button.attr('href', $('.lightboxinner .point2').attr('href') ).text('В корзине')
						}
						button = $('td.bF1Block_eBuy[ref='+ tokenS +'] input.button')
						if( button.length ) {
							button.addClass('active').val('В корзине').attr( 'href', carturl )
						}
					}
				}
			}
		}
		if( lbox.servicesInCart )
		for( var tokenS in lbox.servicesInCart ) { // Service Card
			if( lbox.servicesInCart[ tokenS ][0] ) {
				var button = $('div.mServ[ref='+ tokenS +'] a.link1')
				if( button.length ) {
					button.attr('href', $('.lightboxinner .point2').attr('href') ).text('В корзине').addClass('active')
				}
				button = $('div.bServiceCard[ref='+ tokenS +'] input')
				if( button.length ) {
					button.val('В корзине').addClass('active').attr( 'href', carturl )
				}
			}
		}
		// if( lbox.is_credit )
		// 	if( $('#creditinput').length )
		// 		$('#creditinput').trigger('click')
	}
	/* ---- */

	// hover imitation for IE
	if (window.navigator.userAgent.indexOf ("MSIE") >= 0){
		$('.allpageinner').delegate( '.goodsbox__inner', 'hover', function() {
			$(this).toggleClass('hover');
		})
	}

	$('.allpageinner').delegate( '.goodsbox', 'mouseenter', function() { // expanded view hack
		currentItem = $(this).attr('ref')
	})
	/* ---- */

	$('.goodsbox__inner').live('click', function(e) {
		if( $(this).attr('data-url') )
			window.location.href = $(this).attr('data-url')
	})

	/* GA tracks */
	var accessoriesMsg = {
		uri: window.location.pathname,
		atcl: $('.bGood__eArticle span:last').text().replace(/[^0-9\-]/g, '')
	}
	$('.bigcarousel').eq(0).bind('click', function(e) {
		if( typeof(_gaq) !== 'undefined' )
			_gaq.push(['_trackEvent', 'accessories_up', accessoriesMsg['atcl'], accessoriesMsg['uri'] ])
	})
	$('.bigcarousel').eq(1).bind('click', function(e) {
		if( typeof(_gaq) !== 'undefined' )
			_gaq.push(['_trackEvent', 'accessories_down', accessoriesMsg['atcl'], accessoriesMsg['uri'] ])
	})

	/* F1 */
	if( $('div.bF1Info').length ) {
		var look    = $('div.bF1Info')
		var f1lines = $('div.bF1Block')
		// open popup
		$('.link1, .bF1Info_Logo', look).click( function(){
			if( $('div.hideblock.extWarranty').is(':visible') )
				$('div.hideblock.extWarranty').hide()
			f1lines.show()	
			return false
		})
		// close popup
		$('.close', f1lines).click( function(){
			f1lines.hide()
		})
		// add f1
		f1lines.find('input.button').bind ('click', function() {
			if( $(this).hasClass('active') ){
				window.location.href = $(this).attr('href')
				return false
			}
				
			$(this).val('В корзине').addClass('active').attr( 'href', carturl )
			var f1item = $(this).data()
			//credit case
// 			if( 'creditBox' in window ) {
// //				if( !f1item.url.match(/_quantity\/[0-9]+/) )
// //					f1item.url += '/1' //quantity
// 				if( creditBox.getState() )
// 					f1item.url += '1/1' //credit
// 				else 	
// 					f1item.url += '1/0' //no credit
// 			}			
			f1lines.fadeOut()
			$.getJSON( f1item.url, function(data) {
				if( !data.success )
					return true
				look.find('h3').text('Вы добавили услуги:')
				var f1line = tmpl('f1look', f1item)
				f1line = f1line.replace('F1ID', f1item.fid )
				look.find('.link1').before( f1line )


				// flybox
				var tmpitem = {
					'id'    : $('.goodsbarbig .link1').attr('href'),
					'title' : $('h1').html(),
					'vitems': data.data.full_quantity,
					'sum'   : data.data.full_price,
					'link'  : data.data.link,
					'price' : $('.goodsinfo .price').html(),
					'img'   : $('.goodsphoto img.mainImg').attr('src')
				}
				tmpitem.f1 = f1item
				if( isInCart )
					tmpitem.f1.only = 'yes'
				ltbx.getBasket( tmpitem )
				if( !isInCart ) {
					isInCart = true
					markPageButtons()
				}
			})
			return false
		})
		// remove f1
		$('a.bBacketServ__eMore', look).live('click', function(){
			var thislink = this
			$.getJSON( $(this).attr('href'), function(data) {
				if( !data.success )
					return true
				var line = $(thislink).parent()
				f1lines.find('td[ref='+ line.attr('ref') +']').find('input').val('Купить услугу').removeClass('active')
				line.remove()
				ltbx.update({ sum: data.data.full_price })

				if( !$('a.bBacketServ__eMore', look).length )
					look.find('h3').html('Выбирай услуги F1<br/>вместе с этим товаром')
			})
			return false
		})
	}

	/* draganddrop */
	var draganddrop = new DDforLB( $('.allpageinner'), ltbx )	
	$('.goodsbox__inner[ref] .mainImg').live('mousedown', function(e){
			e.stopPropagation()
			e.preventDefault()
			if(e.which == 1)
				draganddrop.prepare( e.pageX, e.pageY, parseItemNode(currentItem) ) // if delta then d&d
	})
	$('.goodsbox__inner[ref] .mainImg').live('mouseup', function(e){
		draganddrop.cancel()
	})

	function DDforLB( outer , ltbx ) {	
		if (! outer.length ) 
			return null

		var self     = this
		var lightbox = ltbx
		var isactive = false
		var icon     = null
		var margin   = 25 // gravitation parameter
		var wdiv2    = 30 // draged box halfwidth
		var containers = $('.dropbox')
		if (! containers.length ) 
			return null
		var abziss 	 = []		
		var ordinat  = 0
		var itemdata = null

		/* initia */
		var divicon = $('<div>').addClass('dragbox').css('display','none')
		var icon = $('<div>')
		divicon.append( icon )
		$(outer).append( divicon )
		var shtorka = $('<div>').addClass('graying')
				.css( {'display':'none', 'opacity': '0.5'} ) //ie special							
		$(outer).append( shtorka )
		var shtorkaoffset = 0

		this.cancel = function() {
			$(document).unbind('mousemove.dragitem')
		}

		$(document).bind('mouseup', function(e) {
		if(e.target.id == 'dragimg')
			self.cancel()
		})

		this.prepare = function( pageX, pageY, item ) {
			itemdata = item
			if(  $( '.goodsbox__inner[ref='+ itemdata.id +'] a.link1').hasClass('active') ) 
				return
			$(document).bind('mousemove.dragitem', function(e) {
				e.preventDefault()
				if(! isactive) {
					if( Math.abs(pageX - e.pageX) > margin || Math.abs(pageY - e.pageY) > margin ) {
						self.turnon(e.pageX, e.pageY)
						isactive = true
					}
				} else {		
					icon.css({'left':e.pageX - wdiv2, 'top':e.pageY - shtorkaoffset - wdiv2 })
					ordinat = $(containers[0]).offset().top

					if( e.pageY + wdiv2 > ordinat - margin &&
						e.pageX + wdiv2 > abziss[0] - margin && e.pageX - 30 < abziss[0] + 70 + margin ) { // mouse in HOT area
	//					e.pageX + wdiv2 > abziss[0] - margin && e.pageX - 30 < abziss[2] + 70 + margin ) { // mouse in HOT area
						/*var cindex = 3
						if( e.pageX  < abziss[0] + 70 + margin )
							cindex = 1
						else if( e.pageX < abziss[1]  + 70 + margin )
							cindex = 2*/

						lightbox.toFire( 3 ) // to burn the box !
					} else
						lightbox.putOutBoxes() // run checking is inside
				}
			})		
		}

		this.turnon = function( pageX, pageY ) {
			lightbox.clear()
			shtorka.show()
			shtorkaoffset = shtorka.offset().top
			icon.html( $('<img>').css({'width':60, 'height':60}).attr({'id':'dragimg','width':60, 'height':60, 'alt':'', 'src': itemdata.img }) )
			icon.css({'left':pageX - wdiv2, 'top':pageY - shtorkaoffset - wdiv2 })

			divicon.show()
			lightbox.getContainers()		
			for(var i=0; i < containers.length; i++) {
				abziss[i] = $(containers[i]).offset().left
			}	
			$(document).bind('mouseup.dragitem', function() {
				if( fbox = lightbox.gravitation( ) ) {
					//$(document).unbind('mousemove')
					$(document).unbind('.dragitem')
					icon.animate( {
	//						left: abziss[ fbox - 1 ] + 5,
							left: abziss[ 0 ] + 5,
							top: ordinat - shtorkaoffset + 5
						} , 400, 
						function() { self.finalize( fbox ) } )
				} else 
					self.turnoff()
			})		
		}

		this.turnoff = function() {
			isactive = false
			shtorka.fadeOut()
			divicon.hide()
			lightbox.hideContainers()
			//$(document).unbind('mousemove')
			$(document).unbind('.dragitem')
		}

		this.finalize = function( actioncode ) {
			setTimeout(function(){
				self.turnoff()
				switch( actioncode ) {
					case 1: //comparing
						lightbox.getComparing( itemdata )
						break
					case 2: //wishes 
						lightbox.getWishes( itemdata )
						break
					case 3: //basket
						$.getJSON( $( '.goodsbox__inner[ref='+ itemdata.id +'] a.link1').attr('href'), function(data) {
							if ( data.success && ltbx ) {
								var tmpitem = itemdata
								tmpitem.vitems = data.data.full_quantity
								tmpitem.sum = data.data.full_price
								ltbx.getBasket( tmpitem )
							}	
						})
						//lightbox.getBasket( itemdata )
						break
				}
			}, 400)
		}

	} // DDforLB object
	
    /* EXT WARRANTY */
    if ( ($('div.bBlueButton.extWarranty').length)&&($('div.bBlueButton.extWarranty').is(':visible')) ){
        var look_extWarr = $('div.bBlueButton.extWarranty')
        var f1lines_extWarr = $('div.hideblock.extWarranty')
        var ew_look = $("#ew_look")
        //open popup
        $('.link1',look_extWarr).click( function(){
            if( $('div.bF1Block').is(':visible') )
                $('div.bF1Block').hide()
            f1lines_extWarr.show()
            return false
        })
        //close popup
        $('.close', f1lines_extWarr).click( function(){
            f1lines_extWarr.hide()
        })
        //add warranty
        f1lines_extWarr.find('input.button').bind ('click', function() {
            if( $('input.button',f1lines_extWarr).hasClass('active') ){
                $('input.button',f1lines_extWarr).val('Выбрать').removeClass('active');
            }
            $(this).val('Выбрана').addClass('active')
            var extWarr_item = $(this).data()
            f1lines_extWarr.fadeOut()
            $.getJSON( extWarr_item.url, function(ext_data) {
                if( !ext_data.success )
                    return true
                $('.link1',look_extWarr).text('Изменить гарантию')
                look_extWarr.find('h3').text('Вы выбрали гарантию:')

                $('.ew_title', ew_look).text(extWarr_item.f1title)
                $('.ew_price', ew_look).text(extWarr_item.f1price)
                $('.bBacketServ__eMore', ew_look).attr('href', extWarr_item.deleteurl)
                ew_look.show()
                var tmpitem = {
                    'id'    : $('.goodsbarbig .link1').attr('href'),
                    'title' : $('h1').html(),
                    'vitems': ext_data.data.full_quantity,
                    'sum'   : ext_data.data.full_price,
                    'link'  : ext_data.data.link,
                    'price' : $('.goodsinfo .price').html(),
                    'img'   : $('.goodsphoto img.mainImg').attr('src')
                }
                tmpitem.f1 = extWarr_item
                if( isInCart )
                    tmpitem.f1.only = 'yes'
                ltbx.getBasket( tmpitem )
                if( !isInCart ) {
                    isInCart = true
                    markPageButtons()
                }
            })
            return false
        })
        $('.bBacketServ__eMore', ew_look).live('click', function(e){
            e.preventDefault()
            var thislink = this
            $.getJSON( $(this).attr('href'), function(ext_data) {
                if( !ext_data.success )
                    return true
                var line = $(thislink).parent()
                f1lines_extWarr.find('td[ref='+ line.attr('ref') +']').find('input').val('Купить услугу').removeClass('active')
                line.hide()
                ltbx.update({ sum: ext_data.data.full_price })
                ew_look.hide()
                if( !$('a.bBacketServ__eMore', look_extWarr).length )
                    look_extWarr.find('h3').html('Выбирай услуги F1<br/>вместе с этим товаром')
            })
            return false
        })
    }
    
	/* buy bottons */
	var markPageButtons = function(){
		var carturl = $('.lightboxinner .point2').attr('href')
		$('body').addClass('bought')
		$('.goodsbarbig .link1').attr('href', carturl )//.addClass('active')
		$('#bigpopup a.link1').attr('href', carturl )//.addClass('active')//.html('в корзине')
		$('.bSet__ePrice .link1').unbind('click')
		$('.goodsbar .link1').die('click')
		//$('.bCountSet__eP').addClass('disabled')
		//$('.bCountSet__eM').addClass('disabled')
	}
	
	/* stuff go to litebox */
	function parseItemNode( ref ){
		var jn = $( 'div[ref='+ ref +']')
		var item = {
			'id'   : $(jn).attr('ref'),
			'title': $('h3 a', jn).html(),
			'price': $('.price', jn).html(),
			'img'  : $('.photo img.mainImg', jn).attr('src')
		}
		return item
	}

	$('.goodsbox a.link1').live('click', function(e) {
		e.stopPropagation()
		var button = this
		if( $(button).hasClass('disabled') )
			return false
		if( $(button).hasClass('active') )
			return true
		if ( typeof(currentItem)==='undefined' )
			return false

        if( ltbx ){
			var tmp = $(this).parent().parent().parent().find('.photo img.mainImg')
			tmp.effect('transfer',{ to: $('.point2 b') , easing: 'easeInOutQuint', img: tmp.attr('src') }, 500 )
		}
		var boughtItem = currentItem
		// is_credit
		// var ajurl = $( button ).attr('href') +'/1'
		// if( ltbx.isCredit() )
		// 	ajurl += '/1'
		var ajurl = $( button ).attr('href')
		$.getJSON( ajurl, function(data) {
			if ( data.success && ltbx ) {
				var tmpitem = parseItemNode( boughtItem )
				tmpitem.vitems = data.data.full_quantity
				tmpitem.sum = data.data.full_price
				tmpitem.link = data.data.link
				ltbx.getBasket( tmpitem )
				$(button).attr('href', $('.lightboxinner .point2').attr('href') )
				$(button).addClass('active')
				PubSub.publish( 'productBought', currentItem )

                sendAnalytics($(button))
            }
		})

		return false
	})

    function sendAnalytics(item) {
        if (typeof(MyThings) != "undefined") {
            //matches = item.match("\/cart\/add\/(\\d+)/_quantity")
            if (item.data('product') != "undefined") {
            //    productId = matches[1]

                MyThings.Track({
                    EventType: MyThings.Event.Visit,
                    Action: "1013",
                    ProductId: item.data('product')
                })
            }
        }
        if (($('#adriverProduct').length || $('#adriverCommon').length) && (item.data('product') != "undefined")){
        	 (function(s){
				var d = document, i = d.createElement('IMG'), b = d.body;
				s = s.replace(/![rnd]/, Math.round(Math.random()*9999999)) + '&tail256=' + escape(d.referrer || 'unknown');
				i.style.position = 'absolute'; i.style.width = i.style.height = '0px';
				i.onload = i.onerror = function()
				{b.removeChild(i); i = b = null}
				i.src = s;
				b.insertBefore(i, b.firstChild);
			})('http://ad.adriver.ru/cgi-bin/rle.cgi?sid=182615&sz=add_basket&custom=10='+item.data('product')+';11='+item.data('category')+'&bt=55&pz=0&rnd=![rnd]');
        }
    }

	var BB = new BuyBottons()
	BB.push( 'div.bServiceCardWrap input' ) // F1
	BB.push('div.goodsbarbig a.link1', $('div.goodsbarbig').data('value'), markPageButtons ) // product card, buy big
	// commented cause the same selector works
	// BB.push( '#bigpopup a.link1', $('div.goodsbarbig').data('value'), markPageButtons ) // product card, buy in popup
	BB.push('div.bSet a.link1', $('div.bSet').data('value'), markPageButtons ) // a set card, buy big
	BB.push('div.mServ a.link1', $('div.mServ').data('value') ) // service card, buy big
	BB.push('div.bInShop__eButton a.link1', $('div.bInShop__eButton').data('value'), function(){
		var link1 = $('div.bInShop__eButton a.link1')
		link1.html( '<i> </i>'+link1.html( ) ) 
	}) // stock product card, buy orange
	BB.push('div.goodsinfosmall a.link1', $('div.goodsinfosmall').data('value') ) //feedback feed

	/* BB */
	function BuyBottons() {
		this.push = function( selector, jsond,  afterpost ) {
			if( ! $(selector).length )
				return
			var carturl = $('.lightboxinner .point2').attr('href')
			$('body').delegate( selector, 'click', function(e) {
				e.preventDefault()
				var button = $(this)
				if( !jsond )
					jsond = button.data('value')
				if( !jsond )
					return false
				if( $('body').hasClass('bought') ) {
					document.location = carturl//return true
					return false
				}
				if( button.hasClass('disabled') )
					return false	

				var ajurl = '/404.html'
				if( button.is('a') ) {
					var bt = button.text().replace(/\s/g,'')
					if( bt !== '' && bt !== '&nbsp;' )
						button.text('В корзине')
					ajurl = button.attr('href')
				}
				if( button.is('input') ) {
					button.val('В корзине')
					ajurl = jsond.url
				}
				$('body').addClass('bought')
				$.getJSON( ajurl, function( data ) {
					if ( data.success && ltbx ) {
						var tmpitem = {
							'id'    : jsond.jsref,
							'title' : jsond.jstitle,
							'price' : jsond.jsprice,
							'img'   : ( jsond.jsimg ) ? jsond.jsimg : '/images/logo.png',
							'vitems': data.data.full_quantity,
							'sum'   : data.data.full_price,
							'link'  : data.data.link
						}
						ltbx.getBasket( tmpitem )
						if( afterpost )
							afterpost()
						PubSub.publish( 'productBought', tmpitem )

                        sendAnalytics(ajurl)
                    }
				})
				return false
			})
		}

	} // object BuyBottons

	// analytics HAS YOU
	if( 'ANALYTICS' in window ) {
		PubSub.subscribe( 'productBought', function() {
		})
	}

})

