/**
 * Created by alexandr.anpilogov on 16.02.16.
 */
;(function(){
   var $body = $('body'),
       overlay = '<div class="order-status-overlay"><div>',
       creatingPopup = function(e){
           e.preventDefault();

           var $template = $('#tpl-order-statusForm'),
               $form = Mustache.render($template.html(), {action: "/"});


           removePpopup();

           $body.append($form).append(overlay);
           $('.js-order-name').mask('kkkk-nnnnnn').focus();
       },
       removePpopup = function(){
           if($body.find('.js-popup-status')){
               $body.find('.js-order-status').remove();
               $body.find('.order-status-overlay').remove();
           }
       };

    $.mask.definitions['n'] = '[0-9]';
    $.mask.definitions['k'] = '[а-яА-ЯёЁa-zA-Z]';

    $body.on('click', '.js-checkStatus', creatingPopup);

    $body.on('click', '.js-order-status-del', removePpopup);

    $body.on('click', '.order-status-overlay', removePpopup)
})();