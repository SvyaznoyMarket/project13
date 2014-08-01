/**
 * Улучшения консоли
 *
 * https://github.com/theshock/console-cap
 */
;(function ( console ) {
	var
		i,
		global  = this,
		fnProto = Function.prototype,
		fnApply = fnProto.apply,
		fnBind  = fnProto.bind,
		bind    = function ( context, fn ) {
			return fnBind ?
				fnBind.call( fn, context ) :
				function () {
					return fnApply.call( fn, context, arguments );
				};
		},
		methods = 'assert count debug dir dirxml error group groupCollapsed groupEnd info log markTimeline profile profileEnd table time timeEnd trace warn'.split(' '),
		emptyFn = function(){},
		empty   = {},
		timeCounters;

	for ( i = methods.length; i-- ; ) empty[methods[i]] = emptyFn;

	if ( console ) {
		
		if ( !console.time ) {
			console.timeCounters = timeCounters = {};
			
			console.time = function( name, reset ) {
				if ( name ) {
					var time = +new Date, key = 'KEY' + name.toString();
					if (reset || !timeCounters[key]) timeCounters[key] = time;
				}
			};

			console.timeEnd = function( name ) {
				var diff,
					time = +new Date,
					key = 'KEY' + name.toString(),
					timeCounter = timeCounters[key];
				
				if ( timeCounter ) {
					diff  = time - timeCounter;
					console.info( name + ': ' + diff + 'ms' );
					delete timeCounters[key];
				}
				
				return diff;
			};
		}
		
		for ( i = methods.length; i-- ; ) {
			console[methods[i]] = methods[i] in console ?
				bind(console, console[methods[i]]) : emptyFn;
		}

		console.disable = function () {
			global.console = empty;
		};

		empty.enable  = function () {
			global.console = console;
		};
		
		empty.disable = console.enable = emptyFn;
		
	}
	else {
		console = global.console = empty;
		console.disable = console.enable = emptyFn;
	}
})( typeof console === 'undefined' ? null : console );


(function() {
	var global = window,
		jsStartTime = new Date().getTime(),

		knockoutUrl = '',
		optimizelyUrl = '//cdn.optimizely.com/js/204544654.js',
		yandexMapUrl = '',
		mustacheUrl = '',
		historyUrl = '',
		kladr = '',
		directCreditUrl = 'http://direct-credit.ru/widget/api_script_utf.js',

		debug = false,
		pageConfig = $('#page-config').data('value'),
		templateType = document.body.getAttribute('data-template') || '',
		templSep = templateType.indexOf(' ')
	; // end of vars

	if ( templSep > 0 ) {
		templateType = templateType.substring(0, templSep);
	}

	var
		/**
		 * Версионность файлов и загрузка неминифицированных скриптов в debug режиме
		 * 
		 * @param	{String}	filename	Имя файла который нужно загрузить
		 * 
		 * @return	{String}				Новое имя файла
		 */
		getWithVersion = function getWithVersion( filename ) {
			if ( typeof( global.release['version']) !== 'undefined' ) {

				if ( !debug ) {
					filename = filename.replace('.js', '.min.js');
				}

                filename += '?t=' + global.release['version'];
			}

			return filename;
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
		 * Загрузка скриптов для панели отладки
		 */
		loadDebugPanel = function loadDebugPanel() {
			if ( debug ) {
				$LAB.script('debug-panel.js')
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
	global.onerror = function() {
		return !debug;
	};


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

	// Если продуктивный режим - заглушить консоль
	if ( !debug ) {
		console.disable();
	}


	/**
	 * Создаем единый нэймспейс для проекта
	 * Добавляем модуль utils с функцией расширения нэймспейса
	 * Добавляем модуль config с основными конфигурациями клиентской стороны
	 */
	extendApp('ENTER.utils');
	global.ENTER.utils.extendApp = extendApp;

	extendApp('ENTER.config');
	global.ENTER.config.debug = debug;
	global.ENTER.config.jsStartTime = jsStartTime;
	global.ENTER.config.pageConfig = pageConfig;

	extendApp('ENTER.constructors');

	console.info('Создан единый namespace проекта');
	console.log(global.ENTER);

	if ( typeof $LAB === 'undefined' ) {
		throw new Error( 'Невозможно загрузить файлы JavaScript' );
	}

	/**
	 * Первоначальная настройка LAB
	 */
	$LAB.setGlobalDefaults({ AllowDuplicates: true, AlwaysPreserveOrder: true, UseLocalXHR: false, BasePath: '/js/prod/'})
		.queueWait(newDocumentWrite);


	// knockoutUrl = ( debug ) ? 'http://knockoutjs.com/downloads/knockout-2.2.1.debug.js' : 'http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js';
	knockoutUrl = ( debug ) ? '/js/vendor/knockout.js' : '/js/prod/knockout.min.js';
	yandexMapUrl = ( debug ) ? 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU&mode=debug' : 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU&mode=release';
	mustacheUrl = ( debug ) ? '/js/vendor/mustache.js' : '/js/prod/mustache.min.js';
	historyUrl = ( debug ) ? '/js/vendor/history.js' : '/js/prod/history.min.js';
	kladr = ( debug ) ? '/js/vendor/jquery.kladr.js' : '/js/prod/jquery.kladr.min.js';

	/**
	 * Загрузка скриптов по шаблону
	 */
	loadScripts = {
		'default': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
                    .wait()
                    .script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'main': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('main.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') )
					.wait()
					.script( logTimeAfterPartnerScript );
			}).runQueue();
		},

		'tag-category': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'infopage': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('infopage.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'enterprize': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('infopage.js') )
					.script( getWithVersion('enterprize.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'cart': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script('JsHttpRequest.min.js')
					.script( getWithVersion('library.js') )
					.script( directCreditUrl )
					.script( mustacheUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.wait()
					.script( getWithVersion('cart.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'lk': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('lk.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'order': function() {
			$LAB.queueScript( yandexMapUrl )
				.queueScript( knockoutUrl )
				.queueWait( function() {
					$LAB.script( getWithVersion('jquery-plugins.js') )
						.script('JsHttpRequest.min.js')
						.script( getWithVersion('library.js') )
						.script( directCreditUrl )
						.script( mustacheUrl )
						.script( loadDebugPanel )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('order-new.js') )
						.wait()
						.script( optimizelyUrl )
						.script('adfox.asyn.code.ver3.min.js')
						.wait()
						.script( getWithVersion('ports.js') )
				}).runQueue();
		},

		'order_new': function() {
			$LAB.queueScript( yandexMapUrl )
				.queueScript( knockoutUrl )
				.queueScript( kladr )
				.queueWait( function() {
					$LAB.script( getWithVersion('jquery-plugins.js') )
						.script('JsHttpRequest.min.js')
						.script( getWithVersion('library.js') )
						.script( directCreditUrl )
						.script( mustacheUrl )
						.script( loadDebugPanel )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('order-new-v5.js') )
						.wait()
						.script('adfox.asyn.code.ver3.min.js')
						.wait()
						.script( getWithVersion('ports.js') );
				}).runQueue();
		},

		'order_complete': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('order.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'product_catalog': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( historyUrl )
					.script( mustacheUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
                    .script( getWithVersion('infopage.js') )
					.script( getWithVersion('tchibo.js') )
					.script( getWithVersion('catalog.js') )
					.script( getWithVersion('pandora.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'product_card': function() {
			$LAB
				// .queueScript( knockoutUrl )
				// .queueScript( yandexMapUrl )
				.queueWait( function() {
					$LAB.script( getWithVersion('jquery-plugins.js') )
						.script( getWithVersion('library.js') )
						.script('JsHttpRequest.min.js')
						.script( directCreditUrl )
						.script( mustacheUrl )
						.script( loadDebugPanel )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('product.js') )
						// .script( getWithVersion('oneclick.js') )
						.wait()
						.script( optimizelyUrl )
						.script('adfox.asyn.code.ver3.min.js')
						.wait()
						.script( getWithVersion('ports.js') )
				}).runQueue();
		},

		'service': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.wait()
					.script( optimizelyUrl )
					.script('adfox.asyn.code.ver3.min.js')
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'shop': function() {
			$LAB.queueScript( yandexMapUrl )
				.queueWait( function() {
					$LAB.script( getWithVersion('jquery-plugins.js') )
						.script( getWithVersion('library.js') )
						.script( mustacheUrl )
						.script( loadDebugPanel )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('shop.js') )
						.wait()
						.script('tour.min.js')
						.wait()
						.script( optimizelyUrl )
                        .wait()
                        .script( getWithVersion('ports.js') )
			}).runQueue();
		},

		// неиспользуется
		'product_stock': function() {
			$LAB.queueScript( yandexMapUrl )
				.queueScript( knockoutUrl )
				.queueWait( function() {
					$LAB.script( getWithVersion('jquery-plugins.js') )
						.script( getWithVersion('library.js') )
						.script( mustacheUrl )
						.script( loadDebugPanel )
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
		},
        'slots': function() {
            $LAB.queueWait( function() {
                $LAB.script( getWithVersion('jquery-plugins.js') )
                    .script( getWithVersion('library.js') )
                    .wait()
                    .script( getWithVersion('common.js') )
                    .script( getWithVersion('infopage.js') )
                    .wait()
                    .script( getWithVersion('enterprize.js') )
                    .wait()
                    .script( getWithVersion('/js/game/slots.js') )

            }).runQueue();
        }
	};

	if ( loadScripts.hasOwnProperty(templateType) ) {
		console.log('Загрузка скриптов. Шаблон %s', templateType);
		loadScripts[templateType]();
	}
	else {
		console.log('Шаблон %s не найден. Загрузка стандартного набора скриптов', templateType);
		loadScripts['default']();
	}

}());
