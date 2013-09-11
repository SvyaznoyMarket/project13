/**
 * Filters
 *
 * @requires jQuery, Mustache, ENTER.utils, ENTER.config
 * 
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	global	Enter namespace
 */
;(function( ENTER ) {
	var userUrl = ENTER.config.pageConfig.userUrl,
		utils = ENTER.utils;
	// end of vars
	
	console.info('New catalog init: filter.js');

	// ==== Mustache test out
	console.log('Mustache is '+typeof Mustache);

	var person = {
			firstName: "Alexandr",
			lastName: "Zaytsev"
		},
		template = "<h1>{{firstName}} {{lastName}}</h1>test out with Mustache<br/><a class='jsHistoryLink' href='/newurl'>test history api</a>",
		html = Mustache.to_html(template, person),
		testOut = $('<div>').addClass('popup').html(html);
	// end of vars

	// testOut.appendTo('body');

	// testOut.lightbox_me({
	// 	centered: true
	// });
	// ==== END Mustache test out
	
	
	var historyLinkHandler = function historyLinkHandler() {
		var state = {
			title: 'history link to '+$(this).attr('href'),
			url: $(this).attr('href')
		}

		console.info('link handler. push state '+state.url);

		History.pushState(state, state.title, state.url);

		return false;
	};

	/**
	 * Обработка back\forward
	 */
	var backForwardHandler = function backForwardHandler() {
		var returnLocation = history.location || document.location;

		console.info(returnLocation);
		console.log( JSON.stringify(history.state) );
	};

	var stateChangeHandler = function stateChangeHandler() {
		var state = History.getState(),
			url = state.url;
		// end of vars
		
		console.info('statechange');

		console.log(url);
		console.log(state);
	};

	History.Adapter.bind(window, "statechange", stateChangeHandler);
	$('body').on('click', '.jsHistoryLink', historyLinkHandler);

}(window.ENTER));