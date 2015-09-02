/**
 * @module      nter.order.step2
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.order.step2.view
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.step2',
        [
            'jQuery',
            'enter.order.step2.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, OrderStep2View ) {
        'use strict';

        provide({
            init: function( el ) {
                var
                    $el         = $(el),
                    inited      = $el.prop('inited');

                if ( inited ) {
                    // console.warn('--- element %s initialized! ---', $el);
                    return;
                }

                $el.prop('inited', true);

                new OrderStep2View({
                    el: $el
                });
            }
        });
    }
);
