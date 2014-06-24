define(
    ['jquery', 'jquery.popup'],
    function ($) {
        var $body = $('body'),

        showPopup = function (e) {
            e.stopPropagation();

            console.log('showPopup');

            $('.js-searchWindow').enterPopup({
                popupCSS : {top: $('.header').height() + 20, marginTop: 0},
                closeBtn: false
            });

            e.preventDefault();
        };

        $body.on('click', '.js-searchLink', showPopup);

        $('.js-search-form').on('submit', function(e) {
            var $input = $($(e.target).data('inputSelector'));

            if ($input.length && ($input.val().length < 3)) { // FIXME: вынести в data-атрибут
                e.preventDefault();
            }
        })
    }
);