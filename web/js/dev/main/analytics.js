$(function() {
    var
        $body = $('body')
    ;

    // клик на главном баннере
    $body.on('click', '.jsMainBannerLink', function() {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'click',
            label: 'banner'
        });
    });

    // скролл на главном баннере
    $body.on('click', '.jsMainBannerThumb, .jsMainBannersButton', function(e) {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'scroll',
            label: 'banner'
        });
    });

    // клик на нижнем баннере
    $body.on('click', '.jsSlidesWideItem', function() {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'click',
            label: 'collection'
        });
    });

    // скролл на нижнем баннере
    $body.on('click', '.jsSlidesWideButton', function(e) {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'scroll',
            label: 'collection'
        });
    });
});