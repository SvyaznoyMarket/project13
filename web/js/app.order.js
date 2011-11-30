var initOrder = function(quickform) {

quickform = quickform || false;

function triggerDelivery( i, init ) {
    //init = init || false;
	if ( i == 3 ) {
		$('.shop_block').show()
		$('.delivery_block').hide()
		$('.deliverytext').html('Представьтесь:')
        $('#delivered_at_block label').html('Выберите дату:')
        /*var ds = $('#delivered_at_block select').html(deliveryAtOptions.slice(0,4)).prepend('<option value=""></option>');
        if (init) {
            ds.change();
        } else {
            ds.val('').change();
        }*/
	} else {
		$('.shop_block').hide()
		$('.delivery_block').show()
		$('.deliverytext').html('Кому и куда доставить:')
        $('#delivered_at_block label').html('Выберите дату доставки:')
        /*var ds = $('#delivered_at_block select').html(deliveryAtOptions.slice(1)).prepend('<option value=""></option>');
        if (init) {
            ds.change();
        } else {
            ds.val('').change();
        }*/
	}
	$('#order_shop_id').trigger('change')
}
var checker = $('.order-form').find('[name="order[delivery_type_id]"]:checked')
var deliveryAtOptions = $('#delivered_at_block select option').clone();
triggerDelivery( checker.val(), true )
if (quickform) {
    $('.order-form').find('[name="order[delivery_type_id]"]').change(function(){
        triggerDelivery( $(this).val() );
    });
} else { // TODO REWRITE !!! 
$('.order-form').bind({
    'change': function(e) {
      var form = $(this)
      var hidden = []

      /*
      var el = form.find('[name="order[receipt_type]"]:checked')
      // если способ получения не доставка
      if (!el.length || ('delivery' != el.val())) {
        hidden.push(
          'delivery_type_id',
          'delivered_at',
          'address'
        )
      }
      // если способ получения не самовывоз
      if (!el.length || ('pickup' != el.val())) {
        hidden.push(
          'shop_id'
        )
      }
      */
      function checkDeliveryType() {
        var d = $.Deferred();

        // если изменился способ доставки
        if ('order[shop_id]' == $(e.target).attr('name')) { 
        	var el = $(e.target).find('option:selected')
        	if (!el.length) 
          		return
          	
        }
        if ('order[delivery_type_id]' == $(e.target).attr('name')) {
          var el = form.find('[name="order[delivery_type_id]"]:checked')
          if (el.length) {
			triggerDelivery( el.val() )
            $.post(form.data('updateFieldUrl'), {
				  order: {
				  	delivery_type_id: form.find('[name="order[delivery_type_id]"]:checked').val(),
					shop_id: el.val()
				  	},
				  field: 'delivered_at'
				  }, function(result) {
					if (false === result.success) {
						d.reject()
					}
					var toupdate = form.find('[name="order[delivered_at]"]')
					toupdate.empty()
					$.each(result.data.content, function(v, n) {
					  toupdate.append('<option value="'+v+'">'+n+'</option>')
					})
					toupdate.find(':first').attr('selected', 'selected')
					toupdate.change()
					
					d.resolve()
				}).error(function() {
              		d.reject()
            	})
            $.post(form.data('updateFieldUrl'), {
              order: {
                delivery_type_id: el.val()
              },
              field: 'delivery_period_id'
            }, function(result) {
              if (true === result.success) {
                var select = $('[name="order[delivery_period_id]"]')
                select.empty()
                $.each(result.data.content, function(v, n) {
                  select.append('<option value="'+v+'">'+n+'</option>')
                })
                select.find(':first').attr('selected', 'selected')
                select.change()

                d.resolve()
              }
              else {
                d.reject()
              }
            }, 'json')
            .error(function() {
              d.reject()
            })

          }
        }
        else {
          d.resolve()
        }

        return d.promise();
      }

      $.when(checkDeliveryType())
      .then(function() {
        /*
        form.find('.form-row').each(function(i, el) {
          var el = $(el)

          if (-1 == $.inArray(el.data('field'), hidden)) {
            if (true == effect) {el.show('fast')} else {el.show()}
          }
          else {
            if (true == effect) {el.hide('fast')} else {el.hide()}
          }
        })
        */
      })

    }
  })

	//$('#order_shop_id').trigger('change')
}

  $('.order_user_address').bind('change', function(e) {
    var el = $(this)

    $('[name="order[address]"]').val(el.val())
  })

  $('.order_region_name').autocomplete($('#order_region_id').data('url'), {
    queryArgument: 'q',
    minChars: 2,
    max: 20,
    width: 300,
    formatItem: function(item) {
      return item.name
    },
    parse: function parse(data) {
      var parsed = [];
      var rows = data.data;
      for (var i=0; i < rows.length; i++) {
        var row = rows[i]
        parsed[parsed.length] = {
          data: row,
          value: row.id,
          result: row.name
        }
      }
      return parsed;
    }
  })
  .result(function(e, item) {
    $('#order_region_id').val(item.id)
    $('.order-form').submit()
  })

}
$(document).ready( function(){ initOrder() });