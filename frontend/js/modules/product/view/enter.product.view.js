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
                BUY_BUTTON: 'js-buy-button',
                COMPARE_BUTTON: 'js-compare-button',
                COMPARE_ACTIVE: 'active'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление страницы сайта
             * @memberOf    module:enter.product.view~
             * @augments    module:BaseViewClass
             * @constructs  ProductView
             */
            initialize: function( options ) {
                this.listenTo(this.model, 'change:inCart', this.changeCartStatus);
                this.listenTo(this.model, 'change:inCompare', this.changeCompareStatus);

                this.subViews = {
                    compareBtn: this.$el.find('.' + CSS_CLASSES.COMPARE_BUTTON)
                };

                 // Setup events
                this.events['click .' + CSS_CLASSES.BUY_BUTTON]     = 'buyButtonHandler';
                this.events['click .' + CSS_CLASSES.COMPARE_BUTTON] = 'compareButtonHandler';

                // Apply events
                this.delegateEvents();

                this.changeCompareStatus();
            },

            events: {},

            changeCartStatus: function() {
                // console.info('Product %s in cart', this.model.get('id'));
            },

            changeCompareStatus: function() {
                var
                    inCompare = this.model.get('inCompare');

                if ( inCompare ) {
                    this.subViews.compareBtn.addClass(CSS_CLASSES.COMPARE_ACTIVE);
                } else {
                    this.subViews.compareBtn.removeClass(CSS_CLASSES.COMPARE_ACTIVE);
                }
            },

            buyButtonHandler: function( event ) {
                var
                    target   = Backbone.$(event.currentTarget),
                    url      = target.attr('href'),
                    inCart   = this.model.get('inCart'),
                    quantity = this.model.get('quantity');

                if ( inCart ) {
                    this.model.set({'quantity': quantity++});
                }

                this.model.set({'addUrl': url, 'inCart': true});
                // App.cart.add(this.model, {merge: true});
                App.cart.trigger('add', this.model);

                return false;
            },

            compareButtonHandler: function () {
                var
                    inCompare = this.model.get('inCompare');

                if ( inCompare ) {
                    App.compare.remove(this.model);
                } else {
                    App.compare.add(this.model);
                }

                return false;
            }

        }));
    }
);
