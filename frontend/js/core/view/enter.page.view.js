/**
 * @module      enter.page.view
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.page.view',
        [
            'jQuery',
            'enter.BaseViewClass'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass ) {
        'use strict';

        var
            /**
             * Используемые CSS классы
             *
             * @private
             * @constant
             * @type        {Object}
             */
            CSS_CLASSES = {};

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление страницы сайта
             * @memberOf    module:enter.page.view~
             * @augments    module:BaseViewClass
             * @constructs  EnterPageView
             */
            initialize: function( options ) {
                console.info('enter.page.view~EnterPageView#initialize');
            },

            events: {}
        }));
    }
);
