+function(){

    var elements;

    function loadScript(url, callback) {

        var script = document.createElement("script");
        script.type = "text/javascript";

        if (script.readyState) { //IE
            script.onreadystatechange = function () {
                if (script.readyState == "loaded" || script.readyState == "complete") {
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else { //Others
            script.onload = function () {
                callback();
            };
        }

        script.src = url + '?' +  (new Date()).getTime();
        document.getElementsByTagName("head")[0].appendChild(script);
    }

    modules.define('jQuery', [], function(provide){
        loadScript("https://yastatic.net/jquery/2.1.4/jquery.min.js", function () {
            console.log('[Module] jQuery');
            provide($);
        });
    });

    modules.define('underscore', [], function(provide){
        loadScript("/public/vendor/js/underscore.1.8.3.min.js", function () {
            console.log('[Module] underscore');
            provide(_);
        });
    });

    modules.define('ko', [], function(provide){
        loadScript("/public/vendor/js/knockout.3.3.1.min.js", function () {
            console.log('[Module] knockout');
            provide(ko);
        });
    });

    modules.define('Mustache', [], function(provide){
        loadScript("/public/vendor/js/mustache.2.1.2.min.js", function () {
            console.log('[Module] Mustache');
            provide(Mustache);
        });
    });

    modules.define('library', [], function(provide){
        loadScript("/public/js/library.js", function () {
            console.log('[Module] library');
            provide();
        });
    });

    modules.define('jquery.slick', ['jQuery'], function(provide){
        loadScript("/public/js/plugins/jquery.slick.js", function () {
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
        loadScript("/public/js/plugins/jquery.maskedinput.js", function () {
            console.log('[Module] jquery.maskedinput');
            provide();
        });
    });

    modules.define('jquery.lightbox_me', ['jQuery'], function(provide){
        loadScript("/public/js/plugins/jquery.lightbox_me.js", function () {
            console.log('[Module] jquery.lightbox_me');
            provide();
        });
    });

    modules.define('jquery.ui', ['jQuery'], function(provide){
        loadScript("/public/js/plugins/jquery-ui-1.11.4.custom.js", function () {
            console.log('[Module] jquery.ui');
            provide();
        });
    });

    modules.define('jquery.visible', ['jQuery'], function(provide){
        loadScript("/public/js/plugins/jquery.visible.js", function () {
            console.log('[Module] jquery.visible');
            provide();
        });
    });

    modules.define('history', [], function(provide){
        loadScript("/public/js/plugins/history.js", function () {
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

    modules.define('enter.search', ['jQuery', 'ko', 'library'], function(provide){
        loadScript("/public/js/modules/enter.search.js", function () {
            console.log('[Module] enter.search');
            provide();
        });
    });

    modules.define('enter.region', ['jQuery', 'jquery.slick', 'jquery.lightbox_me'], function(provide){
        loadScript("/public/js/modules/enter.region.js", function () {
            console.log('[Module] enter.region');
            provide({});
        });
    });

    modules.define('enter.catalog', ['jQuery'], function(provide){
        loadScript("/public/js/modules/enter.catalog.js", function () {
            console.log('[Module] enter.catalog');
            provide({
                init: function( el ) {
                    modules.require(['enter.catalog'], function( m ) {
                        m.init(el)
                    });
                }
            });
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

}();