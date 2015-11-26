(function(ENTER) {
    // SITE-6456
    var
        params = {}
    ;

    if (true || ('/' == window.location.pathname)) {
        params = ENTER.utils.parseUrlParams(window.location.href);
        window.docCookies.setItem('urlParams', JSON.stringify(params), 60 * 5, '/');
    }
}(window.ENTER));