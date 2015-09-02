/**
 * @module      enter.page.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    enter.cart.view
 * @requires    enter.userbar.view
 * @requires    enter.feedback.popup.view
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
            'enter.feedback.popup.view',
            'jquery.visible',
            'jquery.scrollTo',
            'findModules'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, CartView, UserbarView, FeedbackPopupView, jVisible, jqueryScrollTo, findModules ) {
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
                CART: 'js-cart',
                FEEDBACK_POPUP: 'js-feedback-popup',
                FEEDBACK_BTN: 'js-feedback-from-btn',
                CHANGE_REGION_LNK: 'js-change-region',
                GO_TO_LNK: 'js-go-to',
                LOADER: 'loader-fixed'
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
                    feedbackPopup = this.$el.find('.' + CSS_CLASSES.FEEDBACK_POPUP),
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

                this.subViews.feedbackPopup = new FeedbackPopupView({
                    el: feedbackPopup
                });

                // Setup events
                this.events['click .' + CSS_CLASSES.LOGIN_POPUP_BTN]   = 'showLoginPopup';
                this.events['click .' + CSS_CLASSES.FEEDBACK_BTN]      = 'showFeedbackPopup';
                this.events['click .' + CSS_CLASSES.CHANGE_REGION_LNK] = 'showRegionPopup';
                this.events['click .' + CSS_CLASSES.GO_TO_LNK]         = 'goToTarget';

                window.onbeforeunload = function() {
                    self.$el.addClass(CSS_CLASSES.LOADER);
                    console.warn('window.onbeforeunload');
                };

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.page.view~EnterPageView
             * @type        {Object}
             */
            events: {
                'DOMNodeInserted': 'change'
            },

            /**
             * Показ окна с формой обратной связи
             *
             * @method      showFeedbackPopup
             * @memberOf    module:enter.page.view~EnterPageView#
             */
            showFeedbackPopup: function() {
                this.subViews.feedbackPopup.show();

                return false;
            },

            goToTarget: function(e) {
                this.$el.scrollTo(
                    $($(e.target).attr('href')), // target
                    300, // duration
                    {
                        offset: this.subViews.userbar_0 ? - this.subViews.userbar_0.staticHeight - 20 : 0
                    });
                return false;
            },

            /**
             * Показ окна с выбором региона
             *
             * @method      showRegionPopup
             * @memberOf    module:enter.page.view~EnterPageView#
             */
            showRegionPopup: function() {
                modules.require('enter.region', function(module){
                    if (typeof module.show == 'function') {
                        module.show()
                    }
                });
                return false;
            },

            /**
             * Показ окна с формой авторизации
             *
             * @method      showLoginPopup
             * @memberOf    module:enter.page.view~EnterPageView#
             */
            showLoginPopup: function() {
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
             *
             * @memberOf    module:enter.page.view~EnterPageView
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
