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

        script.src = url;
        document.getElementsByTagName("head")[0].appendChild(script);
    }

    modules.define('jQuery', [], function(provide){
        loadScript("https://yastatic.net/jquery/2.1.4/jquery.min.js", function () {
            console.log('[Module] jQuery');
            provide($);
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
            provide();
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
            module.init = function(){
                console.log('slick init function')
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

    /* ENTER Modules */
    modules.define('enter.debug', ['jQuery', 'Mustache', 'enter.config'], function(provide){
        loadScript("/js/prod/debug-panel.js", function () {
            console.log('[Module] Debug panel');
            provide();
        });
    });

    // Пройдем по всем элементам
    elements = document.querySelectorAll('.js-module-require');

    if (elements) {
        for (var i in elements) {
            var moduleName = elements.hasOwnProperty(i) ? elements[i].dataset.module : null;
            if (moduleName) {
                modules.require(moduleName, function(module){
                    if (typeof module.init == 'function') module.init(elements[i]);
                });
            }
        }
    }

}();