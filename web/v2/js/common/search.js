/**
 * Окно поиска
 *
 */
;(function (app, window, $, _, undefined) {

	app.searchForm = function() {
		var body = $('body'),
			popupSearch = $('.jsSearchBox'),
			popupOpen = $('.jsSearch');
		// end of vars	

		var
		/**
		 * Показываем попап формы поиска
		*/
		searchPopup = function searchPopup( event ) {
			var topPopup = $('.header').height() + 20;

			popupSearch.enterPopup({
				popupCSS : {top: topPopup, marginTop: 0}
			});

			event.preventDefault();
		};
		//end of functions

		popupOpen.on('click', searchPopup);
	};

	$(function () {
        app.searchForm();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._));