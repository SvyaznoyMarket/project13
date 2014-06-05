define(
    ['jquery', 'jquery.popup'],
    function ($) {
        var $body = $('body'),

            showPopup = function (e) {
                e.stopPropagation();

                console.log('showPopup');

                $('.js-searchWindow').enterPopup({
                    popupCSS : {top: $('.header').height() + 20, marginTop: 0}
                });

                e.preventDefault();
            }
        ;

        $body.on('click', '.js-searchLink', showPopup);
    }
);