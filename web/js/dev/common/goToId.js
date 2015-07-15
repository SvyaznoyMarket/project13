/**
 * Перемотка к id
 */
$(function() {
	$('.jsGoToId').on('click', function(e) {
		e.preventDefault();

		var
			$topbar = $('.js-topbar-fixed'),
			to = $('#' + $(e.currentTarget).data('goto'));

		if ($topbar.length) {
			to = to.offset().top - $topbar.outerHeight();
		}

		$(document).stop().scrollTo(to, 800);
	});
});