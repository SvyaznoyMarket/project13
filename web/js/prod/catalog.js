/**
 * Catalog main config
 *
 * @requires jQuery, Mustache, ENTER.utils, ENTER.config
 * 
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	console.info('Catalog init: catalog.js');

	var //pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),
		lastPage = $('#bCatalog').data('lastpage');
	// end of vars


	catalog.enableHistoryAPI = ( typeof Mustache === 'object' ) && ( History.enabled );
	catalog.listingWrap = $('.bListing');
	catalog.liveScroll = false;
	catalog.lastPage = null;

	if ( lastPage ) {
		catalog.lastPage = lastPage;
	}

	console.info('Mustache is '+ typeof Mustache + ' (Catalog main config)');
	console.info('enableHistoryAPI '+ catalog.enableHistoryAPI);

    /**
     * Подключение слайдера товаров
     */
    $('.bGoodsSlider').goodsSlider({
        onLoad: function(goodsSlider) {
            ko.applyBindings(ENTER.UserModel, goodsSlider);
        }
    });

}(window.ENTER));
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
		pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),

		filterBlock = $('.js-filter'),
		hasAlwaysShowFilters = filterBlock.hasClass('js-filter-hasAlwaysShowFilters'),

		filterOtherParamsToggleButton = filterBlock.find('.js-filter-otherParamsToggleButton'),
		filterOtherParamsContent = filterBlock.find('.js-filter-otherParamsContent'),
		filterSliders = filterBlock.find('.js-filter-rangeSlider'),
		filterMenuItem = filterBlock.find('.js-filter-param'),
		filterCategoryBlocks = filterBlock.find('.js-filter-element'),

		viewParamPanel = $('.bSortingLine'),
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
			var changeViewItemsBtns = viewParamPanel.find('.mViewer .mSortItem');

			return changeViewItemsBtns.filter('.mActive').data('type');
		},

		applyTemplate: {
			list: function( html ) {
				console.info('applyTemplate list');
				catalog.listingWrap.empty();
				catalog.listingWrap.html(html);
			},

			selectedFilter: function( html ) {
				var filterFooterWrap = filterBlock.find('.bFilterFoot');

				filterFooterWrap.empty();
				filterFooterWrap.html(html);
			},

			sorting: function( html ) {
				var sortingWrap = viewParamPanel.find('.bSortingList.mSorting');

				sortingWrap.empty();
				sortingWrap.html(html);
			},

			pagination: function( html ) {
				var paginationWrap = $('.bSortingList.mPager');

				paginationWrap.empty();
				paginationWrap.html(html);
			},

			page: function( html ) {
				var title = $('.bTitlePage');

				title.empty();
				title.html(html);
			},

			countProducts: function ( html ) {
				var
					subminBtn = $('.bBtnPick__eLink', '.bFilter'),
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

					catalog.filter.updateFilter( data['values'] );
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

			catalog.filter.resetForm();

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
			console.info('getFilterUrl');

			var formData = filterBlock.serializeArray(),
				url = filterBlock.attr('action') || '',
				slidersInputState = catalog.filter.getSlidersInputState(),
				activeSort = viewParamPanel.find('.mSortItem.mActive').find('.jsSorting'),
				sortUrl = activeSort.data('sort'),
				formSerizalizeData,
				urlParams = catalog.filter.getUrlParams(),
				hasCategory = false;
			// end of vars

			for ( var i = formData.length - 1; i >= 0; i-- ) {
				if ( slidersInputState.unchangedSliders.indexOf(formData[i].name) !== -1 ) {
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
		changeFilterHandler: function( e, needUpdate ) {
			console.info('change filter');
			console.log(e);
			console.log(needUpdate);

			var sendUpdate = function sendUpdate() {
				filterBlock.trigger('submit');
			};

			if ( typeof e === 'object' && e.isTrigger && !needUpdate ) {
				console.warn('it\'s trigger event!');

				return;
			}

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
			}

			if ( e.isTrigger ) {
				console.warn('it\'s trigger');
			}
			
			else if ( typeof e === 'object' && catalog.enableHistoryAPI ) {
				console.warn('it\'s true event and HistoryAPI enable');

				$.scrollTo(filterBlock.find('.bFilterFoot'), 500);
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
				},

				resetCheckbox = function resetCheckbox( nf, input ) {
					$(input).removeAttr('checked').trigger('change');
				},

				resetSliders = function resetSliders() {
					var sliderWrap = $(this),
						slider = sliderWrap.find('.bFilterSlider'),
						sliderConfig = slider.data('config'),
						sliderFromInput = sliderWrap.find('.mFromRange'),
						sliderToInput = sliderWrap.find('.mToRange'),

						min = sliderConfig.min,
						max = sliderConfig.max;
					// end of vars
					
					sliderFromInput.val(min).trigger('change');
					sliderToInput.val(max).trigger('change');
				};
			// end of functions


			filterBlock.find(':input:radio:checked').each(resetRadio);
			filterBlock.find(':input:checkbox:checked').each(resetCheckbox);
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

			$('.js-filter-toggle-container', filterBlock).each(function() {
				$('.js-filter-toggle-button', this).addClass(filterOpenClass);
				$('.js-filter-toggle-content', this).slideDown(400);
			});
		}
	};


		/**
		 * Слайдеры в фильтре
		 */
	var initSliderRange = function initSliderRange() {
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

					if ( e.originalEvent ) {
						sliderFromInput.trigger('change', [true]);
						sliderToInput.trigger('change', [true]);
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

			catalog.history.gotoUrl(url);

			return false;
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
				$container = $('.js-filter-toggle-container'),
				$content = $('.js-filter-toggle-content', $button.closest('.js-filter-toggle-container'));
			// end of vars

			if ($button.hasClass(filterOpenClass)) {
				$button.removeClass(filterOpenClass);
				$content.slideUp(400);
				$self.parent('.js-filter-toggle-container').addClass('fltrSet-close');
			} else {
				$button.addClass(filterOpenClass);
				$content.slideDown(400);
				$self.parent('.js-filter-toggle-container').removeClass('fltrSet-close');
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
				changeViewItemsBtns = viewParamPanel.find('.mViewer .mSortItem'),
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
				sortingItemsBtns = viewParamPanel.find('.mSorting .mSortItem'),
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


	// Handlers
	filterBlock.on('click', '.js-filter-toggle-button', toggleHandler);
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
/**
 * Работа с HISTORY API
 *
 * @requires	jQuery, History.js, ENTER.utils, ENTER.config, ENTER.catalog
 *
 * @author		Zaytsev Alexandr
 *
 * @param		{Object}	global	Enter namespace
 */
;(function( ENTER ) {
	var
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog');
	// end of vars

	console.info('New catalog history module');

	catalog.history = {
		/**
		 * Ссылка на функцию обратного вызова по-умолчанию после получения данных с сервера при изменении history state
		 * 
		 * @type	{Function}
		 */
		_defaultCallback: catalog.filter.renderCatalogPage,

		/**
		 * Кастомная функция обратного вызова после получения данных с сервера
		 * 
		 * @type	{Function}
		 */
		_customCallback: null,

		/**
		 * Обработка перехода на URL
		 * Если браузер не поддерживает History API происходит обычный переход по ссылке
		 * 
		 * @param	{String}	url			Адрес на который необходимо выполнить переход
		 * @param	{Function}	callback	Функция которая будет вызвана после получения данных от сервера
		 * @param	{Boolean}	onlychange	Показывает что необходимо только изменить URL и не запрашивать данные
		 */
		gotoUrl: function gotoUrl( url, customCallback, onlychange ) {
			console.info('gotoUrl');
			var state = {
					title: document.title,
					url: url,
					data: {
						_onlychange: (onlychange) ? true : false
					}
				};
			// end of vars

			if ( !catalog.enableHistoryAPI ) {
				document.location.href = url;

				return;
			}

			catalog.history._customCallback = (customCallback) ? customCallback : null;

			console.info('link handler. push state new url: ' + state.url);
			History.pushState(state, state.title, state.url);

			return;
		},

		updateUrl: function updateUrl( url, customCallback ) {
			var callback = (customCallback) ? customCallback : null;

			catalog.history.gotoUrl( url, callback, true );

			return;
		},

		/**
		 * Запросить новые данные с сервера по url
		 * 
		 * @param	{String}	url
		 */
		getDataFromServer: function getDataFromServer( url, callback ) {
			console.info('getDataFromServer ' + url);
			
			if ( catalog.loader ) {
				catalog.loader.loading();
			}

			console.log('getDataFromServer::loading');

			// utils.blockScreen.block('Загрузка товаров');

				/**
				 * Обработка ошибки загрузки данных
				 */
			var errorHandler = function errorHandler() {
					// utils.blockScreen.unblock();
					if ( catalog.loader ) {
						catalog.loader.complete();
					}
				},

				/**
				 * Получение данных от сервера
				 * Перенаправление данных в функцию обратного вызова
				 * 
				 * @param	{Object}	res	Полученные данные
				 */
				resHandler = function resHandler( res ) {
					console.info('resHandler');

					if ( typeof res === 'object') {
						callback(res);
					}
					else {
						console.warn('res isn\'t object');
						console.log(typeof res);
					}

					// utils.blockScreen.unblock();
					if ( catalog.loader ) {
						catalog.loader.complete();
					}
				};
			// end of functions

			$.ajax({
				type: 'GET',
				url: url,
				success: resHandler,
				statusCode: {
					500: errorHandler,
					503: errorHandler
				},
				error: errorHandler
			});
		}
	};

		/**
		 * Обработчик изменения состояния истории в браузере
		 */
	var stateChangeHandler = function stateChangeHandler() {
			var state = History.getState(),
				url = state.url,
				data = state.data.data,
				callback = ( typeof catalog.history._customCallback === 'function' ) ? catalog.history._customCallback : catalog.history._defaultCallback;
			// end of vars
			
			console.info('statechange');
			console.log(state);

			if ( data._onlychange ) {
				console.info('only update url ' + url);

				callback();
			}
			else {
				url = url.addParameterToUrl('ajax', 'true');
				catalog.history.getDataFromServer(url, callback);
			}

			catalog.history._customCallback = null;
		};
	// end of functions

	History.Adapter.bind(window, 'statechange', stateChangeHandler);
	
}(window.ENTER));	
/**
 * Catalog infinity scroll
 *
 * @requires jQuery, jquery.visible, Mustache, docCookies, ENTER.utils, ENTER.config, ENTER.catalog.history
 * 
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	console.info('[Catalog] Init catalog_infinityScroll.js');

	var
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),
		viewParamPanel = $('.bSortingLine'),
        bottomInfButton = $('.jsInfinityEnable').last();
	// end of vars

	
	catalog.infScroll = {
		loading: false,

		nowPage: 1,

		checkInfinity: function() {
			console.info('Infinity scroll cookie = '+ window.docCookies.getItem( 'infScroll' ));
			if ( window.docCookies.getItem( 'infScroll' ) === '1' ) {
				catalog.infScroll.enable();
			}
		},

		checkScroll: function() {

			var w = $(window),
				d = $(document);
			// end of vars

			if ( !catalog.infScroll.loading && bottomInfButton.visible() &&
				( catalog.lastPage - catalog.infScroll.nowPage > 0 || null === catalog.lastPage ) ) {
				console.warn('checkscroll true. load');
				catalog.infScroll.nowPage += 1;
				catalog.infScroll.load();
			}
		},

		load: function() {
			console.info('load page');

			var url = catalog.filter.getFilterUrl();

			var resHandler = function resHandler( res ) {
				var html;

				html = catalog.filter.render['list']( res['list'] );
				catalog.infScroll.loading = false;

				catalog.listingWrap.append(html);
			};

			catalog.liveScroll = true;
			catalog.infScroll.loading = true;
			url = url.addParameterToUrl('page', catalog.infScroll.nowPage);
			url = url.addParameterToUrl('ajax', 'true');

			catalog.history.getDataFromServer(url, resHandler);
		},

		enable: function() {

			var activeClass = 'mActive',
				infBtn = viewParamPanel.find('.mInfinity'),
				pagingBtn = viewParamPanel.find('.mPaging'),
				pageBtn = viewParamPanel.find('.bSortingList__eItem.mPage'),
				url = catalog.filter.getFilterUrl(),
				hasPaging = document.location.search.match('page=');
			// end of vars

			pagingBtn.show();
			pageBtn.hide();
			infBtn.addClass(activeClass);

			catalog.infScroll.nowPage = 1;
			catalog.infScroll.loading = false;

			window.docCookies.setItem('infScroll', 1, 4*7*24*60*60, '/' );

			catalog.infScroll.checkScroll();
			$(window).on('scroll', catalog.infScroll.checkScroll);

			console.info(hasPaging);

			if ( catalog.enableHistoryAPI && hasPaging ) {
				catalog.history.gotoUrl(url);
			}

			bottomInfButton = $('.jsInfinityEnable').last();

			if (bottomInfButton.visible() && catalog.lastPage > 1) {
				catalog.infScroll.nowPage += 1;
				this.load();
			}

			console.log('Infinity scroll enabled');
		},

		disable: function() {
			console.info('Infinity scroll disabling');

			var url = catalog.filter.getFilterUrl();
			// end of vars

			catalog.liveScroll = false;
			url = url.addParameterToUrl('ajax', 'true');

			window.docCookies.setItem('infScroll', 0, 4*7*24*60*60, '/' );
			$(window).off('scroll', catalog.infScroll.checkScroll);
			catalog.history.getDataFromServer(url, catalog.filter.renderCatalogPage);

			console.log('infinity scroll disable '+window.docCookies.getItem( 'infScroll' ));
		}
	};

	var infBtnHandler = function infBtnHandler() {
			var activeClass = 'mActive',
				infBtn = viewParamPanel.find('.mInfinity'),
				isActiveTab = infBtn.hasClass(activeClass);
			// end of vars
			
			if ( isActiveTab ) {
				return false;
			}

			catalog.infScroll.enable();

			return false;
		},

		paginationBtnHandler = function paginationBtnHandler() {
			console.info('paginationBtnHandler');
			var activeClass = 'mActive',
				infBtn = viewParamPanel.find('.mInfinity'),
				isActiveTab = infBtn.hasClass(activeClass);
			// end of vars
			
			if ( isActiveTab ) {
				catalog.infScroll.disable();
			}

			return false;
		};
	// end of functions

	catalog.infScroll.checkInfinity();

	viewParamPanel.on('click', '.jsPaginationEnable', paginationBtnHandler);
	viewParamPanel.on('click', '.jsInfinityEnable', infBtnHandler);

}(window.ENTER));
/**
 * Catalog loader
 *
 * @requires jQuery, Mustache, ENTER.utils, ENTER.config, ENTER.catalog.history
 * 
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	console.info('New catalog init: loader.js');

	var
		pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),
		filterSubminBtn = $('.bBtnPick__eLink', '.bFilter');
	// end of vars

	console.info('Mustache is '+ typeof Mustache);
	console.info('enableHistoryAPI '+ catalog.enableHistoryAPI);

	catalog.loader = {
		_loader: null,
		loading: function() {
			if ( catalog.loader._loader ) {
				return;
			}

			catalog.loader._loader = $('<li>').addClass('mLoader');
			filterSubminBtn.addClass('mButLoader').text('Подобрать');

			if ( catalog.liveScroll ) {
				catalog.listingWrap.append(catalog.loader._loader);
			}
			else {
				catalog.listingWrap.empty();
				catalog.listingWrap.append(catalog.loader._loader);
			}
		},

		complete: function() {
			if ( catalog.loader._loader ) {
				catalog.loader._loader.remove();
				catalog.loader._loader = null;
			}
			filterSubminBtn.removeClass('mButLoader');
			$('body').trigger('catalogLoadingComplete');
		}
	};

}(window.ENTER));
/**
 * Package set
 *
 * 
 */
;(function( ENTER ) {

	var packageSetBtn = $('.jsChangePackageSet'),
		packageSetWindow = $('.jsPackageSetPopup');
	// end of vars
	
	/**
	 * Показ окна с изменение комплекта 
	 */
	var showPackageSetPopup = function showPackageSetPopup() {
			packageSetWindow.lightbox_me({
				autofocus: true,
			});
		};

	packageSetBtn.on('click', showPackageSetPopup);

}(window.ENTER));
/**
 * Обработчик для SprosiKupi
 */
$(function() {
	if (!$('.spk-good-rating').length) {
		return;
	}

	$.getScript("//static.sprosikupi.ru/js/widget/sprosikupi.bootstrap.js");
	$('body').on('catalogLoadingComplete', function(){
        spkLoadRatings();
	});
});

/**
 * Обработчик для ShopPilot
 */
$(function() {
	if (!$('.shoppilot-category-container').length) {
		return;
	}

	_shoppilot = window._shoppilot || [];
	_shoppilot.push(['_setStoreId', '535a852cec8d830a890000a6']);
	_shoppilot.push(['_setOnReady', function (Shoppilot) {
		function initWidgets() {
			$('.shoppilot-category-container').each(function() {
				var ratingContainer = $(this);
				(new Shoppilot.ProductWidget({
					name: 'category-product-rating',
					styles: 'product-reviews',
					product_id: ratingContainer.data('productId')
				})).appendTo(ratingContainer[0]);
			});
		}

		initWidgets();
		$('body').on('catalogLoadingComplete', initWidgets);
	}]);

	(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.async = true;
		script.src = '//ugc.shoppilot.ru/javascripts/require.js';
		script.setAttribute('data-main',
			'//ugc.shoppilot.ru/javascripts/social-apps.js');
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(script, s);
	})();
});

;$(function() {
	var
		brandLinkActiveClass = 'active',
		brandTitleOpenedClass = 'opened',
		$selectedBrandsWrapper = $('.js-productCategory-rootPage-brands-selectedBrandsWrapper'),
		$brandLinks = $('.js-productCategory-rootPage-brands-link');

	function renderSelectedBrandsTemplate() {
		var $template = $('#root_page_selected_brands_tmpl');
		$selectedBrandsWrapper.html(Mustache.render($template.html(), {brandsCount: $brandLinks.length, selectedBrandsCount: $brandLinks.filter('.' + brandLinkActiveClass).length}, $template.data('partial')));
	}

	$brandLinks.click(function(e) {
		e.preventDefault();

		var
			$brandLink = $(e.currentTarget),
			url = document.location.href,
			brandLinkUrlParam = e.currentTarget.href.replace(/^.*?\?([^#]+)(\#.*)?$/, '$1'),
			brandLinkUrlParamName = decodeURIComponent(brandLinkUrlParam.replace(/^(.*?)\=.*$/, '$1')),
			brandLinkUrlParamValue = decodeURIComponent(brandLinkUrlParam.replace(/^.*?\=(.*)$/, '$1'));

		if ($brandLink.hasClass(brandLinkActiveClass)) {
			$brandLink.removeClass(brandLinkActiveClass);
			url = ENTER.utils.setURLParam(brandLinkUrlParamName, null, url);
		} else {
			$brandLink.addClass(brandLinkActiveClass);
			url = ENTER.utils.setURLParam(brandLinkUrlParamName, brandLinkUrlParamValue, url);
		}

		history.pushState({}, document.title, url);
		renderSelectedBrandsTemplate();

		$.ajax({
			url: url,
			type: 'GET',
			success: function(result){
				var
					$template = $('#root_page_links_tmpl'),
					$linksWrapper = $('.js-productCategory-rootPage-linksWrapper');

				$linksWrapper.html(Mustache.render($template.html(), {links: result.links, category: result.category}, $template.data('partial')));
			}
		});
	});


	$('.js-productCategory-rootPage-brands-else, .js-productCategory-rootPage-brands-title').click(function(e) {
		e.preventDefault();

		var
			$other = $('.js-productCategory-rootPage-brands-other'),
			$else = $('.js-productCategory-rootPage-brands-else'),
			$title = $('.js-productCategory-rootPage-brands-title');

		$other.toggle();

		if ($other.css('display') == 'none') {
			$else.show();
			$title.removeClass(brandTitleOpenedClass);
		} else {
			$else.hide();
			$title.addClass(brandTitleOpenedClass);
		}
	});

	$selectedBrandsWrapper.on('click', '.js-productCategory-rootPage-selectedBrands-clear', function(e) {
		e.preventDefault();

		$brandLinks.removeClass(brandLinkActiveClass);
		history.pushState({}, document.title, '?');
		renderSelectedBrandsTemplate();
	});
});
;(function($) {
	var seoList = $('.js-seo-list');

	seoList.find('.js-seo-list-item:lt(11)').css({'display' : 'inline-block'});

	seoList.each(function() {
		if ( $(this).children('.js-seo-list-item').length > 11 ) {
			$(this).append('<li class="bPopularSection_more js-seo-list-item-more">Показать ещё</li>');
		}
	});

	var seoListToggle = function seoListToggle() {
		var text = $(this).text();
		$(this).parent().children().toggleClass('seotext-show');
    	$(this).text(text == " Скрыть " ? " Показать ещё " : " Скрыть ");
	};

	seoList.find('.js-seo-list-item-more').on('click', seoListToggle);

})(jQuery);
;$(document).ready(function(){

	var smartChoiceSlider = $('.jsDataSmartChoice'),
		smartChoiceItem = $('.specialPriceItem'),
		smartChoiceItemAttr = smartChoiceSlider.attr('data-smartchoice');

	if ( typeof smartChoiceSlider.data('smartchoice') !== 'undefined' ) {
		$.getJSON('/ajax/product-smartchoice',{
				"products[]": smartChoiceSlider.data('smartchoice') },
			function(data){
				if (data.success) {
					$.each(data.result, function(i, value){
						$slider = $.parseHTML(value.content);
						$($slider).hide();
						$('.specialBorderBox').append($slider);
						$('.smartChoiceSliderToggle-'+i).show();
					});

					var goodsSlider = $('.bGoodsSlider');
					goodsSlider.goodsSlider();
					goodsSlider.each(function() {
						ko.applyBindings(ENTER.UserModel, this);
					});

					console.info('smartchoice ajax: ', data.result);
				}
			}
		);
	}

	$('.jsSmartChoiceSliderToggle a').click(function(e){
		e.preventDefault();
		var $target = $(e.target),
			id = $target.closest('div').data('smartchoice'),
			$link = $target.closest('a'),
			$specialPriceItemFoot_links = $('.specialPriceItemFoot_link');
		if (!$link.hasClass('mActive')) {
			$specialPriceItemFoot_links.removeClass('mActive');
			$link.addClass('mActive');
			$('.bGoodsSlider').hide();
			$('.specialBorderBox').addClass('specialBorderBox_render');
			$('.smartChoiceId-' + id).parent().show();
		} else {
			$specialPriceItemFoot_links.removeClass('mActive');
			$('.smartChoiceId-' + id).parent().hide();
			$('.specialBorderBox').removeClass('specialBorderBox_render');
		}
	});

	if ( typeof smartChoiceItemAttr !== 'undefined' && smartChoiceItemAttr !== false ) {
		smartChoiceItem.addClass('specialPriceItem_minHeight');
	}
	else { smartChoiceItem.removeClass('specialPriceItem_minHeight') };

	function track(event, article) {
		var ga = window[window.GoogleAnalyticsObject],
			_gaq = window['_gaq'],
			loc = window.location.href;

		if (ga) ga('send', 'event', event, loc, article);
		if (_gaq) _gaq.push(['_trackEvent', event, loc, article]);
	}

	// Tracking click on <a>
	smartChoiceItem.on('click', '.specialPriceItemCont_imgLink, .specialPriceItemCont_name', function(){
		var article = $(this).data('article');
		track('SmartChoice_click', article);
	});

	// Tracking click on <a> in similar carousel
	smartChoiceSlider.on('click', '.productImg, .productName a', function(e){
		var article = $(e.target).closest('.bSlider__eItem').data('product').article;
		track('SmartChoice_similar_click', article);
	});

	var $specialPrice = $('.js-specialPrice');
	if ($specialPrice.length) {
		ko.applyBindings(ENTER.UserModel, $specialPrice[0]);
	}
});