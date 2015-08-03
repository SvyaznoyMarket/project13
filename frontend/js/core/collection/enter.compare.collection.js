/**
 * @module      enter.compare.collection
 * @version     0.1
 *
 * @requires    Backbone
 * @requires    enter.BaseCollectionClass
 * @requires    enter.product.model
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.compare.collection',
        [
            'Backbone',
            'enter.BaseCollectionClass',
            'enter.product.model'
        ],
        module
    );
}(
    this.modules,
    function( provide, Backbone, BaseCollectionClass, ProductModel ) {
        'use strict';

        provide(BaseCollectionClass.extend({

            model: ProductModel,

            addUrl: '/compare/add-product/',
            deleteUrl: '/compare/delete-product/',

            /**
             * Инициализация коллекции карточек продукта
             *
             * @classdesc   Коллекция карточек продукта
             * @memberOf    module:enter.compare.collection~
             * @augments    module:Backbone.Collection
             * @constructs  CompareCollection
             */
            initialize: function() {
                console.info(' module:enter.compare.collection~CompareCollection#initialize');
                this.listenTo(this, 'add', this.addToCompare);
                this.listenTo(this, 'remove', this.removeFromCompare);
            },

            /**
             * Добавление товара в коллекцию сравнения
             *
             * @method      addToCompare
             * @memberOf    module:enter.compare.collection~CompareCollection#
             *
             * @listens     module:enter.compare.collection~CompareCollection#add
             *
             * @param       {module:enter.product.model}    addedModel     Модель добавляемого товара
             */
            addToCompare: function( addedModel ) {

                var id  = addedModel.get('id'),
                    url = this.addUrl + id;

                console.groupCollapsed('module:enter.compare.collection~CompareCollection#addToCompare || product id', addedModel.get('id'));

                this.ajax({
                    type: 'GET',
                    url: url,
                    success: this.updateCompare.bind(this)
                });

                console.dir(addedModel);
                console.groupEnd();
            },

            /**
             * Удаление товара из коллекции сравнения
             *
             * @method      removeFromCompare
             * @memberOf    module:enter.compare.collection~CompareCollection#
             *
             * @listens     module:enter.compare.collection~CompareCollection#remove
             *
             * @param       {module:enter.product.model}    model     Модель удаляемого товара
             */
            removeFromCompare: function ( model ) {

                var id  = model.get('id'),
                    url = this.deleteUrl + id;

                console.groupCollapsed('module:enter.compare.collection~CompareCollection#removeFromCompare || product id', model.get('id'));

                this.ajax({
                    type: 'GET',
                    url: url,
                    success: this.updateCompare.bind(this)
                });

                console.dir(model);
                console.groupEnd();
            },

            /**
             * Хандлер срабатывающий при получении новых моделей с сервера
             *
             * @method      updateCompare
             * @memberOf    module:enter.compare.collection~CompareCollection#
             *
             * @fires       module:enter.compare.collection~CompareCollection#syncEnd
             *
             * @param       {Array}    data   Новые данные сравнения
             */
            updateCompare: function( data ) {
                console.groupCollapsed('module:enter.compare.collection~CompareCollection#updateCompare');
                console.dir(data);
                console.groupEnd();
            }

        }));
    }
);
