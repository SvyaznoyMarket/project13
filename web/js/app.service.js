$(document).ready(function() {
    $('.info').find('h3').css('cursor', 'pointer')
    $('.info').delegate('.info h3', 'click', function(e) {
        var el = $(this)
        el.closest('.info').find('> div').toggle()
    })
})