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
	
	function checkForFeedbackForm( userName, data ) {
		if( !data ) {
			if( $('#auth-link').length ) {
				if( $('#auth-block').length )
					$('#auth-block h2').text('Чтобы оставить отзыв, авторизуйтесь!')
				$('#auth-link').trigger('click')			
			}
			$('#rating_form').bind('submit.access', function(e){
				e.preventDefault()
			})
		}
	}

	var psAuth = PubSub.subscribe( 'auth try', checkForFeedbackForm ) // dash.js
		
// no init cause it is new user's comment	
//	$('.ratingvalue').each( function() {
//		$(this).prev().prev().find('.ra'+$(this).val()).click()
//	})
});
