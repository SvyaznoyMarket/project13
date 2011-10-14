$(document).ready(function() {

    $('.open_auth-link').bind('click', function(e) {
        e.preventDefault()

        var el = $(this)
        window.open(el.attr('href'), 'oauthWindow', 'status = 1, width = 540, height = 420').focus()
    })
  
    $('#auth-link').click(function() {
        $('#auth-block').lightbox_me({
            centered: true, 
            onLoad: function() { 
                $('#auth-block').find('input:first').focus()
            }
        })
        
        return false
    })
})