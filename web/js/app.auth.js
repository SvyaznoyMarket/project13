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

  $('#signin-form, #register-form').bind('submit', function(e) {
    e.preventDefault()

    var form = $(e.target)

    form.find('[type="submit"]:first')
      .attr('disabled', true)
      .val('signin-form' == form.attr('id') ? 'Вхожу...' : 'Регистрируюсь...')
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
	})

	$('#remember-pwd-trigger,#remember-pwd-trigger2').click(function(){
		$('#reset-pwd-form').hide();
		$('#reset-pwd-key-form').hide();
		$('#login-form').show();
	})

	$('#reset-pwd-form').submit(function(){
		var form = $(this);
		form.find('.error_list').html('');
		$.post(form.prop('action'), form.serializeArray(), function(resp){
			if (resp.success == true) {

				$('#reset-pwd-form').hide();
				$('#reset-pwd-key-form').show();

			} else {
				form.find('.error_list').html('Вы ввели неправильные данные');
			}
		}, 'json');

		return false;
	})

	$('#reset-pwd-key-form').submit(function(){
		var form = $(this);
		form.find('.error_list').html('');
		$.post(form.prop('action'), form.serializeArray(), function(resp){
			if (resp.success == true) {

				$('#reset-pwd-form').hide();
				$('#reset-pwd-key-form').hide();
				$('#login-form').show();
				alert('Новый пароль был вам выслан по почте или смс');

			} else {
				form.find('.error_list').html('Вы ввели неправильный ключ');
			}
		}, 'json');

		return false;
	})
})