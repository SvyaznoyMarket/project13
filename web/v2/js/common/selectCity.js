/**
 * Окно смены региона
 *
 */
;(function (app, window, $, _, undefined) {

	app.selectCity = function() {
		var body = $('body'),
			popupCity = $('.popupBox'),
			jsSelectCity = $('.jsSelectCity');
		// end of vars	

		var
		/**
		 * Показываем попап выбора города
		*/
		selectCityPopup = function selectCityPopup() {
			var topPopup = $('.header').height() + 20;

			popupCity.enterPopup({
				popupCSS : {top: topPopup, marginTop: 0}
			});
		};
		//end of functions

		jsSelectCity.bind('click', selectCityPopup);
	};

	$(function () {
        app.selectCity();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._));