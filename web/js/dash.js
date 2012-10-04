$(document).ready(function(){
	var carturl = $('.lightboxinner .point2').attr('href')

	var shortinfo = '/user/shortinfo'
	if( !docCookies.hasItem('enter') ||  !docCookies.hasItem('enter_auth'))
		shortinfo += '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000)
	
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

	/* IKEA-like hover */
	var id          = null // setTimeout
	var currentItem = 0 // ref= product ID
	var bkgr = $('b.rt:first').css('background-image') 
	$('.goodsbox').live( {
		'mouseenter': function() {
			var self = this
			if( $(this).hasClass('goodsline') ) {
				currentItem = $( '.goodsboxlink',self).attr('ref')
				return
			}
			if( $(this).parent().hasClass('bigcarousel') ) {
				currentItem = $( '.boxhover',self).attr('ref')
				return
			}
			
			$(self).css('cursor','pointer')
			var im = $('.boxhover .mainImg', $(self))

			function showBorders() {
				if(	$(self).data('run') ) {
					var w = im.attr('width')*1
					var h = im.attr('height')*1
					$('.boxhover .mainImg', $(self)).css({'width': w + 3, 'height': h + 3 , 'top':'-1px'})
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
			if( $(this).hasClass('goodsline') || $(this).parent().hasClass('bigcarousel')  ) return
			var self = this
			var im = $('.boxhover .mainImg', self)
			if(	$(self).data('run') ) {
				clearTimeout( id )
				$(self).data('run',false)
				var w = im.attr('width')*1
				var h = im.attr('height')*1
				$('.boxhover .mainImg', self).css({'width': w + 3, 'height': h + 3})
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
			if( $(this).hasClass('active') )
				return false
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
		$('.goodsbarbig .link1').attr('href', carturl ).addClass('active')
		$('#bigpopup a.link1').attr('href', carturl ).html('в корзине')
		$('.bSet__ePrice .link1').unbind('click')
		$('.goodsbar .link1').die('click')
		$('.bCountSet__eP').html('&nbsp;').addClass('disabled')
		$('.bCountSet__eM').html('&nbsp;').addClass('disabled')
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
		var button = this
		if( $(button).hasClass('disabled') )
			return false
		if( $(button).hasClass('active') )
			return true
		if (! currentItem ) return false

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
			$('body').delegate( selector, 'click', function() {
				var button = $(this)
				if( !jsond )
					jsond = button.data('value')
				if( !jsond )
					return false
				if( button.hasClass('active') ) {
					document.location = button.attr('href')//return true
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
				button.addClass('active').attr('href', carturl)
				//credit case
				// if( 'creditBox' in window ) { // productCard
				// 	if( !ajurl.match(/_quantity\/[0-9]+/) )
				// 		ajurl += '/1' //quantity
				// 	if( creditBox.getState() )
				// 		ajurl += '/1' //credit
				// 	else 	
				// 		ajurl += '/0' //no credit
				// }
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
					}
				})
				return false
			})
		}

	} // object BuyBottons

	// analytics HAS YOU
	if( 'ANALYTICS' in window ) {
		PubSub.subscribe( 'productBought', function() {
			if( 'gooReMaBuy' in ANALYTICS ) {
				ANALYTICS.gooReMaBuy()
			}
			if( 'myThingsBuy' in ANALYTICS ) {
				ANALYTICS.myThingsBuy( arguments[1] )
			}
		})
	}

})
