/**
 * @module      enter.userbar.view
 * @version     0.1
 *
 * @requires    enter.BaseViewClass
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.userbar.view',
        [
            'App',
            'enter.BaseViewClass',
            'Mustache'
        ],
        module
    );
}(
    this.modules,
    function( provide, App, BaseViewClass, mustache ) {
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
                FIXED: 'js-userbar-fixed'
            },

            /**
             * Используемые шаблоны
             *
             * @private
             * @constant
             * @type        {Object}
             */
            TEMPLATES = {
                COMPARING_ITEM: $('#js-userbar-comparing-item').html(),
            },

            $window = $(window);

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

                this.subViews = {
                    compare: this.$el.find('.' + CSS_CLASSES.COMPARE),
                    compareCounter: this.$el.find('.' + CSS_CLASSES.COMPARE_COUNTER),
                    comparingDD: this.$el.find('.' + CSS_CLASSES.COMPARE_DD)
                };

                // Setup events
                // this.events['click .' + CSS_CLASSES.] = '';

                if ( this.isFixed ) {
                    $window.on('scroll', this.scrollHandler.bind(this));
                }

                this.listenTo(App.compare, 'syncEnd', this.compareChange);

                // Apply events
                this.delegateEvents();
            },

            /**
             * События привязанные к текущему экземляру View
             *
             * @memberOf    module:enter.userbar.view~EnterUserbarView
             * @type        {Object}
             */
            events: {},

            /**
             * Хандлер изменения сравнения. Срабатывает каждый раз при добавлении\удалении\инициализации сравнения
             *
             * @method      compareChange
             * @memberOf    module:enter.userbar.view~EnterUserbarView#
             *
             * @listens     module:enter.compare.collection~CompareCollection#syncEnd
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
                }
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

                if ( this.target.length && !this.target.visible() ) {
                    this.$el.fadeIn();
                } else {
                    this.$el.fadeOut();
                }
            }
        }));
    }
);
