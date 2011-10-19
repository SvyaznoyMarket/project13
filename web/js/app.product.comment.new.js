//TODO rewrite
$(function(){
	
	$('.ratingscalebox a').click(function(){
		var n = parseInt($(this).prop('class').replace('ra', ''));
		$(this).parent().parent().find('.current').width(n*30).html(n);
		$(this).parent().parent().parent().next().html($(this).html());
		$(this).parent().parent().parent().next().next().val(n);
		$(this).parent().parent().parent().next().next().next().val(n);
	});
	
	$('.ratingbox a').click(function(){
		var n = parseInt($(this).prop('class').replace('ra', ''));
		$(this).parent().parent().find('.current').width(n*30).html(n);
		$(this).parent().parent().parent().next().html($(this).html());
		$(this).parent().parent().parent().next().next().val(n);
	});
	
	
	$('.ratingvalue').each(function(i, item){
		$(this).prev().prev().find('.ra'+$(this).val()).click();
	});
	
});