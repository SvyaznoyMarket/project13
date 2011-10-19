$(document).ready(function() {

  $('#filter_product_type-form').bind('click', function(e) {
    var form = $(this)
    var el = $(e.target)

    if ('product_types[]' == el.prop('name')) {
      form.submit()
    }
  })

})