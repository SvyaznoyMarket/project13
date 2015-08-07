/**
 * @module      enter.productkit.counter.view
 * @version     0.1
 *
 * @requires    enter.ui.baseCounter
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.productkit.counter.view',
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
                MINUS: 'js-counter-minus'
            };

        provide(BaseCounter.extend({
            /**
             * @classdesc   Представление каунтера элемента набора
             * @memberOf    module:enter.productkit.counter.view~
             * @augments    module:enter.ui.baseCounter~BaseCounter
             * @constructs  ProductKitItemCounterView
             */
            initialize: function( options ) {
                console.info('module:enter.productkit.counter.view~ProductKitItemCounterView#initialize');

                this.quantity = this.model.get('quantity');
                this.input    = this.$el.find('.' + CSS_CLASSES.VALUE);
                this.minValue = 0;

                this.listenTo(this.model, 'change', this.updateCounter);
                this.listenTo(this, 'changeQuantity', this.render);

                this.updateCounter();

                // Setup events
                this.events['click .' + CSS_CLASSES.PLUS]  = 'plus';
                this.events['click .' + CSS_CLASSES.MINUS] = 'minus';

                // Apply events
                this.delegateEvents();
            },

            events: {},

            /**
             * Обновление каунтера из модели
             *
             * @method      updateCounter
             * @memberOf    module:enter.productkit.counter.view~ProductKitItemCounterView#
             *
             * @listens     module:enter.productkit.model~ProductKitModel#change
             */
            updateCounter: function() {
                this.quantity = this.model.get('quantity');
                this.input.val(this.quantity);
            },

            /**
             * Вызов отрисовки модели
             *
             * @method      render
             * @memberOf    module:enter.productkit.counter.view~ProductKitItemCounterView#
             *
             * @listens     module:enter.productkit.counter.view~ProductKitItemCounterView#changeQuantity
             *
             * @param       {module:enter.ui.baseCounter~BaseCounter#changeQuantity}    event
             */
            render: function( event ) {
                var
                    self = this;

                this.timeout_id && clearTimeout(this.timeout_id);

                self.timeout_id = setTimeout(function() {
                    self.model.set({'quantity': event.quantity});
                }, 400);

                this.input.val(event.quantity);
            }
        }));
    }
);
