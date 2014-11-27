$(function() {
	var dropBoxOpenedClass = 'opn';

	$('.js-productCategory-filter2-dropBox-open').click(function(e) {
		e.preventDefault();
		var $dropBox = $(e.currentTarget).closest('.js-productCategory-filter2-dropBox');
		$(this).toggleClass(dropBoxOpenedClass);
		$('.js-productCategory-filter2-dropBox-content', $dropBox).toggle();
	});
});