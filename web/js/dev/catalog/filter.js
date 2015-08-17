;(function() {
	console.info('New catalog init: filter.js');

	var
		$body = $('body'),
		catalog = ENTER.utils.extendApp('ENTER.catalog'),
		catalogPath = document.location.pathname.replace(/^\/catalog\/([^\/]*).*$/i, '$1'), // Используем значение URL адреса на момент загрузки страницы, т.к. на данный момент при выполнении поиска URL страницы изменяется на URL формы, в которой задан URL из метода http://admin.enter.ru/v2/category/get-seo (в котором содержится некорректный URL; без средней части - "/catalog/holodilniki-i-morozilniki-1096" вместо "/catalog/appliances/holodilniki-i-morozilniki-1096")

		filterOpenClass = 'fltrSet_tggl-dn',
		viewSwitcherActiveClass = 'active',

		$filterBlock = $('.js-category-filter'),
		$filterOtherParamsToggleButton = $filterBlock.find('.js-category-filter-otherParamsToggleButton'),
		$filterOtherParamsContent = $filterBlock.find('.js-category-filter-otherParamsContent'),
		$filterSliders = $filterBlock.find('.js-category-filter-rangeSlider'),
		$filterNumbers = $filterBlock.find('.js-category-v2-filter-element-number input'),
		$filterMenuItem = $filterBlock.find('.js-category-filter-param'),
		$filterCategoryBlocks = $filterBlock.find('.js-category-filter-element'),
		$priceFilter = $('.js-category-v1-filter-element-price'),
		$priceForFacetSearch = $('.js-gift-category-filter-element-price'),
		$otherParams = $('.js-category-v1-filter-otherParams'),
		$viewParamPanel = $('.js-category-sortingAndPagination'),
		$bottomInfButton = $('.js-category-pagination-infinity-enableLink').last(),
		$filterSubmitBtn = $('.js-category-filter-submit', '.js-category-filter'),
		$listingWrap = $('.js-listing'),

		isV3 = $filterBlock.hasClass('js-category-filter-v3'),

		manualDefinedPriceFrom = ENTER.utils.getURLParam('f-price-from', document.location.href),
		manualDefinedPriceTo = ENTER.utils.getURLParam('f-price-to', document.location.href),

		backClick = true,
		updateOnChange = true,
		nowPage = 1,
		lastPage = $('#bCatalog').data('lastpage'),
		lastResult = null,
		loading = false, // SITE-5008 "Товары не найдены" в листингах
		liveScroll = false,
		loader = createLoader(),
		timer,

		templateAppliers = {
			list: function(html) {
				$listingWrap.empty();
				$listingWrap.html(html);
			},

			selectedFilter: function(html) {
				$filterBlock.find('.js-category-filter-selected').empty().html(html);
			},

			sorting: function(html) {
				$viewParamPanel.find('.js-category-sorting').replaceWith(html);
			},

			pagination: function(html) {
				$('.js-category-pagination').replaceWith(html);
			},

			page: function(html) {
				$('.js-pageTitle').empty().html(html);
			},

			countProducts: function (html) {
				if (html && parseInt(html) >= 0) {
					$('.js-category-filter-submit', '.js-category-filter').text('Подобрать (' +  html + ')');
				}
			}
		},
		templateRenderers = {
			list: function(data) {
				var
					template,
					$expandedViewSwitcher = $('.js-category-viewSwitcher-link-expanded');

				// Используем проверку HTML элемента вместо проверки значение cookie categoryView, т.к. во время
				// просмотра страницы каталога значение cookie может быть изменено (например, при просмотре страницы
				// каталога в другом окне) и при бесконечной прокрутке или переключении страниц будут подгружаться
				// товары в другом виде, нежели выбран в переключателе
				if ($expandedViewSwitcher.hasClass(viewSwitcherActiveClass) || !$expandedViewSwitcher.length && $listingWrap.data('category-view') == 'expanded') {
					template = $('#listing_expanded_tmpl');
				} else {
					template = $('#listing_compact_tmpl');
				}

				return Mustache.render(template.html(), data, template.data('partial'));
			},

			selectedFilter: function(data) {
				if (!data) {
					return;
				}

				var template = $('#tplSelectedFilter');
				return Mustache.render(template.html(), data, template.data('partial'));
			},

			sorting: function(data) {
				var template = $('script.tplSorting:first');
				return Mustache.render(template.html(), data, template.data('partial'));
			},

			pagination: function(data) {
				var template = $('script.tplPagination:first');
				return Mustache.render(template.html(), data, template.data('partial'));
			},

			page: function(data) {
				return data.title;
			},

			countProducts: function (data) {
				return data;
			}
		}
	;

	function createLoader() {
		var $loader = null;
		return {
			start: function() {
				if ($loader) {
					return;
				}

				$loader = $('<li>').addClass('mLoader');
				$filterSubmitBtn.addClass('mButLoader').text('Подобрать');

				if (!liveScroll) {
					$listingWrap.empty();
				}

				$listingWrap.append($loader);
			},

			stop: function() {
				if ($loader) {
					$loader.remove();
					$loader = null;
				}

				$filterSubmitBtn.removeClass('mButLoader');
			}
		}
	}

	/**
	 * Обработка перехода на URL
	 * Если браузер не поддерживает History API происходит обычный переход по ссылке
	 *
	 * @param	{String}	url			Адрес на который необходимо выполнить переход
	 */
	function goToUrl(url) {
		if (!History.enabled) {
			document.location.href = url;
			return;
		}

		var state = {
			title: document.title,
			url: url,
			data: {
				scrollTop: $(window).scrollTop()
			}
		};

		backClick = false;
		History.pushState(state, state.title, state.url);
	}

	/**
	 * Запросить новые данные с сервера по url
	 *
	 * @param	{String}	url
	 * @param	{Function}	callback
	 */
	function getDataFromServer(url, callback) {
		loader.start();

		$.ajax({
			type: 'GET',
			url: url,
			success: function(res) {
				if (typeof res === 'object') {
					callback(res);
				} else {
					console.warn('res isn\'t object');
					console.log(typeof res);
				}

				loader.stop();

				$('.js-listing, .js-jewelListing').each(function() {
					ko.cleanNode(this);
					ko.applyBindings(ENTER.UserModel, this);
				});
			},
			error: function() {
				loading = false;
				loader.stop();
			}
		});
	}

	function checkInfinityScroll() {
        console.info('check...', {lastPage: lastPage});
		if (!loading && $bottomInfButton.visible() && (lastPage - nowPage > 0 || null == lastPage)) {
			loadInfinityPage();
			$body.trigger('loadInfinityPage', [nowPage]);
		}
	}

	function loadInfinityPage() {
		nowPage += 1;
		liveScroll = true;
		loading = true;

		getDataFromServer(
            getFilterUrl().addParameterToUrl('page', nowPage).addParameterToUrl('ajax', 'true'),
            function(res) {
                loading = false;
                $listingWrap.append(templateRenderers['list'](res['list'])); // TODO Вызывать renderCatalogPage вместо templateRenderers['list']?
    		}
        );
	}

	function enableInfinityScroll(onlyIfAlreadyEnabled) {
		if (onlyIfAlreadyEnabled && docCookies.getItem('infScroll') != '1') {
			return;
		}

		var
			activeClass = 'mActive act',
			infBtn = $viewParamPanel.find('.js-category-pagination-infinity'),
			pagingBtn = $viewParamPanel.find('.js-category-pagination-paging'),
			pageBtn = $viewParamPanel.find('.js-category-pagination-page'),
			url = getFilterUrl(),
			hasPaging = document.location.search.match('page=');

		pagingBtn.css({'display':'inline-block'});
		pageBtn.hide();
		infBtn.addClass(activeClass);

		nowPage = 1;
		loading = false;

		docCookies.setItem('infScroll', 1, 4*7*24*60*60, '/');

		checkInfinityScroll();
		$(window).on('scroll', checkInfinityScroll);

		if (History.enabled && hasPaging) {
			goToUrl(url);
		}

		$bottomInfButton = $('.js-category-pagination-infinity-enableLink').last();

		if ($bottomInfButton.visible() && lastPage > 1) {
			loadInfinityPage();
		}
	}

	function disableInfinityScroll() {
		var url = getFilterUrl();

		liveScroll = false;
		url = url.addParameterToUrl('ajax', 'true');

		docCookies.setItem('infScroll', 0, 4*7*24*60*60, '/');
		$(window).off('scroll', checkInfinityScroll);
		getDataFromServer(url, renderCatalogPage);
	}

	function setManualDefinedPriceFrom(from, min) {
		if (manualDefinedPriceFrom != from) {
			if (from == min) {
				manualDefinedPriceFrom = null;
			} else {
				manualDefinedPriceFrom = from;
			}
		}
	}

	function setManualDefinedPriceTo(to, max) {
		if (manualDefinedPriceTo != to) {
			if (to == max) {
				manualDefinedPriceTo = null;
			} else {
				manualDefinedPriceTo = to;
			}
		}
	}

	/**
	 * Отрисовка шаблона продуктов
	 *
	 * @param	{Object}	res		Данные для шаблона
	 */
	function renderCatalogPage(res) {
		var
			dataToRender = res ? res : lastResult,
			key,
			template,
			newLastPage = res['pagination'] ? res['pagination']['lastPage'] : false;

		(function() {
			if (res.filters && res.filters.price && $priceForFacetSearch.length) {
				var
					$slider = $('.js-category-filter-rangeSlider-slider', $priceForFacetSearch),
					$from = $('.js-category-filter-rangeSlider-from', $priceForFacetSearch),
					$to = $('.js-category-filter-rangeSlider-to', $priceForFacetSearch),
					from = parseFloat('0' + $from.val()),
					to = parseFloat('0' + $to.val());

				if ((!manualDefinedPriceFrom || manualDefinedPriceFrom <= res.filters.price.max) && res.filters.price.max) {
					$slider.slider('option', 'max', res.filters.price.max);

					// Внимание: изменение данных значений не выполняет поиск заново, поэтому важно, чтобы данные изменения не влияли на возможный результат поиска
					if (to > res.filters.price.max) {
						$to.val(res.filters.price.max);
						$slider.slider('values', 1, res.filters.price.max);
					} else {
						var newTo = manualDefinedPriceTo || res.filters.price.max;  // Если значение "до" не задавалось вручную, то значение "до" будет установлено в новое максимальное значение
						$to.val(newTo);
						$slider.slider('values', 1, newTo);
					}
				}

				if (!manualDefinedPriceTo || manualDefinedPriceTo >= res.filters.price.min) {
					$slider.slider('option', 'min', res.filters.price.min);

					// Внимание: изменение данных значений не выполняет поиск заново, поэтому важно, чтобы данные изменения не влияли на возможный результат поиска
					if (from < res.filters.price.min) {
						$from.val(res.filters.price.min);
						$slider.slider('values', 0, res.filters.price.min);
					} else {
						var newFrom = manualDefinedPriceFrom || res.filters.price.min; // Если значение "от" не задавалось вручную, то значение "от" будет установлено в новое минимальное значение
						$from.val(newFrom);
						$slider.slider('values', 0, newFrom);
					}
				}
			}
		})();

		for (key in dataToRender) {
			if (!dataToRender.hasOwnProperty(key)) {
				continue;
			}

			if (templateRenderers.hasOwnProperty(key)) {
				template = templateRenderers[key](dataToRender[key]);
			}

			if (templateAppliers.hasOwnProperty(key)) {
				templateAppliers[key](template);
			}
		}

		if (newLastPage) {
			lastPage = newLastPage;
		}

		lastResult = dataToRender;
		enableInfinityScroll(true);
	}

	/**
	 * Получение изменненых и неизменненых полей слайдеров
	 * @return {Object} {changedSliders: массив имен измененных полей, unchangedSliders: массив имен неизмененных полей}
	 */
	function getSlidersInputState() {
		var result = {
			changedSliders: [],
			unchangedSliders: []
		};

		$filterSliders.each(function() {
			var
				sliderWrap = $(this),
				slider = sliderWrap.find('.js-category-filter-rangeSlider-slider'),
				sliderFromInput = sliderWrap.find('.js-category-filter-rangeSlider-from'),
				sliderToInput = sliderWrap.find('.js-category-filter-rangeSlider-to');

			if (sliderFromInput.val() * 1 == slider.slider('option', 'min')) {
				result.unchangedSliders.push(sliderFromInput.attr('name'));
			} else {
				result.changedSliders.push(sliderFromInput.attr('name'));
			}

			if (sliderToInput.val() * 1 == slider.slider('option', 'max')) {
				result.unchangedSliders.push(sliderToInput.attr('name'));
			} else {
				result.changedSliders.push(sliderToInput.attr('name'));
			}
		});

		return result;
	}

	function getUnchangedNumberFieldNames() {
		var unchangedNumbers = [];
		$filterNumbers.each(function(index, input) {
			var $input = $(input);
			// В IE <= 9 класс placeholder добавляется к полям, в которых введено значение, которое не надо передавать на сервер
			if ($input.hasClass('placeholder')) {
				unchangedNumbers.push(input.name);
			}
		});

		return unchangedNumbers;
	}

	/**
	 * Формирование URL для получения результатов фильтра
	 *
	 * @return	{String}	url
	 */
	function getFilterUrl(page) {
		var formData = $filterBlock.serializeArray(),
			url = $filterBlock.attr('action') || '',
			slidersInputState = getSlidersInputState(),
			unchangedNumberFieldNames = getUnchangedNumberFieldNames(),
			activeSort = $viewParamPanel.find('.js-category-sorting-activeItem:not(.js-category-sorting-defaultItem)').find('.js-category-sorting-link'),
			sortUrl = activeSort.length ? activeSort.data('sort') : null,
			formSerizalizeData,
			urlParams = getUrlParams(),
			hasCategory = false;

		for (var i = formData.length - 1; i >= 0; i--) {
			if (slidersInputState.unchangedSliders.indexOf(formData[i].name) !== -1 || unchangedNumberFieldNames.indexOf(formData[i].name) != -1 || formData[i].value == '') {
				formData.splice(i,1);
			}
		}

		// передаем категрию, если она была задана
		if (urlParams && urlParams['category']) {
			$.each(formData, function() {
				if (this.name == 'category') {
					hasCategory = true;
				}
			});

			// добавляем категорию в параметры, если она не добавлена
			if (!hasCategory) {
				formData.unshift({name: 'category', value: urlParams['category']});
			}
		}

		formSerizalizeData = $.param(formData);

		if (formSerizalizeData.length !== 0) {
			url += (url.indexOf('?') === -1) ? '?' + formSerizalizeData : '&' + formSerizalizeData;
		}

		if (sortUrl) {
			url = url.addParameterToUrl('sort', sortUrl);
		}

		if (!page) {
			page = $.deparam(location.search).page;
		}

		if (page && page > 1 && (!lastPage || page <= lastPage)) {
			url = url.addParameterToUrl('page', page);
		}

		return url;
	}

	/**
	 * Получение get параметров текущей страницы
	 */
	function getUrlParams() {
		var
			$_GET = {},
			__GET = location.search.substring(1).split('&'),
			getVar,
			i;

		for (i = 0; i < __GET.length; i++) {
			getVar = __GET[i].split('=');
			$_GET[getVar[0]] = typeof(getVar[1]) == 'undefined' ? '' : getVar[1];
		}

		return $_GET;
	}

	/**
	 * Обновление значений формы
	 */
	function updateFilterForm(values) {
		var
			input,
			val,
			type,
			fieldName;

		var updateInput = {
			'text': function(input, val) {
				input.val(val).trigger('change');
			},

			'radio': function(input, val) {
				var
					self = input.filter('[value="'+val+'"]'),
					id = self.attr('id'),
					label = $filterBlock.find('label[for="'+id+'"]');

				self.attr('checked', 'checked');
				label.addClass('mChecked');
				self.trigger('change');
			},

			'checkbox': function(input, val) {
				input.filter('[value="'+val+'"]').attr('checked', 'checked').trigger('change');
			}
		};

		for (fieldName in values) {
			if (!values.hasOwnProperty(fieldName)) {
				return;
			}

			input = $filterBlock.find('input[name="'+fieldName+'"]');
			val = values[fieldName];
			type = input.attr('type');

			if (updateInput.hasOwnProperty(type)) {
				updateInput[type](input, val);
			}
		}
	}

	/**
	 * Обнуление значений формы
	 */
	function resetFilterForm() {
		var
			resetRadio = function resetRadio(nf, input) {
				var
					self = $(input),
					id = self.attr('id'),
					label = $filterBlock.find('label[for="'+id+'"]');

				self.removeAttr('checked');
				label.removeClass('mChecked');
				self.trigger('change');
			},

			resetCheckbox = function resetCheckbox(nf, input) {
				$(input).removeAttr('checked').trigger('change');
			},

			resetSliders = function resetSliders() {
				var sliderWrap = $(this),
					slider = sliderWrap.find('.js-category-filter-rangeSlider-slider'),
					sliderFromInput = sliderWrap.find('.js-category-filter-rangeSlider-from'),
					sliderToInput = sliderWrap.find('.js-category-filter-rangeSlider-to');

				sliderFromInput.val(slider.slider('option', 'min')).trigger('change');
				sliderToInput.val(slider.slider('option', 'max')).trigger('change');
			},
			resetText = function(nf, input) {
				$(input).val('').trigger('change');
			};

		$filterBlock.find(':input:radio:checked').each(resetRadio);
		$filterBlock.find(':input:checkbox:checked').each(resetCheckbox);
		$filterBlock.find(':input:text:not(.js-category-filter-rangeSlider-from):not(.js-category-filter-rangeSlider-to)').each(resetText);
		$filterSliders.each(resetSliders);
	}

	function sendFilter(page) {
		// SITE-4894 Не изменяются выбранные фильтры при переходе назад
		if (!updateOnChange) {
			return;
		}

		clearTimeout(timer);

		timer = setTimeout(function() {
			var url = getFilterUrl(page);

			// SITE-5063 Дублирование товаров в листинге
			$(window).off('scroll', checkInfinityScroll);

			if (url != document.location.pathname + document.location.search) {
				if (!History.enabled) {
					url = url.replace(/\#.*$|$/, '#productCatalog-filter-form');
				}

				goToUrl(url);

				// Устанавливаем фильтры в ссылки списка дочерних категорий
				$('.js-category-children-link').each(function(index, link) {
					var
						$link = $(link),
						hrefWithoutQueryString = $link.attr('href').indexOf('?') == -1 ? $link.attr('href') : $link.attr('href').slice(0, $link.attr('href').indexOf('?')),
						filterQueryString = url.slice(url.indexOf('?') + 1).replace(/(^|&)page=[^&]+/, '').replace(/^&/, '');

					if (filterQueryString != '') {
						filterQueryString = '?' + filterQueryString;
					}

					$link.attr('href', hrefWithoutQueryString + filterQueryString);
				});
			}
		}, 300);
	}

	function reloadFilter() {
		clearTimeout(timer);

		// SITE-5063 Дублирование товаров в листинге
		$(window).off('scroll', checkInfinityScroll);

		getDataFromServer(getFilterUrl().addParameterToUrl('ajax', 'true'), renderCatalogPage);
	}

	function toggleAdvancedFilters(openAnyway) {
		var open = $filterOtherParamsToggleButton.hasClass(filterOpenClass);

		if (open && !openAnyway) {
			$filterOtherParamsToggleButton.removeClass(filterOpenClass);
			$filterOtherParamsToggleButton.parent().removeClass('bFilterHead-open');
			$filterOtherParamsContent.slideUp(400);
		} else {
			$filterOtherParamsToggleButton.addClass(filterOpenClass);
			$filterOtherParamsToggleButton.parent().addClass('bFilterHead-open');
			$filterOtherParamsContent.slideDown(400);
		}
	}

	// Иницилизация слайдеров в фильтрах
	$filterSliders.each(function() {
		var sliderWrap = $(this),
			slider = sliderWrap.find('.js-category-filter-rangeSlider-slider'),
			sliderConfig = slider.data('config'),
			sliderFromInput = sliderWrap.find('.js-category-filter-rangeSlider-from'),
			sliderToInput = sliderWrap.find('.js-category-filter-rangeSlider-to');

		slider.slider({
			range: true,
			step: sliderConfig.step,
			min: sliderConfig.min,
			max: sliderConfig.max,
			values: [
				sliderFromInput.val(),
				sliderToInput.val()
			],

			slide: function(e, ui) {
				sliderFromInput.val(ui.values[0]);
				sliderToInput.val(ui.values[1]);

				sliderFromInput.trigger('change', [true]);
				sliderToInput.trigger('change', [true]);
			}
		});

		sliderFromInput.on('change', function(e, fromSliderChange) {
			var
				from = '0' + sliderFromInput.val(),
				to = '0' + sliderToInput.val(),
				min = slider.slider('option', 'min'),
				max = slider.slider('option', 'max');

			from = parseFloat(from);
			to = parseFloat(to);

			if (from < min) {
				from = min;
			} else if (from > max) {
				from = max;
			}

			if (from > to) {
				from = to;
			}

			sliderFromInput.val(from);

			if (!fromSliderChange) {
				slider.slider('values', 0, from);
			}

			if ((e.originalEvent || fromSliderChange) && sliderWrap.is($priceForFacetSearch)) {
				setManualDefinedPriceFrom(from, min);
			}
		});

		sliderToInput.on('change', function(e, fromSliderChange) {
			var
				from = '0' + sliderFromInput.val(),
				to = '0' + sliderToInput.val(),
				min = slider.slider('option', 'min'),
				max = slider.slider('option', 'max');

			from = parseFloat(from);
			to = parseFloat(to);

			if (to < min) {
				to = min;
			} else if (to > max) {
				to = max;
			}

			if (from > to) {
				to = from;
			}

			sliderToInput.val(to);

			if (!fromSliderChange) {
				slider.slider('values', 1, to);
			}

			if ((e.originalEvent || fromSliderChange) && sliderWrap.is($priceForFacetSearch)) {
				setManualDefinedPriceTo(to, max);
			}
		});
	});

    if ((true === ENTER.config.pageConfig.infinityScroll) && !docCookies.hasItem('infScroll')) {
        enableInfinityScroll();
    }
	enableInfinityScroll(true);

	// Обработчик изменения состояния истории в браузере
	$(window).on('statechange', function() {
		var
			state = History.getState(),
			url = state.url,
			data = state.data.data
		;

		// SITE-4941 Происходит редирект в начало страницы при нажатии на кнопку "назад" в браузере с примененными фильтрами
		setTimeout(function() {
			if (data.scrollTop) {
				$(window).scrollTop(data.scrollTop);
			}
		}, 0);

		// SITE-4894 Не изменяются выбранные фильтры при переходе назад
		if (backClick) {
			updateOnChange = false;
			resetFilterForm();
			updateFilterForm(ENTER.utils.parseUrlParams(url));
			updateOnChange = true;
		}

		getDataFromServer(url.addParameterToUrl('ajax', 'true'), renderCatalogPage);
		backClick = true;
	});

	// Обработчик кнопки "Бренды и параметры"
	$filterOtherParamsToggleButton.on('click', function(e) {
		e.preventDefault();
		toggleAdvancedFilters();
	});

	// Изменение значений фильтров
	$filterBlock.on('change', 'input, select, textarea', function() {
		sendFilter(1);
	});

	// Отправка формы
	$filterBlock.on('submit', function(e) {
		e.preventDefault();
		sendFilter(1);
	});

	// Обработчик кнопк сворачивания/разворачивания блоков
	$filterBlock.on('click', '.js-category-filter-toggle-button', function(e) {
		var
			$self = $(this),
			$button = $(e.currentTarget),
			$content = $('.js-category-filter-toggle-content', $button.closest('.js-category-filter-toggle-container'));

		if ($button.hasClass(filterOpenClass)) {
			$button.removeClass(filterOpenClass);
			$content.slideUp(400);
			$self.parent('.js-category-filter-toggle-container').addClass('fltrSet-close');
		} else {
			$button.addClass(filterOpenClass);
			$content.slideDown(400);
			$self.parent('.js-category-filter-toggle-container').removeClass('fltrSet-close');
		}

		return false;
	});

	// Обработчик выбора категории фильтра
	$filterMenuItem.on('click', function() {
		var
			$self = $(this),
			activeClass = 'mActive',
			categoryId = $self.data('ref');

		if ($self.hasClass(activeClass)) {
			return false;
		}

		$filterMenuItem.removeClass(activeClass);
		$self.addClass(activeClass);

		$filterCategoryBlocks.fadeOut(300);
		$filterCategoryBlocks.promise().done(function() {
			$('#'+categoryId).fadeIn(300);
		});

		if (!isV3) {
			$.scrollTo($filterBlock, 500);
		}

		return false;
	});

	// Обработка нажатий на ссылки удаления фильтров
	$body.on('click', '.js-category-filter-deleteLink', function(e) {
		var url = $(this).attr('href');

		e.preventDefault();

		resetFilterForm();
		updateFilterForm(ENTER.utils.parseUrlParams(url));
		goToUrl(url);
	});

	// Нажатие на кнопку "Подобрать"
	$('.js-category-v1-filter-submit').click(function() {
		$.scrollTo($filterBlock.find('.js-category-filter-selected'), 500);

		$body.trigger('trackGoogleEvent', {
			category: 'filter_old',
			action: 'find',
			label: catalogPath
		});
	});

	// Сортировка элементов
	$viewParamPanel.on('click', '.js-category-sorting-link', function(e) {
		var
			$self = $(this),
			activeClass = 'mActive',
			$parentItem = $self.parent();

		e.preventDefault();

		if ($parentItem.hasClass(activeClass) || $parentItem.hasClass('js-category-sorting-activeItem')) {
			return;
		}

		$viewParamPanel.find('.js-category-sorting-item').removeClass(activeClass).removeClass('act').removeClass('js-category-sorting-activeItem');
		$parentItem.addClass(activeClass).addClass('act').addClass('js-category-sorting-activeItem');
		sendFilter(1);
	});

	// Обработчик для ссылок смены отображения каталога
	$viewParamPanel.on('click', '.js-category-viewSwitcher-link', function(e) {
		var $viewLink = $(e.currentTarget);

		e.preventDefault();

		if ($viewLink.hasClass(viewSwitcherActiveClass)) {
			return;
		}

		$('.js-category-viewSwitcher-link').removeClass(viewSwitcherActiveClass);
		$viewLink.addClass(viewSwitcherActiveClass);

		if ($viewLink.hasClass('js-category-viewSwitcher-link-expanded')) {
			$listingWrap.addClass('listing');
			docCookies.setItem('categoryView', 'expanded', 4*7*24*60*60, '/');

			$body.trigger('trackGoogleEvent', {
				category: 'design_listing',
				action: 'change',
				label: 'список'
			});
		} else {
			$listingWrap.removeClass('listing');
			docCookies.setItem('categoryView', 'compact', 4*7*24*60*60, '/');
			
			$body.trigger('trackGoogleEvent', {
				category: 'design_listing',
				action: 'change',
				label: 'плитка'
			});
		}

		reloadFilter();
	});

	// Обработчик для ссылок переключения страниц
	$viewParamPanel.on('click', '.js-category-pagination-page-link', function(e) {
		var
			$self = $(this),
			$parentItem = $self.parent();

		e.preventDefault();

		if ($parentItem.hasClass('mActive') || $parentItem.hasClass('js-category-pagination-activePage')) {
			return;
		}

		sendFilter(ENTER.utils.getURLParam('page', $self.attr('href')) || 1);

		if ($filterBlock.length) {
			$.scrollTo($filterBlock, 500);
		}
	});

	// Обработчик для ссылки переключения на постраничную навигацию
	$viewParamPanel.on('click', '.js-category-pagination-paging-enableLink', function(e) {
		var
			infBtn = $viewParamPanel.find('.js-category-pagination-infinity'),
			isActiveTab = infBtn.hasClass('mActive');

		e.preventDefault();

		if (isActiveTab) {
			disableInfinityScroll();
		}
	});

	// Обработчик для ссылки переключения на бесконечную прокрутку
	$viewParamPanel.on('click', '.js-category-pagination-infinity-enableLink', function(e) {
		var
			infBtn = $viewParamPanel.find('.js-category-pagination-infinity'),
			isActiveTab = infBtn.hasClass('mActive');

		e.preventDefault();

		if (!isActiveTab) {
			enableInfinityScroll();
		}
	});

	// Фокус ввода на поля цены
	$('input', $priceFilter).focus(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_old',
			action: 'cost',
			label: catalogPath
		});
	});

	// Нажатие на слайдер цены
	$('.js-category-filter-rangeSlider-slider', $priceFilter).mousedown(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_old',
			action: 'cost',
			label: catalogPath
		});
	});

	// Нажатие на кнопку "Бренды и параметры"
	$('.js-category-v1-filter-otherParamsToggleButton').click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_old',
			action: 'brand_parameters',
			label: catalogPath
		});
	});

	// Нажатие на ссылки разделов фильтра
	$('.js-category-filter-param', $otherParams).click(function() {
		$body.trigger('trackGoogleEvent', {
			category: 'filter_old',
			action: 'using_brand_parameters',
			label: catalogPath
		});
	});

	// Использование элементов фильтра
	(function() {
		$('input[type="checkbox"], input[type="radio"]', $otherParams).click(function() {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'using_brand_parameters',
				label: catalogPath
			});
		});

		$('input[type="text"]', $otherParams).focus(function() {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'using_brand_parameters',
				label: catalogPath
			});
		});

		$('.js-category-filter-rangeSlider-slider', $otherParams).mousedown(function() {
			$body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'using_brand_parameters',
				label: catalogPath
			});
		});
	})();

	catalog.filter = {
		open: function() {
			toggleAdvancedFilters(true);

			$('.js-category-filter-toggle-container', $filterBlock).each(function() {
				$('.js-category-filter-toggle-button', this).addClass(filterOpenClass);
				$('.js-category-filter-toggle-content', this).slideDown(400);
			});
		},

		/**
		 * Отправка результатов фильтров
		 * Получение ответа от сервера
		 */
		send: function() {
			return sendFilter(1);
		}
	};

    // analytics
    $(window).on('scroll', function() {
        try {
            if (!loading && $('.js-category-pagination').last().visible()) {
                var categoryName, data;

                if (data = $('#jsProductCategory').data('value')) {
                    categoryName = data.name;
                } else if (data = $('#jsSlice').data('value')) {
                    categoryName = data.category ? data.category.name : '';
                }

                $('body').trigger('trackGoogleEvent', {
                    action: (docCookies.getItem('infScroll') != '1') ? 'not_upload' : 'upload',
                    category: 'listing_upload',
                    label: ('string' === typeof categoryName) ? categoryName : ''
                });
            }
        } catch (error) { console.info(error); }
    });

}());