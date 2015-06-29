module.exports = function (grunt, options) {

    //console.log(options);
    var pathDev = options.pathDev,
        pathRoot = options.pathRoot;

    return {

        stylesLite: {
            files: ['frontend/css/*.less', 'frontend/css/**/*.less'],
            tasks: ['less:compileLite', 'less:compressLite']
        },

        styles: {
            files: ['web/css/*.less', 'web/css/**/*.less'],
            tasks: ['less:compile', 'less:compress']
        },

        stylesNew: {
            files: ['web/styles/*.less', 'web/styles/**/*.less'],
            tasks: ['less:compileNew', 'less:compressNew']
        },

        photoContest: {
            files: ['web/css/photoContest/*.less'],
            tasks: ['less:photoContestCompile', 'less:photoContestCompress']
        },

		gameSlots: {
            files: ['web/styles/game/slots/*.less'],
            tasks: ['less:gameSlotsCompile', 'less:gameSlotsCompress']
        },

        basketIE: {
            files: ['web/css/basket/ie10.less'],
            tasks: ['less:basketIECompile', 'less:basketIECompress']
        },

        partnerScripts: {
            files: [ pathRoot + 'partner/*.js'],
            tasks: ['uglify:partnerScripts']
        },

        vendorScripts: {
            files: [ pathRoot + 'vendor/*.js'],
            tasks: ['uglify:vendorScripts']
        },

        debugPanel: {
            files: [ pathDev + 'debug-panel/*.js'],
            tasks: ['concat:debugPanel']
        },

        cartJS:{
            files: [ pathDev + 'cart/*.js'],
            tasks: ['concat:cartJS', 'uglify:cartJS']
        },

		compareJS:{
            files: [ pathDev + 'compare/*.js'],
            tasks: ['concat:compareJS', 'uglify:compareJS']
        },

        commonJS:{
            files: [ pathDev + 'common/*.js'],
            tasks: ['concat:commonJS', 'uglify:commonJS', 'jsmin-sourcemap:common']
        },

        infopageJS:{
            files: [ pathDev + 'infopage/*.js'],
            tasks: ['concat:infopageJS', 'uglify:infopageJS']
        },

		jqueryPlugins:{
            files: [ pathDev + 'jquery-plugins/*.js'],
            tasks: ['concat:jqueryPlugins', 'uglify:jqueryPlugins', 'jsmin-sourcemap:jqueryPlugins']
        },

        libraryJS:{
            files: [ pathDev + 'library/*.js'],
            tasks: ['concat:libraryJS', 'uglify:libraryJS', 'jsmin-sourcemap:library']
        },

        lkJS:{
            files: [ pathDev + 'lk/*.js'],
            tasks: ['concat:lkJS', 'uglify:lkJS']
        },

        enterprizeJS:{
            files: [ pathDev + 'enterprize/*.js'],
            tasks: ['concat:enterprizeJS', 'uglify:enterprizeJS']
        },

        mainJS:{
            files: [ pathDev + 'main/*.js'],
            tasks: ['concat:mainJS', 'uglify:mainJS']
        },

        oneclickJS:{
            files: [ pathDev + 'oneclick/*.js'],
            tasks: ['concat:oneclickJS', 'uglify:oneclickJS']
        },

        orderJS:{
            files: [ pathDev + 'order/*.js'],
            tasks: ['concat:orderJS', 'uglify:orderJS']
        },

        orderV31ClickJS: {
            files: [ pathDev + 'order-v3-1click/*.js'],
            tasks: ['concat:orderV31ClickJS', 'uglify:orderV31ClickJS', 'jsmin-sourcemap:orderV31ClickJS']
        },

        orderV3newJS: {
            files: [ pathDev + 'order-v3-new/*.js'],
            tasks: ['concat:orderV3newJS', 'uglify:orderV3newJS', 'jsmin-sourcemap:orderV3newJS']
        },

        orderV3lifegiftJS: {
            files: [ pathDev + 'order-v3-lifegift/*.js'],
            tasks: ['concat:orderV3lifegiftJS', 'uglify:orderV3lifegiftJS', 'jsmin-sourcemap:orderV3lifegiftJS']
        },

        pandoraJS:{
            files: [ pathDev + 'pandora/*.js'],
            tasks: ['concat:pandoraJS', 'uglify:pandoraJS']
        },

        portsJS:{
            files: [ pathDev + 'ports/*.js'],
            tasks: ['concat:portsJS', 'uglify:portsJS']
        },

        catalogJS:{
            files: [ pathDev + 'catalog/*.js'],
            tasks: ['concat:catalogJS', 'uglify:catalogJS', 'jsmin-sourcemap:catalog']
        },

		giftJS:{
			files: [ pathDev + 'gift/*.js'],
			tasks: ['concat:giftJS', 'uglify:giftJS']
		},

        productJS:{
            files: [ pathDev + 'product/*.js'],
            tasks: ['concat:productJS', 'uglify:productJS']
        },

        shopJS:{
            files: [ pathDev + 'shop/*.js'],
            tasks: ['concat:shopJS', 'uglify:shopJS', 'jsmin-sourcemap:shop']
        },

        watch3dJS:{
            files: [ pathDev + 'watch3d/*.js'],
            tasks: ['concat:watch3dJS', 'uglify:watch3dJS']
        },

        serviceHaJS:{
            files: [ pathDev + 'service_ha/*.js'],
            tasks: ['concat:serviceHaJS', 'uglify:serviceHaJS']
        },

        loadJS:{
            files: [ pathRoot + 'loadjs.js'],
            tasks: ['uglify:loadJS']
        },

        supplier:{
            files: [ pathDev + 'supplier/*.js'],
            tasks: ['concat:supplier', 'uglify:supplier', 'jsmin-sourcemap:supplier']
        }
    }
};