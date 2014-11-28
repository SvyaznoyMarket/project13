;(function($){
	var $body = $(document.body);

	if (!$body.hasClass('jsMainNew')) return;

	$body.on('click', '.jsMainSlidesButton', function(){
		var step = 473,
			$block = $(this).closest('.jsMainSlidesRetailRocket');
		if ($(this).hasClass('jsMainSlidesLeftButton')) {
			$block.find('.jsMainSlidesProductBlock').css('margin-left', '+='+step);
		} else {
			$block.find('.jsMainSlidesProductBlock').css('margin-left', '-='+step);
		}

	});

	$body.on('click', '.jsShopInfoPreview', function(){
		$('.jsShopInfoBlock').hide();
		$('.jsShopInfoBlock[data-id='+ $(this).data('id')+']').toggle();
	});

	if ($('.jsMainSlidesRetailRocket').length == 0) {
		$.get('/index/recommend').done(function(data){
			if (data.result) {
				$(data.result).insertBefore($('.jsDivBeforeRecommend'));
			}
		})
	}


}(jQuery));