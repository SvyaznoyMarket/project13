module.exports = {

    options: {

    },

    debugPanel: {
        src: ['<%= pathDev  %>debug-panel/*.js'],
        dest: '<%= pathProd  %>debug-panel.js'
    },

    jqueryPlugins: {
        src: '<%= gc.jqueryPlugins %>',
        dest: '<%= pathProd  %>jquery-plugins.js'
    },

    cartJS : {
        src: ['<%= pathDev %>cart/*.js'],
        dest: '<%= pathProd %>cart.js'
    },

	compareJS : {
		src: ['<%= pathDev %>compare/*.js'],
		dest: '<%= pathProd %>compare.js'
	},

	favoriteJS : {
		src: ['<%= pathDev %>favorite/*.js'],
		dest: '<%= pathProd %>favorite.js'
	},

    commonJS : {
        src: ['<%= pathDev %>common/*.js'],
        dest: '<%= pathProd %>common.js'
    },

    infopageJS : {
        src: ['<%= pathDev %>infopage/*.js'],
        dest: '<%= pathProd %>infopage.js'
    },

    libraryJS : {
        src: '<%= gc.libraryFiles %>',
        dest: '<%= pathProd %>library.js'
    },

    lkJS : {
        src: ['<%= pathDev %>lk/*.js'],
        dest: '<%= pathProd %>lk.js'
    },

    enterprizeJS : {
        src: ['<%= pathDev %>enterprize/*.js'],
        dest: '<%= pathProd %>enterprize.js'
    },

    mainJS : {
        src: ['<%= pathDev %>main/*.js'],
        dest: '<%= pathProd %>main.js'
    },

    orderV31ClickJS: {
        src: ['<%= pathDev %>order-v3-1click/*.js'],
        dest: '<%= pathProd %>order-v3-1click.js'
    },

    orderV3newJS: {
        src: ['<%= pathDev %>order-v3-new/*.js'],
        dest: '<%= pathProd %>order-v3-new.js'
    },

    pandoraJS : {
        src: ['<%= pathDev %>pandora/*.js'],
        dest: '<%= pathProd %>pandora.js'
    },

    portsJS : {
        src: ['<%= pathDev %>ports/*.js'],
        dest: '<%= pathProd %>ports.js'
    },

    catalogJS:{
        src: ['<%= pathDev %>catalog/*.js'],
        dest: '<%= pathProd %>catalog.js'
    },

	giftJS : {
		src: ['<%= pathDev %>gift/*.js'],
		dest: '<%= pathProd %>gift.js'
	},

    productJS : {
        src: ['<%= pathDev %>product/*.js'],
        dest: '<%= pathProd %>product.js'
    },

    shopJS : {
        src: ['<%= pathDev %>shop/*.js'],
        dest: '<%= pathProd %>shop.js'
    },

    watch3dJS : {
        src: ['<%= pathDev %>watch3d/*.js'],
        dest: '<%= pathProd %>watch3d.js'
    },

    supplier: {
        src: ['<%= pathDev %>supplier/*.js'],
        dest: '<%= pathProd %>supplier.js'
    },

    /* Adfox присылают уже минифицированный скрипт, который необходимо только положить в папку prod */
    adfox: {
        src: ['<%= pathDev %>adfox/adfox.js'],
        dest: '<%= pathProd %>adfox_lib_ff.min.js'
    }
};