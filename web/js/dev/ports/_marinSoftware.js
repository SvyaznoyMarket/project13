ANALYTICS.marinSoftwarePageAddJS = function( callback ) {

    console.info('marinSoftwarePageAddJS');

    var mClientId ='7saq97byg0';

    $LAB.script('//tracker.marinsm.com/tracker/async/' + mClientId + '.js' ).wait(callback);
};

ANALYTICS.marinLandingPageTagJS = function() {
    var marinLandingPageTagJSHandler = function marinLandingPageTagJSHandler() {
        console.info('marinLandingPageTagJS run');

        var _mTrack = window._mTrack || [];

        _mTrack.push(['trackPage']);

        console.log('marinLandingPageTagJS complete');
    };
    // end of functions

    this.marinSoftwarePageAddJS(marinLandingPageTagJSHandler);
};

ANALYTICS.marinConversionTagJS = function() {
    var marinConversionTagJSHandler = function marinConversionTagJSHandler() {
        console.info('marinConversionTagJS run');

        var ordersInfo = $('#marinConversionTagJS').data('value'),
            _mTrack = window._mTrack || [];
        // end of vars

        if ( 'undefined' === typeof(ordersInfo) ) {
            return;
        }

        _mTrack.push(['addTrans', ordersInfo]);
        _mTrack.push(['processOrders']);

        console.log('marinConversionTagJS complete');
    };
    // end of functions

    this.marinSoftwarePageAddJS(marinConversionTagJSHandler);
};