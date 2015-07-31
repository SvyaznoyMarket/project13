/**
 * @module      enter.compare.collection
 * @version     0.1
 *
 * @requires    Backbone
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
             * @memberOf    module:enter.products.collection~
             * @augments    module:Backbone.Collection
             * @constructs  CompareCollection
             */
            initialize: function() {
                console.info(' module:enter.compare.collection~ProductsCollection#initialize');
                this.listenTo(this, 'add', this.addToCompare);
                this.listenTo(this, 'remove', this.removeFromCompare);
            },

            addToCompare: function( addedModel ) {

                var id  = addedModel.get('id'),
                    url = this.addUrl + id;

                console.groupCollapsed('module:enter.compare.collection~CartCollection#addToCompare || product id', addedModel.get('id'));

                this.ajax({
                    type: 'GET',
                    url: url,
                    success: function(){

                    }
                });

                console.dir(addedModel);
                console.groupEnd();
            },

            update: function() {

            },

            removeFromCompare: function ( model ) {

                var id  = model.get('id'),
                    url = this.deleteUrl + id;

                console.groupCollapsed('module:enter.compare.collection~CartCollection#removeFromCompare || product id', model.get('id'));

                this.ajax({
                    type: 'GET',
                    url: url,
                    success: function(){

                    }
                });

                console.dir(model);
                console.groupEnd();
            }

        }));
    }
);
