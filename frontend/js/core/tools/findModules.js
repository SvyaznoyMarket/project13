/**
 * @module      findModules
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( window, document, modules, module ) {
    modules.define(
        'findModules',
        [
            'jQuery'
        ],
        module
    );
}(
    this,
    this.document,
    this.modules,
    function( provide, $ ) {
        'use strict';

        var
            MODULES_SELECTOR = '.js-module-require';

        provide(function( el ) {
            var
                node     = el || $('body'),
                elements = node.find(MODULES_SELECTOR);

            elements.each(function() {
                var
                    element    = $(this),
                    moduleName = element.attr('data-module');

                if (!moduleName) return;

                (function(name, elem) {
                    modules.require(name, function(module){
                        if (typeof module.init == 'function') {
                            module.init(elem);
                        }
                    });
                })(moduleName, element);
            });
        });
    }
);