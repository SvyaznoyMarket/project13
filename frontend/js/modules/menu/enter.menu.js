/**
 * @module      enter.auth
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.menu',
        ['jQuery', 'lscache'],
        module
    );
}(
    this.modules,
    function( provide, $, lscache ) {
        'use strict';

        var module = {};

        /**
         * Хранилище данных. Возвращает либо lscache, либо объект с необходимыми свойствами (функциями)
         * @return {*}
         * @link https://github.com/pamelafox/lscache
         * @constructor
         */
        module.storage = (function () {
            var cKey = 'cachedData';
            if (lscache && typeof lscache.supported == 'function' && lscache.supported()) {
                return lscache
            } else {
                return {
                    'set': function(key, value, time, $el){
                        $el.data(cKey, value);
                    },
                    'get': function(key, $el) {
                        return $el.data(cKey) ? $el.data(cKey) : false;
                    },
                    'remove': function(key, $el) {
                        $el.data(cKey, false);
                    }
                }
            }
        })();

        /**
         * Заполнение блоков меню "товарами дня"
         * @param $el
         * @param blocks
         */
        module.fillRecommendBlocks = function fillRecommendBlocksF($el, blocks) {

            var $containers = $el.find('.jsMenuRecommendation');

            $.each(blocks, function(i, block) {
                try {
                    if (!block.categoryId) return;
                    var $container = $containers.filter('[data-parent-category-id="' + block.categoryId + '"]');

                    /* TODO переписать запрос модуля */
                    $(block.content).find('.js-module-require').each(function(i, el){
                        modules.require($(this).data('module'), function(module){
                            if (typeof module.init == 'function') module.init(el);
                        })
                    });

                    $container.html(block.content);
                } catch (e) {
                    console.error(e);
                }
            });
        };

        module.init = function(el){

            var	$el = $(el),
                url = $el.data('recommendUrl'),
                lKey = 'xhrLoading', // ключ для предотвращения дополнительного запроса на загрузку данных
                cacheTime = 10, // время кэширования в localstorage (в минутах)
                key, xhr;

            if (typeof url == 'string' && !$el.data(lKey) === true) {

                // отрезаем от url параметры для ключа в localstorage
                key = url.indexOf('?') === -1 ? url : url.substring(0, url.indexOf('?'));

                if (!module.storage.get(key, $el)) {

                    xhr = $.get(url);
                    $el.data(lKey, true);

                    xhr.done(function(response) {
                        var data = response.productBlocks;
                        if (!data) return;
                        module.storage.set(key, data, cacheTime, $el);
                        module.fillRecommendBlocks($el, data);
                    }).fail(function() {
                        module.storage.remove(key, $el);
                    }).always(function(){
                        $el.data(lKey, false)
                    });
                } else {
                    module.fillRecommendBlocks($el, module.storage.get(key, $el));
                }

            }
        };

        provide(module);
    }
);