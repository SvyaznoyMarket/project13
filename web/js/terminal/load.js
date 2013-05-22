// main
requirejs.config({
	baseUrl: "/js/terminal/",
    paths:{
        'jquery': '/js/jquery-1.6.4.min',
        'ejs': '/js/ejs_production'
    },
    shim: {
        'jquery': {
            exports: '$',
        },
        "ejs": {
            exports: 'EJS',
            deps: ['jquery']
        }
    },
    urlArgs : "bust="+new Date().getTime()
});

var develop = true

// for all pages
require(["termAPI"])

require(["jquery"], function($) {
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
        }
    })
})