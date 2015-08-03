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
            'App',
            'Backbone',
            'underscore',
            'enter.BaseCollectionClass',
            'enter.product.model'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, Backbone, _, BaseCollectionClass, ProductModel ) {
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
                var
                    id  = addedModel.get('id'),
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
                console.groupCollapsed('module:enter.compare.collection~CompareCollection#removeFromCompare || product id', model.get('id'));

                var
                    id  = model.get('id'),
                    url = this.deleteUrl + id;

                this.ajax({
                    type: 'GET',
                    url: url,
                    success: this.updateCompare.bind(this)
                });

                console.dir(model);
                console.groupEnd();

                 App.productsCollection.get(id).set({'inCompare': false});
            },

            /**
             * Обновление моделей в колекции корзины. Удаляет лишние модели из коллекции, запускает обновление для измененных моделей и добавляет в коллекцию новые
             *
             * @method      updateModels
             * @memberOf    module:enter.compare.collection~CompareCollection#
             *
             * @fires       module:enter.compare.collection~CompareCollection#add
             * @fires       module:enter.compare.collection~CompareCollection#remove
             *
             * @param       {Array}    models   Модели товаров в корзине на сервере
             */
            updateModels: function( models ) {
                var
                    reverseModel = models.reverse(),
                    self         = this,
                    i, j,

                    /**
                     * Выявление лишней модели в коллекции
                     *
                     * @method      checkServerModels
                     * @memberOf    module:enter.compare.collection~CompareCollection#
                     * @private
                     *
                     * @param       {Array}                     modelsFromServer    Модели пришедшие с сервера
                     * @param       {module:enter.cart.model}   collectionModel     Проверяемая модель из коллекции
                     *
                     * @return      {Boolean}                   Возвращает true если модель из коллекции присутствует в массиве пришедшем с сервера
                     */
                    checkServerModels = function( modelsFromServer, collectionModel ) {
                        for ( j = 0; j < modelsFromServer.length; j++ ) {
                            if ( collectionModel.get('id') == modelsFromServer[j].id ) {
                                return true;
                            }
                        }

                        return false;
                    },

                    /**
                     * Проверка модели.
                     * Сравнивает модель с сервера и модели в коллекции
                     * Если с одной из моделей совпадает линк, но не совпадает тип, такую модель следует удалить из коллекции(чтобы удалить ее view), а затем добавить заного.
                     *
                     * @method      checkModelExits
                     * @memberOf    module:enter.compare.collection~CompareCollection#
                     * @private
                     *
                     * @param       {Object}    model     Проверяемая модель пришедшая с сервера
                     *
                     * @return      {Boolean}   Возвращает `true` если модель пришедшая с сервера присутствует в коллекции. Если атрибуты `type` различаются, но атрибут `categoryLink` идентичен между сравниваемыми моделями, то такую модель необходимо удалить и добавить заного.
                     */
                    checkModelExits = function( model ) {
                        if ( !self.models.length ) {
                            return false;
                        }

                        for ( j = 0; j < self.models.length; j++ ) {
                            if ( self.models[j].get('id') == model.id ) {
                                return true;
                            }
                        }

                        return false;
                    };


                for ( i = 0; i < reverseModel.length; i++ ) {
                    reverseModel[i].inCompare = true;
                    App.productsCollection.add(reverseModel[i], {merge: true});

                    if ( checkModelExits(reverseModel[i]) ) {
                        self.get(reverseModel[i].id).set(reverseModel[i]);
                    } else {
                        self.push(reverseModel[i], {silent:true});
                    }
                }

                // Если моделей пришло меньше чем есть в коллекции, необходимо удалить лишнюю модель
                console.groupCollapsed('find models for removing...');
                console.log('Models in collection:');
                console.dir(self.models);
                console.log('Models from server:');
                console.dir(models);
                console.groupEnd();

                for ( i = 0; i < self.models.length; i++ ) {
                    if ( !checkServerModels(models, self.models[i]) ) {
                        App.productsCollection.add(self.models[i], {merge: true});
                        self.remove(self.models[i], {silent:true});
                    }
                }
            },

            /**
             * Хандлер срабатывающий при получении новых моделей с сервера
             *
             * @method      updateCompare
             * @memberOf    module:enter.compare.collection~CompareCollection#
             *
             * @fires       module:enter.compare.collection~CompareCollection#syncEnd
             *
             * @param       {Object}    data   Новые данные сравнения
             */
            updateCompare: function( data ) {
                var
                    compare     = data.compare || {},
                    product     = data.product,
                    compareSize = _.size(compare),
                    key;

                console.groupCollapsed('module:enter.compare.collection~CompareCollection#updateCompare');
                console.dir(data);
                console.groupEnd();

                this.updateModels(_.toArray(compare));

                this.trigger('syncEnd', {
                    size: compareSize,
                    product: product
                });
            }

        }));
    }
);
