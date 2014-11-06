$(function(){
    var $bodybar = $('.js-bodybar');

    $bodybar.on('mouseenter', function() {
		$bodybar.removeClass('bodybar-hide');
	});
	
	$('.js-bodybar-hideButton', $bodybar).on('click', function() {
		$bodybar.addClass('bodybar-hide');
	});
});