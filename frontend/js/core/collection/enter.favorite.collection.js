/**
 * @module      enter.favorite.collection
 * @version     0.1
 *
 * @requires    App
 * @requires    Backbone
 * @requires    underscore
 * @requires    enter.BaseCollectionClass
 * @requires    enter.product.model
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.favorite.collection',
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

            addUrl: '/favorite/add-product/',
            deleteUrl: '/favorite/delete-product/',

            /**
             * Инициализация коллекции избранного
             *
             * @classdesc   Коллекция избранного
             * @memberOf    module:enter.favorite.collection~
             * @augments    module:Backbone.Collection
             * @constructs  FavoriteCollection
             */
            initialize: function() {
                console.info(' module:enter.favorite.collection~FavoriteCollection#initialize');
                this.listenTo(this, 'add', this.addToFavorite);
                this.listenTo(this, 'remove', this.removeFromfavorite);
            },

            /**
             * Добавление товара в коллекцию избранного
             *
             * @method      addToFavorite
             * @memberOf    module:enter.favorite.collection~FavoriteCollection#
             *
             * @listens     module:enter.favorite.collection~FavoriteCollection#add
             *
             * @param       {module:enter.product.model}    model     Модель добавляемого товара
             */
            addToFavorite: function( model ) {
                var
                    ui  = model.get('ui'),
                    url = this.addUrl + ui;

                console.groupCollapsed('module:enter.favorite.collection~FavoriteCollection#addToFavorite || product id', model.get('id'));

                this.ajax({
                    type: 'GET',
                    url: url,
                    success: this.updateFavorite.bind(this)
                });

                console.dir(model);
                console.groupEnd();
            },

            /**
             * Удаление товара из коллекции избранного
             *
             * @method      removeFromfavorite
             * @memberOf    module:enter.favorite.collection~FavoriteCollection#
             *
             * @listens     module:enter.favorite.collection~FavoriteCollection#remove
             *
             * @param       {module:enter.product.model}    model     Модель удаляемого товара
             */
            removeFromfavorite: function ( model ) {
                console.groupCollapsed('module:enter.favorite.collection~FavoriteCollection#removeFromfavorite || product id', model.get('id'));

                var
                    id  = model.get('id'),
                    ui  = model.get('ui'),
                    url = this.deleteUrl + ui;

                this.ajax({
                    type: 'GET',
                    url: url,
                    success: this.updateFavorite.bind(this)
                });

                console.dir(model);
                console.groupEnd();

                App.productsCollection.get(id).set({'inFavorite': false});
            },

            /**
             * Обновление моделей в колекции избранного. Удаляет лишние модели из коллекции, запускает обновление для измененных моделей и добавляет в коллекцию новые
             *
             * @method      updateModels
             * @memberOf    module:enter.favorite.collection~FavoriteCollection#
             *
             * @fires       module:enter.favorite.collection~FavoriteCollection#add
             * @fires       module:enter.favorite.collection~FavoriteCollection#remove
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
                     * @memberOf    module:enter.favorite.collection~FavoriteCollection#
                     * @private
                     *
                     * @param       {Array}                         modelsFromServer    Модели пришедшие с сервера
                     * @param       {module:enter.product.model}    collectionModel     Проверяемая модель из коллекции
                     *
                     * @return      {Boolean}                       Возвращает true если модель из коллекции присутствует в массиве пришедшем с сервера
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
                     * @memberOf    module:enter.favorite.collection~FavoriteCollection#
                     * @private
                     *
                     * @param       {Object}    model     Проверяемая модель пришедшая с сервера
                     *
                     * @return      {Boolean}   Возвращает `true` если модель пришедшая с сервера присутствует в коллекции
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
                    reverseModel[i].inFavorite = true;
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
             * @method      updateFavorite
             * @memberOf    module:enter.favorite.collection~FavoriteCollection#
             *
             * @fires       module:enter.favorite.collection~FavoriteCollection#syncEnd
             *
             * @param       {Object}    data   Новые данные сравнения
             */
            updateFavorite: function( data ) {
                var
                    favourite = data.favourite || {},
                    product   = data.product,
                    size      = _.size(favourite);

                console.groupCollapsed('module:enter.favorite.collection~FavoriteCollection#updateFavorite');
                console.dir(data);
                console.groupEnd();

                this.updateModels(_.toArray(favourite));

                this.trigger('syncEnd', {
                    size: size,
                    product: product
                });
            }

        }));
    }
);
