/**
 * @module      enter.cart.collection
 * @version     0.1
 *
 * @requires    App
 * @requires    underscore
 * @requires    enter.BaseCollectionClass
 * @requires    enter.cart.model
 * @requires    urlHelper
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.cart.collection',
        [
            'App',
            'underscore',
            'enter.BaseCollectionClass',
            'enter.cart.model',
            'urlHelper'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, _, BaseCollectionClass, CartModel, urlHelper ) {
        'use strict';

        provide(BaseCollectionClass.extend(/** @lends module:enter.cart.collection~CartCollection */{
            timeToSend: 100,
            total: 0,
            quantity: 0,
            model: CartModel,

            addUrl: '/cart/add-product/',
            deleteUrl: '/cart/delete-product/',
            _addUrl: ' /cart/set-products',

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
                    self = this,
                    SaveToServerCollection = BaseCollectionClass.extend({
                        model: self.model
                    });

                console.info('module:enter.cart.collection~CartCollection#initialize');

                this.listenTo(this, 'add', this.addToCart);
                this.listenTo(this, 'remove', this.removeFromCart);

                this.saveToServerCollection = new SaveToServerCollection();
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
                var
                    id       = addedModel.get('id'),
                    quantity = addedModel.get('quantity'),

                    url = urlHelper.addParams(this.addUrl + id, {
                        quantity: quantity
                    });

                console.groupCollapsed('module:enter.cart.collection~CartCollection#addToCart || product id', addedModel.get('id'));

                this.saveToServerCollection.add(addedModel);

                this.request && this.request.abort();
                clearTimeout(this.timeout_ID);
                this.timeout_ID = setTimeout(this.sendCartToServer.bind(this), this.timeToSend);

                console.dir(addedModel);
                console.groupEnd();
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

                tmpModel.set({'inCart': false, 'quantity': 0});

                this.ajax({
                    type: 'GET',
                    url: this.deleteUrl + removedId,
                    success: this.updateCart.bind(this)
                });

                console.dir(removedModel);
                console.groupEnd();

                App.productsCollection.get(removedId).set({'inCart': false, 'quantity': 1});
            },

            /**
             * Отправка корзины на сервер
             *
             * @method      sendCartToServer
             * @memberOf    module:enter.cart.collection~CartCollection#
             */
            sendCartToServer: function() {
                var
                    products = _.map(this.saveToServerCollection.toJSON(), function( obj ) {
                        return {
                            id: obj.attributes.id,
                            quantity: obj.attributes.quantity,
                        }
                    }),
                    url = urlHelper.addParams(this._addUrl, {
                        product: products
                    });

                console.groupCollapsed('module:enter.cart.collection~CartCollection#sendCartToServer');
                console.log(url);
                console.dir(products);
                console.dir(this.saveToServerCollection.toJSON());
                console.groupEnd();

                this.request = this.ajax({
                    type: 'GET',
                    url: url,
                    success: this.updateCart.bind(this)
                });
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
                    reverseModel = models,
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
                var
                    self = this;

                console.groupCollapsed('module:enter.cart.collection~CartCollection#updateCart');
                console.dir(newCartData);


                this.updateModels(newCartData.cart.products || []);
                this.saveToServerCollection.reset();

                this.quantity = newCartData.cart.fullQuantity || 0;
                this.total    = 0;

                /**
                 * Расчет общей стоимости коризны. Это вообще полная шляпа, но пока backend нихера сам не считает
                 */
                this.each(function( model ) {
                    var
                        q = model.get('quantity'),
                        p = model.get('price');

                    self.total += q * p;
                });

                console.log('Общая стоимость корзины', this.total);
                console.log('Количество наименований в корзине', this.quantity);

                console.groupEnd();

                this.trigger('syncEnd');
            }
        }));
    }
);
