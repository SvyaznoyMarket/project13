// Simple lazy loading
;$('nav').on('mouseover', '.navsite2_i', function(){
	$(this).find('.menuImgLazy').each(function(){
		$(this).attr('src', $(this).data('src'))
	});
});