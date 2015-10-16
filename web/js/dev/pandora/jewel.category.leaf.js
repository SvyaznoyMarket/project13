$(document).ready(function() {

    if($('body.jewel .allpagerJewel').length == 0) return;

    /* Infinity scroll */
    var ableToLoadJewel = true,
        preloaderClass = 'mLoader',
        $preloader = $('<li />').addClass(preloaderClass),
        checkScrollJewel = function(){},
        liveScrollJewel = function( lsURL, filters, pageid ) {
            var params = [],
                tmpnodeJewel = $('.js-jewel-category');

            tmpnodeJewel.append($preloader);

            if ( lsURL.match(/\?/) ) {
                lsURL += '&page=' + pageid;
            } else {
                lsURL += '?page=' + pageid;
            }

            $.get( lsURL, params, function(data){
                if ( data != "" && !data.data ) { // JSON === error
                    ableToLoadJewel = true;
                    tmpnodeJewel.append(data.products);
                }
                $('.' + preloaderClass).remove();
            });
        },
        switch_to_scroll = function(checkScrollJewel) {

            var next = $('div.pageslist:first li:first');

            window.docCookies.setItem('infScroll', 1, 4*7*24*60*60, '/' );
            if ( next.hasClass('current') ) next = next.next();

            var next_a = next.find('a')
                .html('<span>123</span>')
                .addClass('borderedR');
            next_a.attr('href', next_a.attr('href').replace(/page=\d+/,'') );
            $('.pageslist li').remove();
            $('.pageslist ul').append( next ).find('a')
                .bind('click', function() {
                    window.docCookies.setItem('infScroll', 0, 4*7*24*60*60, '/' );
                });
            $('.allpagerJewel').addClass('mChecked');
            checkScrollJewel();
            $(window).scroll(checkScrollJewel);
        };

    $('.allpagerJewel').each(function() {

        var lsURL = $(this).data('url'),
            filters = '',
            vnext = ( $(this).data('page') !== '') ? $(this).data('page') * 1 + 1 : 2,
            vinit = vnext - 1,
            vlast = parseInt('0' + $(this).data('lastpage') , 10);

        checkScrollJewel = function(){
            if ( ableToLoadJewel && $(window).scrollTop() + 800 > $(document).height() - $(window).height() ){
                ableToLoadJewel = false;
                if( vlast + vinit > vnext ) {
                    liveScrollJewel( lsURL, filters, ((vnext % vlast) ? (vnext % vlast) : vnext ));
                }
                vnext += 1;
            }
        };

        $(this).unbind('click');
        $(this).bind('click', function(){
            switch_to_scroll(checkScrollJewel);
        })
    });

    setTimeout(function(){
        if ( window.docCookies.getItem( 'infScroll' ) === '1' ) {
            switch_to_scroll(checkScrollJewel);
        }
    },600);

});

