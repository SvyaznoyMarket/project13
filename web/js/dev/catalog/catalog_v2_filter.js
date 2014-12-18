$(function() {
	var
		dropBoxOpenClass = 'opn',
		dropBoxSelectClass = 'actv',
		brandTitleOpenClass = 'opn',
		$body = $(document.body),
		$filter = $('.js-category-filter'),
		$otherBrands = $('.js-category-v2-filter-otherBrands'),
		$otherBrandsOpener = $('.js-category-v2-filter-otherBrandsOpener'),
		$brandTitle = $('.js-category-v2-filter-brandTitle'),
		$brandFilter = $('.js-category-v2-filter-brand'),
		$priceFilter = $('.js-category-v2-filter-element-price'),
		$dropBoxes = $('.js-category-v2-filter-dropBox'),
		$dropBoxOpeners = $('.js-category-v2-filter-dropBox-opener'),
		$dropBoxContents = $('.js-category-v2-filter-dropBox-content'),
		$priceLinks = $('.js-category-v2-filter-price-link'),
		$radio = $('.js-category-v2-filter-element-list-radio'),
		catalogPath = document.location.pathname.replace(/^\/catalog\/([^\/]*).*$/i, '$1'); // Используем значение URL адреса на момент загрузки страницы, т.к. на данный момент при выполнении поиска URL страницы изменяется на URL формы, в которой задан URL из метода http://admin.enter.ru/v2/category/get-seo (в котором содержится некорректный URL; без средней части - "/catalog/holodilniki-i-morozilniki-1096" вместо "/catalog/appliances/holodilniki-i-morozilniki-1096")

	// Открытие и закрытие выпадающих списков
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

		// Закрытие по нажати/ на Esc
		$(document).keyup(function(e) {
			if (e.keyCode == 27) {
				$dropBoxes.removeClass(dropBoxOpenClass);
			}
		});
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
			category: 'filter_bt',
			action: 'brand',
			label: catalogPath
		});
	});

	// Нажатие на один из брендов
	$brandFilter.click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_bt',
			action: 'brand',
			label: catalogPath
		});
	});

	// Фокус ввода на поля цены
	$('input', $priceFilter).focus(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_bt',
			action: 'cost',
			label: catalogPath
		});
	});

	// Нажатие на слайдер цены
	$('.js-category-filter-rangeSlider-slider', $priceFilter).mousedown(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_bt',
			action: 'cost',
			label: catalogPath
		});
	});

	// Нажатие на ссылки открытия выпадающих списков "Цена" и "Скидки"
	$('.js-category-v2-filter-dropBox-price .js-category-v2-filter-dropBox-opener, .js-category-v2-filter-dropBox-labels .js-category-v2-filter-dropBox-opener').click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_bt',
			action: 'cost',
			label: catalogPath
		});
	});

	// Нажатие на диапазоны цен
	$('.js-category-v2-filter-dropBox-price .js-category-v2-filter-price-link').click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_bt',
			action: 'cost_var',
			label: catalogPath
		});
	});

	// Нажатие на диапазоны цен
	$('.js-category-v2-filter-dropBox-labels .js-customInput').click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_bt',
			action: 'cost_sale',
			label: catalogPath
		});
	});

	$('.js-category-v2-filter-otherGroups .js-category-v2-filter-dropBox-opener').click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_bt',
			action: 'other',
			label: catalogPath
		});
	});

	$('.js-category-v2-filter-element-shop-input').click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_bt',
			action: 'other_shops',
			label: catalogPath
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

		ENTER.catalog.filter.sendFilter();
	});

	// Выделение групп при изменении фильтра
	$('input, select, textarea', $filter).on('change', function(e) {
		var
			$dropBox = $(e.currentTarget).closest('.js-category-v2-filter-dropBox'),
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

	// Снятие radio "В магазине"
	(function() {
		$radio.each(function(index, radio) {
			$(radio).data('previous-checked', radio.checked);
		});

		$radio.click(function(e) {
			if ($(e.currentTarget).data('previous-checked')) {
				e.currentTarget.checked = false;
				$(e.currentTarget).data('previous-checked', false).change();
				ENTER.catalog.filter.sendFilter();
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

	// Placeholder'ы для IE9
	$('.js-category-v2-filter-element-number input[type="text"]').placeholder();
});