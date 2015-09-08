$(function() {
	var
		$v3Filter = $('.js-category-filter-v3'),
		$v3OtherParams = $('.js-category-filter-otherParamsContent', $v3Filter),
		$v3PriceFilter = $('.js-category-filter-element-price', $v3Filter)
	;

	// Выбор/отмена значений у фильтров "Металл" и "Вставка"
	$('.js-category-filter-alwaysShowFilter input[type="checkbox"], .js-category-filter-alwaysShowFilter input[type="radio"]', $v3Filter).click(function(e) {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_jewelry',
			action: $(e.currentTarget).closest('.js-category-filter-alwaysShowFilter').data('name'),
			label: ''
		});
	});

	// Фокус ввода на поля цены
	$('input', $v3PriceFilter).focus(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_jewelry',
			action: 'Цена',
			label: ''
		});
	});

	// Нажатие на слайдер цены
	$('.js-category-filter-rangeSlider-slider', $v3PriceFilter).mousedown(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_jewelry',
			action: 'Цена',
			label: ''
		});
	});

	// Использование элементов фильтра из блока "Ещё параметры"
	(function() {
		$('.js-category-filter-element input[type="checkbox"], .js-category-filter-element input[type="radio"]', $v3OtherParams).click(function(e) {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_jewelry',
				action: $(e.currentTarget).closest('.js-category-filter-element').data('name'),
				label: ''
			});
		});

		$('.js-category-filter-element input[type="text"]', $v3OtherParams).focus(function(e) {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_jewelry',
				action: $(e.currentTarget).closest('.js-category-filter-element').data('name'),
				label: ''
			});
		});

		$('.js-category-filter-rangeSlider-slider', $v3OtherParams).mousedown(function(e) {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_jewelry',
				action: $(e.currentTarget).closest('.js-category-filter-element').data('name'),
				label: ''
			});
		});
	})();
});