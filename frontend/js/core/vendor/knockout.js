/**
 * @module      ko
 * @version     0.1
 *
 * @author      Zaytsev Alexandr
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'ko',
        [
            'loadScript'
        ],
        module
    );
}(
    this.modules,
    function( provide, loadScript ) {
        'use strict';

        loadScript('https://cdnjs.cloudflare.com/ajax/libs/knockout/3.3.0/knockout-min.js', function () {
            console.log('[Module] knockout');
            provide(ko);
        });
    }
);
