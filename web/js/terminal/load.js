// main
requirejs.config({
	baseUrl: "/js/terminal/",
    paths:{
        'jquery': 'vendor/jquery-1.8.3.min',
        'ejs': 'vendor/ejs_production',
        'bigjquery': '/js/prod/jquery-plugins.min'
    },
    shim: {
        'jquery': {
            exports: '$',
        },
        'ejs': {
            exports: 'EJS',
            deps: ['jquery']
        },
        'bigjquery': {
            deps: ['jquery']
        }
    },
    urlArgs : 'bust=' + new Date().getTime()
});

var develop = false;

// for all pages
// require(["termAPI"])

require(['jquery'], function( $ ) {
    $(document).ready(function() {

        var pagetype = $('article').data('pagetype')

        switch (pagetype){
            case 'product_list':
                // product list scripts
                require(["product_list"])
                break
            case 'product_model_list':
                // product line scripts
                require(["product_list"])
                break
            case 'product':
                // product scripts
                require(["product"])
                break
            case 'filter':
                // catalog filter scripts
                require(["filter"])
                break
        }
    })
})