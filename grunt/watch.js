module.exports = {

    styles: {
        files: ['web/css/*.less', 'web/css/**/*.less'],
        tasks: ['less:compile', 'less:compress']
    },

    stylesNew: {
        files: ['web/styles/*.less', 'web/styles/**/*.less'],
        tasks: ['less:compileNew', 'less:compressNew']
    },

    stylesV2: {
        files: ['web/v2/css/*.less', 'web/v2/css/modules/**/*.less'],
        tasks: ['less:compileV2', 'less:compressV2']
    },
	
	photoContest: {
		files: ['web/css/photoContest/*.less'],
		tasks: ['less:photoContestCompile', 'less:photoContestCompress']
	},

    partnerScripts: {
        files: ['web/js/partner/*.js'],
        tasks: ['concat:partnerScripts', 'jshint', 'uglify:partnerScripts', 'connect', 'qunit', 'exec:getVersion']
    },
    
    vendorScripts: {
        files: ['web/js/vendor/*.js'],
        tasks: ['uglify:vendorScripts', 'jshint', 'exec:getVersion']
    },
    
    debugPanel: {
        files: ['<% pathDev %>debug-panel/*.js'],
        tasks: ['concat:debugPanel', 'jshint']
    },
    
    cartJS:{
        files: ['<% pathDev %>cart/*.js'],
        tasks: ['concat:cartJS', 'jshint', 'uglify:cartJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    commonJS:{
        files: ['<% pathDev %>common/*.js'],
        tasks: ['concat:commonJS', 'jshint', 'uglify:commonJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    infopageJS:{
        files: ['<% pathDev %>infopage/*.js'],
        tasks: ['concat:infopageJS', 'jshint', 'uglify:infopageJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    jqueryPluginsJS:{
        files: ['<% pathDev %>jquery-plugins/*.js'],
        tasks: ['jsmin-sourcemap:jqueryPlugins']
    },
    
    libraryJS:{
        files: ['<% pathDev %>library/*.js'],
        tasks: ['concat:libraryJS', 'jshint', 'uglify:libraryJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    lkJS:{
        files: ['<% pathDev %>lk/*.js'],
        tasks: ['concat:lkJS', 'jshint', 'uglify:lkJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    enterprizeJS:{
        files: ['<% pathDev %>enterprize/*.js'],
        tasks: ['concat:enterprizeJS', 'jshint', 'uglify:enterprizeJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    mainJS:{
        files: ['<% pathDev %>main/*.js'],
        tasks: ['concat:mainJS', 'jshint', 'uglify:mainJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    oneclickJS:{
        files: ['<% pathDev %>oneclick/*.js'],
        tasks: ['concat:oneclickJS', 'jshint', 'uglify:oneclickJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    orderJS:{
        files: ['<% pathDev %>order/*.js'],
        tasks: ['concat:orderJS', 'jshint', 'uglify:orderJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    orderNewV5JS:{
        files: ['<% pathDev %>order-new-v5/*.js'],
        tasks: ['jsmin-sourcemap:order_new_v5']
    },
    
    pandoraJS:{
        files: ['<% pathDev %>pandora/*.js'],
        tasks: ['concat:pandoraJS', 'jshint', 'uglify:pandoraJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    portsJS:{
        files: ['<% pathDev %>ports/*.js'],
        tasks: ['concat:portsJS', 'jshint', 'uglify:portsJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    catalogJS:{
        files: ['<% pathDev %>catalog/*.js'],
        tasks: ['jsmin-sourcemap:catalog']
    },
    
    productJS:{
        files: ['<% pathDev %>product/*.js'],
        tasks: ['concat:productJS', 'jshint', 'uglify:productJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    shopJS:{
        files: ['<% pathDev %>shop/*.js'],
        tasks: ['concat:shopJS', 'jshint', 'uglify:shopJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    tchiboJS:{
        files: ['<% pathDev %>tchibo/*.js'],
        tasks: ['concat:tchiboJS', 'jshint', 'uglify:tchiboJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    watch3dJS:{
        files: ['<% pathDev %>watch3d/*.js'],
        tasks: ['concat:watch3dJS', 'jshint', 'uglify:watch3dJS',  'connect', 'qunit', 'exec:getVersion']
    },
    
    loadJS:{
        files: ['<% pathRoot %>loadjs.js'],
        tasks: ['uglify:loadJS']
    }
};