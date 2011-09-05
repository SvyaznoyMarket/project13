$(document).ready(function() {

  $('.open_auth-link').bind('click', function(e) {
    e.preventDefault()

    var el = $(this)
    window.open(el.attr('href'), 'oauthWindow', 'status = 1, width = 540, height = 420').focus()
  })

})