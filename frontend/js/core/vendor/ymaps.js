/**
 * @module      ymaps
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'ymaps',
        [
            'loadScript'
        ],
        module
    );
}(
    this.modules,
    function( provide, loadScript ) {
        'use strict';

        loadScript('http://api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU&mode=release', function () {
            console.log('[Module] ymaps');

            ymaps.ready(function() {
                provide(ymaps);
            });
        });
    }
);