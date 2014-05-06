(function(window, document, modules) {
    /**
     * Расширяем стандартные функции YM Modules
     */
    modules.require(['loader'], function(loader) {
        modules.setOptions({
            loader: loader
        });
    });
}(
    this,
    this.document,
    this.modules
));