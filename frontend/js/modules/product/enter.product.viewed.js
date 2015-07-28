/**
 * @module      enter.product.view
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.product.viewed',
        [
            'jQuery'
        ],
        module
    );
}(
    this.modules,
    function( provide ) {
        'use strict';

        var product = JSON.parse($('.js-product-json').html()),
            viewedItems;

        function Product(product){
            this.id = product.id;
            this.name = product.name;
            this.productUrl = product.url;
            this.imageUrl = product.image120;
            return this;
        }

        if (localStorage) {
            viewedItems = JSON.parse(localStorage.getItem('enter.viewed'));
            console.log(viewedItems);
            if (!viewedItems) {
                localStorage.setItem('enter.viewed', JSON.stringify([new Product(product)]))
            } else if (!viewedItems.some(function(el){return el.id == product.id})) {
                viewedItems.unshift(new Product(product));
                localStorage.setItem('enter.viewed', JSON.stringify(viewedItems))
            }
        }

        provide({});
    }
);
