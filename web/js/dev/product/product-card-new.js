/*
* Новая карточка товара
* */
;(function($){

    var $window = $(window),
        $body = $(document.body),
        $creditButton = $body.find('.jsProductCreditButton'),
        $tabs = $('.jsProductTabs');

    /* Если это не новая карточка, то do nothing */
    if (!$body.hasClass('product-card-new')) return;

    /* Попап новой карточки товара */
    $body.on('click', '.jsOpenProductImgPopup', function(){
        $body.find('.jsProductImgPopup').lightbox_me({
            centered: true,
            closeSelector: '.closer',
            onLoad: function() {},
            onClose: function() {}
        });
    });

    /* Меняем большое изображение в popup при клике на миниатюру */
    $body.find('.jsProductPhotoThumb').on('click', function(){
        $body.find('.jsProductPopupBigPhoto').attr('src', $(this).data('big-img'));
    });

    /* Зум в попапе */
    $body.on('click', '.jsProductPopupZoom', function(){
/*        var direction = parseInt($(this).data('dir'), 10),
            $img = $body.find('.jsProductPopupBigPhoto'),
            cssInc = direction > 0 ? '+=' : '-=',
            multiply = 500;

        $img.css('height', cssInc + multiply).css('top', (direction < 0 ? '+=' : '-=') + multiply/2).css('left', (direction < 0 ? '+=' : '-=') + multiply/2);*/

    });

    /* Слайд в попапе */
    $body.on('click', '.jsProductPopupSlide', function(){
        //var direction = $(this).data('dir')
    });

    // Кредит
    if ($creditButton.length > 0 && typeof window['dc_getCreditForTheProduct'] == 'function') {
        window['dc_getCreditForTheProduct'](
            4427,
            window.docCookies.getItem('enter_auth'),
            'getPayment',
            { price : $creditButton.data('credit')['price'], count : 1, type : $creditButton.data('credit')['product_type'] },
            function( result ) {
                if( typeof result['payment'] != 'undefined' && result['payment'] > 0 ) {
                    $creditButton.find('.jsProductCreditPrice').text( printPrice( Math.ceil(result['payment']) ) );
                    $creditButton.show();
                }
            }
        )
    }

    $body.on('click', '.jsReviewAdd', function(){
        $('.jsReviewForm').lightbox_me({
            centered: true,
            closeSelector: '.closer',
            onLoad: function() {},
            onClose: function() {}
        });
    });

    // Отзывы
    $body.on('click', '.jsShowMoreReviews', function(){
        var $hiddenReviews = $('.jsReviewItem:hidden');
        if ($hiddenReviews.length > 0) {
            $hiddenReviews.show()
        } else {
            // Подгружаем отзывы
        }
    });

    if ($tabs.length) {
        $window.on('scroll', function(){
            var fixedClass = 'product-tabs-fixed';
            if ($window.scrollTop() - 70 > $tabs.offset().top) {
                $tabs.addClass(fixedClass)
            } else {
                $tabs.removeClass(fixedClass)
            }
        });
    }

    $body.scrollspy({ target: '#jsScrollSpy' });

})(jQuery);
