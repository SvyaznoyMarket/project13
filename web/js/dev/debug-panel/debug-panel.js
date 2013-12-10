;(function ( window, document, $, ENTER, Mustache ) {
	console.info('debug panel loaded');

	var
		d = $(document),
		debugPanel = $('.jsDebugPanel'),
		debugPanelConfig = debugPanel.data('value'),
		debugContent = debugPanel.find('.jsDebugPanelContent');
	// end of vars


	var
		/**
		 * Рендер шаблонов панели отладки
		 */
		render = {
			'_default': function( name, data, icon ) {
				console.info('render like default');

				var
					templateWrap = $('#tplDebugFirstLevelDefault'),
					template = templateWrap.html(),
					partials = templateWrap.data('partial'),
					html;
				// end of vars
				
				data.name = name;
				data.iconUrl = (icon) ? icon : '/debug/icons/default.png';

				html = Mustache.render(template, data, partials);

				return html;
			},

			'_expanded': function( name, data, icon ) {
				var
					templateWrap = $('#tplDebugFirstLevelHidden'),
					template = templateWrap.html(),
					partials = templateWrap.data('partial'),
					html;
				// end of vars
				
				data.name = name;
				data._data = window.JSON.stringify(data.value, null, 4);
				data.iconUrl = (icon) ? icon : '/debug/icons/default.png';

				html = Mustache.render(template, data, partials);

				return html;
			},

			'id': function( name, data ) {
				console.info('render like id');

				var
					iconUrl = '/debug/icons/id.png',
					html;
				// end of vars

				html = render['_default'](name, data, iconUrl);

				return html;
			},

			'git': function( name, data ) {
				console.info('render like git');

				var
					templateWrap = $('#tplDebugFirstLevelGit'),
					template = templateWrap.html(),
					partials = templateWrap.data('partial'),
					html;
				// end of vars
				
				data.name = name;
				data.iconUrl = '/debug/icons/git.png';

				html = Mustache.render(template, data, partials);

				return html;
			},
			
			'query': function( name, data ) {
				console.info('render like query');

				var
					templateWrap = $('#tplDebugFirstLevelQuery'),
					template = templateWrap.html(),
					partials = templateWrap.data('partial'),
					html;
				// end of vars
				
				data.name = name;
				data.iconUrl = '/debug/icons/query.png';

				html = Mustache.render(template, data, partials);

				return html;
			},

			'timer': function( name, data ) {
				console.info('render like timer');

				var
					templateWrap = $('#tplDebugFirstLevelTimer'),
					template = templateWrap.html(),
					partials = templateWrap.data('partial'),
					html;
				// end of vars
				
				data.name = name;
				data.iconUrl = '/debug/icons/timer.png';

				html = Mustache.render(template, data, partials);

				return html;
			},

			'session': function( name, data ) {
				console.info('render like session');

				var
					iconUrl = '/debug/icons/session.png',
					html;
				// end of vars

				html = render['_expanded'](name, data, iconUrl);

				return html;
			},
			
			'memory': function( name, data ) {
				console.info('render like memory');

				var
					templateWrap = $('#tplDebugFirstLevelMemory'),
					template = templateWrap.html(),
					partials = templateWrap.data('partial'),
					html;
				// end of vars

				data.name = name;
				data.iconUrl = '/debug/icons/memory.png';

				html = Mustache.render(template, data, partials);

				return html;
			},

			'config': function( name, data ) {
				console.info('render like config');

				var
					iconUrl = '/debug/icons/config.png',
					html;
				// end of vars

				html = render['_expanded'](name, data, iconUrl);

				return html;
			},

			'abTest': function( name, data ) {
				console.info('render like config');

				var
					iconUrl = '/debug/icons/abTest.png',
					html;
				// end of vars

				html = render['_expanded'](name, data, iconUrl);

				return html;
			},

			'abTestJson': function( name, data ) {
				console.info('render like config');

				var
					iconUrl = '/debug/icons/abTestJson.png',
					html;
				// end of vars

				html = render['_expanded'](name, data, iconUrl);

				return html;
			},

			'server': function( name, data ) {
				console.info('render like config');

				var
					iconUrl = '/debug/icons/server.png',
					html;
				// end of vars

				html = render['_expanded'](name, data, iconUrl);

				return html;
			},

			'ajax': function( name ) {
				console.info('render like ajax');

				var
					templateWrap = $('#tplDebugAjax'),
					template = templateWrap.html(),
					partials = templateWrap.data('partial'),
					data = {},
					html;
				// end of vars
				
				data.name = name;

				html = Mustache.render(template, data, partials);

				return html;
			}
		},

		/**
		 * Инициализация панели отладки
		 */
		initPanel = function initPanel( node, data ) {
			console.info('Render debug panel');

			var
				html,
				key;
			// end of vars
			
			for ( key in data ) {
				if ( !data.hasOwnProperty(key) ) {
					continue;
				}

				console.log(key);
				console.log(data[key]);

				if ( render.hasOwnProperty(key) ) {
					html = render[key](key, data[key]);
				}
				else {
					html = render['_default'](key, data[key]);
				}

				node.append(html);
			}
		},

		/**
		 * Общий обработчик AJAX
		 */
		ajaxResponse = function ajaxResponse( event, xhr, settings ) {
			console.warn('=== ajaxSuccess ===');

			var
				res = JSON.parse(xhr.responseText),
				debugInfo = res.debug || false,
				siteUrl = settings.url,
				outNode,
				html;
			// end of vars

			if ( !debugInfo ) {
				return;
			}

			html = render['ajax'](siteUrl);
			
			debugPanel.append(html);

			outNode = debugPanel.find('.jsDebugPanelContent').eq(debugPanel.find('.jsDebugPanelContent').length - 1);

			console.log(outNode);
			// debugPanel.append(html);

			initPanel( outNode, debugInfo );
		},

		/**
		 * Сворачивание\Разворачивание значений
		 */
		expandValue = function expandValue() {
			console.info('expandValue');

			var
				self = $(this),
				openClass = 'jsOpened',
				opened = self.hasClass(openClass),
				expandValueNode = self.parents('tr').find('.jsExpandedValue');
			// end of vars
			
			if ( opened ) {
				expandValueNode.hide(300);
				self.removeClass(openClass);
			}
			else {
				expandValueNode.show(300);
				self.addClass(openClass);
			}

			return false;
		},

		/**
		 * Открытие дебаг панели
		 */
		openDebugPanel = function openDebugPanel() {
			console.info('Show debug panel');

			var
				self = $(this),
				openClass = 'jsOpened',
				opened = self.hasClass(openClass),
				expand = self.siblings('.jsDebugPanelContent');
			// end of vars

			if ( opened ) {
				expand.fadeOut(300);
				self.removeClass(openClass);
			}
			else {
				expand.fadeIn(300);
				self.addClass(openClass);
			}

			return false;
		};
	// end of functions


	initPanel( debugContent, debugPanelConfig );

	debugPanel.on('click', '.jsExpandValue', expandValue);
	debugPanel.on('click', '.jsOpenDebugPanel', openDebugPanel);

	d.ajaxSuccess(ajaxResponse);

}(this, this.document, this.jQuery, this.ENTER, this.Mustache));