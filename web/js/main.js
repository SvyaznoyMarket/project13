$(document).ready(function(){

    $('.form input[type=checkbox],.form input[type=radio]').prettyCheckboxes();

	$(".bigfilter dt").click(function(){
		$(this).next(".bigfilter dd").slideToggle(200)
		$(this).toggleClass("current")
		return false
	})

	$(".f1list dt B").click(function(){
		$(this).parent("dt").next(".f1list dd").slideToggle(200)
		$(this).toggleClass("current")
		return false
	})

	$(".tagslist dt").click(function(){
		$(this).next(".tagslist dd").slideToggle(200)
		$(this).toggleClass("current")
		return false
	})


/* — Sliders ---------------------------------------------------------------------------------------*/	

	var mini = $('.sliderbox .fl').html() * 1
	var maxi = $('.sliderbox .fr').html() * 1
	var from = null
	var to   = null
	if( $('#t_price_from').length ) {
		from = $('#t_price_from')
		to   = $('#t_price_to')
	}
	if( $('#f_price_from').length ) {
		from = $('#f_price_from')
		to   = $('#f_price_to')
	}	
	if( from && from.val() ) {
		$('.bigfilter dd:first').slideToggle(200)
	}
		
	if ($( "#slider-range1" ).length) $( "#slider-range1" ).slider({
		range: true,
		step: 10,
		min: mini,
		max: maxi,
		values: [ from.val() ? from.val() : mini ,  to.val() ? to.val() : maxi ],
		slide: function( event, ui ) {
			from.val( ui.values[ 0 ] )
			to.val( ui.values[ 1 ] )
		},
		change: function() {
			$('.product_filter-block').trigger('preview')
		}
	})
	if ( from && to ) {
		from.val( $( "#slider-range1" ).slider( "values", 0 ) )
		to.val( $( "#slider-range1" ).slider( "values", 1 ) )
		from.change( function(){
			from.val( from.val().replace(/\D/g,'') )
			if( from.val() > $( "#slider-range1" ).slider( "values", 1 ) ) {
				$( "#slider-range1" ).slider( "values", 1 , from.val()*1 + 10 )
				to.val( from.val()*1 + 10 )
			}
			$( "#slider-range1" ).slider( "values", 0 , from.val() )
			
		})
		to.change( function(){
			to.val( to.val().replace(/\D/g,'') )
			if( parseInt(to.val()) < $( "#slider-range1" ).slider( "values", 0 ) ) {
				$( "#slider-range1" ).slider( "values", 0 , to.val()*1- 10 )
				from.val( to.val()*1 - 10 )
			}			
			$( "#slider-range1" ).slider( "values", 1 , to.val() )
		})

	}

// TODO Rating
    jQuery(this).find('.ratingbox A').hover(function(){
        $("#ratingresult").html(this.innerHTML)
        return false;
    });

    $(".yellowbutton").mousedown(function()   {
    	$(this).toggleClass("yellowbuttonactive")
    }).mouseup(function()   {
    	$(this).removeClass("yellowbuttonactive")
    })

    $(".whitebutton").mousedown(function()   {
    	$(this).toggleClass("whitebuttonactive")
    }).mouseup(function()   {
    	$(this).removeClass("whitebuttonactive")
    })

    $(".whitelink").mousedown(function()   {
    	$(this).toggleClass("whitelinkactive")
    }).mouseup(function()   {
    	$(this).removeClass("whitelinkactive")
    })

    $(".goodsbar .link1").bind( 'click.css', function()   {
        $(this).addClass("link1active")
    })

    $(".goodsbar .link2").bind( 'click.css', function()   {
        //$(this).addClass("link2active")
    })

    $(".goodsbar .link3").bind( 'click.css', function()   {
        //$(this).addClass("link3active");
    })


	var idcm          = null // setTimeout
	var currentMenu = 0 // ref= product ID
	var corneroffsets = [167,222,290,362,435,515,587,662,717]

	$('.topmenu a').bind( {
		'mouseenter': function() {
			$('.extramenu').hide()
			var self = this

			function showList() {
				if(	$(self).data('run') ) {
					var i = $(self).attr('class').replace(/\D+/,'')
					var punkt = $( '#extramenu-root-'+ $(self).attr('id').replace(/\D+/,'') )
					if( punkt.find('dl').html().replace(/\s/g,'') != '' )
						punkt.show().find('.corner').css('left',corneroffsets[i-1])
				}
			}
			$(self).data('run', true)
			currentMenu = $(self).attr('id').replace(/\D+/,'')
			idcm = setTimeout( showList, 300)
		},
		'mouseleave': function() {
			var self = this

			if(	$(self).data('run') ) {
				clearTimeout( idcm )
				$(self).data('run',false)
			}
			//currentMenu = 0
		}
	})

	$(document).click( function(e){
		if (currentMenu) {			
			if( e.which == 1 )
				$( '#extramenu-root-'+currentMenu+'').data('run', false).hide()
		}	
	})
	
	$('.extramenu').click( function(e){
		e.stopPropagation()
	})
	/* ---- */ 
	if( $('.error_list').length && $('.basketheader').length ) {
		$.scrollTo( $('.error_list:first'), 300 )
	}
	/* bill typewriter */
	
	if( $('.chequebottom ul').length && ! $('.error_list').length ) {
		$('.chequebottom li div').hide()
		$('.chequebottom li strong').hide()
		$('.chequebottom .total strong').hide()
		var rubl = $('<span class="rubl">p</span>')
		function recF( i ) {
			if( i == $('.chequebottom li').length ) {
				var total = $('.chequebottom .total strong')
				total.show().find('span').remove()
				total.typewriter( 700, function(){ total.append( rubl ) })
			}
			if( i < $('.chequebottom li').length )
				$('.chequebottom li').eq(i).find('div').show().typewriter( 1000, function(){ 
					$('.chequebottom li').eq(i).find('strong').show()
					recF(i+1) 
				})
		}		

		recF(0)

	}
	/* CART */
	function printPrice ( val ) {
	
		var float = (val+'').split('.')
		var out = float[0]
		var le = float[0].length
		if( le > 6 ) { // billions
			out = out.substr( 0, le - 6) + ' ' + out.substr( le - 6, le - 4) + ' ' + out.substr( le - 3, le ) 			
		} else if ( le > 3 ) { // thousands
			out = out.substr( 0, le - 3) + ' ' + out.substr( le - 3, le )			
		}		
		if( float.length == 2 ) 
			out += '.' + float[1]		
		return out// + '&nbsp;'
	}
	
	var total = $('.allpageinner > .basketinfo .price')
	
	function getTotal() {
		for(var i=0, tmp=0; i < basket.length; i++ ) {
			if( ! basket[i].noview ) 
				tmp += basket[i].sum * 1
		}
		total.html( printPrice( tmp ) )
		total.typewriter(800)
	}		
	
	function basketline ( nodes ) {
		var self = this
		
		$(nodes.less).data('run',false)
		$(nodes.more).data('run',false)
			var main = $(nodes.line)	
		var deladd   = $(nodes.more).parent().attr('href')
		var drop     = $(nodes.drop).attr('href')
		var  price   = $(nodes.price).html().replace(/\s/,'')
		this.sum     = $(nodes.sum).html().replace(/\s/,'')		
		this.quantum = $(nodes.quan).html().replace(/\D/g,'')
		this.noview  = false
		var dropflag = false
		
		this.calculate = function( q ) {
			self.quantum = q
			self.sum = price * q
			$(nodes.sum).html( printPrice( self.sum ) )
			$(nodes.sum).typewriter(800, getTotal)
//			getTotal() 			
		}
		
		this.clear = function() {
			$.getJSON( drop , function( data ) {
				$(nodes.drop).data('run',false)
				if( data.success ) {
					main.hide()
					self.noview = true
					getTotal() 
				}
			})
		}
		
		this.update = function( minimax, delta ) {
			$.getJSON( deladd + '/'+ delta , function( data ) {
				$(minimax).data('run',false)
				if( data.success && data.data.quantity ) {
					$(nodes.quan).html( data.data.quantity + ' шт.' )
					self.calculate( data.data.quantity )
					var liteboxJSON = ltbx.restore() 
					liteboxJSON.vitems += delta
					liteboxJSON.sum    += delta * price
					ltbx.update( liteboxJSON )
				}
			})
		}
		
		$(nodes.drop).click( function() {
			if(! $(nodes.drop).data('run') ) {
				$(nodes.drop).data('run', true)
				dropflag = self.clear()
			}	
			return false
		})
		
		$(nodes.less).click( function() {
			var minus = this
			
			if( ! $(minus).data('run') ) {
				$(minus).data('run',true)
				if( self.quantum > 1 ) 
					self.update( minus, -1 )					
				else
					self.clear()				
			}
			return false
		})
		
		$(nodes.more).click( function() {
			var plus = this
			
			if( ! $(plus).data('run') ) {
				$(plus).data('run',true)
				self.update( plus, 1 )
			}
			return false
		})
		
	} // object basketline
	
	var basket = []
	
	$('.basketline').each( function(){
		var tmpline = new basketline({ 
						'line': $(this),
						'less': $(this).find('.ajaless'),
						'more': $(this).find('.ajamore'),
						'quan': $(this).find('.ajaquant'),
						'price': $(this).find('.basketinfo .price'),
						'sum': $(this).find('.basketinfo .sum'),
						'drop': $(this).find('.basketinfo .whitelink')
						})
		basket.push( tmpline )
	})
	
	/* ---- */	
	/* tags */
	$('.fm').toggle( function(){
		$(this).parent().find('.hf').slideDown()
		$(this).html('скрыть')
	}, function(){
		$(this).parent().find('.hf').slideUp()		
		$(this).html('еще...')	
	})	
	/* ---- */	
	/* cards carousel  */
	
	function cardsCarousel ( nodes ) {
		var self = this
		var current = 1
		var max = $(nodes.times).html() * 1
		var wi  = $(nodes.width).html().replace(/\D/g,'')
		var buffer = 2
		var ajaxflag = false
		
		this.notify = function() {
			$(nodes.crnt).html( current )
			if ( current == 1 ) 
				$(nodes.prev).addClass('disabled')
			else 
				$(nodes.prev).removeClass('disabled')
			if ( current == max ) 
				$(nodes.next).addClass('disabled')
			else 
				$(nodes.next).removeClass('disabled')	
		}
		
		var shiftme = function() {
			var boxes = $(nodes.wrap).find('.goodsbox')
			$(boxes).hide()
			var le = boxes.length				
			for(var j = (current - 1) * 3 ; j < current  * 3 ; j++) {
				boxes.eq( j ).show()
			}
		}
	
		$(nodes.next).bind('click', function() {			
			if( current < max && !ajaxflag ) {
				if( current + 1 == max ) { //the last pull is loaded , so special shift
					var boxes = $(nodes.wrap).find('.goodsbox')
					$(boxes).hide()
					var le = boxes.length				
					var rest = ( wi % 3 ) ?  wi % 3  : 3 		
					for(var j = 1; j <= rest; j++)
						boxes.eq( le - j ).show()
					current++	
				} else {
					if( current + 1 >= buffer ) { // we have to get new pull from server
						$(nodes.next).css('opacity','0.4') // addClass dont work ((
						ajaxflag = true
						$.get( $(nodes.prev).attr('data-url') + '?page=' + (buffer+1), function(data) {
							buffer++
							$(nodes.next).css('opacity','1')
							ajaxflag = false
							var tr = $('<div>')
							$(tr).html( data )
							$(tr).find('.goodsbox').css('display','none')							
							$(nodes.wrap).html( $(nodes.wrap).html() + tr.html() )
							tr = null
						})
						current++
						shiftme()
					} else { // we have new portion as already loaded one
						current++
						shiftme() // TODO repair
					}
				}
				
							
				self.notify()				
			}
		})
		
		$(nodes.prev).click( function() {
			if( current > 1 ) {				
				current--
				shiftme()								 
				self.notify()					
			}
		})	
		
	} // cardsCarousel object
	
		
	$('.carouseltitle').each( function(){
		var tmpline = new cardsCarousel ({ 
					'prev'  : $(this).find('.back'),
					'next'  : $(this).find('.forvard'),
					'crnt'  : $(this).find('span:first'),
					'times' : $(this).find('span:eq(1)'),
					'width' : $(this).find('.rubrictitle strong'),
					'wrap'  : $(this).find('~ .carousel').first()
					})
	})
		
	/* ---- */
	/* charachteristics */
	if ( $('#toggler').length ) {
		$('#toggler').toggle( function(){
			$('.descriptionlist:first').slideUp()
			$('.descriptionlist.second').slideDown()
			$(this).html('Общие характеристики')
		},  function(){
			$('.descriptionlist.second').slideUp()
			$('.descriptionlist:first').slideDown()			
			$(this).html('Все характеристики')			
		})
	}
	/* search tags */
	if( $('#plus10').length ) {
		if( $('#filter_product_type-form li').length < 10 )
			$('#plus10').hide()
		else
			$('#plus10').html( 'еще '+ ($('#filter_product_type-form .hf').length % 10 + 1) +' из ' + $('#filter_product_type-form li').length )
		$('#plus10').click( function(){
			$('#filter_product_type-form .hf').slice(0,10).removeClass('hf')
			if ( !$('#filter_product_type-form .hf').length ) 
				$(this).parent().hide()
			return false
		})
	}
});


$(function(){

	$(".tearmlist dt").click(function(){
		$(this).next(".tearmlist dd").slideToggle(200);
		$(this).toggleClass("current");
		return false;
	});

});
