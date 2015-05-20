ANALYTICS.MyThingsJS = function() {
    var token = window.mtAdvertiserToken = '1989-100-ru',
        data = $('#MyThingsJS').data('value');

    window._mt_ready = function() {
        var obj = $.extend({}, data, {
            EventType: MyThings.Event[data.EventType]
        });
        console.log('MyThings Track Object', obj);
        if (typeof(MyThings) != "undefined") {
            MyThings.Track(obj);
        }
    };

    $LAB.script('//rainbow-ru.mythings.com/c.aspx?atok=' + token)
};