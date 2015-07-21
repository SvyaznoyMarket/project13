/**
 * @param       {Object}     window     Ссылка на window
 * @param       {Object}     document   Ссылка на document
 * @param       {Object}     modules    Ссылка на модульную систему YModules
 */
!function ( window, document, modules, undefined ) {
    'use strict';

    var
        moduleUrlHelper = function( provide ) {
            var
                /**
                 * @alias   module:getURLParameter
                 */
                urlHelper = (function() {
                    var
                        /**
                         * Экранирование строки для применения ее в качестве RegExp
                         *
                         * @method      escape
                         * @memberOf    module:urlHelper#
                         * @private
                         *
                         * @param   {String}    text    Текст подлежащий экранированию
                         *
                         * @return  {String}            Экранированный текст
                         */
                        escape = function escape( text ) {
                            return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
                        },

                        /**
                         * Добавить новый параметр в URL
                         *
                         * @method      addParam
                         * @memberOf    module:urlHelper#
                         * @private
                         *
                         * @param   {String}    url     url
                         * @param   {String}    key     Ключ
                         * @param   {String}    value   Значение
                         *
                         * @return  {String}            Сформированный URL
                         */
                        addParam = function addParam( url, key, value ) {
                            var
                                re = new RegExp('([?|&])' + escape(key) + '=.*?(&|#|$)(.*)', 'gi'),
                                separator,
                                valueKey,
                                newKey,
                                hash;
                            // end of vars

                            if ( typeof value === 'object' ) {
                                for ( valueKey in value ) {
                                    if ( value.hasOwnProperty(valueKey) ) {
                                        newKey = key + '[' + valueKey + ']';

                                        url = addParam(url, key + '[' + valueKey + ']', value[valueKey]);
                                    }
                                }

                                return url;
                            }

                            value = encodeURIComponent(value);

                            if ( re.test(url) ) {
                                if ( typeof value !== 'undefined' && value !== null ) {
                                    return url.replace(re, '$1' + key + '=' + value + '$2$3');
                                } else {
                                    return url.replace(re, '$1$3').replace(/(&|\?)$/, '');
                                }
                            } else {
                                if ( typeof value !== 'undefined' && value !== null ) {
                                    separator = url.indexOf('?') !== -1 ? '&' : '?';
                                    hash = url.split('#');
                                    url = hash[0] + separator + key + '=' + value;

                                    if ( hash[1] ) {
                                        url += '#' + hash[1];
                                    }

                                    return url;
                                } else {
                                    return url;
                                }
                            }
                        },

                        /**
                         * Очистка URL от ошибочных параметров
                         *
                         * @method      clearUrl
                         * @memberOf    module:urlHelper#
                         * @private
                         *
                         * @param       {String}    url
                         *
                         * @return      {String}
                         */
                        clearUrl = function( url ) {
                            var
                                decodedUrl     = decodeURIComponent(url),
                                reBadParam     = /([^\s=&?]*=undefined&?|[^\s=&?]*=&|(?:\?|&)[^\s=&?]*=?$)/gi,
                                reBadSeparator = /\?&/;
                            // end of vars

                            return decodedUrl.replace(reBadParam, '').replace(reBadParam, '').replace(reBadSeparator, '?');
                        },

                        /**
                         * Добавление параметров в URL
                         *
                         * @method      addParams
                         * @memberOf    module:urlHelper#
                         * @public
                         *
                         * @param   {String}    url     URL в который необходимо добавить параметры
                         * @param   {Object}    params  Объект ключей и значений, которые необходимо добавть в URL
                         *
                         * @return  {String}            Сформированный URL
                         */
                        addParams = function addParams( url, params ) {
                            var
                                newUrl = decodeURIComponent(url),
                                key,
                                value;
                            // end of vars

                            for ( key in params ) {
                                if ( params.hasOwnProperty(key) ) {
                                    value = params[key];
                                    newUrl = addParam(newUrl, key, value);
                                }
                            }

                            return clearUrl(newUrl);
                        },

                        /**
                         * Получение параметра из строки запроса URL
                         *
                         * @method      getURLParam
                         * @memberOf    module:urlHelper#
                         * @public
                         *
                         * @param       {String}    name    Имя параметра, значение которого необходимо получить
                         *
                         * @return      {String}    Значение параметра URL
                         */
                        getURLParam = function getURLParam( name ) {
                            var
                                // url = window.location.search,
                                url = decodeURIComponent(window.location.search),
                                re  = new RegExp(escape(name) + '=' + '(.+?)(&|$)'),
                                out = decodeURI(
                                    (re.exec(url)||[null,null])[1]
                                );
                            // end of vars

                            return ( out === 'null' ) ? null : out;
                        },

                        /**
                         * Удаление параметра из строки запроса URL
                         *
                         * @method      removeURLParam
                         * @memberOf    module:urlHelper#
                         * @private
                         *
                         * @param   {String}    url     URL в который необходимо добавить параметры
                         * @param   {String}    param   Параметр который необходимо удалить из URL
                         *
                         * @return  {String}    Обновленный URL
                         */
                        removeURLParam = function removeURLParam( url, param ) {
                            var
                                decodedUrl = decodeURIComponent(url),
                                re         = new RegExp(escape(param) +'=[^&|#|$]*','gi'),
                                newUrl;
                            // end of vars

                            newUrl = decodedUrl.replace(re, '').replace(/\?{1,}/g, '?').replace(/\&{1,}/g, '&');
                            newUrl = ( newUrl.slice(-1) === '?' ) ? newUrl.slice(0, -1) : newUrl;

                            return clearUrl(newUrl);
                        },

                        /**
                         * Удаление параметров из строки запроса URL
                         *
                         * @method      removeURLParams
                         * @memberOf    module:urlHelper#
                         * @public
                         *
                         * @param   {String}    url     URL в который необходимо добавить параметры
                         * @param   {Array}     params  Параметры которые необходимо удалить из URL
                         *
                         * @return  {String}    Обновленный URL
                         */
                        removeURLParams = function removeURLParams( url, params ) {
                            var
                                i,
                                newUrl = url;
                            // end of vars

                            for ( i = 0; i < params.length; i++ ) {
                                newUrl = removeURLParam(newUrl, params[i]);
                            }

                            if ( newUrl[newUrl.length - 1] === '?' || newUrl[newUrl.length - 1] === '&' ) {
                                return newUrl.slice(0, newUrl.length - 1);
                            }

                            return newUrl;
                        },

                        /**
                         * Взять hash из строки и вернуть ключ - значение
                         *
                         * @method      getHash
                         * @memberOf    module:urlHelper#
                         * @public
                         *
                         * @param   {String}    url     URL из которого достаем hash
                         *
                         * @return  {Object}    Ключ и значение
                         */
                        getHash = function getHash( url ) {
                            var
                                result = {},

                                re = /#([^&|#|$|=]*)=([^&|#|$|=]*)/i,
                                replacer = function( str, p1, p2, offset, s ) {
                                    result = {
                                        key: p1 || false,
                                        value: p2 || false
                                    };
                                };
                            // end of vars

                            url.replace(re, replacer);

                            return result;
                        };
                    // end of vars


                    return {
                        addParams: addParams,
                        getURLParam: getURLParam,
                        removeURLParams: removeURLParams,
                        getHash: getHash
                    };
                }());
            // end of vars

            provide(urlHelper);
        };
    // end of vars

    /**
     * Получение параметра из строки запроса URL
     *
     * @module      urlHelper
     * @version     1.3
     */
    modules.define(
        'urlHelper',
        [],
        moduleUrlHelper
    );
}(
    this,
    this.document,
    this.modules
);
