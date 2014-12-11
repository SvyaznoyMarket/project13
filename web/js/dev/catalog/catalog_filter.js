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
		catalogPath = document.location.pathname.replace(/^\/catalog\/([^\/]*).*$/i, '$1'),
		catalog = utils.extendApp('ENTER.catalog'),

		filterBlock = $('.js-category-filter'),
		hasAlwaysShowFilters = filterBlock.hasClass('js-category-filter-hasAlwaysShowFilters'),

		filterOtherParamsToggleButton = filterBlock.find('.js-category-filter-otherParamsToggleButton'),
		filterOtherParamsContent = filterBlock.find('.js-category-filter-otherParamsContent'),
		filterSliders = filterBlock.find('.js-category-filter-rangeSlider'),
		filterNumbers = filterBlock.find('.js-category-v2-filter-element-number input'),
		filterMenuItem = filterBlock.find('.js-category-filter-param'),
		filterCategoryBlocks = filterBlock.find('.js-category-filter-element'),
		$priceFilter = $('.js-category-v1-filter-element-price'),
		$otherParams = $('.js-category-v1-filter-otherParams'),

		viewParamPanel = $('.js-category-sortingAndPagination'),
		filterOpenClass = 'fltrSet_tggl-dn',

		tID;
	// end of vars

	catalog.filter = {
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
					sliderConfig = slider.data('config'),
					sliderFromInput = sliderWrap.find('.js-category-filter-rangeSlider-from'),
					sliderToInput = sliderWrap.find('.js-category-filter-rangeSlider-to'),

					min = sliderConfig.min,
					max = sliderConfig.max;
				// end of vars


				if ( sliderFromInput.val() * 1 === min ) {
					res.unchangedSliders.push(sliderFromInput.attr('name'));
				}
				else {
					res.changedSliders.push(sliderFromInput.attr('name'));
				}

				if ( sliderToInput.val() * 1 === max ) {
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
		getFilterUrl: function() {
			console.info('getFilterUrl');

			var formData = filterBlock.serializeArray(),
				url = filterBlock.attr('action') || '',
				slidersInputState = catalog.filter.getSlidersInputState(),
				unchangedNumberFieldNames = catalog.filter.getUnchangedNumberFieldNames(),
				activeSort = viewParamPanel.find('.js-category-sorting-activeItem').find('.jsSorting'),
				sortUrl = activeSort.data('sort'),
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

			url = url.addParameterToUrl('sort', sortUrl);

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
		changeFilterHandler: function( e ) {
			console.info('change filter');
			console.log(e);

			var sendUpdate = function sendUpdate() {
				filterBlock.trigger('submit');
			};

			console.info('need update from server...');

			clearTimeout(tID);

			tID = setTimeout(sendUpdate, 300);
		},

		/**
		 * Отправка результатов фильтров
		 * Получение ответа от сервера
		 */
		sendFilter: function( e ) {
			console.info('sendFilter');
			console.log(e);

			var url = catalog.filter.getFilterUrl();

			if ( url !== (document.location.pathname + document.location.search) ) {
				console.info('goto url '+url);

				if ( !catalog.enableHistoryAPI ) {
					url = url.replace(/\#.*$|$/, '#productCatalog-filter-form');
				}

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

			return false;
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
						sliderConfig = slider.data('config'),
						sliderFromInput = sliderWrap.find('.js-category-filter-rangeSlider-from'),
						sliderToInput = sliderWrap.find('.js-category-filter-rangeSlider-to'),

						min = sliderConfig.min,
						max = sliderConfig.max;
					// end of vars

					sliderFromInput.val(min).trigger('change');
					sliderToInput.val(max).trigger('change');
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
	var initSliderRange = function initSliderRange() {
			var sliderWrap = $(this),
				slider = sliderWrap.find('.js-category-filter-rangeSlider-slider'),
				sliderConfig = slider.data('config'),
				sliderFromInput = sliderWrap.find('.js-category-filter-rangeSlider-from'),
				sliderToInput = sliderWrap.find('.js-category-filter-rangeSlider-to'),

				min = sliderConfig.min,
				max = sliderConfig.max,
				step = sliderConfig.step;
			// end of vars

			slider.slider({
				range: true,
				step: step,
				min: min,
				max: max,
				values: [
					sliderFromInput.val(),
					sliderToInput.val()
				],

				slide: function( e, ui ) {
					sliderFromInput.val( ui.values[ 0 ] );
					sliderToInput.val( ui.values[ 1 ] );
				},

				change: function( e, ui ) {
					console.log('change slider');

					if ( e.originalEvent ) {
						sliderFromInput.trigger('change');
						sliderToInput.trigger('change');
					}
				}
			});

			var inputUpdates = function inputUpdates() {
				var val = '0' + $(this).val();

				val = parseFloat(val);
				console.info('inputUpdates');
				console.log(val);
				val =
					( val > max ) ? max :
					( val < min ) ? min :
					val;

				$(this).val(val);

				slider.slider({
					values: [
						sliderFromInput.val(),
						sliderToInput.val()
					]
				});
			};

			sliderToInput.on('change', inputUpdates);
			sliderFromInput.on('change', inputUpdates);
		},

		/**
		 * Обработка нажатий на ссылки завязанные на живую подгрузку данных
		 */
		jsHistoryLinkHandler = function jsHistoryLinkHandler() {
			var self = $(this),
				url = self.attr('href');
			// end of vars

			catalog.filter.resetForm();
			catalog.filter.updateFilter(parseUrlParams(url));
			catalog.history.gotoUrl(url);

			return false;
		},

		parseUrlParams = function(url) {
			var
				result = {},
				params = url.replace(/^[^?]*\?|\#.*$/g, '').split('&');

			for (var i = 0; i < params.length; i++) {
				var param = params[i].split('=');

				if (!param[0]) {
					param[0] = '';
				}

				if (!param[1]) {
					param[1] = '';
				}

				param[0] = decodeURIComponent(param[0]);
				param[1] = decodeURIComponent(param[1]);

				result[param[0]] = param[1];
			}

			return result;
		},

		/**
		 * Обработчик кнопки переключения между расширенным и компактным видом фильтра
		 */
		toggleFilterViewHandler = function toggleFilterViewHandler( openAnyway ) {
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
		jsPaginationLinkHandler = function jsPaginationLinkHandler() {
			var self = $(this),
				url = self.attr('href'),
				activeClass = 'mActive',
				parentItem = self.parent(),
				isActiveTab = parentItem.hasClass(activeClass);
			// end of vars

			if ( isActiveTab ) {
				return false;
			}

			catalog.history.gotoUrl(url);

			if ( filterBlock.length ) {
				$.scrollTo(filterBlock, 500);
			}

			return false;
		},

		/**
		 * Обработчик выбора категории фильтра
		 */
		selectFilterCategoryHandler = function selectFilterCategoryHandler() {
			var self = $(this),
				activeClass = 'mActive',
				isActiveTab = self.hasClass(activeClass),
				categoryId = self.data('ref');
			// end of vars

			if ( isActiveTab ) {
				return false;
			}

			filterMenuItem.removeClass(activeClass);
			self.addClass(activeClass);

			filterCategoryBlocks.fadeOut(300);
			filterCategoryBlocks.promise().done(function() {
				$('#'+categoryId).fadeIn(300);
			});

			if (!hasAlwaysShowFilters) {
				$.scrollTo(filterBlock, 500);
			}

			return false;
		},


		/**
		 * Смена отображения каталога
		 */
		changeViewItemsHandler = function changeViewItemsHandler() {
			var self = $(this),
				url = self.attr('href'),
				activeClass = 'mActive',
				parentItem = self.parent(),
				changeViewItemsBtns = viewParamPanel.find('.js-category-viewer .js-category-viewer-item'),
				isActiveTab = parentItem.hasClass(activeClass);
			// end of vars

			if ( isActiveTab ) {
				return false;
			}

			changeViewItemsBtns.removeClass(activeClass);
			parentItem.addClass(activeClass);

			if ( catalog.filter.lastRes ) {
				catalog.history.updateUrl(url);
			}
			else {
				catalog.history.gotoUrl(url);
			}

			return false;
		},


		/**
		 * Сортировка элементов
		 */
		sortingItemsHandler = function sortingItemsHandler() {
			var self = $(this),
				url = self.attr('href'),
				activeClass = 'mActive',
				parentItem = self.parent(),
				sortingItemsBtns = viewParamPanel.find('.js-category-sorting-item'),
				isActiveTab = parentItem.hasClass(activeClass);
			// end of vars

			if ( isActiveTab ) {
				return false;
			}

			sortingItemsBtns.removeClass(activeClass);
			parentItem.addClass(activeClass);
			catalog.history.gotoUrl(url);

			return false;
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
	$('input, select, textarea', filterBlock).on('change', catalog.filter.changeFilterHandler);
	filterBlock.on('submit', catalog.filter.sendFilter);

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