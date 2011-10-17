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
	var label = $( "#f_price_from" ).parent().find(':disabled')
	if( $('#f_price_from').val() ) {
		$('.bigfilter dd:first').slideToggle(200)
	}
	if ($( "#slider-range1" ).length) $( "#slider-range1" ).slider({
		range: true,
		step: 10,
		min: mini,
		max: maxi,
		values: [ $('#f_price_from').val() ? $('#f_price_from').val() : mini ,  $('#f_price_to').val() ? $('#f_price_to').val() : maxi ],
		slide: function( event, ui ) {
			label.val( ui.values[ 0 ] + " - " + ui.values[ 1 ] )
			$('#f_price_from').val( ui.values[ 0 ] )
			$('#f_price_to').val( ui.values[ 1 ] )
		}
	})
	if ( label.length ) label.val( $( "#slider-range1" ).slider( "values", 0 ) +
		" - " + $( "#slider-range1" ).slider( "values", 1 ) )


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

	/* top menu */	 
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
					if( punkt.find('dl').html().trim() != '' )
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
	
	$(document).click( function(){
		if (currentMenu)
			$( '#extramenu-root-'+currentMenu+'').data('run', false).hide()
	})
	/* ---- */	
	
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
console.info('printPrice',val, out)			
		return out// + '&nbsp;'
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
		
console.info(  price, this.quantum, drop, deladd)
		
		this.calculate = function( q ) {
			self.quantum = q
			self.sum = price * q
			$(nodes.sum).html( printPrice( self.sum ) )
		}
		
		this.clear = function() {
			$.getJSON( drop , function( data ) {
				if( data.success ) {
					main.hide()
				}
			})
		}
		
		this.update = function( minimax, delta ) {
console.info(deladd + '/'+ delta)
			$.getJSON( deladd + '/'+ delta , function( data ) {
				$(minimax).data('run',false)
				if( data.success && data.data.quantity ) {
					$(nodes.quan).html( data.data.quantity + ' шт.' )
					self.calculate( data.data.quantity )
				}
			})
		}
		
		$(nodes.drop).click( function() {
console.info('drop')
			//self.clear()
			return false
		})
		
		$(nodes.less).click( function() {
			var minus = this
			
			if( ! $(minus).data('run') ) {
				$(minus).data('run',true)
console.info('minus',self.quantum )
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
	console.info($(this).find('.basketinfo .sum'))
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
	
	/* cards carousel  */	
	$('.forvard').click( function(){
		//сделать запрос, получить новые данные
		//найти блок карусели
		//скрыть старые, показать новое
		//console.info( $(this) )
		//обновить внутреннюю переменную
		//обновить состояние кнопок
		return false
	})
	/* ---- */
});	