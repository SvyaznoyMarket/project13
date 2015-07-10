module.exports = {

    // TODO исключение папок, которые сжимаются в один файл

    compress: {
        files: [{
            expand: true,
            cwd: 'js',
            src: ['plugins/*', 'layouts/*', 'vendor/*'],
            dest: '../web/public/js'
        },{
            '../web/public/js/modules.js' : 'js/modules/*',
            '../web/public/js/library.js' : 'js/library/*'
        }],
        options: {
            sourceMap: true
        }
    }

};