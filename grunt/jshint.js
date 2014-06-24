/**
 * Валидация JS файлов
 *
 * @link http://github.com/gruntjs/grunt-contrib-jshint
 */

module.exports = {
    withReporterShouldFail: {
        options: {
            reporter: 'checkstyle',
                reporterOutput: 'web/js/jsHintReport/report.xml',
                force: true
        },
        src: ['<%= pathDev %>**/*.js', 'Gruntfile.js']
    },
    options: {
        '-W034': true,
            'curly': true,
            'eqeqeq': true,
            'immed': true,
            'latedef': true,
            'newcap': true,
            'noarg': true,
            'sub': true,
            'undef': true,
            'boss': true,
            'eqnull': true,
            'node': true,
            'browser': true,
            'funcscope': true,
            'quotmark': 'single',
            // 'onevar': true,
            'globals': {
            'jQuery': true,
                '$': true,
                'google': true,
                'ymaps': true,
                '_gaq': true,
                'escape': true,
                'unescape': true,
                'tmpl': true,
                '_kmq': true
        }
    }
};