module.exports = function( grunt ) {

	var
		jsRootPath = '../web/js/',
		jsDevPath = jsRootPath+'dev/',
		jsProdPath = jsRootPath+'prod/',

		/**
		 * Файлы и их порядок для jquery-plugins.js
		 * @type {Array}
		 */
		bigjqueryFiles = [
			// 'custom-form-elements.js',
			'jquery.lightbox_me.js',
			'jquery.scrollto.js',
			'jquery.placeholder.js',
			// 'prettyCheckboxes.js',
			'jquery.infinityCarousel.js',
			'typewriter.js',
			'jquery.maskedinput.js',
			'jquery.put_cursor_at_end.js',
			'goodsCounter.js',
			'jquery.elevatezoom.js',
			'jquery.animate-shadow.js',
			// 'customRadio.js',
			'customDropDown.js',
			'goodsSlider.js',
			'jquery-ui-1.10.3.custom.js'
		],

		/**
		 * Файлы и их порядок для library.js
		 * @type {Array}
		 */
		libraryFiles = [
			jsDevPath+'library/cloneObject.js',
			jsDevPath+'library/getKeysLength.js',
			jsDevPath+'library/JSON.js',
			jsDevPath+'library/pubSub.js',
			jsDevPath+'library/isTrueEmail.js',
			jsDevPath+'library/printPrice.js',
			jsDevPath+'library/doc_cookies.js',
			jsDevPath+'library/simple_templating.js',
			jsDevPath+'library/library.js',
			jsDevPath+'library/mapDriver.js',
			jsDevPath+'library/mapDriver-v2.js',
			jsDevPath+'library/black_box.js',
			jsDevPath+'library/formValidator.js',
			jsDevPath+'library/addParameterToUrl.js',
			jsDevPath+'library/blockScreen.js',
			jsDevPath+'library/*.js'
		];
	// end of vars
	

	grunt.initConfig({

		/**
		 * Документация к файлам
		 * 
		 * @link https://github.com/krampstudio/grunt-jsdoc-plugin
		 */
		jsdoc : {
			dist : {
				src: [jsDevPath+'**/*.js'], 
				options: {
					destination: '../web/js/docs/'
				}
			}
		},

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
			},

			// компиляция LESS
			compileNew: {
				options: {
					paths: ['../web/styles/']
				},
				files: {
					'../web/styles/global.css': ['../web/styles/global.less']
				}
			},
			// компиляция и минификация LESS
			compressNew: {
				options: {
					paths: ['../web/styles/'],
					compress: true
				},
				files: {
					'../web/styles/global.min.css': ['../web/styles/global.less']
				}
			},

			// компиляция LESS
			compileV2: {
				options: {
					paths: ['../web/v2/css/']
				},
				files: {
					'../web/v2/css/global.css': ['../web/v2/css/global.less']
				}
			},
			// компиляция и минификация LESS
			compressV2: {
				options: {
					paths: ['../web/v2/css/'],
					compress: true
				},
				files: {
					'../web/v2/css/global.min.css': ['../web/v2/css/global.less']
				}
			},
		},


		/**
		 * Отслеживание изменений файлов
		 *
		 * @link https://github.com/gruntjs/grunt-contrib-watch
		 */
		watch: {
			// less: {
			// 	files: ['../web/css/*.less', '../web/css/**/*.less', '../web/styles/*.less', '../web/styles/**/*.less', '../web/v2/css/*.less', '../web/v2/css/modules/**/*.less' ,'../web/v2/css/**/*.less'],
			// 	tasks: ['less'],
			// 	options: {
			// 		livereload: true,
			// 	},
			// },

			styles: {
				files: ['../web/css/*.less', '../web/css/**/*.less'],
				tasks: ['less:compile', 'less:compress']
			},

			stylesNew: {
				files: ['../web/styles/*.less', '../web/styles/**/*.less'],
				tasks: ['less:compileNew', 'less:compressNew']
			},

			stylesV2: {
				files: ['../web/v2/css/*.less', '../web/v2/css/modules/**/*.less'],
				tasks: ['less:compileV2', 'less:compressV2']
			},


			partnerScripts: {
				files: ['../web/js/partner/*.js'],
				tasks: ['concat:partnerScripts', 'jshint', 'uglify:partnerScripts', 'connect', 'qunit', 'exec:getVersion']
			},
			vendorScripts: {
				files: ['../web/js/vendor/*.js'],
				tasks: ['uglify:vendorScripts', 'jshint', 'exec:getVersion']
			},
			debugPanel: {
				files: [jsDevPath+'debug-panel/*.js'],
				tasks: ['concat:debugPanel', 'jshint']
			},
			cartJS:{
				files: [jsDevPath+'cart/*.js'],
				tasks: ['concat:cartJS', 'jshint', 'uglify:cartJS',  'connect', 'qunit', 'exec:getVersion']
			},
			commonJS:{
				files: [jsDevPath+'common/*.js'],
				tasks: ['concat:commonJS', 'jshint', 'uglify:commonJS',  'connect', 'qunit', 'exec:getVersion']
			},
			infopageJS:{
				files: [jsDevPath+'infopage/*.js'],
				tasks: ['concat:infopageJS', 'jshint', 'uglify:infopageJS',  'connect', 'qunit', 'exec:getVersion']
			},
			jqueryPluginsJS:{
				files: [jsDevPath+'jquery-plugins/*.js'],
				tasks: ['exec:compileBJ', 'jshint', 'exec:getVersion']
			},
			libraryJS:{
				files: [jsDevPath+'library/*.js'],
				tasks: ['concat:libraryJS', 'jshint', 'uglify:libraryJS',  'connect', 'qunit', 'exec:getVersion']
			},
			lkJS:{
				files: [jsDevPath+'lk/*.js'],
				tasks: ['concat:lkJS', 'jshint', 'uglify:lkJS',  'connect', 'qunit', 'exec:getVersion']
			},
			enterprizeJS:{
				files: [jsDevPath+'enterprize/*.js'],
				tasks: ['concat:enterprizeJS', 'jshint', 'uglify:enterprizeJS',  'connect', 'qunit', 'exec:getVersion']
			},
			mainJS:{
				files: [jsDevPath+'main/*.js'],
				tasks: ['concat:mainJS', 'jshint', 'uglify:mainJS',  'connect', 'qunit', 'exec:getVersion']
			},
			oneclickJS:{
				files: [jsDevPath+'oneclick/*.js'],
				tasks: ['concat:oneclickJS', 'jshint', 'uglify:oneclickJS',  'connect', 'qunit', 'exec:getVersion']
			},
			orderJS:{
				files: [jsDevPath+'order/*.js'],
				tasks: ['concat:orderJS', 'jshint', 'uglify:orderJS',  'connect', 'qunit', 'exec:getVersion']
			},
			orderNewV5JS:{
				files: [jsDevPath+'order-new-v5/*.js'],
				tasks: ['concat:orderNewV5JS', 'jshint', 'uglify:orderNewV5JS',  'connect', 'qunit', 'exec:getVersion']
			},
			pandoraJS:{
				files: [jsDevPath+'pandora/*.js'],
				tasks: ['concat:pandoraJS', 'jshint', 'uglify:pandoraJS',  'connect', 'qunit', 'exec:getVersion']
			},
			portsJS:{
				files: [jsDevPath+'ports/*.js'],
				tasks: ['concat:portsJS', 'jshint', 'uglify:portsJS',  'connect', 'qunit', 'exec:getVersion']
			},
			catalogJS:{
				files: [jsDevPath+'catalog/*.js'],
				tasks: ['concat:catalogJS', 'jshint', 'uglify:catalogJS',  'connect', 'qunit', 'exec:getVersion']
			},
			productJS:{
				files: [jsDevPath+'product/*.js'],
				tasks: ['concat:productJS', 'jshint', 'uglify:productJS',  'connect', 'qunit', 'exec:getVersion']
			},
			shopJS:{
				files: [jsDevPath+'shop/*.js'],
				tasks: ['concat:shopJS', 'jshint', 'uglify:shopJS',  'connect', 'qunit', 'exec:getVersion']
			},
			tchiboJS:{
				files: [jsDevPath+'tchibo/*.js'],
				tasks: ['concat:tchiboJS', 'jshint', 'uglify:tchiboJS',  'connect', 'qunit', 'exec:getVersion']
			},
			watch3dJS:{
				files: [jsDevPath+'watch3d/*.js'],
				tasks: ['concat:watch3dJS', 'jshint', 'uglify:watch3dJS',  'connect', 'qunit', 'exec:getVersion']
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
			debugPanel: {
				src: [jsDevPath+'debug-panel/*.js'],
				dest: jsProdPath+'debug-panel.js'
			},
			partnerScripts: {},
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
			libraryJS : {
				src: libraryFiles,
				dest: jsProdPath+'library.js'
			},
			lkJS : {
				src: [jsDevPath+'lk/*.js'],
				dest: jsProdPath+'lk.js'
			},
			enterprizeJS : {
				src: [jsDevPath+'enterprize/*.js'],
				dest: jsProdPath+'enterprize.js'
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
			orderNewV5JS : {
				src: [jsDevPath+'order-new-v5/*.js'],
				dest: jsProdPath+'order-new-v5.js'
			},
			pandoraJS : {
				src: [jsDevPath+'pandora/*.js'],
				dest: jsProdPath+'pandora.js'
			},
			portsJS : {
				src: [jsDevPath+'ports/*.js'],
				dest: jsProdPath+'ports.js'
			},
			catalogJS:{
				src: [jsDevPath+'catalog/*.js'],
				dest: jsProdPath+'catalog.js'
			},
			productJS : {
				src: [jsDevPath+'product/*.js'],
				dest: jsProdPath+'product.js'
			},
			shopJS : {
				src: [jsDevPath+'shop/*.js'],
				dest: jsProdPath+'shop.js'
			},
			tchiboJS : {
				src: [jsDevPath+'tchibo/*.js'],
				dest: jsProdPath+'tchibo.js'
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

			// debugPanel: {
			// 	files: {
			// 		'../web/js/prod/debug-panel.min.js': [jsDevPath+'debug-panel/*.js']
			// 	}
			// },

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
				src: '../web/js/prod/library.js',
        		dest: '../web/js/prod/library.min.js'
			},

			lkJS: {
				files: {
					'../web/js/prod/lk.min.js': [jsDevPath+'lk/*.js']
				}
			},

			enterprizeJS: {
				files: {
					'../web/js/prod/enterprize.min.js': [jsDevPath+'enterprize/*.js']
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

			orderNewV5JS : {
				files: {
					'../web/js/prod/order-new-v5.min.js': [jsDevPath+'order-new-v5/*.js']
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

			catalogJS: {
				files: {
					'../web/js/prod/catalog.min.js': [jsDevPath+'catalog/*.js']
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

			tchiboJS: {
				files: {
					'../web/js/prod/tchibo.min.js': [jsDevPath+'tchibo/*.js']
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
	grunt.loadNpmTasks('grunt-jsdoc');


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
	grunt.registerTask('default', ['less', 'concat', 'jshint', 'uglify', 'exec:compileBJ', 'connect', 'qunit', 'exec:getVersion']);
	// Генерация рандомных полигонов яндекс карт
	grunt.registerTask('ymaps', ['ymaps_generate']);
	// Тестирование JS, валидация JS
	grunt.registerTask('test', ['connect', 'qunit', 'jshint']);
};