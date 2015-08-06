/**
 * @module      enter.productkit.item.view
 * @version     0.1
 *
 * @author      Zaytsev Alexandr
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.productkit.item.view',
        [
            'jQuery',
            'enter.BaseViewClass',
            'enter.productkit.counter.view',
            'Mustache',
            'printPrice'
        ],
        module
    );
}(
    this.modules,
    function( provide, jQuery, BaseViewClass, ProductKitItemCounterView, mustache, printPrice ) {
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
                DISABLED: 'disable',
                COUNTER: 'js-productkit-item-counter',
                SUM: 'js-productkit-item-counter-sum'
            },

            /**
             * Используемые шаблоны
             *
             * @private
             * @constant
             * @type        {Object}
             */
            TEMPLATES = {};

        provide(BaseViewClass.extend({
            /**
             * @classdesc   Представление элемента набора
             * @memberOf    module:enter.productkit.item.view~
             * @augments    module:BaseViewClass
             * @constructs  ProductKitItemView
             */
            initialize: function( options ) {
                console.info('module:enter.productkit.item.view~ProductKitItemView#initialize');

                // Setup events
                // this.events['click .' + CSS_CLASSES.] = '';

                this.listenTo(this.model, 'change:removedItem', this.checkRemovedStatus);
                this.listenTo(this.model, 'change:sum', this.changeSum);

                // Apply events
                this.delegateEvents();
            },

            events: {},

            /**
             * Обработчик изменения свойства `removedItem` у модели
             *
             * @method      checkRemovedStatus
             * @memberOf    module:enter.productkit.item.view~ProductKitItemView#
             *
             * @listens     module:enter.productkit.model~ProductKitModel#change:removedItem
             */
            checkRemovedStatus: function() {
                var
                    status = this.model.get('removedItem');

                if ( status ) {
                    this.$el.addClass(CSS_CLASSES.DISABLED);
                } else {
                    this.$el.removeClass(CSS_CLASSES.DISABLED);
                }
            },

            /**
             * Обработчик изменения свойства `sum` у модели
             *
             * @method      changeSum
             * @memberOf    module:enter.productkit.item.view~ProductKitItemView#
             *
             * @listens     module:enter.productkit.model~ProductKitModel#change:sum
             *
             * @param       {Object}    model
             * @param       {String}    newSum
             */
            changeSum: function( model, newSum ) {
                var
                    textSum = ( newSum ) ? newSum : this.model.get('price');

                this.sumWrap.text(printPrice(textSum));
            },

            /**
             * Отрисовка представления элемента набора
             *
             * @method      render
             * @memberOf    module:enter.productkit.item.view~ProductKitItemView#
             */
            render: function() {
                var
                    // EXAMPLE
                    html = mustache.render('{{tmp}}', { tmp: 'TEMP DATA ' + this.model.get('id')}),
                    self = this,
                    counters;

                this.$el     = $(html);
                this.sumWrap = this.$el.find('.' + CSS_CLASSES.SUM);
                counters     = this.$el.find('.' + CSS_CLASSES.COUNTER);

                console.groupCollapsed('module:enter.productkit.item.view~ProductKitItemView#render');
                console.dir(html);
                console.groupEnd();

                // Init counters
                counters.each(function( index ) {
                    self.subViews['counter_' + index] = new ProductKitItemCounterView({
                        el: $(this)
                    });
                });

                this.checkRemovedStatus();
                this.delegateEvents();

                return this.$el;
            }
        }));
    }
);
