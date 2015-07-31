/**
 * @module      enter.product.model
 * @version     0.1
 *
 * @requires    Backbone
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.product.model',
        [
            'Backbone'
        ],
        module
    );
}(
    this.modules,
    function( provide, Backbone ) {
        'use strict';

        provide(Backbone.Model.extend(/** @lends module:enter.product.model~ProductModel */{
            defaults: {
                quantity: 1,
                inCompare: false,
                inCart: false
            },

            /**
             * Инициализация модели карточки продукта
             *
             * @classdesc   Модель карточки продукта
             * @memberOf    module:enter.product.model~
             * @augments    module:Backbone.Model
             * @constructs  ProductModel
             */
            initialize: function() {
                console.groupCollapsed('Product %s initialized', this.get('id'));
                console.log(this.attributes);
                console.groupEnd();
            }
        }));
    }
);
