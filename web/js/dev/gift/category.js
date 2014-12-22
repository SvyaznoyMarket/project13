$(function() {
	var
		dropBoxOpenClass = 'opn',
		$dropBoxes = $('.js-gift-category-filter-property-dropBox'),
		$dropBoxOpeners = $('.js-gift-category-filter-property-dropBox-opener'),
		$dropBoxContents = $('.js-gift-category-filter-property-dropBox-content');

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
					var
						$statusDropBox = $('.js-gift-category-filter-property-dropBox-status'),
						$statusDropBoxItems = $('.js-gift-category-filter-property-dropBox-content-item', $statusDropBox),
						$prototypeItem = $($statusDropBoxItems[0]),
						$prototypeItemParent = $prototypeItem.parent(),
						optionGroups = $statusDropBox.data('optionGroups'),
						optionGroup = null;

					if (selectedSexValue == '687') { // Женщине
						optionGroup = optionGroups['woman'];
					} else if (selectedSexValue == '688') { // Мужчине
						optionGroup = optionGroups['man'];
					}

					$statusDropBoxItems.each(function(index, item) {
						$(item).remove();
					});

					$.each(optionGroup, function(index, option) {
						var
							$prototypeItemClone = $prototypeItem.clone(),
							$input = $('input', $prototypeItemClone),
							$label = $('label', $prototypeItemClone);

						$input.attr('id', option.id);
						$input.attr('name', option.name);
						$input.attr('value', option.value);
						$input.removeAttr('checked');
						$label.attr('for', option.id);
						$label.text(option.title);

						$prototypeItemParent.append($prototypeItemClone);
					});

					$($('.js-gift-category-filter-property-dropBox-content-item-clicker', $statusDropBox)[0]).click();
				}
			}
		}

		$dropBoxContents.on('click', '.js-gift-category-filter-property-dropBox-content-item-clicker', function(e) {
			var
				$dropBox = $(e.currentTarget).closest('.js-gift-category-filter-property-dropBox'),
				$item = $(e.currentTarget).closest('.js-gift-category-filter-property-dropBox-content-item');

			$('.js-gift-category-filter-property-dropBox-title', $dropBox).text($('.js-gift-category-filter-property-dropBox-content-item-title', $item).text());
			$('.js-gift-category-filter-property-dropBox-opener', $dropBox).removeClass('fltrBtnBox_tggl-dsbld');

			setTimeout(function() { // setTimeout для IE8 (иначе не посылается событие change)
				$dropBox.removeClass(dropBoxOpenClass);
			}, 100);

			changeStatusDropBox($dropBox, $item);
		});
	})();
});