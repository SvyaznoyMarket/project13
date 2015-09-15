$(function() {
    var
        $body = $('body')
    ;

    // клик на главном баннере
    $body.on('click', '.jsMainBannerLink', function() {

        var $el = $(this),
            data = {
                id: $el.data('uid'),
                name: $el.data('name'),
                position: 1 + $el.data('position')
            };

        // Старое событие
        /*
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'click',
            label: 'banner'
        });
        */

        if (!ENTER.utils.analytics.isEnabled()) return;

        /* Cобытие с использованием e-commerce */
        ga('ec:addPromo', data);
        ga('ec:setAction', 'promo_click');
        $body.trigger('trackGoogleEvent', {
            category: 'Internal Promotions',
            action: 'click',
            label: data.name,
            hitCallback: $el.attr('href')
        })
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