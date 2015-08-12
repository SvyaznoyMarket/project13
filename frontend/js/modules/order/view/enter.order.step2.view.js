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
            url: '/order/delivery',

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
                        el: $(this),
                        orderView: self
                    });
                });

                // Setup events
                this.events['click .' + CSS_CLASSES.CHANGE_REGION_BTN] = 'changeRegion';

                this.listenTo(this, 'sendChanges', this.sendChanges);

                this.loader.hide = this.loader.hide.bind(this);
                this.loader.show = this.loader.show.bind(this);

                // Apply events
                this.delegateEvents();
            },

            events: {},

            /**
             * Объект загрузчика. Передается в опциях в AJAX вызовы.
             * Свойство loading изменяет ajax автоматически.
             *
             * @memberOf    module:enter.order.step2.view~OrderStep2View#
             * @type        {Object}
             */
            loader: {
                loading: false,

                show: function() {
                    console.info('module:enter.order.step2.view~OrderStep2View#show');
                },

                hide: function() {
                    console.info('module:enter.order.step2.view~OrderStep2View#hide');
                }
            },

            /**
             * Отправка изменений на сервер
             *
             * @method      sendChanges
             * @memberOf    module:enter.order.step2.view~OrderStep2View#
             *
             * @todo        написать обработчик
             */
            sendChanges: function( event ) {
                var
                    action = event.action,
                    data   = event.data;

                console.groupCollapsed('module:enter.order.step2.view~OrderStep2View#sendChanges');
                console.dir(event);
                console.groupEnd();

                this.ajax({
                    type: 'POST',
                    data: {
                        action: action,
                        params: data
                    },
                    url: this.url,
                    loader: this.loader,
                    success: this.render.bind(this),
                    error: function( jqXHR, textStatus, errorThrown ) {
                        console.groupCollapsed('module:enter.order.step2.view~OrderStep2View#sendChanges: error');
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                        console.groupEnd();
                    }
                });
            },

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
            },

            /**
             * Отрисовка оформления заказа
             *
             * @method      render
             * @memberOf    module:enter.order.step2.view~OrderStep2View#
             *
             * @todo        написать обработчик
             */
            render: function( data ) {
                console.info('module:enter.order.step2.view~OrderStep2View#render');
            }
        }));
    }
);
