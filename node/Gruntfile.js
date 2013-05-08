module.exports = function(grunt) {

  var jsFiles = [
    "app.order.v4.js",
    "ports.js",
    "app.oneclick.js",
    "infopages.js",
    "library.js",
    "app.cart.js",
    "bigjquery.js",
    "welcome.js",
    "main.js",
    "dash.js",
    "app.order.js",
    "app.product.comment.list.js",
    "app.product.js",
    "app.shop.js"
  ];

  var bigjqueryFiles = [
    "custom-form-elements.js",
    "jquery.ui.core.js",
    "jquery.ui.widget.js",
    "jquery.ui.position.js",
    "jquery.ui.mouse.js",
    "jquery.ui.autocomplete.js",
    "jquery.ui.slider.js",
    "jquery.effects.core.js",
    "jquery.effects.transfer.js",
    "jquery.effects.blind.js",
    "jquery.lightbox_me.js",
    "jquery.mousewheel.min.js",
    "jquery.raty.js",
    "jquery.scrollto.js",
    "jquery.placeholder.js",
    "prettyCheckboxes.js",
    "jquery.infinityCarousel.js",
    "typewriter.js",
    "jquery.ui.touch-punch.js",
    "jquery.maskedinput.js",
    "jquery.put_cursor_at_end.js"
  ];

  grunt.initConfig({

    exec: {
      compileBJ:{
        command: function(){
          var compilerPath = 'closure-compiler/build/compiler.jar';
          var execCommand = 'java -jar '+compilerPath;
          for (var i=0, len=bigjqueryFiles.length; i<len; i++){
            execCommand += ' --js ../web/js/bigjquery/'+bigjqueryFiles[i];
          }
          execCommand += ' --js_output_file ../web/js/bigjquery.js';
          return execCommand;
        },
      }
    },

    less: {
      compile: {
        options: {
          paths: ['../web/css/']
        },
        files: {
          '../web/css/global.css': ['../web/css/global.less']
        }
      },
      compress: {
        options: {
          paths: ['../web/css/'],
          compress: true
        },
        files: {
          '../web/css/global.min.css': ['../web/css/global.less']
        }
      }
      // ,yuicompress: {
      //   options: {
      //     paths: ['../web/css/'],
      //     yuicompress: true
      //   },
      //   files: {
      //     '../web/css/global.yui.css': ['../web/css/global.less']
      //   }
      // }
    },

    watch: {
      less: {
        files: ['../web/css/*.less', '../web/css/**/*.less'],
        tasks: ['less'],
      },
      bigjquery: {
        files: ['../web/js/bigjquery/*.js'],
        tasks: ['exec','uglify','set_version'],
      },
      scripts: {
        files: ['../web/js/*.js', '!../web/js/*.min.js', '!../web/js/combine.js'],
        tasks: ['uglify','set_version'],
      },
    },

    uglify: {
      scripts: {
        options: {
          // report : 'gzip',
          // compress : true,
        },
        files: [
          {
            expand: true,
            cwd: '../web/js/',
            src: jsFiles,
            dest: '../web/js/',
            rename: function(destBase, destPath) {
              return destBase + destPath.replace('js', 'min.js');
            },
          },
        ],
      },
    }

  });

  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-exec');

  grunt.registerTask('set_version', 'Set compilation timestamp for js files', function() {
    var combine = 'window.filesWithVersion = {\n';
    var data = new Date();
    for(var i=0, len=jsFiles.length; i<len; i++) {
      grunt.log.writeln(jsFiles[i]+'...');
      combine += '"' + jsFiles[i] + '":' + Math.round(data.getTime()/1000) +',\n';
    }
    combine += '\n}';
    grunt.file.write('../web/js/combine.js' , combine);
    grunt.log.writeln('Done');
  });

  grunt.registerTask('css', ['less']);
  grunt.registerTask('js', ['exec','uglify','set_version']);
  grunt.registerTask('default', ['less','uglify','set_version']);
};
