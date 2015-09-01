/**
 * @module      enter.productvideo.popup
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.ui.BasePopup
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.productvideo.popup',
        [
            'jQuery',
            'enter.ui.BasePopup'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BasePopup ) {
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
                CONTAINTER: 'js-iframe-containter'
            };

        provide(BasePopup.extend(/** @lends module:enter.ui.BasePopup~ProductVideoPopup */{

             /**
             * @classdesc   Представление окна c видео
             * @memberOf    module:enter.productvideo.popup~
             * @augments    module:enter.ui.BasePopup
             * @constructs  ProductVideoPopup
             *
             * [Пример карточки товара]{@link /product/jewel/dvoynaya-podveska-sharm-sestri-pandora-791383-2034000001666}
             */
            initialize: function( options ) {
                console.info('module:enter.productvideo.popup~ProductVideoPopup#initialize');

                this.subViews = {
                    containter: this.$el.find('.' + CSS_CLASSES.CONTAINTER)
                };

                this.initialHTML = this.subViews.containter.html();
                this.subViews.containter.empty();

                // Apply events
                this.delegateEvents();
            },

            events: {},

            beforeOnLoad: function() {
                var
                    iframe;

                this.subViews.containter.append(this.initialHTML);

                iframe = $('iframe', this.subViews.containter);
                iframe.attr('src', iframe.data('src'));
            },

            onClose: function() {
                this.subViews.containter.empty();
            }
        }));
    }
);
