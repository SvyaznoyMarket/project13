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
            overlay = $('.js-overlay'),
            /**
             * Используемые CSS классы
             *
             * @private
             * @constant
             * @type        {Object}
             */

            CSS_CLASSES = {
                CART_PAGE: 'js-cart-page',
                CART_ITEMS_WRAPPER: 'js-cart-items-wrapper',
                CART_QUANTITY: 'js-cart-quantity',
                CART_SUM: 'js-cart-sum',
                EMPTY_CART: 'header-cart_empty',
                FULL_CART: 'header-cart_full',
                CART_DROP_DOWN: 'js-cart-notice',
                CART_DROP_DOWN_CONTENT: 'js-cart-notice-content',
                CART_SHOW: 'show',
                LOADER: 'loader',
                CART_DROPDOWN: 'js-cart-notice'
            },

            /**
             * Используемые шаблоны
             *
             * @private
             * @constant
             * @type        {Object}
             */
            TEMPLATES = {
                CART_DROPDOWN_ITEM: $('#js-cart-item-template').html(),
                CART_PAGE_ITEM: $('#js-cart-page-item-template').html()
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
                console.log(this.$el);

                this.collection = App.cart;
                this.timeToHide = 5 * 1000; // 5 sec

                this.subViews = {
                    cartItemsWrapper: this.$el.find('.' + CSS_CLASSES.CART_ITEMS_WRAPPER),
                    cartQuantity: this.$el.find('.' + CSS_CLASSES.CART_QUANTITY),
                    cartSum: this.$el.find('.' + CSS_CLASSES.CART_SUM),
                    cartDropDown: this.$el.find('.' + CSS_CLASSES.CART_DROP_DOWN),
                    cartDropDownContent: this.$el.find('.' + CSS_CLASSES.CART_DROP_DOWN_CONTENT)
                };

                this.bindedHide = this.hide.bind(this);

                this.listenTo(this.collection, 'remove', this.removeItem);
                this.listenTo(this.collection, 'add', this.addHandler);
                this.listenTo(this.collection, 'silentAdd', this.silentAddItem);
                this.listenTo(this.collection, 'silentRemove', this.removeItem);
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
                    this.subViews.cartDropDownContent.addClass(CSS_CLASSES.LOADER);
                },

                hide: function() {
                   this.subViews.cartDropDownContent.removeClass(CSS_CLASSES.LOADER);
                }
            },

            show: function() {
                this.subViews.cartDropDown.addClass(CSS_CLASSES.CART_SHOW);
                this.showOverlay();

                this.tid && clearTimeout(this.tid);
                this.tid = setTimeout(this.hide.bind(this), this.timeToHide);
            },

            hide: function() {
                this.tid && clearTimeout(this.tid);
                this.subViews.cartDropDown.removeClass(CSS_CLASSES.CART_SHOW);
                this.hideOverlay();
            },

            showOverlay: function() {
                overlay.show();
                overlay.on('click', this.bindedHide);
            },

            hideOverlay: function() {
                overlay.hide();
                overlay.off('click', this.bindedHide);
            },

            /**
             * Удаление продукта из корзины. Событие срабатывет до того как отработал AJAX запрос, сразу после вызова метода `remove` у коллекции
             *
             * @method      removeHandler
             * @memberOf    module:enter.cart.view~EnterCartView#
             *
             * @listens     module:enter.cart.collection~CartCollection#remove
             */
            removeHandler: function( removedItem ) {
                console.info('module:enter.cart.view~EnterCartView#removeHandler');
                this.loader.show.call(this);
                this.show();
            },

            /**
             * Удаление продукта из корзины. Событие срабатывает после завершения AJAX запроса
             *
             * @method      removeItem
             * @memberOf    module:enter.cart.view~EnterCartView#
             *
             * @listens     module:enter.cart.collection~CartCollection#silentRemove
             *
             * @param       {module:enter.cart.model}    removedItem    Модель удаляемого товара
             */
            removeItem: function( removedItem ) {
                var
                    id = removedItem.get('id');

                console.groupCollapsed('module:enter.cart.view~EnterCartView#removeItem || product id ', id);
                console.dir(removedItem);
                console.groupEnd();

                if ( !this.subViews.hasOwnProperty(id) ) {
                    console.warn('Subview %s not found (', id);
                    return;
                }

                this.subViews[id].destroy();
                delete this.subViews[id];
            },

            /**
             * Добавление продукта в корзину. Событие срабатывет до того как отработал AJAX запрос, но уже был послан
             *
             * @method      addHandler
             * @memberOf    module:enter.cart.view~EnterCartView#
             *
             * @listens     module:enter.cart.collection~CartCollection#add
             */
            addHandler: function() {
                console.info('module:enter.cart.view~EnterCartView#addHandler');
                this.loader.show.call(this);
                this.show();
            },

            /**
             * Создание и добавление представления элемента корзины.
             * Если такой элемент уже есть в корзине, рендер пропускается.
             *
             * @method      addHandler
             * @memberOf    module:enter.cart.view~EnterCartView#
             */
            addItem: function( item ) {
                var
                    id = item.get('id'),
                    tmpCartItemNode;

                if ( this.subViews.hasOwnProperty(id) ) {
                    return;
                }

                console.groupCollapsed('module:enter.cart.view~EnterCartView#addItem || product id ', id);
                console.dir(item);
                console.groupEnd();

                this.subViews[id] = new CartItemView({
                    collection: this.collection,
                    model: item,
                    template: ( this.$el.hasClass(CSS_CLASSES.CART_PAGE) ) ? TEMPLATES.CART_PAGE_ITEM : TEMPLATES.CART_DROPDOWN_ITEM
                });

                tmpCartItemNode = this.subViews[id].render();
                this.subViews.cartItemsWrapper.prepend(tmpCartItemNode);
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
            },

            /**
             * Отрисовка элементов корзины. Срабатывает каждый раз после успешной синхронизации корзины с сервером.
             *
             * @method      render
             * @memberOf    module:enter.cart.view~EnterCartView#
             *
             * @listens     module:enter.cart.collection~CartCollection#syncEnd
             */
            render: function() {
                var
                    cartQ = this.collection.quantity;

                console.info('module:enter.cart.view~EnterCartView#render');
                console.log('cart quantity', cartQ);

                this.loader.hide.call(this);

                if ( !cartQ ) {
                    console.warn('cart empty');
                    this.$el.addClass(CSS_CLASSES.EMPTY_CART);
                    this.$el.removeClass(CSS_CLASSES.FULL_CART);
                    this.hide();
                } else {
                    console.warn('cart full');
                    this.$el.addClass(CSS_CLASSES.FULL_CART);
                    this.$el.removeClass(CSS_CLASSES.EMPTY_CART);
                }

                this.subViews.cartSum.text(this.collection.total);
                this.subViews.cartQuantity.text(this.collection.quantity);

                this.collection.each(this.addItem.bind(this));
            }
        }));
    }
);
