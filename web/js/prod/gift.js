$(function() {
	var
		$body = $('body'),
		dropBoxOpenClass = 'opn',
		$dropBoxes = $('.js-gift-category-filter-property-dropBox'),
		$dropBoxOpeners = $('.js-gift-category-filter-property-dropBox-opener'),
		$dropBoxContents = $('.js-gift-category-filter-property-dropBox-content'),
		$priceProperty = $('.js-gift-category-filter-element-price');

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

		function changeSexDropBox($dropBox, $selectedItem) {
			if ($dropBox.is('.js-gift-category-filter-property-dropBox-holiday')) {
				var
					selectedHolidayValue = $('input:radio', $selectedItem).val(),
					previousSelectedHolidayValue = $('input:radio:checked', $dropBox).val();

				if (selectedHolidayValue != previousSelectedHolidayValue) {
					var newSexValue;
					if (selectedHolidayValue == '739') { // 8 марта
						newSexValue = '687'; // Женщине
					} else if (selectedHolidayValue == '738') { // 23 февраля
						newSexValue = '688'; // Мужчине
					}

					if (newSexValue) {
						$('.js-gift-category-filter-property-dropBox-sex .js-gift-category-filter-property-dropBox-content-item-input[value="' + newSexValue + '"]').closest('.js-gift-category-filter-property-dropBox-content-item').find('.js-gift-category-filter-property-dropBox-content-item-clicker').click();
					}
				}
			}
		}

		function changeStatusDropBox($dropBox, $selectedItem) {
			if ($dropBox.is('.js-gift-category-filter-property-dropBox-sex')) {
				var
					$statusDropBox = $('.js-gift-category-filter-property-dropBox-status'),
					$statusDropBoxItems = $('.js-gift-category-filter-property-dropBox-content-item', $statusDropBox),
					selectedSexValue = $('input:radio', $selectedItem).val(),
					selectedStatusIndex = $statusDropBoxItems.index($('input:radio:checked', $statusDropBox).closest('.js-gift-category-filter-property-dropBox-content-item')),
					previousSelectedSexValue = $('input:radio:checked', $dropBox).val();

				if (selectedSexValue != previousSelectedSexValue) {
					var
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

					var $statusDropBoxClickers = $('.js-gift-category-filter-property-dropBox-content-item-clicker', $statusDropBox);
					if (!$statusDropBoxClickers[selectedStatusIndex]) {
						selectedStatusIndex = 0;
					}

					$($statusDropBoxClickers[selectedStatusIndex]).click();
				}
			}
		}

		$dropBoxContents.on('click', '.js-gift-category-filter-property-dropBox-content-item-clicker', function(e) {
			var
				$dropBox = $(e.currentTarget).closest('.js-gift-category-filter-property-dropBox'),
				$item = $(e.currentTarget).closest('.js-gift-category-filter-property-dropBox-content-item'),
				title = $('.js-gift-category-filter-property-dropBox-content-item-title', $item).text();

			$('.js-gift-category-filter-property-dropBox-title', $dropBox).text(title);
			$('.js-gift-category-filter-property-dropBox-opener', $dropBox).removeClass('fltrBtnBox_tggl-dsbld');

			setTimeout(function() { // setTimeout для IE8 (иначе не посылается событие change)
				$dropBox.removeClass(dropBoxOpenClass);
			}, 100);

			changeSexDropBox($dropBox, $item);
			changeStatusDropBox($dropBox, $item);

			$body.trigger('trackGoogleEvent', {
				category: 'gift',
				action: 'sort_' + $('input', $item).data('id'),
				label: (title + '').replace(/^\s+|\s+$/g, '')
			});
		});
	})();

	$('.js-gift-category-filter-category input[type="checkbox"]').click(function(e) {
		if (e.currentTarget.checked) {
			$body.trigger('trackGoogleEvent', {
				category: 'gift',
				action: 'category',
				label: $(e.currentTarget).data('title') + ''
			});
		}
	});

	// Фокус ввода на поля цены
	$('input', $priceProperty).focus(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'gift',
			action: 'price',
			label: 'digits'
		});
	});

	// Нажатие на слайдер цены
	$('.js-category-filter-rangeSlider-slider', $priceProperty).mousedown(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'gift',
			action: 'price',
			label: 'scale'
		});
	});

	// Клик по ссылкам страниц
	$body.on('click', '.js-gift-category-pagination-page-link', function(e) {
		$body.trigger('trackGoogleEvent', {
			category: 'gift',
			action: 'scroll',
			label: $(e.currentTarget).data('page') + ''
		});
	});

	// Подгрузка страниц при бесконечной подгрузке
	$body.on('loadInfinityPage', function(e, page) {
		$body.trigger('trackGoogleEvent', {
			category: 'gift',
			action: 'scroll',
			label: page + ''
		});
	});
});