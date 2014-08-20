/**
 * Source maps
 *
 * @link http://github.com/twolfson/grunt-jsmin-sourcemap
 */

module.exports = function (grunt, options) {

    var jqueryPlugins = options.gc.jqueryPlugins,
        libraryFiles = options.gc.libraryFiles;

    return {
        catalog: {
            src: ['dev/catalog/*.js'],
            dest: 'prod/catalog.js',
            destMap: 'prod/catalog.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        ports: {
            src: ['dev/ports/*.js'],
            dest: 'prod/ports.js',
            destMap: 'prod/ports.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        debugPanel: {
            src: ['dev/debug-panel/*.js'],
            dest: 'prod/debug-panel.js',
            destMap: 'prod/debug-panel.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        cart: {
            src: ['dev/cart/*.js'],
            dest: 'prod/cart.js',
            destMap: 'prod/cart.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        common: {
            src: ['dev/common/*.js'],
            dest: 'prod/common.js',
            destMap: 'prod/common.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        infopage: {
            src: ['dev/infopage/*.js'],
            dest: 'prod/infopage.js',
            destMap: 'prod/infopage.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        library: {
            src: libraryFiles,
                dest: 'prod/library.js',
                destMap: 'prod/library.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        jqueryPlugins: {
            src: jqueryPlugins,
                dest: 'prod/jquery-plugins.js',
                destMap: 'prod/jquery-plugins.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        lk: {
            src: ['dev/lk/*.js'],
                dest: 'prod/lk.js',
                destMap: 'prod/lk.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        enterprize: {
            src: ['dev/enterprize/*.js'],
                dest: 'prod/enterprize.js',
                destMap: 'prod/enterprize.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        main: {
            src: ['dev/main/*.js'],
                dest: 'prod/main.js',
                destMap: 'prod/main.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        oneclick: {
            src: ['dev/oneclick/*.js'],
                dest: 'prod/oneclick.js',
                destMap: 'prod/oneclick.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        order: {
            src: ['dev/order/*.js'],
                dest: 'prod/order.js',
                destMap: 'prod/order.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        order_new_v5: {
            src: ['dev/order-new-v5/*.js'],
                dest: 'prod/order-new-v5.js',
                destMap: 'prod/order-new-v5.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        pandora: {
            src: ['dev/pandora/*.js'],
                dest: 'prod/pandora.js',
                destMap: 'prod/pandora.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        product: {
            src: ['dev/product/*.js'],
                dest: 'prod/product.js',
                destMap: 'prod/product.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        shop: {
            src: ['dev/shop/*.js'],
                dest: 'prod/shop.js',
                destMap: 'prod/shop.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        tchibo: {
            src: ['dev/tchibo/*.js'],
                dest: 'prod/tchibo.js',
                destMap: 'prod/tchibo.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        watch3d: {
            src: ['dev/watch3d/*.js'],
                dest: 'prod/watch3d.js',
                destMap: 'prod/watch3d.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },

        serviceHa: {
            src: ['dev/service_ha/*.js'],
                dest: 'prod/service_ha.js',
                destMap: 'prod/service_ha.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        }
    }
};