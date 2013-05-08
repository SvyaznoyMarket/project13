module.exports = function(grunt) {

  // var jsFiles = [
  //   "app.order.v4.js",
  //   "ports.js",
  //   "app.oneclick.js",
  //   "infopages.js",
  //   "library.js",
  //   "app.cart.js",
  //   "bigjquery.js",
  //   "welcome.js",
  //   "main.js",
  //   "dash.js",
  //   "app.order.js",
  //   "app.product.comment.list.js",
  //   "app.product.js",
  //   "app.shop.js"
  // ];

  // var minFilesList = "{"
  // for (var i=0, len = jsFiles.length; i<len; i++){
  //   minFilesList += "'../web/js/"+jsFiles[i].replace('js', 'min.js')+"' : ['../web/js/"+jsFiles[i]+"'],";
  // }
  // minFilesList += "}";
  // grunt.log.writeln(minFilesList)


  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

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
        tasks: ['concat', 'uglify','set_version'],
      },
      scripts: {
        files: ['../web/js/*.js', '!../web/js/*.min.js', '!../web/js/combine.js'],
        tasks: ['uglify','set_version'],
      },
    },

    concat: {
      options: {
        separator: ';',
        stripBanners: true,
        banner: '/*! <%= pkg.name %> - v.<%= pkg.version %> - ' +
        '<%= grunt.template.today("yyyy-mm-dd") %> */'
      },
      dist: {
        src: ['../web/js/bigjquery/*.js'],
        dest: '../web/js/bigjquery.js'
      }
    },

    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> - v.<%= pkg.version %> - ' +
        '<%= grunt.template.today("yyyy-mm-dd") %> */'
      },
      my_target: {
        files: {
          '../web/js/welcome.min.js': ['../web/js/welcome.js'],
          '../web/js/bigjquery.min.js': ['../web/js/bigjquery.js'],
          '../web/js/library.min.js': ['../web/js/library.js'],
          '../web/js/main.min.js': ['../web/js/main.js'],
          '../web/js/dash.min.js': ['../web/js/dash.js'],
          '../web/js/infopages.min.js': ['../web/js/infopages.js'],
          '../web/js/ports.min.js': ['../web/js/ports.js'],
          '../web/js/app.cart.min.js': ['../web/js/app.cart.js'],
          '../web/js/app.order.min.js': ['../web/js/app.order.js'],
          '../web/js/app.order.v4.min.js': ['../web/js/app.order.v4.js'],
          '../web/js/app.product.min.js': ['../web/js/app.product.js'],
          '../web/js/app.oneclick.min.js': ['../web/js/app.oneclick.js'],
          '../web/js/app.product.comment.list.min.js': ['../web/js/app.product.comment.list.js'],
          '../web/js/app.shop.min.js': ['../web/js/app.shop.js'],
        }
        // files: [
        //   {
        //     expand: true,     // Enable dynamic expansion.
        //     cwd: '../web/js/',      // Src matches are relative to this path.
        //     src: ['*.js', '!*.min.js','!combine.js','!knockout-2.1.0.js','!require.js','!loadjs.js'], // Actual pattern(s) to match.
        //     dest: '../web/js/',   // Destination path prefix.
        //     ext: '.min.js',   // Dest filepaths will have this extension.
        //   },
        // ],
      }
    }

  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');

  grunt.registerTask('set_version', 'Set version for js files', function() {
    // grunt.log.writeln('set version activate...');
    // grunt.log.writeln('set version activate...done');
  });

  grunt.registerTask('css', ['less']);
  grunt.registerTask('js', ['concat','uglify','set_version']);
  grunt.registerTask('default', ['less','concat','uglify','set_version']);

};
