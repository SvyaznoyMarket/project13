/*
* Новая карточка товара
* */
;(function($){

    var $window = $(window),
        $body = $(document.body),
        $creditButton = $body.find('.jsProductCreditButton'),
        $userbar = $('.js-topbar-fixed'),
        $tabs = $('.jsProductTabs'),
        tabsOffset = $tabs.offset().top,// это не очень хорошее поведение, т.к. при добавлении сверху элементов (AJAX, например) offset не изменяется
        popupDefaults = {
            centered: true,
            closeSelector: '.jsPopupCloser'
        };

    /* Если это не новая карточка, то do nothing */
    if (!$body.hasClass('product-card-new')) return;

    /* Попап новой карточки товара */
    $body.on('click', '.jsOpenProductImgPopup', function(){
        $body.find('.jsProductImgPopup').lightbox_me(popupDefaults);
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

    // Youtube и 3D
    $body.on('click', '.jsProductMediaButton', function(e){
        var $popup = $(e.target).next(),
            $iframe = $popup.find('iframe'),
            src = $iframe.data('src'),
            $3dContainer = $popup.find('.jsProduct3DContainer'),
            $3DJSONContainer = $popup.find('.jsProduct3DJSON');

        // Загружаем видео только при открытии попапа
        if (src) $iframe.attr('src', src);

        if ($3dContainer.length == 0 && $3DJSONContainer.length == 0) {
            // Видео
            $popup.lightbox_me($.extend(popupDefaults, {
                destroyOnClose: true,
                onClose: function(){
                    $iframe.removeAttr('src');
                    $(e.target).parent().append($popup.clone().hide()); // Возвращаем всё на место
                }
            }))
        } else {
            // 3D
            if ($3dContainer.data('type') == 'swf') {
                $LAB.script('swfobject.min.js').wait(function() {
                    var id = 'js-product-3d-swf-popup-object';

                    swfobject.embedSWF(
                        $3dContainer.data('url'),
                        'js-product-3d-swf-popup-model', '700px', '500px', '10.0.0', 'js/vendor/expressInstall.swf',
                        { language: 'auto' },
                        {
                            menu: 'false',
                            scale: 'noScale',
                            allowFullscreen: 'true',
                            allowScriptAccess: 'always',
                            wmode: 'direct'
                        },
                        { id: id }
                    );

                    $popup.lightbox_me($.extend(popupDefaults, {
                        onClose: function() {
                            $(e.target).parent().append($popup.clone().hide());
                        }
                    }))

                });
            } else if ($3DJSONContainer.length > 0) {
                $LAB.script('DAnimFramePlayer.min.js').wait(function() {
                    var data = $3DJSONContainer.data('value'),
                        host = $3DJSONContainer.data('host');

                    try {
                        if (!$('#js-product-3d-img-container').length) {
                            (new DAnimFramePlayer($3DJSONContainer[0], host)).DoLoadModel(data);
                        }

                        $popup.lightbox_me($.extend(popupDefaults, {
                            onClose: function() {
                                $(e.target).parent().append($popup.clone().hide());
                            }
                        }));
                    }
                    catch (err) {
                        console.error(err)
                    }
                });
            }
        }
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
        $('.jsReviewForm').lightbox_me($.extend(popupDefaults, {
            onLoad: function() {},
            onClose: function() {}
        }));
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
            var fixedClass = 'pp-fixed';
            if ($window.scrollTop() - 60 > tabsOffset) {
                $tabs.addClass(fixedClass);
                $userbar.addClass(fixedClass);
            } else {
                $tabs.removeClass(fixedClass);
                $userbar.removeClass(fixedClass);
            }
        });
    }

    $body.scrollspy({ target: '#jsScrollSpy' });



})(jQuery);
