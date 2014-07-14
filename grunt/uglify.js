module.exports = {

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
                dest: 'web/js/prod/',
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
                dest: 'web/js/prod/',
                rename: function(destBase, destPath) {
                    return destBase + destPath.replace('js', 'min.js');
                }
            }
        ]
    },

    loadJS: {
        files: {
            'web/js/loadjs.min.js': ['<%= pathRoot %>loadjs.js']
        }
    },

    cartJS: {
        files: {
            'web/js/prod/cart.min.js': ['<%= pathDev %>cart/*.js']
        }
    },

    commonJS: {
        files: {
            'web/js/prod/common.min.js': ['<%= pathDev %>common/*.js']
        }
    },

    infopageJS: {
        files: {
            'web/js/prod/infopage.min.js': ['<%= pathDev %>infopage/*.js']
        }
    },

    libraryJS: {
        src: 'web/js/prod/library.js',
            dest: 'web/js/prod/library.min.js'
    },

    jqueryPlugins: {
        options: {
            mangle: {
                except: ['jQuery']
            }
        },
        src: 'web/js/prod/jquery-plugins.js',
            dest: 'web/js/prod/jquery-plugins.min.js'
    },

    lkJS: {
        files: {
            'web/js/prod/lk.min.js': ['<%= pathDev %>lk/*.js']
        }
    },

    enterprizeJS: {
        files: {
            'web/js/prod/enterprize.min.js': ['<%= pathDev %>enterprize/*.js']
        }
    },

    mainJS: {
        files: {
            'web/js/prod/main.min.js': ['<%= pathDev %>main/*.js']
        }
    },

    oneclickJS: {
        files: {
            'web/js/prod/oneclick.min.js': ['<%= pathDev %>oneclick/*.js']
        }
    },

    orderJS: {
        files: {
            'web/js/prod/order.min.js': ['<%= pathDev %>order/*.js']
        }
    },

    orderNewV5JS : {
        files: {
            'web/js/prod/order-new-v5.min.js': ['<%= pathDev %>order-new-v5/*.js']
        }
    },

    pandoraJS: {
        files: {
            'web/js/prod/pandora.min.js': ['<%= pathDev %>pandora/*.js']
        }
    },

    portsJS: {
        files: {
            'web/js/prod/ports.min.js': ['<%= pathDev %>ports/*.js']
        }
    },

    catalogJS: {
        files: {
            'web/js/prod/catalog.min.js': ['<%= pathDev %>catalog/*.js']
        }
    },

    productJS: {
        files: {
            'web/js/prod/product.min.js': ['<%= pathDev %>product/*.js']
        }
    },

    shopJS: {
        files: {
            'web/js/prod/shop.min.js': ['<%= pathDev %>shop/*.js']
        }
    },

    tchiboJS: {
        files: {
            'web/js/prod/tchibo.min.js': ['<%= pathDev %>tchibo/*.js']
        }
    },

    watch3dJS: {
        files: {
            'web/js/prod/watch3d.min.js': ['<%= pathDev %>watch3d/*.js']
        }
    },
	
	gameSlotsJs: {
        files: {
            'web/js/game/slots.min.js': [
				'web/js/game/slots/jquery.transit.js',
				'web/js/game/slots/slots.js'
			]
        }
    }
};