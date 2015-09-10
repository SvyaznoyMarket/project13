$(function() {
	var
		dropBoxOpenClass = 'opn',
		dropBoxSelectClass = 'actv',
		brandTitleOpenClass = 'opn',
		$body = $(document.body),
		$v2Filter = $('.js-category-filter-v2'),
		$otherBrands = $('.js-category-v2-filter-otherBrands', $v2Filter),
		$otherBrandsOpener = $('.js-category-v2-filter-otherBrandsOpener', $v2Filter),
		$brandTitle = $('.js-category-v2-filter-brandTitle', $v2Filter),
		$v2BrandFilter = $('.js-category-v2-filter-brand', $v2Filter),
		$v2PriceFilter = $('.js-category-v2-filter-element-price', $v2Filter),
		$priceLinks = $('.js-category-v2-filter-price-link', $v2Filter),
		$radio = $('.js-category-v2-filter-element-list-radio', $v2Filter),
		pageBusinessUnitId = ENTER.utils.getPageBusinessUnitId();

	// Выпадающие списки
	(function() {
		var
			$dropBoxes = $('.js-category-v2-filter-dropBox2'),
			$dropBoxOpeners = $('.js-category-v2-filter-dropBox2-opener'),
			$dropBoxContents = $('.js-category-v2-filter-dropBox2-content');

		$dropBoxOpeners.click(function(e) {
			e.preventDefault();

			var
				$dropBox = $(e.currentTarget).closest('.js-category-v2-filter-dropBox2'),
				isOpen = $dropBox.hasClass(dropBoxOpenClass);

			$dropBoxes.removeClass(dropBoxOpenClass);

			if (!isOpen) {
				$dropBox.addClass(dropBoxOpenClass);
			}
		});

		$('html').click(function() {
			$dropBoxes.removeClass(dropBoxOpenClass);
		});

		$('input[type="radio"]', $dropBoxContents).on('click change', function(e) {
			var
				$dropBox = $(e.currentTarget).closest('.js-category-v2-filter-dropBox2'),
				title;

				$('input[type="radio"]', $dropBox).each(function(i, input) {
					if (input.checked) {
						title = ENTER.utils.trim($dropBox.find('label[for="' + input.id + '"]').text())
						return false;
					}
				});

				if (!title) {
					title = $dropBox.data('default-title');
				}

				$dropBox.find('.js-category-v2-filter-dropBox2-title').text(title);
		});

		$('input[type="checkbox"]', $dropBoxContents).on('click change', function(e) {
			var
				$dropBox = $(e.currentTarget).closest('.js-category-v2-filter-dropBox2'),
				title = '';

			$('input[type="checkbox"]', $dropBox).each(function(i, input) {
				if (input.checked) {
					if (title != '') {
						title += '...';
						return false;
					}
					title += ENTER.utils.trim($dropBox.find('label[for="' + input.id + '"]').text())
				}
			});

			if (!title) {
				title = $dropBox.data('default-title');
			}

			$dropBox.find('.js-category-v2-filter-dropBox2-title').text(title);
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
	})();

	// Выпадающие списки-группы
	(function() {
		var
			$dropBoxes = $('.js-category-v2-filter-dropBox'),
			$dropBoxOpeners = $('.js-category-v2-filter-dropBox-opener'),
			$dropBoxContents = $('.js-category-v2-filter-dropBox-content');

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

		// Закрытие по нажати/ на Esc
		$(document).keyup(function(e) {
			if (e.keyCode == 27) {
				$dropBoxes.removeClass(dropBoxOpenClass);
			}
		});
	})();

	// Выделение групп при изменении фильтра
	$('input, select, textarea', $v2Filter).on('change', function(e) {
		var
			$dropBox = $(e.currentTarget).closest('.js-category-v2-filter-dropBox, .js-category-v2-filter-dropBox2'),
			isSelected = false;

		$('input, select, textarea', $dropBox).each(function(index, element) {
			var $element = $(element);
			if (
				($element.is('input[type="text"], textarea') && ('' != $element.val() || (null != $element.data('min') && $element.val() != $element.data('min')) || (null != $element.data('max') && $element.val() != $element.data('max'))))
				|| ($element.is('input[type="checkbox"], input[type="radio"]') && $element[0].checked)
				|| ($element.is('select') && null != $element.val())
			) {
				isSelected = true;
				return false;
			}
		});

		if (isSelected) {
			$dropBox.addClass(dropBoxSelectClass);
		} else {
			$dropBox.removeClass(dropBoxSelectClass);
		}
	});

	// Снятие radio
	(function() {
		$radio.each(function(index, radio) {
			$(radio).data('previous-checked', radio.checked);
		});

		$radio.click(function(e) {
			var previousChecked = $(e.currentTarget).data('previous-checked');
			$radio.filter('[name="' + e.currentTarget.name + '"]').data('previous-checked', false);

			if (previousChecked) {
				e.currentTarget.checked = false;
				$(e.currentTarget).change();
				ENTER.catalog.filter.send();
			} else {
				$(e.currentTarget).data('previous-checked', true);
			}
		});
	})();

	// Корректировка введённого значения в числовое поле
	(function() {
		var correctNumber = function(e) {
			var
				$input = $(e.currentTarget),
				val = parseFloat(($input.val() + '').replace(/[^\d\.\,]/g, '').replace(',', '.'));

			if (isNaN(val)) {
				val = '';
			} else if (val % 1 != 0) {
				val = Math.floor(val * 10) / 10;
			}

			$input.val(val);
		};

		$('.js-category-v2-filter-element-number-from').on('change', correctNumber);
		$('.js-category-v2-filter-element-number-to').on('change', correctNumber);
	})();

	// Сворачивание/разворачивание брендов
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

		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'brand',
			label: pageBusinessUnitId
		});
	});

	// Диапазоны цен
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

		ENTER.catalog.filter.send();
	});

	// Placeholder'ы для IE9
	$('.js-category-v2-filter-element-number input[type="text"]').placeholder();

	// Нажатие на один из брендов
	$v2BrandFilter.click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'brand',
			label: pageBusinessUnitId
		});
	});

	// Фокус ввода на поля цены
	$('input', $v2PriceFilter).focus(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'cost',
			label: pageBusinessUnitId
		});
	});

	// Нажатие на слайдер цены
	$('.js-category-filter-rangeSlider-slider', $v2PriceFilter).mousedown(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'cost',
			label: pageBusinessUnitId
		});
	});

	// Нажатие на ссылки открытия выпадающего списка "Цена"
	$('.js-category-v2-filter-dropBox-price .js-category-v2-filter-dropBox-opener', $v2Filter).click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'cost',
			label: pageBusinessUnitId
		});
	});

	// Нажатие на диапазоны цен
	$('.js-category-v2-filter-dropBox-price .js-category-v2-filter-price-link', $v2Filter).click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'cost_range',
			label: pageBusinessUnitId
		});
	});

	// Нажатие на ссылки открытия выпадающего списка "Скидки" и на элементы блока "Скидки"
	$('.js-category-v2-filter-dropBox-labels .js-category-v2-filter-dropBox-opener, .js-category-v2-filter-dropBox-labels .js-customInput', $v2Filter).click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'cost_sale',
			label: pageBusinessUnitId
		});
	});

	// Открытие остальных групп фильтров
	$('.js-category-v2-filter-otherGroups .js-category-v2-filter-dropBox-opener', $v2Filter).click(function(e) {
		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'other_' + $(e.currentTarget).data('name'),
			label: pageBusinessUnitId
		});
	});

	$('.js-category-v2-filter-element-shop-input', $v2Filter).click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter',
			action: 'other_shops',
			label: pageBusinessUnitId
		});
	});
});