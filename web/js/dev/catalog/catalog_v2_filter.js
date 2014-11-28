$(function() {
	var
		dropBoxOpenClass = 'opn',
		brandTitleOpenClass = 'opn',
		$otherBrands = $('.js-category-v2-filter-otherBrands'),
		$otherBrandsOpener = $('.js-category-v2-filter-otherBrandsOpener'),
		$brandTitle = $('.js-category-v2-filter-brandTitle'),
		$dropBoxes = $('.js-category-v2-filter-dropBox'),
		$dropBoxOpeners = $('.js-category-v2-filter-dropBox-opener'),
		$dropBoxContents = $('.js-category-v2-filter-dropBox-content'),
		$priceLinks = $('.js-category-v2-filter-price-link');

	(function() {
		$dropBoxOpeners.click(function(e) {
			e.preventDefault();

			var
				$dropBox = $(e.currentTarget).closest('.js-category-v2-filter-dropBox'),
				isOpen = $dropBox.hasClass(dropBoxOpenClass);

			$dropBoxes.removeClass(dropBoxOpenClass);

			if (!isOpen) {
				$dropBox.addClass(dropBoxOpenClass);
			}
		});

		$('html').click(function() {
			$dropBoxes.removeClass(dropBoxOpenClass);
		});

		$dropBoxOpeners.add($dropBoxContents).click(function(e) {
			e.stopPropagation();
		});

		$(document).keyup(function(e) {
			if (e.keyCode == 27) {
				$dropBoxes.removeClass(dropBoxOpenClass);
			}
		});
	})();

	$brandTitle.add($otherBrandsOpener).click(function(e) {
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

	$priceLinks.on('click', function(e) {
		e.preventDefault();

		var
			from = ENTER.utils.getURLParam('f-price-from', e.currentTarget.href),
			to = ENTER.utils.getURLParam('f-price-to', e.currentTarget.href),
			$from = $('.js-category-v2-filter-element-price-from'),
			$to = $('.js-category-v2-filter-element-price-to');

		if (from == null) {
			from = $from.data('min');
		}

		if (to == null) {
			to = $to.data('max');
		}

		$from.val(from);
		$to.val(to);

		$from.change();
		$to.change();

		ENTER.catalog.filter.sendFilter();
	});

	$('.js-category-v2-filter-element-number input[type="text"]').placeholder();
});