/**
 * @module      enter.order.view
 * @version     0.1
 *
 * @requires    enter.BaseViewClass
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.view',
        [
            'App',
            'Backbone',
            'enter.BaseViewClass',
            'jquery.maskedinput'
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
                valid: 'valid',
                error: 'error',
                phoneField: 'js-order-phone',
                emailField: 'js-order-email',
                nameField: 'js-order-name'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление оформления заказа
             * @memberOf    module:enter.order.view~
             * @augments    module:BaseViewClass
             * @constructs  OrderView
             */
            initialize: function( options ) {
                for (var i in CSS_CLASSES) {
                    var $elem = $('.'+CSS_CLASSES[i]);
                    if ($elem.data('mask')) $elem.mask($elem.data('mask'));
                }
                /*this.listenTo(this.model, 'change:inCart', this.changeCartStatus);
                this.listenTo(this.model, 'change:inCompare', this.changeCompareStatus);
                this.listenTo(this.model, 'change:inFavorite', this.changeFavoriteStatus);

                this.subViews = {
                    compareBtn: this.$el.find('.' + CSS_CLASSES.COMPARE_BUTTON),
                    favoriteBtn: this.$el.find('.' + CSS_CLASSES.FAVORITE_BUTTON)
                };

                // Setup events
                this.events['click .' + CSS_CLASSES.BUY_BUTTON]      = 'buyButtonHandler';
                this.events['click .' + CSS_CLASSES.COMPARE_BUTTON]  = 'compareButtonHandler';
                this.events['click .' + CSS_CLASSES.FAVORITE_BUTTON] = 'favoriteButtonHandler';
                this.events['click .' + CSS_CLASSES.SHOW_KIT_BTN]    = 'showKitPopup';

                // Apply events
                this.delegateEvents();

                this.changeCompareStatus();
                this.changeFavoriteStatus();*/
            },

            events: {}

        }));
    }
);
