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
                this.events['change .' + CSS_CLASSES.VALUE] = 'changeInput';

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.order.item.counter.view~OrderItemCounterView
             * @type        {Object}
             */
            events: {},

            /**
             * Показать каунтер
             *
             * @method      show
             * @memberOf    module:enter.order.item.counter.view~OrderItemCounterView#
             */
            show: function() {
                this.$el.show();

                return false;
            },

            /**
             * Скрыть каунтер
             *
             * @method      hide
             * @memberOf    module:enter.order.item.counter.view~OrderItemCounterView#
             */
            hide: function() {
                this.$el.hide();

                return false;
            },

            /**
             * Обработчик изменения в поле ввода
             *
             * @method      changeInput
             * @memberOf    module:enter.order.item.counter.view~OrderItemCounterView#
             */
            changeInput: function( ) {
                var
                    val = parseInt(this.input.val(), 10);

                this.quantity = isNaN(val) ? this.quantity : val;
                this.input.val(this.quantity);
            },

            /**
             * Применить каунтер
             *
             * @method      changeInput
             * @memberOf    module:enter.order.item.counter.view~OrderItemCounterView#
             */
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

            /**
             * Обработчик удаления продукта
             *
             * @method      render
             * @memberOf    module:enter.order.item.counter.view~OrderItemCounterView#
             *
             * @todo        написать обработчик
             */
            deleteProduct: function() {
                console.info('module:enter.order.item.counter.view~OrderItemCounterView#deleteProduct');

                this.orderView.trigger('sendChanges', {
                    action: 'changeProductQuantity',
                    data: {
                        block_name: this.blockName,
                        id: this.productData.id,
                        quantity: 0
                    }
                });

                return false;
            },

            /**
             * Отрисовка каунтера
             *
             * @method      render
             * @memberOf    module:enter.order.item.counter.view~OrderItemCounterView#
             *
             * @listens     module:enter.ui.baseCounterr~BaseCounter#changeQuantity
             */
            render: function( event ) {
                console.info('module:enter.order.item.counter.view~OrderItemCounterView#render');

                this.input.val(event.quantity);
            }
        }));
    }
);
