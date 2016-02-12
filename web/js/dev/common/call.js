/**
 * Created by alexandr.anpilogov on 08.02.16.
 */

(function(){
    var 
        body = $('body'),
        callback = $('.js-callback-button'),
        callBackTxt = $('.js-callback-button-txt'),
        callbackInput = $('.js-callback-input'),
        callbackError = $('.js-callback-error'),
        popup = $('.js-callback-popup'),
        popupClose = $('.js-callback-popup-close'),
        popupContent = $('.js-callback-popup-content'),
        classError = 'is-error'
        duration = 26000,
        durationDel = 4000,
        isActiveTime = 6000,

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
        },

        responseForm = function( e ) {
            e.preventDefault();

            var
                el = $(e.target),
                data = el.serializeArray();

            $.post(el.attr('action'), data).done(function(response) {
                if ( typeof response.errors != 'undefined' && response.errors.length > 0 ) {
                    for ( i = 0; i < response.errors.length; i++ ) {
                        callbackError.text(response.errors[i].message);
                        $('.js-callback-input[data-field="' + response.errors[i].field + '"]').addClass(classError);
                    }
                } else {
                    popup.removeClass('is-active');
                }

                return false;
            });
        },

        unMarkField = function( e ) {
            var
                el = $(e.target);

            el.removeClass(classError);
            el.closest('.js-callback-field').find('.js-callback-error').text('');
        };

    // добавляем маску для поля ввода телефона
    $.mask.definitions['n'] = '[0-9]';
    $('.js-callback-phone').mask('+7 (nnn) nnn-nn-nn');

    setInterval(isActiveTxt, duration);
    setTimeout(isActive, isActiveTime);

    callback.on('click', isActivePopup);
    popup.on('click', isDelPopup);
    popupClose.on('click', isDelPopup);
    body.on('submit', '.js-callback-form', responseForm);
    callbackInput.on('focus', unMarkField);

    $(window).on('keydown', function(e){
        if( e.keyCode === 27 ) {
            isDelPopup(e);
        }
    });

    popupContent.on('click', function(e){
        e.stopPropagation();
    });
})();