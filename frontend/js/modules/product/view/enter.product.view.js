/**
 * @module      enter.product.view
 * @version     0.1
 *
 * @requires    enter.BaseViewClass
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.product.view',
        [
            'App',
            'Backbone',
            'enter.BaseViewClass'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, Backbone, BaseViewClass ) {
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
                BUY_BUTTON: 'js-buy-button'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление страницы сайта
             * @memberOf    module:enter.product.view~
             * @augments    module:BaseViewClass
             * @constructs  ProductView
             */
            initialize: function( options ) {
                console.info('enter.page.view~ProductView#initialize', this.model.get('id'));

                this.listenTo(this.model, 'change:inCart', this.changeCartStatus);
                this.listenTo(this.model, 'change:inCompare', this.changeCompareStatus);

                 // Setup events
                this.events['click .' + CSS_CLASSES.BUY_BUTTON] = 'buyButtonHandler';

                // Apply events
                this.delegateEvents();
            },

            events: {},

            changeCartStatus: function() {
                console.info('Product %s in cart', this.model.get('id'));
            },

            changeCompareStatus: function() {
                console.info('Product %s in compare list', this.model.get('id'));
            },

            buyButtonHandler: function( event ) {
                var
                    target     = Backbone.$(event.currentTarget),
                    url        = target.attr('href');

                this.model.set({'addUrl': url, 'inCart': true});
                App.cart.add(this.model);

                return false;
            }
        }));
    }
);
