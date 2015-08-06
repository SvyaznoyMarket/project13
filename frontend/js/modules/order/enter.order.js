/**
 * @module      enter.order
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    App
 * @requires    enter.order.view
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order',
        [
            'jQuery',
            'App',
            'enter.order.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, App, OrderView ) {
        'use strict';

        provide({
            init: function( el ) {
                new OrderView({
                    el: $(el)
                });
            }
        });
    }
);
