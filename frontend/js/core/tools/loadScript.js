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
        module
    );
}(
    this,
    this.document,
    this.modules,
    function( provide ) {
        'use strict';

        var
            URL_PREFIX      = '/public/js/',
            COMPLETE_FLAG   = 'finished',
            registryScripts = {},

            doCallbacks = function( url ) {
                var
                    cbs = registryScripts[url].callbacks,
                    i;

                registryScripts[url][COMPLETE_FLAG] = true;

                for ( i = 0; i < cbs.length; i++ ) {
                    cbs[i]();
                }

                return;
            },

            registerScript = function( url ) {
                var
                    script = document.createElement('script');

                script.type = 'text/javascript';

                if (script.readyState) { //IE
                    script.onreadystatechange = function () {
                        if (script.readyState == 'loaded' || script.readyState == 'complete') {
                            script.onreadystatechange = null;
                            doCallbacks(url);
                        }
                    };
                } else { //Others
                    script.onload = function () {
                        doCallbacks(url);
                    };
                }

                script.src = (( /^http:/.test(url) || /^https:/.test(url) ) ? url : URL_PREFIX + url ) + '?' +  (new Date()).getTime();
                document.getElementsByTagName('head')[0].appendChild(script);
            };

        provide(function( url, callback ) {
            if ( registryScripts.hasOwnProperty(url) && registryScripts[COMPLETE_FLAG] ) {
                callback();

                return;
            } else if ( registryScripts.hasOwnProperty(url) ) {
                registryScripts[url].callbacks.push(callback);

                return;
            }

            registryScripts[url]                = {};
            registryScripts[url].callbacks      = [];
            registryScripts[url].script         = registerScript(url);
            registryScripts[url][COMPLETE_FLAG] = false;
            registryScripts[url].callbacks.push(callback);
        });
    }
);
