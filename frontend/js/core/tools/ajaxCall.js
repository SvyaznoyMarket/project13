/**
 * Модуль для выполнения AJAX запросов.
 * Умеет отменять AJAX запросы как на уровне текущего экземпляра класса, так и на уровне всего приложения.
 *
 * Автоматически можно настроить время до показа лоадера. Настраивается опцией `timeToAjaxLoader`, на уровне свойства класса. По-умолчанию это свойство равно 300.
 * В опциях к запросу необходимо передать объект loader у которого должно быть два метода show и hide.
 *
 * Отмена всех AJAX запросов текущего экземпляра класса: this.disposeAjax()
 * Отмена всех AJAX запросов приложения Backbone.disposeAjax()
 *
 * @module      ajaxCall
 * @version     0.1
 *
 * @requires    extendBackbone
 * @requires    underscore
 * @requires    generateUUID
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'ajaxCall',
        [
            'extendBackbone',
            'underscore',
            'generateUUID'
        ],
        module
    );
}(
    this.modules,
    function( provide, Backbone, _, generateUUID ) {
        'use strict';

        provide({

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
             */
            ajax: function( ajaxSettings ) {
                var
                    self = this,
                    defaultSettings = {
                        dataType: 'json'
                    },

                    callSettings = _.extend(defaultSettings, ajaxSettings),
                    xhr          = Backbone.ajax(callSettings),
                    requestID    = xhr.__requestID,
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
                        if ( _.isObject(callSettings) && _.isObject(callSettings.loader) ) {
                            callSettings.loader.loading = false;

                            if ( _.isFunction(options.loader.hide) ) {
                                options.loader.hide.call(self);
                            }
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


                this.currentAjaxCalls[requestID] = true;

                if ( _.isObject(callSettings) && _.isObject(callSettings.loader) ) {
                    callSettings.loader.loading = true;

                    if ( _.isFunction(callSettings.loader.show) ) {
                        timeoutID = setTimeout(callSettings.loader.show.bind(self), this.timeToAjaxLoader);
                    }
                }

                xhr.always(alwaysCb.bind(this, requestID, timeoutID, callSettings));
                xhr.done(doneCb.bind(this, requestID, timeoutID));
                xhr.fail(failCb.bind(this, requestID, timeoutID));

                return xhr;
            },

            /**
             * Отменить все ajax запросы текущего экзмепляра
             *
             * @method  disposeAjax
             */
            disposeAjax: function() {
                var
                    key;

                for ( key in this.currentAjaxCalls ) {
                    if ( this.currentAjaxCalls.hasOwnProperty(key) ) {
                        Backbone.disposeAjax(key);
                    }
                }
            }
        });
    }
);
