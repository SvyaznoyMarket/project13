$(document).ready(function() {

  $('.order-form').bind({
    'change': function(e, effect) {

      var form = $(this)
      var hidden = []

      if (undefined == effect) {
        effect = true
      }

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

      function checkPersonType() {
        var d = $.Deferred();

        // если изменился тип лица (юридическое, физическое)
        if ('order[person_type]' == $(e.target).attr('name')) {
          var el = form.find('[name="order[person_type]"]:checked')
          if (el.length) {
            effect = $('.form-row[data-field="delivery_type_id"]').is(':visible')

            $.post(form.data('updateFieldUrl'), {
              order: {
                person_type: el.val()
              },
              field: 'delivery_type_id'
            }, function(result) {
              if (true === result.success) {
                $('.form-row[data-field="delivery_type_id"] .content').hide('fast', function() {
                  $('.form-row[data-field="delivery_type_id"]').replaceWith(result.data.content)
                  d.resolve()
                })
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

      $.when(checkPersonType())
      .then(function() {
        form.find('.form-row').each(function(i, el) {
          var el = $(el)

          if (-1 == $.inArray(el.data('field'), hidden)) {
            if (true == effect) {el.show('fast')} else {el.show()}
          }
          else {
            if (true == effect) {el.hide('fast')} else {el.hide()}
          }
        })
      })

    }
  })



  $('.order_user_address').bind('change', function(e) {
    var el = $(this)

    $('[name="order[address]"]').val(el.val())
  })



  $('.order-form').trigger('change', [false])

})