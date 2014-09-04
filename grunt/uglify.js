module.exports = function (grunt, options) {

    var jqueryPlugins = options.gc.jqueryPlugins,
        libraryFiles = options.gc.libraryFiles,
        pathDev = options.pathDev,
        pathProd = options.pathProd,
        pathRoot = options.pathRoot;

    return {

        options: {
            compress: {
                drop_console: true
            }
        }
        ,
        partnerScripts: {
            files: [
                {
                    expand: true,
                    cwd: 'web/js/partner/',
                    src: ['*.js'],
                    dest: pathProd + '',
                    rename: function(destBase, destPath) {
                        return destBase + destPath.replace('js', 'min.js');
                    }
                }
            ]
        },

        vendorScripts: {
            files: [
                {
                    expand: true,
                    cwd: 'web/js/vendor/',
                    src: ['*.js'],
                    dest: pathProd + '',
                    rename: function(destBase, destPath) {
                        return destBase + destPath.replace('js', 'min.js');
                    }
                }
            ]
        },

        loadJS: {
            src: pathRoot + 'loadjs.js',
            dest: pathRoot + 'loadjs.min.js'
        },

        cartJS: {
            src: pathDev + 'cart/*.js',
            dest: pathProd + 'cart.min.js'
        },

        commonJS: {
            src: pathDev + 'common/*.js',
            dest: pathProd + 'common.min.js'
        },

        infopageJS: {
            src: pathDev + 'infopage/*.js',
            dest: pathProd + 'infopage.min.js'
        },

        libraryJS: {
            src: libraryFiles,
            dest: pathProd + 'library.min.js'
        },

        jqueryPlugins: {
            options: {
                mangle: {
                    except: ['jQuery']
                }
            },
            src: jqueryPlugins,
            dest: pathProd + 'jquery-plugins.min.js'
        },

        lkJS: {
            src: pathDev + 'lk/*.js',
            dest: pathProd + 'lk.min.js'
        },

        enterprizeJS: {
            src: pathDev + 'enterprize/*.js',
            dest: pathProd + 'enterprize.min.js'
        },

        mainJS: {
            src: pathDev + 'main/*.js',
            dest: pathProd + 'main.min.js'
        },

        oneclickJS: {
            src: pathDev + 'oneclick/*.js',
            dest: pathProd + 'oneclick.min.js'
        },

        orderJS : {
            src: pathDev + 'order/*.js',
            dest: pathProd + 'order.min.js'
        },

        orderNewV5JS : {
            src: pathDev + 'order-new-v5/*.js',
            dest: pathProd + 'order-new-v5.min.js'
        },

        orderV3JS: {
            src: pathDev + 'order-v3/*.js',
            dest: pathProd + 'order-v3.min.js'
        },

        pandoraJS: {
            src: pathDev + 'pandora/*.js',
            dest: pathProd + 'pandora.min.js'
        },

        portsJS: {
            src: pathDev + 'ports/*.js',
            dest: pathProd + 'ports.min.js'
        },

        catalogJS: {
            src: pathDev + 'catalog/*.js',
            dest: pathProd + 'catalog.min.js'
        },

        productJS: {
            src: pathDev + 'product/*.js',
            dest: pathProd + 'product.min.js'
        },

        shopJS: {
            src: pathDev + 'shop/*.js',
            dest: pathProd + 'shop.min.js'
        },

        tchiboJS: {
            src: pathDev + 'tchibo/*.js',
            dest: pathProd + 'tchibo.min.js'
        },

        watch3dJS: {
            src: pathDev + 'watch3d/*.js',
            dest: pathProd + 'watch3d.min.js'
        },

        gameSlotsJs: {
            src: [ pathDev + 'game/slots/jquery.transit.js', pathDev + 'game/slots/slots.js'],
            dest: pathProd + 'game/slots.min.js'
        },

        serviceHaJS: {
            src: pathDev + 'service_ha/*.js',
            dest: pathProd + 'service_ha.min.js'
        }        
    }
};