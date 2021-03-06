/**
 * Улучшения консоли
 *
 * https://github.com/theshock/console-cap
 */
;(function ( console ) {
	var
		i,
		global  = this,
		// Function.prototype.bind не работает в IE8
		bind    = function ( context, fn ) {
			return function () {
					return Function.prototype.apply.call( fn, context, arguments );
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

        pageConfig = $('#page-config').data('value'),

		directCreditUrl = '//api.direct-credit.ru/dc.js',
        adfoxUrl = 'adfox_lib_ff.min.js', // 'adfox.asyn.code.ver3.min.js',
		yandexMapUrlv2_1, mustacheUrl, historyUrl, knockoutUrl,

		debug = false,
		templateType = document.body.getAttribute('data-template') || '',
		templSep = templateType.indexOf(' ');

	if ( templSep > 0 ) {
		templateType = templateType.substring(0, templSep);
	}

	if (pageConfig) {
		if (pageConfig.adfoxEnabled == false) adfoxUrl = '';
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
			if ( typeof(global.release) !== 'undefined' && typeof(global.release['version']) !== 'undefined' ) {

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
				return 'debug-panel.js';
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
	if ( document.body.getAttribute('data-debug') === 'true' && typeof console == 'object' && typeof console.warn == 'function') {
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
	global.ENTER.config.userInfo = $('.js-userConfig').data('value');

	extendApp('ENTER.constructors');
	extendApp('ENTER.auth');

	console.info('Создан единый namespace проекта');
	console.log(global.ENTER);

	if ( typeof $LAB === 'undefined' ) {
		throw new Error( 'Невозможно загрузить файлы JavaScript' );
	}

	/**
	 * Первоначальная настройка LAB
	 */
	$LAB.setGlobalDefaults({ AllowDuplicates: true, AlwaysPreserveOrder: true, UseLocalXHR: false, BasePath: '/js/prod/'});


	// knockoutUrl = ( debug ) ? 'http://knockoutjs.com/downloads/knockout-2.2.1.debug.js' : 'http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js';
	knockoutUrl = ( debug ) ? '/js/vendor/knockout.js' : '/js/prod/knockout.min.js';
	yandexMapUrlv2_1 = ( debug ) ? '//api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU&mode=debug' : '//api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU&mode=release';
	mustacheUrl = ( debug ) ? '/js/vendor/mustache.js' : '/js/prod/mustache.min.js';
	historyUrl = ( debug ) ? '/js/vendor/history.js' : '/js/prod/history.min.js';

	/**
	 * Загрузка скриптов по шаблону
	 */
	var loadScripts = {
		'default': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( knockoutUrl )
					.wait()
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
                    .wait()
					.script(yandexMapUrlv2_1)
					.script( getWithVersion('order-v3-1click.js') )
                    .script( getWithVersion('supplier.js') )
                    .script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'main': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( knockoutUrl )
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('main.js') )
                    .script( getWithVersion('product.js') )
					.wait()
					.script(yandexMapUrlv2_1)
					.script( getWithVersion('order-v3-1click.js') )
					.script( adfoxUrl )
					.wait()
					.script( getWithVersion('ports.js') )
					.wait()
					.script( logTimeAfterPartnerScript );
			}).runQueue();
		},

		'infopage': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( knockoutUrl )
					.wait()
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('infopage.js') )
					.wait()
					.script(yandexMapUrlv2_1)
					.script( getWithVersion('order-v3-1click.js') )
					.script( adfoxUrl )
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'cart': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script('JsHttpRequest.min.js')
					.script( getWithVersion('library.js') )
					.script( { src: directCreditUrl, type: 'text/javascript', charset: 'windows-1251' } )
					.script( mustacheUrl )
					.script( knockoutUrl )
					.wait()
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.wait()
					.script( getWithVersion('cart.js') )
					.wait()
					.script(yandexMapUrlv2_1)
					.script( getWithVersion('order-v3-1click.js') )
					.script( adfoxUrl )
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'compare': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( { src: directCreditUrl, type: 'text/javascript', charset: 'windows-1251' } )
					.script( mustacheUrl )
					.script( knockoutUrl )
					.wait()
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.wait()
					.script( getWithVersion('compare.js') )
					.wait()
					.script(yandexMapUrlv2_1)
					.script( getWithVersion('order-v3-1click.js') )
					.script( adfoxUrl )
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'lk': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( mustacheUrl )
					.script( knockoutUrl )
					.wait()
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.script( getWithVersion('lk.js') )
					.script( getWithVersion('favorite.js') )
					.script( getWithVersion('product.js') )
					.wait()
					.script(yandexMapUrlv2_1)
					.script( getWithVersion('order-v3-1click.js') )
					.script( adfoxUrl )
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'order-v3-new': function() {
			$LAB.queueScript(yandexMapUrlv2_1)
				.queueWait( function() {
					$LAB.script( getWithVersion('jquery-plugins.js') )
						.script( getWithVersion('library.js') )
						.script( mustacheUrl )
						.script( knockoutUrl )
						.wait()
						.script( loadDebugPanel )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('order-v3-new.js') )
						.wait()
						.script( getWithVersion('ports.js') );
				}).runQueue();
		},

		'product_catalog': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( historyUrl )
					.script( mustacheUrl )
					.script( knockoutUrl )
					.wait()
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
                    .script( getWithVersion('infopage.js') )
					.script( getWithVersion('catalog.js') )
					.wait()
					.script(yandexMapUrlv2_1)
					.script( getWithVersion('order-v3-1click.js') )
					.script( adfoxUrl )
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'product_card': function() {
			$LAB
				.queueWait( function() {
					$LAB.script( getWithVersion('jquery-plugins.js') )
						.script( getWithVersion('library.js') )
						.script('JsHttpRequest.min.js')
						.script( { src: directCreditUrl, type: 'text/javascript', charset: 'windows-1251' } )
						.script( mustacheUrl )
						.script( knockoutUrl )
						.wait()
						.script( loadDebugPanel )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('product.js') )
						.wait()
                        .script(yandexMapUrlv2_1)
                        .script( getWithVersion('order-v3-1click.js') )
						.script( adfoxUrl )
						.wait()
						.script( getWithVersion('ports.js') )
				}).runQueue();
		},

		'gift': function() {
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('jquery-plugins.js') )
					.script( getWithVersion('library.js') )
					.script( historyUrl )
					.script( mustacheUrl )
					.script( knockoutUrl )
					.wait()
					.script( loadDebugPanel )
					.wait()
					.script( getWithVersion('common.js') )
					.wait()
					.script( getWithVersion('catalog.js') )
					.script( getWithVersion('gift.js') )
					.wait()
					.script(yandexMapUrlv2_1)
					.script( getWithVersion('order-v3-1click.js') )
					.script( adfoxUrl )
					.wait()
					.script( getWithVersion('ports.js') )
			}).runQueue();
		},

		'shop': function() {
			$LAB.queueScript( yandexMapUrlv2_1 )
				.queueWait( function() {
					$LAB.script( getWithVersion('jquery-plugins.js') )
						.script( getWithVersion('library.js') )
						.script( mustacheUrl )
						.script( knockoutUrl )
						.wait()
						.script( loadDebugPanel )
						.wait()
						.script( getWithVersion('common.js') )
						.script( getWithVersion('shop.js') )
                        .script( getWithVersion('infopage.js') )
						.wait()
						.script( getWithVersion('order-v3-1click.js') )
                        .wait()
                        .script( getWithVersion('ports.js') )
			}).runQueue();
		},

        'page404': function() {
            $LAB.queueWait( function() {
                $LAB.script( getWithVersion('jquery-plugins.js') )
                    .script( getWithVersion('library.js') )
                    .script('JsHttpRequest.min.js')
                    .script( mustacheUrl )
                    .script( knockoutUrl )
                    .wait()
					.script( loadDebugPanel )
                    .wait()
                    .script( getWithVersion('common.js') )
                    .script( getWithVersion('product.js') )
                    .wait()
					.script(yandexMapUrlv2_1)
					.script( getWithVersion('order-v3-1click.js') )
                    .script( getWithVersion('ports.js') )
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
