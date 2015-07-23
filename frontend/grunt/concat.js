/**
 * Concatenate files.
 *
 * @see {@link https://github.com/gruntjs/grunt-contrib-concat}
 */
module.exports = function( grunt, options ) {
    return {
        options: {
            process: function( src, filepath ) {
                return '\n\n\n/**\n * === NEW FILE ===\n * filename: ' + filepath + '\n' + ' */\n' + src;
            },
            banner: '/**\n * Concat timestamp '+ grunt.template.today('HH:MM dd-mm-yyyy') + '\n' + ' */\n'
        }
    }
}
