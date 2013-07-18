/**
 * Саджест для поля поиска
 * Нужен рефакторинг
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
;(function(){
	var nowSelectSuggest = -1;
	var suggestLen = 0;

	/**
	 * Хандлер на поднятие клавиши в поле поиска
	 * @param  {event} e
	 */
	var suggestUp = function(e){
        var text = $(this).attr('value');

        if (!text.length){
            if($(this).siblings('.searchtextClear').length) {
                $(this).siblings('.searchtextClear').addClass('vh');
            }
        }
        else {
            if($(this).siblings('.searchtextClear').length) {
                $(this).siblings('.searchtextClear').removeClass('vh');
            }
        }

		var authFromServer = function(response){
			$('#searchAutocomplete').html(response);
			suggestLen = $('.bSearchSuggest__eRes').length;
		};

        if ((e.which < 37 || e.which>40) && (nowSelectSuggest = -1)){
            if (!text.length){ 
                return false;
            }

            if($(this).siblings('.searchtextClear').length) {
                $(this).siblings('.searchtextClear').removeClass('vh');
            }
			
			$('.bSearchSuggest__eRes').removeClass('hover');
			nowSelectSuggest = -1;

			var url = '/search/autocomplete?q='+encodeURI(text);

			$.ajax({
				type: 'GET',
				url: url,
				success: authFromServer
			});
		}
	};

	/**
	 * Хандлер на нажатие клавиши в поле поиска
	 * @param  {event} e
	 */
	var suggestDown = function(e){
		/**
		 * маркировка пункта
		 */
		var markSuggest = function(){
			$('.bSearchSuggest__eRes').removeClass('hover').eq(nowSelectSuggest).addClass('hover');
		};

		/**
		 * стрелка вверх
		 */
		var upSuggestItem = function(){
			if (nowSelectSuggest-1 >= 0){
				nowSelectSuggest--;
				markSuggest();
			}
			else{
				nowSelectSuggest = -1;
				$('.bSearchSuggest__eRes').removeClass('hover');
				$(this).focus();
			}
			
		};

		/**
		 * стрелка вниз
		 */
		var downSuggestItem = function(){
			if (nowSelectSuggest+1 <= suggestLen-1){
				nowSelectSuggest++;
				markSuggest();
			}			
		};

		/**
		 * нажатие клавиши 'enter'
		 */
		var enterSuggest = function(){
			// suggest analitycs
			var link = $('.bSearchSuggest__eRes').eq(nowSelectSuggest).attr('href');
			var type = ($('.bSearchSuggest__eRes').eq(nowSelectSuggest).hasClass('bSearchSuggest__eCategoryRes')) ? 'suggest_category' : 'suggest_product';
			
			if ( typeof(_gaq) !== 'undefined' ){	
				_gaq.push(['_trackEvent', 'Search', type, link]);
			}
			document.location.href = link;
		};

		if (e.which === 38){
			upSuggestItem();
		}
		else if (e.which === 40){
			downSuggestItem();
		}
		else if (e.which === 13 && nowSelectSuggest !== -1){
			e.preventDefault();
			enterSuggest();
		}
		// console.log(nowSelectSuggest)
	};

	var suggestInputFocus = function(){
		nowSelectSuggest = -1;
		$('.bSearchSuggest__eRes').removeClass('hover');
	};

	var suggestInputClick = function(){
		$('#searchAutocomplete').show();
	};

	$(document).ready(function() {
		/**
		 * навешивание хандлеров на поле поиска
		 */
		$('.searchbox .searchtext').keydown(suggestDown).keyup(suggestUp).mouseenter(suggestInputFocus).focus(suggestInputFocus).click(suggestInputClick).placeholder();

		$('.searchbox .search-form').submit(function(){
			var text = $('.searchbox .searchtext').attr('value');
			if (!text.length){
				return false;
			}
		});

		$('.bSearchSuggest__eRes').on('mouseover', function(){
			$('.bSearchSuggest__eRes').removeClass('hover');
			var index = $(this).addClass('hover').index();
			nowSelectSuggest = index - 1;
		});

		$('body').click(function(e){		
			var targ = e.target.className;
			if (!(targ.indexOf('bSearchSuggest')+1 || targ.indexOf('searchtext')+1)) {
				$('#searchAutocomplete').hide();
			}
		});

		/**
		 * suggest analitycs
		 */
		$('.bSearchSuggest__eRes').on('click', function(){
			if ( typeof(_gaq) !== 'undefined' ){
				var type = ($(this).hasClass('bSearchSuggest__eCategoryRes')) ? 'suggest_category' : 'suggest_product';
				var url = $(this).attr('href');

				_gaq.push(['_trackEvent', 'Search', type, url]);
			}
		});
	});
}());