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

		filterBlock = $('.bFilter'),

		filterSubminBtn = filterBlock.find('.bBtnPick__eLink'),
		filterToggleBtn = filterBlock.find('.bFilterToggle'),
		filterContent = filterBlock.find('.bFilterCont'),
		filterSliders = filterBlock.find('.bRangeSlider'),
		filterMenuItem = filterBlock.find('.bFilterParams__eItem'),
		filterCategoryBlocks = filterBlock.find('.bFilterValuesItem'),

		viewParamPanel = $('.bSortingLine'),

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

			catalog.infScroll.checkInfinity();

			if ( lastPage ) {
				catalog.lastPage = lastPage;
			}

			catalog.filter.lastRes = dataToRender;

			body.trigger('markcartbutton');
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

			if ( !catalog.enableHistoryAPI ) {
				console.warn('history api off');

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
						filterBlock.trigger('change', [true]);
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
			var openClass = 'mOpen',
				closeClass = 'mClose',
				open = filterToggleBtn.hasClass(openClass);
			// end of vars


			if ( open && typeof openAnyway !== 'boolean' ) {
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

			$.scrollTo(filterBlock, 500);

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
	filterToggleBtn.on('click', toggleFilterViewHandler);
	filterMenuItem.on('click', selectFilterCategoryHandler);
	filterBlock.on('change', catalog.filter.changeFilterHandler);
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