;(function( global ) {
	var _gaq = global._gaq || [],

		startTime = new Date().getTime(),

		knockoutUrl = '',
		optimizelyUrl = '//cdn.optimizely.com/js/204544654.js',
		yandexMapUrl = '',
		directCreditUrl = 'http://direct-credit.ru/widget/api_script_utf.js',

		debug = false,
		pageConfig = $('#page-config').data('value'),
		templateType = document.body.getAttribute('data-template');
	// end of vars


		/**
		 * Версионность файлов и загрузка неминифицированных скриптов в debug режиме
		 * 
		 * @param	{String}	filename	Имя файла который нужно загрузить
		 * 
		 * @return	{String}				Новое имя файла
		 */
	var getWithVersion = function getWithVersion( filename ) {
			if ( typeof( global.release['version']) !== 'undefined' ) {
				if ( !debug ) {
					filename = filename.replace('js', 'min.js');
					filename += '?t=' + global.release['version'];
				}
				else {
					filename += '?t=' + global.release['version'];
				}
			} 

			return filename;
		},


		/**
		 * Логирование данных с клиента на сервер
		 * 
		 * https://wiki.enter.ru/pages/viewpage.action?pageId=11239960
		 * 
		 * @param  {Object} data данные отсылаемы на сервер
		 */
		logError = function logError( data ) {
			if ( data.ajaxUrl === '/log-json' ) {
				return;
			}

			if ( !pageConfig.jsonLog ) {
				return false;
			}

			data.pageID = data.pageID || document.body.getAttribute('data-id');

			$.ajax({
				type: 'POST',
				global: false,
				url: '/log-json',
				data: data
			});
		},

		/**
		 * Функция расширения нэймспейса проекта
		 * 
		 * @param 	{String}	ns_string	Строка отображающая глубину вложенности модуля
		 * 
		 * @return	{Object}				Созданный модуль в нэймспейсе
		 */
		extendApp = function extend( ns_string ) {
			window.ENTER = window.ENTER || {};

			var parts = ns_string.split('.'),
				parent = window.ENTER,
				pl, i;
			// end of vars

			if ( parts[0] == 'ENTER' ) {
				parts = parts.slice(1);
			}

			pl = parts.length;

			for ( i = 0; i < pl; i++ ) {
				//create a property if it doesnt exist  
				if ( typeof parent[parts[i]] === 'undefined' ) {
					parent[parts[i]] = {};
				}

				parent = parent[parts[i]];
			}

			return parent;  
		},

		/**
		 * Перехват вывода в консоль на продуктиве
		 */
		disableConsole = function disableConsole() {
			var original = global.console,
				console  = global.console = {},

			// список методов
				methods = ['assert', 'count', 'debug', 'dir', 'dirxml', 'error', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd', 'trace', 'warn'];

			// обход все элементов массива в обратном порядке
			for ( var i = methods.length; i-- ; ) {
				// обратите внимание, что обязательно необходима анонимная функция,
				// иначе любой метод нашей консоли всегда будет вызывать метод 'assert'
				(function ( methodName ) {
					// определяем новый метод
					console[methodName] = function () {
						return false;
					};
				})(methods[i]);
			}
		},

		/**
		 * LAB.js переопределяет document.write
		 * Для того чтобы метод document.write корректно работал, мы делаем для него замену
		 */
		newDocumentWrite = function newDocumentWrite() {
			document.write = function() {
				if( arguments[0].match( /<script(.?)* type=(\'|")text\/javascript(\'|")(.?)*><\/script>/ ) ) {
					$LAB.script( arguments[0].match( /src=(\'|")([^"\']?)+/ )[0].replace(/src=(\'|")/,'') );
				}
				else {
					document.writeln( arguments[0] );
				}
			}
		};
	// end of functions


	/**
	 * Перехват глобальных ошибок js
	 */
	global.onerror = function( msg, url, line ) {
		var preventErrorAlert = true;

		return preventErrorAlert;
	}


	/**
	 * Определение debug режима
	 */
	if ( document.body.getAttribute('data-debug') === 'true') {
		console.warn('Включен debug режим');

		debug = true;
	}
	else if ( document.location.search.match(/jsdbg/) ) {
		console.warn('Включен debug режим');

		debug = true;
	}

	/**
	 * Логирование открытие страницы и старта загрузки скриптов
	 */
	if ( pageConfig.jsonLog ) {
		logError({
					event: 'page_load'
				});
	}

	// Если продуктивный режим - заглушить консоль
	if ( !debug ) {
		disableConsole();
	}

	/**
	 * Создаем единый нэймспейс для проекта
	 * Добавляем модуль utils с функцией расширения нэймспейса
	 * Добавляем модуль config с основными конфигурациями клиентской стороны
	 */
	extendApp('ENTER.utils');
	global.ENTER.utils.extendApp = extendApp;
	global.ENTER.utils.logError = logError;

	extendApp('ENTER.config');
	global.ENTER.config.debug = debug;
	global.ENTER.config.startTime = startTime;
	global.ENTER.config.pageConfig = pageConfig;

	console.info('Создан единый namespace проекта');
	console.log(global.ENTER);

	if ( typeof $LAB === 'undefined' ) {
		throw new Error( 'Невозможно загрузить файлы JavaScript' );
	}

	/**
	 * Первоначальная настройка LAB
	 */
	$LAB.setGlobalDefaults({ AllowDuplicates: true, AlwaysPreserveOrder: true, UseLocalXHR: false, BasePath: '/js/prod/'})
		.queueScript('/js/combine.js')
		.queueWait(newDocumentWrite);


	knockoutUrl = ( debug ) ? 'http://knockoutjs.com/downloads/knockout-2.2.1.debug.js' : 'http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js';
	yandexMapUrl = ( debug ) ? 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU&mode=debug' : 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU&mode=release';


	/**
	 * Загрузка скриптов по шаблону
	 */
	loadScripts = {
		'default': function() {
			$LAB.queueWait( function() {
				$LAB.script('jquery-plugins.min.js')
					.script( getWithVersion('library.js') )
					.wait()
					.script( getWithVersion('common.js') );
			}).runQueue();
		},

		'main': function() {
			$LAB.queueWait( function() {
				$LAB.script('jquery-plugins.min.js')
					.script( getWithVersion('library.js') )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('main.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') );
			}).runQueue();
		},

		'tag-category': function() {
			$LAB.queueWait( function() {
				$LAB.script('jquery-plugins.min.js')
					.script( getWithVersion('library.js') )
					.wait()
					.script( getWithVersion('common.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') );
			}).runQueue();
		},

		'infopage': function() {
			$LAB.queueWait( function() {
				$LAB.script('jquery-plugins.min.js')
					.script( getWithVersion('library.js') )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('infopage.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') );
			}).runQueue();
		},

		'cart': function() {
			$LAB.queueWait( function() {
				$LAB.script('jquery-plugins.min.js')
					.script('JsHttpRequest.min.js')
					.script( getWithVersion('library.js') )
					.script( directCreditUrl )
					.wait()
					.script( getWithVersion('common.js') )
					.wait()
					.script( getWithVersion('cart.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') );
			}).runQueue();
		},

		'order': function() {
			$LAB.queueScript( yandexMapUrl )
				.queueScript( knockoutUrl )
				.queueWait( function() {
					$LAB.script('jquery-plugins.min.js')
						.script('JsHttpRequest.min.js')
						.script( getWithVersion('library.js') )
						.script( directCreditUrl )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('order-new.js') )
						.wait()
						.script( optimizelyUrl )
						.script('adfox.asyn.code.ver3.min.js')
						.wait()
						.script( getWithVersion('ports.js') );
				}).runQueue();
		},

		'order.new': function() {
			$LAB.queueScript( yandexMapUrl )
				.queueScript( knockoutUrl )
				.queueWait( function() {
					$LAB.script('jquery-plugins.min.js')
						.script('JsHttpRequest.min.js')
						.script( getWithVersion('library.js') )
						.script( directCreditUrl )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('order-new-v5.js') );
				}).runQueue();
		},

		'order_complete': function() {
			$LAB.queueWait( function() {
				$LAB.script('jquery-plugins.min.js')
					.script( getWithVersion('library.js') )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('order.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') );
			}).runQueue();
		},

		// неиспользуется
		'order_error': function() {
			$LAB.queueWait( function() {
				$LAB.script('jquery-plugins.min.js')
					.script( getWithVersion('library.js') )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('order.js') )
					.wait()
					.script( optimizelyUrl );
			}).runQueue();
		},

		'product_catalog': function() {
			$LAB.queueWait( function() {
				$LAB.script('jquery-plugins.min.js')
					.script( getWithVersion('library.js') )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('pandora.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') );
			}).runQueue();
		},

		'product_card': function() {
			$LAB.queueScript( knockoutUrl )
				.queueScript( yandexMapUrl )
				.queueWait( function() {
					$LAB.script('jquery-plugins.min.js')
						.script( getWithVersion('library.js') )
						.script('JsHttpRequest.min.js')			
						.script( directCreditUrl )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('product.js') )
						.script( getWithVersion('oneclick.js') )
						.wait()
						.script( optimizelyUrl )
						.script('adfox.asyn.code.ver3.min.js')
						.wait()
						.script( getWithVersion('ports.js') );
				}).runQueue();
		},

		'service': function() {
			$LAB.queueWait( function() {
				$LAB.script('jquery-plugins.min.js')
					.script( getWithVersion('library.js') )
					.wait()
					.script( getWithVersion('common.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') );
			}).runQueue();
		},

		'shop': function() {
			$LAB.queueScript( yandexMapUrl )
				.queueWait( function() {
					$LAB.script('jquery-plugins.min.js')
						.script( getWithVersion('library.js') )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('shop.js') )
						.wait()
						.script('tour.min.js')
						.wait()
						.script( optimizelyUrl );
			}).runQueue();
		},

		// неиспользуется
		'product_stock': function() {
			$LAB.queueScript( yandexMapUrl )
				.queueScript( knockoutUrl )
				.queueWait( function() {
					$LAB.script('jquery-plugins.min.js')
						.script( getWithVersion('library.js') )
						.wait()
						.script( getWithVersion('common.js') )
						.wait()
						.script( getWithVersion('product.js') )
						.script( getWithVersion('oneclick.js') )
						.wait()
						.script( optimizelyUrl )
						.script('adfox.asyn.code.ver3.min.js')
						.wait()
						.script( getWithVersion('ports.js') )
				}).runQueue();
		}
	}

	if ( loadScripts.hasOwnProperty(templateType) ) {
		console.log('Загрузка скриптов. Шаблон '+templateType);
		loadScripts[templateType]();
	}
	else {
		console.log('Шаблон '+templateType+' не найден. Загрузка стандартного набора скриптов');
		loadScripts['default']
	}

}(this));
