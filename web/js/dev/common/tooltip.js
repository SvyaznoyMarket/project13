$(function() {
	var $body = $('body');
	
	function open($tooltipContents) {
		$tooltipContents.show();
	}
	
	function close($tooltipContents) {
		$tooltipContents.hide();
	}
	
	$body.on('click', '.js-tooltip-opener', function(e) {
		e.preventDefault();
		e.stopPropagation();
		open($('.js-tooltip-content', $(e.currentTarget).closest('.js-tooltip')));
	});
	
	$body.on('click', '.js-tooltip-closer', function(e) {
		e.preventDefault();
		close($('.js-tooltip-content', $(e.currentTarget).closest('.js-tooltip')));
	});

	$body.on('click', '.js-tooltip-content', function(e) {
		e.stopPropagation();
	});
	
	$('html').click(function() {
		close($('.js-tooltip-content'));
	});

	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			close($('.js-tooltip-content'));
		}
	});
});