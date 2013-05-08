module.exports = function(grunt) {

  var jsFiles = [
    "app.order.v4.js",
    "ports.js",
    "app.oneclick.js",
    "infopages.js",
    "library.js",
    "app.cart.js",
    // "bigjquery.js",
    "welcome.js",
    "main.js",
    "dash.js",
    "app.order.js",
    "app.product.comment.list.js",
    "app.product.js",
    "app.shop.js"
  ];

  grunt.initConfig({

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
      // bigjquery: {
      //   files: ['../web/js/bigjquery/*.js'],
      //   tasks: ['uglify','set_version'],
      // },
      scripts: {
        files: ['../web/js/*.js', '!../web/js/*.min.js', '!../web/js/combine.js'],
        tasks: ['uglify'],
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

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');

  grunt.registerTask('set_version', 'Set version for js files', function() {
    grunt.log.writeln('set version activate...');
    grunt.log.writeln('set version activate...done');
  });

  grunt.registerTask('css', ['less']);
  grunt.registerTask('js', ['uglify']);
  grunt.registerTask('default', ['less','uglify']);
};
