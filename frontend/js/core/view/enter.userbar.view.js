/**
 * @module      enter.userbar.view
 * @version     0.1
 *
 * @requires    App
 * @requires    enter.BaseViewClass
 * @requires    Mustache
 * @requires    jquery.scrollTo
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.userbar.view',
        [
            'App',
            'jQuery',
            'enter.BaseViewClass',
            'Mustache',
            'jquery.scrollTo'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, $, BaseViewClass, mustache, jqueryScrollTo ) {
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
                COMPARE: 'user-controls__item_compare',
                COMPARE_COUNTER: 'js-userbar-compare-counter',
                COMPARE_ACTIVE: 'active',
                COMPARE_DD: 'js-userbar-compare-dd',
                STATIC: 'js-userbar',
                FIXED: 'js-userbar-fixed',
                GO_TO_ID: 'js-userbar-goto-id'
            },

            /**
             * Используемые шаблоны
             *
             * @private
             * @constant
             * @type        {Object}
             */
            TEMPLATES = {
                COMPARING_ITEM: $('#js-userbar-comparing-item').html()
            },

            $WINDOW   = $(window),
            $DOCUMENT = $(document);

        provide(BaseViewClass.extend({

            isFixed: false,

            /**
             * @classdesc   Представление параплашки
             * @memberOf    module:enter.userbar.view~
             * @augments    module:BaseViewClass
             * @constructs  EnterUserbarView
             */
            initialize: function( options ) {
                console.groupCollapsed('module:enter.userbar.view~EnterUserbarView#initialize');
                console.dir(this);
                console.groupEnd();

                this.target     = options.target;
                this.isFixed    = this.$el.hasClass(CSS_CLASSES.FIXED);
                this.timeToHide = 5 * 1000; // 5 sec
                this.staticHeight = $(document.body).find('.' + CSS_CLASSES.STATIC).height();

                this.subViews = {
                    compare: this.$el.find('.' + CSS_CLASSES.COMPARE),
                    compareCounter: this.$el.find('.' + CSS_CLASSES.COMPARE_COUNTER),
                    comparingDD: this.$el.find('.' + CSS_CLASSES.COMPARE_DD)
                };

                // Setup events
                this.events['click .' + CSS_CLASSES.GO_TO_ID] = 'goToId';

                if ( this.isFixed ) {
                    $WINDOW.on('scroll', this.scrollHandler.bind(this));
                }

                this.listenTo(App.compare, 'syncEnd', this.compareChange);
                this.listenTo(App, 'showuserbar', this.showFixedUserbar);

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.userbar.view~EnterUserbarView
             * @type        {Object}
             */
            events: {
                'click .js-userbar-comparing-closer': 'closeCompareDd'
            },

            /**
             * Перелистывание страницы до идентификатора
             *
             * @method  goToId
             */
            goToId: function( event ) {
                var
                    target = $(event.currentTarget),
                    toEl   = $('#' + target.attr('data-goto')),
                    to     = toEl.offset().top - this.$el.outerHeight() - 20;

                $DOCUMENT.stop().scrollTo(to, 800);
            },

            /**
             * Хандлер изменения сравнения. Срабатывает каждый раз при добавлении\удалении\инициализации сравнения
             *
             * @method      compareChange
             * @memberOf    module:enter.userbar.view~EnterUserbarView#
             *
             * @listens     module:enter.compare.collection~CompareCollection#syncEnd
             * @fires       module:App#showuserbar
             */
            compareChange: function( event ) {
                var
                    self = this,
                    productHtml;

                console.groupCollapsed('module:enter.userbar.view~EnterUserbarView#compareChange');
                console.dir(event);
                console.groupEnd();

                if ( event.size ) {
                    this.subViews.compare.addClass(CSS_CLASSES.COMPARE_ACTIVE);
                    this.subViews.compareCounter.html(event.size);
                } else {
                    this.subViews.compare.removeClass(CSS_CLASSES.COMPARE_ACTIVE);
                    this.subViews.compareCounter.html('');
                }

                if ( event.product ) {
                    productHtml = mustache.render(TEMPLATES.COMPARING_ITEM, event.product);
                    this.subViews.comparingDD.html(productHtml);
                    this.subViews.comparingDD.fadeIn();
                    this.tid && clearTimeout(this.tid);
                    this.tid = setTimeout(self.subViews.comparingDD.fadeOut.bind(self.subViews.comparingDD, 300), this.timeToHide);

                    App.trigger('showuserbar');
                }
            },

            /**
             * Закрыть попап сравнения
             */
            closeCompareDd: function() {
                this.subViews.comparingDD.fadeOut();
            },

            /**
             * Принудительный показ юзербара
             *
             * @method      showFixedUserbar
             * @memberOf    module:enter.userbar.view~EnterUserbarView#
             *
             * @listens     module:App#showuserbar
             */
            showFixedUserbar: function() {
                if ( !this.isFixed ) {
                    return;
                }

                if ( this.$el.offset().top < 10 ) {
                    return;
                }

                this.$el.fadeIn();
            },

            /**
             * Обработчик скролла страницы
             *
             * @method      scrollHandler
             * @memberOf    module:enter.userbar.view~EnterUserbarView#
             */
            scrollHandler: function() {
                if ( !this.isFixed ) {
                    return;
                }

                if ( this.target.length &&
                    ( !this.target.visible() || this.target.offset().top < window.pageYOffset + this.staticHeight )
                    ) {
                    this.$el.fadeIn();
                } else {
                    this.$el.fadeOut();
                }
            }
        }));
    }
);
