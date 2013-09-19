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

	var pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),

		filterBlock = $('.bFilter'),

		filterToggleBtn = filterBlock.find('.bFilterToggle'),
		filterContent = filterBlock.find('.bFilterCont'),
		filterSliders = filterBlock.find('.bRangeSlider'),
		filterMenuItem = filterBlock.find('.bFilterParams__eItem'),
		filterCategoryBlocks = filterBlock.find('.bFilterValuesItem'),

		viewParamPanel = $('.bSortingLine'),

		sortingItemsBtns = viewParamPanel.find('.mSorting .mSortItem'),
		changeViewItemsBtns = viewParamPanel.find('.mViewer .mSortItem'),
		changePaginationBtns = viewParamPanel.find('.mViewer .mPager');
	// end of vars
	
	catalog.enableHistoryAPI = ( typeof Mustache === 'object' ) && ( History.enabled );

	console.info('Mustache is '+ typeof Mustache);
	console.info('enableHistoryAPI '+ catalog.enableHistoryAPI);
	
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
			return changeViewItemsBtns.filter('.mActive').data('type');
		},

		/**
		 * Отрисовка шаблона продуктов
		 * 
		 * @param	{Object}	res		Данные для шаблона
		 */
		renderCatalogPage: function( res ) {
			console.info('renderCatalogPage');
			console.log(( typeof catalog.filter.getViewType() !== 'undefined' ) ? catalog.filter.getViewType() : 'default');

			var templateType = ( typeof catalog.filter.getViewType() !== 'undefined' ) ? catalog.filter.getViewType() : 'default',
				template = {
					'compact': $('#listing_compact_tmpl'),
					'expanded': $('#listing_compact_tmpl'), // Заменить когда будет шаблон расширенного вида
					'default': $('#listing_compact_tmpl')
				},
				dataToRender = ( res ) ? res : catalog.filter.lastRes,
				listingTemplate = template[templateType].html(),
				partials = template[templateType].data('partial'),
				listingWrap = $('.bListing'),
				html;
			// end of vars

			html = Mustache.render(listingTemplate, dataToRender, partials);

			listingWrap.empty();
			listingWrap.html(html);

			catalog.filter.lastRes = dataToRender;
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
					slider = sliderWrap.find('.bFilterSlider'),
					sliderConfig = slider.data('config'),
					sliderFromInput = sliderWrap.find('.mFromRange'),
					sliderToInput = sliderWrap.find('.mToRange'),

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

		/**
		 * Формирование URL для получения результатов фильтра
		 * 
		 * @return	{String}	url
		 */
		getFilterUrl: function() {
			var formData = filterBlock.serializeArray(),
				url = filterBlock.attr('action'),
				slidersInputState = catalog.filter.getSlidersInputState(),
				formSerizalizeData;
			// end of vars

			for ( var i = formData.length - 1; i >= 0; i-- ) {
				if ( slidersInputState.unchangedSliders.indexOf(formData[i].name) !== -1 ) {
					console.log('slider input '+formData[i].name+' unchanged');

					formData.splice(i,1);
				}
			}

			formSerizalizeData = $.param( formData );

			if ( formSerizalizeData.length !== 0 ) {
				url += '?' + formSerizalizeData;
			}

			return url;
		},

		/**
		 * Изменение параметров фильтра
		 */
		changeFilterHandler: function() {
			console.info('change filter');
		},

		/**
		 * Отправка результатов фильтров
		 * Получение ответа от сервера
		 */
		sendFilter: function() {
			var url = catalog.filter.getFilterUrl();

			if ( url !== (document.location.pathname + document.location.search) ) {
				console.info('goto url '+url);

				catalog.history.gotoUrl(url, catalog.filter.renderCatalogPage);
			}

			return false;
		},

		/**
		 * Обновление значений фильтра
		 */
		updateFilter: function() {
			console.info('update filter');
		}
	};



		/**
		 * Обработчик кнопки переключения между расширенным и компактным видом фильтра
		 */
	var toggleFilterViewHandler = function toggleFilterViewHandler() {
			var openClass = 'mOpen',
				closeClass = 'mClose',
				open = filterToggleBtn.hasClass(openClass);
			// end of vars

			if ( open ) {
				filterToggleBtn.removeClass(openClass).addClass(closeClass);
				filterContent.slideUp(400);
			}
			else {
				filterToggleBtn.removeClass(closeClass).addClass(openClass);
				filterContent.slideDown(400);
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

			return false;
		},

		/**
		 * Слайдеры в фильтре
		 */
		initSliderRange = function initSliderRange() {
			var sliderWrap = $(this),
				slider = sliderWrap.find('.bFilterSlider'),
				sliderConfig = slider.data('config'),
				sliderFromInput = sliderWrap.find('.mFromRange'),
				sliderToInput = sliderWrap.find('.mToRange'),

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

					filterBlock.trigger('change');
				}
			});

			var inputUpdates = function inputUpdates() {
				var val = '0' + $(this).val();

				val = parseInt(val, 10);
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
		 * Смена отображения каталога
		 */
		changeViewItemsHandler = function changeViewItemsHandler() {
			var self = $(this),
				url = self.attr('href'),
				parentItem = self.parent();
			// end of vars
			
			changeViewItemsBtns.removeClass('mActive');
			parentItem.addClass('mActive');

			if ( catalog.filter.lastRes ) {
				catalog.history.gotoUrl(url, catalog.filter.renderCatalogPage, true);
			}
			else {
				catalog.history.gotoUrl(url, catalog.filter.renderCatalogPage);
			}

			return false;
		},
	
		/**
		 * Сортировка элементов
		 */
		sortingItemsHandler = function sortingItemsHandler() {
			var self = $(this),
				url = self.attr('href'),
				parentItem = self.parent();
			// end of vars
			 
			sortingItemsBtns.removeClass('mActive');
			parentItem.addClass('mActive');
			catalog.history.gotoUrl(url, catalog.filter.renderCatalogPage);

			return false;
		};
	// end of functions


	// Handlers
	filterToggleBtn.on('click', toggleFilterViewHandler);
	filterMenuItem.on('click', selectFilterCategoryHandler);
	filterBlock.on('change', catalog.filter.changeFilterHandler);
	filterBlock.on('submit', catalog.filter.sendFilter);

	// Sorting items
	sortingItemsBtns.on('click', '.bSortingList__eLink', sortingItemsHandler);

	// Change view mode
	changeViewItemsBtns.on('click', '.bSortingList__eLink', changeViewItemsHandler)
	
	// Init sliders
	filterSliders.each(initSliderRange);

}(window.ENTER));