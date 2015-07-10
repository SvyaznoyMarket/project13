module.exports = {

    // компиляция LESS
    compileLite: {
        options: {
            paths: ['css/'],
            sourceMapURL: '/public/css/global.css.map',
            sourceMapRootpath: '/frontend',
            sourceMap: true
        },
        files: {
            '../web/public/css/global.css': ['css/global.less']
        }
    },

    // компиляция и минификация LESS
    compressLite: {
        options: {
            paths: ['frontend/css/'],
            compress: true
        },
        files: {
            '../web/public/css/global.min.css': ['css/global.less']
        }
    }


};