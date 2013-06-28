/*
	http://www.JSON.org/json2.js
	2011-10-19
	creates a global JSON object containing two methods: stringify and parse
	IVN COMMENT:
	!!! ATTENTION: method parse is replaced by json_parse !!!
	
		JSON.stringify(value, replacer, space)
			value       any JavaScript value, usually an object or array.

			replacer    an optional parameter that determines how object
						values are stringified for objects. It can be a
						function or an array of strings.

			space       an optional parameter that specifies the indentation
						of nested structures. If it is omitted, the text will
						be packed without extra whitespace. If it is a number,
						it will specify the number of spaces to indent at each
						level. If it is a string (such as '\t' or '&nbsp;'),
						it contains the characters used to indent at each level.

			This method produces a JSON text from a JavaScript value.

			When an object value is found, if the object contains a toJSON
			method, its toJSON method will be called and the result will be
			stringified. A toJSON method does not serialize: it returns the
			value represented by the name/value pair that should be serialized,
			or undefined if nothing should be serialized. The toJSON method
			will be passed the key associated with the value, and this will be
			bound to the value

			For example, this would serialize Dates as ISO strings.

				Date.prototype.toJSON = function (key) {
					function f(n) {
						// Format integers to have at least two digits.
						return n < 10 ? '0' + n : n;
					}

					return this.getUTCFullYear()   + '-' +
						f(this.getUTCMonth() + 1) + '-' +
						f(this.getUTCDate())      + 'T' +
						f(this.getUTCHours())     + ':' +
						f(this.getUTCMinutes())   + ':' +
						f(this.getUTCSeconds())   + 'Z';
				};

			You can provide an optional replacer method. It will be passed the
			key and value of each member, with this bound to the containing
			object. The value that is returned from your method will be
			serialized. If your method returns undefined, then the member will
			be excluded from the serialization.

			If the replacer parameter is an array of strings, then it will be
			used to select the members to be serialized. It filters the results
			such that only members with keys listed in the replacer array are
			stringified.

			Values that do not have JSON representations, such as undefined or
			functions, will not be serialized. Such values in objects will be
			dropped; in arrays they will be replaced with null. You can use
			a replacer function to replace those with JSON values.
			JSON.stringify(undefined) returns undefined.

			The optional space parameter produces a stringification of the
			value that is filled with line breaks and indentation to make it
			easier to read.

			If the space parameter is a non-empty string, then that string will
			be used for indentation. If the space parameter is a number, then
			the indentation will be that many spaces.

			Example:

			text = JSON.stringify(['e', {pluribus: 'unum'}]);
			// text is '["e",{"pluribus":"unum"}]'


			text = JSON.stringify(['e', {pluribus: 'unum'}], null, '\t');
			// text is '[\n\t"e",\n\t{\n\t\t"pluribus": "unum"\n\t}\n]'

			text = JSON.stringify([new Date()], function (key, value) {
				return this[key] instanceof Date ?
					'Date(' + this[key] + ')' : value;
			});
			// text is '["Date(---current time---)"]'


		JSON.parse(text, reviver)
			This method parses a JSON text to produce an object or array.
			It can throw a SyntaxError exception.

			The optional reviver parameter is a function that can filter and
			transform the results. It receives each of the keys and values,
			and its return value is used instead of the original value.
			If it returns what it received, then the structure is not modified.
			If it returns undefined then the member is deleted.

			Example:

			// Parse the text. Values that look like ISO date strings will
			// be converted to Date objects.

			myData = JSON.parse(text, function (key, value) {
				var a;
				if (typeof value === 'string') {
					a =
/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}(?:\.\d*)?)Z$/.exec(value);
					if (a) {
						return new Date(Date.UTC(+a[1], +a[2] - 1, +a[3], +a[4],
							+a[5], +a[6]));
					}
				}
				return value;
			});

			myData = JSON.parse('["Date(09/09/2001)"]', function (key, value) {
				var d;
				if (typeof value === 'string' &&
						value.slice(0, 5) === 'Date(' &&
						value.slice(-1) === ')') {
					d = new Date(value.slice(5, -1));
					if (d) {
						return d;
					}
				}
				return value;
			});
*/

if (!window.JSON) {
	window.JSON = {};
}

(function () {
	'use strict';

	function f(n) {
		// Format integers to have at least two digits.
		return n < 10 ? '0' + n : n;
	}

	if (typeof Date.prototype.toJSON !== 'function') {

		Date.prototype.toJSON = function (key) {

			return isFinite(this.valueOf()) ? this.getUTCFullYear()     + '-' +
					f(this.getUTCMonth() + 1) + '-' +
					f(this.getUTCDate())      + 'T' +
					f(this.getUTCHours())     + ':' +
					f(this.getUTCMinutes())   + ':' +
					f(this.getUTCSeconds())   + 'Z'
				: null;
		};

		String.prototype.toJSON      =
			Number.prototype.toJSON  =
			Boolean.prototype.toJSON = function (key) {
				return this.valueOf();
			};
	}

	var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
		escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
		gap,
		indent,
		meta = {    // table of character substitutions
			'\b': '\\b',
			'\t': '\\t',
			'\n': '\\n',
			'\f': '\\f',
			'\r': '\\r',
			'"' : '\\"',
			'\\': '\\\\'
		},
		rep;


	function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

		escapable.lastIndex = 0;
		return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
			var c = meta[a];
			return typeof c === 'string' ? c
				: '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
		}) + '"' : '"' + string + '"';
	}


	function str(key, holder) {

// Produce a string from holder[key].

		var i,          // The loop counter.
			k,          // The member key.
			v,          // The member value.
			length,
			mind = gap,
			partial,
			value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

		if (value && typeof value === 'object' &&
				typeof value.toJSON === 'function') {
			value = value.toJSON(key);
		}

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

		if (typeof rep === 'function') {
			value = rep.call(holder, key, value);
		}

// What happens next depends on the value's type.

		switch (typeof value) {
		case 'string':
			return quote(value);

		case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

			return isFinite(value) ? String(value) : 'null';

		case 'boolean':
		case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

			return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

		case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

			if (!value) {
				return 'null';
			}

// Make an array to hold the partial results of stringifying this object value.

			gap += indent;
			partial = [];

// Is the value an array?

			if (Object.prototype.toString.apply(value) === '[object Array]') {

// The value is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

				length = value.length;
				for (i = 0; i < length; i += 1) {
					partial[i] = str(i, value) || 'null';
				}

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

				v = partial.length === 0 ? '[]'
					: gap ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']'
					: '[' + partial.join(',') + ']';
				gap = mind;
				return v;
			}

// If the replacer is an array, use it to select the members to be stringified.

			if (rep && typeof rep === 'object') {
				length = rep.length;
				for (i = 0; i < length; i += 1) {
					if (typeof rep[i] === 'string') {
						k = rep[i];
						v = str(k, value);
						if (v) {
							partial.push(quote(k) + (gap ? ': ' : ':') + v);
						}
					}
				}
			} else {

// Otherwise, iterate through all of the keys in the object.

				for (k in value) {
					if (Object.prototype.hasOwnProperty.call(value, k)) {
						v = str(k, value);
						if (v) {
							partial.push(quote(k) + (gap ? ': ' : ':') + v);
						}
					}
				}
			}

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

			v = partial.length === 0 ? '{}'
				: gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}'
				: '{' + partial.join(',') + '}';
			gap = mind;
			return v;
		}
	}

// If the JSON object does not yet have a stringify method, give it one.

	if (typeof JSON.stringify !== 'function') {
		JSON.stringify = function (value, replacer, space) {
// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

			var i;
			gap = '';
			indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

			if (typeof space === 'number') {
				for (i = 0; i < space; i += 1) {
					indent += ' ';
				}

// If the space parameter is a string, it will be used as the indent string.

			} else if (typeof space === 'string') {
				indent = space;
			}

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

			rep = replacer;
			if (replacer && typeof replacer !== 'function' &&
					(typeof replacer !== 'object' ||
					typeof replacer.length !== 'number')) {
				throw new Error('JSON.stringify');
			}

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

			return str('', {'': value});
		};
	}


// If the JSON object does not yet have a parse method, give it one.
	if (typeof JSON.parse !== 'function') {
		JSON.parse = (function () {
			"use strict";
		
		// This function creates a JSON parse function that uses a state machine rather
		// than the dangerous eval function to parse a JSON text.
		
			var state,      // The state of the parser, one of
							// 'go'         The starting state
							// 'ok'         The final, accepting state
							// 'firstokey'  Ready for the first key of the object or
							//              the closing of an empty object
							// 'okey'       Ready for the next key of the object
							// 'colon'      Ready for the colon
							// 'ovalue'     Ready for the value half of a key/value pair
							// 'ocomma'     Ready for a comma or closing }
							// 'firstavalue' Ready for the first value of an array or
							//              an empty array
							// 'avalue'     Ready for the next value of an array
							// 'acomma'     Ready for a comma or closing ]
				stack,      // The stack, for controlling nesting.
				container,  // The current container object or array
				key,        // The current key
				value,      // The current value
				escapes = { // Escapement translation table
					'\\': '\\',
					'"': '"',
					'/': '/',
					't': '\t',
					'n': '\n',
					'r': '\r',
					'f': '\f',
					'b': '\b'
				},
				string = {   // The actions for string tokens
					go: function () {
						state = 'ok';
					},
					firstokey: function () {
						key = value;
						state = 'colon';
					},
					okey: function () {
						key = value;
						state = 'colon';
					},
					ovalue: function () {
						state = 'ocomma';
					},
					firstavalue: function () {
						state = 'acomma';
					},
					avalue: function () {
						state = 'acomma';
					}
				},
				number = {   // The actions for number tokens
					go: function () {
						state = 'ok';
					},
					ovalue: function () {
						state = 'ocomma';
					},
					firstavalue: function () {
						state = 'acomma';
					},
					avalue: function () {
						state = 'acomma';
					}
				},
				action = {
		
		// The action table describes the behavior of the machine. It contains an
		// object for each token. Each object contains a method that is called when
		// a token is matched in a state. An object will lack a method for illegal
		// states.
		
					'{': {
						go: function () {
							stack.push({state: 'ok'});
							container = {};
							state = 'firstokey';
						},
						ovalue: function () {
							stack.push({container: container, state: 'ocomma', key: key});
							container = {};
							state = 'firstokey';
						},
						firstavalue: function () {
							stack.push({container: container, state: 'acomma'});
							container = {};
							state = 'firstokey';
						},
						avalue: function () {
							stack.push({container: container, state: 'acomma'});
							container = {};
							state = 'firstokey';
						}
					},
					'}': {
						firstokey: function () {
							var pop = stack.pop();
							value = container;
							container = pop.container;
							key = pop.key;
							state = pop.state;
						},
						ocomma: function () {
							var pop = stack.pop();
							container[key] = value;
							value = container;
							container = pop.container;
							key = pop.key;
							state = pop.state;
						}
					},
					'[': {
						go: function () {
							stack.push({state: 'ok'});
							container = [];
							state = 'firstavalue';
						},
						ovalue: function () {
							stack.push({container: container, state: 'ocomma', key: key});
							container = [];
							state = 'firstavalue';
						},
						firstavalue: function () {
							stack.push({container: container, state: 'acomma'});
							container = [];
							state = 'firstavalue';
						},
						avalue: function () {
							stack.push({container: container, state: 'acomma'});
							container = [];
							state = 'firstavalue';
						}
					},
					']': {
						firstavalue: function () {
							var pop = stack.pop();
							value = container;
							container = pop.container;
							key = pop.key;
							state = pop.state;
						},
						acomma: function () {
							var pop = stack.pop();
							container.push(value);
							value = container;
							container = pop.container;
							key = pop.key;
							state = pop.state;
						}
					},
					':': {
						colon: function () {
							if (Object.hasOwnProperty.call(container, key)) {
								throw new SyntaxError('Duplicate key "' + key + '"');
							}
							state = 'ovalue';
						}
					},
					',': {
						ocomma: function () {
							container[key] = value;
							state = 'okey';
						},
						acomma: function () {
							container.push(value);
							state = 'avalue';
						}
					},
					'true': {
						go: function () {
							value = true;
							state = 'ok';
						},
						ovalue: function () {
							value = true;
							state = 'ocomma';
						},
						firstavalue: function () {
							value = true;
							state = 'acomma';
						},
						avalue: function () {
							value = true;
							state = 'acomma';
						}
					},
					'false': {
						go: function () {
							value = false;
							state = 'ok';
						},
						ovalue: function () {
							value = false;
							state = 'ocomma';
						},
						firstavalue: function () {
							value = false;
							state = 'acomma';
						},
						avalue: function () {
							value = false;
							state = 'acomma';
						}
					},
					'null': {
						go: function () {
							value = null;
							state = 'ok';
						},
						ovalue: function () {
							value = null;
							state = 'ocomma';
						},
						firstavalue: function () {
							value = null;
							state = 'acomma';
						},
						avalue: function () {
							value = null;
							state = 'acomma';
						}
					}
				};
		
			function debackslashify(text) {
		
		// Remove and replace any backslash escapement.
		
				return text.replace(/\\(?:u(.{4})|([^u]))/g, function (a, b, c) {
					return b ? String.fromCharCode(parseInt(b, 16)) : escapes[c];
				});
			}
		
			return function (source, reviver) {
		
		// A regular expression is used to extract tokens from the JSON text.
		// The extraction process is cautious.
		
				var r,          // The result of the exec method.
					tx = /^[\x20\t\n\r]*(?:([,:\[\]{}]|true|false|null)|(-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)|"((?:[^\r\n\t\\\"]|\\(?:["\\\/trnfb]|u[0-9a-fA-F]{4}))*)")/;
		
		// Set the starting state.
		
				state = 'go';
		
		// The stack records the container, key, and state for each object or array
		// that contains another object or array while processing nested structures.
		
				stack = [];
		
		// If any error occurs, we will catch it and ultimately throw a syntax error.
		
				try {
		
		// For each token...
		
					for (;;) {
						r = tx.exec(source);
						if (!r) {
							break;
						}
		
		// r is the result array from matching the tokenizing regular expression.
		//  r[0] contains everything that matched, including any initial whitespace.
		//  r[1] contains any punctuation that was matched, or true, false, or null.
		//  r[2] contains a matched number, still in string form.
		//  r[3] contains a matched string, without quotes but with ecapement.
		
						if (r[1]) {
		
		// Token: Execute the action for this state and token.
		
							action[r[1]][state]();
		
						} else if (r[2]) {
		
		// Number token: Convert the number string into a number value and execute
		// the action for this state and number.
		
							value = +r[2];
							number[state]();
						} else {
		
		// String token: Replace the escapement sequences and execute the action for
		// this state and string.
		
							value = debackslashify(r[3]);
							string[state]();
						}
		
		// Remove the token from the string. The loop will continue as long as there
		// are tokens. This is a slow process, but it allows the use of ^ matching,
		// which assures that no illegal tokens slip through.
		
						source = source.slice(r[0].length);
					}
		
		// If we find a state/token combination that is illegal, then the action will
		// cause an error. We handle the error by simply changing the state.
		
				} catch (e) {
					state = e;
				}
		
		// The parsing is finished. If we are not in the final 'ok' state, or if the
		// remaining source contains anything except whitespace, then we did not have
		//a well-formed JSON text.
		
				if (state !== 'ok' || /[^\x20\t\n\r]/.test(source)) {
					throw state instanceof SyntaxError ? state : new SyntaxError('JSON');
				}
		
		// If there is a reviver function, we recursively walk the new structure,
		// passing each name/value pair to the reviver function for possible
		// transformation, starting with a temporary root object that holds the current
		// value in an empty key. If there is not a reviver function, we simply return
		// that value.
		
				return typeof reviver === 'function' ? (function walk(holder, key) {
					var k, v, value = holder[key];
					if (value && typeof value === 'object') {
						for (k in value) {
							if (Object.prototype.hasOwnProperty.call(value, k)) {
								v = walk(value, k);
								if (v !== undefined) {
									value[k] = v;
								} else {
									delete value[k];
								}
							}
						}
					}
					return reviver.call(holder, key, value);
				}({'': value}, '')) : value;
			};
		}());
	}
	
}());
//Copyright (c) 2010 Morgan Roderick http://roderick.dk
var PubSub = {};
(function(p){
	"use strict";
	p.version = "1.0.1";
	var messages = {};
	var lastUid = -1;
	var publish = function( message, data, sync ){
		if ( !messages.hasOwnProperty( message ) ){
			return false;
		}
		
		var deliverMessage = function(){
			var subscribers = messages[message];
			var throwException = function(e){
				return function(){
					throw e;
				};
			}; 
			for ( var i = 0, j = subscribers.length; i < j; i++ ){
				try {
					subscribers[i].func( message, data );
				} catch( e ){
					setTimeout( throwException(e), 0);
				}
			}
		};
		
		if ( sync === true ){
			deliverMessage();
		} else {
			setTimeout( deliverMessage, 0 );
		}
		return true;
	};
	p.publish = function( message, data ){
		return publish( message, data, false );
	};    
	p.publishSync = function( message, data ){
		return publish( message, data, true );
	};
	p.subscribe = function( message, func ){
		if ( !messages.hasOwnProperty( message ) ){
			messages[message] = [];
		}
		var token = (++lastUid).toString();
		messages[message].push( { token : token, func : func } );
		return token;
	};
	p.unsubscribe = function( token ){
		for ( var m in messages ){
			if ( messages.hasOwnProperty( m ) ){
				for ( var i = 0, j = messages[m].length; i < j; i++ ){
					if ( messages[m][i].token === token ){
						messages[m].splice( i, 1 );
						return token;
					}
				}
			}
		}
		return false;
	};
}(PubSub));

(function(){
// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
	var cache = {};
	this.tmpl = function tmpl(str, data){
		// Figure out if we're getting a template, or if we need to
		// load the template - and be sure to cache the result.
		var fn = !/\W/.test(str) ?
			cache[str] = cache[str] ||
			tmpl(document.getElementById(str).innerHTML) :
			// Generate a reusable function that will serve as a template
			// generator (and which will be cached).
			new Function("obj",
			"var p=[],print=function(){p.push.apply(p,arguments);};" +
			// Introduce the data as local variables using with(){}
			"with(obj){p.push('" +
			// Convert the template into pure JavaScript
			str
				.replace(/[\r\t\n]/g, " ")
				.split("<%").join("\t")
				.replace(/((^|%>)[^\t]*)'/g, "$1\r")
				.replace(/\t=(.*?)%>/g, "',$1,'")
				.split("\t").join("');")
				.split("%>").join("p.push('")
				.split("\r").join("\\'") +
				"');}return p.join('');");
		// Provide some basic currying to the user
		return data ? fn( data ) : fn;
	};
})();


/** 
  * docCookies.setItem(obj, sKey, sValue, vEnd, sPath, sDomain, bSecure) 
  *
  * https://developer.mozilla.org/en/DOM/document.cookie
  *
  * IVN: object into cookie is available
  * 
  * @argument obj (Boolean): flag if object is saved
  * @argument sKey (String): the name of the cookie; 
  * @argument sValue (String): the value of the cookie; 
  * @optional argument vEnd (Number, String, Date Object or null): the max-age in seconds (e.g., 31536e3 for a year) or the 
  *  expires date in GMTString format or in Date Object format; if not specified it will expire at the end of session;  
  * @optional argument sPath (String or null): e.g., "/", "/mydir"; if not specified, defaults to the current path of the current document location; 
  * @optional argument sDomain (String or null): e.g., "example.com", ".example.com" (includes all subdomains) or "subdomain.example.com"; if not 
  * specified, defaults to the host portion of the current document location; 
  * @optional argument bSecure (Boolean or null): cookie will be transmitted only over secure protocol as https; 
  * @return undefined; 
  **/  
var docCookies = {
	getItem: function (sKey, obj) {
		if (!sKey || !this.hasItem(sKey)) {
			return null;
		}
		var out = unescape(
			document.cookie.replace(
				new RegExp(
					"(?:^|.*;\\s*)" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*((?:[^;](?!;))*[^;]?).*"
				),
			"$1")
		);
		if( obj ){
			out = JSON.parse( out );
		}
		return out;
	},
  
	setItem: function (obj, sKey, sValue, vEnd, sPath, sDomain, bSecure) {
		if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/.test(sKey)) {
			return;
		}
		var sExpires = "";

		if (vEnd) {
			switch (typeof vEnd) {  
				case "number":
					sExpires = "; max-age=" + vEnd;
					break;
				case "string":
					sExpires = "; expires=" + vEnd;
					break;
				case "object":
					if (vEnd.hasOwnProperty("toGMTString")) {
						sExpires = "; expires=" + vEnd.toGMTString();
					}
					break;
			}
		}
		if( obj ){
			sValue = JSON.stringify( sValue );
		}
		document.cookie = escape(sKey) + "=" + escape(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");  
	},

	removeItem: function (sKey) {  
	if (!sKey || !this.hasItem(sKey)) {
		return;
	}
	var oExpDate = new Date();  
	oExpDate.setDate(oExpDate.getDate() - 1);
	document.cookie = escape(sKey) + "=; expires=" + oExpDate.toGMTString() + "; path=/";  
	//console.info(escape(sKey) + "=; expires=" + oExpDate.toGMTString() + "; path=/")
	},

	hasItem: function (sKey) {
		return (new RegExp("(?:^|;\\s*)" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
	}
};


/**
 * Проверка является ли строка e-mail
 * 
 * @return {Boolean} 
 */
function isTrueEmail(){
	var t = this.toString(),
		re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(t);
}
String.prototype.isEmail = isTrueEmail; // добавляем методом для всех строк


/**
 * jQuery плагин валидации e-mail'ов
 * 
 * @param  {[type]} $ [description]
 * @return {jQuery object}   [description]
 */
(function($) {
	$.fn.emailValidate = function(params) {

		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.emailValidate.defaults,
							params);
			var $self = $(this);

			var validate = function(e){
				var email = $self.val();
				
				if (email.isEmail()){
					options.onValid();
				}
				else{
					options.onInvalid();
				}
			};

			$self.bind('keyup', validate);
		});
	};

	$.fn.emailValidate.defaults = {
		// callbacks
		onValid: function() {},
		onInvalid: function() {},
	};

})(jQuery);


/**
 * Разбиение числа по разрядам
 *
 * @public
 * @param  {number|string}  число которое нужно отформатировать
 * @return {string}         отформатированное число
 */
var printPrice = function(num){
	var str = num+'';
	return str.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
};


function brwsr () {
	var userag      = navigator.userAgent.toLowerCase();
	this.isAndroid  = userag.indexOf("android") > -1;
	this.isOSX      = ( userag.indexOf('ipad') > -1 ||  userag.indexOf('iphone') > -1 );
	this.isOSX4     = this.isOSX && userag.indexOf('os 5') === -1;
	this.isOpera    = userag.indexOf("opera") > -1;
	
	this.isTouch    = this.isOSX || this.isAndroid;
}		

function parse_url( url ) {
	if( typeof( url ) !== 'string' ){
		return false;
	}
	if( url.indexOf('?') === -1 ){
		return false;
	}
	url = url.replace('?','');
	var url_ar = url.split('&');
	var url_hash = {};
	for (var i=0, l=url_ar.length; i<l; i++ ) {
		var pair = url_ar[i].split('=');
		url_hash[ pair[0] ] = pair[1];
	}
	return url_hash;
}

/* MAP Object */

function MapGoogleWithShops( center, templateIWnode, DOMid, updateIWT ) {
/* Arguments:
	center is a center of a map
	templateIWnode is node(jQ) which include template for InfoWindow popup
	DOMid is selector (id) for google.maps.Map initialization
	updateIWT is a procedure calling each time after marker is clicked
*/
	var self         = this,
		mapWS        = null,
		infoWindow   = null,
		positionC    = null,
		markers      = [],
		currMarker   = null,
		mapContainer = $('#'+DOMid),
		infoWindowTemplate = templateIWnode.prop('innerHTML');

	self.updateInfoWindowTemplate = function( marker ) {
		if( typeof(updateIWT) !== 'undefined' ) {
			updateIWT( marker );
		}
		infoWindowTemplate = templateIWnode.prop('innerHTML');
	};
	
	function create() {
		positionC = new google.maps.LatLng(center.latitude, center.longitude);
		var options = {
			zoom: 11,
			center: positionC,
			scrollwheel: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			}
		};
		mapWS      = new google.maps.Map( document.getElementById( DOMid ), options );
		infoWindow = new google.maps.InfoWindow({
			maxWidth: 400,
			disableAutoPan: false
		});
	}

	this.showInfobox = function( markerId ) {
		if( currMarker ){
			currMarker.setVisible(true); // show preceding marker
		}
		var marker = markers[markerId].ref;
		currMarker = marker;
		var item = markers[marker.id];
		marker.setVisible(false); // hides marker
		self.updateInfoWindowTemplate( item );
		infoWindow.setContent( infoWindowTemplate );
		infoWindow.setPosition( marker.position );
		infoWindow.open( mapWS );
		google.maps.event.addListener( infoWindow, 'closeclick', function() { 
			marker.setVisible(true);
		});
	};
	
	this.hideInfobox = function() {
		infoWindow.close();
	};

	var handlers = [];

	this.addHandlerMarker = function( e, callback ) {
		handlers.push( { 'event': e, 'callback': callback } );
	};
	
	this.showMarkers = function( argmarkers ) {
		mapContainer.show();
		$.each( markers, function(i, item) {
			if( typeof( item.ref ) !== 'undefined' ){
				item.ref.setMap(null);
			}
		});
		markers = argmarkers;
		google.maps.event.trigger( mapWS, 'resize' );
		mapWS.setCenter( positionC );
		var latMax = 0, longMax = 0, latMin = 90, longMin = 90;
		var len = 0;
		$.each( markers, function(i, item) {
			len ++;
			if( item.latitude > latMax ){
				latMax = item.latitude;
			}
			if( item.longitude > longMax ){
				longMax = item.longitude;
			}
			if( item.latitude < latMin ){
				latMin = item.latitude;
			}
			if( item.longitude < longMin ){
				longMin = item.longitude;
			}

			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(item.latitude, item.longitude),
				map: mapWS,
				title: item.name,
				icon: '/images/marker.png',
				id: item.id
			});
			google.maps.event.addListener(marker, 'click', function() {
				self.showInfobox(this.id);
			});
			$.each( handlers, function( h, handler ) {
				google.maps.event.addListener( marker, handler.event, function() {
					handler.callback(item);
				});
			});
			
			markers[marker.id].ref = marker;
		});
		if( len === 1 ) {
			latMin -= 0.001;
			latMin -= 0.001;
			latMax = latMax*1 +  0.001;
			longMax = longMax*1 + 0.001;
		}
		var sw = new google.maps.LatLng( latMin , longMin );
		var ne = new google.maps.LatLng( latMax , longMax );
		var bounds = new google.maps.LatLngBounds(sw, ne);
		if( len ){
			mapWS.fitBounds(bounds);
		}
	};

	this.closeMap = function( callback ) {
		infoWindow.close();
		mapContainer.hide('blind', null, 800, function() {
			if( callback ){
				callback();
			}
		});
	};

	this.closePopupMap = function( callback ) {
		infoWindow.close();
		if( callback ){
			callback();
		}
	};
			
	this.addHandler = function( selector, callback ) {
		mapContainer.delegate( selector, 'click', function(e) { //desktops			
			e.preventDefault();
			callback( e.target );
		});
		var bw = new brwsr();
		if( bw.isTouch ){
			mapContainer[0].addEventListener("touchstart",  //touch devices
			function(e) {
				e.preventDefault();
				if( e.target.is( selector ) ){
					callback( e.target );
				}
			} , false);
		}							
	};

	/* main() */
	create();

} // object MapGoogleWithShops

function MapYandexWithShops( center, templateIWnode, DOMid ) {
/* Arguments:
	center is a center of a map
	templateIWnode is node(jQ) which include template for InfoWindow popup
	DOMid is selector (id) for google.maps.Map initialization
*/
	var self         = this,
		mapWS        = null,
		infoWindow   = null,
		positionC    = null,
		markers      = [],
		currMarker   = null,
		mapContainer = $('#'+DOMid),
		infoWindowTemplate = templateIWnode.prop('innerHTML');
	
	this.updateInfoWindowTemplate = function( marker ) {
		// if( updateInfoWindowTemplate )
		//     updateInfoWindowTemplate( marker )
		// infoWindowTemplate = templateIWnode.prop('innerHTML')   
	};
	
	function create() {
		mapWS = new ymaps.Map(DOMid, {
			center: [center.latitude, center.longitude],
			zoom: 10
		});
		
		mapWS.controls
		.add('zoomControl');
		// setTimeout( function() {
		//     mapWS = new ymaps.Map(DOMid, {
		//         center: [center.latitude, center.longitude],
		//         zoom: 10
		//     })
			
		//     mapWS.controls
		//     .add('zoomControl')
		//     //.add('typeSelector', { left: 5, top: 15 })// Список типов карты
		// }, 1200)        
	}

	this.showInfobox = function( markerId ) {
		markers[markerId].ref.balloon.open();
	};
	
	this.hideInfobox = function() {
		// infoWindow.close()
	};

	var handlers = [];

	this.addHandlerMarker = function( e, callback ) {
		// handlers.push( { 'event': e, 'callback': callback } )
	};
	
	this.clear = function() {
		mapWS.geoObjects.each( function( mapObj ) {
			mapWS.geoObjects.remove(mapObj);
		});
	};

	this.showMarkers = function( argmarkers ) {   
		// console.info(argmarkers)
		mapContainer.show();
		mapWS.container.fitToViewport();
		mapWS.setCenter([center.latitude, center.longitude]);
		self.clear();
		markers = argmarkers;
		var myCollection = new ymaps.GeoObjectCollection();
		$.each( markers, function(i, item) {           
			// Создаем метку и задаем изображение для ее иконки
			var tmpitem = {
				id: item.id,
				name: item.name,
				address: item.address,
				link: item.link,
				regtime: (item.regtime) ? item.regtime : item.regime,
				regime: (item.regtime) ? item.regtime : item.regime
			};
			var marker = new ymaps.Placemark( [item.latitude, item.longitude], tmpitem, {
					iconImageHref: '/images/marker.png', // картинка иконки
					iconImageSize: [39, 59], 
					iconImageOffset: [-19, -57] 
				}
			);
			myCollection.add(marker);
			markers[item.id].ref = marker;
		});
// console.info(markers)        
		// Создаем шаблон для отображения контента балуна         
		var myBalloonLayout = ymaps.templateLayoutFactory.createClass(templateIWnode.prop('innerHTML').replace(/<%=([a-z]+)%>/g, '$[properties.$1]'));
		
		// Помещаем созданный шаблон в хранилище шаблонов. Теперь наш шаблон доступен по ключу 'my#superlayout'.
		ymaps.layout.storage.add('my#superlayout', myBalloonLayout);
		// Задаем наш шаблон для балунов геобъектов коллекции.
		myCollection.options.set({
			balloonContentBodyLayout:'my#superlayout',
			// Максимальная ширина балуна в пикселах
			balloonMaxWidth: 350
		});
		mapWS.geoObjects.add( myCollection );
		var bounds = myCollection.getBounds(); 
		if( bounds[0][0] !== bounds[1][0] ){ // cause setBounds() hit a bug if only one point  
			mapWS.setBounds( bounds );
		}
		else{
			$.each( markers, function(i, item) {
				mapWS.setCenter([markers[i].latitude, markers[i].longitude], 14);
			});
		}
	};

	this.showCluster = function( argmarkers ){
		// console.log('cluster!')
		// mapContainer.show()
		// mapWS.container.fitToViewport()
		mapWS.setCenter([center.latitude, center.longitude]);
		self.clear();
		var dots = argmarkers;
		var clusterer = new ymaps.Clusterer({clusterDisableClickZoom: false, maxZoom:8, synchAdd:true, minClusterSize:1});
		$.each( dots, function(i, item) {           
			// Создаем метку и задаем изображение для ее иконки
			var tmpitem = {
				id: item.id,
				name: item.name,
				address: item.address,
				link: item.link,
				regtime: (item.regtime) ? item.regtime : item.regime,
				regime: (item.regtime) ? item.regtime : item.regime
			};
			var marker = new ymaps.Placemark( [item.latitude, item.longitude], tmpitem, {
					iconImageHref: '/images/marker.png', // картинка иконки
					iconImageSize: [39, 59], 
					iconImageOffset: [-19, -57] 
				}
			);
			clusterer.add(marker);
			dots[i].ref = marker;
			// console.log(dots)
		});
		var myBalloonLayout = ymaps.templateLayoutFactory.createClass(
			templateIWnode.prop('innerHTML').replace(/<%=([a-z]+)%>/g, '$[properties.$1]')
		);
		
		// Помещаем созданный шаблон в хранилище шаблонов. Теперь наш шаблон доступен по ключу 'my#superlayout'.
		ymaps.layout.storage.add('my#superlayout', myBalloonLayout);
		// Задаем наш шаблон для балунов геобъектов коллекции.
		clusterer.options.set({
			balloonContentBodyLayout:'my#superlayout',
			// Максимальная ширина балуна в пикселах
			balloonMaxWidth: 350
		});
		mapWS.geoObjects.add(clusterer);
		mapWS.setZoom(4);
	};

	this.chZoomCenter = function( center, zoom ) {
		mapWS.setCenter([center.latitude, center.longitude], zoom, { checkZoomRange: true, duration:800 } );
	};

	this.closeMap = function( callback ) {
		// infoWindow.close()
		mapContainer.hide('blind', null, 800, function() {
			if( callback ){
				callback();
			}
		});
	};

	this.closePopupMap = function( callback ) {
		// infoWindow.close()
		if( callback ){
			callback();
		}
	};
			
	this.addHandler = function( selector, callback ) {
		mapContainer.delegate( selector, 'click', function(e) { //desktops          
			e.preventDefault();
			callback( e.target );
		});
		var bw = new brwsr();
		if( bw.isTouch ){
			mapContainer[0].addEventListener("touchstart",  //touch devices
			function(e) {
				e.preventDefault();
				if( e.target.is( selector ) ){
					callback( e.target );
				}
			} , false);
		}                   
	};

	/* main() */
	create();

} // object MapYandexWithShops

function MapOnePoint( position, nodeId ) {
	if( !position ){
		return false;
	}
	if( !position.longitude || !position.latitude ){
		return false;
	}
	var self = this;

	var markerPreset = {
		iconImageHref: '/images/marker.png',
		iconImageSize: [39, 59], 
		iconImageOffset: [-19, -57] 
	};

	if ($('#staticYMap').length){ //static map for printPage
		var url = "http://static-maps.yandex.ru/1.x/?";
		var statType = 'l=map';
		var statCord = 'll='+position.longitude+','+position.latitude;
		var statZoom = 'spn=0.004,0.004';
		var statSize = 'size=650,450'; // it's max value :`(
		var statPlacemark = 'pt='+position.longitude+','+position.latitude+',pm2dol';
		var src = url+statCord+'&'+statZoom+'&'+statType+'&'+statSize+'&'+statPlacemark;
		$('#staticYMap img').attr('src',src);
	}

	self.yandex = function() {
		var point = [ position.latitude*1 , position.longitude*1 ];
		var myMap = new ymaps.Map ( nodeId, {
			center: point,
			zoom: 16
		});
		myMap.controls.add('zoomControl');

		var myPlacemark = new ymaps.Placemark( point, {}, markerPreset);
		myMap.geoObjects.add(myPlacemark);
		
		myMap.zoomRange.get( point ).then( function (range) {
			myMap.setZoom( range[1] );
		});
	};

	self.google = function() {
		var options = {
			zoom: 16,
			// center: position,
			scrollwheel: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			}
		};
		
		var point = new google.maps.LatLng( position.latitude , position.longitude );
		options.center = point;
		var map = new google.maps.Map(document.getElementById( nodeId ), options);

		var marker = new google.maps.Marker({
			position: point,
			map: map,
			icon: markerPreset.iconImageHref
		});
	};

} // object MapOnePoint

function calcMCenter( shops ) {
	var latitude = 0, longitude = 0, l = 0;
	for(var i in shops ) {
		latitude  += shops[i].latitude*1;
		longitude += shops[i].longitude*1;
		l++;
	}
	var mapCenter = {
		latitude  : latitude / l,
		longitude : longitude / l
	};
	return mapCenter;
}

window.MapInterface = (function() {
	var vendor, tmplSource;

	return {
		ready: function( vendorName, tmpl) {
			var mapReady = $.Deferred();
			vendor     = vendorName;
			tmplSource = tmpl;
			if( vendor==='yandex' ) {
				ymaps.ready( function() {
					// console.info('yandexIsReady')            
					PubSub.publish('yandexIsReady');
					ymaps.isReady = true;
					mapReady.resolve();
				});
			}
			return mapReady.promise();
			// if( vendor==='google' ) {
			//      $LAB.sandbox().script( 'http://maps.google.com/maps/api/js?sensor=false' )
			// } else // $LAB.sandbox().script( 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU' ).wait( function() {
		},

		init: function( coordinates, mapContainerId, callback, updater ) {
			// console.log('инитимся..', coordinates, mapContainerId, callback, updater)
			if( vendor === 'yandex' ) {
				if( typeof(ymaps)!=='undefined') {
					// console.info('1')
					window.regionMap = new MapYandexWithShops(
						coordinates,
						tmplSource.yandex, 
						mapContainerId
					);
					if( typeof( callback ) !== 'undefined' ) {
						callback();
					}
				}
				else {
					// console.info('2')
					PubSub.subscribe( 'yandexIsReady', function() {
						window.regionMap = new MapYandexWithShops( 
							coordinates,
							tmplSource.yandex,
							mapContainerId
						);
						if( typeof( callback ) !== 'undefined' ) {
							callback();
						}
					});
				}
			}
			if( vendor === 'google' ) {        
				window.regionMap = new MapGoogleWithShops(
					coordinates,
					tmplSource.google,
					mapContainerId,
					updater
				);
				if( typeof( callback ) !== 'undefined' ) {
					callback();
				}
			}
		},

		onePoint: function( coordinates, mapContainerId ) {
			var mtmp = new MapOnePoint( coordinates, mapContainerId );
			
			if( vendor === 'yandex' ) {
				if( typeof(ymaps)!=='undefined' && ymaps.isReady ) {
					mtmp[vendor]();
				}
				else {
					PubSub.subscribe('yandexIsReady', function() {
						mtmp[vendor]();
					});
				}
			} 
			if( vendor === 'google' ) {
				mtmp[vendor]();
			}
		},

		getMapContainer: function() {
			// TODO
			// return window.regionMap
		}

		// TODO wrap fn calcMCenter as a method
	};
}() ); // singleton

/*
	Mechanics @ enter.ru 
	(c) Ivan Kotov, Enter.ru
	v 0.5

	jQuery is prohibited
							*/
function Flybox( parent ){
// TODO
//для конкретных блоков всплытия нужны гиперссылки

	if(! $(parent).length ) {
		return null;
	}
		
	var box = $('<div>').addClass('flybox').css('display','none');
	var crnr = $('<i>').addClass('corner').appendTo( box );
	var close = $('<i>').addClass('close').attr('title','Закрыть').html('Закрыть').appendTo( box );
	box.appendTo( parent );
	
	var self = this;
	var hidei = 0;
	var thestuff = null;
	
	close.bind('click', function(){
		clearTimeout( hidei );
		self.jinny();
	});
	
	this.updateItem = function( item ) {
		thestuff = item;
	};
	
	var basket  = '';
	var wishes  = '';
	var rcmndtn = '';

	
	this.showWishes = function() {
		wishes = 
			'<div class="font16 pb20">Только что был добавлен в список желаний</div>'+
			'<div class="fl width70">'+
				'<a href="">'+
					'<img width="60" height="60" alt="" src="'+ thestuff.img +'">'+
				'</a>'+
			'</div>'+
			'<div class="ml70">'+
				'<div class="pb5">'+
					'<a href="">'+ thestuff.title +'</a>'+
				'</div>'+
				'<strong>'+
					thestuff.price +
					'<span class="rubl">p</span>'+
				'</strong>'+
			'</div>'+
			'<div class="clear pb10"></div>'+
			'<div class="line pb5"></div>'+
			'<div class="ar pb10">Всего товаров: '+ thestuff.vwish +'</div>'+
			'<div class="ar">'+
				'<a class="button bigbuttonlink" value="" href="">Перейти в список желаний</a>'+
			'</div>	';
	
		box.css({'left':'400px','width':'290px'});
		crnr.css('left','132px');
		this.fillup ( wishes );
		box.fadeIn(1000);
		hidei = setTimeout( self.jinny, 5000 );
	};

	this.showBingo = function() {
		rcmndtn = 
			'<div class="font16 pb20">Этот товар может пригодиться!</div>'+
			'<div class="fl width70">'+
			'<a href="">'+
			'<img width="60" height="60" alt="" src="'+ thestuff.img +'">'+
			'</a>'+
			'</div>'+
			'<div class="ml70">'+
			'<div class="pb5">'+
			'<a href="">'+ thestuff.title +'</a>'+
			'</div>'+
			'<div class="pb10">'+
			'<strong>'+
			thestuff.price +
			'<span class="rubl">p</span>'+
			'</strong>'+
			'</div>'+
			'<input class="button yellowbutton" type="button" value="Купить"></div>';
			
		box.css({'left':'3px','width':'250px'});
		crnr.css('left','27px');
		this.fillup (rcmndtn);
		box.fadeIn(1000);
		hidei = setTimeout( self.jinny, 5000 );
	};

	this.showComparing = function() {
		box.css({'left':'3px','width':'874px'});
		crnr.css('left','374px');
		this.fillup ( $('#zaglu').html() );
		box.fadeIn(1000);
		hidei = setTimeout( self.jinny, 7000 );
	};

	var flyboxcloser = function(e){
		var targ = e.target.className;

		if (!(targ.indexOf('flybox')+1) || !(targ.indexOf('fillup')+1)) {
			box.hide();
			$('body').unbind('click', flyboxcloser);
		}
	};


	var hrefcart = $('.point2', parent).attr('href'); //OLD: /orders/new
	this.showBasket = function( f1 ) {
		if( typeof( thestuff.link ) !== 'undefined' ) {
			hrefcart = thestuff.link;
		}
		var f1tmpl = '';
		if ( typeof(f1) !== "undefined" ){
			f1tmpl = 
				'<br/><div class="bLiteboxF1">'+
					'<div class="fl width70 bLiteboxF1__eWrap">'+
						'<div class="bLiteboxF1__ePlus">+</div>'+
						'<a href=""><img src="/images/f1info1.png" alt="" width="60" height="60" /></a></div>'+
					'<div class="ml70">'+
						'<div class="pb5 bLiteboxF1__eG"><a href>'+ f1.f1title +'</a></div>'+
						'<strong>'+ f1.f1price +' <span class="rubl">p</span></strong>'+
					'</div>'+
				'</div>';
		}
		basket = 
			'<div class="font16 pb20">Только что был добавлен в корзину:</div>'+
			'<div class="fl width70">'+
				'<a href="">'+
					'<img width="60" height="60" alt="" src="'+ thestuff.img +'">'+
				'</a>'+
			'</div>'+
			'<div class="ml70">'+
				'<div class="pb5">'+
					'<a href="">'+ thestuff.title +'</a>'+
				'</div>'+
				'<strong>'+
					thestuff.price +
					'<span> &nbsp;</span><span class="rubl">p</span>'+
				'</strong>'+
			'</div>'+ f1tmpl +
			'<div class="clear pb10"></div>'+
			'<div class="line pb5"></div>'+
			'<div class="fr">Сумма: '+ thestuff.sum +' Р</div>'+
			'Всего товаров: '+ thestuff.vitems +
			'<div class="clear pb10"></div>'+
			'<div class="ar">'+ 
				'<a class="button bigbuttonlink" value="" href="'+ hrefcart +'">Оформить заказ</a>'+
			'</div>';
	
		box.css({'left':'605px','width':'290px'});
		crnr.css('left','142px');
		this.fillup (basket);
		box.fadeIn(500);
		// hidei = setTimeout( self.jinny, 5000 );
		$('body').bind('click', flyboxcloser);
	};

	this.showBasketF1 = function( f1 ) {
		if ( typeof(f1) === "undefined" ){
			return false;
		}
		var f1tmpl = 
			'<div class="bLiteboxF1">'+
				'<div class="fl width70 bLiteboxF1__eWrap">'+
					'<div class="bLiteboxF1__ePlus"></div>'+
					'<a href=""><img src="/images/f1info1.png" alt="" width="60" height="60" /></a></div>'+
				'<div class="ml70">'+
					'<div class="pb5 bLiteboxF1__eG"><a href>'+ f1.f1title +'</a></div>'+
					'<strong>'+ f1.f1price +' <span class="rubl">p</span></strong>'+
				'</div>'+
			'</div>';
		basket = 
			'<div class="font16 pb20">Только что был добавлен в корзину:</div>'+
			f1tmpl +
			'<div class="clear pb10"></div>'+
			'<div class="line pb5"></div>'+
			'<div class="fr">Сумма: '+ thestuff.sum +' Р</div>'+
			'Всего товаров: '+ thestuff.vitems +
			'<div class="clear pb10"></div>'+
			'<div class="ar">'+ 
				'<a class="button bigbuttonlink" value="" href="'+ hrefcart +'">Оформить заказ</a>'+
			'</div>';
	
		box.css({'left':'588px','width':'290px'});
		crnr.css('left','132px');
		this.fillup (basket);
		box.fadeIn(500);
		// hidei = setTimeout( self.jinny, 5000 );
		$('body').bind('click', flyboxcloser);
	};
	
	this.fillup = function( nodes ) {
		var tmp = $('<div>').addClass('fillup').html( nodes );
		box.append( tmp );
	};
	
	this.jinny = function() {		
		box.fadeOut(500);
		setTimeout( function() {
			$('.fillup', box).remove();
		} , 550);
	};

	this.clear = function() {		
		clearTimeout(hidei);
		box.hide();
		$('.fillup', box).remove();
	};
} // Flybox object

function Lightbox( jn, data ){
	if(! $(jn).length ) {
		return null;
	}
	var self = this;
	
	var init = data;
	var plashka = jn;
	var bingobox = null;
	var flybox = null;
	var firedbox = 0;
	
	this.save = function() {
		var cooka = init;
		cooka.basket={};
		docCookies.setItem( true, 'Lightbox', cooka, 20*60, '/' );
	};
	
	this.restore = function() {
		return docCookies.getItem('Lightbox', true);
	};
	
	if( ! init.name ) {
		//init = this.restore()
		if( !init ) {
			init = {
				'name':false,
				'vcomp':0, // число сравниваемых
				'vwish':0, // число товаров в вишлисте
				'vitems': 0, // число покупок
				'sum': 0, // текущая сумма покупок
				'bingo': {}
			};
		}
	}	
	
	this.getBasket = function( item ) {
		item.price +='';
		var _gafrom = ( $('.goodsbarbig').length ) ? 'product' : 'catalog';
		if ( typeof(_gaq) !== 'undefined') {
			_gaq.push(['_trackEvent', 'Add2Basket', _gafrom, item.title + ' ' + item.id, Math.round( item.price.replace(/\D/g,'') ) ]);
		}
		flybox.clear();
		item.price = item.price.replace(/\s+/,'');
		init.basket = item;
		init.sum = item.sum * 1;
		//if ( parseInt(init.sum) == parseInt(item.price) )
			$('.total').show();
		item.sum = printPrice ( init.sum );
		item.price = printPrice ( item.price );
		//init.vitems++
		//item.vitems = init.vitems
		init.vitems = item.vitems;
		flybox.updateItem( item );				
		$('#sum', plashka).html( item.sum );
		$('.point2 b', plashka).html( item.vitems );
		this.fillTopBlock();
		if( 'f1' in item ) {
			if( 'only' in item.f1  ) {
				flybox.showBasketF1( item.f1 );
			}
			else {
				flybox.showBasket( item.f1 );
			}
		}
		else {
			flybox.showBasket();
		}
		//self.save()
	};

	this.getWishes = function( item ) {	
		flybox.clear();
		item.price = item.price.replace(/\s/,'');
		item.price = printPrice ( item.price );
		init.wishes = item;
		init.vwish++;
		item.vwish = init.vwish;
		flybox.updateItem( item );
		$('.point3 b', plashka).html(init.vwish);
		flybox.showWishes();
	};

	this.bingo = function( item ) {
		if( flybox ) {
			flybox.clear();
		}
		item.price = printPrice ( item.price );
		init.bingo = item;
		bingobox.updateItem( item );
		bingobox.showBingo();
	};

	this.getComparing = function() {	
		flybox.clear();
		if (bingobox) {
			bingobox.clear();
		}
		flybox.showComparing();
	};
	
	this.clear = function() {
		flybox.clear();
		if (bingobox) {
			bingobox.clear();
		}
	};
	
	this.getContainers = function() {
		$('.dropbox', plashka).show();
	};
	
	this.hideContainers = function() {
		$('.dropbox', plashka).hide();
	};
	
	this.toFire = function( i ) {
		//if( firedbox )
		//	self.putOut( firedbox )
		firedbox = i;
		//$($('.dropbox', plashka)[i - 1]).addClass('active').find('p').html('Отпустите мышь')
		$('.dropbox', plashka).addClass('active').find('p').html('Отпустите мышь');
	};
	
	this.putOut = function( i ) {
		$($('.dropbox', plashka)[i - 1]).removeClass('active').find('p').html('Перетащите сюда');
	};
	
	this.putOutBoxes = function() {
		if( firedbox ){
			for(var i = 1; i < 4; i++ ) {
				self.putOut( i );
			}
			firedbox = 0;
		}
	};
	
	this.gravitation = function( ) {
		if( firedbox ) {	
			return firedbox;
		}
		else {
			return false;
		}
	};
	
	this.fillTopBlock = function() {
		if( $('#topBasket') ) {
			$('#topBasket').text( '('+init.vitems+')' );
		}
	};
	
	this.update = function( newinit ) {
		if ( newinit ) {
			init = newinit;
		}
		if( init  ) {
			if( init.name ) {
				$('.fl .point', plashka).removeClass('point1').addClass('point6').html('<b></b>' + init.name);
			}
			if( init.link ) {
				$('.point6', plashka).attr('href', init.link );
			}
			if( init.vcomp ) {
				$('.point4 b', plashka).html(init.vcomp);
			}
			if( init.vwish ) {
				$('.point3 b', plashka).html(init.vwish);
			}		
			if( init.sum ) {
				$('#sum', plashka).html( printPrice(init.sum ) );
				$('.total').show();
			}		
			if( init.vitems ) {
				$('.point2', plashka).addClass('orangeme');
				$('.point2 b', plashka).html(init.vitems);
				this.fillTopBlock();
			}		
			if ( init.bingo && init.bingo.id ){
				var li = $('<li>').addClass('fl').html(
					'<a class="point point5" href="">'+
					'<b></b></a>' );
				$('.lightboxmenu').prepend( li );
				li.bind('click', function(){
					self.bingo( init.bingo );
					return false;
				});
				bingobox = new Flybox( jn );
				self.bingo( init.bingo );
			}
		}
	};
	
	this.authorized = function(){
		if( init.name ) {
			return true;
		}
		else {
			return false;
		}
	};

	this.isCredit = function(){
		if( 'is_credit' in init ) {
			if( init.is_credit ) {
				return true;
			}
		}
		return false;
	};

	// initia
	this.update();
	//setTimeout( function () { plashka.fadeIn('slow') }, 2000)
	flybox = new Flybox( jn );
	
}


/**
 * Создает объект для обновления данных с сервера и отображения текущих покупок
 *
 * @author	Zaytsev Alexandr
 * @this	{BlackBox}
 * @param	{String}		updateUrl URL по которому будут запрашиватся данные о пользователе и корзине.
 * @param	{jQuery node}	mainNode  DOM элемент бокса
 * @constructor
 */
var BlackBox = function(updateUrl, mainContatiner){
	this.updUrl = (!docCookies.hasItem('enter') ||  !docCookies.hasItem('enter_auth')) ? updateUrl += '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000) : updateUrl;
	this.mainNode = mainContatiner;
};

/**
 * Объект по работе с корзиной
 *
 * @author	Zaytsev Alexandr
 * @this	{BlackBox}
 * @return	{function} update	обновление данных о корзине
 * @return	{function} add		добавление в корзину
 */
BlackBox.prototype.basket = function() {
	var self = this;

	var headQ = $('#topBasket');
	var bottomQ = self.mainNode.find('.bBlackBox__eCartQuan');
	var bottomSum = self.mainNode.find('.bBlackBox__eCartSum');
	var total = self.mainNode.find('.bBlackBox__eCartTotal');
	var bottomCart = self.mainNode.find('.bBlackBox__eCart');
	var flyboxBasket = self.mainNode.find('.bBlackBox__eFlybox.mBasket');
	var flyboxInner = self.mainNode.find('.bBlackBox__eFlyboxInner');


	/**
	 * Уничтожение содержимого flybox и его скрытие
	 *
	 * @author	Zaytsev Alexandr
	 * @private
	 */
	var flyboxDestroy = function(){
		flyboxBasket.fadeOut(300, function(){
			flyboxInner.remove();
		});
	};

	/**
	 * Закрытие flybox по клику
	 * 
	 * @author	Zaytsev Alexandr
	 * @param	{Event} e
	 * @private
	 */
	var flyboxcloser = function(e){
		var targ = e.target.className;

		if (!(targ.indexOf('bBlackBox__eFlybox')+1) || !(targ.indexOf('fillup')+1)) {
			flyboxDestroy();
			$('body').unbind('click', flyboxcloser);
		}
	};

	/**
	 * Обновление данных о корзине
	 *
	 * @author	Zaytsev Alexandr
	 * @param	{Object} basketInfo			Информация о корзине
	 * @param	{Number} basketInfo.cartQ	Количество товаров в корзине
	 * @param	{Number} basketInfo.cartSum	Стоимость товаров в корзине
	 * @public
	 */
	var update = function(basketInfo) {
		headQ.html('('+basketInfo.cartQ+')');
		bottomQ.html(basketInfo.cartQ);
		bottomSum.html(basketInfo.cartSum);
		bottomCart.addClass('mBought');
		total.show();
	};

	/**
	 * Добавление товара в корзину
	 *
	 * @author	Zaytsev Alexandr
	 * @param	{Object} item
	 * @param	{String} item.title			Название товара
	 * @param	{Number} item.price			Стоимость товара
	 * @param	{String} item.imgSrc		Ссылка на изображение товара
	 * @param	{Number} item.TotalQuan		Общее количество товаров в корзине
	 * @param	{Number} item.totalSum		Общая стоимость корзины
	 * @param	{String} item.linkToOrder	Ссылка на оформление заказа
	 * @public
	 */
	var add = function(item) {
		var flyboxTmpl = tmpl('blackbox_basketshow_tmpl', item);

		flyboxDestroy();
		flyboxBasket.append(flyboxTmpl);
		flyboxBasket.show(300);

		var nowBasket = {
			cartQ: item.totalQuan,
			cartSum: item.totalSum
		};

		self.basket().update(nowBasket);

		$('body').bind('click', flyboxcloser);

	};

	return {
		'update': update,
		'add': add
	};
};

/**
 * Объект по работе с данными пользователя
 *
 * @author	Zaytsev Alexandr
 * @this	{BlackBox}
 * @return	{function} update
 */
BlackBox.prototype.user = function() {
	var self = this;

	/**
	 * Обновление пользователя
	 *
	 * @author	Zaytsev Alexandr
	 * @param	{String} userName Имя пользователя
	 * @public
	 */
	var update = function(userName) {
		var topAuth = $('#auth-link');
		var bottomAuth = self.mainNode.find('.bBlackBox__eUserLink');

		if (userName !== '') {
			var dtmpl={
				user: userName
			};
			var show_user = tmpl('auth_tmpl', dtmpl);

			topAuth.hide();
			topAuth.after(show_user);
			bottomAuth.html(userName).addClass('mAuth');
		}
		else {
			topAuth.show();
			
		}
	};

	return {
		'update': update
	};
};


/**
 * Инициализация BlackBox.
 * Получение данных о корзине и пользователе с сервера.
 *
 * @author	Zaytsev Alexandr
 * @this	{BlackBox}
 */
BlackBox.prototype.init = function() {
	var self = this;

	/**
	 * Обработчик Action присланных с сервера
	 * 
	 * @param	{Object} action Список действий которые необходимо выполнить
	 * @private
	 */
	var startAction = function(action) {
		if (action.subscribe !== undefined) {
			//  TODO: перевести action на события
			// lboxCheckSubscribe(action.subscribe);
		}
	};

	/**
	 * Обработчик данных о корзине и пользователе
	 * 
	 * @param	{Object} data
	 * @private
	 */
	var parseUserInfo = function(data) {
		if (data.success !== true) {
			return false;
		}

		var userInfo = data.data;

		self.user().update(userInfo.name);

		if (userInfo.vitems !== 0) {
			var nowBasket = {
				cartQ: userInfo.vitems,
				cartSum: userInfo.sum
			};

			self.basket().update(nowBasket);
		}

		if (userInfo.action !== undefined) {
			startAction(userInfo.action);
		}
	};

	$.get(self.updUrl, parseUserInfo);
};

/**
 * Создание и иницилизация объекта для работы с корзиной и данными пользователя
 * @type	{BlackBox}
 */
var blackBox = new BlackBox('/user/shortinfo', $('.bBlackBox__eInner'));
blackBox.init();



var ltbx = null;

function mediaLib( jn ) {
	if ( ! jn.length ) {
		return;
	}
	var self = this;
	var popup = jn;
	var gii = null;
	var running360 = false;
	var vis = false;
	
	this.show = function( ntype, url ) {
		if (! vis ) {
			var currentfunction = function(){};
			switch ( ntype ) {
				case 'image':
					currentfunction = self.openEnormous;
					break;
				case '360':
					currentfunction = self.open360;
					break;
			}
			
			$(popup).lightbox_me({
				centered: true, 
				onLoad: function() {
						currentfunction( url );
					},
				onClose: function() {
						self.close();
						vis = false;
					},
				reallyBig: true	
			});
			vis = true;
		}
		else { // toggle
			self.close();
			switch ( ntype ) {
				case 'image':
					$('<img>').attr('src', url ).attr('id','gii').appendTo($('.photobox', popup));
					gii = new gigaimage( $('#gii'), 2,  $('.scale', popup));
					gii.addZoom();
					break;
				case '360':
					if( ! running360 ){					
						if( typeof(lkmv.start)!=='undefined' ) {
							lkmv.start();
						}
						running360 = true;
					}
					else{
						if( typeof(lkmv.show)!=='undefined' ) {
							lkmv.show();
						}
					}
					break;
			}
		}
		
		return false;
	};
	
	this.close = function() {
		if ( gii ) {
			gii.destroy();
			gii = null;		
			$('#gii').remove();
		}
		if ( running360 && lkmv ) {	
			if( typeof(lkmv.hide)!=='undefined' ) {
				lkmv.hide();
			}
		}
	};
	
	this.openEnormous = function( url ) {				
		$('<img>').attr('src', url ).attr('id','gii').appendTo($('.photobox', popup));
		gii = new gigaimage( $('#gii'), 2,  $('.scale', popup));
		gii.addZoom();
	};
	
	this.open360 = function() {	
		if( ! running360 ){					
			if( typeof(lkmv.start)!=='undefined' ) {
				lkmv.start();
			}
			running360 = true;
		} else
			if( typeof(lkmv.show)!=='undefined' ) {
				lkmv.show();
			}
	};
	
} // mediaLib object

/* Credit Brokers */
var DirectCredit = {

	basketPull : [],

	output : null,
	input  : null,

	init : function( input, output ) {
		if( !input || !output ) {
			return 'incorrect input data';
		}
		this.input  = input;
		this.output = output;
		for( var i=0, l=input.length; i < l; i++ ) {
			var tmp = {
				id : input[i].id,
				price : input[i].price,
				count : input[i].quantity,
				type : input[i].type
			};
			
			this.basketPull.push( tmp );
		}
		this.sendCredit();
	},

	change : function( message, data ) {
		var self = DirectCredit;
		if( data.q > 0 ) {
			var item = self.findProduct( self.basketPull, data.id );
			if( item < 0 ) {
				PubSub.publish( 'bankAnswered', null ); // hack
				return;
			}
			item.count = data.q;
		} else {
			var key = self.findProductKey( self.basketPull, data.id );
			if( key < 0 ) {
				PubSub.publish( 'bankAnswered', null ); // hack
				return;
			}
			self.basketPull.splice( key, 1 );
		}
		self.sendCredit();
	},

	findProduct : function( array, id) {
		for( var key=0, lk=array.length; key < lk; key++ ) {
			if( array[key].id == id ) {
				return array[key];
			}
		}
		return -1;
	},

	findProductKey : function( array, id) {
		for( var key=0, lk=array.length; key < lk; key++ ) {
			if( array[key].id == id ) {
				return key;
			}
		}
		return -1;
	},
	
	sendCredit : function(  ) {
		var self = this;
		dc_getCreditForTheProduct(
			'4427',
			'none',
			'getPayment', 
			{ products : self.basketPull },
			function(result){                       
				//var creditPrice = 0
				// for( var i=0, l=self.basketPull.length; i < l; i++ ) {
				//  var item = self.findProduct( self.basketPull, result.products[i].id )
				//  if( item ) {
				//      var itemPrice = item.price
				//      creditPrice += result.products[i].initial_instalment * itemPrice/100 * item.count
				//  }
					
				// }               
				self.output.text( printPrice( Math.ceil( result.payment ) ) );
				PubSub.publish( 'bankAnswered', null );
			}
		);
	}
}; // DirectCredit singleton


/* Date object upgrade */
if ( !Date.prototype.toISOString ) {
	
	( function() {
	
		function pad(number) {
			var r = String(number);
			if ( r.length === 1 ) {
				r = '0' + r;
			}
			return r;
		}
 
		Date.prototype.toISOString = function() {
			return this.getUTCFullYear() +
				'-' + pad( this.getUTCMonth() + 1 ) +
				'-' + pad( this.getUTCDate() ) +
				'T' + pad( this.getUTCHours() ) +
				':' + pad( this.getUTCMinutes() ) +
				':' + pad( this.getUTCSeconds() ) +
				'.' + String( (this.getUTCMilliseconds()/1000).toFixed(3) ).slice( 2, 5 ) +
				'Z';
		};
  
	}() );
}

function parseISO8601(dateStringInRange) {
	var isoExp = /^\s*(\d{4})-(\d\d)-(\d\d)\s*$/,
		date = new Date(NaN), month,
		parts = isoExp.exec(dateStringInRange);

	if (parts) {
		month = +parts[2];
		date.setFullYear(parts[1], month - 1, parts[3]);
		if (month != date.getMonth() + 1) {
			date.setTime(NaN);
		}
	}
	return date.getTime();
} 

