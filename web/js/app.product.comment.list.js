
$(function(){
	
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
		
		//alert(trigger.html());
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
				'</div>',
			];
			
			form.find('textarea').val('');
			trigger.html('Комментарии ('+(num+1)+')');
			cnt.find('.commentanswer').append(html.join(''));
			cnt.find('.subcomments-add-trigger').click();
			
		}, 'json');
		
		return false;
	});
	
});