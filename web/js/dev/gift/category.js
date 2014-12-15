$(function() {
	var
		dropBoxOpenClass = 'opn',
		$dropBoxes = $('.js-gift-category-filter-property-dropBox'),
		$dropBoxOpeners = $('.js-gift-category-filter-property-dropBox-opener'),
		$dropBoxContents = $('.js-gift-category-filter-property-dropBox-content'),
		$dropBoxItems = $('.js-gift-category-filter-property-dropBox-content-item');

	// Открытие и закрытие выпадающих списков
	(function() {
		$dropBoxOpeners.click(function(e) {
			e.preventDefault();

			var
				$dropBox = $(e.currentTarget).closest('.js-gift-category-filter-property-dropBox'),
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

		// Закрытие по нажати/ на Esc
		$(document).keyup(function(e) {
			if (e.keyCode == 27) {
				$dropBoxes.removeClass(dropBoxOpenClass);
			}
		});

		$dropBoxItems.click(function(e) {
			var $dropBox = $(e.currentTarget).closest('.js-gift-category-filter-property-dropBox');
			$('.js-gift-category-filter-property-dropBox-opener', $dropBox).text($('.js-gift-category-filter-property-dropBox-content-item-title', e.currentTarget).text());
			$dropBox.removeClass(dropBoxOpenClass);

			if ($dropBox.is('.js-gift-category-filter-property-dropBox-sex')) {
				if ($('input:radio', e.currentTarget).val() == '1') {

				}
			}
		});
	})();
});