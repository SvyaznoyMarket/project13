module.exports = function () {

    return {

        stylesLite: {
            files: ['css/*.less', 'css/**/*.less'],
            tasks: ['less:compileLite', 'less:compressLite']
        },

        uglify: {
            files: ['js/*', 'js/**/*', '!js/plugins/*'],
            tasks: ['uglify:compress']
        },

        uglifyPlugins: {
            files: ['js/plugins/*.js'],
            tasks: ['uglify:compressPlugins']
        }
    }

};