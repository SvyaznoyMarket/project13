;(function($){
	var $body = $(document.body);
	$('.jsMainSlidesRetailRocket').on('click', '.jsMainSlidesButton', function(){
		var step = 473,
			$block = $(this).closest('.jsMainSlidesRetailRocket');
		if ($(this).hasClass('jsMainSlidesLeftButton')) {
			$block.find('.jsMainSlidesProductBlock').css('margin-left', '+='+step);
		} else {
			$block.find('.jsMainSlidesProductBlock').css('margin-left', '-='+step);
		}

	})
}(jQuery));