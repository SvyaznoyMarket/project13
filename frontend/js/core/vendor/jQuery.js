/**
 * @module      jQuery
 * @version     0.1
 *
 * @author      Zaytsev Alexandr
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'jQuery',
        [
            'loadScript'
        ],
        module
    );
}(
    this.modules,
    function( provide, loadScript ) {
        'use strict';

        loadScript('https://yastatic.net/jquery/2.1.4/jquery.min.js', function () {
            console.log('[Module] jQuery');
            provide($);
        });
    }
);
