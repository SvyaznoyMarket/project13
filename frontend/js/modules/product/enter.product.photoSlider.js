/**
 * @module      enter.product.photoSlider
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.product.photoSlider.view
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.product.photoSlider',
        [
            'jQuery',
            'enter.product.photoSlider.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, ProductPhotoSlider ) {
        'use strict';

        provide({
            init: function( el ) {
                var
                    $el    = $(el),
                    inited = $el.prop('inited');

                if ( inited ) {
                    // console.warn('--- element %s initialized! ---', $el);
                    return;
                }

                $el.prop('inited', true);

                new ProductPhotoSlider({
                    el: $el,
                });
            }
        });
    }
);
