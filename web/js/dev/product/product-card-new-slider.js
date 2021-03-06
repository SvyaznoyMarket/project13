;+function($){

    var $body = $(document.body),
        $imgPopup = $body.find('.jsProductImgPopup'),
        $popupPhoto = $body.find('.jsProductPopupBigPhoto'),
        $popupPhotoHolder = $('.jsProductPopupBigPhotoHolder'),
        $popupPhotoThumbs = $('.jsPopupPhotoThumb'),
        $productPhotoThumb = $('.jsProductPhotoThumb'),
        $productPhotoThumbs = $('.jsProductThumbList'),
        $zoomBtn   = $('.jsProductPopupZoom'),
        $popupThumbs = $('.jsPopupThumbList'),
        thumbActiveClass = 'product-card-photo-thumbs__i--act',
        thumbsCount = $popupPhotoThumbs.length,
        popupDefaults = {
            centered: true,
            closeSelector: '.jsPopupCloser',
            preventScroll: true,
            closeClick: true
        },
        /* Проверка возможности увеличения изображения товара */
        checkZoom = function(){
            var newImage, initWidth, initHeight, result;

            // Получаем реальные размеры изображения
            newImage = new Image();
            newImage.src = $popupPhoto.attr("src");

            initWidth = newImage.width;
            initHeight = newImage.height;

            result = initWidth < $popupPhotoHolder.width() && initHeight < $popupPhotoHolder.height();

            if (result) {
                $zoomBtn.addClass('disabled');
            }

            return !result;

        },
        /* Функция для зума фотографии */
        setZoom = function(direction) {
            var dataZoom = $popupPhoto.data('zoom'),
                newImage, initHeight, initWidth;

            if (typeof dataZoom == 'undefined') {
                if (direction < 0) return;
                else $popupPhoto.data('zoom', direction);
            } else if (dataZoom == 0 && direction < 0) {
                return;
            } else {
                $popupPhoto.data('zoom', dataZoom + direction)
            }

            // Получаем реальные размеры изображения
            newImage = new Image();
            newImage.src = $popupPhoto.attr("src");

            initWidth = newImage.width;
            initHeight = newImage.height;

            // нажали плюс и размеры картинки больше контейнера
            if ( direction > 0 && ( initWidth > $popupPhotoHolder.width() || initHeight > $popupPhotoHolder.height() ) ) {
                $popupPhoto
                    .removeClass('fixed')
                    .css({'max-height' : initHeight, 'max-width' : initWidth});

                var
                    parentOffset       = $popupPhotoHolder.offset(),
                    parentOffsetHeight = $popupPhotoHolder.height(),
                    parentOffsetWidth  = $popupPhotoHolder.width(),
                    imgWidth           = $popupPhoto.width(),
                    imgHeight          = $popupPhoto.height(),
                    right              = parentOffset.left,
                    bottom             = parentOffset.top,
                    left, top;

                if ( imgWidth > parentOffsetWidth ) {
                    left = parentOffsetWidth - imgWidth + right;
                } else {
                    left = 0;
                }

                if ( imgHeight > parentOffsetHeight ) {
                    top = parentOffsetHeight - imgHeight + bottom;
                } else {
                    top = 0;
                }

                $popupPhoto.draggable({
                    containment: [left, top, right, bottom],
                    scroll: false
                });
            }

            // нажали минус
            if ( direction < 0) {
                setDefaultSetting();
            }
        },

        // начальные установки для блока большого изображения
        setDefaultSetting = function() {
            $popupPhoto.addClass('fixed');
            $popupPhoto.css({'max-height' : '100%', 'max-width' : '100%', 'top' : 0, 'left' : 0}); // fix при установке в 0
            $popupPhoto.data('zoom', 0);
            $zoomBtn.removeClass('disabled');
            $('.jsProductPopupZoomOut').addClass('disabled');
            if ( $popupPhoto.hasClass('ui-draggable') ) {
                $popupPhoto.draggable('destroy')
            }
        },

        setPhoto = function(index) {
            // отмечаем активным классом thumb
            $popupPhotoThumbs.removeClass(thumbActiveClass).eq(index).addClass(thumbActiveClass);
            // меняем картинку
            $popupPhoto.attr('src', $popupPhotoThumbs.eq(index).data('big-img')).css({'max-height' : '100%', 'max-width' : '100%', 'top' : 0, 'left' : 0});
            setDefaultSetting();
        };

    /* Клик по фото в карточке товара */
    $body.on('click', '.jsOpenProductImgPopup', function(){
        var $activeThumb = $('.' + thumbActiveClass);
        // устанавливаем большую картинку
        $imgPopup.find('.jsProductPopupBigPhoto').attr('src', $activeThumb.data('big-img'));
        // активируем thumb в попапе
        $popupPhotoThumbs.removeClass(thumbActiveClass)
            .eq($activeThumb.index()).addClass(thumbActiveClass);
        // и открываем popup
        $imgPopup.enterLightboxMe({
            centered: false,
            closeSelector: '.jsPopupCloser',
            modalCSS: {top: '0', left: '0'},
            closeClick: true,
			preventScroll: true,
            onLoad: function() {
                checkZoom();
                if ($popupPhotoThumbs.length > 11) {
                    $popupThumbs.slick(
                        {
                            prevArrow: '.product-card-photo-thumbs__btn--l.jsPopupThumbBtn',
                            nextArrow: '.product-card-photo-thumbs__btn--r.jsPopupThumbBtn',
                            infinite: false,
                            slidesToShow: 11,
                            slidesToScroll: 11
                        }
                    );
                }
            },
            onClose: function() {
                setDefaultSetting();
            }
        });

        $(window).on('resize', function() {
            setDefaultSetting();
        });
    });

    /* Меняем большое изображение в popup при клике на миниатюру */
    $body.find('.jsProductPhotoThumb').on('click', function(){
        var $this = $(this);
        $this.siblings().removeClass('product-card-photo-thumbs__i--act');
        $this.addClass('product-card-photo-thumbs__i--act');
        $body.find('.jsProductMiddlePhoto').attr('src', $this.data('middle-img')).attr('alt', $this.find('img').attr('alt'));
    });

    // /* Зум в попапе */
    $body.on('click', '.jsProductPopupZoom', function(){
        var
            $this     = $(this),
            direction = parseInt($(this).data('dir'), 10);

        if (checkZoom()) {
            $zoomBtn.removeClass('disabled');
            $this.addClass('disabled');
            setZoom(direction);
        }
    });

    /* Слайд в попапе */
    $body.on('click', '.jsProductPopupSlide', function(){

        var direction = $(this).data('dir'),
            curIndex = $popupPhotoThumbs.index($imgPopup.find('.'+thumbActiveClass)),
            max = $popupPhotoThumbs.length - 1 ,
            photoIndex = (curIndex + direction == thumbsCount) ? 0 : curIndex + direction;

        (photoIndex == -1) && (photoIndex = max);

        setPhoto(photoIndex);

        if ($popupPhotoThumbs.length > 11) { $popupThumbs.slick('slickGoTo', photoIndex); }
    });

    $popupPhotoThumbs.on('click', function(){
        setPhoto($popupPhotoThumbs.index($(this)));
    });


    // Youtube и 3D
    $body.on('click', '.jsProductMediaButton li', function(e){
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
                    var data = $3DJSONContainer.data('value');

                    try {
                        if (!$('#js-product-3d-img-container').length) {
                            (new DAnimFramePlayer($3DJSONContainer[0])).DoLoadModel(data);
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
    //slick.js
    if ($productPhotoThumb.length > 5){
        $productPhotoThumbs.slick(
            {
                prevArrow: '.product-card-photo-thumbs__btn--l.jsProductThumbBtn',
                nextArrow: '.product-card-photo-thumbs__btn--r.jsProductThumbBtn',
                infinite: false,
                slidesToShow: 5,
                slidesToScroll: 5
            }
        );
    }


}(jQuery);