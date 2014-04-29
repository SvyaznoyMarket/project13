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
			console.log('show search popup');

			event.preventDefault();

			var topPopup = $('.header').height() + 20;

			popupSearch.enterPopup({
				popupCSS : {top: 0, marginTop: 0}
			});
		};
		//end of functions

		popupOpen.on('click', searchPopup);
	};

	$(function () {
		console.log('show search popup app');
        app.searchForm();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._));