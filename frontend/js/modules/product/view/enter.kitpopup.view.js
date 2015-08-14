/**
 * @module      enter.kit.popup.view
 * @version     0.1
 *
 * @requires    enter.ui.BasePopup
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.kit.popup.view',
        [
            'enter.ui.BasePopup'
        ],
        module
    );
}(
    this.modules,
    function( provide, BasePopup ) {
        'use strict';

        provide(BasePopup.extend(/** @lends module:enter.ui.BasePopup~KitPopupView */{
             /**
             * @classdesc   Представление окна с набором
             * @memberOf    module:enter.kit.popup.view~
             * @augments    module:enter.ui.BasePopup
             * @constructs  KitPopupView
             */
            initialize: function( options ) {
                console.info('module:enter.kit.popup.view~KitPopupView#initialize');

                var
                    self = this;

                modules.require('enter.productkit.view', function( ProductKitView ) {
                    console.log('enter.productkit.view required');
                    self.productKitView = new ProductKitView({
                        el: self.$el,
                        productUi: self.model.get('ui')
                    });

                    self.listenTo(self.productKitView, 'buyHandler', self.hide);
                });
            }
        }));
    }
);
