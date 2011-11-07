$(document).ready(function() {

  $('.open_auth-link').bind('click', function(e) {
    e.preventDefault()

    var el = $(this)
    window.open(el.attr('href'), 'oauthWindow', 'status = 1, width = 540, height = 420').focus()
  })

  $('#auth-link').click(function() {
    $('#auth-block').lightbox_me({
      centered: true,
      onLoad: function() {
        $('#auth-block').find('input:first').focus()
      }
    })

    return false
  })
  
  $('#signin_password').keypress(function(e) { 
		var s = String.fromCharCode( e.which )		
		var cln = $('<strong id="capslock" style="border: 1px solid red; color: red; border-radius: 3px 3px 3px 3px; background: none repeat scroll 0% 0% white; position: absolute; height: 16px; padding: 1px 3px; margin-top: 2px; margin-left: -78px;">CAPS LOCK</strong>')		
		if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
			if( !$('#capslock').length ) $(this).after(cln)
		} else {
			if( $('#capslock').length ) $('#capslock').remove()
		}			
  })
  $('#signin_password').keyup(function(e) { 
  		var rwn = $('<strong id="ruschars" style="border: 1px solid red; color: red; border-radius: 3px 3px 3px 3px; background: none repeat scroll 0% 0% white; position: absolute; height: 16px; padding: 1px 3px; margin-top: 2px; margin-left: -36px;">RUS</strong>')
  		if( /[а-яА-ЯёЁ]/.test( $(this).val() ) ) {
			if( !$('#ruschars').length ) $(this).after(rwn)
		} else { 
			if( $('#ruschars').length ) $('#ruschars').remove()
		}
  })

  $('#login-form, #register-form').bind('submit', function(e) {
    e.preventDefault()

    var form = $(e.target)

    form.find('[type="submit"]:first')
      .attr('disabled', true)
      .val('login-form' == form.attr('id') ? 'Вхожу...' : 'Регистрируюсь...')

    form.ajaxSubmit({
      async: false,
      data: {
        redirect_to: form.find('[name="redirect_to"]:first').val()
      },
      success: function(response) {
        if (true == response.success)
        {
          form.unbind('submit')
          form.submit()
        }
        else {
          form.html($(response.data.content).html())
        }
      }
    })
  })

	$('#forgot-pwd-trigger').click(function(){
		$('#reset-pwd-form').show();
		$('#reset-pwd-key-form').hide();
		$('#login-form').hide();
		return false;
	})

	$('#remember-pwd-trigger,#remember-pwd-trigger2').click(function(){
		$('#reset-pwd-form').hide();
		$('#reset-pwd-key-form').hide();
		$('#login-form').show();
		return false;
	})

	$('#reset-pwd-form').submit(function(){
		var form = $(this);
		form.find('.error_list').html('');
		$.post(form.prop('action'), form.serializeArray(), function(resp){
			if (resp.success == true) {

				$('#reset-pwd-form').hide();
				$('#login-form').show();
				alert('Новый пароль был вам выслан по почте или смс');

			} else {
				form.find('.error_list').html('Вы ввели неправильные данные');
			}
		}, 'json');

		return false;
	})

//	$('#reset-pwd-key-form').submit(function(){
//		var form = $(this);
//		form.find('.error_list').html('');
//		$.post(form.prop('action'), form.serializeArray(), function(resp){
//			if (resp.success == true) {
//
//				$('#reset-pwd-form').hide();
//				$('#reset-pwd-key-form').hide();
//				$('#login-form').show();
//				alert('Новый пароль был вам выслан по почте или смс');
//
//			} else {
//				form.find('.error_list').html('Вы ввели неправильный ключ');
//			}
//		}, 'json');
//
//		return false;
//	})
})