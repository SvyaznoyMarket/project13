/**
 * @module      enter.order.item.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    enter.order.item.counter.view
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.item.view',
        [
            'jQuery',
            'enter.BaseViewClass',
            'enter.order.item.counter.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, OrderItemCounterView ) {
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
                COUNTER: 'js-order-item-counter',
                SHOW_COUNTER_BTN: 'js-show-counter'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление товара в подзаказе
             * @memberOf    module:enter.order.item.view~
             * @augments    module:BaseViewClass
             * @constructs  OrderItemView
             */
            initialize: function( options ) {
                console.info('module:enter.order.item.view~OrderItemView#initialize');

                var
                    self = this;

                this.subViews = {
                    counter: new OrderItemCounterView({
                        el: this.$el.find('.' + CSS_CLASSES.COUNTER)
                    }),

                    showCounterBtn: this.$el.find('.' + CSS_CLASSES.SHOW_COUNTER_BTN)
                };

                this.events['click .' + CSS_CLASSES.SHOW_COUNTER_BTN]  = 'showCounter';

                // Apply events
                this.delegateEvents();
            },

            events: {},

            showCounter: function() {
                this.subViews.showCounterBtn.hide();
                this.subViews.counter.show();
            }
        }));
    }
);
