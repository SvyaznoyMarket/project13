/**
 * Created by alexandr.anpilogov on 08.02.16.
 */

(function(){
    var callback = $('.js-callback-button'),
        callBackTxt = $('.js-callback-button-txt'),
        counter = 1,
        duration = 26000,
        durationDel = 4000,
        isActiveTime = 6000,
        popup = $('.js-callback-popup'),
        popupClose = $('.js-callback-popup-close'),
        popupContent = $('.js-callback-popup-content'),

        isActive = function(time){
            callback.addClass('is-active');
        },

        isActiveTxt = function(){

            duration = 40000;

            if(callBackTxt.hasClass('is-hide')){
                callBackTxt.removeClass('is-hide');
            }

            callBackTxt.addClass('is-active');

            setTimeout(isDelTxt, durationDel);
        },

        isDelTxt = function(){
            callBackTxt.removeClass('is-active').addClass('is-hide');
        },

        isActivePopup = function(e){

            e.preventDefault();

            popup.addClass('is-active');
        },
        isDelPopup = function(e){

            e.preventDefault();

            popup.removeClass('is-active');
        };

    setInterval(isActiveTxt, duration);

    setTimeout(isActive, isActiveTime);

    callback.on('click', isActivePopup);

    popup.on('click', isDelPopup);

    $(window).on('keydown', function(e){
        if( e.keyCode === 27 ) {
            isDelPopup(e);
        }
    });

    popupContent.on('click', function(e){
        e.stopPropagation();
    });

    popupClose.on('click', isDelPopup);

})();