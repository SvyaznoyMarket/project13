/**
 * Работа с HISTORY API
 *
 * @requires	jQuery, History.js
 *
 * @author		Zaytsev Alexandr
 *
 * @param		{Object}	global	Enter namespace
 */
;(function( ENTER ) {
	var pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog');
	// end of vars

	console.info('New catalog history module');

	catalog.history = {
		/**
		 * Флаг обновления данных с сервера
		 * true - только обновить URL, false - запросить новые данные с сервера
		 * 
		 * @type {Boolean}
		 */
		_onlychange: false,

		/**
		 * Функция обратного вызова после получения данных с сервера
		 * 
		 * @type	{Function}
		 */
		_callback: null,


		/**
		 * Обработка перехода на URL
		 * Если браузер не поддерживает History API происходит обычный переход по ссылке
		 * 
		 * @param	{String}	url			Адрес на который необходимо выполнить переход
		 * @param	{Function}	callback	Функция которая будет вызвана после получения данных от сервера
		 * @param	{Boolean}	onlychange	Показывает что необходимо только изменить URL и не запрашивать данные
		 */
		gotoUrl: function gotoUrl( url, callback, onlychange ) {
			var state = {
					title: document.title,
					url: url
				};
			// end of vars

			catalog.history._callback = callback;
			catalog.history._onlychange = ( onlychange ) ? true : false;

			if ( !catalog.enableHistoryAPI ) {
				document.location.href = url;

				return;
			}

			console.info('link handler. push state new url: '+state.url);
			History.pushState(state, state.title, state.url);

			return;
		}
	};


		/**
		 * Обработка ошибки загрузки данных
		 */
	var errorHandler = function errorHandler() {
			utils.blockScreen.unblock();
		},

		/**
		 * Получение данных от сервера
		 * Перенаправление данных в функцию обратного вызова
		 * 
		 * @param	{Object}	res	Полученные данные
		 */
		resHandler = function resHandler( res ) {
			console.info('resHandler');

			if ( typeof res === 'object' && typeof catalog.history._callback === 'function' ) {
				catalog.history._callback(res);
				catalog.history._callback = null;
			}
			else {
				console.warn('res isn\'t object or catalog.history._callback isn\'t function');
				console.log(typeof res);
				console.log(typeof catalog.history._callback);
			}

			utils.blockScreen.unblock();
		},

		/**
		 * Запросить новые данные с сервера по url
		 * 
		 * @param	{String}	url
		 */
		getDataFromServer = function getDataFromServer( url ) {
			console.info('getDataFromServer ' + url);
			
			utils.blockScreen.block('Загрузка товаров');

			$.ajax({
				type: 'GET',
				url: url,
				success: resHandler,
				statusCode: {
					500: errorHandler,
					503: errorHandler
				}
			});
		},

		/**
		 * Обработчик изменения состояния истории в браузере
		 */
		stateChangeHandler = function stateChangeHandler() {
			var state = History.getState(),
				url = state.url;
			// end of vars
			
			console.info('statechange');
			console.log(state);

			if ( catalog.history._onlychange && typeof catalog.history._callback === 'function' ) {
				console.info('only update url ' + url);

				catalog.history._callback();
				catalog.history._onlychange = false;
				catalog.history._callback = null;
			}
			else {
				url = url.addParameterToUrl('ajax', 'true');
				getDataFromServer(url);
			}
		};
	// end of functions

	History.Adapter.bind(window, 'statechange', stateChangeHandler);
	
}(window.ENTER));	
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Filters
 *
 * @requires jQuery, Mustache, ENTER.utils, ENTER.config
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