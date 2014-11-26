;(function($){
	var $body = $(document.body);

	$body.on('click', '.jsMainSlidesButton', function(){
		var step = 473,
			$block = $(this).closest('.jsMainSlidesRetailRocket');
		if ($(this).hasClass('jsMainSlidesLeftButton')) {
			$block.find('.jsMainSlidesProductBlock').css('margin-left', '+='+step);
		} else {
			$block.find('.jsMainSlidesProductBlock').css('margin-left', '-='+step);
		}

	});

	if ($('.jsMainSlidesRetailRocket').length == 0) {
		$.get('/index/recommend').done(function(data){
			if (data.result) {
				$(data.result).insertBefore($('.infoBox'));
			}
		})
	}
}(jQuery));