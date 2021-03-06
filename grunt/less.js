module.exports = {

    // компиляция LESS
    compile: {
        options: {
            paths: ['web/css/'],
            sourceMapBasepath: 'web/css/',
            sourceMap: true
        },
        files: {
            'web/css/global.css': ['web/css/global.less']
        }
    },

    // компиляция и минификация LESS
    compress: {
        options: {
            paths: ['web/css/'],
                compress: true
        },
        files: {
            'web/css/global.min.css': ['web/css/global.less']
        }
    },

    // компиляция LESS
    compileNew: {
        options: {
            paths: ['web/styles/'],
            sourceMapBasepath: 'web/styles/',
            sourceMap: true
        },
        files: {
            'web/styles/global.css': ['web/styles/global.less']
        }
    },
    // компиляция и минификация LESS
    compressNew: {
        options: {
            paths: ['web/styles/'],
                compress: true
        },
        files: {
            'web/styles/global.min.css': ['web/styles/global.less']
        }
    }
};