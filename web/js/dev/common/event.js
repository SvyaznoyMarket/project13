;$(function(){
    var
        $body = $('body')
    ;

    $('.jsEvent_documentReady').each(function(i, el) {
        var
            event = $(el).data('value')
        ;

        if (!event.name) return;
        
        $body.trigger(event.name, event.data || []);
        console.info('event', event.name, event.data);
    });
});