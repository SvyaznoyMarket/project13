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
            'jquery.replaceWithPush'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, mustache, replaceWithPush ) {
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
                DELETE: 'js-cart-item-delete'
            },

            /**
             * Используемые шаблоны
             *
             * @private
             * @constant
             * @type        {Object}
             */
            TEMPLATES = {
                CART_ITEM: $('#js-cart-item-template').html()
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
                var
                    html = mustache.render(TEMPLATES.CART_ITEM, this.model.attributes);

                this.$el = $(html);
                this.delegateEvents();

                return this.$el;
            }
        }));
    }
);
