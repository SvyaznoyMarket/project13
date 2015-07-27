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
        modulesDep = JSON.parse(document.getElementById('js-modules-definitions').innerHTML),

        /**
         * Первая точка входа в приложение
         *
         * @method  initApp
         */
        initApp = function( App, Backbone, _, PageView, ProductsCollection, CartCollection ) {
            console.info('Application initialize');

            // Добавляем возможность добавлять события к объекту приложения
            _.extend(App, Backbone.Events);

            /**
             * Главное View приложения
             * @todo сюда переписать весь postinit
             * Уже с него вешать все обработчики событий
             */
            App.pageView = new PageView({
                el: document.body
            });

            App.productsCollection = new ProductsCollection();
            App.cart               = new CartCollection();
        },


        /**
         * Расширяем стандартные функции YM Modules
         */
        extendModules = function( enterModules, loadModule ) {
            modules.setOptions({
                loadModules: loadModule,
                findDep: function( dep ) {
                    console.info('Module %s find in file %s', dep, modulesDep[dep]);
                    return modulesDep.hasOwnProperty(dep);
                }
            });
        };

    // Загружаем 'enter.modules' только для того чтобы сразу объявить все модули.
    // Не очень кошерно, на самом деле
    modules.require(['enter.modules', 'loadModule'], extendModules);

    /**
     * Запрос модулей для инициализации приложения.
     * Вызываем extendBackbone для того чтобы сразу расширить функционал Backbone'a
     */
    modules.require([
        'App',
        'extendBackbone',
        'underscore',
        'enter.page.view',
        'enter.products.collection',
        'enter.cart.collection'
    ], initApp);
}(
    this,
    this.document,
    this.modules
);