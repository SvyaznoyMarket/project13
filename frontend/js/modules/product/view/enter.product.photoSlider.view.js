/**
 * @module      enter.product.photoSlider.view
 * @version     0.1
 *
 * @requires    enter.BaseViewClass
 * @requires    jquery.elevatezoom
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.product.photoSlider.view',
        [
            'jQuery',
            'enter.BaseViewClass',
            'jquery.elevatezoom'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, jqueryElevatezoom ) {
        'use strict';

        var
            /**
             * Используемые CSS классы
             *
             * @private
             * @constant
             * @type        {Object}
             */
            CSS_CLASSES = {
                PHOTO_CONTAINER: 'js-photo-container',
                PHOTO: 'js-photo-zoomedImg',
                PHOTO_THUMB: 'jsProductPhotoThumb',
                PHOTO_THUMB_ACTIVE: 'active',
                SLIDE_WRAPPER: 'jsProductThumbList',
                SLIDE_CTRL: 'jsProductThumbBtn',
                SLIDE_CTRL_DISABLIED: 'product-card-photo-thumbs__btn--disabled',
                POPUP_3D: 'js-product-3d-popup',
                POPUP_3D_BTN: 'js-product-open-3d',
                POPUP_VIDEO: 'js-product-video-popup',
                POPUP_VIDEO_BTN: 'js-product-open-video'
            },

            /**
             * Используемые шаблоны
             *
             * @private
             * @constant
             * @type        {Object}
             */
            TEMPLATES = {},

            $PRODUCT_CONTENT = $('#product_card_content');

        provide(BaseViewClass.extend(/** @lends module:enter.BaseViewClass~ProductPhotoSlider */{
             /**
             * @classdesc   Представление окна с набором
             * @memberOf    module:enter.product.photoSlider.view~
             * @augments    module:enter.BaseViewClass
             * @constructs  ProductPhotoSlider
             */
            initialize: function( options ) {
                var
                    self = this,
                    zoomConfig, productContentPaddingLeft, photoContainerW,
                    photoContainerH, photoW, photoH;

                this.subViews = {
                    photoContainer: this.$el.find('.' + CSS_CLASSES.PHOTO_CONTAINER),
                    photo: this.$el.find('.' + CSS_CLASSES.PHOTO),
                    thumbs: this.$el.find('.' + CSS_CLASSES.PHOTO_THUMB),
                    f_thumb: this.$el.find('.' + CSS_CLASSES.PHOTO_THUMB).eq(0),
                    slideControl: this.$el.find('.' + CSS_CLASSES.SLIDE_CTRL),
                    slideWrapper: this.$el.find('.' + CSS_CLASSES.SLIDE_WRAPPER),
                    popup3D_el: this.$el.find('.' + CSS_CLASSES.POPUP_3D),
                    popupVideo_el: this.$el.find('.' + CSS_CLASSES.POPUP_VIDEO)
                };

                productContentPaddingLeft = parseInt($PRODUCT_CONTENT.css('paddingLeft'), 10);
                photoContainerW           = parseInt(this.subViews.photoContainer.width(), 10);
                photoContainerH           = parseInt(this.subViews.photoContainer.height(), 10);
                photoW                    = parseInt(this.subViews.photo.width(), 10);
                photoH                    = parseInt(this.subViews.photo.height(), 10);

                console.groupCollapsed('module:enter.product.photoSlider.view~ProductPhotoSlider#initialize');
                console.log('productContentPaddingLeft', productContentPaddingLeft);
                console.log('photoContainerW', photoContainerW);
                console.log('photoContainerH', photoContainerH);
                console.log('---------------------');
                console.log('photoW', photoW);
                console.log('photoH', photoH);
                console.groupEnd();

                zoomConfig = {
                    $imageContainer: this.subViews.photoContainer,
                    zoomWindowOffety: 30,
                    zoomWindowPosition: 2,
                    zoomWindowOffetx: productContentPaddingLeft - photoContainerW + (photoContainerW - photoW)/2,
                    zoomWindowWidth: $PRODUCT_CONTENT.width(),
                    borderSize: 1,
                    borderColour: '#EBEBEB'
                };

                if ( !this.subViews.photo.attr('data-zoom-image') ) {
                    zoomConfig.zoomType = 'Lens';
                    zoomConfig.lensBorder = 0;
                    zoomConfig.lensSize = 0;
                    zoomConfig.lensOpacity = 0;
                }

                this.slideW   = parseInt(this.subViews.slideWrapper.width(), 10);
                this.slideMin = 0;
                this.slideMax = -(((parseInt(this.subViews.f_thumb.width(), 10) + parseInt(this.subViews.f_thumb.css('margin-right'), 10)) * this.subViews.thumbs.length) - this.slideW);

                this.checkSlider();

                this.subViews.photo.elevateZoom(zoomConfig);

                // Setup events
                this.events['click .' + CSS_CLASSES.PHOTO_THUMB]     = 'changePhoto';
                this.events['click .' + CSS_CLASSES.SLIDE_CTRL]      = 'slide';
                this.events['click .' + CSS_CLASSES.POPUP_3D_BTN]    = 'openPopup3D';
                this.events['click .' + CSS_CLASSES.POPUP_VIDEO_BTN] = 'openPopupVideo';

                // Apply events
                this.delegateEvents();
            },

            events: {},

            openPopup3D: function() {
                var
                    self = this;

                if ( this.subViews.popup3D ) {
                    this.subViews.popup3D.show();
                } else {
                    modules.require(['enter.product3d.popup'], function( Product3dPopup ) {
                        self.subViews.popup3D = new Product3dPopup({
                            el: self.subViews.popup3D_el
                        });

                        self.subViews.popup3D.show();
                    });
                }

                return false;
            },

            openPopupVideo: function() {
                var
                    self = this;

                if ( this.subViews.popupVideo ) {
                    this.subViews.popupVideo.show();
                } else {
                    modules.require(['enter.productvideo.popup'], function( ProductVideoPopup ) {
                        self.subViews.popupVideo = new ProductVideoPopup({
                            el: self.subViews.popupVideo_el
                        });

                        self.subViews.popupVideo.show();
                    });
                }

                return false;
            },

            changePhoto: function( event ) {
                var
                    target = $(event.currentTarget);

                if ( target.hasClass(CSS_CLASSES.PHOTO_THUMB_ACTIVE) ) {
                    return;
                }

                this.subViews.thumbs.removeClass(CSS_CLASSES.PHOTO_THUMB_ACTIVE);
                target.addClass(CSS_CLASSES.PHOTO_THUMB_ACTIVE);

                if ( this.subViews.photo.data('elevateZoom') ) {
                    this.subViews.photo.data('elevateZoom').swaptheimage(target.attr('data-middle-img'), target.attr('data-big-img'));
                }

                return false;
            },

            checkSlider: function() {
                var
                    currentOffset = parseInt(this.subViews.f_thumb.css('margin-left') , 10);

                console.groupCollapsed('module:enter.product.photoSlider.view~ProductPhotoSlider#checkSlider');
                console.log('min', this.slideMin);
                console.log('max', this.slideMax);
                console.log('currentOffset', currentOffset);
                console.groupEnd();


                if ( currentOffset === this.slideMin ) {
                    this.subViews.slideControl.eq(0).addClass(CSS_CLASSES.SLIDE_CTRL_DISABLIED);
                } else {
                    this.subViews.slideControl.eq(0).removeClass(CSS_CLASSES.SLIDE_CTRL_DISABLIED);
                }

                if ( currentOffset <= this.slideMax ) {
                    this.subViews.slideControl.eq(1).addClass(CSS_CLASSES.SLIDE_CTRL_DISABLIED);
                } else {
                    this.subViews.slideControl.eq(1).removeClass(CSS_CLASSES.SLIDE_CTRL_DISABLIED);
                }
            },

            slide: function( event ) {
                var
                    target        = $(event.currentTarget),
                    direction     = target.attr('data-dir'),
                    thumb         = this.subViews.thumbs.eq(0),
                    currentOffset = parseInt(this.subViews.f_thumb.css('margin-left'), 10),
                    newOffset     = 0;

                if ( direction === '-=' ) { // right
                    newOffset = ( currentOffset - this.slideW < this.slideMax ) ? this.slideMax : currentOffset - this.slideW;
                } else if ( direction === '+=' ) { // left
                    newOffset = ( currentOffset + this.slideW > this.slideMin ) ? this.slideMin : currentOffset + this.slideW;
                }

                this.subViews.f_thumb.stop(true, true).animate({
                    'margin-left': newOffset
                }, 300, this.checkSlider.bind(this));

                return false;
            }
        }));
    }
);
