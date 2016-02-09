/**
 * Created by alexandr.anpilogov on 08.02.16.
 */

(function(){
    var callback = $('.js-callback-button'),
        callBackTxt = $('.js-callback-button-txt'),
        counter = 1,
        duration = 20000,
        durationDel = 5000,
        time = 5000,

        isActive = function(time){
            callback.addClass('is-active');
        },

        isActiveTxt = function(){

            duration = 40000;

            if(callBackTxt.hasClass('is-hide')){
                callBackTxt.removeClass('is-hide');
            }

            callBackTxt.addClass('is-active');

            setTimeout(isDelTxt, durationDel)
        },

        isDelTxt = function(){
            callBackTxt.removeClass('is-active').addClass('is-hide');
        };

    setInterval(isActiveTxt, duration);

    setTimeout(isActive, time);
})();

