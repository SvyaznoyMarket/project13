$(function(){
   var $body = $('body'),
       $mainContainer = $('#personal-container'),
       $deleteAddressPopupTemplate = $('#tpl-user-deleteOrderPopup'),
       $form = $('.js-userAddress-form'),

       showPopup = function(selector) {
          $('body').append('<div class="overlay"></div>');
          $('.order-list__modal').data('popup', selector).show();
          $(selector).show();
       },

       hidePopup = function(selector) {
          $(selector).remove();
          $('.js-modal').remove();
       };

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
});