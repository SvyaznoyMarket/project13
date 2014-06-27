/**
 * Shim files as modules by template
 *
 * @see  {@link http://github.com/13rentgen/grunt-shim-modules}
 */
module.exports = {
    options: {
        template: './node_modules/grunt-shim-modules/ymodules-module_template.tpl',
            importNonFirst: true
    }

    /*
    jQuery: {
        src: '<%= pathRootV2 %>vendor/jquery-1.8.3.js',
            dest: '<%= pathRootV2 %>module/jquery.js',
            module_name: 'jQuery',
            desc: 'jQuery JavaScript Library',
            exports: '$'
    },

    mustache: {
        src: '<%= pathRootV2 %>vendor/mustache-0.8.2.js',
            dest: '<%= pathRootV2 %>module/mustache.js',
            module_name: 'mustache',
            desc: 'Logic-less {{mustache}} templates with JavaScript',
            exports: 'this.Mustache'
    },

    underscore: {
        src: '<%= pathRootV2 %>vendor/underscore-1.6.0.js',
            dest: '<%= pathRootV2 %>module/underscore.js',
            desc: 'Underscore.js 1.6.0',
            module_name: 'underscore',
            exports: 'this._'
    },

    lab: {
        src: '<%= pathRootV2 %>vendor/LAB-2.0.3.js',
            dest: '<%= pathRootV2 %>module/LAB.js',
            desc: 'JavaScript loader',
            module_name: 'LAB',
            exports: 'this.$LAB'
    }
    */
};