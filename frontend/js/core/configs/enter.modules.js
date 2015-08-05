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
                        $slider = config && config.slider ? $elem.find(config.slider) : $elem,
                        initClass = 'js-slick-inited';
                    if (config && !$slider.hasClass(initClass)) {
                        // Предполагаем, что стрелочки находятся внутри слайдера
                        config = $.extend({}, config, { nextArrow: $elem.find(config.nextArrow), prevArrow: $elem.find(config.prevArrow)});
                        $slider.slick(config).addClass(initClass);
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

        modules.define('lscache', [], function(provide){
            loadScript("plugins/lscache.js", function () {
                console.log('[Module] lscache');
                provide(lscache);
            });
        });

        /* ENTER Modules */
        modules.define('enter.debug', ['jQuery', 'Mustache', 'enter.config'], function(provide){
            window.ENTER = {};
            loadScript("enter.modules/enter.debug.js", function () {
                console.log('[Module] enter.debug');
                provide();
            });
        });

        provide({});
    }
);
