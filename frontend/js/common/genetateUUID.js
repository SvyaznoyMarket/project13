/**
 * Модуль генерации UUID
 *
 * @module      generateUUID
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'generateUUID',
        [],
        module
    );
}(
    this.modules,
    function( provide ) {
        'use strict';

        var
            generate = function() {
                var
                    uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                        var
                            r = Math.random() * 16|0,
                            v = c == 'x' ? r : (r&0x3|0x8);
                        // end of vars

                        return v.toString(16);
                    });

                return uuid;
            };

        provide(generate);
    }
);
