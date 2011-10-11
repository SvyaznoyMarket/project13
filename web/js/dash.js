$(document).ready(function(){
	/* Lightbox */
	var lbox = {
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
	}
	ltbx = new Lightbox( $('.lightboxinner'), lbox )
	console.info(ltbx)
	/* ---- */
	
	/* IKEA-like hover */
	var id          = null // setTimeout
	var currentItem = 0 // ref= product ID
	
	$('.goodsbox').bind( {
		'mouseenter': function() {
			var self = this
			function showBorders() {
				if(	$(self).data('run') ) {
					$('.boxhover .photo img', $(self)).css({'width':'163px', 'height':'163px', 'top':'-1px'})					
					$(self).css( {'position':'relative', 'z-index':2 } )
					$('.boxhover', $(self)).fadeIn(200)
				}	
			}
			$(self).data('run', true)
			currentItem = $( '.boxhover',self).attr('ref')
			id = setTimeout( showBorders, 200)
		},
		'mouseleave': function() {
			if(	$(this).data('run') ) {
				clearTimeout( id )
				$(this).data('run',false)
				$('.boxhover', $(this)).hide()
			}
			currentItem = 0
		}		
	}) 
	//$.proxy( $('.goodsbox').mouseenter , $('.goodsbox') )
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
		if( ltbx )
			ltbx.getBasket( parseItemNode( currentItem ) )
		//TODO ajax	
		return false
	})
	$('.goodsbar .link2').click( function() {
		if (! currentItem ) return	
		if( ltbx ) 
			ltbx.getWishes( parseItemNode( currentItem ) )
		//TODO ajax					
		return false
	})
	$('.goodsbar .link3').click( function() {
		if( ltbx )
			ltbx.getComparing()
		//TODO ajax					
		return false
	})
	/* ---- */		
	
	/* draganddrop */
	var draganddrop = new DDforLB( $('.allpageinner'), ltbx )
	console.info(draganddrop)
	$('.boxhover .photo img').bind('mousedown', function(e){
			e.stopPropagation();
			draganddrop.prepare( e.pageX, e.pageY, parseItemNode(currentItem) ) // if delta then d&d
			//window.location.href=''
	})	
	/* ---- */
})