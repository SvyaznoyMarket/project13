;(function( ENTER ) {
	var utils = ENTER.utils;

	utils.cloneObject = function cloneObject( obj ) {
		if  ( obj == null || typeof( obj ) !== 'object' ) {
			return obj;
		}

		var temp = {},
			key;

		for ( key in obj ) {
			if ( obj.hasOwnProperty(key) ) {
				temp[key] = cloneObject(obj[key]);
			}
		}

		return temp;
	};
}(window.ENTER));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Получение количества свойств объекта
 */
;(function ( global ) {
	global.getKeysLength = function getKeysLength( obj ) {
		var len = 0;
		
		for ( var i in obj ) {
			if ( !obj.hasOwnProperty(i) ){
				continue;
			}
			
			len++;
		}
		
		return len;
	};
}(this));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/*
    json2.js
    2013-05-26

    Public Domain.

    NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.

    See http://www.JSON.org/js.html


    This code should be minified before deployment.
    See http://javascript.crockford.com/jsmin.html

    USE YOUR OWN COPY. IT IS EXTREMELY UNWISE TO LOAD CODE FROM SERVERS YOU DO
    NOT CONTROL.


    This file creates a global JSON object containing two methods: stringify
    and parse.

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


    This is a reference implementation. You are free to copy, modify, or
    redistribute.
*/

/*jslint evil: true, regexp: true */

/*members "", "\b", "\t", "\n", "\f", "\r", "\"", JSON, "\\", apply,
    call, charCodeAt, getUTCDate, getUTCFullYear, getUTCHours,
    getUTCMinutes, getUTCMonth, getUTCSeconds, hasOwnProperty, join,
    lastIndex, length, parse, prototype, push, replace, slice, stringify,
    test, toJSON, toString, valueOf
*/


// Create a JSON object only if one does not already exist. We create the
// methods in a closure to avoid creating global variables.

if (typeof JSON !== 'object') {
    JSON = {};
}

(function () {
    'use strict';

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function () {

            return isFinite(this.valueOf())
                ? this.getUTCFullYear()     + '-' +
                    f(this.getUTCMonth() + 1) + '-' +
                    f(this.getUTCDate())      + 'T' +
                    f(this.getUTCHours())     + ':' +
                    f(this.getUTCMinutes())   + ':' +
                    f(this.getUTCSeconds())   + 'Z'
                : null;
        };

        String.prototype.toJSON      =
            Number.prototype.toJSON  =
            Boolean.prototype.toJSON = function () {
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
            return typeof c === 'string'
                ? c
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

                v = partial.length === 0
                    ? '[]'
                    : gap
                    ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']'
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

            v = partial.length === 0
                ? '{}'
                : gap
                ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}'
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
        JSON.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

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
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/
                    .test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
                        .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
                        .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function'
                    ? walk({'': j}, '')
                    : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
}());

 
 
/** 
 * NEW FILE!!! 
 */
 
 
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
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Проверка является ли строка e-mail
 *
 * @author	Zaytsev Alexandr
 * @return	{Boolean} 
 */
function isTrueEmail(){
	var t = this.toString(),
		re = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i;
	return re.test(t);
}
String.prototype.isEmail = isTrueEmail; // добавляем методом для всех строк
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Разбиение числа по разрядам
 *
 * @author	Zaytsev Alexandr
 * @param	{number|string}		число которое нужно отформатировать
 * @return	{string}			отформатированное число
 */
(function( global ) {
	global.printPrice = function( num ) {
		var str = num+'';
		
		return str.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
	};
}(this));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/*\
|*|
|*|  :: cookies.js ::
|*|
|*|  A complete cookies reader/writer framework with full unicode support.
|*|
|*|  https://developer.mozilla.org/en-US/docs/DOM/document.cookie
|*|
|*|  This framework is released under the GNU Public License, version 3 or later.
|*|  http://www.gnu.org/licenses/gpl-3.0-standalone.html
|*|
|*|  Syntaxes:
|*|
|*|  * docCookies.setItem(name, value[, end[, path[, domain[, secure]]]])
|*|  * docCookies.getItem(name)
|*|  * docCookies.removeItem(name[, path])
|*|  * docCookies.hasItem(name)
|*|  * docCookies.keys()
|*|
\*/

;(function( global ) {	
	global.docCookies = {
		getItem:function ( sKey ) {
			return unescape(document.cookie.replace(new RegExp('(?:(?:^|.*;)\\s*' + escape(sKey).replace(/[\-\.\+\*]/g, '\\$&') + '\\s*\\=\\s*([^;]*).*$)|^.*$'), '$1')) || null;
		},

		setItem: function ( sKey, sValue, vEnd, sPath, sDomain, bSecure ) {
			if ( !sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey) ) {

				return false;
			}

			var sExpires = '';

			if ( vEnd ) {
				switch ( vEnd.constructor ) {
					case Number:
						sExpires = vEnd === Infinity ? '; expires=Fri, 31 Dec 9999 23:59:59 GMT' : '; max-age=' + vEnd;
						break;
					case String:
						sExpires = '; expires=' + vEnd;
						break;
					case Date:
						sExpires = '; expires=' + vEnd.toGMTString();
						break;
				}
			}

			document.cookie = escape(sKey) + '=' + escape(sValue) + sExpires + (sDomain ? '; domain=' + sDomain:
						'') + (sPath ? '; path=' + sPath:
						'') + (bSecure ? '; secure':
						'');
						
			return true;
		},

		removeItem: function ( sKey, sPath ) {
			if ( !sKey || !this.hasItem(sKey) ) {
				return false;
			}
			
			document.cookie = escape(sKey) + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT' + (sPath ? '; path=' + sPath: '');

			return true;
		},

		hasItem: function ( sKey ) {
			return (new RegExp('(?:^|;\\s*)' + escape(sKey).replace(/[\-\.\+\*]/g, '\\$&') + '\\s*\\=')).test(document.cookie);
		},

		/* optional method: you can safely remove it! */ 
		keys: function () {
			var aKeys = document.cookie.replace(/((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, '').split(/\s*(?:\=[^;]*)?;\s*/);

			for (var nIdx = 0; nIdx < aKeys.length; nIdx++) {
				aKeys[nIdx] = unescape(aKeys[nIdx]);
			}

			return aKeys;
		}
	};
}(this));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
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
 * NEW FILE!!! 
 */
 
 
function brwsr () {
	var userag      = navigator.userAgent.toLowerCase();
	this.isAndroid  = userag.indexOf("android") > -1;
	this.isOSX      = ( userag.indexOf('ipad') > -1 ||  userag.indexOf('iphone') > -1 );
	this.isOSX4     = this.isOSX && userag.indexOf('os 5') === -1;
	this.isOpera    = userag.indexOf("opera") > -1;
	
	this.isTouch    = this.isOSX || this.isAndroid;
}

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
				self.output.text( window.printPrice( Math.ceil( result.payment ) ) );
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
};

 
 
/** 
 * NEW FILE!!! 
 */
 
 
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
 
 
/** 
 * NEW FILE!!! 
 */
 
 
;(function( ENTER ) {
	var userUrl = ENTER.config.pageConfig.userUrl,
		constructors = ENTER.constructors;
	// end of vars

	
	/**
	 * Новый класс по работе с картой
	 *
	 * @author	Zaytsev Alexandr
	 * 
	 * @this	{CreateMap}
	 *
	 * @param	{Object}	args			DOM объект в который необходимо вывести карту
	 * @param	{Array}		points			Массив точек, которые необходимо вывести на карту
	 * @param	{Object}	baloonTemplate	Шаблон для балунов на карте
	 *
	 * @constructor
	 */
	constructors.CreateMap = (function() {
		'use strict';
	
		function CreateMap( nodeId, points, baloonTemplate ) {
			// enforces new
			if ( !(this instanceof CreateMap) ) {
				return new CreateMap(nodeId, points, baloonTemplate);
			}
			// constructor body
			
			console.info('CreateMap');
			console.log(points);

			this.points = points;
			this.template = baloonTemplate.html();
			this.center = this._calcCenter();

			console.log(this.center);

			this.mapWS = new ymaps.Map(nodeId, {
				center: [this.center.latitude, this.center.longitude],
				zoom: 10
			});

			this.mapWS.controls.add('zoomControl');

			this._showMarkers();
		}

		/**
		 * Расчет центра карты для исходного массива точек
		 */
		CreateMap.prototype._calcCenter = function() {
			console.info('calcCenter');

			var latitude = 0,
				longitude = 0,
				l = 0,
				i = 0,

				mapCenter = {};
			// end of vars

			for ( i = this.points.length - 1; i >= 0; i-- ) {
				latitude  += this.points[i].latitude * 1;
				longitude += this.points[i].longitude * 1;

				l++;
			}

			mapCenter = {
				latitude  : latitude / l,
				longitude : longitude / l
			};

			return mapCenter;
		};

		CreateMap.prototype._showMarkers = function() {
			var tmpPointInfo = null,
				tmpPlacemark = null,
				pointsCollection = new ymaps.GeoObjectArray();
			// end of vars

			// layout for baloon
			var pointContentLayout = ymaps.templateLayoutFactory.createClass(this.template);

			for ( var i = this.points.length - 1; i >= 0; i--) {
				tmpPointInfo = {
					id: this.points[i].id,
					name: this.points[i].name,
					address: this.points[i].address,
					link: this.points[i].link,
					regtime: this.points[i].regtime,
					parentBoxToken: this.points[i].parentBoxToken
				};

				tmpPlacemark = new ymaps.Placemark(
					// координаты точки
					[
						this.points[i].latitude,
						this.points[i].longitude
					],

					// данные для шаблона
					tmpPointInfo,

					// оформление метки на карте
					{
						iconImageHref: '/images/marker.png', // картинка иконки
						iconImageSize: [39, 59],
						iconImageOffset: [-19, -57]
					}
				);

				pointsCollection.add(tmpPlacemark);
			}

			ymaps.layout.storage.add('my#superlayout', pointContentLayout);
			pointsCollection.options.set({
				balloonContentBodyLayout:'my#superlayout',
				balloonMaxWidth: 350
			});

			this.mapWS.geoObjects.add(pointsCollection);
		};
	
	
		return CreateMap;
	
	}());
}(window.ENTER));

 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * @requires jQuery, simple_templating, docCookies, ENTER.utils, ENTER.config
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	var userUrl = ENTER.config.pageConfig.userUrl,
		utils = ENTER.utils;
	// end of vars
	

	/**
	 * === BLACKBOX CONSTRUCTOR ===
	 */
	var BlackBox = (function() {
	
		/**
		 * Создает объект для обновления данных с сервера и отображения текущих покупок
		 *
		 * @author	Zaytsev Alexandr
		 * @this	{BlackBox}
		 * 
		 * @param	{String}		updateUrl	URL по которому будут запрашиватся данные о пользователе и корзине.
		 * @param	{Object}		mainNode	DOM элемент бокса
		 * 
		 * @constructor
		 */
		function BlackBox( updateUrl, mainContatiner ) {
			// enforces new
			if ( !(this instanceof BlackBox) ) {
				return new BlackBox(updateUrl, mainContatiner);
			}
			// constructor body
			
			this.updUrl = ( !window.docCookies.hasItem('enter') || !window.docCookies.hasItem('enter_auth') ) ? updateUrl += '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000) : updateUrl;
			this.mainNode = mainContatiner;
		}
	
		
		/**
		 * Объект по работе с корзиной
		 *
		 * @author	Zaytsev Alexandr
		 * @this	{BlackBox}
		 * 
		 * @return	{Function} update	обновление данных о корзине
		 * @return	{Function} add		добавление в корзину
		 */
		BlackBox.prototype.basket = function() {
			var self = this,

				headQ = $('#topBasket'),
				bottomQ = self.mainNode.find('.bBlackBox__eCartQuan'),
				bottomSum = self.mainNode.find('.bBlackBox__eCartSum'),
				total = self.mainNode.find('.bBlackBox__eCartTotal'),
				bottomCart = self.mainNode.find('.bBlackBox__eCart'),
				flyboxBasket = self.mainNode.find('.bBlackBox__eFlybox.mBasket'),
				flyboxInner = self.mainNode.find('.bBlackBox__eFlyboxInner');
			// end of vars

				/**
				 * Уничтожение содержимого flybox и его скрытие
				 *
				 * @author	Zaytsev Alexandr
				 * 
				 * @private
				 */
			var flyboxDestroy = function flyboxDestroy() {
					flyboxBasket.hide(0, function() {
						flyboxInner.remove();
					});
				},

				/**
				 * Закрытие flybox по клику
				 * 
				 * @author	Zaytsev Alexandr
				 * 
				 * @param	{Event}	e
				 * 
				 * @private
				 */
				flyboxcloser = function flyboxcloser( e ) {
					var targ = e.target.className;

					if ( !(targ.indexOf('bBlackBox__eFlybox') + 1) || !(targ.indexOf('fillup') + 1) ) {
						flyboxDestroy();
						$('body').unbind('click', flyboxcloser);
					}
				},

				/**
				 * Обновление данных о корзине
				 *
				 * @author	Zaytsev Alexandr
				 * 
				 * @param	{Object}	basketInfo			Информация о корзине
				 * @param	{Number}	basketInfo.cartQ	Количество товаров в корзине
				 * @param	{Number}	basketInfo.cartSum	Стоимость товаров в корзине
				 * 
				 * @public
				 */
				update = function update( basketInfo ) {
					headQ.html('(' + basketInfo.cartQ + ')');
					bottomQ.html(basketInfo.cartQ);
					bottomSum.html(basketInfo.cartSum);
					bottomCart.addClass('mBought');
					total.show();
				},

				/**
				 * Добавление товара в корзину
				 *
				 * @author	Zaytsev Alexandr
				 * 
				 * @param	{Object}	item
				 * @param	{String}	item.title			Название товара
				 * @param	{Number}	item.price			Стоимость товара
				 * @param	{String}	item.imgSrc			Ссылка на изображение товара
				 * @param	{Number}	item.TotalQuan		Общее количество товаров в корзине
				 * @param	{Number}	item.totalSum		Общая стоимость корзины
				 * @param	{String}	item.linkToOrder	Ссылка на оформление заказа
				 * 
				 * @public
				 */
				add = function add ( item ) {
					var flyboxTmpl = tmpl('blackbox_basketshow_tmpl', item),
							nowBasket = {
							cartQ: item.totalQuan,
							cartSum: item.totalSum
						};
					// end of vars

					flyboxDestroy();
					flyboxBasket.append(flyboxTmpl);
					flyboxBasket.show(300);

					self.basket().update(nowBasket);

					$('body').bind('click', flyboxcloser);

				};
			//end of functions

			return {
				'update': update,
				'add': add
			};
		};

		/**
		 * Объект по работе с данными пользователя
		 *
		 * @author	Zaytsev Alexandr
		 * 
		 * @this	{BlackBox}
		 * 
		 * @return	{Function}	update
		 */
		BlackBox.prototype.user = function() {
			var self = this;

			/**
			 * Обновление пользователя
			 *
			 * @author	Zaytsev Alexandr
			 * 
			 * @param	{String}	userName	Имя пользователя
			 * 
			 * @public
			 */
			var update = function update ( userName ) {
				var topAuth = $('#auth-link'),
					bottomAuth = self.mainNode.find('.bBlackBox__eUserLink'),
					dtmpl = {},
					show_user = '';
				//end of vars

				if ( userName !== null ) {
					dtmpl = {
						user: userName
					};

					show_user = tmpl('auth_tmpl', dtmpl);
					
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
		 * 
		 * @this	{BlackBox}
		 */
		BlackBox.prototype.init = function() {
			var self = this;

				/**
				 * Обработчик Action присланных с сервера
				 * 
				 * @param	{Object}	action	Список действий которые необходимо выполнить
				 * 
				 * @private
				 */
			var startAction = function startAction( action ) {
					if ( action.subscribe !== undefined ) {
						$('body').trigger('showsubscribe', [action.subscribe]);
					}
					if ( action.cartButton !== undefined ) {
						$('body').trigger('markcartbutton', [action.cartButton]);
						$('body').trigger('updatespinner', [action.cartButton]);
					}
				},

				/**
				 * Обработчик данных о корзине и пользователе
				 * 
				 * @param	{Object}	data
				 * 
				 * @private
				 */ 
				parseUserInfo = function parseUserInfo( data ) {
					var userInfo = data.user,
						cartInfo = data.cart,
						actionInfo = data.action,
						nowBasket = {};
					//end of vars
					
					if ( data.success !== true ) {
						return false;
					}

					self.user().update(userInfo.name);

					if ( cartInfo.quantity !== 0 ) {
						nowBasket = {
							cartQ: cartInfo.quantity,
							cartSum: cartInfo.sum
						};

						self.basket().update(nowBasket);
					}

					if ( actionInfo !== undefined ) {
						startAction(actionInfo);
					}
				};
			//end of functions

			$.get(self.updUrl, parseUserInfo);
		};
	
		return BlackBox;
	
	}());
	/**
	 * === END BLACKBOX CONSTRUCTOR ===
	 */


	/**
	 * Создание и иницилизация объекта для работы с корзиной и данными пользователя
	 * 
	 * @type	{BlackBox}
	 */
	utils.blackBox = new BlackBox(userUrl, $('.bBlackBox__eInner'));
	utils.blackBox.init();
	
}(window.ENTER));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Валидатор форм
 *
 * @author		Zaytsev Alexandr
 * @this		{FormValidator}
 * @requires	jQuery
 * @constructor
 */
function FormValidator( config ) {
	if ( !config.fields.length ) {
		return;
	}

	this.config = $.extend(
						{},
						this._defaultsConfig,
						config );

	this._enableHandlers();
}

/**
 * ============ PRIVATE METHODS ===================
 */

/**
 * Стандартные настройки валидатора
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._defaultsConfig = {
	errorClass: 'mError'
};

/**
 * Поля, на которые уже навешен обработчик валидации на ходу
 */
FormValidator.prototype._validateOnChangeFields = {
};

/**
 * Проверка обязательных к заполнению полей
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._requireAs = {
	checkbox : function( fieldNode ) {
		var value = fieldNode.attr('checked');

		if ( value === undefined ) {
			return {
				hasError: true,
				errorMsg : 'Поле обязательно для заполнения'
			};
		}

		return {
			hasError: false
		};
	},

	radio: function( fieldNode ) {
		var checked = fieldNode.filter(':checked').val();

		if ( checked === undefined ) {
			return {
				hasError: true,
				errorMsg : 'Необходимо выбрать пункт из списка'
			};
		}

		return {
			hasError: false
		};
	},

	text: function( fieldNode ) {
		var value = fieldNode.val();

		if ( value.length === 0 ) {
			return {
				hasError: true,
				errorMsg : 'Поле обязательно для заполнения'
			};
		}

		return {
			hasError: false
		};
	},

	password: function( fieldNode ) {
		var value = fieldNode.val();

		if ( value.length === 0 ) {
			return {
				hasError: true,
				errorMsg : 'Поле обязательно для заполнения'
			};
		}

		return {
			hasError: false
		};
	},

	textarea: function( fieldNode ) {
		var value = fieldNode.text();

		if ( value.length === 0 ) {
			return {
				hasError: true,
				errorMsg : 'Поле обязательно для заполнения'
			};
		}

		return {
			hasError: false
		};
	},

	select: function( fieldNode ) {
		if ( fieldNode.val() ) {
			return {
				hasError: false
			};
		}

		return {
			hasError: true,
			errorMsg : 'Необходимо выбрать значение из списка'
		};
	}
};

/**
 * Валидирование поля
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._validBy = {
	isEmail: function( fieldNode ) {
		var re = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i,
			value = fieldNode.val();

		if ( re.test(value) ) {
			return {
				hasError: false
			};
		}
		else {
			return {
				hasError: true,
				errorMsg : 'Некорректно введен e-mail'
			};
		}
	},

	isPhone: function( fieldNode ) {
		var re = /(\+7|8)(-|\s)?(\(\d(-|\s)?\d(-|\s)?\d\s?\)|\d(-|\s)?\d(-|\s)?\d\s?)(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d$/i,
			value = fieldNode.val();

		if ( re.test(value) ) {
			return {
				hasError: false
			};
		}
		else {
			return {
				hasError: true,
				errorMsg : 'Некорректно введен телефон'
			};
		}
	},

	isNumber: function( fieldNode ) {
		var re = /^[0-9]+$/,
			value = fieldNode.val();

		if ( re.test(value) ) {
			return {
				hasError: false
			};
		}
		else {
			return {
				hasError: true,
				errorMsg : 'Поле может содержать только числа'
			};
		}
	}
};

/**
 * Валидация поля
 * 
 * @param	{Object}	field			Объект поля для валидации
 * @param	{Object}	field.fieldNode	Ссылка на jQuery объект поля
 * @param	{String}	field.validBy	Тип валидации поля
 * @param	{Boolean}	field.require	Является ли поле обязательным к заполению
 * @param	{String}	field.customErr	Сообщение об ошибке, если поле не прошло валидацию
 *
 * @return	{Object}	error			Объект с ошибкой
 * @return	{Boolean}	error.hasError	Есть ли ошибка
 * @return	{Boolean}	error.errorMsg	Сообщение об ошибке
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._validateField = function( field ) {
	var self = this,

		elementType = null,

		fieldNode = null,
		validBy = null,
		require = null,
		customErr = '',

		error = {
			hasError: false
		},
		result = {};
	// end of vars

	fieldNode = field.fieldNode;
	require = ( fieldNode.attr('required') === 'required' ) ? true : field.require; // если у элемента формы есть required то поле обязательное, иначе брать из конфига
	validBy = field.validBy;
	customErr = field.customErr;

	elementType = ( fieldNode.tagName === 'TEXTAREA') ? 'textarea' : ( fieldNode.tagName === 'SELECT') ? 'select' : fieldNode.attr('type') ; // если тэг элемента TEXTAREA то тип проверки TEXTAREA, если SELECT - то SELECT, иначе берем из атрибута type

	/**
	 * Проверка обязательно ли поле для заполенения
	 */
	if ( require ) {
		/**
		 * Проверка существования метода проверки на обязательность для данного типа поля
		 */
		if ( self._requireAs.hasOwnProperty(elementType) ) {
			result = self._requireAs[elementType](fieldNode);

			if ( result.hasError ) {
				error = {
					hasError: true,
					errorMsg : ( customErr !== undefined ) ? customErr : result.errorMsg
				};

				return error;
			}
		}
		else {
			error = {
				hasError: true,
				errorMsg : 'Обязательное поле. Неизвестный метод проверки для '+elementType
			};

			return error;
		}
	}

	/**
	 * Проверка существоаания метода валидации
	 * Валидация поля, если не пустое
	 */
	if ( self._validBy.hasOwnProperty(validBy) && field.fieldNode.val().length !==0 ) {
		result = self._validBy[validBy](fieldNode);

		if ( result.hasError ) {
			error = {
				hasError: true,
				errorMsg: ( customErr !== undefined ) ? customErr : result.errorMsg
			};
		}
	}
	else if ( validBy !== undefined && field.fieldNode.val().length !==0 ) {
		error = {
			hasError: true,
			errorMsg : 'Неизвестный метод валидации '+validBy
		};
	}

	return error;
};

FormValidator.prototype._unmarkFieldError = function( fieldNode ) {
	console.info('Снимаем маркировку');

	fieldNode.removeClass(this.config.errorClass);
	fieldNode.parent().find('.bErrorText').remove();
};

FormValidator.prototype._markFieldError = function( fieldNode, errorMsg ) {
	console.info('маркируем');
	console.log(errorMsg);
	
	fieldNode.addClass(this.config.errorClass);
	fieldNode.before('<div class="bErrorText"><div class="bErrorText__eInner">'+errorMsg+'</div></div>');
};

/**
 * Активация хандлеров для полей
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._enableHandlers = function() {
	console.info('_enableHandlers');
	var self = this,
		fields = this.config.fields,
		currentField = null;
		
	// end of vars

	var validateOnBlur = function validateOnBlur( that ) {
			var result = {},
				findedField = self._findFieldByNode( that );
			// end of vars

			if ( findedField.finded ) {
				result = self._validateField(findedField.field);

				if ( result.hasError ) {
					self._markFieldError(that, result.errorMsg);
				}
			}
			else {
				console.log('поле не найдено или тип валидации не существует, хандлер нужно убрать');
				that.unbind('blur', validateOnBlur);
			}

			return false;
		},

		blurHandler = function blurHandler( ) {
			var that = $(this),
				timeout_id = null;
			// end of vars
			
			clearTimeout(timeout_id);
			timeout_id = window.setTimeout(function(){
				validateOnBlur(that);
			}, 5);
		},

		clearError = function clearError() {
			self._unmarkFieldError($(this));
		};
	// end of functions

	for (var i = fields.length - 1; i >= 0; i--) {
		currentField = fields[i];

		if ( currentField.validateOnChange ) {
			if ( self._validateOnChangeFields[ currentField.fieldNode.get(0).outerHTML ] ) {
				console.log('уже вешали');
				continue;
			}

			currentField.fieldNode.bind('blur', blurHandler);
			currentField.fieldNode.bind('focus', clearError);
			self._validateOnChangeFields[ currentField.fieldNode.get(0).outerHTML ] = true;
		}
	}

	console.log(self);
};

/**
 * Поиск поля
 * 
 * @param	{Object}	nodeToFind		Ссылка на jQuery объект поля которое нужно найти
 * @return	{Object}    Object			Объект с параметрами найденой ноды
 * @return	{Boolean}	Object.finded	Было ли поле найдено
 * @return	{Object}	Object.field	Объект поля из конфига
 * @return	{Number}	Object.index	Порядковый номер поля
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._findFieldByNode = function( nodeToFind ) {
	var fields = this.config.fields;

	for ( var i = fields.length - 1; i >= 0; i-- ) {
		if ( fields[i].fieldNode.get(0) === nodeToFind.get(0) ) {
			return {
				finded: true,
				field: fields[i],
				index: i
			};
		}
	}

	return {
		finded: false
	};
};



/**
 * ============ PUBLIC METHODS ===================
 */


/**
 * Запуск валидации полей
 *
 * @param	{Object}	callbacks				Объект со ссылками на функции обратных вызовов
 * @param	{Function}	callbacks.onInvalid		Функция обратного вызова, если поля не прошли валидацию. В функцию передается массив объектов ошибок.
 * @param	{Function}	callbacks.onValid		Функция обратного вызова, если поля прошли валидацию
 *
 * @this	{FormValidator}
 * @public
 */
FormValidator.prototype.validate = function( callbacks ) {
	var self = this,
		fields = this.config.fields,
		i = 0,
		errors = [],
		result = {};
	// end of vars	
	
	for ( i = fields.length - 1; i >= 0; i-- ) { // перебираем поля из конфига
		result = self._validateField(fields[i]);

		if ( result.hasError ) {
			self._markFieldError(fields[i].fieldNode, result.errorMsg);
			errors.push({
				fieldNode: fields[i].fieldNode,
				errorMsg: result.errorMsg
			});
		}
		else {
			self._unmarkFieldError(fields[i].fieldNode);
		}
	}

	if ( errors.length ) {
		callbacks.onInvalid(errors);
	}
	else {
		callbacks.onValid();
	}
};

/**
 * Получить тип валидации для поля
 *
 * @param	{Object}			fieldToFind		Ссылка на jQuery объект поля для которого нужно получить параметры валидации
 * 
 * @return	{Object|Boolean}					Возвращает или конфигурацию валидации для поля, или false
 * 
 * @this	{FormValidator}
 * @public
 */
FormValidator.prototype.getValidate = function( fieldToFind ) {
	var findedField = this._findFieldByNode(fieldToFind);

	if ( findedField.finded ) {
		return findedField.field;
	}

	return false;
};

/**
 * Установить новый тип валидации для поля. Если поле не найдено, создает новое с указанными параметрами.
 *
 * @param	{Object}	fieldNodeToCange					Ссылка на jQuery объект поля для которого нужно изменить параметры валидации
 * @param	{Object}	paramsToChange						Новые свойства валидации поля
 * @param	{String}	paramsToChange.validBy				Тип валидации поля
 * @param	{Boolean}	paramsToChange.require				Является ли поле обязательным к заполению
 * @param	{String}	paramsToChange.customErr			Сообщение об ошибке, если поле не прошло валидацию
 * @param	{Boolean}	paramsToChange.validateOnChange		Нужно ли валидировать поле при его изменении
 *
 * @this	{FormValidator}
 * @public
 */
FormValidator.prototype.setValidate = function( fieldNodeToCange, paramsToChange ) {
	var findedField = this._findFieldByNode(fieldNodeToCange),
		addindField = null;

	if ( findedField.finded ) {
		addindField = $.extend(
						{},
						findedField.field,
						paramsToChange );
		this.config.fields.splice(findedField.index, 1);

	}
	else {
		paramsToChange.fieldNode = fieldNodeToCange;
		addindField = paramsToChange;
	}

	this.addFieldToValidate(addindField);
};

/**
 * Удалить поле для валидации
 * 
 * @param	{Object}	fieldNodeToRemove	Ссылка на jQuery объект поля которое нужно удалить из списка валидации
 *
 * @return	{Boolean}						Был ли удален объект из массива полей для валидации
 *
 * @this	{FormValidator}
 * @public
 */
FormValidator.prototype.removeFieldToValidate = function( fieldNodeToRemove ) {
	var findedField = this._findFieldByNode(fieldNodeToRemove);

	if ( findedField.finded ) {
		this.config.fields.splice(findedField.index, 1);

		return true;
	}

	return false;
};

/**
 * Добавить поле для валидации
 * 
 * @param	{Object}	field					Объект поля для валидации
 * @param	{Object}	field.fieldNode			Ссылка на jQuery объект поля
 * @param	{String}	field.validBy			Тип валидации поля
 * @param	{Boolean}	field.require			Является ли поле обязательным к заполению
 * @param	{String}	field.customErr			Сообщение об ошибке, если поле не прошло валидацию
 * @param	{Boolean}	field.validateOnChange	Нужно ли валидировать поле при его изменении
 *
 * @this	{FormValidator}
 * @public
 */
FormValidator.prototype.addFieldToValidate = function( field ) {
	this.config.fields.push(field);
	this._enableHandlers();
};
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Добавить новый параметр в URL
 * 
 * @param	{String}	key		Ключ
 * @param	{String}	value	Значение
 * @return	{String}			Сформированный URL
 */
var UpdateUrlString = function(key, value) {
	var url = this.toString();
	var re = new RegExp('([?|&])' + key + '=.*?(&|#|$)(.*)', 'gi');

	if (re.test(url)) {
		if (typeof value !== 'undefined' && value !== null){
			return url.replace(re, '$1' + key + '=' + value + '$2$3');
		}
		else {
			return url.replace(re, '$1$3').replace(/(&|\?)$/, '');
		}
	}
	else {
		if (typeof value !== 'undefined' && value !== null) {
			var separator = url.indexOf('?') !== -1 ? '&' : '?',
				hash = url.split('#');
			url = hash[0] + separator + key + '=' + value;
			if (hash[1]) {
				url += '#' + hash[1];
			}
			return url;
		}
		else{
			return url;
		}
	}
};
String.prototype.addParameterToUrl = UpdateUrlString;
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Блокер экрана
 *
 * @requires jQuery, jQuery.lightbox_me, ENTER.utils
 *
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}		noti		Объект jQuery блокера экрана
 * @param	{Function}		block		Функция блокировки экрана. На вход принимает текст который нужно отобразить в окошке блокера
 * @param	{Function}		unblock		Функция разблокировки экрана. Объект окна блокера удаляется.
 */
;(function( ENTER ) {
	var utils = ENTER.utils;
	
	utils.blockScreen = {
		noti: null,
		block: function( text ) {
			var self = this;

			console.warn('block screen');

			if ( self.noti ) {
				self.unblock();
			}

			self.noti = $('<div>').addClass('noti').html('<div><img src="/images/ajaxnoti.gif" /></br></br> '+ text +'</div>');
			self.noti.appendTo('body');

			self.noti.lightbox_me({
				centered:true,
				closeClick:false,
				closeEsc:false,
				onClose: function() {
					self.noti.remove();
				}
			});
		},

		unblock: function() {
			if ( this.noti ) {
				console.warn('unblock screen');
				
				this.noti.trigger('close');
			}
		}
	};
}(window.ENTER));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * IE indexOf fix
 */
if ( !Array.prototype.indexOf ) {
	Array.prototype.indexOf = function(elt /*, from*/) {
		var len = this.length >>> 0,
			from = Number(arguments[1]) || 0;
		// end of vars
		
		from = (from < 0) ? Math.ceil(from) : Math.floor(from);

		if ( from < 0 ) {
			from += len;
		}

		for ( ; from < len; from++ ) {
			if ( from in this && this[from] === elt ) {
					return from;
			}
		}

		return -1;
	};
}
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Пакетная отправка данных на сервер
 *
 * @author	Zaytsev Alexandr
 */
;(function( global ) {
	var pageConfig = global.ENTER.config.pageConfig,
		utils = global.ENTER.utils;
	// end of vars

	utils.packageReq = function packageReq( reqArray ) {
		console.info('Пакетный запрос');

		var dataToSend = {},
			callbacks = [],

			i, len;
		// end of vars
		
		dataToSend.actions = [];
		
		var resHandler = function resHandler( res ) {
			console.info('Обработка ответа пакетого запроса');

			for ( i = 0, len = res.length - 1; i <= len; i++ ) {
				callbacks[i](res[i]);
			}
		};

		for ( i = 0, len = reqArray.length - 1; i <= len; i++ ) {
			console.log(i);

			// Обход странного бага с IE
			if ( !reqArray[i] || !reqArray[i].url ) {
				console.info('continue');

				continue;
			}

			dataToSend.actions.push({
				url: reqArray[i].url,
				method: reqArray[i].type,
				data: reqArray[i].data || null
			});

			callbacks[i] = reqArray[i].callback;
		}

		$.ajax({
			url: pageConfig.routeUrl,
			type: 'POST',
			data: dataToSend,
			success: resHandler
		});
	};
}(this));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Обработчик опроса
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
    var question = null,
        sbWidthDiff = null,
        sbHeightDiff = null,
        sbWidthDiffAfterSubmit = null,
        initTime = null,
        serverTime = null,
        showDelay = null,
        isTimePassed = null,
        questionHash = null,
        cookieName = 'survey',
        cookieNameCollapsed = 'surveyCollapsed';

    /**
     * Функция инициализации параметров опроса
     */
    var initSurveyBoxData = function() {
        var surveyBox = $('.surveyBox');
        question = $('.surveyBox__question').html();
        sbWidthDiff = parseInt( surveyBox.data('expanded-width-diff'), 10 );
        sbHeightDiff = parseInt( surveyBox.data('expanded-height-diff'), 10 );
        sbWidthDiffAfterSubmit = sbWidthDiff - 14;
        initTime = parseInt( surveyBox.data('init-time'), 10 );
        serverTime = parseInt( surveyBox.data('server-time'), 10 );
        showDelay = parseInt( surveyBox.data('show-delay'), 10 );
        isTimePassed = parseInt( surveyBox.data('is-time-passed'), 10 );
        questionHash = surveyBox.data('questionHash');
    };

    /**
     * Функция разворачивания/сворачивания опроса
     */
    var toggleSurveyBox = function(){
        var toggle = this;

        if ( $('.surveyBox').hasClass('expanded') ) {
            $('.surveyBox').animate( {
                width: '-=' + sbWidthDiff,
                height: '-=' + sbHeightDiff
            }, 250, function() {
                window.docCookies.setItem( cookieNameCollapsed, questionHash, 7*24*60*60, '/' );
                $(toggle).html('Показать опрос');
                $('.surveyBox__content').hide();
                $('.surveyBox').removeClass('expanded');
            } );
        } else {
            $('.surveyBox').animate( {
                width: '+=' + sbWidthDiff,
                height: '+=' + sbHeightDiff
            }, 250, function() {
                window.docCookies.removeItem( cookieNameCollapsed, '/' );
                $(toggle).html('Скрыть опрос');
                $('.surveyBox__content').show();
                $('.surveyBox').addClass('expanded');
            } );
        }
        return false;
    };

    /**
     * Функция ответа на опрос
     */
    var submitAnswer = function() {
        var answer = $(this).html(),
            kmId = null;
        if ( typeof(window.KM) !== 'undefined' ) {
            kmId = window.KM.i;
        }
        $.ajax({
            type: 'POST',
            url: '/survey/submit-answer',
            data: {
                question: question,
                answer: answer,
                kmId: kmId
            },
            success: function() {
                window.docCookies.setItem( cookieName, initTime, 7*24*60*60, '/' );
                $('.surveyBox__toggleWrapper').html('Спасибо за ответ!');
                $('.surveyBox__content').remove();
                $('.surveyBox').animate( {
                    width: '-=' + sbWidthDiffAfterSubmit,
                    height: '-=' + sbHeightDiff
                }, 250, function() {
                    setTimeout(function() {
                        $('.surveyBox').removeClass('expanded');
                        $('.surveyBox').fadeOut();
                    }, 2000);
                } );
            }
        });
        return false;
    };

    /**
     * Функция слежения за необходимостью показа опроса
     */
    var trackIfShouldShow = function() {
        var shouldShow = false;

        serverTime += 1;
        if ( serverTime > initTime + showDelay ) {
            shouldShow = true;
        }

        if ( shouldShow ) {
            showSurvey();
        } else {
            setTimeout(function() {
                trackIfShouldShow();
            }, 1000);
        }
    }; 

    /**
     * Функция определения поддерживать ли опрос свернутым
     */
    var keepCollapsed = function() {
        var cookieCollapsed = window.docCookies.getItem( cookieNameCollapsed );
        return cookieCollapsed === questionHash;
    }; 

    /**
     * Функция отображения панели опроса
     */
    var showSurvey = function() {
        $('.surveyBox').fadeIn();
        if( !keepCollapsed() ) {
            $('.surveyBox__toggle').click();
        }
    }; 

    $(document).ready(function() {
        initSurveyBoxData();
        $('.surveyBox__toggle').bind('click', toggleSurveyBox);
        $('.surveyBox__answer').bind('click', submitAnswer);

        if ( !isTimePassed ) {
            trackIfShouldShow();
        } else {
            setTimeout(function() {
                showSurvey();
            }, 1000);
        }
    });
}());
