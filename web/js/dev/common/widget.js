$(function() {
    $('body').on('updateWidgets', function(e, widgets){
        $.each(widgets, function(id, value) {
            console.info('replace ' + id +' with ' + value);
            $(id).html($(value).html());
        })
    });
});