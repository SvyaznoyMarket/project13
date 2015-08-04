/**
 * @module      extendBackbone
 * @version     0.1
 *
 * @requires    Backbone
 * @requires    underscore
 * @requires    generateUUID
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'extendBackbone',
        [
            'Backbone',
            'underscore',
            'generateUUID'
        ],
        module
    );
}(
    this.modules,
    function( provide, Backbone, _, generateUUID ) {
        'use strict';

        var
            /**
             * Хранилище всех текущих AJAX запросов приложения
             *
             * @type  {Object}
             */
            currentAjaxCalls = {},

            /**
             * Общий счетчик AJAX запросов
             *
             * @type  {Number}
             */
            requestCounter = 0;


        /**
         * Переопределяем стандартный ajax вызов.
         * Обогощаем его возможностью очистки всех ajax вызовов.
         *
         * @method      ajax
         *
         * @param       {Object}    ajaxSettings    jQuery настройки ajax вызова [About jQuery.ajax]{@link http://api.jquery.com/jquery.ajax/}
         *
         * @return      {jQuery.xhr}
         */
        Backbone.ajax = function( ajaxSettings ) {
            var
                defaultSettings = {
                    type: 'POST',
                    dataType: 'json'
                },

                callSettings = _.extend(defaultSettings, ajaxSettings),
                xhr          = Backbone.$.ajax(callSettings),
                requestID    = ++requestCounter,

                /**
                 * Обратный вызов выполнения запроса
                 *
                 * @method  alwaysCb
                 */
                alwaysCb = function( rid, data, textStatus, jqXHR ) {
                    if ( data.status === 403 ) {
                        console.warn('Status 403');
                    }

                    delete currentAjaxCalls[rid];
                };


            console.info('Backbone.ajax',requestID);

            xhr.__requestID = requestID;
            currentAjaxCalls[requestID] = xhr;
            xhr.always(alwaysCb.bind(this, requestID));

            return xhr;
        };

        /**
         * Отмена всех текущих ajax запросов.
         * Если указан конкретный идентификатор запроса, то отменяем только его.
         *
         * @method      disposeAjax
         *
         * @param       {Number}    id    Идентификатор запроса который нужно отменить
         */
        Backbone.disposeAjax = function( id ) {
            var
                // Уникальная строка, позволяющая отделить собственный 'abort' запроса от реальной ошибки при выполнении запроса
                uuid = generateUUID(),
                key;

            console.warn('Backbone.disposeAjax', id);

            if ( id ) {
                if ( currentAjaxCalls.hasOwnProperty(id) ) {
                    currentAjaxCalls[id].abort(uuid);
                    delete currentAjaxCalls[key];
                }
            } else {
                for ( key in currentAjaxCalls ) {
                    if ( !currentAjaxCalls.hasOwnProperty(key) ) {
                        continue;
                    }

                    currentAjaxCalls[key].abort(uuid);
                    delete currentAjaxCalls[key];
                }
            }
        };

        provide(Backbone);
    }
);
