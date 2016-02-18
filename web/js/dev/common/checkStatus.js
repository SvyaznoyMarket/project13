/**
 * Created by alexandr.anpilogov on 16.02.16.
 */
;(function(){
   var $body = $('body'),
       overlay = '<div class="order-status-overlay js-order-status-overlay"><div>',
       input,
       container,
       popup,
       statusBlock,
       creatingPopup = function(e){
           e.preventDefault();

           var $template = $('#tpl-order-statusForm'),
               $form = Mustache.render($template.html(), {action: "/"});

           removePopup();

           $body.append($form).append(overlay);

           input = $('.js-order-name');
           container = $('.js-order-status-block');
           popup = $('.js-popup-status');
           statusBlock = $('.js-status-block');

           input.mask('kkkk-nnnnnn').focus();
       },
       removePopup = function(){
           if(popup){
               popup.remove();
               $('.js-order-status-overlay').remove();
           }
       };

    $.mask.definitions = {
        'n': '[0-9]',
        'k': '[а-яА-ЯёЁa-zA-Z]'
    };

    $body.on('click', '.js-checkStatus', creatingPopup);

    $body.on('click', '.js-order-status-del, .order-status-overlay', removePopup);

    $body.on('submit', '.js-status-order', function(e){
        e.preventDefault();

        if(input.val() == ''){
            input.addClass('is-empty');
            return false;
        }

        if(true){
            container.addClass('is-info');
            if(!false){
                statusBlock.addClass('is-error')
            }
        }
        container.addClass('is-info');
    });

    $body.on('click', '.js-order-status-back', function(e){
        e.preventDefault();
        container.removeClass('is-info');
    });

    $body.on('blur', '.js-order-status-submit', function(e){
        if(input.hasClass('is-empty')){
            input.removeClass('is-empty');
        }
    })
})();