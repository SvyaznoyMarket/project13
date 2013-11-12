/**
 * RequireJS config
 */
requirejs.config({
	baseUrl: "/js/terminal/",
	paths:{
		'jquery': 'vendor/jquery-1.8.3.min',
		'ejs': 'vendor/ejs_production',
		'bigjquery': '/js/prod/jquery-plugins.min'
	},
	shim: {
		'jquery': {
			exports: '$',
		},
		'ejs': {
			exports: 'EJS',
			deps: ['jquery']
		},
		'bigjquery': {
			deps: ['jquery']
		}
	},
	urlArgs : 'bust=' + new Date().getTime()
});

var extendApp = function extend( ns_string ) {
	window.terminal = window.terminal || {};

	var parts = ns_string.split('.'),
		parent = window.terminal,
		pl, i;
	// end of vars

	if ( parts[0] == 'terminal' ) {
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
};

var develop = false,
	trueTerminal = false;
// end of vars


if ( typeof terminal !== 'undefined' ) {
	trueTerminal = true;
}
else {
	/**
	 * имитируем объект терминал и его методы. Заполняем константы
	 */

	var terminalAPI = [
		'terminal.screen',
		'terminal.compare',
		'terminal.log',
		'terminal.compare.productRemoved',
		'terminal.compare.productAdded',
		'terminal.cart',
		'terminal.flickable',
		'terminal.interactive',
		'terminal.flickable.scrollValueChanged'
	];

	for ( var i = terminalAPI.length - 1; i >= 0; i-- ) {
		extendApp( terminalAPI[i] );
	}

	
	terminal.screen.push = function(){ return false; };
	terminal.compare.hasProduct = function(){ return false; };
	terminal.compare.removeProduct = function(){ return false; };
	terminal.compare.addProduct = function(){ return false; };
	terminal.log.write = function(){ return false; };
	terminal.compare.productRemoved.connect = function(){ return false; };
	terminal.compare.productAdded.connect = function(){ return false; };
	terminal.cart.setWarranty = function(){ return false; };
	terminal.cart.addService = function(){ return false; };
	terminal.cart.addProduct = function(){ return false; };
	terminal.compare.productRemoved.disconnect = function(){ return false; };
	terminal.compare.productAdded.disconnect = function(){ return false; };
	terminal.flickable.scrollValueChanged.connect = function(){ return false; };
	
	terminal.interactive = false;
	terminal.flickable.contentY = 0;
	terminal.flickable.contentY = 0;
	terminal.flickable.height = 0;
	terminal.flickable.contentHeight = 0;
}

// for all pages
// require(["termAPI"])

require(['jquery'], function( $ ) {
	$(document).ready(function() {

		var pagetype = $('article').data('pagetype');

		switch ( pagetype ) {
			case 'product_list':
				// product list scripts
				require(["product_list"]);
				break;
			case 'product_model_list':
				// product line scripts
				require(["product_list"]);
				break;
			case 'product':
				// product scripts
				require(["product"]);
				break;
			case 'filter':
				// catalog filter scripts
				require(["filter"]);
				break;
		}
	});
});