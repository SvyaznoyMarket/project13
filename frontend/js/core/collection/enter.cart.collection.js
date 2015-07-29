/**
 * @module      enter.cart.collection
 * @version     0.1
 *
 * @requires    App
 * @requires    enter.BaseCollectionClass
 * @requires    enter.cart.model
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.cart.collection',
        [
            'App',
            'enter.BaseCollectionClass',
            'enter.cart.model'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, BaseCollectionClass, CartModel, userInfo ) {
        'use strict';

        provide(BaseCollectionClass.extend(/** @lends module:enter.cart.collection~CartCollection */{
            model: CartModel,

            /**
             * Инициализация коллекции корзины
             *
             * @classdesc   Коллекция корзины
             * @memberOf    module:enter.cart.collection~
             * @augments    module:enter.BaseCollectionClass
             * @constructs  CartCollection
             */
            initialize: function() {
                var
                    self = this;

                console.info('module:enter.cart.collection~CartCollection#initialize');

                this.listenTo(this, 'add', this.addToCart);
                this.listenTo(this, 'remove', this.removeFromCart);

                window.onload = function() {
                    modules.require(['enter.user'], self.updateCart.bind(self));
                }
            },

            /**
             * Добавление товара в коллекцию корзину
             *
             * @method      addToCart
             * @memberOf    module:enter.cart.collection~CartCollection#
             *
             * @listens     module:enter.cart.collection~CartCollection#add
             *
             * @param       {module:enter.cart.model}    addedModel     Модель добавляемого товара
             */
            addToCart: function( addedModel ) {
                console.groupCollapsed('module:enter.cart.collection~CartCollection#addToCart || product id', addedModel.get('id'));
                console.dir(addedModel);
                console.groupEnd();

                this.ajax({
                    type: 'GET',
                    url: addedModel.get('addUrl'),
                    success: this.updateCart.bind(this)
                });
            },

            /**
             * Удаление товара из коллекции корзины
             *
             * @method      removeFromCart
             * @memberOf    module:enter.cart.collection~CartCollection#
             *
             * @listens     module:enter.cart.collection~CartCollection#remove
             *
             * @param       {module:enter.cart.model}    removedModel   Модель удаляемого товара
             */
            removeFromCart: function( removedModel ) {
                var
                    tmpModel  = removedModel.clone(),
                    removedId = tmpModel.get('id');

                console.groupCollapsed('module:enter.cart.collection~CartCollection#removeFromCart || product id', removedId);
                console.dir(removedModel);


                tmpModel.set({'inCart': false, 'quantity': 0});

                this.ajax({
                    type: 'GET',
                    url: tmpModel.get('deleteUrl'),
                    success: this.updateCart.bind(this)
                });

                App.productsCollection.get(removedId).set({'inCart': false, 'quantity': 1});
                console.log('finish!');
                console.groupEnd();
            },

            /**
             * Обновление моделей в колекции корзины. Удаляет лишние модели из коллекции, запускает обновление для измененных моделей и добавляет в коллекцию новые
             *
             * @method      updateModels
             * @memberOf    module:enter.cart.collection~CartCollection#
             *
             * @fires       module:enter.cart.collection~CartCollection#add
             * @fires       module:enter.cart.collection~CartCollection#silentAdd
             * @fires       module:enter.cart.collection~CartCollection#remove
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
                     * @memberOf    module:enter.cart.collection~CartCollection#
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
                     * @memberOf    module:enter.cart.collection~CartCollection#
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
                    reverseModel[i].inCart = true;
                    App.productsCollection.add(reverseModel[i], {merge: true});

                    if ( checkModelExits(reverseModel[i]) ) {
                        self.get(reverseModel[i].id).set(reverseModel[i]);
                    } else {
                        self.push(reverseModel[i], {silent:true});
                        self.trigger('silentAdd', self.get(reverseModel[i].id));
                    }
                }

                // Если моделей пришло меньше чем есть в коллекции, необходимо удалить лишнюю модель
                console.groupCollapsed('find models for removing...');
                console.log('Models in collection:');
                console.dir(self.models);
                console.log('Models from server:');
                console.dir(models);
                console.groupEnd();
                // console.log(models.length);
                // console.log(self.models.length);
                for ( i = 0; i < self.models.length; i++ ) {
                    if ( !checkServerModels(models, self.models[i]) ) {
                        console.warn('Need remove product');
                        self.models[i].set({'quantity': 1});
                        App.productsCollection.add(self.models[i], {merge: true});
                        self.remove(self.models[i], {silent:true});
                        self.trigger('silentRemove', self.models[i]);
                    }
                }
            },

            /**
             * Хандлер срабатывающий при получении новых моделей с сервера
             *
             * @method      updateCart
             * @memberOf    module:enter.cart.collection~CartCollection#
             *
             * @fires       module:enter.cart.collection~CartCollection#syncEnd
             *
             * @param       {Array}    newCartData   Новые данные корзины
             */
            updateCart: function( newCartData ) {
                console.groupCollapsed('module:enter.cart.collection~CartCollection#updateCart');
                console.dir(newCartData);
                console.groupEnd();

                this.total    = newCartData.cart.sum || 0;
                this.quantity = newCartData.cart.full_quantity || 0;

                this.updateModels(newCartData.cart.products || []);

                this.trigger('syncEnd');
            }
        }));
    }
);
