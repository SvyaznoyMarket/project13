/**
 * Filters
 *
 * @requires jQuery, Mustache, ENTER.utils, ENTER.config, ENTER.catalog.history
 *
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	console.info('New catalog init: filter.js');

	var
		body = $('body'),
		utils = ENTER.utils,
		catalogPath = document.location.pathname.replace(/^\/catalog\/([^\/]*).*$/i, '$1'), // Используем значение URL адреса на момент загрузки страницы, т.к. на данный момент при выполнении поиска URL страницы изменяется на URL формы, в которой задан URL из метода http://admin.enter.ru/v2/category/get-seo (в котором содержится некорректный URL; без средней части - "/catalog/holodilniki-i-morozilniki-1096" вместо "/catalog/appliances/holodilniki-i-morozilniki-1096")
		catalog = utils.extendApp('ENTER.catalog'),

		filterBlock = $('.js-category-filter'),
		isV3 = filterBlock.hasClass('js-category-filter-v3'),

		filterOtherParamsToggleButton = filterBlock.find('.js-category-filter-otherParamsToggleButton'),
		filterOtherParamsContent = filterBlock.find('.js-category-filter-otherParamsContent'),
		filterSliders = filterBlock.find('.js-category-filter-rangeSlider'),
		filterNumbers = filterBlock.find('.js-category-v2-filter-element-number input'),
		filterMenuItem = filterBlock.find('.js-category-filter-param'),
		filterCategoryBlocks = filterBlock.find('.js-category-filter-element'),
		$priceFilter = $('.js-category-v1-filter-element-price'),
		$priceForFacetSearch = $('.js-gift-category-filter-element-price'),
		$otherParams = $('.js-category-v1-filter-otherParams'),

		viewParamPanel = $('.js-category-sortingAndPagination'),
		filterOpenClass = 'fltrSet_tggl-dn',

		manualDefinedPriceFrom = utils.getURLParam('f-price-from', document.location.href),
		manualDefinedPriceTo = utils.getURLParam('f-price-to', document.location.href),

		tID;
	// end of vars

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

	catalog.filter = {
		/**
		 * Выполнять ли обновление фильтров при изменении свойства
		 *
		 * @type	{boolean}
		 */
		updateOnChange: true,

		/**
		 * Последние загруженные данные
		 *
		 * @type	{Object}
		 */
		lastRes: null,

		/**
		 * Получение текущего режима просмотра
		 *
		 * @return	{String}	Текущий режим просмотра
		 */
		getViewType: function() {
			var changeViewItemsBtns = viewParamPanel.find('.js-category-viewer .js-category-viewer-item');

			return changeViewItemsBtns.filter('.js-category-viewer-activeItem').data('type');
		},

		applyTemplate: {
			list: function( html ) {
				console.info('applyTemplate list');
				catalog.listingWrap.empty();
				catalog.listingWrap.html(html);
			},

			selectedFilter: function( html ) {
				var filterFooterWrap = filterBlock.find('.js-category-filter-selected'); // TODO

				filterFooterWrap.empty();
				filterFooterWrap.html(html);
			},

			sorting: function( html ) {
				var sortingWrap = viewParamPanel.find('.js-category-sorting');

				sortingWrap.empty();
				sortingWrap.html(html);
			},

			pagination: function( html ) {
				var paginationWrap = $('.js-category-pagination');

				paginationWrap.empty();
				paginationWrap.html(html);
			},

			page: function( html ) {
				var title = $('.js-pageTitle');

				title.empty();
				title.html(html);
			},

			countProducts: function ( html ) {
				var
					subminBtn = $('.js-category-filter-submit', '.js-category-filter'),
					count = html ? parseInt(html) : -1;

				if ( count >= 0 && subminBtn.length ) {
					subminBtn.text('Подобрать (' +  html + ')');
				}
			}
		},


		render: {

			list: function( data ) {
				console.info('render list');
				console.log(( typeof catalog.filter.getViewType() !== 'undefined' ) ? catalog.filter.getViewType() : 'default');

				var templateType = ( typeof catalog.filter.getViewType() !== 'undefined' ) ? catalog.filter.getViewType() : 'default',
					template = {
						'compact': $('#listing_compact_tmpl'),
						'expanded': $('#listing_compact_tmpl'), // Заменить когда будет шаблон расширенного вида
						'default': $('#listing_compact_tmpl')
					},
					listingTemplate = template[templateType].html(),
					partials = template[templateType].data('partial'),
					html;
				// end of vars

				html = Mustache.render(listingTemplate, data, partials);

				console.log('end of render list');

				return html;
			},

			selectedFilter: function( data ) {
				console.info('render selectedFilter');

				if ( !data ) {
					console.warn('nothing to render');

					return;
				}

				var template = $('#tplSelectedFilter'),
					filterTemplate = template.html(),
					partials = template.data('partial'),
					html;
				// end of vars

				html = Mustache.render(filterTemplate, data, partials);

				if ( data.hasOwnProperty('values') ) {
					console.info('run update filter!');

					// SITE-4825
//					catalog.filter.updateFilter( data['values'] );
				}

				console.log('end of render selectedFilter');

				return html;
			},

			sorting: function( data ) {
				console.info('render sorting');

				var
					template = $('script.tplSorting:first'),
					sortingTemplate = template.html(),
					partials = template.data('partial'),
					html;
				// end of vars

				html = Mustache.render(sortingTemplate, data, partials);

				console.log('end of render sorting');

				return html;
			},

			pagination: function( data ) {
				console.info('render pagination');

				var
					template = $('script.tplPagination:first'),
					paginationTemplate = template.html(),
					partials = template.data('partial'),
					html;
				// end of vars

				html = Mustache.render(paginationTemplate, data, partials);

				console.log('end of render paginaton');

				return html;
			},

			page: function( data ) {
				var title = data.title;

				return title;
			},

			countProducts: function ( data ) {
				console.info('render countProducts');
				return data;
			}
		},

		/**
		 * Отрисовка шаблона продуктов
		 *
		 * @param	{Object}	res		Данные для шаблона
		 */
		renderCatalogPage: function( res ) {
			console.info('renderCatalogPage');

			var dataToRender = ( res ) ? res : catalog.filter.lastRes,
				key,
				template,
				lastPage = res['pagination'] ? res['pagination']['lastPage'] : false;
			// end of vars

			// SITE-4825
//			catalog.filter.resetForm();

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

			for ( key in dataToRender ) {
				if ( catalog.filter.render.hasOwnProperty(key) ) {
					template = catalog.filter.render[key]( dataToRender[key] );
				}

				if ( catalog.filter.applyTemplate.hasOwnProperty(key) ) {
					catalog.filter.applyTemplate[key](template);
				}
			}

			if ( lastPage ) {
				catalog.lastPage = lastPage;
			}

			catalog.filter.lastRes = dataToRender;

			catalog.infScroll.checkInfinity();
		},

		/**
		 * Получение изменненых и неизменненых полей слайдеров
		 *
		 * @return	{Object}	res
		 * @return	{Array}		res.changedSliders		Массив имен измененных полей
		 * @return	{Array}		res.unchangedSliders	Массив имен неизмененных полей
		 */
		getSlidersInputState: function() {
			console.info('getSlidersInputState');

			var res = {
					changedSliders: [],
					unchangedSliders: []
				};
			// end of vars

			var sortSliders = function sortSliders() {
				var sliderWrap = $(this),
					slider = sliderWrap.find('.js-category-filter-rangeSlider-slider'),
					sliderFromInput = sliderWrap.find('.js-category-filter-rangeSlider-from'),
					sliderToInput = sliderWrap.find('.js-category-filter-rangeSlider-to');


				if ( sliderFromInput.val() * 1 == slider.slider('option', 'min') ) {
					res.unchangedSliders.push(sliderFromInput.attr('name'));
				}
				else {
					res.changedSliders.push(sliderFromInput.attr('name'));
				}

				if ( sliderToInput.val() * 1 == slider.slider('option', 'max') ) {
					res.unchangedSliders.push(sliderToInput.attr('name'));
				}
				else {
					res.changedSliders.push(sliderToInput.attr('name'));
				}
			};

			filterSliders.each(sortSliders);

			return res;
		},

		getUnchangedNumberFieldNames: function() {
			var unchangedNumbers = [];
			filterNumbers.each(function(index, input) {
				var $input = $(input);
				// В IE <= 9 класс placeholder добавляется к полям, в которых введено значение, которое не надо передавать на сервер
				if ($input.hasClass('placeholder')) {
					unchangedNumbers.push(input.name);
				}
			});

			return unchangedNumbers;
		},

		/**
		 * Формирование URL для получения результатов фильтра
		 *
		 * @return	{String}	url
		 */
		getFilterUrl: function(page) {
			console.info('getFilterUrl');

			var formData = filterBlock.serializeArray(),
				url = filterBlock.attr('action') || '',
				slidersInputState = catalog.filter.getSlidersInputState(),
				unchangedNumberFieldNames = catalog.filter.getUnchangedNumberFieldNames(),
				activeSort = viewParamPanel.find('.js-category-sorting-activeItem:not(.js-category-sorting-defaultItem)').find('.jsSorting'),
				sortUrl = activeSort.length ? activeSort.data('sort') : null,
				formSerizalizeData,
				urlParams = catalog.filter.getUrlParams(),
				hasCategory = false;
			// end of vars

			for ( var i = formData.length - 1; i >= 0; i-- ) {
				if ( slidersInputState.unchangedSliders.indexOf(formData[i].name) !== -1 || unchangedNumberFieldNames.indexOf(formData[i].name) != -1 || formData[i].value == '') {
					console.log('slider input '+formData[i].name+' unchanged');

					formData.splice(i,1);
				}
			}

			// передаем категрию, если она была задана
			if ( urlParams && urlParams['category'] ) {
				$.each(formData, function () {
					if ( this.name == 'category' ) {
						hasCategory = true;
					}
				});

				// добавляем категорию в параметры, если она не добавлена
				if ( !hasCategory ) {
					formData.unshift({name: 'category', value: urlParams['category']});
				}
			}

			formSerizalizeData = $.param( formData );

			if ( formSerizalizeData.length !== 0 ) {
				url += ( url.indexOf('?') === -1 ) ? '?' + formSerizalizeData : '&' + formSerizalizeData;
			}

			console.log(url);

			if (sortUrl) {
				url = url.addParameterToUrl('sort', sortUrl);
			}

			if (page && page > 1 && (!catalog.lastPage || page <= catalog.lastPage)) {
				url = url.addParameterToUrl('page', page);
			}

			return url;
		},

		/**
		 * Получение get параметров текущей страницы
		 */
		getUrlParams: function () {
			var $_GET = {},
				__GET = window.location.search.substring(1).split('&'),
				getVar,
				i;
			// end of vars

			for ( i = 0; i < __GET.length; i++ ) {
				getVar = __GET[i].split('=');
				$_GET[getVar[0]] = typeof(getVar[1]) == 'undefined' ? '' : getVar[1];
			}

			return $_GET;
		},

		/**
		 * Изменение параметров фильтра
		 */
		changeFilterHandler: function(page) {
			// SITE-4894 Не изменяются выбранные фильтры при переходе назад
			if (!catalog.filter.updateOnChange) {
				return;
			}

			clearTimeout(tID);

			tID = setTimeout(function() {
				catalog.filter.sendFilter(page);
			}, 300);
		},

		/**
		 * Отправка результатов фильтров
		 * Получение ответа от сервера
		 */
		sendFilter: function(page) {
			var url = catalog.filter.getFilterUrl(page);

			if ( url !== (document.location.pathname + document.location.search) ) {
				console.info('goto url '+url);

				if ( !catalog.enableHistoryAPI ) {
					url = url.replace(/\#.*$|$/, '#productCatalog-filter-form');
				}

				// SITE-5063 Дублирование товаров в листинге
				$(window).off('scroll', catalog.infScroll.checkScroll);
				catalog.history.gotoUrl(url);

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
		},

		/**
		 * Обнуление значений формы
		 */
		resetForm: function() {
			console.info('resetForm');
			// return;

			var resetRadio = function resetRadio( nf, input ) {
					var self = $(input),
						id = self.attr('id'),
						label = filterBlock.find('label[for="'+id+'"]');
					// end of vars

					console.info(id);
					console.info(label);

					self.removeAttr('checked');
					label.removeClass('mChecked');
					self.trigger('change');
				},

				resetCheckbox = function resetCheckbox( nf, input ) {
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
				resetText = function( nf, input ) {
					$(input).val('').trigger('change');
				};
			// end of functions

			filterBlock.find(':input:radio:checked').each(resetRadio);
			filterBlock.find(':input:checkbox:checked').each(resetCheckbox);
			filterBlock.find(':input:text:not(.js-category-filter-rangeSlider-from):not(.js-category-filter-rangeSlider-to)').each(resetText);
			filterSliders.each(resetSliders);
		},

		/**
		 * Обновление значений фильтра
		 */
		updateFilter: function( values ) {
			console.info('update filter');

			var input,
				val,
				type,
				fieldName;
			// end of vars

			console.info(values);

			var updateInput = {
				'text': function( input, val ) {
					input.val(val).trigger('change');
				},

				'radio': function( input, val ) {
					var self = input.filter('[value="'+val+'"]'),
						id = self.attr('id'),
						label = filterBlock.find('label[for="'+id+'"]');
					// end of vars

					self.attr('checked', 'checked');
					label.addClass('mChecked');
					self.trigger('change');
				},

				'checkbox': function( input, val ) {
					input.filter('[value="'+val+'"]').attr('checked', 'checked').trigger('change');
				}
			};

			for ( fieldName in values ) {
				if ( !values.hasOwnProperty(fieldName) ) {
					return;
				}

				input = filterBlock.find('input[name="'+fieldName+'"]');
				val = values[fieldName];
				type = input.attr('type');

				console.log(input);
				console.log(val);
				console.log(type);

				if ( updateInput.hasOwnProperty(type) ) {
					updateInput[type](input, val);
				}
			}
		},

		openFilter: function() {
			toggleFilterViewHandler( true );

			$('.js-category-filter-toggle-container', filterBlock).each(function() {
				$('.js-category-filter-toggle-button', this).addClass(filterOpenClass);
				$('.js-category-filter-toggle-content', this).slideDown(400);
			});
		}
	};

	/**
	 * Слайдеры в фильтре
	 */
	var initSliderRange = function() {
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

				slide: function( e, ui ) {
					sliderFromInput.val( ui.values[ 0 ] );
					sliderToInput.val( ui.values[ 1 ] );

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
		},

		/**
		 * Обработка нажатий на ссылки удаления фильтров
		 */
		jsHistoryLinkHandler = function(e) {
			var self = $(this),
				url = self.attr('href');
			// end of vars

			e.preventDefault();

			catalog.filter.resetForm();
			catalog.filter.updateFilter(utils.parseUrlParams(url));
			catalog.history.gotoUrl(url);
		},

		/**
		 * Обработчик кнопки для отображения расширенных фильтров
		 */
		toggleFilterViewHandler = function( openAnyway ) {
			var open = filterOtherParamsToggleButton.hasClass(filterOpenClass);
			// end of vars

			if ( open && typeof openAnyway !== 'boolean' ) {
				filterOtherParamsToggleButton.removeClass(filterOpenClass);
				filterOtherParamsToggleButton.parent().removeClass('bFilterHead-open');
				filterOtherParamsContent.slideUp(400);
			}
			else {
				filterOtherParamsToggleButton.addClass(filterOpenClass);
				filterOtherParamsToggleButton.parent().addClass('bFilterHead-open');
				filterOtherParamsContent.slideDown(400);
			}

			return false;
		},

		/**
		 * Обработчик кнопк сворачивания/разворачивания блоков
		 */
		toggleHandler = function(e) {
			var $self = $(this),
				$button = $(e.currentTarget),
				$container = $('.js-category-filter-toggle-container'),
				$content = $('.js-category-filter-toggle-content', $button.closest('.js-category-filter-toggle-container'));
			// end of vars

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
		},

		/**
		 * Обработка пагинации
		 */
		jsPaginationLinkHandler = function(e) {
			var self = $(this),
				page = utils.getURLParam('page', self.attr('href')),
				activeClass = 'mActive',
				parentItem = self.parent();
			// end of vars

			e.preventDefault();

			if (parentItem.hasClass(activeClass) || parentItem.hasClass('js-category-pagination-activePage')) {
				return;
			}

			catalog.filter.changeFilterHandler(page);

			if ( filterBlock.length ) {
				$.scrollTo(filterBlock, 500);
			}
		},

		/**
		 * Обработчик выбора категории фильтра
		 */
		selectFilterCategoryHandler = function() {
			var self = $(this),
				activeClass = 'mActive',
				categoryId = self.data('ref');
			// end of vars

			if ( self.hasClass(activeClass) ) {
				return false;
			}

			filterMenuItem.removeClass(activeClass);
			self.addClass(activeClass);

			filterCategoryBlocks.fadeOut(300);
			filterCategoryBlocks.promise().done(function() {
				$('#'+categoryId).fadeIn(300);
			});

			if (!isV3) {
				$.scrollTo(filterBlock, 500);
			}

			return false;
		},


		/**
		 * Смена отображения каталога
		 */
		changeViewItemsHandler = function(e) {
			var self = $(this),
				url = self.attr('href'),
				activeClass = 'mActive',
				parentItem = self.parent(),
				changeViewItemsBtns = viewParamPanel.find('.js-category-viewer .js-category-viewer-item'),
				isActiveTab = parentItem.hasClass(activeClass);
			// end of vars

			e.preventDefault();

			if ( isActiveTab ) {
				return;
			}

			changeViewItemsBtns.removeClass(activeClass);
			parentItem.addClass(activeClass);

			if ( catalog.filter.lastRes ) {
				catalog.history.updateUrl(url);
			}
			else {
				catalog.history.gotoUrl(url);
			}
		},


		/**
		 * Сортировка элементов
		 */
		sortingItemsHandler = function(e) {
			var self = $(this),
				activeClass = 'mActive',
				parentItem = self.parent();
			// end of vars

			e.preventDefault();

			if (parentItem.hasClass(activeClass) || parentItem.hasClass('js-category-sorting-activeItem')) {
				return;
			}

			viewParamPanel.find('.js-category-sorting-item').removeClass(activeClass).removeClass('act').removeClass('js-category-sorting-activeItem');
			parentItem.addClass(activeClass).addClass('act').addClass('js-category-sorting-activeItem');
			catalog.filter.changeFilterHandler();
		};
	// end of functions


	// Фокус ввода на поля цены
	$('input', $priceFilter).focus(function() {
		body.trigger('trackGoogleEvent', {
			category: 'filter_old',
			action: 'cost',
			label: catalogPath
		});
	});

	// Нажатие на слайдер цены
	$('.js-category-filter-rangeSlider-slider', $priceFilter).mousedown(function() {
		body.trigger('trackGoogleEvent', {
			category: 'filter_old',
			action: 'cost',
			label: catalogPath
		});
	});

	// Нажатие на кнопку "Бренды и параметры"
	$('.js-category-v1-filter-otherParamsToggleButton').click(function() {
		body.trigger('trackGoogleEvent', {
			category: 'filter_old',
			action: 'brand_parameters',
			label: catalogPath
		});
	});

	// Нажатие на ссылки разделов фильтра
	$('.js-category-filter-param', $otherParams).click(function() {
		body.trigger('trackGoogleEvent', {
			category: 'filter_old',
			action: 'using_brand_parameters',
			label: catalogPath
		});
	});

	// Использование элементов фильтра
	(function() {
		$('input[type="checkbox"], input[type="radio"]', $otherParams).click(function() {
			body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'using_brand_parameters',
				label: catalogPath
			});
		});

		$('input[type="text"]', $otherParams).focus(function() {
			body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'using_brand_parameters',
				label: catalogPath
			});
		});

		$('.js-category-filter-rangeSlider-slider', $otherParams).mousedown(function() {
			body.trigger('trackGoogleEvent', {
				category: 'filter_old',
				action: 'using_brand_parameters',
				label: catalogPath
			});
		});
	})();

	// Нажатие на кнопку "Подобрать"
	$('.js-category-v1-filter-submit').click(function() {
		$.scrollTo(filterBlock.find('.js-category-filter-selected'), 500);

		body.trigger('trackGoogleEvent', {
			category: 'filter_old',
			action: 'find',
			label: catalogPath
		});
	});

	// Handlers
	filterBlock.on('click', '.js-category-filter-toggle-button', toggleHandler);
	filterOtherParamsToggleButton.on('click', toggleFilterViewHandler);
	filterMenuItem.on('click', selectFilterCategoryHandler);
	filterBlock.on('change', 'input, select, textarea', catalog.filter.changeFilterHandler);
	filterBlock.on('submit', function(e) {
		e.preventDefault();
		catalog.filter.sendFilter();
	});

	// Sorting items
	viewParamPanel.on('click', '.jsSorting', sortingItemsHandler);

	// Change view mode
	viewParamPanel.on('click', '.jsChangeView', changeViewItemsHandler);

	// Pagination
	viewParamPanel.on('click', '.jsPagination', jsPaginationLinkHandler);

	// Other HistoryAPI link
	body.on('click', '.jsHistoryLink', jsHistoryLinkHandler);

	// Init sliders
	filterSliders.each(initSliderRange);

}(window.ENTER));