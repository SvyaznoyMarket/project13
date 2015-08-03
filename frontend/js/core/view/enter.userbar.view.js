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
            'jQuery',
            'enter.BaseViewClass'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, BaseViewClass ) {
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
                FIXED: 'js-userbar-fixed'
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

                this.target  = options.target;
                this.isFixed = this.$el.hasClass(CSS_CLASSES.FIXED);

                // Setup events
                // this.events['click .' + CSS_CLASSES.] = '';

                if ( this.isFixed ) {
                    $window.on('scroll', this.scrollHandler.bind(this));
                }

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
