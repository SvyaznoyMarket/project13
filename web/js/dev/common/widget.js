$(function() {
    $('body').on('updateWidgets', function(e, widgetAndCallbackObj){

        $.each(widgetAndCallbackObj.widgets, function(selector, value) {
			$(selector).each(function(i, oldNode) {
				console.info('replace ' + selector +' with ' + value);
				$(oldNode).replaceWith(value);
			});
        });

        if (typeof widgetAndCallbackObj.callback == 'function') {
            console.info('call callback ' + widgetAndCallbackObj.callback);
            widgetAndCallbackObj.callback();
        }

    });
});