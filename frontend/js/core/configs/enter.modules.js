/**
 * @module      enter.modules
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.modules',
        [
            'loadScript'
        ],
        module
    );
}(
    this.modules,
    function( provide, loadScript ) {
        'use strict';

        var
            elements;


        /**
         * =============================
         * === DEFINE VENDOR SCRIPTS ===
         * =============================
         */

        modules.define('jquery.slick', ['jQuery'], function(provide){
            loadScript("plugins/jquery.slick.js", function () {
                var module = {};
                module.init = function(elem){
                    var $elem = $(elem),
                        config = $elem.data('slick-config'),
                        $slider = config && config.slider ? $elem.find(config.slider) : $elem;
                    if (config) {
                        // Предполагаем, что стрелочки находятся внутри слайдера
                        config = $.extend({}, config, { nextArrow: $elem.find(config.nextArrow), prevArrow: $elem.find(config.prevArrow)});
                        $slider.slick(config);
                    }
                };
                console.log('[Module] jquery.slick');
                provide(module);
            });
        });

        modules.define('jquery.maskedinput', ['jQuery'], function(provide){
            loadScript("plugins/jquery.maskedinput.js", function () {
                console.log('[Module] jquery.maskedinput');
                provide();
            });
        });

        modules.define('jquery.lightbox_me', ['jQuery'], function(provide){
            loadScript("plugins/jquery.lightbox_me.js", function () {
                console.log('[Module] jquery.lightbox_me');
                provide();
            });
        });

        modules.define('jquery.ui', ['jQuery'], function(provide){
            loadScript("plugins/jquery-ui-1.11.4.custom.js", function () {
                console.log('[Module] jquery.ui');
                provide();
            });
        });

        modules.define('jquery.visible', ['jQuery'], function(provide){
            loadScript("plugins/jquery.visible.js", function () {
                console.log('[Module] jquery.visible');
                provide();
            });
        });

        modules.define('history', [], function(provide){
            loadScript("plugins/history.js", function () {
                console.log('[Module] history');
                provide(History);
            });
        });

        /* ENTER Modules */
        modules.define('enter.debug', ['jQuery', 'Mustache', 'enter.config'], function(provide){
            window.ENTER = {};
            loadScript("/js/prod/debug-panel.js", function () {
                console.log('[Module] enter.debug');
                provide();
            });
        });


        // Пройдем по всем элементам
        elements = document.querySelectorAll('.js-module-require');

        if (elements) {
            for (var i in elements) {
                if (elements.hasOwnProperty(i) && typeof elements[i] == 'object'){
                    var moduleName =  elements[i].dataset.module;
                    if (moduleName) {
                        // closure
                        (function(name, elem){
                            modules.require(name, function(module){
                                if (typeof module.init == 'function') {
                                    module.init(elem);
                                }
                            });
                        })(moduleName, elements[i])
                    }
                }
            }
        }

        provide({});
    }
);
