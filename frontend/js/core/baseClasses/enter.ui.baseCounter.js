/**
 * @module      enter.ui.baseCounter
 * @version     0.1
 *
 * @requires    Backbone
 * @requires    underscore
 * @requires    enter.BaseViewClass
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.ui.baseCounter',
        [
            'Backbone',
            'underscore',
            'enter.BaseViewClass'
        ],
        module
    );
}(
    this.modules,
    function( provide, Backbone, _, BaseView ) {
        'use strict';

        var
             /**
             * @classdesc   Базовый класс каунтера. Устанавливает значения каунтера по-умолчанию.
             * @memberOf    module:enter.ui.baseCounter~
             * @augments    module:enter.BaseViewClass
             * @constructs  BaseCounter
             */
            BaseCounter = function( options ) {
                this.disableClass = 'disabled';
                this.maxValue     = false;
                this.minValue     = 1;
                this.quantity     = 1;

                BaseView.apply(this, [options]);
            };

        _.extend(BaseCounter.prototype, {
            /**
             * Уменьшение каунтера
             *
             * @method      minus
             * @memberOf    module:enter.ui.baseCounter~BaseCounter#
             *
             * @fires       module:enter.ui.baseCounter~BaseCounter#changeQuantity
             */
            minus: function() {
                console.info('minus');
                if ( this.quantity === this.minValue ) {
                    return false;
                }

                this.quantity--;

                /**
                 * Событие уменьшения каунтера
                 *
                 * @event       module:enter.ui.baseCounter~BaseCounter#changeQuantity
                 * @type        {Object}
                 * @property    {Number}    quantity    Текущее значение счетчика
                 */
                this.trigger('changeQuantity', {
                    quantity: this.quantity
                });

                return false;
            },

            /**
             * Увеличивание каунтера
             *
             * @method      plus
             * @memberOf    module:enter.ui.baseCounter~BaseCounter#
             *
             * @fires       module:enter.ui.baseCounter~BaseCounter#changeQuantity
             */
            plus: function() {
                console.info('plus');
                if ( this.quantity === this.maxValue ) {
                    return false;
                }

                this.quantity++;

                /**
                 * Событие увеличения каунтера
                 *
                 * @event       module:enter.ui.baseCounter~BaseCounter#changeQuantity
                 * @type        {Object}
                 * @property    {Number}    quantity    Текущее значение счетчика
                 */
                this.trigger('changeQuantity', {
                    quantity: this.quantity
                });

                return false;
            }
        }, BaseView.prototype);

        BaseCounter.extend = BaseView.extend;

        provide(BaseCounter);
    }
);
