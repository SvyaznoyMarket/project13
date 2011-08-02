$(document).ready(function() {

  $('.product_filter-block')
    // change
    .bind('change', function(e) {
      var el = $(e.target)

      if (el.is('input') && (-1 != $.inArray(el.attr('type'), ['radio', 'checkbox']))) {
        el.trigger('preview')
        return false
      }
    })
    // preview
    .bind('preview', function(e) {
      var el = $(e.target)
      var form = $(this)

      function disable() {
        var d = $.Deferred();
        //el.attr('disabled', true)
        return d.resolve();
      }

      function enable() {
        var d = $.Deferred();
        //el.attr('disabled', false)
        return d.promise();
      }

      function getData() {
        var d = $.Deferred();

        form.ajaxSubmit({
          url: form.data('action-count'),
          success: d.resolve,
          error: d.reject
        })

        return d.promise();
      }

      $.when(getData())
        .then(function(result) {
          if (true === result.success) {
            $('.product_count-block').remove();
            el.parent().find('> label').first().after('<div class="product_count-block" style="position: absolute; background: #fff; padding: 4px; opacity: 0.9; border-radius: 5px; border: 1px solid #ccc; cursor: pointer;">Найдено '+result.data+'</div>')
            $('.product_count-block')
              .hover(
                function() {
                  $(this).stopTime('hide')
                },
                function() {
                  $(this).oneTime(2000, 'hide', function() {
                    $(this).remove()
                  })
                }
              )
              .click(function() {
                form.submit()
              })
              .trigger('mouseout')
          }
        })
        .fail(function(error) {
          console.info(error);
        })
    })

})