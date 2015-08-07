/**
 * @module      enter.productkit.view
 * @version     0.1
 *
 * @requires    App
 * @requires    enter.BaseViewClass
 * @requires    enter.productkit.collection
 * @requires    enter.productkit.item.view
 * @requires    printPrice
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.productkit.view',
        [
            'App',
            'enter.BaseViewClass',
            'enter.productkit.collection',
            'enter.productkit.item.view',
            'printPrice'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, BaseViewClass, ProductKitCollection, ProductKitItemView, printPrice ) {
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
                BUY_BUTTON: 'js-buy-button',
                BASESET_CHECKBOX: 'js-base-set',
                TOTAL_SUM: 'js-kit-total-sum',
                TOTAL_QUANTITY: 'js-kit-total-quantity',
                WRAPPER: 'js-kit-wrapper'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление набора
             * @memberOf    module:enter.productkit.view~
             * @augments    module:BaseViewClass
             * @constructs  ProductKitView
             */
            initialize: function( options ) {
                this.collection = new ProductKitCollection([], {
                    productUi: options.productUi
                });

                this.listenTo(this.collection, 'changeTotalQuantity', this.changeQuantity);
                this.listenTo(this.collection, 'changeTotalSum', this.changeSum);
                this.listenTo(this.collection, 'changeBaseSet', this.changeBaseSet);
                this.listenTo(this.collection, 'syncEnd', this.render);

                this.wrapper        = this.$el.find('.' + CSS_CLASSES.WRAPPER);
                this.totalQuantity  = this.$el.find('.' + CSS_CLASSES.TOTAL_QUANTITY);
                this.totalSum       = this.$el.find('.' + CSS_CLASSES.TOTAL_SUM);
                this.baseSetChekbox = this.$el.find('.' + CSS_CLASSES.BASESET_CHECKBOX);
                this.buyButton      = this.$el.find('.' + CSS_CLASSES.BUY_BUTTON);

                 // Setup events
                this.events['click .' + CSS_CLASSES.BASESET_CHECKBOX] = 'checkBaseSet';
                this.events['click .' + CSS_CLASSES.BUY_BUTTON]       = 'buyButtonHandler';

                this.checkBaseSet();

                // Apply events
                this.delegateEvents();
            },

            events: {},

            /**
             * Установка набора в базовый
             *
             * @method      checkBaseSet
             * @memberOf    module:enter.productkit.view~ProductKitView#
             */
            checkBaseSet: function() {
                this.baseSetChekbox.prop('checked', true);

                if ( !this.collection.isBaseSet ) {
                    this.collection.toBaseSet();
                }
            },

            /**
             * Хандлер нажатия на кнопку купить
             *
             * @method      buyButtonHandler
             * @memberOf    module:enter.productkit.view~ProductKitView#
             */
            buyButtonHandler: function() {
                this.collection.each(function( model ) {
                    var
                        clone       = model.clone(),
                        id          = model.get('id'),
                        quantity    = model.get('quantity'),
                        modelInCart = App.cart.get(id);

                    clone.set({
                        quantity: ( modelInCart ) ? quantity + modelInCart.get('quantity') : quantity
                    });

                    if ( quantity > 0 ) {
                        App.cart.trigger('add', clone);
                    }
                });
            },

            /**
             * Хандлер изменения количества товаров в наборе
             *
             * @method      changeQuantity
             * @memberOf    module:enter.productkit.view~ProductKitView#
             *
             * @listens     module:enter.productkit.collection~ProductKitCollection#changeTotalQuantity
             */
            changeQuantity: function( event ) {
                var
                    newQuantity = event.totalQuantity;

                this.totalQuantity.text(newQuantity);

                if ( !newQuantity ) {
                    this.buyButton.hide();
                } else {
                    this.buyButton.show();
                }
            },

            /**
             * Хандлер изменения стоимости набора
             *
             * @method      changeSum
             * @memberOf    module:enter.productkit.view~ProductKitView#
             *
             * @listens     module:enter.productkit.collection~ProductKitCollection#changeTotalSum
             */
            changeSum: function( event ) {
                var
                    newSum = event.totalSum;

                this.totalSum.text(printPrice(newSum));
            },

            /**
             * Сброса набора в базовый
             *
             * @method      changeBaseSet
             * @memberOf    module:enter.productkit.view~ProductKitView#
             *
             * @listens     module:enter.productkit.collection~ProductKitCollection#changeBaseSet
             */
            changeBaseSet: function( event ) {
                var
                    isBaseSet = event.isBaseSet;

                this.baseSetChekbox.prop('checked', isBaseSet);
            },

            /**
             * Отрисовка элементов набора
             *
             * @method      render
             * @memberOf    module:enter.productkit.view~ProductKitView#
             *
             * @listens     module:enter.productkit.collection~ProductKitCollection#syncEnd
             */
            render: function() {
                var
                    self = this;

                console.groupCollapsed('module:enter.productkit.view~ProductKitView#render');
                console.dir(this.collection.models);
                console.groupEnd();

                this.collection.each(function( model ) {
                    var
                        id          = model.get('id'),
                        subViewName = 'kititem_' + id;

                    self.subViews[subViewName] = new ProductKitItemView({
                        model: model
                    });

                    self.wrapper.append(self.subViews[subViewName].render());
                });
            }
        }));
    }
);
