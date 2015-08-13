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
            'enter.order.item.view',
            'enter.order.calendar.view',
            'enter.order.points.popup.view',
            'enter.points.popup.collection'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, OrderItemView, OrderCalendarView, OrderPointMapPopupView, OrderPointsCollection ) {
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
                SHOW_POINS_POPUP: 'js-order-changePlace-link',
                SHOW_DISCOUNT: 'js-show-discount',
                CHANGE_PAYMENT_METHOD_RADIO: 'js-payment-method-radio',
                CHANGE_PAYMENT_METHOD_SELECT: 'js-payment-method-select',
                CALENDAR_OPENER: 'js-order-open-calendar',
                CHANGE_DELIVERY_METHOD: 'js-order-change-delivery-method',
                CHANGE_DELIVERY_METHOD_ACITVE: 'orderCol_delivrLst_i-act',
                SHOW_INTERVALS_BTN: 'js-order-show-intervals',
                INTERVALS_POPUP: 'js-order-intervals',
                PICK_INTERVAL: 'js-order-pick-interval',
                POINTS_DATA: 'js-points-data'
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
                    self        = this,
                    items       = this.$el.find('.' + CSS_CLASSES.ITEM),
                    orderPoints = JSON.parse(this.$el.find('.' + CSS_CLASSES.POINTS_DATA).html());

                this.orderView        = options.orderView;
                this.blockName        = this.$el.attr('data-block_name');
                this.pointsCollection = new OrderPointsCollection(orderPoints.points);

                items.each(function( index ) {
                    self.subViews['suborder_' + index] = new OrderItemView({
                        el: $(this),
                        orderView: self.orderView,
                        blockName: self.blockName
                    });
                });

                this.subViews.intervalsPopup = this.$el.find('.' + CSS_CLASSES.INTERVALS_POPUP);

                // Setup events
                this.events['click .' + CSS_CLASSES.SHOW_POINS_POPUP]              = 'showPointsPopup';
                this.events['click .' + CSS_CLASSES.SHOW_DISCOUNT]                 = 'showDiscount';
                this.events['click .' + CSS_CLASSES.CHANGE_PAYMENT_METHOD_RADIO]   = 'changePaymentMethod';
                this.events['change .' + CSS_CLASSES.CHANGE_PAYMENT_METHOD_SELECT] = 'changePaymentMethodSelect';
                this.events['click .' + CSS_CLASSES.CALENDAR_OPENER]               = 'openCalendar';
                this.events['click .' + CSS_CLASSES.CHANGE_DELIVERY_METHOD]        = 'changeDeliveryMethod';
                this.events['click .' + CSS_CLASSES.SHOW_INTERVALS_BTN]            = 'showIntervals';
                this.events['click .' + CSS_CLASSES.PICK_INTERVAL]                 = 'pickInterval';


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
             * Обработчик выбора интервала
             *
             * @method      changeDeliveryMethod
             * @memberOf    module:enter.suborder.view~SubOrderView#
             */
            pickInterval: function( event ) {
                var
                    target   = $(event.currentTarget),
                    interval = target.data('value');

                this.orderView.trigger('sendChanges', {
                    action: 'changeInterval',
                    data: {
                        block_name: this.blockName,
                        interval: interval
                    }
                });

                return false;
            },

            /**
             * Показать окно с выбором интервала
             *
             * @method      changeDeliveryMethod
             * @memberOf    module:enter.suborder.view~SubOrderView#
             */
            showIntervals: function() {
                this.subViews.intervalsPopup.show();
            },

            /**
             * Смена метода доставки
             *
             * @method      changeDeliveryMethod
             * @memberOf    module:enter.suborder.view~SubOrderView#
             */
            changeDeliveryMethod: function( event ) {
                var
                    target        = $(event.currentTarget),
                    deliveryToken = target.attr('data-delivery_method_token'),
                    deliveryId    = target.attr('data-delivery_group_id');

                if ( target.hasClass(CSS_CLASSES.CHANGE_DELIVERY_METHOD_ACITVE) ) {
                    return false;
                }

                this.orderView.trigger('sendChanges', {
                    action: 'changeDelivery',
                    data: {
                        block_name: this.blockName,
                        delivery_method_token: deliveryToken
                    }
                });

                return false;
            },

            /**
             * Открытие календаря
             *
             * @method      changePaymentMethod
             * @memberOf    module:enter.suborder.view~SubOrderView#
             */
            openCalendar: function( event ) {
                var
                    target   = $(event.currentTarget),
                    calendar = this.$el.find(target.attr('data-content'));

                if ( this.subViews.calendar ) {
                    this.subViews.calendar.show();
                } else {
                    this.subViews.calendar = new OrderCalendarView({
                        el: calendar,
                        orderView: this.orderView,
                        blockName: this.blockName
                    });
                }

                return false;
            },

            /**
             * Обработчик смены метода оплаты
             *
             * @method      changePaymentMethod
             * @memberOf    module:enter.suborder.view~SubOrderView#
             *
             * @param       {jQuery.Event}  event
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
                    action: 'changePaymentMethod',
                    data: data
                });

                return false;
            },

            /**
             * Обработчик смены метода оплаты в селекте
             *
             * @method      changePaymentMethodSelect
             * @memberOf    module:enter.suborder.view~SubOrderView#
             *
             * @param       {jQuery.Event}  event
             */
            changePaymentMethodSelect: function( event ) {
                var
                    target = $(event.currentTarget),
                    method = target.find(':selected').val(),

                    data = {
                        block_name: this.blockName
                    };

                console.info('module:enter.suborder.view~SubOrderView#changePaymentMethodSelect');

                data[method] = true;

                this.orderView.trigger('sendChanges', {
                    action: 'changePaymentMethod',
                    data: data
                });
            },

            /**
             * Показать окно с выбором точки самовывоза
             *
             * @method      showPointsPopup
             * @memberOf    module:enter.suborder.view~SubOrderView#
             *
             * @todo        написать обработчик
             */
            showPointsPopup: function() {
                console.info('module:enter.suborder.view~SubOrderView#showPointsPopup');

                new OrderPointMapPopupView({
                    orderView: this.orderView,
                    blockName: this.blockName,
                    collection: this.pointsCollection
                });

                return false;
            },

            /**
             * Показывать форму для ввода купонов
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
