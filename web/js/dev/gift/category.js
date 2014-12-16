$(function() {
	var
		dropBoxOpenClass = 'opn',
		$dropBoxes = $('.js-gift-category-filter-property-dropBox'),
		$statusDropBoxItems = $('.js-gift-category-filter-property-dropBox-status .js-gift-category-filter-property-dropBox-content-item'),
		$dropBoxOpeners = $('.js-gift-category-filter-property-dropBox-opener'),
		$dropBoxContents = $('.js-gift-category-filter-property-dropBox-content'),
		$dropBoxClickers = $('.js-gift-category-filter-property-dropBox-content-item-clicker');

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
		
		function changeStatusDropBox($dropBox, $selectedItem) {
			if ($dropBox.is('.js-gift-category-filter-property-dropBox-sex')) {
				var
					selectedSexValue = $('input:radio', $selectedItem).val(),
					previousSelectedSexValue = $('input:radio:checked', $dropBox).val();
				
				if (selectedSexValue != previousSelectedSexValue) {
					var $firstItem = null;
					
					if (selectedSexValue == '687') { // Женщине
						(function() {
							var hide = false;
							$statusDropBoxItems.each(function(index, item) {
								var
									$item = $(item),
									value = $('input:radio', $item).val();

								if (!$firstItem) {
									$firstItem = $item;
								}
								
								if (698 == value) {
									hide = true;
								}

								if (hide) {
									$item.hide();
								} else {
									$item.show();
								}
							});
						})();
					} else if (selectedSexValue == '688') { // Мужчине
						(function() {
							var hide = true;
							$statusDropBoxItems.each(function(index, item) {
								var
									$item = $(item),
									value = $('input:radio', $item).val();

								$item.hide();

								if (698 == value) {
									hide = false;
									
									$firstItem = $item;
								}

								if (hide) {
									$item.hide();
								} else {
									$item.show();
								}
							});
						})();
					}
					
					$('.js-gift-category-filter-property-dropBox-content-item-clicker', $firstItem).click();
				}
			}
		}

		$dropBoxClickers.click(function(e) {
			var $dropBox = $(e.currentTarget).closest('.js-gift-category-filter-property-dropBox');
			$('.js-gift-category-filter-property-dropBox-opener', $dropBox).text($('.js-gift-category-filter-property-dropBox-content-item-title', e.currentTarget).text());
			$dropBox.removeClass(dropBoxOpenClass);
			changeStatusDropBox($dropBox, $(e.currentTarget).closest('.js-gift-category-filter-property-dropBox-content-item'));
		});
	})();
});