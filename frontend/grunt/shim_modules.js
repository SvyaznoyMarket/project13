/**
 * Shim files as modules by template
 *
 * @see  {@link https://github.com/13rentgen/grunt-shim-modules}
 */
module.exports = {
    options: {
        template: '<%= paths.config %>/ymodules_template.tpl',
        importNonFirst: true
    },

    docCookies: {
        src: '<%= paths.jsCore %>/vendor-to-shim/cookies.js',
        dest: '<%= paths.temp %>/cookies.shim.js',
        module_name: 'docCookies',
        desc: 'A complete cookies reader/writer framework with full unicode support.',
        exports: 'docCookies'
    },

    mustache: {
        src: '<%= paths.jsCore %>/vendor-to-shim/mustache.2.1.2.min.js',
        dest: '<%= paths.temp %>/mustache.shim.js',
        module_name: 'Mustache',
        desc: 'Logic-less {{mustache}} templates with JavaScript',
        exports: 'this.Mustache'
    },

    underscore: {
        src: '<%= paths.jsCore %>/vendor-to-shim/underscore.1.8.3.js',
        dest: '<%= paths.temp %>/underscore.shim.js',
        desc: 'Underscore.js 1.8.3',
        module_name: 'underscore',
        exports: 'this._'
    }
}
