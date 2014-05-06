
    requirejs.config({
        baseUrl: "/v2/js",
        paths: {
            'jquery'           : 'vendor/jquery-1.8.3',
            'jquery.cookie'    : 'vendor/jquery/jquery.cookie-1.4.0',
            'jquery.touchwipe' : 'vendor/jquery/jquery.touchwipe-1.6.5',

            'underscore'    : 'vendor/underscore-1.6.0',
            'html5'         : 'vendor/html5-3.6.2',
            'mustache'      : 'vendor/mustache-0.8.2'
        },

        shim: {
            'mustache': {
                exports: 'mustache'
            },
            'jquery': {
                exports: '$'
            },
            'jquery.cookie': {
                deps: ['jquery']
            },
            'jquery.touchwipe': {
                deps: ['jquery'],
                exports: '$.fn.touchwipe'
            }
        }
    });

    require(
        ['jquery', 'jquery.touchwipe'],
        function($) {
            console.log(typeof $.touchwipe);
        }
    );

    //var moduleName = 'module/' + (document.getElementById('js-enter-module').getAttribute('content') || 'default');

    /*
    require(
        ['jquery', moduleName],
        function($, module) {
            console.info(module);
        }
    );
    */
