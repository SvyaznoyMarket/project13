/**
 * Видео в карточке товара
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, jQuery.lightbox_me
 */
;$(function() {
	var $video = $('.js-product-video');
	if (!$video.length || !$('.js-product-video-container').length) {
		return;
	}

	var
		videoStartTime = 0,
		videoEndTime = 0,
		productUrl = document.location.href,
		$iframeContainer = $('.js-product-video-iframeContainer'),
		iframeHtml = $iframeContainer.html();

	$iframeContainer.empty();

	$video.bind('click', function() {
		var $iframeContainer = $('.js-product-video-iframeContainer');
		$iframeContainer.append(iframeHtml);

		var $iframe = $('iframe', $iframeContainer);
		$iframe.attr('src', $iframe.attr('src') + '?autoplay=1');

		$('.js-product-video-container').lightbox_me({
			centered: true,
			onLoad: function() {
				videoStartTime = new Date().getTime();
			},
			onClose: function() {
				$('.js-product-video-iframeContainer').empty();
				videoEndTime = new Date().getTime();
				var videoSpent = videoEndTime - videoStartTime;
			}
		});

		return false;
	});
});