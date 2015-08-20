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
            'jquery.replaceWithPush',
            'FormValidator'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass, SubOrderView, jReplaceWithPush, FormValidator ) {
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
                CHANGE_REGION_BTN: 'js-change-region',
                COMMENT_BTN: 'js-order-comment',
                COMMENT_AREA: 'js-order-comment-text',
                SUBMIT_ORDER: 'js-order-submit',
                ACCEPT_CHECKBOX: 'jsAcceptAgreement',
                LOADER: 'loader-elem',
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
                    self      = this,
                    suborders = this.$el.find('.' + CSS_CLASSES.SUB_ORDER);

                console.info(this.$el);

                this.subViews = {
                    commentArea: this.$el.find('.' + CSS_CLASSES.COMMENT_AREA),
                    acceptCheckbox: this.$el.find('.' + CSS_CLASSES.ACCEPT_CHECKBOX)
                };

                suborders.each(function( index ) {
                    self.subViews['suborder_' + index] = new SubOrderView({
                        el: $(this),
                        orderView: self
                    });
                });

                // Setup events
                this.events['click .' + CSS_CLASSES.CHANGE_REGION_BTN] = 'changeRegion';
                this.events['click .' + CSS_CLASSES.COMMENT_BTN]       = 'toggleCommentArea';
                this.events['click .' + CSS_CLASSES.SUBMIT_ORDER]      = 'submitOrder';

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
                    this.subViews.commentArea.hide();
                } else {
                    this.subViews.commentArea.show();
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

            /**
             * Обработчик смены региона
             *
             * @method      changeRegion
             * @memberOf    module:enter.order.step2.view~OrderStep2View#
             *
             * @todo        написать обработчик
             */
            changeRegion: function() {
                console.info('module:enter.order.step2.view~OrderStep2View#changeRegion');

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

                this.$el.append(html);

                this.initialize();
            }
        }));
    }
);
