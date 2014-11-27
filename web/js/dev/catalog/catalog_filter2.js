$(function() {
	var
		dropBoxOpenClass = 'opn',
		brandTitleOpenClass = 'opn',
		$otherBrands = $('.js-productCategory-filter2-otherBrands'),
		$otherBrandsOpener = $('.js-productCategory-filter2-otherBrandsOpener'),
		$brandTitle = $('.js-productCategory-filter2-brandTitle'),
		$dropBoxOpener = $('.js-productCategory-filter2-dropBox-opener'),
		$dropBoxContent = $('.js-productCategory-filter2-dropBox-content');

	$dropBoxOpener.click(function(e) {
		e.preventDefault();

		var isOpen = $(this).hasClass(dropBoxOpenClass);

		$dropBoxOpener.removeClass(dropBoxOpenClass);
		$dropBoxContent.hide();

		if (!isOpen) {
			$(this).addClass(dropBoxOpenClass);
			$('.js-productCategory-filter2-dropBox-content', $(e.currentTarget).closest('.js-productCategory-filter2-dropBox')).show();
		}
	});

	$('html').click(function() {
		$dropBoxOpener.removeClass(dropBoxOpenClass);
		$dropBoxContent.hide();
	});

	$dropBoxOpener.add($dropBoxContent).click(function(e) {
		e.stopPropagation();
	});

	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			$dropBoxOpener.removeClass(dropBoxOpenClass);
			$dropBoxContent.hide();
		}
	});

	$otherBrandsOpener.add($brandTitle).click(function(e) {
		e.preventDefault();

		$otherBrands.toggle();

		if ($otherBrands.css('display') == 'none') {
			$otherBrandsOpener.show();
			$brandTitle.removeClass(brandTitleOpenClass);
		} else {
			$otherBrandsOpener.hide();
			$brandTitle.addClass(brandTitleOpenClass);
		}
	});

	$('.js-productCategory-filter2-element-number input[type="text"]').placeholder();
});