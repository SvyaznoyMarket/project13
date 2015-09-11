$(function() {
	var
		pageBusinessUnitId = ENTER.utils.getPageBusinessUnitId(),
		$v1Filter = $('.js-category-filter-v1'),
		$v1PriceFilter = $('.js-category-filter-element-price', $v1Filter),
		$v1OtherParams = $('.js-category-filter-otherParams', $v1Filter),
		isSlice = $('.js-slice').length;
	;

	// Для слайсов события в аналитику пока не шлём, т.к. для реализации событий перехода на карточку, добавления в
	// корзину и покупки необходимо добавить либо поддержку множественных sender'ов либо добавить поддержку
	// параметра sender3 (который использовать для данной аналитики). Отравку событий взаимодействия с фильтрами без
	// отправки события перехода/добавления/покупки не делает, чтобы не портить статистику по filter_old.
	if (!isSlice) {
		// Нажатие на кнопку "Подобрать"
		$('.js-category-filter-submit', $v1Filter).click(function() {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'find',
				label: pageBusinessUnitId
			});
		});

		// Фокус ввода на поля цены
		$('input', $v1PriceFilter).focus(function() {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'cost',
				label: pageBusinessUnitId
			});
		});

		// Нажатие на слайдер цены
		$('.js-category-filter-rangeSlider-slider', $v1PriceFilter).mousedown(function() {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'cost',
				label: pageBusinessUnitId
			});
		});

		// Нажатие на кнопку "Бренды и параметры"
		$('.js-category-filter-otherParamsToggleButton', $v1Filter).click(function() {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'brand_parameters',
				label: pageBusinessUnitId
			});
		});

		// Нажатие на ссылки разделов фильтра
		$('.js-category-filter-param', $v1OtherParams).click(function(e) {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'using_brand_parameters_' + $(e.currentTarget).data('name'),
				label: pageBusinessUnitId
			});
		});

		// Использование элементов фильтра
		(function() {
			$('.js-category-filter-element input[type="checkbox"], .js-category-filter-element input[type="radio"]', $v1OtherParams).click(function(e) {
				$body.trigger('trackGoogleEvent', {
					category: 'filter_old',
					action: 'using_brand_parameters_' + $(e.currentTarget).closest('.js-category-filter-element').data('name'),
					label: pageBusinessUnitId
				});
			});

			$('.js-category-filter-element input[type="text"]', $v1OtherParams).focus(function(e) {
				$body.trigger('trackGoogleEvent', {
					category: 'filter_old',
					action: 'using_brand_parameters_' + $(e.currentTarget).closest('.js-category-filter-element').data('name'),
					label: pageBusinessUnitId
				});
			});

			$('.js-category-filter-rangeSlider-slider', $v1OtherParams).mousedown(function(e) {
				$body.trigger('trackGoogleEvent', {
					category: 'filter_old',
					action: 'using_brand_parameters_' + $(e.currentTarget).closest('.js-category-filter-element').data('name'),
					label: pageBusinessUnitId
				});
			});
		})();
	}
});