module.exports = function(grunt) {

    var
        path  = require('path'),
        env   = process.env.NODE_ENV || 'development',
        paths = {};

    // Grunt tasks
    paths.config      = path.resolve('./grunt/');
    paths.tasks       = path.resolve('./grunt/tasks/');

    // Temp directory
    paths.temp        = path.resolve('./temp/');

    // Production root
    paths.prodRoot    = path.resolve('../web/public/');

    // JS
    paths.jsRoot      = path.resolve('js/');
    paths.jsCore      = path.resolve(paths.jsRoot, 'core/');
    paths.jsPlugins   = path.resolve(paths.jsRoot, 'plugins/');
    paths.jsModules   = path.resolve(paths.jsRoot, 'modules/');
    paths.jsProd      = path.resolve(paths.prodRoot, 'js/');

    // LESS
    paths.lessRoot    = path.resolve('css/');
    paths.lessProd    = path.resolve(paths.prodRoot, 'css/');

    // Modules dependencies JSON
    paths.modulesDeps = path.resolve('./modules.json');


    require('time-grunt')(grunt);
    require('load-grunt-config')(grunt,{
        init: true,
        jitGrunt: {
            staticMappings: {
                expandTasks: path.resolve(paths.tasks, 'expandTasks.js'),
                findModules: path.resolve(paths.tasks, 'findModules.js')
            }
        },
        data: {
            pkg: grunt.file.readJSON('package.json'),
            isProduction: !!( env === 'production' ),
            paths: paths,
            methods: {
                createBanner: function() {
                    return "/*\n * <%= pkg.title %> - " +
                        "<%= grunt.template.today('isoDate') %>\n" +
                        "<%= ' * ' + pkg.homepage + '\\n' %>" +
                        " * Copyright <%= grunt.template.today('yyyy') %> <%= pkg.author.name %>\n" +
                        " * From Russia with love <3\n*/\n";
                }
            }
        }
    });

    grunt.task.run('expandTasks');
};