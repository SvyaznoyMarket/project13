/**
 * Run predefined tasks whenever watched file patterns are added, changed or deleted.
 *
 * @see {@link https://github.com/gruntjs/grunt-contrib-watch}
 */
module.exports = function () {

    return {

        stylesLite: {
            files: ['<%= paths.lessRoot %>/*.less', '<%= paths.lessRoot %>/**/*.less'],
            tasks: ['less:compileLite', 'less:compressLite']
        }

    }

};