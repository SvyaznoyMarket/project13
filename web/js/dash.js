$(document).ready(function(){
	function getCookie(c_name) {
		var x , y, allcookies = document.cookie.split(';')
		for ( var i=0, l=allcookies.length; i < l ; i++ ) {
			x = allcookies[i].substr( 0, allcookies[i].indexOf('=') )
			y = allcookies[i].substr( allcookies[i].indexOf('=') + 1 )
			x = x.replace( /^\s+|\s+$/g, '' )
			if (x === c_name) 
				return unescape(y)
		}
		return false
	}
	var shortinfo = '/user/shortinfo'
	if( !getCookie('enter') )
		shortinfo += '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000)
	
	/* GEOIP fix */
	if( !getCookie('geoshop') ) {
		$.getJSON( '/region/init', function(data) {
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
	}
	
	/* Lightbox */
	var lbox = {}
	if (window.Lightbox === undefined) {
		$('.lightboxinner').hide()
		$.getJSON( shortinfo, function(data) {
			if( data.success ) {
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

	/* draganddrop */
	var draganddrop = new DDforLB( $('.allpageinner'), ltbx )
	$('.boxhover[ref] .photo img').live('mousedown', function(e){
			e.stopPropagation()
			e.preventDefault()
			if(e.which == 1)
				draganddrop.prepare( e.pageX, e.pageY, parseItemNode(currentItem) ) // if delta then d&d
	})
	$('.boxhover[ref] .photo img').live('mouseup', function(e){
		draganddrop.cancel()
	})
	/* ---- */
	$.getJSON( shortinfo, function(data) {
			if( data.success ) {
				lbox = data.data
				ltbx.update( lbox )
				ltbx.save()
				changeButtons( lbox )
				/* ltbx */
				var dropbx = $('div.lightboxinner > .dropbox')
				if( dropbx.length ) {
					dropbx.css('left', $('ul.lightboxmenu > li').eq(1).offset().left - $('div.lightboxinner').offset().left )
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
				button.unbind('click').addClass('active')
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
							button.addClass('active').val('В корзине')
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
					button.val('В корзине').addClass('active')
				}
			}
		}
	}
	/* ---- */

	/* IKEA-like hover */
	var id          = null // setTimeout
	var currentItem = 0 // ref= product ID
	var bkgr = $('b.rt:first').css('background-image')

	$('.goodsbox').live( {
		'mouseenter': function() {
			var self = this
			$(self).css('cursor','pointer')
			var im = $('.boxhover .photo img', $(self))

			function showBorders() {
				if(	$(self).data('run') ) {
					var w = im.attr('width')*1
					var h = im.attr('height')*1
					$('.boxhover .photo img', $(self)).css({'width': w + 3, 'height': h + 3 , 'top':'-1px'})
					$(self).css( {'position':'relative', 'z-index':2 })
					$(self).children().fadeOut()
					$('.boxhover', self).fadeIn(200)
				}
			}
			$(self).data('run', true)
			currentItem = $( '.boxhover',self).attr('ref')
			id = setTimeout( showBorders, 200)
		},
		'mouseleave': function() {
			var self = this
			var im = $('.boxhover .photo img', self)
			if(	$(self).data('run') ) {
				clearTimeout( id )
				$(self).data('run',false)
				var w = im.attr('width')*1
				var h = im.attr('height')*1
				$('.boxhover .photo img', self).css({'width': w + 3, 'height': h + 3})
				$(self).css( 'z-index',1 )

				$(self).children().not('.boxhover').fadeIn()
				$('.boxhover', self).fadeOut('slow')
			}
			//currentItem = 0
		}
	})
	/* ---- */

	$('.boxhover .lt').live('click', function(e) {
		if( $(this).attr('data-url') )
			window.location.href = $(this).attr('data-url')
	})

	/* F1 */
	if( $('div.bF1Info').length ) {
		var look    = $('div.bF1Info')
		var f1lines = $('div.bF1Block')
		// open popup
		$('.link1', look).click( function(){
			f1lines.show()
			return false
		})
		// close popup
		$('.close', f1lines).click( function(){
			f1lines.hide()
		})
		// add f1
		f1lines.find('input.button').bind ('click', function() {
			if( $(this).hasClass('disabled') )
				return false
			$(this).val('В корзине').addClass('disabled')
			var f1item = $(this).data()
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
					'img'   : $('.goodsphoto img').attr('src')
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
				f1lines.find('td[ref='+ line.attr('ref') +']').find('input').val('Купить услугу').removeClass('disabled')
				line.remove()
				ltbx.update({ sum: data.data.full_price })

				if( !$('a.bBacketServ__eMore', look).length )
					look.find('h3').html('Выбирай услуги F1<br/>вместе с этим товаром')
			})
			return false
		})
	}
	/* buy bottons */
	var markPageButtons = function(){
		var carturl = $('.lightboxinner .point2').attr('href')
		$('.goodsbarbig .link1').attr('href', carturl ).addClass('active')
		$('#bigpopup a.link1').attr('href', carturl ).html('в корзине')
		$('.bSet__ePrice .link1').unbind('click')
		$('.goodsbar .link1').die('click')
	}

	/* stuff go to litebox */
	function parseItemNode( ref ){
		var jn = $( '.boxhover[ref='+ ref +']')
		var item = {
			'id'   : $(jn).attr('ref'),
			'title': $('h3 a', jn).html(),
			'price': $('.price', jn).html(),
			'img'  : $('.photo img', jn).attr('src')
		}
		return item
	}

	$('.goodsbar .link1').live('click', function(e) {
		var button = this
		if( $(button).hasClass('disabled') )
			return false
		if( $(button).hasClass('active') )
			return true
		if (! currentItem ) return false

		if( ltbx ){
			var tmp = $(this).parent().parent().find('.photo img')
			tmp.effect('transfer',{ to: $('.point2 b') , easing: 'easeInOutQuint', img: tmp.attr('src') }, 500 )
		}
		var boughtItem = currentItem
		$.getJSON( $( button ).attr('href') +'/1', function(data) {
			if ( data.success && ltbx ) {
				var tmpitem = parseItemNode( boughtItem )
				tmpitem.vitems = data.data.full_quantity
				tmpitem.sum = data.data.full_price
				tmpitem.link = data.data.link
				ltbx.getBasket( tmpitem )
				$(button).attr('href', $('.lightboxinner .point2').attr('href') )
				$(button).addClass('active')
			}
		})
		e.stopPropagation()
		return false
	})

	var BB = new BuyBottons()
	BB.push( 'div.bServiceCardWrap input' ) // F1
	BB.push('div.goodsbarbig a.link1', $('div.goodsbarbig').data('value'), markPageButtons ) // product card, buy big
	BB.push( '#bigpopup a.link1', $('div.popup_leftpanel').data('value'), markPageButtons ) // product card, buy in popup
	BB.push('div.bSet a.link1', $('div.bSet').data('value'), markPageButtons ) // a set card, buy big
	BB.push('div.mServ a.link1', $('div.mServ').data('value') ) // service card, buy big

	/* BB */
	function BuyBottons() {
		this.push = function( selector, jsond,  afterpost ) {
			var carturl = $('.lightboxinner .point2').attr('href')
			$('body').delegate( selector, 'click', function() {
				var button = $(this)
				if( !jsond )
					jsond = button.data('value')
				if( !jsond )
					return false
				if( button.hasClass('active') )
					return true

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
				button.addClass('active').attr('href', carturl)

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
					}
				})
				return false
			})
		}

	} // object BuyBottons

	// hidden buttons wishlist+compare
	$('.goodsbarbig .link2, .goodsbarbig .link3').addClass('disabled').attr('href', '#').click( function(){ return false })
	$('.goodsbar .link2').click( function() {
		//if (! currentItem ) return
		//if( ltbx )
		//	ltbx.getWishes( parseItemNode( currentItem ) )
		return false
	})
	$('.goodsbar .link3').click( function() {
		//if( ltbx )
		//	ltbx.getComparing()
		return false
	})
})
