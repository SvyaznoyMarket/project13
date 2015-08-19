ANALYTICS.MyThingsJS = function() {
    /* Необходимые переменные в глоб. области: mtAdvertiserToken, mtHost, _mt_ready */
    var token = window.mtAdvertiserToken = '1989-100-ru',
        data = $('#MyThingsJS').data('value');

    window.mtHost = (("https:" == document.location.protocol) ? "https" : "http") + "://rainbow-ru.mythings.com";

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