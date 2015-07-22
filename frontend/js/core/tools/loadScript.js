/**
 * @module      loadScript
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( window, document, modules, module ) {
    modules.define(
        'loadScript',
        [],
        module.bind(window, document, modules)
    );
}(
    this,
    this.document,
    this.modules,
    function( window, document, modules, provide ) {
        'use strict';

        provide(function( url, callback ) {
            var
                script = document.createElement('script');

            script.type = 'text/javascript';

            if (script.readyState) { //IE
                script.onreadystatechange = function () {
                    if (script.readyState == 'loaded' || script.readyState == 'complete') {
                        script.onreadystatechange = null;
                        callback();
                    }
                };
            } else { //Others
                script.onload = function () {
                    callback();
                };
            }

            script.src = url + '?' +  (new Date()).getTime();
            document.getElementsByTagName('head')[0].appendChild(script);
        });
    }
);
