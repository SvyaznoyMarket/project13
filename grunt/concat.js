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

    oneclickJS : {
        src: ['<%= pathDev %>oneclick/*.js'],
        dest: '<%= pathProd %>oneclick.js'
    },

    orderJS : {
        src: ['<%= pathDev %>order/*.js'],
        dest: '<%= pathProd %>order.js'
    },

    orderNewV5JS : {
        src: ['<%= pathDev %>order-new-v5/*.js'],
        dest: '<%= pathProd %>order-new-v5.js'
    },

    orderV3JS: {
        src: ['<%= pathDev %>order-v3/*.js'],
        dest: '<%= pathProd %>order-v3.js'
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

    productJS : {
        src: ['<%= pathDev %>product/*.js'],
        dest: '<%= pathProd %>product.js'
    },

    shopJS : {
        src: ['<%= pathDev %>shop/*.js'],
        dest: '<%= pathProd %>shop.js'
    },

    tchiboJS : {
        src: ['<%= pathDev %>tchibo/*.js'],
        dest: '<%= pathProd %>tchibo.js'
    },

    watch3dJS : {
        src: ['<%= pathDev %>watch3d/*.js'],
        dest: '<%= pathProd %>watch3d.js'
    }
};