
require.config({
    baseUrl: "/v2/js",
    paths: {
        //'jquery': 'http://yandex.st/jquery/2.1.0/jquery',
        //'jquery'            : 'vendor/jquery-1.11.0',
        'jquery'                : 'vendor/jquery-1.8.3',
        'jquery.cookie'         : 'vendor/jquery/jquery.cookie-1.4.1',
        'jquery.ui'             : 'vendor/jquery/jquery.ui-1.10.4.custom',
        'jquery.ui.touch-punch' : 'vendor/jquery/jquery.ui.touch-punch-0.2.3',
        'jquery.popup'          : 'plugin/jquery.popup',
        'jquery.enterslide'     : 'plugin/jquery.enterslide',
        'jquery.touchwipe'      : 'plugin/jquery.touchwipe',
        'jquery.photoswipe'     : 'plugin/jquery.photoswipe',

        'underscore'         : 'vendor/underscore-1.6.0',
        'mustache'           : 'vendor/mustache-0.8.2',
        'html5'              : 'vendor/html5-3.6.2',
        'boilerplate.helper' : 'vendor/boilerplate.helper-4.1.0',

        'direct-credit' : 'http://direct-credit.ru/widget/api_script_utf'
    },

    shim: {
        'jquery': {
            exports: 'jQuery'
        },
        'jquery.ui': {
            deps: ['jquery']
        },
        'jquery.ui.touch-punch': {
            deps: ['jquery', 'jquery.ui']
        },
        'jquery.enterslide': {
            deps: ['jquery']
        },
        'jquery.popup': {
            deps: ['jquery']
        },
        'jquery.touchwipe': {
            deps: ['jquery']
        },
        'jquery.photoswipe': {
            deps: ['jquery', 'jquery.touchwipe']
        },
        /*
        'jquery.cookie': {
            deps: ['jquery']
        },
        */
        'underscore': {
            exports: '_'
        },
        'mustache': {
            exports: '_'
        },
        'html5': [],
        'boilerplate.helper': [],
        'direct-credit': []
    }
});

var moduleName = 'module/' + (document.getElementById('js-enter-module').getAttribute('content') || 'default');

require([
    'html5',
    'boilerplate.helper',
    'jquery',
    'jquery.cookie',
    'jquery.ui', 'jquery.ui.touch-punch', 'jquery.popup',
    'jquery.touchwipe',
    'module/navigation',
    'module/region',
    'module/search'
]);

require(
    [moduleName],
    function(module) {
        console.info(module);
    }
);

