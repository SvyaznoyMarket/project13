$(document).ready(function() {
    
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
        if ('order[delivery_type_id]' == $(e.target).attr('name')) {
          var el = form.find('[name="order[delivery_type_id]"]:checked')
          if (el.length) {

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
                //select.change()
                
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

})