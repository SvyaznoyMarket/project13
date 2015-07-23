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
                drop_console: true
            }
        }

    }
}
