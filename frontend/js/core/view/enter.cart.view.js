/**
 * @module      enter.cart.view
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.cart.view',
        [
            'App',
            'jQuery',
            'enter.BaseViewClass',
            'enter.cart.item.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, $, BaseViewClass, CartItemView ) {
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
                CART_ITEMS_WRAPPER: 'js-cart-items-wrapper',
                CART_QUANTITY: 'js-cart-quantity',
                EMPTY_CART: '',
                FULL_CART: ''
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление страницы сайта
             * @memberOf    module:enter.cart.view~
             * @augments    module:BaseViewClass
             * @constructs  EnterCartView
             */
            initialize: function( options ) {
                console.info('enter.cart.view~EnterCartView#initialize');

                this.collection = App.cart;

                this.subViews = {
                    cartItemsWrapper: this.$el.find('.' + CSS_CLASSES.CART_ITEMS_WRAPPER),
                    cartQuantity: this.$el.find('.' + CSS_CLASSES.CART_QUANTITY)
                };

                this.listenTo(this.collection, 'remove', this.removeItem);
                this.listenTo(this.collection, 'add', this.addItem);
                this.listenTo(this.collection, 'silentAdd', this.silentAddItem);
                this.listenTo(this.collection, 'syncEnd', this.render);
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.cart.view~EnterCartView
             * @type        {Object}
             */
            events: {},

            loader: {
                show: function() {
                    console.info('module:enter.cart.view~EnterCartView.loader#show');
                },

                hide: function() {
                    console.info('module:enter.cart.view~EnterCartView.loader#hide');
                }
            },

            show: function() {

            },

            hide: function() {

            },

            /**
             * Удаление продукта из корзины
             *
             * @method      removeItem
             * @memberOf    module:enter.cart.view~EnterCartView#
             *
             * @listens     module:enter.cart.collection~CartCollection#remove
             *
             * @param       {module:enter.cart.model}    removedItem    Модель удаляемого товара
             */
            removeItem: function( removedItem ) {
                var
                    id = removedItem.get('id');

                this.subViews[id].destroy();
            },

            /**
             * Добавление продукта в корзину. Событие срабатывет до того как отработал AJAX запрос, но уже был послан
             *
             * @method      addItem
             * @memberOf    module:enter.cart.view~EnterCartView#
             *
             * @listens     module:enter.cart.collection~CartCollection#add
             *
             * @param       {module:enter.cart.model}    addedModel     Модель добавляемого товара
             */
            addItem: function( addedItem ) {
                console.info('module:enter.cart.view~EnterCartView#addItem');
                this.loader.show();
                this.show();
                this.silentAddItem(addedItem)
            },

            /**
             * Скрытое добавление продукта в корзину. Событие срабатывает после завершения AJAX запроса.
             * Напрямую срабатывает тогда, когда товар был добавлен не через метод `add` у коллекции, а был добавлен при инициализации приложения.
             *
             * @method      silentAddItem
             * @memberOf    module:enter.cart.view~EnterCartView#
             *
             * @listens     module:enter.cart.collection~CartCollection#silentAdd
             *
             * @param       {module:enter.cart.model}    addedModel     Модель добавляемого товара
             */
            silentAddItem: function( addedItem ) {
                console.info('module:enter.cart.view~EnterCartView#silentAddItem');
                var
                    id = addedItem.get('id'),
                    tmpCartItemNode;

                this.subViews[id] = new CartItemView({
                    model: addedItem
                });

                tmpCartItemNode = this.subViews[id].render();
                this.subViews.cartItemsWrapper.append(tmpCartItemNode);
            },

            /**
             * Отрисовка элементов корзины. Срабатывает каждый раз после успешной синхронизации корзины с сервером
             *
             * @method      render
             * @memberOf    module:enter.cart.view~EnterCartView#
             *
             * @listens     module:enter.cart.collection~CartCollection#syncEnd
             */
            render: function() {
                var
                    cartQ = this.collection.quantity;

                console.info(this);
                this.loader.hide();

                if ( !cartQ ) {
                    this.$el.addClass(CSS_CLASSES.EMPTY_CART);
                } else {
                    this.$el.addClass(CSS_CLASSES.FULL_CART);
                }

                this.subViews.cartQuantity.text(this.collection.quantity);
            }
        }));
    }
);
