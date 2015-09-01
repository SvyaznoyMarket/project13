/**
 * Minify files with UglifyJS
 *
 * @see {@link https://github.com/gruntjs/grunt-contrib-uglify}
 */
module.exports = function( grunt, options ) {
    return {

        options: {
            sourceMap: !options.isProduction,
            banner: options.methods.createBanner(),
            compress: {
                drop_console: options.isProduction
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
                sourceMap: !options.isProduction
            }
        }

    }
}
