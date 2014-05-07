define(
    ['jquery'],
    function ($) {

        selectCity = function() {
            var body = $('body'),
                popupCity = $('.jsCitySelectBox'),
                popupOpen = $('.jsSelectCity');
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

            popupOpen.on('click', selectCityPopup);
        };

        selectCity();
    }
);