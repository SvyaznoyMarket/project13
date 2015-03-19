;(function ( window, document, $, ENTER, Mustache ) {
	var
		d = $(document),
		debugPanel = $('.jsDebugPanel'),
		debugPanelContent = $('.jsDebugPanelContent'),
		currentDebugPanelItemConfig = $.parseJSON(debugPanel.find('script').eq(0).html()),
		currentDebugPanelItemContent = debugPanel.find('.jsCurrentDebugPanelItemContent'),
		prevDebugPanelItemConfig = $.parseJSON(debugPanel.find('script').eq(1).html()),
		prevDebugPanelItemContent = debugPanel.find('.jsPrevDebugPanelItemContent');
	// end of vars

	var
		/**
		 * Рендер шаблонов панели отладки
		 */
		render = {
			'_default': function( name, data, icon ) {
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
				var
					iconUrl = '/debug/icons/id.png',
					html;
				// end of vars

				html = render['_default'](name, data, iconUrl);

				return html;
			},

			'git': function( name, data ) {
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

            'jira': function( name, data ) {
                var
                    templateWrap = $('#tplDebugFirstLevelJira'),
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
				var
					iconUrl = '/debug/icons/session.png',
					html;
				// end of vars

				html = render['_expanded'](name, data, iconUrl);

				return html;
			},
			
			'memory': function( name, data ) {
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
				var
                    templateWrap = $('#tplDebugFirstLevelConfig'),
                    template = templateWrap.html(),
                    partials = templateWrap.data('partial'),
                    html;
				// end of vars

                data.name = name;
                data.iconUrl = '/debug/icons/config.png';

                html = Mustache.render(template, data, partials);

				return html;
			},

			'abTest': function( name, data ) {
				var
					iconUrl = '/debug/icons/abTest.png',
					html;
				// end of vars

				html = render['_expanded'](name, data, iconUrl);

				return html;
			},

			'abTestJson': function( name, data ) {
				var
					iconUrl = '/debug/icons/abTestJson.png',
					html;
				// end of vars

				html = render['_expanded'](name, data, iconUrl);

				return html;
			},

			'server': function( name, data ) {
				var
					iconUrl = '/debug/icons/server.png',
					html;
				// end of vars

				html = render['_expanded'](name, data, iconUrl);

				return html;
			},

			'ajax': function( name ) {
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
		initPanel = function( node, data ) {
			var
				html,
				key;
			// end of vars
			
			for ( key in data ) {
				if ( !data.hasOwnProperty(key) ) {
					continue;
				}

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
		ajaxResponse = function( event, xhr, settings ) {
			var
				res,
				debugInfo,
				siteUrl = settings.url,
				outNode,
				html;
			// end of vars

			try {
				res = JSON.parse(xhr.responseText);
				if (!res || typeof res !== "object" || res === null) {
					throw "JSON.parse error";
				}

				debugInfo = res.debug || false;
				if ( !debugInfo ) {
					throw "debugInfo error";
				}
			} catch (e) {
				console.warn(e);
				return;
			}

			html = render['ajax'](siteUrl);
			debugPanelContent.append(html);
			outNode = debugPanel.find('.jsDebugPanelItemContent').eq(debugPanel.find('.jsDebugPanelItemContent').length - 1);

			initPanel( outNode, debugInfo );
		},

		/**
		 * Сворачивание\Разворачивание значений
		 */
		expandValue = function() {
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
		 * Открытие элемента дебаг панели
		 */
		openDebugPanel = function() {
			var
				self = $(this),
				openClass = 'jsOpened',
				opened = self.hasClass(openClass),
				expand = self.siblings('.jsDebugPanelItemContent');
			// end of vars

			if ( opened ) {
				expand.slideUp(200);
				self.removeClass(openClass);
			}
			else {
				expand.slideDown(200);
				self.addClass(openClass);
			}

			return false;
		};

        /**
         * Уничтожение элемента дебаг панели
         */
        removeDebugPanel = function() {
            $(this).parent().remove();

            return false;
        };

		/**
		 * Открытие дебаг панели
		 */
		openDebugPanelContent = function(e) {
			e.preventDefault();
			debugPanelContent.toggle();
		};
	// end of functions


	initPanel( currentDebugPanelItemContent, currentDebugPanelItemConfig );
	initPanel( prevDebugPanelItemContent, prevDebugPanelItemConfig );

	debugPanel.on('click', '.jsExpandValue', expandValue);
	debugPanel.on('click', '.jsOpenDebugPanelItem', openDebugPanel);
	debugPanel.on('click', '.jsCloseDebugPanelItem', removeDebugPanel);
	debugPanel.on('click', '.jsOpenDebugPanelContent', openDebugPanelContent);

	d.ajaxSuccess(ajaxResponse);

}(window, document, jQuery, ENTER, Mustache));