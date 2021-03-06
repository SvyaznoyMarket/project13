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

		compareJS: {
            src: pathDev + 'compare/*.js',
            dest: pathProd + 'compare.min.js'
        },

        favoriteJS: {
            src: pathDev + 'favorite/*.js',
            dest: pathProd + 'favorite.min.js'
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

        mainJS: {
            src: pathDev + 'main/*.js',
            dest: pathProd + 'main.min.js'
        },

		orderV3newJS: {
            src: pathDev + 'order-v3-new/*.js',
            dest: pathProd + 'order-v3-new.min.js'
        },

        orderV31ClickJS: {
            src: pathDev + 'order-v3-1click/*.js',
            dest: pathProd + 'order-v3-1click.min.js'
        },

        portsJS: {
            src: pathDev + 'ports/*.js',
            dest: pathProd + 'ports.min.js'
        },

        catalogJS: {
            src: pathDev + 'catalog/*.js',
            dest: pathProd + 'catalog.min.js'
        },

		giftJS: {
			src: pathDev + 'gift/*.js',
			dest: pathProd + 'gift.min.js'
		},

        productJS: {
            src: pathDev + 'product/*.js',
            dest: pathProd + 'product.min.js'
        },

        shopJS: {
            src: pathDev + 'shop/*.js',
            dest: pathProd + 'shop.min.js'
        },

        watch3dJS: {
            src: pathDev + 'watch3d/*.js',
            dest: pathProd + 'watch3d.min.js'
        },

        supplier: {
            src: pathDev + 'supplier/*.js',
            dest: pathProd + 'supplier.min.js'
        }
    }
};