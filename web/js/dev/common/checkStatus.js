/**
 * Created by alexandr.anpilogov on 16.02.16.
 */
;(function(){
   var $body = $('body');
    $.mask.definitions['n'] = '[0-9]';
    $.mask.definitions['k'] = '[а-яА-ЯёЁa-zA-Z]';

    $body.on('click', '.js-checkStatus', function(e){
        e.preventDefault();

        if($body.find('.js-popup-status')){
            $body.find('.js-popup-status').remove();
        }

        var $template = $('#tpl-order-statusForm'),
            $form = Mustache.render($template.html(), {action: "/"});

        $body.append($form);
        $('.js-order-name').mask('kkkk-nnnnnn').focus();
    });

    $body.on('click', '.js-order-status-del', function(){
        $body.find('.js-order-status').remove();
    });


})();