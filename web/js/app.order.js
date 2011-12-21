$(document).ready( function() {

function printPrice ( val ) {
	var float = (val+'').split('.')
	var out = float[0]
	var le = float[0].length
	if( le > 6 ) { // billions
		out = out.substr( 0, le - 6) + ' ' + out.substr( le - 6, le - 4) + ' ' + out.substr( le - 3, le )
	} else if ( le > 3 ) { // thousands
		out = out.substr( 0, le - 3) + ' ' + out.substr( le - 3, le )
	}
	if( float.length == 2 )
		out += '.' + float[1]
	return out
}

function addDlvrInBill( innertxt ) {
	var rubltmpl = $('<span class="rubl">p</span>')
	var dtmp  = innertxt.split(',')
	var pritm = 0
	if ( dtmp[1].match(/\d+/) )
		pritm = dtmp[1].match(/\d+/)[0]

	var total = $('div.cheque div.total').find('strong').text().replace(/\D+/g, '') * 1 + pritm * 1
	if( $('#dlvrbill').length ) {
		total -= $('#dlvrbill').find('strong').text().replace(/\D+/g, '') * 1
		$('#dlvrbill').remove()
	}
	if( pritm ) {
		var dlvrline = $('<li>').attr('id', 'dlvrbill')
								.append( $('<div>').text( dtmp[0] ) )
								.append( $('<strong>').text( printPrice( pritm ) + ' ').append( '<span class="rubl">p</span>' ) )

		$('div.cheque ul').append( dlvrline )
	}
	$('div.cheque div.total').find('strong').empty().text( printPrice( total ) + ' ').append( rubltmpl )
}

function triggerDelivery( i ) {
	if ( i == 3 ) {
		$('.shop_block').show()
		$('.delivery_block').hide()
		$('.deliverytext').html('Представьтесь:')
        $('#delivered_at_block label').html('Выберите дату:')
	} else {
		$('.shop_block').hide()
		$('.delivery_block').show()
		$('.deliverytext').html('Кому и куда доставить:')
        $('#delivered_at_block label').html('Выберите дату доставки:')
	}
	$('#order_shop_id').trigger('change')
}

var checker = $('.order-form').find('[name="order[delivery_type_id]"]:checked')
triggerDelivery( checker.val() )

$('.order-form').change( function(e) {
        var form = $(this)
        
        if ('order[shop_id]' == $(e.target).attr('name')) {
        	var el = $(e.target).find('option:selected')
        	if (!el.length)
          		return
          	$.post(form.data('updateFieldUrl'), {
				  order: {
				  	delivery_type_id: form.find('[name="order[delivery_type_id]"]:checked').val(),
					shop_id: el.val()
				  	},
				  field: 'delivered_at'
				  }, function(result) {
					if (false === result.success) {
						
					}
					var toupdate = form.find('[name="order[delivered_at]"]')
					toupdate.empty()
					$.each(result.data.content, function(v, n) {
					  toupdate.append('<option value="'+v+'">'+n+'</option>')
					})
					toupdate.find(':first').attr('selected', 'selected')
					toupdate.change()
				})
        }
        
        if ('order[region_id]' == $(e.target).attr('name')) {
          var el = $(e.target).find('option:selected')
          var formreg = $('form#region')
          formreg.attr('action', el.data('url'))
          formreg.submit()
        }
        
        if ('order[delivery_type_id]' == $(e.target).attr('name')) {
          var el = form.find('[name="order[delivery_type_id]"]:checked')
          if (el.length) {
          	addDlvrInBill( el.next().find('strong').text() )
			triggerDelivery( el.val() )
            $.post(form.data('updateFieldUrl'), {
              order: {
                delivery_type_id: el.val()
              },
              field: 'delivery_period_id'
            }, function(result) {
              if ( typeof( result.success ) !== 'undefined' && result.success ) {
                var select = $('[name="order[delivery_period_id]"]')
                select.empty()
                $.each(result.data.content, function(v, n) {
                  select.append('<option value="'+v+'">'+n+'</option>')
                })
                select.find(':first').attr('selected', 'selected')
                select.change()
              }              
            }, 'json')            
          }
        }        
  })

  $('#order_shop_id').trigger('change')


  $('.order_user_address').bind('change', function(e) {
    var el = $(this)

    $('[name="order[address]"]').val(el.val())
  })

  $('#basic_register-form').bind({
    'submit': function(e) {
      e.preventDefault()

      var form = $(this)

      form.ajaxSubmit({
        'beforeSubmit': function() {
          var button = form.find('input:submit')
          button.attr('disabled', true)
          button.attr('value', 'Запоминаю...')
        },
        'success': function(response) {
          if (true !== response.success) {
            form.find('.form-content:first').html(response.data.form)
          }
          else {
            window.location = response.redirect
          }
        },
        'complete': function() {
          var button = form.find('input:submit')
          button.attr('disabled', false)
          button.attr('value', 'Запомнить меня')
        }
      })
    }
  })


  $('.auth-link').bind('click', function(e) {
    e.preventDefault()

    var link = $(this)

    $('#login-form, #register-form').data('redirect', false)
    $('#auth-block').lightbox_me({
      centered: true,
      onLoad: function() {
        $('#auth-block').find('input:first').focus()
      },
      onClose: function() {
        $.get(link.data('updateUrl'), function(response) {
          if (true === response.success) {
            var form = $('.order-form')
            $('#user-block').replaceWith(response.data.content)

            $.each(response.data.fields, function(name, value) {
              var field = form.find('[name="'+name+'"]')
              if (field.val().length < 2) {
                field.val(value)
              }
            })
          }
        })
      }
    })
  })


});