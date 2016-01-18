$(function(){
   var $body = $('body'),
       $mainContainer = $('#personal-container'),
       $deleteAddressPopupTemplate = $('#tpl-user-deleteOrderPopup'),

       showPopup = function(selector) {
          $('body').append('<div class="overlay"></div>');
          $('.order-list__modal').data('popup', selector).show();
          $(selector).show();
       },

       hidePopup = function(selector) {
          $(selector).remove();
          $('.js-modal').remove();
       },
       loadPaymentForm = function($container, url, data) {
           console.info('Загрузка формы оплаты ...');
           $container.html('...'); // TODO: loader

           $.ajax({
               url: url,
               type: 'POST',
               data: data
           }).fail(function(jqXHR){
               $container.html('');
           }).done(function(response){
               if (response.form) {
                   $container.html(response.form);
               }
           }).always(function(){});
       }
   ;

   $body.on('click', '.overlay', function() {
      var selector = $(this).data('popup');
      hidePopup(selector);
   });
   $body.on('click', '.js-modal-close', function() {
      hidePopup('#' + $(this).closest('.js-modal').attr('id'))
   });

   //отменить заказ
   $body.on('click', '.js-orderCancel', function() {
      var
          $el = $(this),
          data = $el.data(),
          templateValue = data.value,
          $popup;

      try {
         $popup = $(Mustache.render($deleteAddressPopupTemplate.html(), templateValue)).appendTo($mainContainer);
         showPopup('#' + $popup.attr('id'));
      } catch (error) {
         console.error(error);
      }
   });

    $('.js-payment-popup-show').on('click',function(){
        var
            $el = $(this),
            relations = $el.data('relation'),
            $container = relations.container ? $(relations.container) : null
        ;

        if ($container && $container.length) {
            $container.find('.js-payment-popup').show();
            $('body').append('<div class="payments-popup__overlay js-payment-popup-overlay"></div>');
        }
    });
    $('.js-payment-popup-closer').on('click',function(){
        $(this).parent().hide();
        $('.js-payment-popup-overlay').remove();
    });
    $body.on('click','.js-payment-popup-overlay',function(){
        $('.js-payment-popup').hide();
        $(this).remove();
    });

    $body.on('change', '.js-order-onlinePaymentMethod', function(e) {
        var
            $el = $(this),
            url = $el.data('url'),
            data = $el.data('value'),
            relations = $el.data('relation'),
            $formContainer = relations['formContainer'] && $(relations['formContainer']),
            $sumContainer = relations['sumContainer'] && $(relations['sumContainer']),
            sum = $el.data('sum')
        ;

        try {
            if (!url) {
                throw {message: 'Не задан url для получения формы'};
            }
            if (!$formContainer.length) {
                throw {message: 'Не найден контейнер для формы'};
            }

            loadPaymentForm($formContainer, url, data);

            if (sum && sum.value) {
                $sumContainer.html(sum.value);
            }
        } catch(error) { console.error(error); };

        //e.preventDefault();
    });
    try {
        $('.js-order-onlinePaymentMethod').each(function(i, el) {
            var
                $el = $(el),
                url,
                data,
                relations,
                $formContainer
            ;

            if ($el.data('checked')) {
                url = $el.data('url');
                data = $el.data('value');
                relations = $el.data('relation');
                $formContainer = relations['formContainer'] && $(relations['formContainer']);

                loadPaymentForm($formContainer, url, data);
            }
        });
    } catch (error) { console.error(error); }
});