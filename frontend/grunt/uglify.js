/**
 * Minify files with UglifyJS
 *
 * @see {@link https://github.com/gruntjs/grunt-contrib-uglify}
 */
module.exports = function( grunt, options ) {
    return {

        options: {
            sourceMap: true,
            banner: options.methods.createBanner(),
            compress: {
                // drop_console: true
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

    }
}
