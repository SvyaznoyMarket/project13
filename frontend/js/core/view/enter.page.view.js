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
            'enter.BaseViewClass',
            'enter.cart.view'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, CartView ) {
        'use strict';

        var
            /**
             * Используемые CSS классы
             *
             * @private
             * @constant
             * @type        {Object}
             */
            CSS_CLASSES = {
                CART: 'js-cart'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление страницы сайта
             * @memberOf    module:enter.page.view~
             * @augments    module:BaseViewClass
             * @constructs  EnterPageView
             */
            initialize: function( options ) {
                console.info('enter.page.view~EnterPageView#initialize');
                var
                    carts = this.$el.find('.' + CSS_CLASSES.CART),
                    self  = this;

                carts.each(function( index ) {
                    self.subViews['cart_' + index] = new CartView({
                        el: $(this)
                    });
                });
            },

            events: {}
        }));
    }
);
