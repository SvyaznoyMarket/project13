/**
 * @module      enter.product.view
 * @version     0.1
 *
 * @requires    App
 * @requires    Backbone
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    enter.kit.popup.view
 * @requires    enter.application.popup
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.product.view',
        [
            'App',
            'Backbone',
            'jQuery',
            'enter.BaseViewClass',
            'enter.kit.popup.view',
            'enter.application.popup'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, Backbone, $, BaseViewClass, KitPopupView, ApplicationPopupView ) {
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
                FAVORITE_BUTTON: 'js-favorite-button',
                COMPARE_ACTIVE: 'active',
                FAVORITE_ACTIVE: 'active',
                KIT_POPUP: 'js-productkit-popup',
                SHOW_KIT_BTN: 'js-buy-kit-button',
                APPLICATION_BTN: 'js-buy-slot-button',
                APPLICATION_POPUP: 'js-popup-application'
            };

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление карточки товара
             * @memberOf    module:enter.product.view~
             * @augments    module:BaseViewClass
             * @constructs  ProductView
             */
            initialize: function( options ) {
                this.listenTo(this.model, 'change:inCart', this.changeCartStatus);
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
                this.events['click .' + CSS_CLASSES.APPLICATION_BTN] = 'showApplicationPopup';

                // Apply events
                this.delegateEvents();

                this.changeCompareStatus();
                this.changeFavoriteStatus();
            },

            events: {},

            /**
             * Хандлер изменения статуса `в корзине` у товара. Срабатывает каждый раз при добавлении\удалении\инициализации корзины
             *
             * @method      compareChange
             * @memberOf    module:enter.userbar.view~EnterUserbarView#
             *
             * @listens    module:enter.product.model~ProductModel#change:inFavorite
             */
            changeCartStatus: function() {
                // console.info('Product %s in cart', this.model.get('id'));
            },

            /**
             * Хандлер изменения статуса `в сравнении` у товара. Срабатывает каждый раз при добавлении\удалении\инициализации сравнения
             *
             * @method      compareChange
             * @memberOf    module:enter.userbar.view~EnterUserbarView#
             *
             * @listens    module:enter.product.model~ProductModel#change:inCompare
             */
            changeCompareStatus: function() {
                var
                    inCompare = this.model.get('inCompare');

                if ( inCompare ) {
                    this.subViews.compareBtn.addClass(CSS_CLASSES.COMPARE_ACTIVE);
                } else {
                    this.subViews.compareBtn.removeClass(CSS_CLASSES.COMPARE_ACTIVE);
                }
            },

            /**
             * Показ окна с набором
             *
             * @method      showKitPopup
             * @memberOf    module:enter.userbar.view~EnterUserbarView#
             */
            showKitPopup: function() {
                if ( this.subViews.kitPopup ) {
                    this.subViews.kitPopup.show();
                    return false;
                }

                this.subViews.kitPopup = new KitPopupView({
                    el: Backbone.$('.' + CSS_CLASSES.KIT_POPUP),
                    model: this.model
                });

                this.subViews.kitPopup.show();

                return false;
            },

            /**
             * Показ окна оставлением заявки на покупку.
             * [Пример карточки товара]{@link /product/furniture/kuhonniy-garnitur-dekor-220-h-130-sm-cherniy-metallik-krasniy-metallik-sahara-2050301025231}
             *
             * @method      showApplicationPopup
             * @memberOf    module:enter.userbar.view~EnterUserbarView#
             */
            showApplicationPopup: function() {
                console.info('module:enter.userbar.view~EnterUserbarView#showApplicationPopup');

                if ( this.subViews.applicationPopup ) {
                    this.subViews.applicationPopup.show();
                    return false;
                }

                this.subViews.applicationPopup = new ApplicationPopupView({
                    model: this.model,
                    el: $('.' + CSS_CLASSES.APPLICATION_POPUP)
                });

                this.subViews.applicationPopup.show();

                return false;
            },

            /**
             * Хандлер изменения статуса `в избранном` у товара. Срабатывает каждый раз при добавлении\удалении\инициализации избранного
             *
             * @method      compareChange
             * @memberOf    module:enter.userbar.view~EnterUserbarView#
             *
             * @listens    module:enter.product.model~ProductModel#change:inFavorite
             */
            changeFavoriteStatus: function() {
                var
                    inFavorite = this.model.get('inFavorite');

                if ( inFavorite ) {
                    this.subViews.favoriteBtn.addClass(CSS_CLASSES.FAVORITE_ACTIVE);
                } else {
                    this.subViews.favoriteBtn.removeClass(CSS_CLASSES.FAVORITE_ACTIVE);
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
            },

            favoriteButtonHandler: function() {
                var
                    inFavorite = this.model.get('inFavorite');

                if ( inFavorite ) {
                    App.favorite.remove(this.model);
                } else {
                    App.favorite.add(this.model);
                }

                return false;
            }

        }));
    }
);