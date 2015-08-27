/**
 * @module      enter.order.step2.view
 * @version     0.1
 *
 * @requires    jQuery
 * @requires    enter.BaseViewClass
 * @requires    enter.suborder.view
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.order.step2.view',
        [
            'jQuery',
            'enter.BaseViewClass',
            'enter.suborder.view',
            'urlHelper'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, SubOrderView, urlHelper ) {
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
                SUB_ORDER: 'js-order-block',
                COMMENT_BTN: 'js-order-comment',
                COMMENT_AREA: 'js-order-comment-text',
                SUBMIT_ORDER: 'js-order-submit',
                ACCEPT_CHECKBOX: 'jsAcceptAgreement',
                LOADER: 'loader-elem',
                ACTIVE: 'active',
                SMART_ADRRESS: 'jsSmartAddressBlock',
                OFFER_POPUP: 'js-order-oferta-popup',
                OFFER_POPUP_CONTENT: 'js-tab-oferta-content',
                OFFER_POPUP_BTN: 'js-order-oferta-popup-bt'
            };

        provide(BaseViewClass.extend({
            url: '/order/delivery',

            timeToAjaxLoader: 0,

            /**
             * @classdesc   Представление второго шага оформления заказа
             * @memberOf    module:enter.order.step2.view~
             * @augments    module:enter.BaseViewClass
             * @constructs  OrderStep2View
             */
            initialize: function( options ) {
                console.info('module:enter.order.step2.view~OrderStep2View#initialize');
                var
                    self        = this,
                    suborders   = this.$el.find('.' + CSS_CLASSES.SUB_ORDER),
                    smartAdress = this.$el.find('.' + CSS_CLASSES.SMART_ADRRESS);

                console.info(this.$el);

                this.subViews = {
                    commentBtn: this.$el.find('.' + CSS_CLASSES.COMMENT_BTN),
                    commentArea: this.$el.find('.' + CSS_CLASSES.COMMENT_AREA),
                    acceptCheckbox: this.$el.find('.' + CSS_CLASSES.ACCEPT_CHECKBOX),
                    offerPopup: $('.' + CSS_CLASSES.OFFER_POPUP),
                    offerPopupContent: $('.' + CSS_CLASSES.OFFER_POPUP_CONTENT)
                };

                suborders.each(function( index ) {
                    self.subViews['suborder_' + index] = new SubOrderView({
                        el: $(this),
                        orderView: self
                    });
                });

                if ( smartAdress.length ) {
                    modules.require('enter.order.smartadress.view', function( OrderSmartAdress ) {
                        self.subViews.smartAdress = new OrderSmartAdress({
                            el: smartAdress,
                            orderView: self
                        });
                    });
                }

                // Setup events
                this.events['click .' + CSS_CLASSES.COMMENT_BTN]     = 'toggleCommentArea';
                this.events['click .' + CSS_CLASSES.SUBMIT_ORDER]    = 'submitOrder';
                this.events['click .' + CSS_CLASSES.OFFER_POPUP_BTN] = 'showOfferPopup';

                this.listenTo(this, 'sendChanges', this.sendChanges);

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.order.step2.view~OrderStep2View
             * @type        {Object}
             */
            events: {},

            /**
             * Объект загрузчика. Передается в опциях в AJAX вызовы.
             * Свойство loading изменяет ajax автоматически.
             *
             * @memberOf    module:enter.order.step2.view~OrderStep2View
             * @type        {Object}
             */
            loader: {
                loading: false,

                show: function() {
                    console.info('module:enter.order.step2.view~OrderStep2View#show', this.$el, this.$el.length);
                    this.$el.addClass(CSS_CLASSES.LOADER);
                },

                hide: function() {
                    console.info('module:enter.order.step2.view~OrderStep2View#hide', this.$el, this.$el.length);
                    this.$el.removeClass(CSS_CLASSES.LOADER);
                }
            },

            /**
             * Скрытие\раскрытие блока дополнительных пожеланий
             *
             * @method      toggleCommentArea
             * @memberOf    module:enter.order.step2.view~OrderStep2View#
             */
            toggleCommentArea: function() {
                if ( this.subViews.commentArea.is(':visible') ) {
                    this.subViews.commentBtn.removeClass(CSS_CLASSES.ACTIVE);
                    this.subViews.commentArea.hide();
                } else {
                    this.subViews.commentArea.show();
                    this.subViews.commentBtn.addClass(CSS_CLASSES.ACTIVE);
                }
            },

            /**
             * Отправка изменений на сервер
             *
             * @method      sendChanges
             * @memberOf    module:enter.order.step2.view~OrderStep2View#
             *
             * @todo        написать обработчик
             */
            sendChanges: function( event ) {
                var
                    action = event.action,
                    data   = event.data;

                console.groupCollapsed('module:enter.order.step2.view~OrderStep2View#sendChanges');
                console.dir(event);
                console.groupEnd();

                this.ajax({
                    type: 'POST',
                    data: {
                        action: action,
                        params: data
                    },
                    url: this.url,
                    loader: this.loader,
                    success: this.render.bind(this),
                    error: function( jqXHR, textStatus, errorThrown ) {
                        console.groupCollapsed('module:enter.order.step2.view~OrderStep2View#sendChanges: error');
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                        console.groupEnd();
                    }
                });
            },

            /**
             * Обработчик отправки заказа
             *
             * @method      submitOrder
             * @memberOf    module:enter.order.step2.view~OrderStep2View#
             *
             * @todo        Дописать обработчик. Валидировать подзаказы
             */
            submitOrder: function() {
                if ( !this.subViews.acceptCheckbox.is(':checked') ) {
                    // mark error
                    return false;
                }
            },

            showOfferPopup: function( event ) {
                var
                    target   = $(event.currentTarget),
                    termsUrl = target.attr('data-value'),
                    self     = this;

                if ( termsUrl === '' ) {
                    return false;
                }

                console.log('OLD termsUrl', termsUrl);
                termsUrl = ( window.location.host !== 'www.enter.ru' ) ? termsUrl.replace(/^.*enter.ru/, '') : termsUrl; /* для работы на demo-серверах */
                console.log('NEW termsUrl', termsUrl);

                this.ajax({
                    url: urlHelper.addParams(termsUrl, {
                        ajax: 1
                    }),
                    success: function(data) {
                        modules.require('jquery.lightbox_me', function() {
                            console.log(self.subViews.offerPopupContent);
                            self.subViews.offerPopupContent.html(data.content || '');
                            self.subViews.offerPopup.lightbox_me({});
                        });
                    }
                });

                return false;
            },


            /**
             * Отрисовка оформления заказа
             *
             * @method      render
             * @memberOf    module:enter.order.step2.view~OrderStep2View#
             */
            render: function( data ) {
                var
                    html = data.result.page,
                    subView;

                console.info('module:enter.order.step2.view~OrderStep2View#render');

                // Destroy all subviews
                for ( subView in this.subViews ) {
                    if ( this.subViews.hasOwnProperty(subView) ) {
                        if ( typeof this.subViews[subView].off === 'function' ) {
                            this.subViews[subView].off();
                        }

                        if ( typeof this.subViews[subView].destroy === 'function' ) {
                            this.subViews[subView].destroy();
                        } else if ( typeof this.subViews[subView].remove === 'function' ) {
                            this.subViews[subView].remove();
                        }

                        delete this.subViews[subView];
                    }
                }

                delete this.subViews;

                // COMPLETELY UNBIND THE VIEW
                this.undelegateEvents();
                this.stopListening();

                this.$el.removeData().unbind();
                this.$el.empty();

                this.$el.prepend(html);

                this.initialize();
            }
        }));
    }
);
