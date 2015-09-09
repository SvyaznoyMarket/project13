/**
 * @module      enter.suborder.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    enter.order.item.view
 * @requires    enter.order.calendar.view
 * @requires    enter.points.popup.view
 * @requires    enter.points.popup.collection
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
            'enter.points.popup.view',
            'enter.points.popup.collection'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, OrderItemView, OrderCalendarView, OrderPointMapPopupView, PointsCollection ) {
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
                POINTS_DATA: 'js-points-data',
                ACTIVE: 'active'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление подзаказа
             * @memberOf    module:enter.suborder.view~
             * @augments    module:enter.BaseViewClass
             * @constructs  SubOrderView
             */
            initialize: function( options ) {
                console.warn('module:enter.suborder.view~SubOrderView#initialize');

                var
                    self            = this,
                    items           = this.$el.find('.' + CSS_CLASSES.ITEM),
                    orderPointsData = JSON.parse(this.$el.find('.' + CSS_CLASSES.POINTS_DATA).html());

                this.orderView        = options.orderView;
                this.blockName        = this.$el.attr('data-block_name');
                this.pointsCollection = new PointsCollection(orderPointsData.points, {
                    popupData: orderPointsData
                });

                items.each(function( index ) {
                    self.subViews['suborder_' + index] = new OrderItemView({
                        el: $(this),
                        orderView: self.orderView,
                        blockName: self.blockName
                    });
                });

                this.subViews.intervalsPopup = this.$el.find('.' + CSS_CLASSES.INTERVALS_POPUP);
                this.subViews.intervalsBtn = this.$el.find('.' + CSS_CLASSES.SHOW_INTERVALS_BTN);


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
                if ( this.subViews.intervalsPopup.is(':visible') ) {
                    this.subViews.intervalsBtn.removeClass(CSS_CLASSES.ACTIVE);
                    this.subViews.intervalsPopup.hide();
                } else {
                    this.subViews.intervalsPopup.show();
                    this.subViews.intervalsBtn.addClass(CSS_CLASSES.ACTIVE);
                }
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

                data[method] = true;

                console.groupCollapsed('module:enter.suborder.view~SubOrderView#changePaymentMethod');
                console.log(this.$el);
                console.log(this.blockName);
                console.dir(data);
                console.groupEnd();

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

                if ( this.subViews.pointsPopup && this.subViews.pointsPopup.destroy ) {
                    this.subViews.pointsPopup.destroy();
                }

                this.subViews.pointsPopup = new OrderPointMapPopupView({
                    collection: this.pointsCollection,
                    pointsSelectable: true
                });

                this.subViews.pointsPopup.on('changePoint', this.changePoint.bind(this));

                return false;
            },

            changePoint: function( pointInfo ) {
                this.orderView.trigger('sendChanges', {
                    action: 'changePoint',
                    data: {
                        block_name: this.blockName,
                        id: pointInfo.id,
                        token: pointInfo.token
                    }
                });

                this.subViews.pointsPopup.hide();

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
