/**
 * @module      loadModule
 * @version     0.1
 *
 * @requires    loadModule
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'loadModule',
        [
            'loadScript'
        ],
        module
    );
}(
    this.modules,
    function( provide, loadScript ) {
        'use strict';

        var
            modulesDep = JSON.parse(document.getElementById('js-modules-definitions').innerHTML),

            /**
             * Получение имени файла. Возвращает имя с *.min.js и версией.
             *
             * @todo    сделать определение debug режима
             *
             * @method      getWithVersion
             * @memberOf    module:loadModule#
             * @private
             *
             * @param   {String}    filename
             * @return  {String}
             */
            getWithVersion = function( filename ) {
                return filename;
            },

            /**
             * Загрузка и вызов модуля
             *
             * @alias       module:loadModule
             * @public
             *
             * @param       {Array}     modulesNames    Массив имен модулей, которые необходимо загрузить
             * @param       {Function}  callback        Функция обратно вызова
             */
            loader = function( modulesNames, callback ) {
                var
                    loadedCnt = 0,
                    filename,
                    i;

                for ( i = 0; i < modulesNames.length; i++ ) {
                    !function( f, cb ) {
                        loadScript(getWithVersion(f), function() {
                            loadedCnt++;

                            if ( loadedCnt === modulesNames.length ) {
                                cb();
                            }
                        });
                    }(
                        modulesDep[modulesNames[i]],
                        callback
                    );
                }
            };

        provide(loader);
    }
);