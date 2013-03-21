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
            case 'product':
                // product scripts
                require(["product"])
                break
        }
    })
})

