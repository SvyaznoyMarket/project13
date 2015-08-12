/**
 * @module      enter.order.step2.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    enter.suborder.view
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.step2.view',
        [
            'jQuery',
            'enter.BaseViewClass',
            'enter.suborder.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, SubOrderView ) {
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
                SUB_ORDER: 'js-order-block',
                CHANGE_REGION_BTN: 'js-change-region'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление второго шага оформления заказа
             * @memberOf    module:enter.order.step2.view~
             * @augments    module:BaseViewClass
             * @constructs  OrderStep2View
             */
            initialize: function( options ) {
                console.info('module:enter.order.step2.view~OrderStep2View#initialize');
                var
                    self      = this,
                    suborders = this.$el.find('.' + CSS_CLASSES.SUB_ORDER);

                suborders.each(function( index ) {
                    self.subViews['suborder_' + index] = new SubOrderView({
                        el: $(this)
                    });
                });

                // Setup events
                this.events['click .' + CSS_CLASSES.CHANGE_REGION_BTN] = 'changeRegion';

                // Apply events
                this.delegateEvents();
            },

            events: {},

            /**
             * Обработчик смены региона
             *
             * @method      changeRegion
             * @memberOf    module:enter.order.step2.view~OrderStep2View#
             *
             * @todo        написать обработчик
             */
            changeRegion: function() {
                console.info('module:enter.order.step2.view~OrderStep2View#changeRegion');

                return false;
            }
        }));
    }
);
