({
    baseUrl: '.',
    dir: './build',
    paths: {
        'jquery': 'empty:',

        'jquery.cookie'         : 'vendor/jquery/jquery.cookie-1.4.1',
        'jquery.ui'             : 'vendor/jquery/jquery.ui-1.10.4.custom',
        'jquery.ui.touch-punch' : 'vendor/jquery/jquery.ui.touch-punch-0.2.3',
        'jquery.popup'          : 'plugin/jquery.popup',
        'jquery.enterslide'     : 'plugin/jquery.enterslide',
        'jquery.touchwipe'      : 'plugin/jquery.touchwipe',
        'jquery.photoswipe'     : 'plugin/jquery.photoswipe',
        'jquery.slides'         : 'plugin/jquery.slides',

        'underscore'         : 'empty:',
        'mustache'           : 'vendor/mustache-0.8.2',
        'html5'              : 'vendor/html5-3.6.2',
        'boilerplate.helper' : 'vendor/boilerplate.helper-4.1.0',

        'browserstate.history'         : 'vendor/browserstate.history-1.8b2',
        'browserstate.history.adapter' : 'vendor/browserstate.history.adapter.jquery-1.8b2',

        'direct-credit' : 'empty:'
    },
    fileExclusionRegExp: /build.js|main.js/,
    optimize: 'uglify2'
})