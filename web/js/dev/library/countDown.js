/**
 * Модуль обратного счетчика
 *
 * @module      сountDown
 * @version     0.1
 *
 * @author      Zaytsev Alexandr
 */
!function( root, module ) {
    var
        CountDown = module();

    // AMD
    if ( typeof define === 'function' && define.amd ) {
        define('CountDown', [], function() {
            return CountDown;
        });

    // Node.js or CommonJS
    } else if ( typeof exports !== 'undefined' ) {
        exports.CountDown = CountDown;

    // YM Modules
    } else if ( typeof modules !== 'undefined' && typeof modules.define === 'function' ) {
        modules.define('CountDown', [], function( provide ) {
            provide(CountDown);
        });

    // Browser global
    } else {
        root.CountDown = CountDown;
    }

}( this, function() {
    'use strict';

    var
        /**
         * @return  {CountDown}
         */
        CountDown = (function() {
            var
                ONE_SEC  = 1000,
                ONE_MIN  = 60 * ONE_SEC,
                ONE_HOUR = 60 * ONE_MIN,
                ONE_DAY  = 24 * ONE_HOUR,

                helpers = {
                    isObject: function( obj ) {
                        var
                            type = typeof obj;

                        return type === 'function' || type === 'object' && !!obj;
                    },

                    isFunction: function( obj ) {
                        return typeof obj == 'function' || false;
                    },

                    isNumber: function( obj ) {
                        return toString.call(obj) === '[object Number]' && !isNaN(obj);
                    }
                },

                /**
                 * Обновление счетчика
                 *
                 * @memberOf    module:countDown~CountDown#
                 * @method      setExpression
                 * @private
                 *
                 * @this        {CountDown}
                 */
                updateCounter = function() {
                    var
                        now  = (new Date()).getTime(),
                        end  = this.endDate.getTime(),
                        diff = end - now;

                    if ( diff <= 0 ) {
                        console.log('Достигли даты завершения');
                        this.stop();
                    }

                    this.tick && this.tick({
                        days: Math.floor(diff/ONE_DAY),
                        hours: Math.floor((diff % ONE_DAY) / ONE_HOUR),
                        minutes: Math.floor(((diff % ONE_DAY) % ONE_HOUR) / ONE_MIN),
                        seconds: Math.floor((((diff % ONE_DAY) % ONE_HOUR) % ONE_MIN) / ONE_SEC)
                    })
                };

                /**
                 * @classdesc   Конструктор класса обратного счетчика
                 *
                 * @constructs  CountDown
                 *
                 * @throws      {Error}         Параметры должны быть объектом
                 * @throws      {Error}         Параметр "timestamp" должен быть объектом
                 * @throws      {Error}         Параметр "tick" должен быть функцией
                 *
                 * @param       {Object}        options
                 * @param       {Number}        options.timestamp   Время, до которого ведем обратный отсчет
                 * @param       {Function}      options.tick        Обратный вызов на каждый шаг счетчика
                 *
                 * @return      {CountDown}
                 */
                CountDown = function CountDown( options ) {
                    // Enforces new
                    if ( !(this instanceof CountDown) ) {
                        return new CountDown(options);
                    }

                    // Validate params
                    if ( !helpers.isObject(options) ) {
                        throw new Error('Параметры должны быть объектом');
                    }

                    if ( !helpers.isNumber(options.timestamp) ) {
                        throw new Error('Параметр "timestamp" должен быть объектом');
                    }

                    if ( !helpers.isFunction(options.tick) ) {
                        throw new Error('Параметр "tick" должен быть функцией');
                    }

                    this.endDate = new Date(options.timestamp);
                    this.tick    = options.tick;
                    this.iid     = setInterval(updateCounter.bind(this), 1000);
                };

            /**
             * Остановка счетчика
             *
             * @memberOf    module:countDown~CountDown#
             * @method      stop
             * @public
             */
            CountDown.prototype.stop = function() {
                clearInterval(this.iid);
            };

            return CountDown;
        }());

    return CountDown;
});
