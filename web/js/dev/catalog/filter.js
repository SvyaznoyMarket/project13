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
		template = "<h1>{{firstName}} {{lastName}}</h1>test out with Mustache",
		html = Mustache.to_html(template, person),
		testOut = $('<div>').addClass('popup').html(html);
	// end of vars

	testOut.appendTo('body');

	testOut.lightbox_me({
		centered: true
	});
	// ==== END Mustache test out

}(window.ENTER));