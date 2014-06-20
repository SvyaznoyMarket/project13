module.exports = function( grunt ) {

	var
		jsRootPath = '../web/js/',
		jsDevPath = jsRootPath+'dev/',
		jsProdPath = jsRootPath+'prod/',
        jsV2RootPath = '../web/v2/js/',

		/**
		 * Файлы и их порядок для jquery-plugins.js
		 * @type {Array}
		 */
		jqueryPlugins = [
            jsDevPath+'jquery-plugins/jquery.lightbox_me.js',
            jsDevPath+'jquery-plugins/jquery.scrollto.js',
            jsDevPath+'jquery-plugins/jquery.placeholder.js',
            jsDevPath+'jquery-plugins/jquery.infinityCarousel.js',
            jsDevPath+'jquery-plugins/typewriter.js',
            jsDevPath+'jquery-plugins/jquery.maskedinput.js',
            jsDevPath+'jquery-plugins/jquery.put_cursor_at_end.js',
            jsDevPath+'jquery-plugins/goodsCounter.js',
            jsDevPath+'jquery-plugins/jquery.elevatezoom.js',
            jsDevPath+'jquery-plugins/jquery.animate-shadow.js',
            jsDevPath+'jquery-plugins/customDropDown.js',
            jsDevPath+'jquery-plugins/goodsSlider.js',
            jsDevPath+'jquery-plugins/jquery-ui-1.10.3.custom.js'
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
		 * @link http://github.com/krampstudio/grunt-jsdoc-plugin
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
		 * @link http://github.com/gruntjs/grunt-contrib-jshint
		 */
		jshint: { 
			withReporterShouldFail: {
				options: {
					reporter: 'checkstyle',
					reporterOutput: '../web/js/jsHintReport/report.xml',
					force: true
				},
				src: [jsDevPath+'**/*.js', 'Gruntfile.js']
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
		},


		/**
		 * QUnit тестирование JS файлов
		 *
		 * @link http://github.com/gruntjs/grunt-contrib-qunit
		 */
		qunit: {
			urls: {
				options: {
					urls: [
						'http://127.0.0.1:8000/js/tests/tests.htm'
					]
				}
			}
		},


		/**
		 * Локальный сервер для тестирования
		 *
		 * @link http://github.com/gruntjs/grunt-contrib-connect
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
		 * Компиляция LESS
		 *
		 * @link http://github.com/gruntjs/grunt-contrib-less
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
			}
		},


		/**
		 * Отслеживание изменений файлов
		 *
		 * @link http://github.com/gruntjs/grunt-contrib-watch
		 */
		watch: {

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
				tasks: ['jsmin-sourcemap:order_new_v5']
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
				separator: '\n \n \n/** \n * NEW FILE!!! \n' + ' */\n \n \n'
			},
			debugPanel: {
				src: [jsDevPath+'debug-panel/*.js'],
				dest: jsProdPath+'debug-panel.js'
			},
			jqueryPlugins: {
                src: jqueryPlugins,
                dest: jsProdPath+'jquery-plugins.js'
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
			}

		},

        /**
         * Source maps
         *
         * @link http://github.com/twolfson/grunt-jsmin-sourcemap
         */

        'jsmin-sourcemap': {
            catalog: {
                src: ['dev/catalog/*.js'],
                dest: 'prod/catalog.js',
                destMap: 'prod/catalog.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            ports: {
                src: ['dev/ports/*.js'],
                dest: 'prod/ports.js',
                destMap: 'prod/ports.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            debugPanel: {
                src: ['dev/debug-panel/*.js'],
                dest: 'prod/debug-panel.js',
                destMap: 'prod/debug-panel.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            cart: {
                src: ['dev/cart/*.js'],
                dest: 'prod/cart.js',
                destMap: 'prod/cart.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            common: {
                src: ['dev/common/*.js'],
                dest: 'prod/common.js',
                destMap: 'prod/common.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            infopage: {
                src: ['dev/infopage/*.js'],
                dest: 'prod/infopage.js',
                destMap: 'prod/infopage.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            library: {
                src: [
                    'dev/library/cloneObject.js',
                    'dev/library/getKeysLength.js',
                    'dev/library/JSON.js',
                    'dev/library/pubSub.js',
                    'dev/library/isTrueEmail.js',
                    'dev/library/printPrice.js',
                    'dev/library/doc_cookies.js',
                    'dev/library/simple_templating.js',
                    'dev/library/library.js',
                    'dev/library/mapDriver.js',
                    'dev/library/mapDriver-v2.js',
                    'dev/library/black_box.js',
                    'dev/library/formValidator.js',
                    'dev/library/addParameterToUrl.js',
                    'dev/library/blockScreen.js',
                    'dev/library/*.js'
                ],
                dest: 'prod/library.js',
                destMap: 'prod/library.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            jqueryPlugins: {
                src: [
                    'dev/jquery-plugins/jquery.lightbox_me.js',
                    'dev/jquery-plugins/jquery.scrollto.js',
                    'dev/jquery-plugins/jquery.placeholder.js',
                    'dev/jquery-plugins/jquery.infinityCarousel.js',
                    'dev/jquery-plugins/typewriter.js',
                    'dev/jquery-plugins/jquery.maskedinput.js',
                    'dev/jquery-plugins/jquery.put_cursor_at_end.js',
                    'dev/jquery-plugins/goodsCounter.js',
                    'dev/jquery-plugins/jquery.elevatezoom.js',
                    'dev/jquery-plugins/jquery.animate-shadow.js',
                    'dev/jquery-plugins/customDropDown.js',
                    'dev/jquery-plugins/goodsSlider.js',
                    'dev/jquery-plugins/jquery-ui-1.10.3.custom.js'
                ],
                dest: 'prod/jquery-plugins.min.js',
                destMap: 'prod/jquery-plugins.min.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            lk: {
                src: ['dev/lk/*.js'],
                dest: 'prod/lk.js',
                destMap: 'prod/lk.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            enterprize: {
                src: ['dev/enterprize/*.js'],
                dest: 'prod/enterprize.js',
                destMap: 'prod/enterprize.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            main: {
                src: ['dev/main/*.js'],
                dest: 'prod/main.js',
                destMap: 'prod/main.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            oneclick: {
                src: ['dev/oneclick/*.js'],
                dest: 'prod/oneclick.js',
                destMap: 'prod/oneclick.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            order: {
                src: ['dev/order/*.js'],
                dest: 'prod/order.js',
                destMap: 'prod/order.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            order_new_v5: {
                src: ['dev/order-new-v5/*.js'],
                dest: 'prod/order-new-v5.js',
                destMap: 'prod/order-new-v5.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            pandora: {
                src: ['dev/pandora/*.js'],
                dest: 'prod/pandora.js',
                destMap: 'prod/pandora.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            product: {
                src: ['dev/product/*.js'],
                dest: 'prod/product.js',
                destMap: 'prod/product.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            shop: {
                src: ['dev/shop/*.js'],
                dest: 'prod/shop.js',
                destMap: 'prod/shop.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            tchibo: {
                src: ['dev/tchibo/*.js'],
                dest: 'prod/tchibo.js',
                destMap: 'prod/tchibo.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            },
            watch3d: {
                src: ['dev/watch3d/*.js'],
                dest: 'prod/watch3d.js',
                destMap: 'prod/watch3d.js.map',
                srcRoot: '/js',
                cwd: '../web/js'
            }
        },


		/**
		 * Минификация js файлов
		 *
		 * @link http://github.com/gruntjs/grunt-contrib-uglify
		 */
		uglify: {
			options: {
				compress: {
					drop_console: true
				}
			},
			partnerScripts: {
				files: [
					{
						expand: true,
						cwd: '../web/js/partner/',
						src: ['*.js'],
						dest: '../web/js/prod/',
						rename: function(destBase, destPath) {
							return destBase + destPath.replace('js', 'min.js');
						}
					}
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
						}
					}
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

            jqueryPlugins: {
                options: {
                    mangle: {
                        except: ['jQuery']
                    }
                },
                src: '../web/js/prod/jquery-plugins.js',
                dest: '../web/js/prod/jquery-plugins.min.js'
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
			}
		},

        /**
         * Shim files as modules by template
         *
         * @see  {@link http://github.com/13rentgen/grunt-shim-modules}
         */
        shim_modules: {
            options: {
                template: './node_modules/grunt-shim-modules/ymodules-module_template.tpl',
                importNonFirst: true
            },

            jQuery: {
                src: jsV2RootPath + 'vendor/jquery-1.8.3.js',
                dest: jsV2RootPath + 'module/jquery.js',
                module_name: 'jQuery',
                desc: 'jQuery JavaScript Library',
                exports: '$'
            },

            mustache: {
                src: jsV2RootPath + 'vendor/mustache-0.8.2.js',
                dest: jsV2RootPath + 'module/mustache.js',
                module_name: 'mustache',
                desc: 'Logic-less {{mustache}} templates with JavaScript',
                exports: 'this.Mustache'
            },

            underscore: {
                src: jsV2RootPath + 'vendor/underscore-1.6.0.js',
                dest: jsV2RootPath + 'module/underscore.js',
                desc: 'Underscore.js 1.6.0',
                module_name: 'underscore',
                exports: 'this._'
            },

            lab: {
                src: jsV2RootPath + 'vendor/LAB-2.0.3.js',
                dest: jsV2RootPath + 'module/LAB.js',
                desc: 'JavaScript loader',
                module_name: 'LAB',
                exports: 'this.$LAB'
            }
        }

	});

	require('load-grunt-tasks')(grunt);

	/**
	 * Tasks
	 */
	// Компиляция LESS
	grunt.registerTask('css', ['less']);
	// Тестирование JS, валидация JS, компиляция bigjquery, минификация JS
	grunt.registerTask('js', ['concat', 'connect', 'qunit', 'jshint', 'uglify']);
	// Компиляция LESS, тестирование JS, валидация, минификация JS
	grunt.registerTask('default', ['less', 'concat', 'jshint', 'uglify', 'connect', 'qunit']);
	// Тестирование JS, валидация JS
	grunt.registerTask('test', ['connect', 'qunit', 'jshint']);
    // Source maps
    grunt.registerTask('sm', ['jsmin-sourcemap']);
};