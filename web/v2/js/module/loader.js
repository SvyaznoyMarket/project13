(function(window, document, modules) {
    'use strict';

    var
        provideModule = function(provide, $LAB) {
            var
                createScript = function() {
                    document.write = function() {
                        if( arguments[0].match(/<script(.?)* type=(\'|")text\/javascript(\'|")(.?)*><\/script>/)) {
                            $LAB.script(arguments[0].match( /src=(\'|")([^"\']?)+/ )[0].replace(/src=(\'|")/,''));
                        } else {
                            document.writeln(arguments[0]);
                        }
                    };
                },

                loadFile = function(filename) {
                    var
                        debug = true,
                        version = new Date().getTime();
                    // end of vars

                    if (!debug) {
                        filename = filename.replace('js', 'min.js');
                    }

                    return filename += '?t=' + version;
                },

                module = function (moduleName, callback) {
                    var
                        filename = '/v2/js/module/' + moduleName + '.js';
                    // end of vars

                    console.log('loader', 'loading', filename);

                    $LAB.script(loadFile(filename)).wait(callback);
                };
            // end of vars

            $LAB.setGlobalDefaults({
                AllowDuplicates: true,
                AlwaysPreserveOrder: true,
                UseLocalXHR: false
            }).queueWait(createScript);

            provide(module);
        };
    // end of vars

    modules.define(
        'loader',           // Module name
        ['LAB'],            // Dependies
        provideModule       // Module realization
    );
}(
    this,
    this.document,
    this.modules
));