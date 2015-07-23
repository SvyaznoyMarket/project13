/**
 * Модуль инициализации каталога. Создает экземпляр класса CatalogView
 *
 * @module      enter.catalog
 * @version     0.1
 *
 * @requires    enter.catalog.view
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.catalog',
        [
            'enter.catalog.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, CatalogView ) {
        'use strict';

        provide({
            init: function( el ) {
                var
                    catalog = new CatalogView({
                        el: el
                    });
            }
        });
    }
);
