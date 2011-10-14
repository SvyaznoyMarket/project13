$(document).ready(function(){
	/* Lightbox */
	var lbox = {}
	$.getJSON('/user/shortinfo', function(data) {
			//console.info( data.data )
			if( data.success )
				lbox = data.data
			else
				lbox  = {
					'name':'Ivanov',
					'vcomp':0, // число сравниваемых
					'vwish':0, // число товаров в вишлисте
					'vitems': 0, // число покупок
					'sum': 0, // текущая сумма покупок
					'bingo': {}
				}
			ltbx = new Lightbox( $('.lightboxinner'), lbox )
			/* draganddrop */
			var draganddrop = new DDforLB( $('.allpageinner'), ltbx )
			$('.boxhover[ref] .photo img').bind('mousedown', function(e){
					e.stopPropagation();
					draganddrop.prepare( e.pageX, e.pageY, parseItemNode(currentItem) ) // if delta then d&d
					//window.location.href=''
			})	
			/* ---- */
		})
	/*var lbox  = {
		'name':'Ivanov',
		'vcomp':2, // число сравниваемых
		'vwish':3, // число товаров в вишлисте
		'vitems': 1, // число покупок
		'sum': 2333, // текущая сумма покупок
		'bingo': { 'id': '22323', // id товара
					'title': 'Карта памяти Sandisk 16GB MicroSDHC Memory Card',
					'price': 2123456.33,
					'img':'images/photo61.jpg'
				 } 
	}*/
	
	/* ---- */
	
	/* IKEA-like hover */
	var id          = null // setTimeout
	var currentItem = 0 // ref= product ID
	
	$('.goodsbox').bind( {
		'mouseenter': function() {
			var self = this
			$(self).css('cursor','pointer')
			var im = $('.boxhover .photo img', $(self))
			function showBorders() {
				if(	$(self).data('run') ) {
					var w = im.attr('width')*1
					var h = im.attr('height')*1					
					$('.boxhover .photo img', $(self)).css({'width': w + 3, 'height': h + 3 , 'top':'-1px'})					
					$(self).css( {'position':'relative', 'z-index':2 } )
					$('.boxhover', $(self)).fadeIn(200)
				}	
			}
			$(self).data('run', true)
			currentItem = $( '.boxhover',self).attr('ref')
			id = setTimeout( showBorders, 200)
		},
		'mouseleave': function() {
			var self = this
			var im = $('.boxhover .photo img', $(self))
			if(	$(self).data('run') ) {
				clearTimeout( id )
				$(self).data('run',false)
				var w = im.attr('width')*1
				var h = im.attr('height')*1									
				$('.boxhover .photo img', $(self)).css({'width': w + 3, 'height': h + 3})				
				$(self).css( 'z-index',1 )
				$('.boxhover', $(self)).hide()				

			}
			currentItem = 0
		}		
	}) 
	/* ---- */
	
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
	
	/* stuff goes into lightbox */
	$('.goodsbar .link1').click( function() {
		if (! currentItem ) return
		var button = this
		if( ltbx ){
			var tmp = $(this).parent().parent().find('.photo img')
			tmp.effect('transfer',{ to: $('.point2 b') , easing: 'easeInOutQuint', img: tmp.attr('src') }, 500, function() {
				//ltbx.getBasket( parseItemNode( currentItem ) )
			})
		}	
		$.getJSON('/cart/add/'+$( '.boxhover[ref='+ currentItem +']').attr('ref') +'/1', function(data) {
			if ( data.success && ltbx ) {
				ltbx.getBasket( parseItemNode( currentItem ) )
				$(button).attr('href', $('.lightboxinner .point2', parent).attr('href') )
				$(button).unbind('click')
			}	
		})
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
	$('.goodsbarbig .link1').click( function() {
		var button = this
		$.getJSON( $( button ).attr('href') +'/1', function(data) {			
			if ( data.success && ltbx ) {
				var tmpitem = { 
					'id'   : $( button ).attr('href'),
					'title': $('h1').html(),
					'price': $('.goodsinfo .price').html(),
					'img'  : $('.goodsphoto img').attr('src')
				}
				ltbx.getBasket( tmpitem ) 
				$(button).attr('href', $('.lightboxinner .point2', parent).attr('href') )
				$(button).unbind('click').addClass('active')				
			}	
		})
		return false
	})
	// hidden buttons wishlist+compare
	$('.goodsbarbig .link2, .goodsbarbig .link3').addClass('disabled').attr('href', '#').click( function(){ return false })
	/* ---- */		
	
	
})