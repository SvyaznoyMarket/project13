/**
 * @module      enter.cart.item.view
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.cart.item.view',
        [
            'jQuery',
            'enter.BaseViewClass',
            'Mustache',
            'enter.cart.counter.view',
            'jquery.replaceWithPush'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, mustache, CartCounter, replaceWithPush ) {
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
                COUNTER: 'js-counter',
                DELETE: 'js-cart-item-delete'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление страницы сайта
             * @memberOf    module:enter.cart.item.view~
             * @augments    module:BaseViewClass
             * @constructs  EnterCartItemView
             */
            initialize: function( options ) {
                console.info('enter.cart.item.view~EnterCartItemView#initialize');

                this.listenTo(this.model, 'change', this.updateCartItem);
                this.template = options.template;

                // Setup events
                this.events['click .' + CSS_CLASSES.DELETE] = 'removeItem';
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.cart.item.view~EnterCartItemView
             * @type        {Object}
             */
            events: {},

            /**
             * Удаление продукта из корзины
             *
             * @method      removeItem
             * @memberOf    module:enter.cart.item.view~EnterCartItemView#
             */
            removeItem: function() {
                console.info('module:enter.cart.item.view~EnterCartItemView#removeItem');
                this.collection.remove(this.model);

                return false;
            },

            /**
             * Обновление атрибутов модели
             *
             * @method      updateCartItem
             * @memberOf    module:enter.cart.item.view~EnterCartItemView#
             *
             * @listens     module:enter.cart.model~CartItemModel#change
             */
            updateCartItem: function() {
                console.info('module:enter.cart.item.view~EnterCartItemView#updateCartItem');

                this.$el.replaceWithPush(this.render());
            },

            /**
             * Рендеринг элемента корзины
             *
             * @method      render
             * @memberOf    module:enter.cart.item.view~EnterCartItemView#
             */
            render: function() {
                console.info('module:enter.cart.item.view~EnterCartItemView#render');
                var
                    html = mustache.render(this.template, this.model.attributes),
                    self = this,
                    key,
                    counters;

                this.$el = $(html);
                counters = this.$el.find('.' + CSS_CLASSES.COUNTER);

                // Destroy prev subViews
                for ( key in this.subViews ) {
                    if ( !this.subViews.hasOwnProperty(key) ) {
                        continue;
                    }

                    if ( typeof this.subViews[key].off === 'function' ) {
                        this.subViews[key].off();
                    }

                    if ( typeof this.subViews[key].destroy === 'function' ) {
                        this.subViews[key].destroy();
                    } else if ( typeof this.subViews[key].remove === 'function' ) {
                        this.subViews[key].remove();
                    }

                    delete this.subViews[key];
                }

                counters.each(function( index ) {
                    self.subViews['counter_' + index] = new CartCounter({
                        el: $(this),
                        model: self.model,
                        collection: self.collection
                    });
                });

                this.delegateEvents();

                return this.$el;
            }
        }));
    }
);
