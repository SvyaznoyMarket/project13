/**
 * @module      enter.product
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    App
 * @requires    enter.product.view
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.product',
        [
            'jQuery',
            'App',
            'enter.product.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, App, ProductView ) {
        'use strict';

        provide({
            init: function( el ) {
                var
                    $el         = $(el),
                    productData = $el.data('product'),
                    inited      = $el.prop('inited');

                if ( inited ) {
                    // console.warn('--- element %s initialized! ---', $el);
                    return;
                }

                $el.prop('inited', true);

                App.productsCollection.add(productData, { merge: true });

                new ProductView({
                    el: $el,
                    model: App.productsCollection.get(productData.id)
                });
            }
        });
    }
);