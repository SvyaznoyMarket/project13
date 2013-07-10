/**
 * Видео в карточке товара
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, jQuery.lightbox_me
 */
;(function(){
	var openVideo = function(){
		if ($('#productVideo').length){
			return false;
		}

		var videoStartTime = 0;
		var videoEndTime = 0;
		var productUrl = document.location.href;
		var shield = $('.bPhotoActionOtherAction__eVideo');
		var iframe = $('#productVideo .productVideo_iframe').html();

		$('#productVideo .productVideo_iframe').empty();
		shield.bind('click', function(){
			$('#productVideo .productVideo_iframe').append(iframe);
			$(".productVideo_iframe iframe").attr("src", $(".productVideo_iframe iframe").attr("src")+"?autoplay=1");
			$('#productVideo').lightbox_me({ 
				centered: true,
				onLoad: function(){
					videoStartTime = new Date().getTime();
					if (typeof(_gaq) !== 'undefined') {
						_gaq.push(['_trackEvent', 'Video', 'Play', productUrl]);
					}
				},
				onClose: function(){
					$('#productVideo .productVideo_iframe').empty();
					videoEndTime = new Date().getTime();
					var videoSpent = videoEndTime - videoStartTime;
					if (typeof(_gaq) !== 'undefined') {
						_gaq.push(['_trackEvent', 'Video', 'Stop', productUrl, videoSpent]);
					}
				}
			});
			return false;
		});
		return false;
	};

	$(document).ready(function() {
		if ($('.bPhotoActionOtherAction__eVideo').length){
			$('.bPhotoActionOtherAction__eVideo').bind('click', openVideo);
		}
	});
}());