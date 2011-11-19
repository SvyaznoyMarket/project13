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

	;(function($) {
		$.fn.warnings = function() {
			var rwn = $('<strong id="ruschars" class="pswwarning">RUS</strong>')
			rwn.css({
				'border': '1px solid red',
				'color': 'red',
				'border-radius': '3px',
				'background-color':'#fff',
				'position': 'absolute',
				'height': '16px',
				'padding': '1px 3px',
				'margin-top': '2px'
			})
			var cln = rwn.clone().attr('id','capslock').html('CAPS LOCK').css('marginLeft', '-78px')

			$(this).keypress(function(e) {
				var s = String.fromCharCode( e.which )
				if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
					if( !$('#capslock').length ) $(this).after(cln)
				} else {
					if( $('#capslock').length ) $('#capslock').remove()
				}
		  })
		  $(this).keyup(function(e) {
				if( /[а-яА-ЯёЁ]/.test( $(this).val() ) ) {
					if( !$('#ruschars').length ) {
						if( $('#capslock').length )
							rwn.css('marginLeft','-116px')
						else
							rwn.css('marginLeft','-36px')
						$(this).after(rwn)
					}
				} else {
					if( $('#ruschars').length ) $('#ruschars').remove()
				}
		  })
		}
	})(jQuery);

  $('#signin_password').warnings()

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

	$('#reset-pwd-form, #auth_forgot-form').submit(function(){
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