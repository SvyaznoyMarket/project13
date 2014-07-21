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

        partnerScripts: {
            files: [ pathRoot + 'partner/*.js'],
            tasks: ['concat:partnerScripts', 'jshint', 'uglify:partnerScripts']
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
            tasks: ['concat:cartJS', 'jshint', 'uglify:cartJS']
        },

        commonJS:{
            files: [ pathDev + 'common/*.js'],
            tasks: ['concat:commonJS', 'jshint', 'uglify:commonJS']
        },

        infopageJS:{
            files: [ pathDev + 'infopage/*.js'],
            tasks: ['concat:infopageJS', 'jshint', 'uglify:infopageJS']
        },

        jqueryPluginsJS:{
            files: [ pathDev + 'jquery-plugins/*.js'],
            tasks: ['jsmin-sourcemap:jqueryPlugins']
        },

        libraryJS:{
            files: [ pathDev + 'library/*.js'],
            tasks: ['concat:libraryJS', 'jshint', 'uglify:libraryJS']
        },

        lkJS:{
            files: [ pathDev + 'lk/*.js'],
            tasks: ['concat:lkJS', 'jshint', 'uglify:lkJS']
        },

        enterprizeJS:{
            files: [ pathDev + 'enterprize/*.js'],
            tasks: ['concat:enterprizeJS', 'jshint', 'uglify:enterprizeJS']
        },

        mainJS:{
            files: [ pathDev + 'main/*.js'],
            tasks: ['concat:mainJS', 'jshint', 'uglify:mainJS']
        },

        oneclickJS:{
            files: [ pathDev + 'oneclick/*.js'],
            tasks: ['concat:oneclickJS', 'jshint', 'uglify:oneclickJS']
        },

        orderJS:{
            files: [ pathDev + 'order/*.js'],
            tasks: ['concat:orderJS', 'jshint', 'uglify:orderJS']
        },

        orderV3JS: {
            files: [ pathDev + 'order-v3/*.js'],
            tasks: ['concat:orderV3JS', 'jshint', 'uglify:orderV3JS']
        },

        orderNewV5JS:{
            files: [ pathDev + 'order-new-v5/*.js'],
            tasks: ['jsmin-sourcemap:order_new_v5']
        },

        pandoraJS:{
            files: [ pathDev + 'pandora/*.js'],
            tasks: ['concat:pandoraJS', 'jshint', 'uglify:pandoraJS']
        },

        portsJS:{
            files: [ pathDev + 'ports/*.js'],
            tasks: ['concat:portsJS', 'jshint', 'uglify:portsJS']
        },

        catalogJS:{
            files: [ pathDev + 'catalog/*.js'],
            tasks: ['jsmin-sourcemap:catalog']
        },

        productJS:{
            files: [ pathDev + 'product/*.js'],
            tasks: ['concat:productJS', 'jshint', 'uglify:productJS']
        },

        shopJS:{
            files: [ pathDev + 'shop/*.js'],
            tasks: ['concat:shopJS', 'jshint', 'uglify:shopJS']
        },

        tchiboJS:{
            files: [ pathDev + 'tchibo/*.js'],
            tasks: ['concat:tchiboJS', 'jshint', 'uglify:tchiboJS']
        },

        watch3dJS:{
            files: [ pathDev + 'watch3d/*.js'],
            tasks: ['concat:watch3dJS', 'jshint', 'uglify:watch3dJS']
        },

        loadJS:{
            files: [ pathRoot + 'loadjs.js'],
            tasks: ['uglify:loadJS']
        }
    }
};