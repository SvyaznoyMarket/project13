;+function($){

    var $body = $(document.body),
        $imgPopup = $body.find('.jsProductImgPopup'),
        $popupPhoto = $body.find('.jsProductPopupBigPhoto'),
        $popupPhotoHolder = $('.jsProductPopupBigPhotoHolder'),
        $popupPhotoThumbs = $('.jsPopupPhotoThumb'),
        $productPhotoThumbs = $('.jsProductThumbList'),
        $productPhotoThumbsBtn = $('.jsProductThumbBtn'),
        productPhotoThumbsWidth = $productPhotoThumbs.width() - 2,
        productPhotoThumbsFullWidth = $productPhotoThumbs.get(0) ? $productPhotoThumbs.get(0).scrollWidth : 0,
        thumbActiveClass = 'product-card-photo-thumbs__i--act',
        thumbBtnDisabledClass = 'product-card-photo-thumbs__btn--disabled',
        thumbsCount = $popupPhotoThumbs.length,
        popupDefaults = {
            centered: true,
            closeSelector: '.jsPopupCloser',
            preventScroll: true,
            closeClick: true
        },
        /* Функция для зума фотографии */
        setZoom = function(direction) {
            var cssInc = direction < 0 ? '+=' : '-=',
                hInc = direction > 0 ? '+=' : '-=',
                dataZoom = $popupPhoto.data('zoom'),
                multiply = 500;

            if (typeof dataZoom == 'undefined') {
                if (direction < 0) return;
                else $popupPhoto.data('zoom', direction);
            } else if (dataZoom == 0 && direction < 0) {
                return;
            } else {
                $popupPhoto.data('zoom', dataZoom + direction)
            }

            $popupPhoto.css('height', hInc + multiply).css('top', cssInc + multiply/2).css('left', cssInc + multiply/2);
            if (dataZoom == 1 && direction < 0) $popupPhoto.css('top', '0px').css('left', '0px'); // fix при установке в 0
        },
        setPhoto = function(index) {
            // отмечаем активным классом thumb
            $popupPhotoThumbs.removeClass(thumbActiveClass).eq(index).addClass(thumbActiveClass);
            // меняем картинку
            $popupPhoto.css('top', '0px').css('left', '0px').css('height', $popupPhotoHolder.height()).data('zoom', 0);
            $popupPhoto.attr('src', $popupPhotoThumbs.eq(index).data('big-img'));
        };

    // Перемещение увеличенной фотографии по движению мыши
    $popupPhotoHolder.on('mousemove mouseleave wheel', function(e){
        var parentOffset = $(this).parent().offset(),
            relX = e.pageX - parentOffset.left,
            relY = e.pageY - parentOffset.top,
            hW = $(this).width(),
            hH = $(this).height(),
            iW = $popupPhoto.width(),
            iH = $popupPhoto.height();

        if (e.type == 'wheel') {
            setZoom(e.originalEvent['wheelDeltaY'] > 0 ? 1 : -1);
            e.stopPropagation(); // иначе будет скролл страницы
        }

        if (typeof $popupPhoto.data('zoom') == 'undefined' || $popupPhoto.data('zoom') == 0) return;

        if (e.type == 'mousemove') $popupPhoto.css('left', relX/hW * (hW - iW)).css('top', relY/hH * (hH - iH));
        if (e.type == 'mouseleave') $popupPhoto.css('left', (hW - iW)/2).css('top', (hH - iH)/2);

    });

    /* Клик по фото в карточке товара */
    $body.on('click', '.jsOpenProductImgPopup', function(){
        var $activeThumb = $('.' + thumbActiveClass);
        // устанавливаем большую картинку
        $imgPopup.find('.jsProductPopupBigPhoto').attr('src', $activeThumb.data('big-img'));
        // активируем thumb в попапе
        $popupPhotoThumbs.removeClass(thumbActiveClass)
            .eq($activeThumb.index()).addClass(thumbActiveClass);
        // и открываем popup
        $imgPopup.lightbox_me(popupDefaults);
    });

    /* Меняем большое изображение в popup при клике на миниатюру */
    $body.find('.jsProductPhotoThumb').on('click', function(){
        var $this = $(this);
        $this.siblings().removeClass('product-card-photo-thumbs__i--act');
        $this.addClass('product-card-photo-thumbs__i--act');
        $body.find('.jsProductMiddlePhoto').attr('src', $this.data('middle-img'));
    });

    /* Зум в попапе */
    $body.on('click', '.jsProductPopupZoom', function(){
        var direction = parseInt($(this).data('dir'), 10);
        setZoom(direction);
    });

    /* Слайд в попапе */
    $body.on('click', '.jsProductPopupSlide', function(){
        var direction = $(this).data('dir'),
            curIndex = $popupPhotoThumbs.index($imgPopup.find('.'+thumbActiveClass));
        if (curIndex + direction == thumbsCount) setPhoto(0);
        else setPhoto(curIndex + direction);
    });

    $popupPhotoThumbs.on('click', function(){
        setPhoto($popupPhotoThumbs.index($(this)));
    });

    $productPhotoThumbsBtn.on('click', function(){
        if (!$productPhotoThumbs.is(':animated'))
        $productPhotoThumbs.animate({
            'margin-left': $(this).data('dir') + productPhotoThumbsWidth
        }, function(){
            var margin = parseInt($productPhotoThumbs.css('margin-left'));
            $productPhotoThumbsBtn.removeClass(thumbBtnDisabledClass);
            if (productPhotoThumbsFullWidth + margin < productPhotoThumbsWidth) $productPhotoThumbsBtn.eq(1).addClass(thumbBtnDisabledClass);
            if (margin > 0) $productPhotoThumbsBtn.eq(0).addClass(thumbBtnDisabledClass);
        });
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

}(jQuery);