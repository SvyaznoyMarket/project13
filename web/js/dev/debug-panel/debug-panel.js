;(function ( window, document, $, ENTER, Mustache ) {
	console.info('debug panel loaded');

	var
		debugPanel = $('.jsDebugPanel'),
		debugPanelConfig = debugPanel.data('value'),
		debugContent = debugPanel.find('.jsDebugPanelContent'),
		openDebugPanelBtn = debugPanel.find('.jsOpenDebugPanel');
	// end of vars


	var
		render = {
			'_default': function( name, data, icon ) {
				console.info('render like default');

				var
					templateWrap = $('#firstLevel'),
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
					templateWrap = $('#firstLevel-hidden'),
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
					templateWrap = $('#firstLevel-git'),
					template = templateWrap.html(),
					partials = templateWrap.data('partial'),
					html;
				// end of vars
				
				data.name = name;
				data.iconUrl = '/debug/icons/git.png';

				html = Mustache.render(template, data, partials);

				return html;
			},

			// 'env': function( name, data ) {

			// },
			
			'query': function( name, data ) {
				console.info('render like query');

				var
					templateWrap = $('#firstLevel-query'),
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
					templateWrap = $('#firstLevel-timer'),
					template = templateWrap.html(),
					partials = templateWrap.data('partial'),
					html;
				// end of vars
				
				data.name = name;
				data.iconUrl = '/debug/icons/timer.png';

				html = Mustache.render(template, data, partials);

				return html;
			},

			// 'user': function( name, data ) {

			// },
			// 'route': function( name, data ) {

			// },
			// 'act': function( name, data ) {

			// },
			// 'sub.act': function( name, data ) {

			// },
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
					templateWrap = $('#firstLevel-memory'),
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
			}

		},

		initPanel = function initPanel() {
			console.info('Render debug panel');

			var
				html,
				key;
			// end of vars
			
			for ( key in debugPanelConfig ) {
				if ( !debugPanelConfig.hasOwnProperty(key) ) {
					continue;
				}

				console.log(key);
				console.log(debugPanelConfig[key]);

				if ( render.hasOwnProperty(key) ) {
					html = render[key](key, debugPanelConfig[key]);
				}
				else {
					html = render['_default'](key, debugPanelConfig[key]);
				}

				debugContent.append(html);
			}
		},

		expandValue = function expandValue() {
			console.info('expandValue');

			var
				self = $(this),
				openClass = 'jsOpened',
				opened = self.hasClass(openClass),
				expandValue = self.siblings('.jsExpandedValue');
			// end of vars
			
			if ( opened ) {
				expandValue.hide(300);
				self.removeClass(openClass).html('...');
			}
			else {
				expandValue.show(300);
				self.addClass(openClass).html('Свернуть');
			}

			return false;
		},

		openDebugPanel = function openDebugPanel() {
			console.info('Show debug panel');

			var
				self = $(this),
				openClass = 'jsOpened',
				opened = self.hasClass(openClass);
			// end of vars

			if ( opened ) {
				debugContent.fadeOut(300);
				self.removeClass(openClass);
			}
			else {
				debugContent.fadeIn(300);
				self.addClass(openClass);
			}

			return false;
		};
	// end of functions

	initPanel();

	debugPanel.on('click', '.jsExpandValue', expandValue)
	openDebugPanelBtn.on('click', openDebugPanel);


	// $('.debug-panel a').on('click', function(e) {
	// 	e.preventDefault();
	// 	console.info('debug cliclked');

	// 	var
	// 		parent = $(this).parent(),
	// 		contentEl = parent.find('.content'),
	// 		content = '<br /><table class="property">';
	// 	// end of vars

	// 	$.each(parent.data('value'), function(i, item) {
	// 		var
	// 			type = item[1],
	// 			value = item[0],
	// 			icon = '/debug/icons/default.png';
	// 		// end of vars

	// 		if ( ('id' == i) || ('env' == i) || ('route' == i) || ('act' == i) || ('sub.act' == i) || ('user' == i) ) {
	// 			value = '<span style="color: #ffffff">' + value + '</span>';
	// 		}
	// 		else if ('status' == i) {
	// 			value = '<span style="color: ' + ((value > 300) ? '#ff0000' : '#00ff00') + '">' + value + '</span>' ;
	// 		}
	// 		else if ('git' == i) {
	// 			value = '<span style="color: #ffff00">' + value.version + '</span> ' + value.tag;
	// 		}
	// 		else if ('timer' == i) {
	// 			value = '<table>';
	// 			$.each(item[0], function(i, item) {
	// 				value += '<tr><td class="query-cell">' + i + ': </td><td class="query-cell query-ok">' + item.value + ' ' + item.unit + ' (' + item.count + ')' + '</td></tr>';
	// 			})
	// 			value += '</table>';
	// 		}
	// 		else if ('memory' == i) {
	// 			value = value.value + ' ' + value.unit;
	// 		}
	// 		else if (('error' == i) && (value[0])) {
	// 			value = value[0];
	// 			value = '<span style="color: #ff0000">#' + value.code + ' ' + value.message + '</span>';
	// 		}
	// 		else if ('query' == i) {
	// 			value = '<table>';
	// 			$.each(item[0], function(i, item) {
	// 				valueClass = 'query-default';
	// 				if (item.error) {
	// 					valueClass = 'query-fail';
	// 				} else if (item.url) {
	// 					valueClass = 'query-ok';
	// 				} else {
	// 					item.url = '';
	// 				}

	// 				value += '<tr>'
	// 					+ '<td class="query-cell">'
	// 						+ ((item.info && item.info.total_time) ? item.info.total_time : '')
	// 					+ '</td>'
	// 					+ '<td class="query-cell">'
	// 						+ ((item.url && item.retryCount) ? item.retryCount : '')
	// 					+ '</td>'
	// 					+ '<td class="query-cell">'
	// 						+ ((item.header && item.header['X-Server-Name']) ? item.header['X-Server-Name'] : '')
	// 						+ ' '
	// 						+ ((item.header && item.header['X-API-Mode']) ? item.header['X-API-Mode'] : '')
	// 					+ '</td>'
	// 					+ '<td class="query-cell"><a href="' + item.url + '" class="query ' + valueClass + '">' + item.escapedUrl + (item.data ? ('<span style="color: #ededed"> --data ' + JSON.stringify(item.data) + '</span>') : '') + '</a></td>'
	// 					+ '</tr>';

	// 			})
	// 			value += '</table>';
	// 		}
	// 		else {
	// 			value = '<pre class="hidden">' + JSON.stringify(value, null, 4) + '</pre>';
	// 		}

	// 		if ( -1 !== $.inArray(i, ['id', 'query', 'user', 'config', 'memory', 'memory', 'time']) ) {
	// 			icon = '/debug/icons/' + i + '.png';
	// 		}

	// 		content += (
	// 			'<tr>'
	// 			+ '<td class="property-name" style="background-image: url(' + icon + ');"><a class="property-name-link" href="#" style="' + (('info' != type) ? ('color: #ff0000;') : '') + '">' + i  + '</a></td>'
	// 			+ '<td class="property-value">' + value + '</td>'
	// 			+ '</tr>'
	// 		);
	// 	});
	// 	content += '</table>';

	// 	contentEl.html(content);
	// });

}(this, this.document, this.jQuery, this.ENTER, this.Mustache));