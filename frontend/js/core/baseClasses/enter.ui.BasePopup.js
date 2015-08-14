/**
 * @module      enter.ui.BasePopup
 * @version     0.1
 *
 * @requires    Backbone
 * @requires    underscore
 * @requires    enter.BaseViewClass
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.ui.BasePopup',
        [
            'Backbone',
            'underscore',
            'enter.BaseViewClass'
        ],
        module
    );
}(
    this.modules,
    function( provide, Backbone, _, BaseView ) {
        'use strict';

        var
             /**
             * @classdesc   Базовый класс попапа
             * @memberOf    module:enter.ui.BasePopup~
             * @augments    module:enter.BaseViewClass
             * @constructs  BasePopup
             */
            BasePopup = function( options ) {
                this.closeSelector = '.js-popup-close';

                BaseView.apply(this, [options]);
            };

        _.extend(BasePopup.prototype, {

            onClose: function() {},

            onLoad: function() {},

            /**
             * Показ окна
             *
             * @method      minus
             * @memberOf    module:enter.ui.BasePopup~BasePopup#
             *
             * @fires       module:enter.ui.BasePopup~BasePopup#changeQuantity
             */
            show: function() {
                var
                    self = this;

                modules.require('jquery.lightbox_me', function(){
                    self.$el.lightbox_me({
                        closeSelector: self.closeSelector,
                        onLoad: self.onLoad.bind(self),
                        onClose: self.onClose.bind(self)
                    });
                });

                return false;
            },

            /**
             * Скрытие окна
             *
             * @method      plus
             * @memberOf    module:enter.ui.BasePopup~BasePopup#
             *
             * @fires       module:enter.ui.BasePopup~BasePopup#changeQuantity
             */
            hide: function() {
                this.$el.trigger('close');
                return false;
            }
        }, BaseView.prototype);

        BasePopup.extend = BaseView.extend;

        provide(BasePopup);
    }
);
