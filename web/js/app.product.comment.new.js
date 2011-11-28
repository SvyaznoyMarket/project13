$(function(){
	
	$('div.ratingscale').each( function(){
		var self = $(this)
		self.delegate('a', 'click', function() {
			setRating( $(this), self.parent() )
		})
	})
	
	$('div.ratingbox').delegate('a', 'click', function() {
		var rpapa = $('div.ratingbig').parent()
		setRating( $(this), rpapa )
	})
	
	function setRating( target, rpapa ) {
		var n = parseInt( target.prop('class').replace(/\D/g, '') )
		rpapa.find('div.current').width( n*30 ).html(n)
		rpapa.find('div:last').text( target.attr('title') )
		rpapa.find('input').val( n )		
	}

// no init cause it is new user's comment	
//	$('.ratingvalue').each( function() {
//		$(this).prev().prev().find('.ra'+$(this).val()).click()
//	})
})
