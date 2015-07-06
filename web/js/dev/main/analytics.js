$(function() {
    var
        $body = $('body')
    ;

    // клик на баннере
    $body.on('click', '.jsMainBannerLink', function() {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'click',
            label: 'banner'
        });
    });

    // скролл баннера
    $body.on('click', '.jsMainBannerThumb, .jsMainBannersButton', function(e) {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'scroll',
            label: 'banner'
        });
    });
});