$(function() {
    $('body').on('updateWidgets', function(e, widgetAndCallbackObj){

        $.each(widgetAndCallbackObj.widgets, function(id, value) {

            var oldNode = document.querySelector(id),
                newNode = $(value)[0];

            console.info('replace ' + id +' with ' + value);

            oldNode.parentNode.replaceChild(newNode, oldNode);
        });

        if (typeof widgetAndCallbackObj.callback == 'function') {
            console.info('call callback ' + widgetAndCallbackObj.callback);
            widgetAndCallbackObj.callback();
        }

    });
});