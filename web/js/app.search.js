$(document).ready(function() {

  $('.search-form').bind('submit', function(e) {
    e.preventDefault()

    var form = $(this)

    if (form.find('input:[name="q"]').val().length < 2)
      return
    
    if( form.find('input:[name="q"]').val() === 'Поиск среди 20 000 товаров' )
	   return

    form.ajaxSubmit({
      async: false,
      success: function(response) {
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

  $('.bCtg__eMore').bind('click', function(e) {
    e.preventDefault()

    var el = $(this)

    el.parent().find('li.hf').slideToggle()

    var link = el.find('a')
    link.text('еще...' == link.text() ? 'скрыть' : 'еще...')
  })

});