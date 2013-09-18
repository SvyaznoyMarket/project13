/**
 * Работа с HISTORY API
 *
 * @requires jQuery, History.js
 *
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	global	Enter namespace
 */
;(function( ENTER ) {
	console.info('New catalog history module');

	var pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog');
	// end of vars

	catalog.history = {
		/**
		 * Обработка перехода на URL
		 * Если браузер не поддерживает History API происходит обычный переход по ссылке
		 * 
		 * @param	{String}	url			Адрес на который необходимо выполнить переход
		 * @param	{Function}	callback	Функция которая будет вызвана после получения данных от сервера
		 */
		gotoUrl: function gotoUrl( url, callback ) {
			var state = {
				title: 'Enter - это выход!',
				url: url
			};

			catalog.history._callback = callback;

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
		 * Обработчик нажатия на линк завязанный на history api
		 */
	var historyLinkHandler = function historyLinkHandler() {
			var url = $(this).attr('href');

			catalog.history.gotoUrl(url);

			return false;
		},

		/**
		 * Обработка ошибки загрузки данных
		 */
		errorHandler = function errorHandler() {
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
			}
			else {
				console.warn('res isn\'t object or catalog.history._callback isn\'t function');
				console.log(typeof res);
				console.log(typeof catalog.history._callback);
			}

			utils.blockScreen.unblock();
		},

		/**
		 * Обработчик изменения состояния истории в браузере
		 */
		stateChangeHandler = function stateChangeHandler() {
			var state = History.getState(),
				url = state.url;
			// end of vars
			
			console.info('statechange');

			utils.blockScreen.block('Загрузка товаров');

			url = url.addParameterToUrl('ajax', 'true');

			console.log(url);
			console.log(state);

			$.ajax({
				type: 'GET',
				url: url,
				success: resHandler,
				statusCode: {
					500: errorHandler,
					503: errorHandler
				}
			});
		};
	// end of functions


	History.Adapter.bind(window, 'statechange', stateChangeHandler);
	$('body').on('click', '.jsHistoryLink', historyLinkHandler);

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
 * @param	{Object}	global	Enter namespace
 */
;(function( ENTER ) {
	console.info('New catalog init: filter.js');

	var pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),

		filterBlock = $('.bFilter'),
		filterToggleBtn = filterBlock.find('.bFilterToggle'),
		filterContent = filterBlock.find('.bFilterCont'),

		filterMenuItem = filterBlock.find('.bFilterParams__eItem'),
		filterCategoryBlocks = filterBlock.find('.bFilterValuesItem');
	// end of vars


	// ==== Mustache test out
	console.log('Mustache is '+typeof Mustache);
	// ==== END Mustache test out
	
	catalog.enableHistoryAPI = ( typeof Mustache === 'object' ) && ( History.enabled );
	
	
	catalog.filter = {
		/**
		 * Отрисовка шаблона продуктов
		 * 
		 * @param	{Object}	res		Данные для шаблона
		 */
		renderTmpl: function( res ) {
			console.info('callback: renderTmpl');

			var compactListing = $('#listing_compact_tmpl'),
				compactListingTmpl = compactListing.html(),
				partials = compactListing.data('partial'),
				listingWrap = $('.bListing'),
				html;
			// end of vars
			
			console.log(listingWrap);
			console.log(partials);

			html = Mustache.render(compactListingTmpl, res, partials);
			// html = Mustache.to_html(compactListingTmpl, res, partials);

			console.log(html);

			listingWrap.empty();
			listingWrap.html(html);
			console.log('end of render');
		},

		/**
		 * Получение изменненых и неизменненых полей слайдеров
		 * 
		 * @return	{Object}	res
		 * @return	{Object}	res.changedSliders		Массив имен измененных полей
		 * @return	{Object}	res.unchangedSliders	Массив имен неизмененных полей
		 */
		getSlidersInputState: function() {
			console.info('getSlidersInputState');

			var sliders = $('.bRangeSlider'),
				res = {
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

			sliders.each(sortSliders);

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
		 * Отправка результатов фильтров
		 * Получение ответа от сервера
		 */
		sendFilter: function() {
			var url = catalog.filter.getFilterUrl();

			if ( url !== (document.location.pathname + document.location.search) ) {
				console.info('goto url '+url);

				catalog.history.gotoUrl(url, catalog.filter.renderTmpl);
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
				}
			});

			var inputUpdates = function inputUpdates() {
				var val = '0' + $(this).val();

				val = parseInt(val, 10);
				val = ( val > max ) ? max : ( val < min ) ? min : val;

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
		};
	// end of functions

	
	// Handlers
	filterToggleBtn.on('click', toggleFilterViewHandler);
	filterMenuItem.on('click', selectFilterCategoryHandler);
	filterBlock.on('submit', catalog.filter.sendFilter);

	$('.bRangeSlider').each(initSliderRange);

}(window.ENTER));