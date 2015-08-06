/**
 * @module      enter.productkit.model
 * @version     0.1
 *
 * @requires    enter.BaseModelClass
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.productkit.model',
        [
            'enter.BaseModelClass'
        ],
        module
    );
}(
    this.modules,
    function( provide, BaseModelClass ) {
        'use strict';

        provide(BaseModelClass.extend(/** @lends module:enter.productkit.model~ProductKitModel */{
            /**
             * Инициализация модели элемента набора
             *
             * @classdesc   Модель элемента набора
             * @memberOf    module:enter.productkit.model~
             * @augments    module:enter.BaseModelClass
             * @constructs  ProductKitModel
             */
            initialize: function() {
                var
                    price       = this.get('price'),
                    nowQuantity = this.get('count'),
                    newSum      = price * nowQuantity;

                console.info('module:enter.productkit.model~ProductKitModel#initialize');

                this.set({'quantity': nowQuantity});
                this.set({'sum': newSum});
                this.listenTo(this, 'change:quantity', this.changeQuantity);
            },

            /**
             * Хандлер изменения количества товара
             *
             * @method      changeQuantity
             * @memberOf    module:enter.productkit.model~ProductKitModel#
             *
             * @listens     module:enter.productkit.model~ProductKitModel#change:quantity
             *
             * @param       {module:enter.productkit.model~ProductKitModel}     model
             * @param       {Number}                                            newQuantity     Новое количество товара
             */
            changeQuantity: function( model, newQuantity ) {
                var
                    price = this.get('price'),
                    sum   = this.get('sum'),
                    newSum;

                if ( newQuantity === 0 ) {
                    this.set({removedItem: true});
                } else {
                    this.set({removedItem: false});
                }

                newSum = price * newQuantity;
                this.set({'sum': newSum});
            }
        }));
    }
);
