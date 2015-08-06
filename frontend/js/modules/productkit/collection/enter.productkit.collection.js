/**
 * @module      enter.productkit.collection
 * @version     0.1
 *
 * @requires    enter.BaseCollectionClass
 * @requires    enter.productkit.model
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.productkit.collection',
        [
            'enter.BaseCollectionClass',
            'enter.productkit.model'
        ],
        module
    );
}(
    this.modules,
    function( provide, BaseCollectionClass, ProductKitModel ) {
        'use strict';

        provide(BaseCollectionClass.extend(/** @lends module:enter.productkit.collection~ProductKitCollection */{
            model: ProductKitModel,

            getUrl: '/ajax/product/kit/',

            /**
             * Инициализация коллекции набора
             *
             * @classdesc   Коллекция набора
             * @memberOf    module:enter.productkit.collection~
             * @augments    module:enter.BaseCollectionClass
             * @constructs  ProductKitCollection
             */
            initialize: function( models, options ) {

                console.info('module:enter.productkit.collection~ProductKitCollection#initialize');

                this.listenTo(this, 'change:quantity', this.getTotalQuantity);
                this.listenTo(this, 'change:sum', this.getTotalSum);
                this.listenTo(this, 'add', this.getTotalQuantity);
                this.listenTo(this, 'add', this.getTotalSum);

                this.productUi = options.productUi;
                this.getUrl    = this.getUrl + options.productUi;

                this.getKitElements();
            },

            /**
             * Получение элементов набора
             *
             * @method      getKitElements
             * @memberOf    module:enter.productkit.collection~ProductKitCollection#
             */
            getKitElements: function() {
                this.ajax({
                    type: 'GET',
                    url: this.getUrl,
                    success: this.addProductkitElements.bind(this)
                });
            },

            /**
             * Получение общего количества товаров в наборе
             *
             * @method      getTotalQuantity
             * @memberOf    module:enter.productkit.collection~ProductKitCollection#
             *
             * @listens     module:enter.productkit.collection~ProductKitCollection#add
             * @listens     module:enter.productkit.collection~ProductKitCollection#change:quantity
             *
             * @fires       module:enter.productkit.collection~ProductKitCollection#changeTotalQuantity
             * @fires       module:enter.productkit.collection~ProductKitCollection#changeBaseSet
             */
            getTotalQuantity: function() {
                var
                    isBaseSet = true,
                    total     = 0,
                    modelQuantity,
                    modelKitCount,
                    i;

                console.groupCollapsed('module:enter.productkit.collection~ProductKitCollection#getTotalQuantity');

                for ( i = 0; i < this.models.length; i++ ) {
                    modelQuantity = this.models[i].get('quantity');
                    modelKitCount = this.models[i].get('count');

                    total += modelQuantity;

                    if ( modelQuantity !== modelKitCount ) {
                        isBaseSet = false;
                    }
                }

                console.log('total quantity', total);
                console.log('is base set? ', isBaseSet);

                this.totalQuantity = total;
                this.isBaseSet     = isBaseSet;

                this.trigger('changeTotalQuantity', {totalQuantity: total});
                this.trigger('changeBaseSet', {isBaseSet: isBaseSet});

                console.groupEnd();
            },

            /**
             * Получение общей стоимости товаров в нмаборе
             *
             * @method      getTotalSum
             * @memberOf    module:enter.productkit.collection~ProductKitCollection#
             *
             * @listens     module:enter.productkit.collection~ProductKitCollection#add
             * @listens     module:enter.productkit.collection~ProductKitCollection#change:sum
             *
             * @fires       module:enter.productkit.collection~ProductKitCollection#changeTotalSum
             */
            getTotalSum: function() {
                var
                    total = 0,
                    tmpSum,
                    i;

                for ( i = 0; i < this.models.length; i++ ) {
                    tmpSum = ( typeof this.models[i].get('sum') !== 'number' ) ? +this.models[i].get('sum').replace(' ', '') : this.models[i].get('sum');
                    total += tmpSum;
                }

                console.groupCollapsed('module:enter.productkit.collection~ProductKitCollection#getTotalSum');
                console.log('total sum', total);
                console.groupEnd();

                this.totalSum = total;
                this.trigger('changeTotalSum', {totalSum: total});
            },

            /**
             * Восстановление к базовой комплектации
             *
             * @method      toBaseSet
             * @memberOf    module:enter.productkit.collection~ProductKitCollection#
             */
            toBaseSet: function() {
                var
                    modelKitCount,
                    i;

                for ( i = 0; i < this.models.length; i++ ) {
                    modelKitCount = this.models[i].get('count');
                    this.models[i].set({'quantity': modelKitCount});
                }
            },

            /**
             * Добавление элементов набора
             *
             * @method      addProductkitElements
             * @memberOf    module:enter.productkit.collection~ProductKitCollection#
             *
             * @param       {Object}    data
             */
            addProductkitElements: function( data ) {
                var
                    kitProducts = data.product.kitProducts || {},
                    template    = data.template,
                    key;

                console.groupCollapsed('module:enter.productkit.collection~ProductKitCollection#addProductkitElements');
                console.dir(data);
                console.groupEnd();

                for ( key in kitProducts ) {
                    if ( kitProducts.hasOwnProperty(key) ) {
                        kitProducts[key].template = template;
                        this.add(kitProducts[key]);
                    }
                }

                this.trigger('syncEnd');
            }
        }));
    }
);
