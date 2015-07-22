/**
 * @param    {Object}    window     Ссылка на window
 * @param    {Object}    document   Ссылка на document
 * @param    {Object}    modules    Ссылка на модульную систему YModules
 */
(function ( window, document, modules ) {
    'use strict';

    var
        module = function ( provide, $ ) {
            $.fn.replaceWithPush = function( a ) {
                this.replaceWith(a);
                this.update();
                return this;
            };

            provide($);
        };
    // end of vars


    /**
     * Модуль jQuery плагина для замещения DOM элемента и возврата нового элемента
     *
     * @module      replaceWithPush
     * @version     0.1
     *
     * @requires    jQuery
     */
    modules.define(
        'jquery.replaceWithPush',
        [
            'jQuery',
            'jquery.update'
        ],
        module
    );
}(
    this,
    this.document,
    this.modules
));
