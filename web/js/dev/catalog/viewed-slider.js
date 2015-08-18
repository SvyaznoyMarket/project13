;(function() {
    // SITE-5928
    var
        $body = $('body'),

        getSlider = function() {
            return $('.js-slider').filter('[data-position="Viewed"]').last()
        },

        init = function() {
            if (('1' == docCookies.getItem('infScroll')) || !docCookies.hasItem('infScroll')) {
                getSlider().hide();
            } else {
                getSlider().show();
            }
        }
    ;

    $body.on('infinityScroll', function(e, data) {
        console.info('infinityScroll', data, ('enabled' === data.state) && (data.page < data.lastPage));
        if (
            ('enabled' === data.state)
            && (data.page < data.lastPage)
        ) {
            getSlider().hide();
            console.info('slider.hide');
        } else {
            getSlider().show();
            console.info('slider.show');
        }
    });

    $body.on('sliderLoaded', function(data) {
        if ('viewed' === data.type) {
            init();
        }
    });

    init();
}());