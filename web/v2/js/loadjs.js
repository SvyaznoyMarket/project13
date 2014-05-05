require.config({
    baseUrl: "/v2/js",
    paths: {
        "jquery"   : "vendor/jquery-1.8.3.min",
        "html5" : 'vendor/html5-3.6.2',
        "mustache" : 'vendor/mustache-0.8.2',
        
        "navopen"  : "nav",

        "touchswipe": "touch/jquery.touchwipe.min",
        "productswipe"  : "touch/products.swipe",
    },

    shim: {
        'mustache': {
            exports: 'Mustache'
        }
    }
}); 

requirejs([
    'jquery', 
    'html5',
    'mustache',
    'navopen',
    'touchswipe',
    'productswipe'], function($, Mustache) {
    	console.log('requireJS');
    }
);