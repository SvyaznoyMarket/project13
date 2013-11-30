/**
 * Саджест для поля поиска
 * Нужен рефакторинг
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, jQuery.placeholder
 *
 * @param	{Object}	searchInput			Поле поиска
 * @param	{Object}	suggestWrapper		Обертка для подсказок
 * @param	{Object}	suggestItem			Результаты поиска
 * 
 * @param	{Number}	nowSelectSuggest	Текущий выделенный элемент, если -1 - значит выделенных элементов нет
 * @param	{Number}	suggestLen			Количество результатов поиска
 */
;(function() {
	var searchForm = $('div.searchbox form'),
        searchInput = searchForm.find('input.searchtext'),
		suggestWrapper = $('#searchAutocomplete'),
		suggestItem = $('.bSearchSuggest__eRes'),

		nowSelectSuggest = -1,
		suggestLen = 0,

		suggestCache = {},

		tID = null;
	// end of vars	


	var suggestAnalytics = function suggestAnalytics() {
			var link = suggestItem.eq(nowSelectSuggest).attr('href'),
				type = ( suggestItem.eq(nowSelectSuggest).hasClass('bSearchSuggest__eCategoryRes') ) ? 'suggest_category' : 'suggest_product';
			// end of vars
			
			if ( typeof(_gaq) !== 'undefined' ) {	
				_gaq.push(['_trackEvent', 'Search', type, link]);
			}
		},

		/**
		 * Обработчик поднятия клавиши
		 * 
		 * @param	{Event}		event
		 * @param	{Number}	keyCode	Код нажатой клавиши
		 * @param	{String}	text	Текст в поле ввода
		 */
		suggestKeyUp = function suggestKeyUp( event ) {
			var keyCode = event.which,
				text = searchInput.attr('value');

				/**
				 * Отрисовка данных с сервера
				 * 
				 * @param	{String}	response	Ответ от сервера
				 */
			var renderResponse = function renderResponse( response ) {
					suggestCache[text] = response; // memoization

					suggestWrapper.html(response);
					suggestItem = $('.bSearchSuggest__eRes');
					suggestLen = suggestItem.length;
				},

				/**
				 * Запрос на получение данных с сервера
				 */
				getResFromServer = function getResFromServer() {
					var url = '/search/autocomplete?q='+encodeURI(text);

					$.ajax({
						type: 'GET',
						url: url,
						success: renderResponse
					});
				};
			// end of function

			
			if ( (keyCode >= 37 && keyCode <= 40) ||  keyCode === 27 || keyCode === 13) { // Arrow Keys or ESC Key or ENTER Key
				return false;
			}

			if ( text.length === 0 ) {
				suggestWrapper.empty();

				return false;
			}

			clearTimeout(tID);

			// memoization
			if ( suggestCache[text] ) {
				renderResponse(suggestCache[text]);

				return false;
			}
			
			tID = setTimeout(getResFromServer, 300);
		},

		/**
		 * Обработчик нажатия клавиши
		 * 
		 * @param	{Event}		event
		 * @param	{Number}	keyCode	Код нажатой клавиши
		 */
		suggestKeyDown = function suggestKeyDown( event ) {
			var keyCode = event.which;

			var markSuggestItem = function markSuggestItem() {
					suggestItem.removeClass('hover').eq(nowSelectSuggest).addClass('hover');
				},

				selectUpItem = function selectUpItem() {
					if ( nowSelectSuggest - 1 >= 0 ) {
						nowSelectSuggest--;
						markSuggestItem();
					}
					else {
						nowSelectSuggest = -1;
						suggestItem.removeClass('hover');
						$(this).focus();
					}
				},

				selectDownItem = function selectDownItem() {
					if ( nowSelectSuggest + 1 <= suggestLen - 1 ) {
						nowSelectSuggest++;
						markSuggestItem();
					}
				},

				enterSelectedItem = function enterSelectedItem() {
					var link = suggestItem.eq(nowSelectSuggest).attr('href');

					suggestAnalytics();
					document.location.href = link;
				}

				escapeSearchQuery = function escapeSearchQuery() {
					var s = searchInput.val().replace(/(^\s*)|(\s*$)/g,'').replace(/(\s+)/g,' ');
					searchInput.val(s);
				};
			// end of functions

			if ( keyCode === 38 ) { // Arrow Up
				selectUpItem();

				return false;
			}
			else if ( keyCode === 40 ) { // Arrow Down
				selectDownItem();

				return false;
			}
			else if ( keyCode === 27 ) { // ESC Key
				suggestWrapper.empty();
				
				return false;
			}
			else if ( keyCode === 13 ) {
				escapeSearchQuery();
				if ( nowSelectSuggest !== -1 ) { // Press Enter and suggest has selected item
					enterSelectedItem();

					return false;
				}
			}
		},

		searchSubmit = function searchSubmit() {
			var text = searchInput.attr('value');

			if ( text.length === 0 ) {
				return false;
			}
			escapeSearchQuery();
		},

		searchInputFocusin = function searchInputFocusin() {
			suggestWrapper.show();
		},
		
		suggestCloser = function suggestCloser( e ) {
			var targ = e.target.className;

			if ( !(targ.indexOf('bSearchSuggest')+1 || targ.indexOf('searchtext')+1) ) {
				suggestWrapper.hide();
			}
		},

		/**
		 * Срабатывание выделения и запоминание индекса выделенного элемента по наведению мыши
		 */
		hoverForItem = function hoverForItem() {
			var index = 0;

			suggestItem.removeClass('hover');
			index = $(this).addClass('hover').index();
			nowSelectSuggest = index - 1;
		},


		/**
		 * Подставляет поисковую подсказку в строку поиска
		 */
		searchHintSelect = function searchHintSelect() {
			var hintValue = $(this).text(),
				searchValue = searchInput.val();
			if ( searchValue ) hintValue = searchValue + ' ' + hintValue;
			return searchInput.val(hintValue + ' ').focus();
		};
	// end of functions


	/**
	 * Attach handlers
	 */
	$(document).ready(function() {
		searchInput.bind('keydown', suggestKeyDown);
		searchInput.bind('keyup', suggestKeyUp);

		searchInput.bind('focus', searchInputFocusin);
        searchForm.bind('submit', searchSubmit);

		searchInput.placeholder();

		$('body').bind('click', suggestCloser);
		$('body').on('mouseenter', '.bSearchSuggest__eRes', hoverForItem);
		$('body').on('click', '.bSearchSuggest__eRes', suggestAnalytics);
		$('body').on('click', '.sHint_value', searchHintSelect);
	});
}());
