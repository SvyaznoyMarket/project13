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

	var userUrl = ENTER.config.pageConfig.userUrl,
		utils = ENTER.utils,

		filterBlock = $('.bFilter'),
		filterToggleBtn = filterBlock.find('.bFilterToggle'),
		filterContent = filterBlock.find('.bFilterCont'),

		filterMenuItem = filterBlock.find('.bFilterParams__eItem'),
		filterCategoryBlocks = filterBlock.find('.bFilterValuesItem');
	// end of vars

	// ==== Mustache test out
	console.log('Mustache is '+typeof Mustache);

	var person = {
			firstName: "Alexandr",
			lastName: "Zaytsev"
		},
		template = "<h1>{{firstName}} {{lastName}}</h1>test out with Mustache<br/><a class='jsHistoryLink' href='/newurl'>test history api</a>",
		html = Mustache.to_html(template, person),
		testOut = $('<div>').addClass('popup').html(html);
	// end of vars

	// testOut.appendTo('body');

	// testOut.lightbox_me({
	// 	centered: true
	// });
	// ==== END Mustache test out
	
	
	var historyLinkHandler = function historyLinkHandler() {
		var state = {
			title: 'history link to '+$(this).attr('href'),
			url: $(this).attr('href')
		}

		console.info('link handler. push state '+state.url);

		History.pushState(state, state.title, state.url);

		return false;
	};

	/**
	 * Обработка back\forward
	 */
	var backForwardHandler = function backForwardHandler() {
		var returnLocation = history.location || document.location;

		console.info(returnLocation);
		console.log( JSON.stringify(history.state) );
	};

	var stateChangeHandler = function stateChangeHandler() {
		var state = History.getState(),
			url = state.url;
		// end of vars
		
		console.info('statechange');

		console.log(url);
		console.log(state);
	};

	History.Adapter.bind(window, "statechange", stateChangeHandler);
	$('body').on('click', '.jsHistoryLink', historyLinkHandler);



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
	};

	/**
	 * Обработчик выбора категории фильтра
	 */
	var selectFilterCategoryHandler = function selectFilterCategoryHandler() {
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
	};

	filterToggleBtn.on('click', toggleFilterViewHandler);
	filterMenuItem.on('click', selectFilterCategoryHandler);

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
			}
		});
	};

	$('.bRangeSlider').each(initSliderRange);

}(window.ENTER));