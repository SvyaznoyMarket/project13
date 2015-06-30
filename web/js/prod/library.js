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
 * Разбиение числа по разрядам
 *
 * @author	Zaytsev Alexandr
 * @param	{number|string}		число которое нужно отформатировать
 * @return	{string}			отформатированное число
 */
(function( global ) {
	global.printPrice = function(price) {
		price = String(price);
		price = price.replace(',', '.');
		price = price.replace(/\s/g, '');
		price = String(Number(price).toFixed(2));
		price = price.split('.');

		if (price[0].length >= 5) {
			price[0] = price[0].replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1&thinsp;'); // TODO: заменить &thinsp; на соответствующий unicode символ
		}

		if (price[1] == 0) {
			price = price.slice(0, 1);
		}

		return price.join('.');
	};
}(this));
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
		var
			value = fieldNode.attr('checked');
		// end of vars

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
		var
			checked = fieldNode.filter(':checked').val();
		// end of vars

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
		var
			value = fieldNode.val();
		// end of vars

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
		var
			value = fieldNode.val();
		// end of vars

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
		var
			value = fieldNode.val();
		// end of vars

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
		var
			re = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i,
			value = fieldNode.val();
		// end of vars

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
		var
			re = /(\+7|8)(-|\s)?(\(\d(-|\s)?\d(-|\s)?\d\s?\)|\d(-|\s)?\d(-|\s)?\d\s?)(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d$/i,
			value = fieldNode.val();
		// end of vars

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
		var
			re = /^[0-9]+$/,
			value = fieldNode.val();
		// end of vars

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
	var
		self = this,

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

	if ( !fieldNode.length ) {
		console.warn('нет поля, не валидируем');

		return error;
	}

	//elementType = ( fieldNode.tagName === 'TEXTAREA') ? 'textarea' : ( fieldNode.tagName === 'SELECT') ? 'select' : fieldNode.attr('type') ; // если тэг элемента TEXTAREA то тип проверки TEXTAREA, если SELECT - то SELECT, иначе берем из атрибута type
	elementType = ( fieldNode.prop('tagName') === 'TEXTAREA') ? 'textarea' : ( fieldNode.prop('tagName') === 'SELECT') ? 'select' : fieldNode.attr('type') ; // если тэг элемента TEXTAREA то тип проверки TEXTAREA, если SELECT - то SELECT, иначе берем из атрибута type

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
	var
		self = this;
	// end of vars

	var
		clearError = function clearError() {
			self._unmarkFieldError($(this));
		};
	// end of functions

	console.info('маркируем');
	console.log(errorMsg);
	
	fieldNode.addClass(this.config.errorClass);
	fieldNode.before('<div class="bErrorText"><div class="bErrorText__eInner">'+errorMsg+'</div></div>');
	fieldNode.bind('focus', clearError);
};

/**
 * Активация хандлеров для полей
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._enableHandlers = function() {
	//console.groupCollapsed('_enableHandlers');

	var
		self = this,
		fields = this.config.fields,
		currentField = null,
		i;
	// end of vars

	var
		validateOnBlur = function validateOnBlur( that ) {
			var
				result = {},
				findedField = self._findFieldByNode( that );
			// end of vars

			if ( findedField.finded ) {
				result = self._validateField(findedField.field);

				if ( result.hasError ) {
					self._markFieldError(that, result.errorMsg);
				}
			}
			else {
				//console.log('поле не найдено или тип валидации не существует, хандлер нужно убрать');
				that.unbind('blur', validateOnBlur);
			}

			return false;
		},

		blurHandler = function blurHandler( ) {
			var
				that = $(this),
				timeout_id = null;
			// end of vars
			
			clearTimeout(timeout_id);
			timeout_id = window.setTimeout(function(){
				validateOnBlur(that);
			}, 5);
		};
	// end of functions

	for ( i = fields.length - 1; i >= 0; i-- ) {
		currentField = fields[i];

		if ( currentField.fieldNode.length === 0 ) {
			continue;
		}


		if ( currentField.validateOnChange ) {
			if ( self._validateOnChangeFields[ currentField.fieldNode.get(0).outerHTML ] ) {
				//console.log('уже вешали');
				continue;
			}

			currentField.fieldNode.bind('blur', blurHandler);
			self._validateOnChangeFields[ currentField.fieldNode.get(0).outerHTML ] = true;
		}
	}

	//console.log(self);
    //console.groupEnd();
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
	var
		fields = this.config.fields,
		i;
	// end of vars

	for ( i = fields.length - 1; i >= 0; i-- ) {
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
	var
		self = this,
		fields = this.config.fields,
		i = 0,
		errors = [],
		result = {};
	// end of vars	
	
	for ( i = fields.length - 1; i >= 0; i-- ) { // перебираем поля из конфига
		result = self._validateField(fields[i]);

		console.log(result);

		if ( result.hasError ) {
			self._markFieldError(fields[i].fieldNode, result.errorMsg);
			errors.push({
				fieldNode: fields[i].fieldNode,
				errorMsg: result.errorMsg
			});
		}
		else {
			console.log('нет ошибки в поле ');
			console.log(fields[i].fieldNode);
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
	var
		findedField = this._findFieldByNode(fieldToFind);
	// end of vars

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
	var
		findedField = this._findFieldByNode(fieldNodeToCange),
		addindField = null;
	// end of vars

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
	var
		findedField = this._findFieldByNode(fieldNodeToRemove);
	// end of vars

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
 * Модуль обратного счетчика
 *
 * @module      сountDown
 * @version     0.1
 *
 * @author      Zaytsev Alexandr
 */
!function( root, module ) {
    var
        CountDown = module();

    // AMD
    if ( typeof define === 'function' && define.amd ) {
        define('CountDown', [], function() {
            return CountDown;
        });

    // Node.js or CommonJS
    } else if ( typeof exports !== 'undefined' ) {
        exports.CountDown = CountDown;

    // YM Modules
    } else if ( typeof modules !== 'undefined' && typeof modules.define === 'function' ) {
        modules.define('CountDown', [], function( provide ) {
            provide(CountDown);
        });

    // Browser global
    } else {
        root.CountDown = CountDown;
    }

}( this, function() {
    'use strict';

    var
        /**
         * @return  {CountDown}
         */
        CountDown = (function() {
            var
                ONE_SEC  = 1000,
                ONE_MIN  = 60 * ONE_SEC,
                ONE_HOUR = 60 * ONE_MIN,
                ONE_DAY  = 24 * ONE_HOUR,

                helpers = {
                    isObject: function( obj ) {
                        var
                            type = typeof obj;

                        return type === 'function' || type === 'object' && !!obj;
                    },

                    isFunction: function( obj ) {
                        return typeof obj == 'function' || false;
                    },

                    isNumber: function( obj ) {
                        return toString.call(obj) === '[object Number]' && !isNaN(obj);
                    }
                },

                /**
                 * Обновление счетчика
                 *
                 * @memberOf    module:countDown~CountDown#
                 * @method      setExpression
                 * @private
                 *
                 * @this        {CountDown}
                 */
                updateCounter = function() {
                    var
                        now  = (new Date()).getTime(),
                        end  = this.endDate.getTime(),
                        diff = end - now;

                    if ( diff <= 0 ) {
                        console.log('Достигли даты завершения');
                        this.stop();
                    }

                    this.tick && this.tick({
                        days: Math.floor(diff/ONE_DAY),
                        hours: Math.floor((diff % ONE_DAY) / ONE_HOUR),
                        minutes: Math.floor(((diff % ONE_DAY) % ONE_HOUR) / ONE_MIN),
                        seconds: Math.floor((((diff % ONE_DAY) % ONE_HOUR) % ONE_MIN) / ONE_SEC)
                    })
                };

                /**
                 * @classdesc   Конструктор класса обратного счетчика
                 *
                 * @constructs  CountDown
                 *
                 * @throws      {Error}         Параметры должны быть объектом
                 * @throws      {Error}         Параметр "timestamp" должен быть объектом
                 * @throws      {Error}         Параметр "tick" должен быть функцией
                 *
                 * @param       {Object}        options
                 * @param       {Number}        options.timestamp   Время, до которого ведем обратный отсчет
                 * @param       {Function}      options.tick        Обратный вызов на каждый шаг счетчика
                 *
                 * @return      {CountDown}
                 */
                CountDown = function CountDown( options ) {
                    // Enforces new
                    if ( !(this instanceof CountDown) ) {
                        return new CountDown(options);
                    }

                    // Validate params
                    if ( !helpers.isObject(options) ) {
                        throw new Error('Параметры должны быть объектом');
                    }

                    if ( !helpers.isNumber(options.timestamp) ) {
                        throw new Error('Параметр "timestamp" должен быть объектом');
                    }

                    if ( !helpers.isFunction(options.tick) ) {
                        throw new Error('Параметр "tick" должен быть функцией');
                    }

                    this.endDate = new Date(options.timestamp);
                    this.tick    = options.tick;
                    this.iid     = setInterval(updateCounter.bind(this), 1000);
                };

            /**
             * Остановка счетчика
             *
             * @memberOf    module:countDown~CountDown#
             * @method      stop
             * @public
             */
            CountDown.prototype.stop = function() {
                clearInterval(this.iid);
            };

            return CountDown;
        }());

    return CountDown;
});

;(function(){
    var clone = docCookies.setItem;
    docCookies.setItem = function(){
        var args = Array.prototype.slice.call(arguments); // making true array
        if (typeof args[4] == 'undefined') args[4] = '.' + /[A-Za-z0-9]+\.[A-Za-z0-9]+$/.exec(window.location.hostname)[0]; // set domain to ".enter.ru" or ".enter.loc" or ".ent3.ru", etc
        return clone.apply(this, args);
    }
}());
// Copyright 2009-2012 by contributors, MIT License
// vim: ts=4 sts=4 sw=4 expandtab

//Add semicolon to prevent IIFE from being passed as argument to concated code.
;
// Module systems magic dance
(function (definition) {
	// RequireJS
	if (typeof define == "function") {
		define(definition);
	// YUI3
	} else if (typeof YUI == "function") {
		YUI.add("es5", definition);
	// CommonJS and <script>
	} else {
		definition();
	}
})(function () {

/**
 * Brings an environment as close to ECMAScript 5 compliance
 * as is possible with the facilities of erstwhile engines.
 *
 * Annotated ES5: http://es5.github.com/ (specific links below)
 * ES5 Spec: http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-262.pdf
 * Required reading: http://javascriptweblog.wordpress.com/2011/12/05/extending-javascript-natives/
 */

//
// Function
// ========
//

// ES-5 15.3.4.5
// http://es5.github.com/#x15.3.4.5

function Empty() {}

if (!Function.prototype.bind) {
	Function.prototype.bind = function bind(that) { // .length is 1
		// 1. Let Target be the this value.
		var target = this;
		// 2. If IsCallable(Target) is false, throw a TypeError exception.
		if (typeof target != "function") {
			throw new TypeError("Function.prototype.bind called on incompatible " + target);
		}
		// 3. Let A be a new (possibly empty) internal list of all of the
		//   argument values provided after thisArg (arg1, arg2 etc), in order.
		// XXX slicedArgs will stand in for "A" if used
		var args = _Array_slice_.call(arguments, 1); // for normal call
		// 4. Let F be a new native ECMAScript object.
		// 11. Set the [[Prototype]] internal property of F to the standard
		//   built-in Function prototype object as specified in 15.3.3.1.
		// 12. Set the [[Call]] internal property of F as described in
		//   15.3.4.5.1.
		// 13. Set the [[Construct]] internal property of F as described in
		//   15.3.4.5.2.
		// 14. Set the [[HasInstance]] internal property of F as described in
		//   15.3.4.5.3.
		var bound = function () {

			if (this instanceof bound) {
				// 15.3.4.5.2 [[Construct]]
				// When the [[Construct]] internal method of a function object,
				// F that was created using the bind function is called with a
				// list of arguments ExtraArgs, the following steps are taken:
				// 1. Let target be the value of F's [[TargetFunction]]
				//   internal property.
				// 2. If target has no [[Construct]] internal method, a
				//   TypeError exception is thrown.
				// 3. Let boundArgs be the value of F's [[BoundArgs]] internal
				//   property.
				// 4. Let args be a new list containing the same values as the
				//   list boundArgs in the same order followed by the same
				//   values as the list ExtraArgs in the same order.
				// 5. Return the result of calling the [[Construct]] internal
				//   method of target providing args as the arguments.

				var result = target.apply(
					this,
					args.concat(_Array_slice_.call(arguments))
				);
				if (Object(result) === result) {
					return result;
				}
				return this;

			} else {
				// 15.3.4.5.1 [[Call]]
				// When the [[Call]] internal method of a function object, F,
				// which was created using the bind function is called with a
				// this value and a list of arguments ExtraArgs, the following
				// steps are taken:
				// 1. Let boundArgs be the value of F's [[BoundArgs]] internal
				//   property.
				// 2. Let boundThis be the value of F's [[BoundThis]] internal
				//   property.
				// 3. Let target be the value of F's [[TargetFunction]] internal
				//   property.
				// 4. Let args be a new list containing the same values as the
				//   list boundArgs in the same order followed by the same
				//   values as the list ExtraArgs in the same order.
				// 5. Return the result of calling the [[Call]] internal method
				//   of target providing boundThis as the this value and
				//   providing args as the arguments.

				// equiv: target.call(this, ...boundArgs, ...args)
				return target.apply(
					that,
					args.concat(_Array_slice_.call(arguments))
				);

			}

		};
		if (target.prototype) {
			Empty.prototype = target.prototype;
			bound.prototype = new Empty();
			// Clean up dangling references.
			Empty.prototype = null;
		}
		// XXX bound.length is never writable, so don't even try
		//
		// 15. If the [[Class]] internal property of Target is "Function", then
		//     a. Let L be the length property of Target minus the length of A.
		//     b. Set the length own property of F to either 0 or L, whichever is
		//       larger.
		// 16. Else set the length own property of F to 0.
		// 17. Set the attributes of the length own property of F to the values
		//   specified in 15.3.5.1.

		// TODO
		// 18. Set the [[Extensible]] internal property of F to true.

		// TODO
		// 19. Let thrower be the [[ThrowTypeError]] function Object (13.2.3).
		// 20. Call the [[DefineOwnProperty]] internal method of F with
		//   arguments "caller", PropertyDescriptor {[[Get]]: thrower, [[Set]]:
		//   thrower, [[Enumerable]]: false, [[Configurable]]: false}, and
		//   false.
		// 21. Call the [[DefineOwnProperty]] internal method of F with
		//   arguments "arguments", PropertyDescriptor {[[Get]]: thrower,
		//   [[Set]]: thrower, [[Enumerable]]: false, [[Configurable]]: false},
		//   and false.

		// TODO
		// NOTE Function objects created using Function.prototype.bind do not
		// have a prototype property or the [[Code]], [[FormalParameters]], and
		// [[Scope]] internal properties.
		// XXX can't delete prototype in pure-js.

		// 22. Return F.
		return bound;
	};
}

// Shortcut to an often accessed properties, in order to avoid multiple
// dereference that costs universally.
// _Please note: Shortcuts are defined after `Function.prototype.bind` as we
// us it in defining shortcuts.
var call = Function.prototype.call;
var prototypeOfArray = Array.prototype;
var prototypeOfObject = Object.prototype;
var _Array_slice_ = prototypeOfArray.slice;
// Having a toString local variable name breaks in Opera so use _toString.
var _toString = call.bind(prototypeOfObject.toString);
var owns = call.bind(prototypeOfObject.hasOwnProperty);

// If JS engine supports accessors creating shortcuts.
var defineGetter;
var defineSetter;
var lookupGetter;
var lookupSetter;
var supportsAccessors;
if ((supportsAccessors = owns(prototypeOfObject, "__defineGetter__"))) {
	defineGetter = call.bind(prototypeOfObject.__defineGetter__);
	defineSetter = call.bind(prototypeOfObject.__defineSetter__);
	lookupGetter = call.bind(prototypeOfObject.__lookupGetter__);
	lookupSetter = call.bind(prototypeOfObject.__lookupSetter__);
}

//
// Array
// =====
//

// ES5 15.4.4.12
// http://es5.github.com/#x15.4.4.12
// Default value for second param
// [bugfix, ielt9, old browsers]
// IE < 9 bug: [1,2].splice(0).join("") == "" but should be "12"
if ([1,2].splice(0).length != 2) {
	var array_splice = Array.prototype.splice;
	var array_push = Array.prototype.push;
	var array_unshift = Array.prototype.unshift;

	if (function() { // test IE < 9 to splice bug - see issue #138
		function makeArray(l) {
			var a = [];
			while (l--) {
				a.unshift(l)
			}
			return a
		}

		var array = []
			, lengthBefore
		;

		array.splice.bind(array, 0, 0).apply(null, makeArray(20));
		array.splice.bind(array, 0, 0).apply(null, makeArray(26));

		lengthBefore = array.length; //20
		array.splice(5, 0, "XXX"); // add one element

		if (lengthBefore + 1 == array.length) {
			return true;// has right splice implementation without bugs
		}
		// else {
		//    IE8 bug
		// }
	}()) {//IE 6/7
		Array.prototype.splice = function(start, deleteCount) {
			if (!arguments.length) {
				return [];
			} else {
				return array_splice.apply(this, [
					start === void 0 ? 0 : start,
					deleteCount === void 0 ? (this.length - start) : deleteCount
				].concat(_Array_slice_.call(arguments, 2)))
			}
		};
	}
	else {//IE8
		Array.prototype.splice = function(start, deleteCount) {
			var result
				, args = _Array_slice_.call(arguments, 2)
				, addElementsCount = args.length
			;

			if (!arguments.length) {
				return [];
			}

			if (start === void 0) { // default
				start = 0;
			}
			if (deleteCount === void 0) { // default
				deleteCount = this.length - start;
			}

			if (addElementsCount > 0) {
				if (deleteCount <= 0) {
					if (start == this.length) { // tiny optimisation #1
						array_push.apply(this, args);
						return [];
					}

					if (start == 0) { // tiny optimisation #2
						array_unshift.apply(this, args);
						return [];
					}
				}

				// Array.prototype.splice implementation
				result = _Array_slice_.call(this, start, start + deleteCount);// delete part
				args.push.apply(args, _Array_slice_.call(this, start + deleteCount, this.length));// right part
				args.unshift.apply(args, _Array_slice_.call(this, 0, start));// left part

				// delete all items from this array and replace it to 'left part' + _Array_slice_.call(arguments, 2) + 'right part'
				args.unshift(0, this.length);

				array_splice.apply(this, args);

				return result;
			}

			return array_splice.call(this, start, deleteCount);
		}

	}
}

// ES5 15.4.4.12
// http://es5.github.com/#x15.4.4.13
// Return len+argCount.
// [bugfix, ielt8]
// IE < 8 bug: [].unshift(0) == undefined but should be "1"
if ([].unshift(0) != 1) {
	var array_unshift = Array.prototype.unshift;
	Array.prototype.unshift = function() {
		array_unshift.apply(this, arguments);
		return this.length;
	};
}

// ES5 15.4.3.2
// http://es5.github.com/#x15.4.3.2
// https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/isArray
if (!Array.isArray) {
	Array.isArray = function isArray(obj) {
		return _toString(obj) == "[object Array]";
	};
}

// The IsCallable() check in the Array functions
// has been replaced with a strict check on the
// internal class of the object to trap cases where
// the provided function was actually a regular
// expression literal, which in V8 and
// JavaScriptCore is a typeof "function".  Only in
// V8 are regular expression literals permitted as
// reduce parameters, so it is desirable in the
// general case for the shim to match the more
// strict and common behavior of rejecting regular
// expressions.

// ES5 15.4.4.18
// http://es5.github.com/#x15.4.4.18
// https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/array/forEach

// Check failure of by-index access of string characters (IE < 9)
// and failure of `0 in boxedString` (Rhino)
var boxedString = Object("a"),
	splitString = boxedString[0] != "a" || !(0 in boxedString);
// Check node 0.6.21 bug where third parameter is not boxed
var boxedForEach = true;
if (Array.prototype.forEach) {
	Array.prototype.forEach.call("foo", function(item, i, obj) {
		if (typeof obj !== 'object') boxedForEach = false;
	});
}

if (!Array.prototype.forEach || !boxedForEach) {
	Array.prototype.forEach = function forEach(fun /*, thisp*/) {
		var object = toObject(this),
			self = splitString && _toString(this) == "[object String]" ?
				this.split("") :
				object,
			thisp = arguments[1],
			i = -1,
			length = self.length >>> 0;

		// If no callback function or if callback is not a callable function
		if (_toString(fun) != "[object Function]") {
			throw new TypeError(); // TODO message
		}

		while (++i < length) {
			if (i in self) {
				// Invoke the callback function with call, passing arguments:
				// context, property value, property key, thisArg object
				// context
				fun.call(thisp, self[i], i, object);
			}
		}
	};
}

// ES5 15.4.4.19
// http://es5.github.com/#x15.4.4.19
// https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Objects/Array/map
if (!Array.prototype.map) {
	Array.prototype.map = function map(fun /*, thisp*/) {
		var object = toObject(this),
			self = splitString && _toString(this) == "[object String]" ?
				this.split("") :
				object,
			length = self.length >>> 0,
			result = Array(length),
			thisp = arguments[1];

		// If no callback function or if callback is not a callable function
		if (_toString(fun) != "[object Function]") {
			throw new TypeError(fun + " is not a function");
		}

		for (var i = 0; i < length; i++) {
			if (i in self)
				result[i] = fun.call(thisp, self[i], i, object);
		}
		return result;
	};
}

// ES5 15.4.4.20
// http://es5.github.com/#x15.4.4.20
// https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Objects/Array/filter
if (!Array.prototype.filter) {
	Array.prototype.filter = function filter(fun /*, thisp */) {
		var object = toObject(this),
			self = splitString && _toString(this) == "[object String]" ?
				this.split("") :
					object,
			length = self.length >>> 0,
			result = [],
			value,
			thisp = arguments[1];

		// If no callback function or if callback is not a callable function
		if (_toString(fun) != "[object Function]") {
			throw new TypeError(fun + " is not a function");
		}

		for (var i = 0; i < length; i++) {
			if (i in self) {
				value = self[i];
				if (fun.call(thisp, value, i, object)) {
					result.push(value);
				}
			}
		}
		return result;
	};
}

// ES5 15.4.4.16
// http://es5.github.com/#x15.4.4.16
// https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/every
if (!Array.prototype.every) {
	Array.prototype.every = function every(fun /*, thisp */) {
		var object = toObject(this),
			self = splitString && _toString(this) == "[object String]" ?
				this.split("") :
				object,
			length = self.length >>> 0,
			thisp = arguments[1];

		// If no callback function or if callback is not a callable function
		if (_toString(fun) != "[object Function]") {
			throw new TypeError(fun + " is not a function");
		}

		for (var i = 0; i < length; i++) {
			if (i in self && !fun.call(thisp, self[i], i, object)) {
				return false;
			}
		}
		return true;
	};
}

// ES5 15.4.4.17
// http://es5.github.com/#x15.4.4.17
// https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/some
if (!Array.prototype.some) {
	Array.prototype.some = function some(fun /*, thisp */) {
		var object = toObject(this),
			self = splitString && _toString(this) == "[object String]" ?
				this.split("") :
				object,
			length = self.length >>> 0,
			thisp = arguments[1];

		// If no callback function or if callback is not a callable function
		if (_toString(fun) != "[object Function]") {
			throw new TypeError(fun + " is not a function");
		}

		for (var i = 0; i < length; i++) {
			if (i in self && fun.call(thisp, self[i], i, object)) {
				return true;
			}
		}
		return false;
	};
}

// ES5 15.4.4.21
// http://es5.github.com/#x15.4.4.21
// https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Objects/Array/reduce
if (!Array.prototype.reduce) {
	Array.prototype.reduce = function reduce(fun /*, initial*/) {
		var object = toObject(this),
			self = splitString && _toString(this) == "[object String]" ?
				this.split("") :
				object,
			length = self.length >>> 0;

		// If no callback function or if callback is not a callable function
		if (_toString(fun) != "[object Function]") {
			throw new TypeError(fun + " is not a function");
		}

		// no value to return if no initial value and an empty array
		if (!length && arguments.length == 1) {
			throw new TypeError("reduce of empty array with no initial value");
		}

		var i = 0;
		var result;
		if (arguments.length >= 2) {
			result = arguments[1];
		} else {
			do {
				if (i in self) {
					result = self[i++];
					break;
				}

				// if array contains no values, no initial value to return
				if (++i >= length) {
					throw new TypeError("reduce of empty array with no initial value");
				}
			} while (true);
		}

		for (; i < length; i++) {
			if (i in self) {
				result = fun.call(void 0, result, self[i], i, object);
			}
		}

		return result;
	};
}

// ES5 15.4.4.22
// http://es5.github.com/#x15.4.4.22
// https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Objects/Array/reduceRight
if (!Array.prototype.reduceRight) {
	Array.prototype.reduceRight = function reduceRight(fun /*, initial*/) {
		var object = toObject(this),
			self = splitString && _toString(this) == "[object String]" ?
				this.split("") :
				object,
			length = self.length >>> 0;

		// If no callback function or if callback is not a callable function
		if (_toString(fun) != "[object Function]") {
			throw new TypeError(fun + " is not a function");
		}

		// no value to return if no initial value, empty array
		if (!length && arguments.length == 1) {
			throw new TypeError("reduceRight of empty array with no initial value");
		}

		var result, i = length - 1;
		if (arguments.length >= 2) {
			result = arguments[1];
		} else {
			do {
				if (i in self) {
					result = self[i--];
					break;
				}

				// if array contains no values, no initial value to return
				if (--i < 0) {
					throw new TypeError("reduceRight of empty array with no initial value");
				}
			} while (true);
		}

		if (i < 0) {
			return result;
		}

		do {
			if (i in this) {
				result = fun.call(void 0, result, self[i], i, object);
			}
		} while (i--);

		return result;
	};
}

// ES5 15.4.4.14
// http://es5.github.com/#x15.4.4.14
// https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/indexOf
if (!Array.prototype.indexOf || ([0, 1].indexOf(1, 2) != -1)) {
	Array.prototype.indexOf = function indexOf(sought /*, fromIndex */ ) {
		var self = splitString && _toString(this) == "[object String]" ?
				this.split("") :
				toObject(this),
			length = self.length >>> 0;

		if (!length) {
			return -1;
		}

		var i = 0;
		if (arguments.length > 1) {
			i = toInteger(arguments[1]);
		}

		// handle negative indices
		i = i >= 0 ? i : Math.max(0, length + i);
		for (; i < length; i++) {
			if (i in self && self[i] === sought) {
				return i;
			}
		}
		return -1;
	};
}

// ES5 15.4.4.15
// http://es5.github.com/#x15.4.4.15
// https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/lastIndexOf
if (!Array.prototype.lastIndexOf || ([0, 1].lastIndexOf(0, -3) != -1)) {
	Array.prototype.lastIndexOf = function lastIndexOf(sought /*, fromIndex */) {
		var self = splitString && _toString(this) == "[object String]" ?
				this.split("") :
				toObject(this),
			length = self.length >>> 0;

		if (!length) {
			return -1;
		}
		var i = length - 1;
		if (arguments.length > 1) {
			i = Math.min(i, toInteger(arguments[1]));
		}
		// handle negative indices
		i = i >= 0 ? i : length - Math.abs(i);
		for (; i >= 0; i--) {
			if (i in self && sought === self[i]) {
				return i;
			}
		}
		return -1;
	};
}

//
// Object
// ======
//

// ES5 15.2.3.14
// http://es5.github.com/#x15.2.3.14
if (!Object.keys) {
	// http://whattheheadsaid.com/2010/10/a-safer-object-keys-compatibility-implementation
	var hasDontEnumBug = true,
		dontEnums = [
			"toString",
			"toLocaleString",
			"valueOf",
			"hasOwnProperty",
			"isPrototypeOf",
			"propertyIsEnumerable",
			"constructor"
		],
		dontEnumsLength = dontEnums.length;

	for (var key in {"toString": null}) {
		hasDontEnumBug = false;
	}

	Object.keys = function keys(object) {

		if (
			(typeof object != "object" && typeof object != "function") ||
			object === null
		) {
			throw new TypeError("Object.keys called on a non-object");
		}

		var keys = [];
		for (var name in object) {
			if (owns(object, name)) {
				keys.push(name);
			}
		}

		if (hasDontEnumBug) {
			for (var i = 0, ii = dontEnumsLength; i < ii; i++) {
				var dontEnum = dontEnums[i];
				if (owns(object, dontEnum)) {
					keys.push(dontEnum);
				}
			}
		}
		return keys;
	};

}

//
// Date
// ====
//

// ES5 15.9.5.43
// http://es5.github.com/#x15.9.5.43
// This function returns a String value represent the instance in time
// represented by this Date object. The format of the String is the Date Time
// string format defined in 15.9.1.15. All fields are present in the String.
// The time zone is always UTC, denoted by the suffix Z. If the time value of
// this object is not a finite Number a RangeError exception is thrown.
var negativeDate = -62198755200000,
	negativeYearString = "-000001";
if (
	!Date.prototype.toISOString ||
	(new Date(negativeDate).toISOString().indexOf(negativeYearString) === -1)
) {
	Date.prototype.toISOString = function toISOString() {
		var result, length, value, year, month;
		if (!isFinite(this)) {
			throw new RangeError("Date.prototype.toISOString called on non-finite value.");
		}

		year = this.getUTCFullYear();

		month = this.getUTCMonth();
		// see https://github.com/kriskowal/es5-shim/issues/111
		year += Math.floor(month / 12);
		month = (month % 12 + 12) % 12;

		// the date time string format is specified in 15.9.1.15.
		result = [month + 1, this.getUTCDate(),
			this.getUTCHours(), this.getUTCMinutes(), this.getUTCSeconds()];
		year = (
			(year < 0 ? "-" : (year > 9999 ? "+" : "")) +
			("00000" + Math.abs(year))
			.slice(0 <= year && year <= 9999 ? -4 : -6)
		);

		length = result.length;
		while (length--) {
			value = result[length];
			// pad months, days, hours, minutes, and seconds to have two
			// digits.
			if (value < 10) {
				result[length] = "0" + value;
			}
		}
		// pad milliseconds to have three digits.
		return (
			year + "-" + result.slice(0, 2).join("-") +
			"T" + result.slice(2).join(":") + "." +
			("000" + this.getUTCMilliseconds()).slice(-3) + "Z"
		);
	};
}


// ES5 15.9.5.44
// http://es5.github.com/#x15.9.5.44
// This function provides a String representation of a Date object for use by
// JSON.stringify (15.12.3).
var dateToJSONIsSupported = false;
try {
	dateToJSONIsSupported = (
		Date.prototype.toJSON &&
		new Date(NaN).toJSON() === null &&
		new Date(negativeDate).toJSON().indexOf(negativeYearString) !== -1 &&
		Date.prototype.toJSON.call({ // generic
			toISOString: function () {
				return true;
			}
		})
	);
} catch (e) {
}
if (!dateToJSONIsSupported) {
	Date.prototype.toJSON = function toJSON(key) {
		// When the toJSON method is called with argument key, the following
		// steps are taken:

		// 1.  Let O be the result of calling ToObject, giving it the this
		// value as its argument.
		// 2. Let tv be toPrimitive(O, hint Number).
		var o = Object(this),
			tv = toPrimitive(o),
			toISO;
		// 3. If tv is a Number and is not finite, return null.
		if (typeof tv === "number" && !isFinite(tv)) {
			return null;
		}
		// 4. Let toISO be the result of calling the [[Get]] internal method of
		// O with argument "toISOString".
		toISO = o.toISOString;
		// 5. If IsCallable(toISO) is false, throw a TypeError exception.
		if (typeof toISO != "function") {
			throw new TypeError("toISOString property is not callable");
		}
		// 6. Return the result of calling the [[Call]] internal method of
		//  toISO with O as the this value and an empty argument list.
		return toISO.call(o);

		// NOTE 1 The argument is ignored.

		// NOTE 2 The toJSON function is intentionally generic; it does not
		// require that its this value be a Date object. Therefore, it can be
		// transferred to other kinds of objects for use as a method. However,
		// it does require that any such object have a toISOString method. An
		// object is free to use the argument key to filter its
		// stringification.
	};
}

// ES5 15.9.4.2
// http://es5.github.com/#x15.9.4.2
// based on work shared by Daniel Friesen (dantman)
// http://gist.github.com/303249
if (!Date.parse || "Date.parse is buggy") {
	// XXX global assignment won't work in embeddings that use
	// an alternate object for the context.
	Date = (function(NativeDate) {

		// Date.length === 7
		function Date(Y, M, D, h, m, s, ms) {
			var length = arguments.length;
			if (this instanceof NativeDate) {
				var date = length == 1 && String(Y) === Y ? // isString(Y)
					// We explicitly pass it through parse:
					new NativeDate(Date.parse(Y)) :
					// We have to manually make calls depending on argument
					// length here
					length >= 7 ? new NativeDate(Y, M, D, h, m, s, ms) :
					length >= 6 ? new NativeDate(Y, M, D, h, m, s) :
					length >= 5 ? new NativeDate(Y, M, D, h, m) :
					length >= 4 ? new NativeDate(Y, M, D, h) :
					length >= 3 ? new NativeDate(Y, M, D) :
					length >= 2 ? new NativeDate(Y, M) :
					length >= 1 ? new NativeDate(Y) :
								  new NativeDate();
				// Prevent mixups with unfixed Date object
				date.constructor = Date;
				return date;
			}
			return NativeDate.apply(this, arguments);
		};

		// 15.9.1.15 Date Time String Format.
		var isoDateExpression = new RegExp("^" +
			"(\\d{4}|[\+\-]\\d{6})" + // four-digit year capture or sign +
									  // 6-digit extended year
			"(?:-(\\d{2})" + // optional month capture
			"(?:-(\\d{2})" + // optional day capture
			"(?:" + // capture hours:minutes:seconds.milliseconds
				"T(\\d{2})" + // hours capture
				":(\\d{2})" + // minutes capture
				"(?:" + // optional :seconds.milliseconds
					":(\\d{2})" + // seconds capture
					"(?:(\\.\\d{1,}))?" + // milliseconds capture
				")?" +
			"(" + // capture UTC offset component
				"Z|" + // UTC capture
				"(?:" + // offset specifier +/-hours:minutes
					"([-+])" + // sign capture
					"(\\d{2})" + // hours offset capture
					":(\\d{2})" + // minutes offset capture
				")" +
			")?)?)?)?" +
		"$");

		var months = [
			0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365
		];

		function dayFromMonth(year, month) {
			var t = month > 1 ? 1 : 0;
			return (
				months[month] +
				Math.floor((year - 1969 + t) / 4) -
				Math.floor((year - 1901 + t) / 100) +
				Math.floor((year - 1601 + t) / 400) +
				365 * (year - 1970)
			);
		}

		function toUTC(t) {
			return Number(new NativeDate(1970, 0, 1, 0, 0, 0, t));
		}

		// Copy any custom methods a 3rd party library may have added
		for (var key in NativeDate) {
			Date[key] = NativeDate[key];
		}

		// Copy "native" methods explicitly; they may be non-enumerable
		Date.now = NativeDate.now;
		Date.UTC = NativeDate.UTC;
		Date.prototype = NativeDate.prototype;
		Date.prototype.constructor = Date;

		// Upgrade Date.parse to handle simplified ISO 8601 strings
		Date.parse = function parse(string) {
			var match = isoDateExpression.exec(string);
			if (match) {
				// parse months, days, hours, minutes, seconds, and milliseconds
				// provide default values if necessary
				// parse the UTC offset component
				var year = Number(match[1]),
					month = Number(match[2] || 1) - 1,
					day = Number(match[3] || 1) - 1,
					hour = Number(match[4] || 0),
					minute = Number(match[5] || 0),
					second = Number(match[6] || 0),
					millisecond = Math.floor(Number(match[7] || 0) * 1000),
					// When time zone is missed, local offset should be used
					// (ES 5.1 bug)
					// see https://bugs.ecmascript.org/show_bug.cgi?id=112
					isLocalTime = Boolean(match[4] && !match[8]),
					signOffset = match[9] === "-" ? 1 : -1,
					hourOffset = Number(match[10] || 0),
					minuteOffset = Number(match[11] || 0),
					result;
				if (
					hour < (
						minute > 0 || second > 0 || millisecond > 0 ?
						24 : 25
					) &&
					minute < 60 && second < 60 && millisecond < 1000 &&
					month > -1 && month < 12 && hourOffset < 24 &&
					minuteOffset < 60 && // detect invalid offsets
					day > -1 &&
					day < (
						dayFromMonth(year, month + 1) -
						dayFromMonth(year, month)
					)
				) {
					result = (
						(dayFromMonth(year, month) + day) * 24 +
						hour +
						hourOffset * signOffset
					) * 60;
					result = (
						(result + minute + minuteOffset * signOffset) * 60 +
						second
					) * 1000 + millisecond;
					if (isLocalTime) {
						result = toUTC(result);
					}
					if (-8.64e15 <= result && result <= 8.64e15) {
						return result;
					}
				}
				return NaN;
			}
			return NativeDate.parse.apply(this, arguments);
		};

		return Date;
	})(Date);
}

// ES5 15.9.4.4
// http://es5.github.com/#x15.9.4.4
if (!Date.now) {
	Date.now = function now() {
		return new Date().getTime();
	};
}


//
// Number
// ======
//

// ES5.1 15.7.4.5
// http://es5.github.com/#x15.7.4.5
if (!Number.prototype.toFixed || (0.00008).toFixed(3) !== '0.000' || (0.9).toFixed(0) === '0' || (1.255).toFixed(2) !== '1.25' || (1000000000000000128).toFixed(0) !== "1000000000000000128") {
	// Hide these variables and functions
	(function () {
		var base, size, data, i;

		base = 1e7;
		size = 6;
		data = [0, 0, 0, 0, 0, 0];

		function multiply(n, c) {
			var i = -1;
			while (++i < size) {
				c += n * data[i];
				data[i] = c % base;
				c = Math.floor(c / base);
			}
		}

		function divide(n) {
			var i = size, c = 0;
			while (--i >= 0) {
				c += data[i];
				data[i] = Math.floor(c / n);
				c = (c % n) * base;
			}
		}

		function toString() {
			var i = size;
			var s = '';
			while (--i >= 0) {
				if (s !== '' || i === 0 || data[i] !== 0) {
					var t = String(data[i]);
					if (s === '') {
						s = t;
					} else {
						s += '0000000'.slice(0, 7 - t.length) + t;
					}
				}
			}
			return s;
		}

		function pow(x, n, acc) {
			return (n === 0 ? acc : (n % 2 === 1 ? pow(x, n - 1, acc * x) : pow(x * x, n / 2, acc)));
		}

		function log(x) {
			var n = 0;
			while (x >= 4096) {
				n += 12;
				x /= 4096;
			}
			while (x >= 2) {
				n += 1;
				x /= 2;
			}
			return n;
		}

		Number.prototype.toFixed = function (fractionDigits) {
			var f, x, s, m, e, z, j, k;

			// Test for NaN and round fractionDigits down
			f = Number(fractionDigits);
			f = f !== f ? 0 : Math.floor(f);

			if (f < 0 || f > 20) {
				throw new RangeError("Number.toFixed called with invalid number of decimals");
			}

			x = Number(this);

			// Test for NaN
			if (x !== x) {
				return "NaN";
			}

			// If it is too big or small, return the string value of the number
			if (x <= -1e21 || x >= 1e21) {
				return String(x);
			}

			s = "";

			if (x < 0) {
				s = "-";
				x = -x;
			}

			m = "0";

			if (x > 1e-21) {
				// 1e-21 < x < 1e21
				// -70 < log2(x) < 70
				e = log(x * pow(2, 69, 1)) - 69;
				z = (e < 0 ? x * pow(2, -e, 1) : x / pow(2, e, 1));
				z *= 0x10000000000000; // Math.pow(2, 52);
				e = 52 - e;

				// -18 < e < 122
				// x = z / 2 ^ e
				if (e > 0) {
					multiply(0, z);
					j = f;

					while (j >= 7) {
						multiply(1e7, 0);
						j -= 7;
					}

					multiply(pow(10, j, 1), 0);
					j = e - 1;

					while (j >= 23) {
						divide(1 << 23);
						j -= 23;
					}

					divide(1 << j);
					multiply(1, 1);
					divide(2);
					m = toString();
				} else {
					multiply(0, z);
					multiply(1 << (-e), 0);
					m = toString() + '0.00000000000000000000'.slice(2, 2 + f);
				}
			}

			if (f > 0) {
				k = m.length;

				if (k <= f) {
					m = s + '0.0000000000000000000'.slice(0, f - k + 2) + m;
				} else {
					m = s + m.slice(0, k - f) + '.' + m.slice(k - f);
				}
			} else {
				m = s + m;
			}

			return m;
		}
	}());
}


//
// String
// ======
//


// ES5 15.5.4.14
// http://es5.github.com/#x15.5.4.14

// [bugfix, IE lt 9, firefox 4, Konqueror, Opera, obscure browsers]
// Many browsers do not split properly with regular expressions or they
// do not perform the split correctly under obscure conditions.
// See http://blog.stevenlevithan.com/archives/cross-browser-split
// I've tested in many browsers and this seems to cover the deviant ones:
//    'ab'.split(/(?:ab)*/) should be ["", ""], not [""]
//    '.'.split(/(.?)(.?)/) should be ["", ".", "", ""], not ["", ""]
//    'tesst'.split(/(s)*/) should be ["t", undefined, "e", "s", "t"], not
//       [undefined, "t", undefined, "e", ...]
//    ''.split(/.?/) should be [], not [""]
//    '.'.split(/()()/) should be ["."], not ["", "", "."]

var string_split = String.prototype.split;
if (
	'ab'.split(/(?:ab)*/).length !== 2 ||
	'.'.split(/(.?)(.?)/).length !== 4 ||
	'tesst'.split(/(s)*/)[1] === "t" ||
	''.split(/.?/).length ||
	'.'.split(/()()/).length > 1
) {
	(function () {
		var compliantExecNpcg = /()??/.exec("")[1] === void 0; // NPCG: nonparticipating capturing group

		String.prototype.split = function (separator, limit) {
			var string = this;
			if (separator === void 0 && limit === 0)
				return [];

			// If `separator` is not a regex, use native split
			if (Object.prototype.toString.call(separator) !== "[object RegExp]") {
				return string_split.apply(this, arguments);
			}

			var output = [],
				flags = (separator.ignoreCase ? "i" : "") +
						(separator.multiline  ? "m" : "") +
						(separator.extended   ? "x" : "") + // Proposed for ES6
						(separator.sticky     ? "y" : ""), // Firefox 3+
				lastLastIndex = 0,
				// Make `global` and avoid `lastIndex` issues by working with a copy
				separator = new RegExp(separator.source, flags + "g"),
				separator2, match, lastIndex, lastLength;
			string += ""; // Type-convert
			if (!compliantExecNpcg) {
				// Doesn't need flags gy, but they don't hurt
				separator2 = new RegExp("^" + separator.source + "$(?!\\s)", flags);
			}
			/* Values for `limit`, per the spec:
			 * If undefined: 4294967295 // Math.pow(2, 32) - 1
			 * If 0, Infinity, or NaN: 0
			 * If positive number: limit = Math.floor(limit); if (limit > 4294967295) limit -= 4294967296;
			 * If negative number: 4294967296 - Math.floor(Math.abs(limit))
			 * If other: Type-convert, then use the above rules
			 */
			limit = limit === void 0 ?
				-1 >>> 0 : // Math.pow(2, 32) - 1
				limit >>> 0; // ToUint32(limit)
			while (match = separator.exec(string)) {
				// `separator.lastIndex` is not reliable cross-browser
				lastIndex = match.index + match[0].length;
				if (lastIndex > lastLastIndex) {
					output.push(string.slice(lastLastIndex, match.index));
					// Fix browsers whose `exec` methods don't consistently return `undefined` for
					// nonparticipating capturing groups
					if (!compliantExecNpcg && match.length > 1) {
						match[0].replace(separator2, function () {
							for (var i = 1; i < arguments.length - 2; i++) {
								if (arguments[i] === void 0) {
									match[i] = void 0;
								}
							}
						});
					}
					if (match.length > 1 && match.index < string.length) {
						Array.prototype.push.apply(output, match.slice(1));
					}
					lastLength = match[0].length;
					lastLastIndex = lastIndex;
					if (output.length >= limit) {
						break;
					}
				}
				if (separator.lastIndex === match.index) {
					separator.lastIndex++; // Avoid an infinite loop
				}
			}
			if (lastLastIndex === string.length) {
				if (lastLength || !separator.test("")) {
					output.push("");
				}
			} else {
				output.push(string.slice(lastLastIndex));
			}
			return output.length > limit ? output.slice(0, limit) : output;
		};
	}());

// [bugfix, chrome]
// If separator is undefined, then the result array contains just one String,
// which is the this value (converted to a String). If limit is not undefined,
// then the output array is truncated so that it contains no more than limit
// elements.
// "0".split(undefined, 0) -> []
} else if ("0".split(void 0, 0).length) {
	String.prototype.split = function(separator, limit) {
		if (separator === void 0 && limit === 0) return [];
		return string_split.apply(this, arguments);
	}
}


// ECMA-262, 3rd B.2.3
// Note an ECMAScript standart, although ECMAScript 3rd Edition has a
// non-normative section suggesting uniform semantics and it should be
// normalized across all browsers
// [bugfix, IE lt 9] IE < 9 substr() with negative value not working in IE
if ("".substr && "0b".substr(-1) !== "b") {
	var string_substr = String.prototype.substr;
	/**
	 *  Get the substring of a string
	 *  @param  {integer}  start   where to start the substring
	 *  @param  {integer}  length  how many characters to return
	 *  @return {string}
	 */
	String.prototype.substr = function(start, length) {
		return string_substr.call(
			this,
			start < 0 ? ((start = this.length + start) < 0 ? 0 : start) : start,
			length
		);
	}
}

// ES5 15.5.4.20
// http://es5.github.com/#x15.5.4.20
var ws = "\x09\x0A\x0B\x0C\x0D\x20\xA0\u1680\u180E\u2000\u2001\u2002\u2003" +
	"\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028" +
	"\u2029\uFEFF";
if (!String.prototype.trim || ws.trim()) {
	// http://blog.stevenlevithan.com/archives/faster-trim-javascript
	// http://perfectionkills.com/whitespace-deviations/
	ws = "[" + ws + "]";
	var trimBeginRegexp = new RegExp("^" + ws + ws + "*"),
		trimEndRegexp = new RegExp(ws + ws + "*$");
	String.prototype.trim = function trim() {
		if (this === void 0 || this === null) {
			throw new TypeError("can't convert "+this+" to object");
		}
		return String(this)
			.replace(trimBeginRegexp, "")
			.replace(trimEndRegexp, "");
	};
}

//
// Util
// ======
//

// ES5 9.4
// http://es5.github.com/#x9.4
// http://jsperf.com/to-integer

function toInteger(n) {
	n = +n;
	if (n !== n) { // isNaN
		n = 0;
	} else if (n !== 0 && n !== (1/0) && n !== -(1/0)) {
		n = (n > 0 || -1) * Math.floor(Math.abs(n));
	}
	return n;
}

function isPrimitive(input) {
	var type = typeof input;
	return (
		input === null ||
		type === "undefined" ||
		type === "boolean" ||
		type === "number" ||
		type === "string"
	);
}

function toPrimitive(input) {
	var val, valueOf, toString;
	if (isPrimitive(input)) {
		return input;
	}
	valueOf = input.valueOf;
	if (typeof valueOf === "function") {
		val = valueOf.call(input);
		if (isPrimitive(val)) {
			return val;
		}
	}
	toString = input.toString;
	if (typeof toString === "function") {
		val = toString.call(input);
		if (isPrimitive(val)) {
			return val;
		}
	}
	throw new TypeError();
}

// ES5 9.9
// http://es5.github.com/#x9.9
var toObject = function (o) {
	if (o == null) { // this matches both null and undefined
		throw new TypeError("can't convert "+o+" to object");
	}
	return Object(o);
};

});

/**
 * lscache library
 * Copyright (c) 2011, Pamela Fox
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/* jshint undef:true, browser:true, node:true */
/* global define */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define([], factory);
    } else if (typeof module !== "undefined" && module.exports) {
        // CommonJS/Node module
        module.exports = factory();
    } else {
        // Browser globals
        root.lscache = factory();
    }
}(this, function () {

  // Prefix for all lscache keys
  var CACHE_PREFIX = 'lscache-';

  // Suffix for the key name on the expiration items in localStorage
  var CACHE_SUFFIX = '-cacheexpiration';

  // expiration date radix (set to Base-36 for most space savings)
  var EXPIRY_RADIX = 10;

  // time resolution in minutes
  var EXPIRY_UNITS = 60 * 1000;

  // ECMAScript max Date (epoch + 1e8 days)
  var MAX_DATE = Math.floor(8.64e15/EXPIRY_UNITS);

  var cachedStorage;
  var cachedJSON;
  var cacheBucket = '';
  var warnings = false;

  // Determines if localStorage is supported in the browser;
  // result is cached for better performance instead of being run each time.
  // Feature detection is based on how Modernizr does it;
  // it's not straightforward due to FF4 issues.
  // It's not run at parse-time as it takes 200ms in Android.
  function supportsStorage() {
    var key = '__lscachetest__';
    var value = key;

    if (cachedStorage !== undefined) {
      return cachedStorage;
    }

    try {
      setItem(key, value);
      removeItem(key);
      cachedStorage = true;
    } catch (e) {
        if (isOutOfSpace(e)) {    // If we hit the limit, then it means we have support, 
            cachedStorage = true; // just maxed it out and even the set test failed.
        } else {
            cachedStorage = false;
        }
    }
    return cachedStorage;
  }

  // Check to set if the error is us dealing with being out of space
  function isOutOfSpace(e) {
    if (e && e.name === 'QUOTA_EXCEEDED_ERR' || 
            e.name === 'NS_ERROR_DOM_QUOTA_REACHED' || 
            e.name === 'QuotaExceededError') {
        return true;
    }
    return false;
  }

  // Determines if native JSON (de-)serialization is supported in the browser.
  function supportsJSON() {
    /*jshint eqnull:true */
    if (cachedJSON === undefined) {
      cachedJSON = (window.JSON != null);
    }
    return cachedJSON;
  }

  /**
   * Returns the full string for the localStorage expiration item.
   * @param {String} key
   * @return {string}
   */
  function expirationKey(key) {
    return key + CACHE_SUFFIX;
  }

  /**
   * Returns the number of minutes since the epoch.
   * @return {number}
   */
  function currentTime() {
    return Math.floor((new Date().getTime())/EXPIRY_UNITS);
  }

  /**
   * Wrapper functions for localStorage methods
   */

  function getItem(key) {
    return localStorage.getItem(CACHE_PREFIX + cacheBucket + key);
  }

  function setItem(key, value) {
    // Fix for iPad issue - sometimes throws QUOTA_EXCEEDED_ERR on setItem.
    localStorage.removeItem(CACHE_PREFIX + cacheBucket + key);
    localStorage.setItem(CACHE_PREFIX + cacheBucket + key, value);
  }

  function removeItem(key) {
    localStorage.removeItem(CACHE_PREFIX + cacheBucket + key);
  }

  function eachKey(fn) {
    var prefixRegExp = new RegExp('^' + CACHE_PREFIX + cacheBucket + '(.*)');
    // Loop in reverse as removing items will change indices of tail
    for (var i = localStorage.length-1; i >= 0 ; --i) {
      var key = localStorage.key(i);
      key = key && key.match(prefixRegExp);
      key = key && key[1];
      if (key && key.indexOf(CACHE_SUFFIX) < 0) {
        fn(key, expirationKey(key));
      }
    }
  }

  function flushItem(key) {
    var exprKey = expirationKey(key);

    removeItem(key);
    removeItem(exprKey);
  }

  function flushExpiredItem(key) {
    var exprKey = expirationKey(key);
    var expr = getItem(exprKey);

    if (expr) {
      var expirationTime = parseInt(expr, EXPIRY_RADIX);

      // Check if we should actually kick item out of storage
      if (currentTime() >= expirationTime) {
        removeItem(key);
        removeItem(exprKey);
        return true;
      }
    }
  }

  function warn(message, err) {
    if (!warnings) return;
    if (!('console' in window) || typeof window.console.warn !== 'function') return;
    window.console.warn("lscache - " + message);
    if (err) window.console.warn("lscache - The error was: " + err.message);
  }

  var lscache = {
    /**
     * Stores the value in localStorage. Expires after specified number of minutes.
     * @param {string} key
     * @param {Object|string} value
     * @param {number} time
     */
    set: function(key, value, time) {
      if (!supportsStorage()) return;

      // If we don't get a string value, try to stringify
      // In future, localStorage may properly support storing non-strings
      // and this can be removed.
      if (typeof value !== 'string') {
        if (!supportsJSON()) return;
        try {
          value = JSON.stringify(value);
        } catch (e) {
          // Sometimes we can't stringify due to circular refs
          // in complex objects, so we won't bother storing then.
          return;
        }
      }

      try {
        setItem(key, value);
      } catch (e) {
        if (isOutOfSpace(e)) {
          // If we exceeded the quota, then we will sort
          // by the expire time, and then remove the N oldest
          var storedKeys = [];
          var storedKey;
          eachKey(function(key, exprKey) {
            var expiration = getItem(exprKey);
            if (expiration) {
              expiration = parseInt(expiration, EXPIRY_RADIX);
            } else {
              // TODO: Store date added for non-expiring items for smarter removal
              expiration = MAX_DATE;
            }
            storedKeys.push({
              key: key,
              size: (getItem(key) || '').length,
              expiration: expiration
            });
          });
          // Sorts the keys with oldest expiration time last
          storedKeys.sort(function(a, b) { return (b.expiration-a.expiration); });

          var targetSize = (value||'').length;
          while (storedKeys.length && targetSize > 0) {
            storedKey = storedKeys.pop();
            warn("Cache is full, removing item with key '" + key + "'");
            flushItem(storedKey.key);
            targetSize -= storedKey.size;
          }
          try {
            setItem(key, value);
          } catch (e) {
            // value may be larger than total quota
            warn("Could not add item with key '" + key + "', perhaps it's too big?", e);
            return;
          }
        } else {
          // If it was some other error, just give up.
          warn("Could not add item with key '" + key + "'", e);
          return;
        }
      }

      // If a time is specified, store expiration info in localStorage
      if (time) {
        setItem(expirationKey(key), (currentTime() + time).toString(EXPIRY_RADIX));
      } else {
        // In case they previously set a time, remove that info from localStorage.
        removeItem(expirationKey(key));
      }
    },

    /**
     * Retrieves specified value from localStorage, if not expired.
     * @param {string} key
     * @return {string|Object}
     */
    get: function(key) {
      if (!supportsStorage()) return null;

      // Return the de-serialized item if not expired
      if (flushExpiredItem(key)) { return null; }

      // Tries to de-serialize stored value if its an object, and returns the normal value otherwise.
      var value = getItem(key);
      if (!value || !supportsJSON()) {
        return value;
      }

      try {
        // We can't tell if its JSON or a string, so we try to parse
        return JSON.parse(value);
      } catch (e) {
        // If we can't parse, it's probably because it isn't an object
        return value;
      }
    },

    /**
     * Removes a value from localStorage.
     * Equivalent to 'delete' in memcache, but that's a keyword in JS.
     * @param {string} key
     */
    remove: function(key) {
      if (!supportsStorage()) return;

      flushItem(key);
    },

    /**
     * Returns whether local storage is supported.
     * Currently exposed for testing purposes.
     * @return {boolean}
     */
    supported: function() {
      return supportsStorage();
    },

    /**
     * Flushes all lscache items and expiry markers without affecting rest of localStorage
     */
    flush: function() {
      if (!supportsStorage()) return;

      eachKey(function(key) {
        flushItem(key);
      });
    },

    /**
     * Flushes expired lscache items and expiry markers without affecting rest of localStorage
     */
    flushExpired: function() {
      if (!supportsStorage()) return;

      eachKey(function(key) {
        flushExpiredItem(key);
      });
    },

    /**
     * Appends CACHE_PREFIX so lscache will partition data in to different buckets.
     * @param {string} bucket
     */
    setBucket: function(bucket) {
      cacheBucket = bucket;
    },

    /**
     * Resets the string being appended to CACHE_PREFIX so lscache will use the default storage behavior.
     */
    resetBucket: function() {
      cacheBucket = '';
    },

    /**
     * Sets whether to display warnings when an item is removed from the cache or not.
     */
    enableWarnings: function(enabled) {
      warnings = enabled;
    }
  };

  // Return the module
  return lscache;
}));

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
		console.info('Выполнение пакетного запроса', reqArray);

		var
			dataToSend = {},
			callbacks = [],

			i, len;
		// end of vars
		
		dataToSend.actions = [];
		
		var 
			resHandler = function resHandler( res ) {
				var
					i, len;
				// end of vars

				console.info('Обработка ответа пакетого запроса', res);

				if ( res.success === false || (res.actions && res.actions.length === 0) ) {
					console.warn('Route false');
					console.log(res.success);
					console.log(res.actions);
				}

				for ( i = 0, len = res.actions.length - 1; i <= len; i++ ) {
					callbacks[i](res.actions[i]);
				}
			};
		// end of functions

		for ( i = 0, len = reqArray.length - 1; i <= len; i++ ) {

			// Обход странного бага с IE
			if ( !(reqArray[i] && reqArray[i].url) ) {
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

		if ( !dataToSend.actions.length ) {
			return;
		}

		$.ajax({
			url: pageConfig.routeUrl,
			type: 'POST',
			data: dataToSend,
			success: resHandler
		});
	};
}(this));
;(function ( ENTER ) {
	var $body = $(document.body),
		utils = ENTER.utils;
	// end of vars

	utils.trim = function(string) {
		return ((string || '') + '').replace(/^\s+|\s+$/g, '');
	};

	/**
	 * Возвращает колчество свойств в объекте.
	 *
	 * @param	{Object}	obj
	 * 
	 * @return	{Number}	count
	 */
	utils.objLen = function objLen( obj ) {
		var
			len = 0,
			p;
		// end of vars

		for ( p in obj ) {
			if ( obj.hasOwnProperty(p) ) {
				len++;
			}
		}

		return len;
	};

	/**
	 * Возвращает гет-параметр с именем paramName у ссылки url
	 *
	 * @param 		{string}	paramName
	 * @param 		{string}	url
	 * @returns 	{string}	{*}
	 */
	utils.getURLParam = function getURLParam(paramName, url) {
		var result = new RegExp('[\\?&]' + utils.escapeRegexp(encodeURIComponent(paramName)) + '=([^&#]*)').exec(url);
		if (result) {
			return decodeURIComponent(result[1]);
		}

		return null;
	};

	/**
	 * Добавляет get-параметр с именем paramName к url или заменяет существующий (удаляя указанный параметр, если paramValue равно null)
	 *
	 * @param 		{string}	paramName
	 * @param 		{string}	paramValue
	 * @param 		{string}	url
	 * @returns 	{string}	Новый URL
	 */
	utils.setURLParam = function(paramName, paramValue, url) {
		var regexp = new RegExp('([\\?&])(' + utils.escapeRegexp(encodeURIComponent(paramName)) + '=)[^&#]*');

		if (regexp.exec(url) === null) {
			if (url.indexOf('?') == -1) {
				url += '?';
			} else if (url.indexOf('?') < url.length - 1) {
				url += '&';
			}

			url += encodeURIComponent(paramName) + '=' + encodeURIComponent(paramValue);
			return url;
		} else if (paramValue === null) {
			return url.replace(regexp, '$1').replace(/\?\&/, '?').replace(/\&\&/, '&').replace(/[\?\&]$/, '');
		} else {
			return url.replace(regexp, '$1$2' + encodeURIComponent(paramValue));
		}
	};

	/**
	 * Извлекает строку запроса из URL адреса и преобразует её в объект (который и возвращает).
	 * @param {string} url
	 * @returns {{}}
	 */
	utils.parseUrlParams = function(url) {
		var
			result = {},
			params = url.replace(/^[^?]*\?|\#.*$/g, '').split('&');

		for (var i = 0; i < params.length; i++) {
			var param = params[i].split('=');

			if (!param[0]) {
				param[0] = '';
			}

			if (!param[1]) {
				param[1] = '';
			}

			param[0] = decodeURIComponent(param[0]);
			param[1] = decodeURIComponent(param[1]);

			result[param[0]] = param[1];
		}

		return result;
	};

	/**
	 * Экранирует спец. символы регулярного выражения в строке
	 *
	 * @param 		{string}	string
	 * @returns 	{string}	Экранированная строка
	 */
	utils.escapeRegexp = function(string) {
		return string.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
	};

	/**
	 * @param {string} routeName
	 * @param {object} [params]
	 * @return {string}
	 */
	utils.generateUrl = function(routeName, params) {
		var url = ENTER.config.pageConfig.routes[routeName]['pattern'];
		$.each((params || {}), function(paramName, paramValue){
			if (url.indexOf('{' + paramName + '}') != -1) {
				url = url.replace('{' + paramName + '}', paramValue);
			} else {
				var params = {};
				params[paramName] = paramValue;
				url += (url.indexOf('?') == -1 ? '?' : '&') + $.param(params);
			}
		});

		return url;
	};

	utils.getObjectWithElement = function(array, elementKey, expectedElementValue) {
		var object = null;
		if (array) {
			$.each(array, function(arrayKey, arrayValue){
				if (arrayValue[elementKey] === expectedElementValue) {
					object = arrayValue;
					return false;
				}
			});
		}

		return object;
	};

	utils.validateEmail = function(email) {
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	};

	utils.checkEan = function checkEanF(data) {
		// Check if only digits
		var ValidChars = "0123456789",
			i, digit, originalCheck, even, odd, total, checksum, eanCode;

		eanCode = data.toString().replace(/\s+/g, '');

		for (i = 0; i < eanCode.length; i++) {
			digit = eanCode.charAt(i);
			if (ValidChars.indexOf(digit) == -1) return false;
		}

		// Add five 0 if the code has only 8 digits
		if (eanCode.length == 8 ) eanCode = "00000" + eanCode;
		// Check for 13 digits otherwise
		else if (eanCode.length != 13) return false;

		// Get the check number
		originalCheck = eanCode.substring(eanCode.length - 1);
		eanCode = eanCode.substring(0, eanCode.length - 1);

		// Add even numbers together
		even = Number(eanCode.charAt(1)) +
		Number(eanCode.charAt(3)) +
		Number(eanCode.charAt(5)) +
		Number(eanCode.charAt(7)) +
		Number(eanCode.charAt(9)) +
		Number(eanCode.charAt(11));
		// Multiply this result by 3
		even *= 3;

		// Add odd numbers together
		odd = Number(eanCode.charAt(0)) +
		Number(eanCode.charAt(2)) +
		Number(eanCode.charAt(4)) +
		Number(eanCode.charAt(6)) +
		Number(eanCode.charAt(8)) +
		Number(eanCode.charAt(10));

		// Add two totals together
		total = even + odd;

		// Calculate the checksum
		// Divide total by 10 and store the remainder
		checksum = total % 10;
		// If result is not 0 then take away 10
		if (checksum != 0) {
			checksum = 10 - checksum;
		}

		// Return the result
		return checksum == originalCheck;
	};

    utils.arrayUnique = function(array) {
        var unique = [];
        for (var i = 0; i < array.length; i++) {
            if (unique.indexOf(array[i]) == -1) {
                unique.push(array[i]);
            }
        }

        return unique;
    };

	utils.Base64 = {_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=utils.Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=utils.Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}

	/**
	 * Приготовление и отправка данных в GA, аналитика
	 * @param orderData
	 */
	utils.sendOrderToGA = function(orderData) {

        var	oData = orderData || { orders: [] };

		console.log('[Google Analytics] Start processing orders', oData.orders);

		var productUis = [];
		$.each(oData.orders, function(i,o) {
			var googleOrderTrackingData = {};
			googleOrderTrackingData.transaction = {
				'id': o.numberErp,
				'affiliation': o.is_partner ? 'Партнер' : 'Enter',
				'total': o.paySum,
				'shipping': o.delivery.price,
				'city': o.region.name
			};
			googleOrderTrackingData.products = $.map(o.products, function(p){
				var
					productName = p.name,
					labels = [];

				if (o.isSlot) {
					labels.push('marketplace-slot');
				} else if (o.is_partner) {
					labels.push('marketplace');
				}

				if (p.sender) {
					labels.push(p.sender);
				}

				if (p.sender && p.position) {
					labels.push('RR_' + p.position);
				}

                // Отслеживание покупок в кредит
                if (p.sender2 == 'credit') labels.push('Credit');

				if (labels.length) {
					productName += ' (' + labels.join(')(') + ')';
				}

				/* SITE-4472 Аналитика по АБ-тесту платного самовывоза и рекомендаций из корзины */
				if (ENTER.config.pageConfig.selfDeliveryTest && ENTER.config.pageConfig.selfDeliveryLimit > parseInt(o.paySum, 10) - o.delivery.price) productName = productName + ' (paid pickup)';

				// Аналитика по купленным товарам из рекомендаций
				// Отправляем RR_покупка не только для retailrocket товаров
				if (p.sender) {
					var rrEventLabel = '';
					// Если товар был куплен из рекомендаций с карточки товара маркетплейс
					if (p.sender2 == 'slot') {
						rrEventLabel = '_marketplace-slot';
					} else if (p.sender2 == 'marketplace') {
						rrEventLabel = '_marketplace';
					}

					if (p.from) $body.trigger('trackGoogleEvent',['RR_покупка' + rrEventLabel,'Купил просмотренные', p.position || '']);
					else $body.trigger('trackGoogleEvent',['RR_покупка' + rrEventLabel,'Купил добавленные', p.position || '']);
				}

				if (p.inCompare) {
					(function() {
						var action;
						if (p.isSlot) {
							action = 'marketplace-slot';
						} else if (p.isOnlyFromPartner) {
							action = 'marketplace';
						} else {
							action = 'enter';
						}

						$body.trigger('trackGoogleEvent', ['Compare_покупка', action, p.compareLocation]);
					})();
				}

				productUis.push(p.ui);

				return {
					'id': p.id,
					'name': productName,
					'sku': p.article,
					'category': p.category.length ? (p.category[0].name +  ' - ' + p.category[p.category.length -1].name) : '',
					'price': p.price,
					'quantity': p.quantity
				}
			});

            if (o.isCredit) {
                if ($.grep(o.products, function(product){ return product.sender2 == 'credit' }).length > 0) {
                    $body.trigger('trackGoogleEvent', ['Credit', 'Покупка', 'Карточка товара'])
                } else {
                    $body.trigger('trackGoogleEvent', ['Credit', 'Покупка', 'Оформление заказа'])
                }
            }

			console.log('[Google Analytics] Order', googleOrderTrackingData);
			$body.trigger('trackGoogleTransaction', [googleOrderTrackingData]);
		});

		// SITE-5466
		(function() {
			var
				action = '',
				label = '';

			var reviewProducts = ENTER.utils.analytics.reviews.get(productUis);
            console.log('localstorage', localStorage.getItem('enter.analytics.reviews'));
            console.log('products ui', productUis);
            console.log('reviewProducts', reviewProducts);

			if (reviewProducts.length) {
				for (var i = 0; i < reviewProducts.length; i++) {
					action += (i > 0 ? '_' : '') + (i + 1) + '_All_' + reviewProducts[i].avgScore + '_Top_' + reviewProducts[i].firstPageAvgScore;
					label += (i > 0 ? '_' : '') + (i + 1) + '_' + reviewProducts[i].categoryName;
				}

				$body.trigger('trackGoogleEvent', {
					category: 'Items_review_transaction',
					action: action,
					label: label
				});
			}
		})();
	};

	utils.sendAdd2BasketGaEvent = function(productArticle, productPrice, isOnlyFromPartner, isSlot, senderName) {
		if (productArticle) {
			var location;
			if (ENTER.config.pageConfig.location.indexOf('listing') != -1) {
				location = 'listing';
			} else if (ENTER.config.pageConfig.location.indexOf('product') != -1) {
				location = 'product';
			}

			if (location) {
				var actions = [];

				if (senderName == 'gift') {
					actions.push(location + '-gift');
				}

				if (typeof productPrice != 'undefined' && parseInt(productPrice, 10) < 500) {
					actions.push(location + '-500');
				}

				if (isSlot) {
					actions.push(location + '-marketplace-slot');
				} else if (isOnlyFromPartner) {
					actions.push(location + '-marketplace');
				} else {
					actions.push(location);
				}

				$body.trigger('trackGoogleEvent', ['Add2Basket', '(' + actions.join(')(') + ')', productArticle]);
			}
		}
	};

	utils.getCategoryPath = function() {
		return document.location.pathname.replace(/^\/(?:catalog|product)\/([^\/]*).*$/i, '$1');
	};

	var abstractAnalytics = {
		add: function(storageName, itemData, storageMaxLength) {
			if (!window.localStorage) {
				return;
			}

			try {
				var data = JSON.parse(localStorage.getItem(storageName)) || [];
			} catch(e) {
				data = [];
			}

			// Поскольку при переполнении переменной удаляются первые товары, важно, чтобы повторное добавление
			// товара перемещало его в конец массива
			for (var i = 0; i < data.length; i++) {
				if (data[i][0] == itemData[0]) {
					data.splice(i, 1);
					break;
				}
			}

			data.push(itemData);

			while (JSON.stringify(data).length > storageMaxLength) {
				data.shift();
			}

			localStorage.setItem(storageName, JSON.stringify(data));
		},
		get: function(storageName, itemsIdentifiers) {
			if (!window.localStorage) {
				return;
			}

			try {
				var data = JSON.parse(localStorage.getItem(storageName)) || [];
			} catch(e) {
				data = [];
			}

			var result = [];
			for (var i = 0; i < data.length; i++) {
				if (itemsIdentifiers.indexOf(data[i][0]) != -1) {
					result.push(data[i]);
				}
			}

			return result;
		},
		clean: function(storageName) {
			if (!window.localStorage) {
				return;
			}

			localStorage.removeItem(storageName);
		}
	};

	utils.analytics = {
		// SITE-5466
		reviews: {
			add: function(productUi, avgScore, firstPageAvgScore, categoryName) {
				// Размер хранилища выбран на основе исследования из задачи ADM-457 За эталон одной записи была взята
				// запись "["00203e03-68ce-4a85-8862-8e7cd1542756",8.78,8.78,"Мобильные телефоны"]".
				return abstractAnalytics.add('enter.analytics.reviews', [productUi, avgScore, firstPageAvgScore, categoryName], 3000);
			},
			get: function(productUis) {
				var result = abstractAnalytics.get('enter.analytics.reviews', productUis);
				for (var i = 0; i < result.length; i++) {
					result[i] = {
						avgScore: result[i][1],
						firstPageAvgScore: result[i][2],
						categoryName: result[i][3]
					};
				}

				return result;
			},
			// Должна вызываться, как мы договорились с Захаровым Николаем Викторовичем, лишь при оформлении заказа
			// через обычное оформление заказа (не через одноклик или слоты).
			clean: function() {
				return abstractAnalytics.clean('enter.analytics.reviews');
			}
		},
		// SITE-5062
		productPageSenders: {
			add: function(productUi, sender) {
				// Размер хранилища выбран на основе исследования из задачи ADM-457 За эталон одной записи была взята
				// запись "["00203e03-68ce-4a85-8862-8e7cd1542756",{"name":"retailrocket","position":"ProductAccessories","type":"alsoBought","method":"CrossSellItemToItems","from":"cart_rec"}],".
				if (sender) {
					return abstractAnalytics.add('enter.analytics.productPageSenders', [productUi, sender], 2500);
				}
			},
			get: function($button) {
				var sender = $button.data('sender') || {};

				if ($('body').data('template') == 'product_card' && ($button.data('location') == 'product-card' || $button.data('location') == 'userbar')) {
					var
						product = $('#jsProductCard').data('value') || {},
						productPageSender = abstractAnalytics.get('enter.analytics.productPageSenders', [product.ui])
					;

					productPageSender = (productPageSender[0] ? productPageSender[0][1] : null) || product.oldProductPageSender; // TODO: удалить oldProductPageSender через неделю после релиза SITE-5543

					if (productPageSender && typeof productPageSender == 'object') {
						function isSenderPresent(sender) {
							if (sender && typeof sender == 'object') {
								for (var key in sender) {
									if (sender.hasOwnProperty(key) && key != 'from' && sender[key]) {
										return true;
									}
								}
							}

							return false;
						}

						if (!isSenderPresent(sender)) {
							sender = productPageSender;
						}
					}
				}

				return sender;
			},
			// Должна вызываться, как мы договорились с Захаровым Николаем Викторовичем, лишь при оформлении заказа
			// через обычное оформление заказа (не через одноклик или слоты).
			clean: function() {
				return abstractAnalytics.clean('enter.analytics.productPageSenders');
			}
		},
		// SITE-5072
		// Используется для сохранения источников переходов, осуществлённых из рекомендаций с карточек товаров
		// маркетплейс. Используется отдельное хранилище (а не хранилище productPageSenders), т.к. данные источники
		// не должны перезатирать источники из productPageSenders.
		productPageSenders2: {
			add: function(productUi, sender2) {
				// Размер хранилища выбран на основе исследования из задачи ADM-457. За эталон одной записи была взята
				// запись "["00203e03-68ce-4a85-8862-8e7cd1542756","marketplace"],".
				if (sender2) {
					return abstractAnalytics.add('enter.analytics.productPageSenders2', [productUi, sender2], 1000);
				}
			},
			get: function($button) {
				var sender2 = $button.data('sender2') || '';

				if ($('body').data('template') == 'product_card' && ($button.data('location') == 'product-card' || $button.data('location') == 'userbar')) {
					var
						product = $('#jsProductCard').data('value') || {},
						productPageSender2 = abstractAnalytics.get('enter.analytics.productPageSenders2', [product.ui])
					;

					productPageSender2 = (productPageSender2[0] ? productPageSender2[0][1] : null) || product.oldProductPageSender2; // TODO: удалить oldProductPageSender2 через неделю после релиза SITE-5543

					if (productPageSender2 && !sender2) {
						sender2 = productPageSender2;
					}
				}

				return sender2;
			},
			// Должна вызываться, как мы договорились с Захаровым Николаем Викторовичем, лишь при оформлении заказа
			// через обычное оформление заказа (не через одноклик или слоты).
			clean: function() {
				return abstractAnalytics.clean('enter.analytics.productPageSenders2');
			}
		}
	};

}(window.ENTER));