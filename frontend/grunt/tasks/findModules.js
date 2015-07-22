module.exports = function( grunt ) {
    'use strict';

    var
        _ = require('lodash'),

        findModules = function() {
            var
                options = this.options({
                    punctuation: ' ',
                    separator: ', '
                }),

                outJsonFile = ( grunt.file.exists(options.outJSON) ) ? grunt.file.readJSON(options.outJSON) : {},

                re = /define\([^'"]*['"]([^"']*)['"][^'"[]*\[([^\]]*)/g,

                getSource = function( file ) {
                    // Concat specified files.
                    var
                        source = file.src.filter(function ( filepath ) {
                            // Warn on and remove invalid source files (if nonull was set).
                            if (!grunt.file.exists(filepath)) {
                                grunt.log.warn('Source file "' + filepath + '" not found.');
                                return false;
                            } else {
                                return true;
                            }
                        }).map(function ( filepath ) {
                            // Read file source.
                            return grunt.file.read(filepath);
                        }).join(grunt.util.normalizelf(options.separator));

                    return source;
                };
            // end of vars

            this.files.forEach(function ( file ) {
                var
                    src         = getSource(file),
                    filepath    = file.src[0],
                    filepathArr = filepath.split('/'),
                    filename    = filepathArr[filepathArr.length - 1],
                    found       = {},
                    out         = {};
                // end of vars

                src.replace(re, function( str, moduleName , deps, offset, s ) {
                    var
                        moduleDepsArray = deps.replace(/['"]*/g, '').split(',');
                    // end of vars

                    found[moduleName] = filename;
                    grunt.log.writeln('Found module %s', moduleName);
                });

                out = _.extend({}, outJsonFile, found);

                // Write the destination file.
                grunt.file.write(options.outJSON, JSON.stringify(out));

                // Print a success message.
                grunt.log.writeln('File "' + options.outJSON.cyan + '" created.');
            });
        };
    // end of vars

    grunt.registerMultiTask('findModules', 'Create YM Modules dependecies json file', findModules);
}
