/**
 * Открытие окна доставки
 *
 * @author		Zhukov Roman
 * @requires	jQuery, lightbox_me
 *
 */
(function() {

    $('.js-show-shops').on('click', function() {
        var popup = $('.shopsPopup'),
            buyButtons = $('.shopsPopup .jsBuyButton');

        popup.lightbox_me({
            centered: true,
            autofocus: true
        });

        buyButtons.on('click', function(){
            popup.trigger('close')
        });
    });

}());
