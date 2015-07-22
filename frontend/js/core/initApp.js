!function( window, document, modules ) {

    var
        modulesDep = JSON.parse(document.body.getAttribute('data-value')),

        /**
         * Первая точка входа в приложение
         *
         * @method  initApp
         */
        initApp = function( EnterPageView, enterModules ) {
            console.info('Application initialize');

            /**
             * @todo сюда переписать весь postinit
             * Уже с него вешать все обработчики событий
             */
            new EnterPageView({
                el: document.body
            });
        },


        /**
         * Расширяем стандартные функции YM Modules
         */
        extendModules = function( loadModule ) {
            modules.setOptions({
                loadModules: loadModule,
                findDep: function( dep ) {
                    // console.info('Module %s find in file %s', dep, modulesDep[dep]);
                    return modulesDep.hasOwnProperty(dep);
                }
            });
        };

    modules.require(['loadModule'], extendModules);

    /**
     * Запрос модулей для инициализации приложения
     */
    modules.require([
        'enter.page.view',
        'enter.modules'
    ], initApp);
}(
    this,
    this.document,
    this.modules
);