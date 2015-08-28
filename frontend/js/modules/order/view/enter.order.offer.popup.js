/**
 * @module      enter.order.offer.popup
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.ui.BasePopup
 * @requires    urlHelper
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.offer.popup',
        [
            'jQuery',
            'enter.ui.BasePopup',
            'urlHelper'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BasePopup, urlHelper ) {
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
                OFFER_POPUP_CONTENT: 'js-tab-oferta-content',
                OFFER_TAB: 'js-oferta-tab',
                TAB_ACTIVE: 'orderOferta_tabs_i-cur'
            };

        provide(BasePopup.extend(/** @lends module:enter.order.offer.popup~OfferPopupView */{

             /**
             * @classdesc   Представление окна c офертой
             * @memberOf    module:enter.order.offer.popup~
             * @augments    module:enter.ui.BasePopup
             * @constructs  OfferPopupView
             */
            initialize: function( options ) {
                var
                    self = this;

                this.subViews = {
                    tabs: this.$el.find('.' + CSS_CLASSES.OFFER_TAB),
                    tabsContent: this.$el.find('.' + CSS_CLASSES.OFFER_POPUP_CONTENT),
                    offerContent: this.$el.find('.' + CSS_CLASSES.OFFER_POPUP_CONTENT).eq(0)
                };

                this.url = ( window.location.host !== 'www.enter.ru' ) ? options.url.replace(/^.*enter.ru/, '') : options.url; /* для работы на demo-серверах */

                // Setup events
                this.events['click .' + CSS_CLASSES.OFFER_TAB] = 'changeTab';

                this.ajax({
                    type: 'GET',
                    url: urlHelper.addParams(this.url, {
                        ajax: 1
                    }),
                    success: function(data) {
                        self.subViews.offerContent.html(data.content || '');
                        self.show();
                    }
                });

                // Apply events
                this.delegateEvents();
            },

            events: {},

            /**
             * Смена таба
             *
             * @method      selectAutocompleteItem
             * @memberOf    module:module:enter.order.offer.popup~OfferPopupView#
             *
             * @param       {Object}    event
             */
            changeTab: function( event ) {
                var
                    target = $(event.currentTarget),
                    tabId  = target.attr('data-tab');

                this.subViews.tabs.removeClass(CSS_CLASSES.TAB_ACTIVE);
                target.addClass(CSS_CLASSES.TAB_ACTIVE);
                this.subViews.tabsContent.hide();
                this.subViews.tabsContent.filter('#' + tabId).show();

                return false;
            }
        }));
    }
);
