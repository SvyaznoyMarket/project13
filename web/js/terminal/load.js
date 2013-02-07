// main
requirejs.config({
	baseUrl: "/js/terminal/",
    paths:{
        'jquery': '/js/jquery-1.6.4.min',
        'jquery-ui': '/js/jquery-ui-1.10.0.custom.min'
    },
    shim: {
        'jquery': {
            exports: '$',
        },
        "jquery-ui": {
            exports: "$",
            deps: ['jquery']
        },
    }
});

require(["product"], function($) {

})