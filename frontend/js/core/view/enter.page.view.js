/**
 * @module      enter.page.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    enter.cart.view
 * @requires    enter.userbar.view
 * @requires    jquery.visible
 * @requires    findModules
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.page.view',
        [
            'jQuery',
            'enter.BaseViewClass',
            'enter.cart.view',
            'enter.userbar.view',
            'jquery.visible',
            'findModules'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, CartView, UserbarView, jVisible, findModules ) {
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
                LOGIN_POPUP_BTN: 'js-userbar-user-link',
                LOGIN_POPUP: 'js-popup-login',
                USERBAR_TARGET: 'js-show-fixed-userbar',
                USERBAR: 'js-userbar',
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
                    carts         = this.$el.find('.' + CSS_CLASSES.CART),
                    userbars      = this.$el.find('.' + CSS_CLASSES.USERBAR),
                    userbarTarget = this.$el.find('.' + CSS_CLASSES.USERBAR_TARGET),
                    self          = this;

                findModules(this.$el);

                this.subViews = {
                    loginPopup: false
                };

                carts.each(function( index ) {
                    self.subViews['cart_' + index] = new CartView({
                        el: $(this)
                    });
                });

                userbars.each(function( index ) {
                    self.subViews['userbar_' + index] = new UserbarView({
                        el: $(this),
                        target: userbarTarget
                    });
                });

                // Setup events
                this.events['click .' + CSS_CLASSES.LOGIN_POPUP_BTN] = 'showLoginPopup';

                // Apply events
                this.delegateEvents();
            },

            events: {
                'DOMNodeInserted': 'change'
            },

            showLoginPopup: function() {
                console.warn('showLoginPopup');

                var
                    self = this;

                if ( this.subViews.loginPopup ) {
                    this.subViews.loginPopup.show();
                    return false;
                }

                modules.require('enter.auth', function( AuthPopupView) {
                    self.subViews.loginPopup = new AuthPopupView({
                        el: self.$el.find('.' + CSS_CLASSES.LOGIN_POPUP)
                    });

                    self.subViews.loginPopup.show();
                });

                return false;
            },

            /**
             * Хандлер изменения DOM. Паттерн debounce
             */
            change: (function () {
                var
                    timeWindow = 500, // time in ms
                    timeout,

                    change = function ( args ) {
                        console.warn('!!! DOM CHANGED !!!');
                        findModules(this.$el);
                    };

                return function() {
                    var
                        context = this,
                        args = arguments;

                    clearTimeout(timeout);

                    timeout = setTimeout(function(){
                        change.apply(context, args);
                    }, timeWindow);
                };
            }())
        }));
    }
);