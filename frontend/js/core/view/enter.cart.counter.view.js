/**
 * @module      enter.cart.counter.view
 * @version     0.1
 *
 * @author      Zaytsev Alexandr
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.cart.counter.view',
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

        provide(BaseCounter.extend(/** @lends module:enter.cart.counter.view~CartCounter */{
            timeout_id: null,

            /**
             * При инициализации запоминает текущее количество товара в корзине
             *
             * @classdesc   Представление каунтера продукта в корзине
             * @memberOf    module:enter.cart.counter.view~
             * @augments    module:enter.ui.baseCounter
             * @constructs  CartCounter
             */
            initialize: function() {
                this.quantity = this.model.get('quantity');
                this.input    = this.$el.find('.' + CSS_CLASSES.VALUE);

                this.listenTo(this.model, 'change', this.updateCounter.bind(this));
                this.listenTo(this, 'changeQuantity', this.render.bind(this));

                // this.render({
                //     quantity: this.quantity
                // });
                this.updateCounter();

                // Setup events
                this.events['click .' + CSS_CLASSES.PLUS]  = 'plus';
                this.events['click .' + CSS_CLASSES.MINUS] = 'minus';

                // Apply events
                this.delegateEvents();
            },

            /**
             * Соответствие событий обработчикам
             *
             * @member      events
             * @memberOf    module:enter.cart.counter.view~CartCounter#
             */
            events: {},

            /**
             * Обновление каунтера из модели
             *
             * @method      updateCounter
             * @memberOf    module:enter.cart.counter.view~CartCounter#
             */
            updateCounter: function() {
                this.quantity = this.model.get('quantity');
                this.input.val(this.quantity);
            },

            /**
             * Вызов отрисовки модели
             *
             * @method      render
             * @memberOf    module:enter.cart.counter.view~CartCounter#
             *
             * @listens     module:enter.ui.baseCounterr~BaseCounter#changeQuantity
             *
             * @param       {module:enter.ui.baseCounterr~BaseCounter#changeQuantity}    event
             */
            render: function( event ) {
                console.info('module:enter.cart.counter.view~CartCounter#render');
                var
                    self = this;

                clearTimeout(this.timeout_id);

                self.timeout_id = setTimeout(function() {
                    self.model.set({'quantity': event.quantity});
                    self.collection.addToCart(self.model);
                }, 400);

                this.input.val(event.quantity);
            }
        }));
    }
);
