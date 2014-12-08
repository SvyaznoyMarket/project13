;(function($) {

    var
        $body = $('body'),

        TLT = (typeof this.TLT === 'object') ? this.TLT : null,

        TLT_logCustomEvent = function(event, TLT_eventName, TLT_eventData) {
            try {
                console.info('TLT_logCustomEvent', TLT_eventName, TLT_eventData);

                TLT.logCustomEvent(TLT_eventName, TLT_eventData);
            } catch (e) {
                console.error(e);
            }
        },

        TLT_processDOMEvent = function(event, originalEvent) {
            try {
                console.info('TLT_processDOMEvent', originalEvent);

                TLT.processDOMEvent(originalEvent);
            } catch (e) {
                console.error(e);
            }
        }
    ;

    $body.on('TLT_logCustomEvent', TLT_logCustomEvent);
    $body.on('TLT_processDOMEvent', TLT_processDOMEvent);

})(jQuery);