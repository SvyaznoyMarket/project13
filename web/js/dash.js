$(document).ready(function(){
	/* Lightbox */
	var lbox = {}
	if (window.Lightbox === undefined) {
		$('.lightboxinner').hide()
		$.getJSON('/user/shortinfo', function(data) {
			if( data.success ) {
				if( data.data.name ) {
					var dtmpl={}
					dtmpl.user = data.data.name
					var show_user = tmpl('auth_tmpl', dtmpl)
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
	$.getJSON('/user/shortinfo', function(data) {
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
					$('#auth-link').after(show_user)
				} else $('#auth-link').show()
			}

	})

	var changeButtons = function( lbox ){
		if(!lbox || !lbox.productsInCart ) return false
		for( var token in lbox.productsInCart) {
			var bx = $('div.boxhover[ref='+ token +']')
			if( bx.length ) {
				var button = $('a.link1', bx)
				button.attr('href', $('.lightboxinner .point2').attr('href') )
				button.addClass('active')	//die('click') doesnt work			
			}
			bx = $('div.goodsbarbig[ref='+ token +']')
			if( bx.length ) {
				var button = $('a.link1', bx)
				button.attr('href', $('.lightboxinner .point2').attr('href') )
				button.unbind('click').addClass('active')
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

	function parseItemNode( ref ){
		var jn = $( '.boxhover[ref='+ ref +']')
		//console.info( 'parseItemNode',ref, jn )
		var item = {
			'id'   : $(jn).attr('ref'),
			'title': $('h3 a', jn).html(),
			'price': $('.price', jn).html(),
			'img'  : $('.photo img', jn).attr('src')
		}
		return item
	}

	/* stuff goes into lightbox */
	$('.boxhover .lt').live('click', function(e) {
		if( $(this).attr('data-url') )
			window.location.href = $(this).attr('data-url')
	})

	$('.goodsbar .link1').live('click', function(e) {
		var button = this
		if( $(button).hasClass('disabled') )
			return false
		if( $(button).hasClass('active') )
			return true	
		if (! currentItem ) return false

		if( ltbx ){
			var tmp = $(this).parent().parent().find('.photo img')
			tmp.effect('transfer',{ to: $('.point2 b') , easing: 'easeInOutQuint', img: tmp.attr('src') }, 500, function() {
				//ltbx.getBasket( parseItemNode( currentItem ) )
			})
		}
		$.getJSON( $( button ).attr('href') +'/1', function(data) {
			if ( data.success && ltbx ) {
				var tmpitem = parseItemNode( currentItem )
				tmpitem.vitems = data.data.full_quantity
				tmpitem.sum = data.data.full_price
				ltbx.getBasket( tmpitem )
				$(button).attr('href', $('.lightboxinner .point2').attr('href') )
				$(button).addClass('active') 
			}
		})
		e.stopPropagation()
		return false
	})
	$('.goodsbar .link2').click( function() {
		//if (! currentItem ) return
		//if( ltbx )
		//	ltbx.getWishes( parseItemNode( currentItem ) )
		//TODO ajax
		return false
	})
	$('.goodsbar .link3').click( function() {
		//if( ltbx )
		//	ltbx.getComparing()
		//TODO ajax
		return false
	})

	/* F1 */
	if( $('div.bF1Info').length ) {
		$('div.bF1Info').click( function(){
			$('div.bF1Block').show()
			return false
		})
		$('div.bF1Block .close').click( function(){
			$('div.bF1Block').hide()
		})
		$('div.bF1Block').delegate('input.button','click', function(){
		console.info( $(this).data('url') )
			$.getJSON( $(this).data('url'), function(data) {
			})
			return false
			// change button title
			// post buy F1 item
				// if product not in cart - post buy product
		})
	}
	/* buy bottons */
	var markPageButtons = function(){
		var carturl = $('.lightboxinner .point2').attr('href')
		$('.goodsbarbig .link1').attr('href', carturl ).addClass('active').unbind('click')
		$('#bigpopup a.link1').attr('href', carturl ).html('в корзине').unbind('click')
		$('.bSet__ePrice .link1').unbind('click')
		$('.goodsbar .link1').die('click')
	}

	$('.goodsbarbig .link1').click( function() {
		var button = this
		if( $(button).hasClass('disabled') )
			return false
		$.getJSON( $( button ).attr('href') +'/1', function(data) {
			if ( data.success && ltbx ) {
				var tmpitem = {
					'id'    : $( button ).attr('href'),
					'title' : $('h1').html(),
					'vitems': data.data.full_quantity,
					'sum'   : data.data.full_price,
					'price' : $('.goodsinfo .price').html(),
					'img'   : $('.goodsphoto img').attr('src')
				}
				ltbx.getBasket( tmpitem )
				markPageButtons()
				//$(button).unbind('click')
			}
		})
		return false
	})

	$('#bigpopup a.link1').click( function() {
		var button = this
		$.getJSON( $( button ).attr('href') +'/1', function(data) {
			if ( data.success && ltbx ) {
				var tmpitem = {
					'id'    : $( button ).attr('href'),
					'title' : $('h1').html(),
					'vitems': data.data.full_quantity,
					'sum'   : data.data.full_price,
					'price' : $('.goodsinfo .price').html(),
					'img'   : $('.goodsphoto img').attr('src')
				}
				ltbx.getBasket( tmpitem )
				markPageButtons()
				//$(button).unbind('click')
			}
		})
		return false
	})
	// hidden buttons wishlist+compare
	$('.goodsbarbig .link2, .goodsbarbig .link3').addClass('disabled').attr('href', '#').click( function(){ return false })

	$('.bSet__ePrice .link1').click( function() {
		var button = this
		if( $(button).hasClass('disabled') )
			return false
		$.getJSON( $( button ).attr('href') +'/1', function(data) {
			if ( data.success && ltbx ) {
				var tmpitem = {
					'id'   : $( button ).attr('href'),
					'title': $('h2').html(),
					'vitems': data.data.full_quantity,
					'sum'   : data.data.full_price,
					'price': $('.bSet__ePrice .price').html(),
					'img'  : $('.bSet__eImage img').attr('src')
				}
				ltbx.getBasket( tmpitem )
				markPageButtons()
				//$(button).unbind('click')
			}
		})
		return false
	})
	/* ---- */


})