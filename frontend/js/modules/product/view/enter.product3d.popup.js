/**
 * @module      enter.product3d.popup
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.ui.BasePopup
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.product3d.popup',
        [
            'jQuery',
            'enter.ui.BasePopup',
            'swfobject'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BasePopup, swfobjectModule ) {
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
                CONTAINER: 'jsProduct3DContainer',
                MODEL_ID: 'js-product-3d-swf-popup-model'
            };

        provide(BasePopup.extend(/** @lends module:enter.ui.BasePopup~Product3dPopup */{

            swfID: 'js-product-3d-swf-popup-object',


             /**
             * @classdesc   Представление окна c 3D моделью
             * @memberOf    module:enter.product3d.popup~
             * @augments    module:enter.ui.BasePopup
             * @constructs  Product3dPopup
             *
             * [Пример карточки товара]{@link /product/electronics/smartfon-apple-iphone-4s-8-gb-cherniy-2060302006740}
             */
            initialize: function( options ) {
                console.info('module:enter.product3d.popup~Product3dPopup#initialize');

                // Setup events
                // this.events['click .' + CSS_CLASSES.SUBMIT_BTN] = 'submit';

                this.subViews = {
                    container: this.$el.find('.' + CSS_CLASSES.CONTAINER),
                    model: this.$el.find('#' + CSS_CLASSES.MODEL_ID)
                }

                if ( !this.subViews.model.length ) {
                    this.subViews.container.append('<div id="' + CSS_CLASSES.MODEL_ID + '"></div>');
                }

                swfobject.embedSWF(
                    this.subViews.container.attr('data-url'),
                    CSS_CLASSES.MODEL_ID,
                    '700px',
                    '500px',
                    '10.0.0',
                    'js/vendor/expressInstall.swf',
                    {
                        language: 'auto'
                    },
                    {
                        menu: 'false',
                        scale: 'noScale',
                        allowFullscreen: 'true',
                        allowScriptAccess: 'always',
                        wmode: 'direct'
                    },
                    {
                        id: this.swfID
                    }
                );

                // Apply events
                this.delegateEvents();
            },

            events: {},

            onClose: function() {
                // swfobject.removeSWF(this.swfID);
            }
        }));
    }
);
