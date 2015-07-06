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

    // скрол на нижнем баннере
    $body.on('click', '.jsSlidesWideButton', function(e) {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'scroll',
            label: 'collection'
        });
    });

    // клик по бренду
    $body.on('click', '.jsMainBrand', function(e) {
        $body.trigger('trackGoogleEvent', {
            category: 'brand_main',
            action: $(this).attr('title'),
            label: ''
        });
    });

    // клик по трастфактору
    $body.on('click', '.jsShopInfoPreview', function(){
        $body.trigger('trackGoogleEvent', {
            category: 'trust_main',
            action: $(this).data('name'),
            label: ''
        });
    });
});