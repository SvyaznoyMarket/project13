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
                PHOTO_THUMB_ACTIVE: 'active'
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

        provide(BaseViewClass.extend(/** @lends module:enter.BaseViewClass~ProductPhotoSliser */{
             /**
             * @classdesc   Представление окна с набором
             * @memberOf    module:enter.product.photoSlider.view~
             * @augments    module:enter.BaseViewClass
             * @constructs  ProductPhotoSliser
             */
            initialize: function( options ) {
                var
                    zoomConfig, productContentPaddingLeft, photoContainerW,
                    photoContainerH, photoW, photoH;

                this.subViews = {
                    photoContainer: this.$el.find('.' + CSS_CLASSES.PHOTO_CONTAINER),
                    photo: this.$el.find('.' + CSS_CLASSES.PHOTO),
                    thumbs: this.$el.find('.' + CSS_CLASSES.PHOTO_THUMB)
                };

                productContentPaddingLeft = parseInt($PRODUCT_CONTENT.css('paddingLeft'), 10);
                photoContainerW           = parseInt(this.subViews.photoContainer.width(), 10);
                photoContainerH           = parseInt(this.subViews.photoContainer.height(), 10);
                photoW                    = parseInt(this.subViews.photo.width(), 10);
                photoH                    = parseInt(this.subViews.photo.height(), 10);

                console.groupCollapsed('module:enter.product.photoSlider.view~ProductPhotoSliser#initialize');
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

                if ( this.subViews.photo.attr('data-zoom-image') ) {
                    this.subViews.photo.elevateZoom(zoomConfig);
                }

                // Setup events
                this.events['click .' + CSS_CLASSES.PHOTO_THUMB] = 'changePhoto';

                // Apply events
                this.delegateEvents();
            },

            events: {},

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
            }
        }));
    }
);
