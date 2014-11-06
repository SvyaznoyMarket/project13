$(function(){
    var $body = $('body'),
    	$bodybar = $('.js-bodybar'),
		timeout;

    $bodybar.on('mouseenter', function() {
		timeout = setTimeout(function() {
			$bodybar.removeClass('bodybar-hide');
		}, 200);
	});

	$bodybar.on('mouseleave', function() {
		clearInterval(timeout);
	});
			
	$bodybar.on('click', function() {
		clearInterval(timeout);
		$bodybar.removeClass('bodybar-hide');
	});
	
	$('.js-bodybar-hideButton', $bodybar).on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		clearInterval(timeout);
		docCookies.setItem('subscribed', 0, 157680000, '/');
		$body.trigger('bodybar-hide');
	});
	
	$body.on('bodybar-hide', function() {
		$bodybar.addClass('bodybar-hide');
	});
});