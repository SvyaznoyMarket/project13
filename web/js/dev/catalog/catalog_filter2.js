$(function() {
	var dropBoxOpenedClass = 'opened';

	$('.js-productCategory-filter2-dropBox-open').click(function(e) {
		e.preventDefault();
		var $dropBox = $(e.currentTarget).closest('.js-productCategory-filter2-dropBox');
		$dropBox.addClass(dropBoxOpenedClass);
		$('.js-productCategory-filter2-dropBox-content', $dropBox).toggle();
	});
});