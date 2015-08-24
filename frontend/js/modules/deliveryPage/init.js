/**
 * @module      deliveryPage
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    deliveryPageView
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'deliveryPage',
        [
            'jQuery',
            'deliveryPageView'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, DeliveryPageView ) {
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

                new DeliveryPageView({
                    el: $el
                });
            }
        });
    }
);
