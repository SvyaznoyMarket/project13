$(function() {
    $('body').on('updateWidgets', function(e, widgets){
        $.each(widgets, function(id, value) {
            $(id).replaceWith(value);
        })
    });
});