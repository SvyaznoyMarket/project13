$(function(){
	/* Comments List */
	var filterlink = $('.filter .filterlink:first');
	var filterlist = $('.filter .filterlist');
	filterlink.mouseenter(function(){
		filterlink.hide();
		filterlist.show();
	});
	filterlist.mouseleave(function(){
		filterlist.hide();
		filterlink.show();
	});
	
	
	$('.sharebox .sharelink').mouseenter(function(){
		$(this).next().show();
	});
	$('.sharebox .sharelist').mouseleave(function(){
		$(this).hide();
	});
	
	
	$('.subcomments-trigger').click(function(){
		$(this).toggleClass('commentlinkopen commentlink');
		$(this).next().toggle();
	});
	
	$('.subcomments-add-trigger').click(function(){
		$(this).next().toggle();
	});
	
	
	$('.addcomment form').submit(function(e){
		e.preventDefault();
		
		var form = $(this),
			cnt = form.parent().parent(),
			trigger = cnt.find('.subcomments-trigger'),
			num = parseInt(trigger.html().replace(/Комментарии \((\d+)\)/, '$1'));
		
		$.post(form.prop('action'), form.serializeArray(), function(resp){
			
			var html = [
				'<div class="answer">',
					'<div class="answerleft">',
						'<div class="avatar">.....&nbsp;&nbsp;<img src="/css/skin/img/avatar.png" alt="" width="54" height="54" /></div>',
						resp.data.user_name,
						'<br/>',
						'<span class="gray">',
						resp.data.created_at,
						'</span>',
					'</div>',
					'<div class="answerright">',
						resp.data.content,
					'</div>',
				'</div>'
			];
			
			form.find('textarea').val('');
			trigger.html('Комментарии ('+(num+1)+')');
			cnt.find('.commentanswer').append(html.join(''));
			cnt.find('.subcomments-add-trigger').click();
			
		}, 'json');
		
		return false;
	});

	/* New Comment */
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
				$('#auth-link').trigger('click')
				e.preventDefault()
			})
		}
	}

	var psAuth = PubSub.subscribe( 'auth try', checkForFeedbackForm ) // dash.js
	$('#rating_form').bind( 'submit.check', function(e) {
		var alertnode = $('<div>').css({'color':'red', 'clear':'both'})
		if( $(this).find('.ratingvalue').val() * 1 == 0 ) {
			$(this).find('.ratingresult').after( alertnode.clone().text('Укажите свою оценку') )
			e.preventDefault()
		}
		if( $(this).find('[name=content_resume]').val() === '' ) {
			$(this).find('[name=content_resume]').after( alertnode.clone().text('Заполните поле') )
			e.preventDefault()
		}
	})
	
});