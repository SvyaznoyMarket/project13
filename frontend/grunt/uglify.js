module.exports = {

    compress: {
        files: [{
            expand: true,
            cwd: 'js',
            src: ['layouts/*.js', 'modules/*.js'],
            dest: '../web/public/js'
        },{
            '../web/public/js/modules.js' : 'js/common/*.js',
            '../web/public/js/library.js' : 'js/library/*.js'
        }],
        options: {
            sourceMap: true
        }
    },

    compressPlugins: {
        files: [{
            expand: true,
            cwd: 'js',
            src: ['plugins/*.js'],
            dest: '../web/public/js'
        }],
        options: {
            sourceMap: true
        }
    }

};