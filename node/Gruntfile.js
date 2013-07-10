module.exports = function(grunt) {

	var jsRootPath = '../web/js/';
	var jsDevPath = jsRootPath+'dev/';
	var jsProdPath = jsRootPath+'prod/';

	/**
	 * Файлы и их порядок для jquery-plugins.js
	 * @type {Array}
	 */
	var bigjqueryFiles = [
		"custom-form-elements.js",
		// "jquery.ui.core.js",
		// "jquery.ui.widget.js",
		// "jquery.ui.position.js",
		// "jquery.ui.mouse.js",
		// "jquery.ui.autocomplete.js",
		// "jquery.ui.slider.js",
		// "jquery.effects.core.js",
		"jquery.email_validate.js",
		// "jquery.effects.transfer.js",
		// "jquery.effects.blind.js",
		"jquery.lightbox_me.js",
		// "jquery.mousewheel.min.js",
		// "jquery.raty.js",
		"jquery.scrollto.js",
		"jquery.placeholder.js",
		"prettyCheckboxes.js",
		"jquery.infinityCarousel.js",
		"typewriter.js",
		// "jquery.ui.touch-punch.js",
		"jquery.maskedinput.js",
		"jquery.put_cursor_at_end.js",
		"goodsCounter.js",
		"jquery.elevatezoom.js",
		"customRadio.js",
		'jquery-ui-1.10.3.custom.js'
	];

	/**
	 * Файлы и их порядок для library.js
	 * @type {Array}
	 */
	var libraryFiles = [
		jsDevPath+'library/pageConfig.js',
		jsDevPath+'library/JSON.js',
		jsDevPath+'library/pubSub.js',
		jsDevPath+'library/isTrueEmail.js',
		jsDevPath+'library/printPrice.js',
		jsDevPath+'library/doc_cookies.js',
		jsDevPath+'library/simple_templating.js',
		jsDevPath+'library/library.js',
		jsDevPath+'library/mapDriver.js',
		jsDevPath+'library/black_box.js',
		jsDevPath+'library/addParameterToUrl.js',
		jsDevPath+'library/*.js'
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
				src: [jsDevPath+'**/*.js', 'Gruntfile.js'],
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
					"tmpl": true,
					"_kmq": true
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
						'http://127.0.0.1:8000/js/tests/tests.htm',
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
						execCommand += ' --js '+jsDevPath+'jquery-plugins/'+bigjqueryFiles[i];
					}
					execCommand += ' --js_output_file '+jsProdPath+'jquery-plugins.min.js';
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
				tasks: ['concat:partnerScripts','uglify:partnerScripts', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			vendorScripts: {
				files: ['../web/js/vendor/*.js'],
				tasks: ['concat:vendorScripts','uglify:vendorScripts', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			cartJS:{
				files: [jsDevPath+'cart/*.js'],
				tasks: ['concat:cartJS','uglify:cartJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			commonJS:{
				files: [jsDevPath+'common/*.js'],
				tasks: ['concat:commonJS','uglify:commonJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			infopageJS:{
				files: [jsDevPath+'infopage/*.js'],
				tasks: ['concat:infopageJS','uglify:infopageJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			jqueryPluginsJS:{
				files: [jsDevPath+'jquery-plugins/*.js'],
				tasks: ['exec:compileBJ', 'exec:getVersion'],
			},
			libraryJS:{
				files: [jsDevPath+'library/*.js'],
				tasks: ['concat:libraryJS','uglify:libraryJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			mainJS:{
				files: [jsDevPath+'main/*.js'],
				tasks: ['concat:mainJS','uglify:mainJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			oneclickJS:{
				files: [jsDevPath+'oneclick/*.js'],
				tasks: ['concat:oneclickJS','uglify:oneclickJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			orderJS:{
				files: [jsDevPath+'order/*.js'],
				tasks: ['concat:orderJS','uglify:orderJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			orderNewJS:{
				files: [jsDevPath+'order-new/*.js'],
				tasks: ['concat:orderNewJS','uglify:orderNewJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			pandoraJS:{
				files: [jsDevPath+'pandora/*.js'],
				tasks: ['concat:pandoraJS','uglify:pandoraJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			portsJS:{
				files: [jsDevPath+'ports/*.js'],
				tasks: ['concat:portsJS','uglify:portsJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			productJS:{
				files: [jsDevPath+'product/*.js'],
				tasks: ['concat:productJS','uglify:productJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			shopJS:{
				files: [jsDevPath+'shop/*.js'],
				tasks: ['concat:shopJS','uglify:shopJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			watch3dJS:{
				files: [jsDevPath+'watch3d/*.js'],
				tasks: ['concat:watch3dJS','uglify:watch3dJS', 'jshint', 'connect', 'qunit', 'exec:getVersion'],
			},
			loadJS:{
				files: [jsRootPath+'loadjs.js'],
				tasks: ['uglify:loadJS']
			}
		},


		/**
		 * Конкатенация файлов
		 */
		concat: {
			options: {
				separator: '\n \n \n/** \n * NEW FILE!!! \n' + ' */\n \n \n',
			},
			cartJS : {
				src: [jsDevPath+'cart/*.js'],
				dest: jsProdPath+'cart.js'
			},
			commonJS : {
				src: [jsDevPath+'common/*.js'],
				dest: jsProdPath+'common.js'
			},
			infopageJS : {
				src: [jsDevPath+'infopage/*.js'],
				dest: jsProdPath+'infopage.js'
			},
			// jqueryPluginsJS : {
			// 	src: [jsDevPath+'jquery-plugins/*.js'],
			// 	dest: jsProdPath+'jquery-plugins.js'
			// },
			libraryJS : {
				src: libraryFiles,
				dest: jsProdPath+'library.js'
			},
			mainJS : {
				src: [jsDevPath+'main/*.js'],
				dest: jsProdPath+'main.js'
			},
			oneclickJS : {
				src: [jsDevPath+'oneclick/*.js'],
				dest: jsProdPath+'oneclick.js'
			},
			orderJS : {
				src: [jsDevPath+'order/*.js'],
				dest: jsProdPath+'order.js'
			},
			orderNewJS : {
				src: [jsDevPath+'order-new/*.js'],
				dest: jsProdPath+'order-new.js'
			},
			pandoraJS : {
				src: [jsDevPath+'pandora/*.js'],
				dest: jsProdPath+'pandora.js'
			},
			portsJS : {
				src: [jsDevPath+'ports/*.js'],
				dest: jsProdPath+'ports.js'
			},
			productJS : {
				src: [jsDevPath+'product/*.js'],
				dest: jsProdPath+'product.js'
			},
			shopJS : {
				src: [jsDevPath+'shop/*.js'],
				dest: jsProdPath+'shop.js'
			},
			watch3dJS : {
				src: [jsDevPath+'watch3d/*.js'],
				dest: jsProdPath+'watch3d.js'
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

			loadJS: {
				files: {
					'../web/js/loadjs.min.js': [jsRootPath+'loadjs.js']
				}
			},

			cartJS: {
				files: {
					'../web/js/prod/cart.min.js': [jsDevPath+'cart/*.js']
				}
			},

			commonJS: {
				files: {
					'../web/js/prod/common.min.js': [jsDevPath+'common/*.js']
				}
			},

			infopageJS: {
				files: {
					'../web/js/prod/infopage.min.js': [jsDevPath+'infopage/*.js']
				}
			},

			libraryJS: {
				files: {
					'../web/js/prod/library.min.js': libraryFiles
				}
			},

			mainJS: {
				files: {
					'../web/js/prod/main.min.js': [jsDevPath+'main/*.js']
				}
			},

			oneclickJS: {
				files: {
					'../web/js/prod/oneclick.min.js': [jsDevPath+'oneclick/*.js']
				}
			},

			orderJS: {
				files: {
					'../web/js/prod/order.min.js': [jsDevPath+'order/*.js']
				}
			},

			orderNewJS: {
				files: {
					'../web/js/prod/order-new.min.js': [jsDevPath+'order-new/*.js']
				}
			},

			pandoraJS: {
				files: {
					'../web/js/prod/pandora.min.js': [jsDevPath+'pandora/*.js']
				}
			},

			portsJS: {
				files: {
					'../web/js/prod/ports.min.js': [jsDevPath+'ports/*.js']
				}
			},

			productJS: {
				files: {
					'../web/js/prod/product.min.js': [jsDevPath+'product/*.js']
				}
			},

			shopJS: {
				files: {
					'../web/js/prod/shop.min.js': [jsDevPath+'shop/*.js']
				}
			},

			watch3dJS: {
				files: {
					'../web/js/prod/watch3d.min.js': [jsDevPath+'watch3d/*.js']
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
	grunt.registerTask('js', ['concat', 'connect', 'qunit', 'jshint', 'uglify', 'exec:compileBJ', 'exec:getVersion']);
	// Компиляция LESS, тестирование JS, валидация, минификация JS, версионность
	grunt.registerTask('default', ['less', 'concat', 'connect', 'qunit', 'jshint', 'uglify', 'exec:compileBJ', 'exec:getVersion']);
	// Генерация рандомных полигонов яндекс карт
	grunt.registerTask('ymaps', ['ymaps_generate']);
	// Тестирование JS, валидация JS
	grunt.registerTask('test', ['connect', 'qunit', 'jshint']);
};
