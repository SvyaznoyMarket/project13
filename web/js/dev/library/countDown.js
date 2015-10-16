/**
 * Модуль обратного счетчика
 *
 * @module      CountDown
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
                ONE_SEC             = 1000,
                ONE_MIN             = 60 * ONE_SEC,
                ONE_HOUR            = 60 * ONE_MIN,
                ONE_DAY             = 24 * ONE_HOUR,
                SUCCESSFUL_COMPLETE = true,

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
                 * @memberOf    module:CountDown~CountDown#
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
                        this.stop(SUCCESSFUL_COMPLETE);
                    }

                    this.tick && this.tick({
                        el: this.el,
                        days: Math.floor(diff/ONE_DAY),
                        hours: Math.floor((diff % ONE_DAY) / ONE_HOUR),
                        minutes: Math.floor(((diff % ONE_DAY) % ONE_HOUR) / ONE_MIN),
                        seconds: Math.floor((((diff % ONE_DAY) % ONE_HOUR) % ONE_MIN) / ONE_SEC)
                    });
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
                 * @param       {Function}      [options.success]   Обратный вызов завершения счетчика
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

                    if ( options.success && !helpers.isFunction(options.success) ) {
                        throw new Error('Параметр "success" должен быть функцией');
                    }

                    this.el = options.el;
                    this.endDate = new Date(options.timestamp);
                    this.tick    = options.tick;
                    this.success = options.success;
                    this.iid     = setInterval(updateCounter.bind(this), 1000);
                };

            /**
             * Остановка счетчика
             *
             * @memberOf    module:CountDown~CountDown#
             * @method      stop
             * @public
             *
             * @param       {Boolean}   successfulComplete  Флаг успешного завершения счетчика
             */
            CountDown.prototype.stop = function( successfulComplete ) {
                clearInterval(this.iid);

                if ( successfulComplete && this.success ) {
                    this.success();
                }
            };

            return CountDown;
        }());

    return CountDown;
});

/**
 * Обратный счетчик акции
 */
!function() {
    var
        countDownWrapper = $('.js-countdown'),

        getDeclension = function( days ) {
            var
                str      = days + '',
                lastChar = str.slice(-1),
                lastNum  = lastChar * 1;

            if ( lastNum === 0 ) {
                return 'дней';
            } else if ( days > 4 && days < 21 ) {
                return 'дней';
            } else if ( lastNum > 4 && days > 20 ) {
                return 'дней';
            } else if ( lastNum > 1 && lastNum < 5 ) {
                return 'дня';
            }

            return 'день';
        },

        tick = function( opts ) {
            var
                countDownOut  = $(opts.el).hasClass('js-countdown-out') ? $(opts.el) : $(opts.el).find('.js-countdown-out'),
                mask = ( opts.days > 0 ) ? 'D ' + getDeclension(opts.days) + ' HH:MM:SS' : 'HH:MM:SS';

            mask = mask.replace(/(D+)/, function( str, d) { return (d.length > 1 && opts.days < 10 ) ? '0' + opts.days : opts.days });
            mask = mask.replace(/(H+)/, function( str, h) { return (h.length > 1 && opts.hours < 10 ) ? '0' + opts.hours : opts.hours });
            mask = mask.replace(/(M+)/, function( str, m) { return (m.length > 1 && opts.minutes < 10 ) ? '0' + opts.minutes : opts.minutes });
            mask = mask.replace(/(S+)/, function( str, s) { return (s.length > 1 && opts.seconds < 10 ) ? '0' + opts.seconds : opts.seconds });

            countDownOut.html(mask);
        };

    try {
        countDownWrapper.each(function(i,el){
            new CountDown({
                // timestamp: 1445597200000,
                timestamp: $(el).attr('data-expires') * 1000,
                tick: tick,
                el: el
            })
        });
    } catch ( err ) {
        // console.warn('Не удалось запустить обратный счетчик акции');
        // console.warn(err);
    }

}();
