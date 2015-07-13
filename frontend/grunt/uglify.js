module.exports = {

    compress: {
        files: [{
            expand: true,
            cwd: 'js',
            src: ['plugins/*.js', 'layouts/*.js', 'enter.modules/*.js'],
            dest: '../web/public/js'
        },{
            '../web/public/js/modules.js' : 'js/modules/*.js',
            '../web/public/js/library.js' : 'js/library/*.js'
        }],
        options: {
            sourceMap: true
        }
    }

};