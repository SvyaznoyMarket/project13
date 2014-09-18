module.exports = function (grunt, options) {

    //console.log(options);
    var pathDev = options.pathDev,
        pathRoot = options.pathRoot;

    return {

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

        partnerScripts: {
            files: [ pathRoot + 'partner/*.js'],
            tasks: ['concat:partnerScripts', 'uglify:partnerScripts']
        },

        vendorScripts: {
            files: [ pathRoot + 'vendor/*.js'],
            tasks: ['uglify:vendorScripts', 'jshint']
        },

        debugPanel: {
            files: [ pathDev + 'debug-panel/*.js'],
            tasks: ['concat:debugPanel', 'jshint']
        },

        cartJS:{
            files: [ pathDev + 'cart/*.js'],
            tasks: ['concat:cartJS', 'uglify:cartJS']
        },

        commonJS:{
            files: [ pathDev + 'common/*.js'],
            tasks: ['concat:commonJS', 'uglify:commonJS']
        },

        infopageJS:{
            files: [ pathDev + 'infopage/*.js'],
            tasks: ['concat:infopageJS', 'uglify:infopageJS']
        },

        jqueryPluginsJS:{
            files: [ pathDev + 'jquery-plugins/*.js'],
            tasks: ['jsmin-sourcemap:jqueryPlugins']
        },

        libraryJS:{
            files: [ pathDev + 'library/*.js'],
            tasks: ['jsmin-sourcemap:library']
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

        orderV3JS: {
            files: [ pathDev + 'order-v3/*.js'],
            tasks: ['jsmin-sourcemap:orderV3JS']
        },

        orderNewV5JS:{
            files: [ pathDev + 'order-new-v5/*.js'],
            tasks: ['jsmin-sourcemap:order_new_v5']
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
            tasks: ['jsmin-sourcemap:catalog']
        },

        productJS:{
            files: [ pathDev + 'product/*.js'],
            tasks: ['concat:productJS', 'uglify:productJS']
        },

        shopJS:{
            files: [ pathDev + 'shop/*.js'],
            tasks: ['jsmin-sourcemap:shop']
        },

        tchiboJS:{
            files: [ pathDev + 'tchibo/*.js'],
            tasks: ['concat:tchiboJS', 'uglify:tchiboJS']
        },

        watch3dJS:{
            files: [ pathDev + 'watch3d/*.js'],
            tasks: ['concat:watch3dJS', 'uglify:watch3dJS']
        },
		
		gameSlotsJs:{
            files: [ pathDev + 'game/slots/jquery.transit.js', pathDev + 'game/slots/slots.js'],
            tasks: ['concat:gameSlotsJs', 'jshint', 'uglify:gameSlotsJs']
        },

        serviceHaJS:{
            files: [ pathDev + 'service_ha/*.js'],
            tasks: ['concat:serviceHaJS', 'uglify:serviceHaJS']
        },

        loadJS:{
            files: [ pathRoot + 'loadjs.js'],
            tasks: ['uglify:loadJS']
        }
    }
};