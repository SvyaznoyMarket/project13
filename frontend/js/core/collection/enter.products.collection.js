/**
 * @module      enter.products.collection
 * @version     0.1
 *
 * @requires    Backbone
 * @requires    enter.product.model
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.products.collection',
        [
            'Backbone',
            'enter.product.model'
        ],
        module
    );
}(
    this.modules,
    function( provide, Backbone, ProductModel ) {
        'use strict';

        provide(Backbone.Collection.extend(/** @lends module:enter.products.collection~ProductsCollection */{
            model: ProductModel,

            /**
             * Инициализация коллекции карточек продукта
             *
             * @classdesc   Коллекция карточек продукта
             * @memberOf    module:enter.products.collection~
             * @augments    module:Backbone.Collection
             * @constructs  ProductsCollection
             */
            initialize: function() {
                console.info(' module:enter.products.collection~ProductsCollection#initialize');
            }
        }));
    }
);
