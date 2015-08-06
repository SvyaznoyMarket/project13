/**
 * @module      printPrice
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'printPrice',
        [],
        module
    );
}(
    this.modules,
    function( provide ) {
        'use strict';

        provide(function( num ) {
            var
                str = num.toString();

            return str.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
        });
    }
);
