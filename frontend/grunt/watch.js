module.exports = function () {

    return {

        stylesLite: {
            files: ['css/*.less', 'css/**/*.less'],
            tasks: ['less:compileLite', 'less:compressLite']
        },

        uglify: {
            files: ['js/*', 'js/**/*'],
            tasks: ['uglify:compress']
        }
    }

};