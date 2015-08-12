/**
 * @module      enter.order.item.counter.view
 * @version     0.1
 *
 * @requires    enter.ui.baseCounter
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.item.counter.view',
        [
            'enter.ui.baseCounter'
        ],
        module
    );
}(
    this.modules,
    function( provide, BaseCounter ) {
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
                VALUE: 'js-counter-value',
                PLUS: 'js-counter-plus',
                MINUS: 'js-counter-minus',
                APPLY: 'js-order-item-counter-apply',
                DELETE: 'js-order-item-counter-delete'
            };

        provide(BaseCounter.extend({
            /**
             * @classdesc   Представление каунтера товара в подзаказе
             * @memberOf    module:enter.order.item.counter.view~
             * @augments    module:enter.ui.baseCounter
             * @constructs  OrderItemCounterView
             */
            initialize: function( options ) {
                console.info('module:enter.order.item.counter.view~OrderItemCounterView#initialize');

                this.input       = this.$el.find('.' + CSS_CLASSES.VALUE);
                this.quantity    = parseInt(this.input.val(), 10);
                this.orderView   = options.orderView;
                this.blockName   = options.blockName;
                this.productData = options.productData;

                this.listenTo(this, 'changeQuantity', this.render.bind(this));

                // Setup events
                this.events['click .' + CSS_CLASSES.PLUS]   = 'plus';
                this.events['click .' + CSS_CLASSES.MINUS]  = 'minus';
                this.events['click .' + CSS_CLASSES.APPLY]  = 'applyCounter';
                this.events['click .' + CSS_CLASSES.DELETE] = 'deleteProduct';

                // Apply events
                this.delegateEvents();
            },

            events: {},

            show: function() {
                this.$el.show();

                return false;
            },

            hide: function() {
                this.$el.hide();

                return false;
            },

            applyCounter: function() {
                console.info('module:enter.order.item.counter.view~OrderItemCounterView#applyCounter');

                this.orderView.trigger('sendChanges', {
                    action: 'changeProductQuantity',
                    data: {
                        block_name: this.blockName,
                        id: this.productData.id,
                        quantity: this.quantity
                    }
                });

                return false;
            },

            deleteProduct: function() {
                console.info('module:enter.order.item.counter.view~OrderItemCounterView#deleteProduct');
                return false;
            },

            render: function( event ) {
                console.info('module:enter.order.item.counter.view~OrderItemCounterView#render');

                this.input.val(event.quantity);
            }
        }));
    }
);
