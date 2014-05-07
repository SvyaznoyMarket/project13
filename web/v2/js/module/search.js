define(
    ['jquery'],
    function ($) {

        searchForm = function() {
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

		console.log('show search popup app');
        searchForm();

    }
);