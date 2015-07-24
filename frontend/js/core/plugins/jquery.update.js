/**
 * @module      jquery.update
 * @version     0.1
 *
 * @requires    jQuery
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'jquery.update',
        [
            'jQuery'
        ],
        module
    );
}(
    this.modules,
    function( provide, $ ) {
        'use strict';

        $.fn.update = function() {
            var
                selector = this.selector,
                context  = this.context,
                newElements, i;

            newElements = $(selector, context);

            for ( i = 0; i < this.length; i++ ) {
                delete this[i];
            }

            for ( i = 0; i < newElements.length; i++ ) {
                this[i] = newElements[i];
            }

            this.length = newElements.length;

            return this;
        };

        provide($);
    }
);
