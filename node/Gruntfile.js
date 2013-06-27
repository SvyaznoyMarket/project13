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
    "app.shop.js",
    "DAnimFramePlayer.js",
    "KupeConstructorScript.js",
    // "three.js",
    "jewel.category.leaf.js"
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
		// "jquery.effects.transfer.js",
		// "jquery.effects.blind.js",
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

		/**
		 * Валидация JS файлов
		 *
		 * @link https://github.com/gruntjs/grunt-contrib-jshint
		 */
		jshint: { 
			withReporterShouldFail: {
				options: {
					reporter: 'checkstyle',
					reporterOutput: '../web/js/jsHintReport/report.xml',
					force: true,
				},
				src: ['../web/js/dev/**/*.js', 'Gruntfile.js'],
			},
			options: {
				"-W034": true,
				"curly": true,
				"eqeqeq": true,
				"immed": true,
				"latedef": true,
				"newcap": true,
				"noarg": true,
				"sub": true,
				"undef": true,
				"boss": true,
				"eqnull": true,
				"node": true,
				"browser": true,
				"globals": {
					"jQuery": true,
					"$": true,
					"google": true,
					"ymaps": true,
					"_gaq": true,
					"escape": true,
					"unescape": true,
					"tmpl": true
				},
			},
		},


		/**
		 * QUnit тестирование JS файлов
		 *
		 * @link https://github.com/gruntjs/grunt-contrib-qunit
		 */
		qunit: {
			urls: {
				options: {
					urls: [
						'http://localhost:8000/js/tests/tests.htm',
					]
				},
			}
		},


		/**
		 * Локальный сервер для тестирования
		 *
		 * @link https://github.com/gruntjs/grunt-contrib-connect
		 */
		connect: {
			server: {
				options: {
					port: 8000,
					base: '../web/'
				}
			}
		},


		/**
		 * Выполнение BASH команд
		 * 
		 * @link https://github.com/jharding/grunt-exec
		 */
		exec: {
			// компиляция bigjquery.js с помощью google closure compiler
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
			},
			// текущая версия в combine.js
			getVersion: {
				stdout: true,
				stderr: true,
				command: function(){
					grunt.log.writeln('getVersion ');
					var execCommand = 'filename="../web/js/combine.js"; rm ../web/js/combine.js; printf \'window.release = { "version":"\'>> $filename \r; res=$(git describe --always --tag); printf $res >> $filename \r; printf \'"}\'>> $filename \r;';
					return execCommand;
				}
			}
		},

		/**
		 * Компиляция LESS
		 *
		 * @link https://github.com/gruntjs/grunt-contrib-less 
		 */
		less: {
			// компиляция LESS
			compile: {
				options: {
					paths: ['../web/css/']
				},
				files: {
					'../web/css/global.css': ['../web/css/global.less']
				}
			},
			// компиляция и минификация LESS
			compress: {
				options: {
					paths: ['../web/css/'],
					compress: true
				},
				files: {
					'../web/css/global.min.css': ['../web/css/global.less']
				}
			}
		},


		/**
		 * Отслеживание изменений файлов
		 *
		 * @link https://github.com/gruntjs/grunt-contrib-watch
		 */
		watch: {
			less: {
				files: ['../web/css/*.less', '../web/css/**/*.less'],
				tasks: ['less'],
			},
			partnerScripts: {
				files: ['../web/js/partner/*.js'],
				tasks: ['uglify:partnerScripts', 'connect', 'qunit', 'exec:getVersion'],
			},
			vendorScripts: {
				files: ['../web/js/vendor/*.js'],
				tasks: ['uglify:vendorScripts', 'connect', 'qunit', 'exec:getVersion'],
			},
			cartJS:{
				files: ['../web/js/dev/cart/*.js'],
				tasks: ['uglify:cartJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			commonJS:{
				files: ['../web/js/dev/common/*.js'],
				tasks: ['uglify:commonJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			infopageJS:{
				files: ['../web/js/dev/infopage/*.js'],
				tasks: ['uglify:infopageJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			jqueryPluginsJS:{
				files: ['../web/js/dev/jquery-plugins/*.js'],
				tasks: ['uglify:jqueryPluginsJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			libraryJS:{
				files: ['../web/js/dev/library/*.js'],
				tasks: ['uglify:libraryJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			mainJS:{
				files: ['../web/js/dev/main/*.js'],
				tasks: ['uglify:mainJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			oneclickJS:{
				files: ['../web/js/dev/oneclick/*.js'],
				tasks: ['uglify:oneclickJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			orderJS:{
				files: ['../web/js/dev/order/*.js'],
				tasks: ['uglify:orderJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			orderNewJS:{
				files: ['../web/js/dev/order-new/*.js'],
				tasks: ['uglify:orderNewJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			pandoraJS:{
				files: ['../web/js/dev/pandora/*.js'],
				tasks: ['uglify:pandoraJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			portsJS:{
				files: ['../web/js/dev/ports/*.js'],
				tasks: ['uglify:portsJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			productJS:{
				files: ['../web/js/dev/product/*.js'],
				tasks: ['uglify:productJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			shopJS:{
				files: ['../web/js/dev/shop/*.js'],
				tasks: ['uglify:shopJS', 'connect', 'qunit', 'exec:getVersion'],
			},
			watch3dJS:{
				files: ['../web/js/dev/watch3d/*.js'],
				tasks: ['uglify:watch3dJS', 'connect', 'qunit', 'exec:getVersion'],
			},
		},


		/**
		 * Конкатенация файлов
		 */
		concat: {
			options: {
				separator: '\n \n \n/** \n * NEW FILE!!! \n' + ' */\n \n \n',
			},
			cartJS : {
				src: ['../web/js/dev/cart/*.js'],
				dest: '../web/js/prod/cart.js'
			},
			commonJS : {
				src: ['../web/js/dev/common/*.js'],
				dest: '../web/js/prod/common.js'
			},
			infopageJS : {
				src: ['../web/js/dev/infopage/*.js'],
				dest: '../web/js/prod/infopage.js'
			},
			jqueryPluginsJS : {
				src: ['../web/js/dev/jquery-plugins/*.js'],
				dest: '../web/js/prod/jquery-plugins.js'
			},
			libraryJS : {
				src: ['../web/js/dev/library/*.js'],
				dest: '../web/js/prod/library.js'
			},
			mainJS : {
				src: ['../web/js/dev/main/*.js'],
				dest: '../web/js/prod/main.js'
			},
			oneclickJS : {
				src: ['../web/js/dev/oneclick/*.js'],
				dest: '../web/js/prod/oneclick.js'
			},
			orderJS : {
				src: ['../web/js/dev/order/*.js'],
				dest: '../web/js/prod/order.js'
			},
			orderNewJS : {
				src: ['../web/js/dev/order-new/*.js'],
				dest: '../web/js/prod/order-new.js'
			},
			pandoraJS : {
				src: ['../web/js/dev/pandora/*.js'],
				dest: '../web/js/prod/pandora.js'
			},
			portsJS : {
				src: ['../web/js/dev/ports/*.js'],
				dest: '../web/js/prod/ports.js'
			},
			productJS : {
				src: ['../web/js/dev/product/*.js'],
				dest: '../web/js/prod/product.js'
			},
			shopJS : {
				src: ['../web/js/dev/shop/*.js'],
				dest: '../web/js/prod/shop.js'
			},
			watch3dJS : {
				src: ['../web/js/dev/watch3d/*.js'],
				dest: '../web/js/prod/watch3d.js'
			},

		},


		/**
		 * Минификация js файлов
		 *
		 * @link https://github.com/gruntjs/grunt-contrib-uglify
		 */
		uglify: {
			partnerScripts: {
				files: [
					{
						expand: true,
						cwd: '../web/js/partner/',
						src: ['*.js'],
						dest: '../web/js/prod/',
						rename: function(destBase, destPath) {
							return destBase + destPath.replace('js', 'min.js');
						},
					},
				]
			},

			vendorScripts: {
				files: [
					{
						expand: true,
						cwd: '../web/js/vendor/',
						src: ['*.js'],
						dest: '../web/js/prod/',
						rename: function(destBase, destPath) {
							return destBase + destPath.replace('js', 'min.js');
						},
					},
				]
			},

			cartJS: {
				files: {
					'../web/js/prod/cart.min.js': ['../web/js/dev/cart/*.js']
				}
			},

			commonJS: {
				files: {
					'../web/js/prod/common.min.js': ['../web/js/dev/common/*.js']
				}
			},

			infopageJS: {
				files: {
					'../web/js/prod/infopage.min.js': ['../web/js/dev/infopage/*.js']
				}
			},

			jqueryPluginsJS: {
				files: {
					'../web/js/prod/jquery-plugins.min.js': ['../web/js/dev/jquery-plugins/*.js']
				}
			},

			libraryJS: {
				files: {
					'../web/js/prod/library.min.js': ['../web/js/dev/library/*.js']
				}
			},

			mainJS: {
				files: {
					'../web/js/prod/main.min.js': ['../web/js/dev/main/*.js']
				}
			},

			oneclickJS: {
				files: {
					'../web/js/prod/oneclick.min.js': ['../web/js/dev/oneclick/*.js']
				}
			},

			orderJS: {
				files: {
					'../web/js/prod/order.min.js': ['../web/js/dev/order/*.js']
				}
			},

			orderNewJS: {
				files: {
					'../web/js/prod/order-new.min.js': ['../web/js/dev/order-new/*.js']
				}
			},

			pandoraJS: {
				files: {
					'../web/js/prod/pandora.min.js': ['../web/js/dev/pandora/*.js']
				}
			},

			portsJS: {
				files: {
					'../web/js/prod/ports.min.js': ['../web/js/dev/ports/*.js']
				}
			},

			productJS: {
				files: {
					'../web/js/prod/product.min.js': ['../web/js/dev/product/*.js']
				}
			},

			shopJS: {
				files: {
					'../web/js/prod/shop.min.js': ['../web/js/dev/shop/*.js']
				}
			},

			watch3dJS: {
				files: {
					'../web/js/prod/watch3d.min.js': ['../web/js/dev/watch3d/*.js']
				}
			},
		}

	});

	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-qunit');
	grunt.loadNpmTasks('grunt-contrib-connect');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-exec');


	/**
	 * Генерация xml файла с полигонами для яндекс карт
	 */
	grunt.registerTask('ymaps_generate', 'Generate Ymap XML', function(){
		// LONGITUDE -180 to + 180
		// LATITUDE -90 to +90
		function generateRandom(from, to, fixed) {
				return (Math.random() * (to - from) + from).toFixed(fixed) * 1;
		}
		
		var count = 2000;
		grunt.log.writeln('Generate '+count+' random polygons');
		var outXML = '<ymaps:ymaps xmlns:ymaps="http://maps.yandex.ru/ymaps/1.x" xmlns:repr="http://maps.yandex.ru/representation/1.x" xmlns:gml="http://www.opengis.net/gml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://maps.yandex.ru/schemas/ymaps/1.x/ymaps.xsd"><ymaps:GeoObjectCollection><gml:featureMember>';
		for (var i=0; i<count; i++){
			grunt.log.writeln('Generating '+i+' polygon...');
			outXML += '<ymaps:GeoObject>'+
							'<gml:name>Многоугольник '+i+'</gml:name>'+
							'<gml:description>'+i+'ый многоугольник из '+count+'</gml:description>'+
							'<gml:Polygon>'+
									'<gml:exterior>'+
											'<gml:LinearRing>'+
													'<gml:posList>'+generateRandom(-180, 180, 3)+' '+generateRandom(-90, 90, 3)+' '+generateRandom(-180, 180, 3)+' '+generateRandom(-90, 90, 3)+' '+generateRandom(-180, 180, 3)+' '+generateRandom(-90, 90, 3)+' '+generateRandom(-180, 180, 3)+' '+generateRandom(-90, 90, 3)+'</gml:posList>'+
											'</gml:LinearRing>'+
									'</gml:exterior>'+
							'</gml:Polygon>'+
					'</ymaps:GeoObject>';
		}
		outXML += '</gml:featureMember></ymaps:GeoObjectCollection></ymaps:ymaps>';
		grunt.file.write('../web/js/tests/polygons'+count+'.xml' , outXML);
		grunt.log.writeln('Done');
	});


	/**
	 * Tasks
	 */
	// Компиляция LESS
	grunt.registerTask('css', ['less']);
	// Тестирование JS, валидация JS, компиляция bigjquery, минификация JS, версионность
	grunt.registerTask('js', [ 'connect', 'qunit', 'jshint',  'exec:compileBJ', 'uglify', 'exec:getVersion']);
	// Компиляция LESS, тестирование JS, валидация, минификация JS, версионность
	grunt.registerTask('default', ['less', 'connect', 'qunit', 'jshint', 'uglify', 'exec:getVersion']);
	// Генерация рандомных полигонов яндекс карт
	grunt.registerTask('ymaps', ['ymaps_generate']);
	// Тестирование JS, валидация JS
	grunt.registerTask('test', ['connect', 'qunit', 'jshint']);
};
