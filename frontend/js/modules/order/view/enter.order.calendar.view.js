/**
 * @module      enter.order.calendar.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.calendar.view',
        [
            'jQuery',
            'enter.BaseViewClass'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass ) {
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
                PICK_DATE: 'js-order-calendar-pickdate',
                CLOSER: 'js-order-calendar-close'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление календаря в подзаказе
             * @memberOf    module:enter.order.calendar.view~
             * @augments    module:enter.BaseViewClass
             * @constructs  OrderCalendarView
             */
            initialize: function( options ) {
                console.info('module:enter.order.calendar.view~OrderCalendarView#initialize');
                var
                    self  = this,
                    items = this.$el.find('.' + CSS_CLASSES.ITEM);

                this.orderView = options.orderView;
                this.blockName = options.blockName;

                // Setup events
                this.events['click .' + CSS_CLASSES.PICK_DATE] = 'pickDate';
                this.events['click .' + CSS_CLASSES.CLOSER]    = 'hide';

                // Apply events
                this.delegateEvents();

                this.show();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.order.calendar.view~OrderCalendarView
             * @type        {Object}
             */
            events: {},

            /**
             * Открытие попапа
             *
             * @method      show
             * @memberOf    module:enter.order.calendar.view~OrderCalendarView#
             */
            show: function() {
                this.$el.show();

                return false;
            },

            /**
             * Выбор даты
             *
             * @method      pickDate
             * @memberOf    module:enter.order.calendar.view~OrderCalendarView#
             *
             * @param       {jQuery.Event}  event
             */
            pickDate: function( event ) {
                var
                    target    = $(event.currentTarget),
                    timestamp = target.attr('data-value');

                this.orderView.trigger('sendChanges', {
                    action: 'changeDate',
                    data: {
                        block_name: this.blockName,
                        date: timestamp
                    }
                });

                return false;
            },

            /**
             * Закрытие попапа
             *
             * @method      show
             * @memberOf    module:enter.order.calendar.view~OrderCalendarView#
             */
            hide: function() {
                this.$el.hide();

                return false;
            }
        }));
    }
);
