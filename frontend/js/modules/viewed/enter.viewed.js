/**
 * @module      enter.viewed
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.viewed',
        [
            'jQuery'
        ],
        module
    );
}(
    this.modules,
    function( provide, $ ) {
        'use strict';

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
            modules.require('jquery.slick', function(module) {
                module.init($block);
            });
        }

        provide({});
    }
);
