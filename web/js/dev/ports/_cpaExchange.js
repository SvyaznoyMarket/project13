ANALYTICS.cpaexchangeJS = function () {
    (function () {
        var
            cpaexchange = $('#cpaexchangeJS'),
            data = cpaexchange.data('value'),
            s, b, c;
        // end of vars

        if ( !data || !$.isNumeric(data.id) ) {
            return;
        }

        s = document.createElement('script');
        b = document.getElementsByTagName('body')[0];
        c = document.createComment('HUBRUS RTB Segments Pixel V2.3');

        s.type = 'text/javascript';
        s.async = true;
        s.src = 'http://track.hubrus.com/pixel?id=' + data.id + '&type=js';
        b.appendChild(c);
        b.appendChild(s);
    })();
};