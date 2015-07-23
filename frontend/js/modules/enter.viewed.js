+function($){

    var $block = $('.js-viewed-products'),
        $holder = $('.js-viewed-products-inner'),
        template = $('.js-product-viewed-template').html(),
        products;

    if (localStorage) {
        products = JSON.parse(localStorage.getItem('enter.viewed'));
    }

    if (products) {
        $block.show();
        modules.require('Mustache', function(M){
            $holder.append(M.render(template, {products: products}));
        });
        modules.require('jquery.slick', function(){
            $block.find('.js-slider-goods').slick($block.data('slick-config'));
        });
    }

}(jQuery);
