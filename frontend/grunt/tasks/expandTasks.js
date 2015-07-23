module.exports = function( grunt ) {
    'use strict';

    var
        fs          = require('fs'),
        path        = require('path'),
        _           = require('lodash-node'),

        globalPaths = grunt.config.get('paths'),

        /**
         * Получение компонент из директории. Каждая директория является компонентом и ее имя интерпритируется как имя компнента
         *
         * @method      findComponents
         * @memberOf    module:expandTasks#
         * @private
         *
         * @param       {String}    componentsPath  Путь, где будет производится поиск компонент
         * @param       {Function}  callback        Функция обратного вызова. По завершению передает в нее первым параметром ошибку если есть, и массив найденных компонентов
         */
        findComponents = function( componentsPath, callback ) {
            var
                results = [];

            fs.readdir(componentsPath, function( err, list ) {
                var
                    pending;

                if ( err ) {
                    return callback(err);
                }

                pending = list.length;

                if ( !pending ) {
                    return callback(null, results);
                }

                list.forEach(function( componentName ) {
                    var
                        file = path.resolve(componentsPath, componentName);
                    // end of vars

                    fs.stat(file, function(err, stat) {
                        if ( stat && stat.isDirectory() ) {
                            results.push({
                                name: componentName,
                                path: file
                            });

                            if ( !--pending ) {
                                callback(null, results);
                            }
                        } else {
                            if ( !--pending ) {
                                callback(null, results);
                            }
                        }
                    });
                });
            });
        },

        /**
         * Регистрация компонента. Создание задач для компонента
         *
         * @method      registerComponent
         * @memberOf    module:expandTasks#
         */
        registerComponent = function( componentName, componentPath, callback ) {
            var
                isCore       = !!(componentName === 'core'),
                isPlugins    = !!(componentName === 'plugins'),

                prodPath     = globalPaths.jsProd,
                jsMinFile    = path.resolve(prodPath, componentName + '.min.js'),

                corePath     = globalPaths.core,

                uglify       = grunt.config.get('uglify'),
                findModules  = grunt.config.get('findModules'),
                watch        = grunt.config.get('watch'),

                jsDirs       = [];

            // grunt.log.writeln('Register component %s with path %s...', componentName, componentPath);

            jsDirs.push(
                componentPath + '/vendor/*.js',
                componentPath + '/**/*.js',
                componentPath + '/*.js'
            );

            if ( isCore ) {
                jsDirs.push(
                    '!' + componentPath + '/vendor-to-shim/*.js',
                    globalPaths.temp + '/*.js'
                );
            }

            /**
             * ===============
             * == MINIFY JS ==
             * ===============
             */
            uglify[componentName] = {
                files: [
                    {
                        src: jsDirs,
                        dest: jsMinFile
                    }
                ]
            };

            /**
             * =====================
             * == FIND JS MODULES ==
             * =====================
             */
            findModules[componentName] = {
                src: jsMinFile
            };

            /**
             * ===========
             * == WATCH ==
             * ===========
             */
            watch[componentName + 'js'] = {
                files: jsDirs,
                tasks: ['uglify:'+ componentName, 'findModules:'+ componentName]
            };

            if ( isCore ) {
                watch[componentName + 'js'].tasks.unshift('shim_modules');
                watch[componentName + 'js'].tasks.push('clean');
            }

            grunt.config.set('watch', watch);
            grunt.config.set('uglify', uglify);
            grunt.config.set('findModules', findModules);

            callback();
        },

        /**
         * Регистрация найденных компонентов
         *
         * @method      registerComponents
         * @memberOf    module:expandTasks#
         * @private
         *
         * @param       {Array}     components  Массив компонент
         * @param       {Function}  callback    Обратный вызов
         */
        registerComponents = function( components, callback ) {
            var
                pending = components.length,
                i;

            if ( !pending ) {
                callback();
            }

            components.forEach(function( componentOptions ) {
                registerComponent(componentOptions.name, componentOptions.path, function() {
                    if ( !--pending ) {
                        callback();
                    }
                });
            });
        },

        /**
         * Задача поиска и регистрации компонентов. Создание набора задач для компонент
         *
         * @method      expandTasks
         * @memberOf    module:expandTasks#
         */
        expandTasks = function() {
            var
                done           = this.async(),
                options        = this.options({}),

                corePath       = options.corePath,
                componentsPath = options.modulesPath,
                pluginsPath    = options.pluginsPath,

                components     = [{
                    name: 'core',
                    path: corePath
                }],

                registerComponentsComplete = function() {
                    grunt.log.oklns('Expand tasks complete');
                    done();
                },

                findComponentsComplete = function( error, result ) {
                    if ( error ) {
                        grunt.log.errorlns(err);
                        done();
                    }

                    components = components.concat(result);
                    registerComponents(components, registerComponentsComplete);
                };

            // grunt.log.writeln(JSON.stringify(options));
            findComponents(componentsPath, findComponentsComplete);
        };
    // end of vars

    grunt.registerMultiTask('expandTasks', 'Expand existing tasks', expandTasks);
}
