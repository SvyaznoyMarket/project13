/**
 * Обработчик для личного кабинета
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
  var checkedSms = false;
  var checkedEmail = false;

  var handleSubscribeSms = function() {
    if ( checkedSms ) {
      $('#mobilePhoneWrapper').hide();
      $('#mobilePhoneWrapper').parent().find('.red').html('');
      checkedSms = false;
    } else {
      $('#mobilePhoneWrapper').show();
      checkedSms = true;
    }
  };

  var handleSubscribeEmail = function() {
    if ( checkedEmail ) {
      $('#emailWrapper').hide();
      $('#emailWrapper').parent().find('.red').html('');
      checkedEmail = false;
    } else {
      $('#emailWrapper').show();
      checkedEmail = true;
    }
  };

  $(document).ready(function(){
    checkedSms = $('.smsCheckbox').hasClass('checked');
    if ( !$('#user_mobile_phone').val() ) {
      $('.smsCheckbox').bind('click', handleSubscribeSms);
    }
    checkedEmail = $('.emailCheckbox').hasClass('checked');
    if ( !$('#user_email').val() ) {
      $('.emailCheckbox').bind('click', handleSubscribeEmail);
    }
  });
}());


