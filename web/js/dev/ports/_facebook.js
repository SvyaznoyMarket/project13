/**
 * Facebook
 */
ANALYTICS.facebookJs = function () {
    var
        $el = $('#facebookJs'),
        data = $el.data('value')
    ;

    if (!data) {
        return;
    }

    window.fbAsyncInit = function() {
        FB.init({
            appId      : data.id,
            xfbml      : true,
            version    : 'v2.3'
        });
    };

    $LAB.script({src: '//connect.facebook.net/ru_RU/sdk.js', id: 'facebook-jssdk'});
};
