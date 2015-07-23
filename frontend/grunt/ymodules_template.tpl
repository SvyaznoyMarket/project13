(function ( window, document, modules ) {

    var
        m = function( provide[%= import_dependencies %] ) {

            /**
             * Module body
             */
            [%= module_code %]

            provide([%= exports %]);
        };

    /**
     * [%= desc %]
     *
     * @module      [%= module_name %]
     */
    modules.define(
        '[%= module_name %]',
        [[%= dependencies %]],
        m
    );
}(
    this,
    this.document,
    this.modules
));
