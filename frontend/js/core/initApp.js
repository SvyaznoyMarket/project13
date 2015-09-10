/**
 * @module      App
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'App',
        [],
        module
    );
}(
    this.modules,
    function( provide ) {
        'use strict';

        provide({});
    }
);


!function( window, document, modules ) {

    var
        /**
         * Первая точка входа в приложение
         *
         * @method  initApp
         */
        initApp = function( App, UserInfo, Backbone, _, PageView, ProductsCollection, CartCollection, CompareCollection, FavoriteCollection ) {
            console.info('Application initialize');

            // Добавляем возможность добавлять события к объекту приложения
            _.extend(App, Backbone.Events);

            App.productsCollection = new ProductsCollection();
            App.cart               = new CartCollection();
            App.compare            = new CompareCollection();
            App.favorite           = new FavoriteCollection();


            App.cart.updateCart(UserInfo);
            App.compare.updateCompare(UserInfo);
            App.favorite.updateFavorite(UserInfo);

            /**
             * Главное View приложения
             * @todo сюда переписать весь postinit
             * Уже с него вешать все обработчики событий
             */
            App.pageView = new PageView({
                el: document.body
            });
        },


        /**
         * Расширяем стандартные функции YM Modules
         */
        extendModules = function( enterModules, loadModule ) {
            var
                modulesDep = JSON.parse(document.getElementById('js-modules-definitions').innerHTML);

            modules.setOptions({
                loadModules: loadModule,
                findDep: function( dep ) {
                    console.info('Module %s find in file %s', dep, modulesDep[dep]);
                    return modulesDep.hasOwnProperty(dep);
                }
            });
        },

        domReady = function() {
            // Загружаем 'enter.modules' только для того чтобы сразу объявить все модули.
            // Не очень кошерно, на самом деле
            modules.require(['enter.modules', 'loadModule'], extendModules);

            /**
             * Запрос модулей для инициализации приложения.
             * Вызываем extendBackbone для того чтобы сразу расширить функционал Backbone'a
             */
            modules.require([
                'App',
                'enter.user',
                'extendBackbone',
                'underscore',
                'enter.page.view',
                'enter.products.collection',
                'enter.cart.collection',
                'enter.compare.collection',
                'enter.favorite.collection'
            ], initApp);
        };

    document.readyState == 'complete' ? domReady() : window.addEventListener('load', domReady);

    }(
    this,
    this.document,
    this.modules
);