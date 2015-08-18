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
                PHOTO_THUMB_ACTIVE: 'product-card-photo-thumbs__i--act'
            },

            /**
             * Используемые шаблоны
             *
             * @private
             * @constant
             * @type        {Object}
             */
            TEMPLATES = {};

        provide(BaseViewClass.extend(/** @lends module:enter.BaseViewClass~ProductPhotoSliser */{
             /**
             * @classdesc   Представление окна с набором
             * @memberOf    module:enter.product.photoSlider.view~
             * @augments    module:enter.BaseViewClass
             * @constructs  ProductPhotoSliser
             */
            initialize: function( options ) {
                console.info('module:enter.product.photoSlider.view~ProductPhotoSliser#initialize');

                var
                    zoomConfig;

                this.subViews = {
                    photoContainer: this.$el.find('.' + CSS_CLASSES.PHOTO_CONTAINER),
                    photo: this.$el.find('.' + CSS_CLASSES.PHOTO),
                    thumbs: this.$el.find('.' + CSS_CLASSES.PHOTO_THUMB)
                };

                zoomConfig = {
                    $imageContainer: this.subViews.photoContainer,
                    zoomWindowOffety: 0,
                    zoomWindowOffetx: 19,
                    zoomWindowWidth: 519,
                    borderSize: 1,
                    borderColour: '#C7C7C7'
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
