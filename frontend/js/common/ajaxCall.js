/**
 * Модуль для выполнения AJAX запросов.
 * Умеет отменять AJAX запросы как на уровне текущего экземпляра класса, так и на уровне всего приложения.
 *
 * Автоматически можно настроить время до показа лоадера. Настраивается опцией `timeToAjaxLoader`, на уровне свойства класса. По-умолчанию это свойство равно 300.
 * В опциях к запросу необходимо передать объект loader у которого должно быть два метода show и hide.
 *
 * Отмена всех AJAX запросов текущего экземпляра класса: this.disposeAjax()
 * Отмена всех AJAX запросов приложения this.disposeAjax(this.DISPOSE_GLOBAL)
 *
 * @module      ajaxCall
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'ajaxCall',
        [
            'jQuery',
            'underscore',
            'generateUUID'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, _, generateUUID ) {
        'use strict';

        var
            /**
             * Общий счетчик AJAX запросов
             *
             * @type  {Number}
             */
            requestCounter  = 0,

            /**
             * Хранилище всех текущих AJAX запросов приложения
             *
             * @type  {Object}
             */
            globalAjaxCalls = {},

            /**
             * Отмена всех текущих ajax запросов.
             * Если указан конкретный идентификатор запроса, то отменяем только его.
             *
             * @method      disposeAjax
             *
             * @param       {Number}    id    Идентификатор запроса который нужно отменить
             */
            globalDisposeAjax = function( id ) {
                var
                    // Уникальная строка, позволяющая отделить собственный 'abort' запроса от реальной ошибки при выполнении запроса
                    uuid = generateUUID(),
                    key;

                console.warn('Backbone.disposeAjax', id);

                if ( id ) {
                    if ( globalAjaxCalls.hasOwnProperty(id) ) {
                        globalAjaxCalls[id].abort(uuid);
                        delete globalAjaxCalls[key];
                    }
                } else {
                    for ( key in globalAjaxCalls ) {
                        if ( !globalAjaxCalls.hasOwnProperty(key) ) {
                            continue;
                        }

                        globalAjaxCalls[key].abort(uuid);
                        delete globalAjaxCalls[key];
                    }
                }
            };

        provide({
            /**
             * Ключ при котором отмена всех текущих AJAX запросов будет выполнена на уровне всего приложения
             *
             * @type  {String}
             */
            DISPOSE_GLOBAL: 'hsadkfjhalksjdhfkjlasdhf',

            /**
             * Стандратное время до показа лоадера
             *
             * @memberOf    module:ajaxCall
             * @type        {Number}
             */
            timeToAjaxLoader: 300,

            /**
             * Хранилище текущих AJAX запросов класса
             *
             * @memberOf    module:ajaxCall
             * @type        {Object}
             */
            currentAjaxCalls: {},

            /**
             * Создание AJAX вызова
             *
             * @memberOf    module:ajaxCall#
             * @method      ajax
             *
             * @param       {Object}    ajaxSettings    jQuery настройки ajax вызова [About jQuery.ajax]{@link http://api.jquery.com/jquery.ajax/}
             * @param       {Function}  options         Дополнительные настройки ajax вызова
             */
            ajax: function( ajaxSettings, options ) {
                var
                    defaultSettings = {
                        dataType: 'json'
                    },

                    callSettings = _.extend(defaultSettings, ajaxSettings),
                    xhr          = $.ajax(callSettings),
                    requestID    = ++requestCounter,
                    timeoutID,

                    /**
                     * Обратный вызов выполнения запроса
                     *
                     * @method  alwaysCb
                     *
                     * @param   {Number}  rid       Идентификатор ajax запроса
                     * @param   {Number}  tid       Идентификатор таймаута
                     * @param   {Object}  options   Параметры запроса
                     */
                    alwaysCb = function( rid, tid, options ) {
                        if ( _.isObject(options) && _.isObject(options.loader) && _.isFunction(options.loader.hide) ) {
                            options.loader.hide();
                        }

                        tid && clearTimeout(tid);
                        delete this.currentAjaxCalls[rid];
                    },

                    /**
                     * Обратный вызов успешного выполнения запроса
                     *
                     * @method  doneCb
                     *
                     * @param   {Number}  rid Идентификатор ajax запроса
                     * @param   {Number}  tid Идентификатор таймаута
                     */
                    doneCb = function( rid, tid ) {
                        tid && clearTimeout(tid);
                    },

                    /**
                     * Обратный вызов неуспешного выполнения запроса
                     *
                     * @method  failCb
                     *
                     * @param   {Number}  rid Идентификатор ajax запроса
                     * @param   {Number}  tid Идентификатор таймаута
                     */
                    failCb = function( rid, tid ) {
                        tid && clearTimeout(tid);
                    };


                xhr.__requestID                  = requestID;
                this.currentAjaxCalls[requestID] = true;
                globalAjaxCalls[requestID]       = xhr;

                if ( _.isObject(options) && _.isObject(options.loader) && _.isFunction(options.loader.show) ) {
                    timeoutID = setTimeout(options.loader.show, this.timeToAjaxLoader)
                }

                xhr.always(alwaysCb.bind(this, requestID, timeoutID, options));
                xhr.done(doneCb.bind(this, requestID, timeoutID));
                xhr.fail(failCb.bind(this, requestID, timeoutID));

                return xhr;
            },

            /**
             * Отменить все ajax запросы текущего экзмепляра
             *
             * @method  disposeAjax
             */
            disposeAjax: function( disposeGlobal ) {
                var
                    key;

                if ( disposeGlobal === this.DISPOSE_GLOBAL ) {
                    globalDisposeAjax();
                } else {
                    for ( key in this.currentAjaxCalls ) {
                        if ( this.currentAjaxCalls.hasOwnProperty(key) ) {
                            globalDisposeAjax(key);
                        }
                    }
                }
            }
        });
    }
);
