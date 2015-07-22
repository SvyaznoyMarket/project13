/**
 * Compile LESS files to CSS.
 *
 * @see  {@link https://github.com/gruntjs/grunt-contrib-less}
 */
module.exports = {

    // компиляция LESS
    compileLite: {
        options: {
            paths: ['<%= paths.lessRoot %>'],
            sourceMapURL: '/public/css/global.css.map',
            sourceMapRootpath: '/frontend',
            sourceMap: true
        },
        files: {
            '<%= paths.lessProd %>/global.css': ['<%= paths.lessRoot %>/global.less']
        }
    },

    // компиляция и минификация LESS
    compressLite: {
        options: {
            paths: ['<%= paths.lessRoot %>'],
            compress: true
        },
        files: {
            '<%= paths.lessProd %>/global.min.css': ['<%= paths.lessRoot %>/global.less']
        }
    }


};