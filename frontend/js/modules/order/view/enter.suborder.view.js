/**
 * @module      enter.suborder.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    enter.order.item.view
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.suborder.view',
        [
            'jQuery',
            'enter.BaseViewClass',
            'enter.order.item.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, OrderItemView ) {
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
                ITEM: 'js-order-item',
                CHANGE_DELIVERY: 'js-order-changePlace-link',
                SHOW_DISCOUNT: 'js-show-discount',
                CHANGE_PAYMENT_METHOD: 'js-payment-method'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление подзаказа
             * @memberOf    module:enter.suborder.view~
             * @augments    module:enter.BaseViewClass
             * @constructs  SubOrderView
             */
            initialize: function( options ) {
                console.info('module:enter.suborder.view~SubOrderView#initialize');
                var
                    self  = this,
                    items = this.$el.find('.' + CSS_CLASSES.ITEM);

                this.orderView = options.orderView;
                this.blockName = this.$el.attr('data-block_name');

                items.each(function( index ) {
                    self.subViews['suborder_' + index] = new OrderItemView({
                        el: $(this),
                        orderView: self.orderView,
                        blockName: self.blockName
                    });
                });

                // Setup events
                this.events['click .' + CSS_CLASSES.CHANGE_DELIVERY]       = 'changeDeliveryPoint';
                this.events['click .' + CSS_CLASSES.SHOW_DISCOUNT]         = 'showDiscount';
                this.events['click .' + CSS_CLASSES.CHANGE_PAYMENT_METHOD] = 'changePaymentMethod';

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.suborder.view~SubOrderView
             * @type        {Object}
             */
            events: {},

            /**
             * Обработчик смены метода оплаты
             *
             * @method      changePaymentMethod
             * @memberOf    module:enter.suborder.view~SubOrderView#
             *
             * @todo        написать обработчик
             */
            changePaymentMethod: function( event ) {
                var
                    target = $(event.currentTarget),
                    method = target.val(),

                    data = {
                        block_name: this.blockName
                    };

                console.info('module:enter.suborder.view~SubOrderView#changePaymentMethod');

                data[method] = true;

                this.orderView.trigger('sendChanges', {
                    action: 'changeProductQuantity',
                    data: data
                });

                return false;
            },

            /**
             * Обработчик смены доставки
             *
             * @method      changeDeliveryPoint
             * @memberOf    module:enter.suborder.view~SubOrderView#
             *
             * @todo        написать обработчик
             */
            changeDeliveryPoint: function() {
                console.info('module:enter.suborder.view~SubOrderView#changeDeliveryPoint');
                return false;
            },

            /**
             * Показывать форму для вводка купонов
             *
             * @method      showDiscount
             * @memberOf    module:enter.suborder.view~SubOrderView#
             *
             * @todo        написать обработчик
             */
            showDiscount: function() {
                console.info('module:enter.suborder.view~SubOrderView#showDiscount');
                return false;
            }
        }));
    }
);
