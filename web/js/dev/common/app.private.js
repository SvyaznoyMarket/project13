/**
 * Обработчи для личного кабинета
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
  var checked = false;

  var handleSubscribeSms = function() {
    if ( checked ) {
      $('#mobilePhoneWrapper').hide();
      checked = false;
    } else {
      $('#mobilePhoneWrapper').show();
      checked = true;
    }
  };

  $(document).ready(function(){
    checked = $('.smsCheckbox').hasClass('checked');
    if ( !$('#user_mobile_phone').val() ) {
      $('.smsCheckbox').bind('click', handleSubscribeSms);
    }
  });
}());


