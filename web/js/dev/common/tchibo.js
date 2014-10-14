;(function(){

    // https://jira.enter.ru/browse/SITE-3508
    // SITE-3508 Закрепить товары в листинге чибы

    if (/catalog\/tchibo/.test(document.location.href) && window.history && window.history.replaceState) {

        var history = window.history;

        $(window).on('beforeunload', function () {
            history.replaceState({pageYOffset: pageYOffset}, '');
        });

        if (history && history.state && history.state.pageYOffset) {
            window.scrollTo(0, history.state.pageYOffset);
        }

    }

}());