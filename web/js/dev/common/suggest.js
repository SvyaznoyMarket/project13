/**
 * Саджест для поля поиска
 * Нужен рефакторинг
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
;(function() {
	var nowSelectSuggest = -1,
		suggestLen = 0;
	// end of vars
	

	/**
	 * Хандлер на поднятие клавиши в поле поиска
	 * @param  {event} e
	 */
	var suggestUp = function suggestUp( e ) {
			var text = $(this).attr('value'),
				url = '/search/autocomplete?q='+encodeURI(text);
			// end of vars

			if (!text.length){
				if ( $(this).siblings('.searchtextClear').length ) {
					$(this).siblings('.searchtextClear').addClass('vh');
				}
			}
			else {
				if ( $(this).siblings('.searchtextClear').length ) {
					$(this).siblings('.searchtextClear').removeClass('vh');
				}
			}

			var authFromServer = function authFromServer( response ) {
				$('#searchAutocomplete').html(response);
				suggestLen = $('.bSearchSuggest__eRes').length;
			};

			if ( (e.which < 37 || e.which>40) && (nowSelectSuggest = -1) ) {
				if ( !text.length ) { 
					return false;
				}

				if ( $(this).siblings('.searchtextClear').length ) {
					$(this).siblings('.searchtextClear').removeClass('vh');
				}
				
				$('.bSearchSuggest__eRes').removeClass('hover');
				nowSelectSuggest = -1;

				$.ajax({
					type: 'GET',
					url: url,
					success: authFromServer
				});
			}
		},

		/**
		 * Хандлер на нажатие клавиши в поле поиска
		 * @param  {event} e
		 */
		suggestDown = function suggestDown( e ) {
			/**
			 * маркировка пункта
			 */
			var markSuggest = function markSuggest() {
					$('.bSearchSuggest__eRes').removeClass('hover').eq(nowSelectSuggest).addClass('hover');
				},

				/**
				 * стрелка вверх
				 */
				upSuggestItem = function upSuggestItem() {
					if ( nowSelectSuggest - 1 >= 0 ) {
						nowSelectSuggest--;
						markSuggest();
					}
					else{
						nowSelectSuggest = -1;
						$('.bSearchSuggest__eRes').removeClass('hover');
						$(this).focus();
					}
					
				},

				/**
				 * стрелка вниз
				 */
				downSuggestItem = function downSuggestItem() {
					if ( nowSelectSuggest + 1 <= suggestLen - 1 ) {
						nowSelectSuggest++;
						markSuggest();
					}			
				},

				/**
				 * нажатие клавиши 'enter'
				 */
				enterSuggest = function enterSuggest() {
					var link = $('.bSearchSuggest__eRes').eq(nowSelectSuggest).attr('href'),
						type = ($('.bSearchSuggest__eRes').eq(nowSelectSuggest).hasClass('bSearchSuggest__eCategoryRes')) ? 'suggest_category' : 'suggest_product';
					// end of vars
					
					if ( typeof(_gaq) !== 'undefined' ) {	
						_gaq.push(['_trackEvent', 'Search', type, link]);
					}

					document.location.href = link;
				};
			// end of functions

			if ( e.which === 38 ) {
				upSuggestItem();
			}
			else if ( e.which === 40 ) {
				downSuggestItem();
			}
			else if ( e.which === 13 && nowSelectSuggest !== -1 ) {
				e.preventDefault();
				enterSuggest();
			}
		},

		suggestInputFocus = function suggestInputFocus() {
			nowSelectSuggest = -1;
			$('.bSearchSuggest__eRes').removeClass('hover');
		},

		suggestInputClick = function suggestInputClick() {
			$('#searchAutocomplete').show();
		};
	// end of functions

	$(document).ready(function() {
		/**
		 * навешивание хандлеров на поле поиска
		 */
		$('.searchbox .searchtext').keydown(suggestDown).keyup(suggestUp).mouseenter(suggestInputFocus).focus(suggestInputFocus).click(suggestInputClick).placeholder();

		$('.searchbox .search-form').submit(function(){
			var text = $('.searchbox .searchtext').attr('value');

			if ( !text.length ) {
				return false;
			}
		});

		$('.bSearchSuggest__eRes').on('mouseover', function(){
			var index = $(this).addClass('hover').index();

			$('.bSearchSuggest__eRes').removeClass('hover');
			nowSelectSuggest = index - 1;
		});

		$('body').click(function(e){		
			var targ = e.target.className;

			if ( !(targ.indexOf('bSearchSuggest') + 1 || targ.indexOf('searchtext') + 1) ) {
				$('#searchAutocomplete').hide();
			}
		});

		/**
		 * suggest analitycs
		 */
		$('.bSearchSuggest__eRes').on('click', function(){
			if ( typeof(_gaq) !== 'undefined' ) {
				var type = ($(this).hasClass('bSearchSuggest__eCategoryRes')) ? 'suggest_category' : 'suggest_product',
					url = $(this).attr('href');
				// end of vars

				_gaq.push(['_trackEvent', 'Search', type, url]);
			}
		});
	});
}());