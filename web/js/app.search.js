$(document).ready(function() {

  $('.search-form').bind('submit', function(e) {
    e.preventDefault()

    var form = $(this)

    form.ajaxSubmit({
      async: false,
      success: function(response) {
        console.info(response);
        if (true === response.success) {
          form.unbind('submit')
          form.submit()
        }
        else {
          var el = $(response.data.content)

          el.appendTo('body')
          $('#search_popup-block').lightbox_me({
            centered: true,
            onLoad: function() {
              //$(this).find('input:first').focus()
            }
          })
        }
      }
    })
  })

  $('#filter_product_type-form').bind('click', function(e) {
    var form = $(this)
    var el = $(e.target)

    if ('product_types[]' == el.prop('name')) {
      form.submit()
    }
  })

})