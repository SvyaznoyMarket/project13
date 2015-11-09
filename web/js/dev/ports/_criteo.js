ANALYTICS.criteoJS = function() {
    console.log('criteoJS');
    window.criteo_q = window.criteo_q || [];
    var criteo_arr =  $('#criteoJS').data('value');
    if ( typeof(criteo_q) != 'undefined' && !jQuery.isEmptyObject(criteo_arr) ) {
        try{
            window.criteo_q.push(criteo_arr);
        } catch(e) {
        }
    }
};