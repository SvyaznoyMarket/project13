/**
 * Перемотка к Id
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
(function(){
	var goToId = function(){
		var to = $(this).data('goto');
		jQuery.scrollTo( $('#'+to), 800 );
		return false;
	};
	
	$(document).ready(function() {
		$('.jsGoToId').bind('click',goToId);
	});
}());