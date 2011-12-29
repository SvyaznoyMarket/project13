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
/*
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

*/
	/* Actors */
	var formOrder       = $('.order-form')
	var js_container    = $('.js_container') // cheque-container
	var js_deliverydate = $('.js_deliverydate')
	var js_shoplist     = $('.js_shoplist')
	var js_address      = $('.js_address')
	var js_delivery     = $('.js_delivery')
	var js_deliverytime = $('.js_deliverytime')	
	var js_deliverytext = $('.deliverytext')
	var js_deliverylabl = $('#delivered_at_block label')	
	
	/* Events descriptions */
	var ev_dlvrtxt = [{
		type: 'dlvr1',
		pars: { txt: 'Кому и куда доставить:' }
	},{
		type: 'dlvr3',
		pars: { txt: 'Представьтесь:' }
	}]
	var ev_dlvrlbl = [{
		type: 'dlvr1',
		pars: { txt: 'Выберите дату доставки:' }
	},{
		type: 'dlvr3',
		pars: { txt: 'Выберите дату:' }
	}]
	js_deliverytext.data('actions', ev_dlvrtxt ).addClass('dynamic')	 
	js_deliverylabl.data('actions', ev_dlvrlbl ).addClass('dynamic')	 	
	
	var ev_hideshow = [{
		type: 'hide',
		pars:{ }
	},{
		type: 'show',
		pars:{ }
	}]
	js_address.data('actions', ev_hideshow )	
	js_shoplist.data('actions', ev_hideshow )	
		
	var ev_deliverytime = [{
		type : 'refresh',
		pars : {
			url: formOrder.data('updateFieldUrl')
		}
	},{
		type: 'hide',
		pars:{ }
	},{
		type: 'show',
		pars:{ }
	}]
	js_deliverytime.data('actions', ev_deliverytime )
	
	var ev_deliverydate = [{
		type : 'refresh',
		pars : {
			url: formOrder.data('updateFieldUrl')
		}
	}]
	js_deliverydate.data('actions', ev_deliverydate )
	
	var ev_rotor = [{
		type: 'replace',
		pars:{
			url: formOrder.data('updateFieldUrl')
		}
	}]
	js_container.data('actions', ev_rotor )
	js_delivery.data('actions', ev_rotor )	
	
	/* Parsing */
	function differenter( e, hash, el ) {
		hl: for(var i=0, l = hash.length; i < l; i++) {
			if( hash[i].type === e.type ) {
				var pars = hash[i].pars
		
				switch( e.type ) {
					case 'hide': 
						toHide( pars, el )
						break
					case 'show': 
						toShow( pars, el )
						break
					case 'refresh': 
						toRefresh( pars, el )
						break
					case 'replace': 
						toReplace( pars, el )
						break
					case 'error': 
						toError( pars, el )
						break
					case 'dlvr1':
						setText( pars, el )
						break
					case 'dlvr3':
						setText( pars, el )
						break
				}	
				break hl
			}
		}
	}
	;(function() {
		$('.dynamic').each( function() {
			var ev_actions = $(this).data('actions')
			if( !ev_actions )
				throw new Error('Custom error: No actions hash')
			for(var i=0, l = ev_actions.length; i < l; i++) {
				if( typeof( ev_actions[i].pars ) === 'undefined' || typeof( ev_actions[i].type ) === 'undefined' )
					throw new Error('Custom error: Wrong actions hash')
				$(this).bind( ev_actions[i].type , function(e){ differenter( e, ev_actions, $(this) ) } )
			}	
		})
	})();
	
	/* Operators */	
	function toRefresh( hash, node ) {
		if( !node.is('select') ) {
			node = node.find('select:first')
			if( !node.length )
				return false
		}	
		if( !node.is(':visible') )
			return false
		if( typeof( hash.url ) === 'undefined' )
			throw new Error('Custom error: Wrong actions hash in toRefresh')
		var fields = formOrder.serializeArray()
		fields.push({ name: 'field', value: node.attr('name').replace( /(.+)\[(.+)\]/, '$2') })
		
		$.post( hash.url, fields, function(data) {
			if( typeof(data.success) === 'undefined' || !data.success )
				return false
			node.empty()	
			$.each(data.data.content, function(v, n) { //load new html	
			  node.append('<option value="'+v+'">'+n+'</option>')
			})
			node.find(':first').attr('selected', 'selected')		
			node.parent().find('.aload').remove() // drop inicator			
		})	
		node.html('').append('<option value="" selected="selected"></option>').trigger('change')
		var ofs = node.parent().offset()
		$('<div>').attr('class','aload').css({'position':'absolute', 'top': '5px', 'left': '5px' })
				  .html('<img src="/images/ajloadersquare.gif" alt=""/>').appendTo( node.parent() ) // print indicator					
	}
	
	function toReplace( hash, node ) {
		if( typeof( hash.html ) === 'undefined' )
			throw new Error('Custom error: Wrong actions hash in toReplace')
		setTimeout( function() { node.html( hash.html ) }, 1000)
	}
	
	function toError( hash, node ) {
		console.info('error',hash)
	}
	
	function toHide( hash, node ) {
		node.hide()
	}

	function toShow( hash, node ) {
		node.show()
	}	
	
	function setText( hash, node ) {
		if( typeof( hash.txt ) === 'undefined' )
			throw new Error('Custom error: Wrong actions hash in setText')
		node.text( hash.txt )
	}
	
	/* Delegations */	
	formOrder.delegate('#order_delivered_at','change', function() {
		js_deliverytime.trigger('refresh')
	})	
	
	formOrder.delegate('#order_shop_id','change', function() {
		js_deliverydate.trigger('refresh')
	})	
	
	formOrder.delegate('.js_delivery','change', function() {	
		if( $(this).val() == 3 ) { // self
			js_deliverydate.trigger('refresh')
			js_deliverytime.trigger('hide')			
			js_shoplist.trigger('show')
			js_address.trigger('hide')
			js_deliverytext.trigger('dlvr3')
			js_deliverylabl.trigger('dlvr3')
		} else {			
			js_deliverydate.trigger('refresh')
			js_deliverytime.trigger('show')	
			js_shoplist.trigger('hide')
			js_address.trigger('show')
			js_deliverytext.trigger('dlvr1')
			js_deliverylabl.trigger('dlvr1')			
		}
		addDlvrInBill( $(this).next().find('strong').text() )
	})
	
	formOrder.delegate('#order_region_id','change', function() {
		var formreg = $('form#region')
        formreg.attr('action', $(this).find('option:selected').data('url'))
        formreg.submit()
		/*js_deliverydate.trigger('refresh')
		js_shoplist.trigger('refresh')
		js_container.trigger('refresh')
		js_delivery.trigger('replace')*/
	})

	/* form initialize */
	var inin = $('.js_delivery:checked')
	if( inin.length ) {
		inin.trigger('change')
	}
	
	/* Validator */
	formOrder.submit( function(e){
		var tosubmit = false
		console.info( formOrder.serializeArray()  )
		if( !tosubmit )
			e.preventDefault()			
	})

/* ---- */
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

	
	;( function() {
		var j_count = $('.timer')
		if( !j_count.length ) 
			return false
		var interval = window.setInterval( sec5run, 1000)
		var secs = j_count.html().replace(/\D/g,'') * 1
		function sec5run() {
			if( secs === 1 ) {
				clearInterval( interval )
				$('form').submit()
			}
			secs -= 1
			j_count.html( secs )
		}
	})();
});