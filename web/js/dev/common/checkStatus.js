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

           var
               $el = $(this),
               $template = $('#tpl-order-statusForm'),
               $form = Mustache.render($template.html(), {action: $el.data('url')});

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
        var $form = $(this);

        e.preventDefault();

        if (input.val() == ''){
            input.addClass('is-empty');
            return false;
        }

        container.addClass('is-loading');

        $.post($form.attr('action'), $form.serializeArray())
            .done(function(response) {
                var order = response.order;

                container
                    .removeClass('is-loading')
                    .addClass('is-info');

                if (response.errors && response.errors.length) {
                    statusBlock
                        .addClass('is-error')
                        .find('.js-order-error').html(response.errors[0].message);

                } else if (order.status && order.status.name) {
                    statusBlock.removeClass('is-error');
                    container.find('.js-order-number').html(order.number);
                    container.find('.js-order-url').attr('href', order.url);
                    container.find('.js-order-status').html(order.status.name);
                } else {
                    statusBlock
                        .addClass('is-error')
                        .find('.js-order-error').html('Заказ ' + order.number + ' не найден');
                }
            });
    });

    $body.on('click', '.js-order-status-back', function(e){
        e.preventDefault();
        container.removeClass('is-info');
        statusBlock.removeClass('is-error');
    });

    $body.on('blur', '.js-order-status-submit', function(e){
        if(input.hasClass('is-empty')){
            input.removeClass('is-empty');
        }
    })
})();