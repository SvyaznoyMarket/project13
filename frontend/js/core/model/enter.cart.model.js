/**
 * @module      enter.cart.model
 * @version     0.1
 *
 * @requires    Backbone
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.cart.model',
        [
            'Backbone'
        ],
        module
    );
}(
    this.modules,
    function( provide, Backbone ) {
        'use strict';

        provide(Backbone.Model.extend(/** @lends module:enter.cart.model~CartItemModel */{
            defaults: {
                quantity: 1,
                inCompare: false,
                inCart: false
            },

            /**
             * Инициализация модели карточки продукта в корзине
             *
             * @classdesc   Модель карточки продукта в корзине
             * @memberOf    module:enter.cart.model~
             * @augments    module:Backbone.Model
             * @constructs  CartItemModel
             */
            initialize: function() {
                console.groupCollapsed('Cart item %s initialized', this.get('id'))
                console.log(this.attributes);
                console.groupEnd();
            }
        }));
    }
);
