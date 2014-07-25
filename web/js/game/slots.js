/*!
 * jQuery Transit - CSS3 transitions and transformations
 * (c) 2011-2014 Rico Sta. Cruz
 * MIT Licensed.
 *
 * http://ricostacruz.com/jquery.transit
 * http://github.com/rstacruz/jquery.transit
 */

/* jshint expr: true */

;(function (root, factory) {

    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        module.exports = factory(require('jquery'));
    } else {
        factory(root.jQuery);
    }

}(this, function($) {

    $.transit = {
        version: "0.9.11",

        // Map of $.css() keys to values for 'transitionProperty'.
        // See https://developer.mozilla.org/en/CSS/CSS_transitions#Properties_that_can_be_animated
        propertyMap: {
            marginLeft    : 'margin',
            marginRight   : 'margin',
            marginBottom  : 'margin',
            marginTop     : 'margin',
            paddingLeft   : 'padding',
            paddingRight  : 'padding',
            paddingBottom : 'padding',
            paddingTop    : 'padding'
        },

        // Will simply transition "instantly" if false
        enabled: true,

        // Set this to false if you don't want to use the transition end property.
        useTransitionEnd: false
    };

    var div = document.createElement('div');
    var support = {};

    // Helper function to get the proper vendor property name.
    // (`transition` => `WebkitTransition`)
    function getVendorPropertyName(prop) {
        // Handle unprefixed versions (FF16+, for example)
        if (prop in div.style) return prop;

        var prefixes = ['Moz', 'Webkit', 'O', 'ms'];
        var prop_ = prop.charAt(0).toUpperCase() + prop.substr(1);

        for (var i=0; i<prefixes.length; ++i) {
            var vendorProp = prefixes[i] + prop_;
            if (vendorProp in div.style) { return vendorProp; }
        }
    }

    // Helper function to check if transform3D is supported.
    // Should return true for Webkits and Firefox 10+.
    function checkTransform3dSupport() {
        div.style[support.transform] = '';
        div.style[support.transform] = 'rotateY(90deg)';
        return div.style[support.transform] !== '';
    }

    var isChrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;

    // Check for the browser's transitions support.
    support.transition      = getVendorPropertyName('transition');
    support.transitionDelay = getVendorPropertyName('transitionDelay');
    support.transform       = getVendorPropertyName('transform');
    support.transformOrigin = getVendorPropertyName('transformOrigin');
    support.filter          = getVendorPropertyName('Filter');
    support.transform3d     = checkTransform3dSupport();

    var eventNames = {
        'transition':       'transitionend',
        'MozTransition':    'transitionend',
        'OTransition':      'oTransitionEnd',
        'WebkitTransition': 'webkitTransitionEnd',
        'msTransition':     'MSTransitionEnd'
    };

    // Detect the 'transitionend' event needed.
    var transitionEnd = support.transitionEnd = eventNames[support.transition] || null;

    // Populate jQuery's `$.support` with the vendor prefixes we know.
    // As per [jQuery's cssHooks documentation](http://api.jquery.com/jQuery.cssHooks/),
    // we set $.support.transition to a string of the actual property name used.
    for (var key in support) {
        if (support.hasOwnProperty(key) && typeof $.support[key] === 'undefined') {
            $.support[key] = support[key];
        }
    }

    // Avoid memory leak in IE.
    div = null;

    // ## $.cssEase
    // List of easing aliases that you can use with `$.fn.transition`.
    $.cssEase = {
        '_default':       'ease',
        'in':             'ease-in',
        'out':            'ease-out',
        'in-out':         'ease-in-out',
        'snap':           'cubic-bezier(0,1,.5,1)',
        // Penner equations
        'easeInCubic':    'cubic-bezier(.550,.055,.675,.190)',
        'easeOutCubic':   'cubic-bezier(.215,.61,.355,1)',
        'easeInOutCubic': 'cubic-bezier(.645,.045,.355,1)',
        'easeInCirc':     'cubic-bezier(.6,.04,.98,.335)',
        'easeOutCirc':    'cubic-bezier(.075,.82,.165,1)',
        'easeInOutCirc':  'cubic-bezier(.785,.135,.15,.86)',
        'easeInExpo':     'cubic-bezier(.95,.05,.795,.035)',
        'easeOutExpo':    'cubic-bezier(.19,1,.22,1)',
        'easeInOutExpo':  'cubic-bezier(1,0,0,1)',
        'easeInQuad':     'cubic-bezier(.55,.085,.68,.53)',
        'easeOutQuad':    'cubic-bezier(.25,.46,.45,.94)',
        'easeInOutQuad':  'cubic-bezier(.455,.03,.515,.955)',
        'easeInQuart':    'cubic-bezier(.895,.03,.685,.22)',
        'easeOutQuart':   'cubic-bezier(.165,.84,.44,1)',
        'easeInOutQuart': 'cubic-bezier(.77,0,.175,1)',
        'easeInQuint':    'cubic-bezier(.755,.05,.855,.06)',
        'easeOutQuint':   'cubic-bezier(.23,1,.32,1)',
        'easeInOutQuint': 'cubic-bezier(.86,0,.07,1)',
        'easeInSine':     'cubic-bezier(.47,0,.745,.715)',
        'easeOutSine':    'cubic-bezier(.39,.575,.565,1)',
        'easeInOutSine':  'cubic-bezier(.445,.05,.55,.95)',
        'easeInBack':     'cubic-bezier(.6,-.28,.735,.045)',
        'easeOutBack':    'cubic-bezier(.175, .885,.32,1.275)',
        'easeInOutBack':  'cubic-bezier(.68,-.55,.265,1.55)'
    };

    // ## 'transform' CSS hook
    // Allows you to use the `transform` property in CSS.
    //
    //     $("#hello").css({ transform: "rotate(90deg)" });
    //
    //     $("#hello").css('transform');
    //     //=> { rotate: '90deg' }
    //
    $.cssHooks['transit:transform'] = {
        // The getter returns a `Transform` object.
        get: function(elem) {
            return $(elem).data('transform') || new Transform();
        },

        // The setter accepts a `Transform` object or a string.
        set: function(elem, v) {
            var value = v;

            if (!(value instanceof Transform)) {
                value = new Transform(value);
            }

            // We've seen the 3D version of Scale() not work in Chrome when the
            // element being scaled extends outside of the viewport.  Thus, we're
            // forcing Chrome to not use the 3d transforms as well.  Not sure if
            // translate is affectede, but not risking it.  Detection code from
            // http://davidwalsh.name/detecting-google-chrome-javascript
            if (support.transform === 'WebkitTransform' && !isChrome) {
                elem.style[support.transform] = value.toString(true);
            } else {
                elem.style[support.transform] = value.toString();
            }

            $(elem).data('transform', value);
        }
    };

    // Add a CSS hook for `.css({ transform: '...' })`.
    // In jQuery 1.8+, this will intentionally override the default `transform`
    // CSS hook so it'll play well with Transit. (see issue #62)
    $.cssHooks.transform = {
        set: $.cssHooks['transit:transform'].set
    };

    // ## 'filter' CSS hook
    // Allows you to use the `filter` property in CSS.
    //
    //     $("#hello").css({ filter: 'blur(10px)' });
    //
    $.cssHooks.filter = {
        get: function(elem) {
            return elem.style[support.filter];
        },
        set: function(elem, value) {
            elem.style[support.filter] = value;
        }
    };

    // jQuery 1.8+ supports prefix-free transitions, so these polyfills will not
    // be necessary.
    if ($.fn.jquery < "1.8") {
        // ## 'transformOrigin' CSS hook
        // Allows the use for `transformOrigin` to define where scaling and rotation
        // is pivoted.
        //
        //     $("#hello").css({ transformOrigin: '0 0' });
        //
        $.cssHooks.transformOrigin = {
            get: function(elem) {
                return elem.style[support.transformOrigin];
            },
            set: function(elem, value) {
                elem.style[support.transformOrigin] = value;
            }
        };

        // ## 'transition' CSS hook
        // Allows you to use the `transition` property in CSS.
        //
        //     $("#hello").css({ transition: 'all 0 ease 0' });
        //
        $.cssHooks.transition = {
            get: function(elem) {
                return elem.style[support.transition];
            },
            set: function(elem, value) {
                elem.style[support.transition] = value;
            }
        };
    }

    // ## Other CSS hooks
    // Allows you to rotate, scale and translate.
    registerCssHook('scale');
    registerCssHook('scaleX');
    registerCssHook('scaleY');
    registerCssHook('translate');
    registerCssHook('rotate');
    registerCssHook('rotateX');
    registerCssHook('rotateY');
    registerCssHook('rotate3d');
    registerCssHook('perspective');
    registerCssHook('skewX');
    registerCssHook('skewY');
    registerCssHook('x', true);
    registerCssHook('y', true);

    // ## Transform class
    // This is the main class of a transformation property that powers
    // `$.fn.css({ transform: '...' })`.
    //
    // This is, in essence, a dictionary object with key/values as `-transform`
    // properties.
    //
    //     var t = new Transform("rotate(90) scale(4)");
    //
    //     t.rotate             //=> "90deg"
    //     t.scale              //=> "4,4"
    //
    // Setters are accounted for.
    //
    //     t.set('rotate', 4)
    //     t.rotate             //=> "4deg"
    //
    // Convert it to a CSS string using the `toString()` and `toString(true)` (for WebKit)
    // functions.
    //
    //     t.toString()         //=> "rotate(90deg) scale(4,4)"
    //     t.toString(true)     //=> "rotate(90deg) scale3d(4,4,0)" (WebKit version)
    //
    function Transform(str) {
        if (typeof str === 'string') { this.parse(str); }
        return this;
    }

    Transform.prototype = {
        // ### setFromString()
        // Sets a property from a string.
        //
        //     t.setFromString('scale', '2,4');
        //     // Same as set('scale', '2', '4');
        //
        setFromString: function(prop, val) {
            var args =
                (typeof val === 'string')  ? val.split(',') :
                    (val.constructor === Array) ? val :
                        [ val ];

            args.unshift(prop);

            Transform.prototype.set.apply(this, args);
        },

        // ### set()
        // Sets a property.
        //
        //     t.set('scale', 2, 4);
        //
        set: function(prop) {
            var args = Array.prototype.slice.apply(arguments, [1]);
            if (this.setter[prop]) {
                this.setter[prop].apply(this, args);
            } else {
                this[prop] = args.join(',');
            }
        },

        get: function(prop) {
            if (this.getter[prop]) {
                return this.getter[prop].apply(this);
            } else {
                return this[prop] || 0;
            }
        },

        setter: {
            // ### rotate
            //
            //     .css({ rotate: 30 })
            //     .css({ rotate: "30" })
            //     .css({ rotate: "30deg" })
            //     .css({ rotate: "30deg" })
            //
            rotate: function(theta) {
                this.rotate = unit(theta, 'deg');
            },

            rotateX: function(theta) {
                this.rotateX = unit(theta, 'deg');
            },

            rotateY: function(theta) {
                this.rotateY = unit(theta, 'deg');
            },

            // ### scale
            //
            //     .css({ scale: 9 })      //=> "scale(9,9)"
            //     .css({ scale: '3,2' })  //=> "scale(3,2)"
            //
            scale: function(x, y) {
                if (y === undefined) { y = x; }
                this.scale = x + "," + y;
            },

            // ### skewX + skewY
            skewX: function(x) {
                this.skewX = unit(x, 'deg');
            },

            skewY: function(y) {
                this.skewY = unit(y, 'deg');
            },

            // ### perspectvie
            perspective: function(dist) {
                this.perspective = unit(dist, 'px');
            },

            // ### x / y
            // Translations. Notice how this keeps the other value.
            //
            //     .css({ x: 4 })       //=> "translate(4px, 0)"
            //     .css({ y: 10 })      //=> "translate(4px, 10px)"
            //
            x: function(x) {
                this.set('translate', x, null);
            },

            y: function(y) {
                this.set('translate', null, y);
            },

            // ### translate
            // Notice how this keeps the other value.
            //
            //     .css({ translate: '2, 5' })    //=> "translate(2px, 5px)"
            //
            translate: function(x, y) {
                if (this._translateX === undefined) { this._translateX = 0; }
                if (this._translateY === undefined) { this._translateY = 0; }

                if (x !== null && x !== undefined) { this._translateX = unit(x, 'px'); }
                if (y !== null && y !== undefined) { this._translateY = unit(y, 'px'); }

                this.translate = this._translateX + "," + this._translateY;
            }
        },

        getter: {
            x: function() {
                return this._translateX || 0;
            },

            y: function() {
                return this._translateY || 0;
            },

            scale: function() {
                var s = (this.scale || "1,1").split(',');
                if (s[0]) { s[0] = parseFloat(s[0]); }
                if (s[1]) { s[1] = parseFloat(s[1]); }

                // "2.5,2.5" => 2.5
                // "2.5,1" => [2.5,1]
                return (s[0] === s[1]) ? s[0] : s;
            },

            rotate3d: function() {
                var s = (this.rotate3d || "0,0,0,0deg").split(',');
                for (var i=0; i<=3; ++i) {
                    if (s[i]) { s[i] = parseFloat(s[i]); }
                }
                if (s[3]) { s[3] = unit(s[3], 'deg'); }

                return s;
            }
        },

        // ### parse()
        // Parses from a string. Called on constructor.
        parse: function(str) {
            var self = this;
            str.replace(/([a-zA-Z0-9]+)\((.*?)\)/g, function(x, prop, val) {
                self.setFromString(prop, val);
            });
        },

        // ### toString()
        // Converts to a `transition` CSS property string. If `use3d` is given,
        // it converts to a `-webkit-transition` CSS property string instead.
        toString: function(use3d) {
            var re = [];

            for (var i in this) {
                if (this.hasOwnProperty(i)) {
                    // Don't use 3D transformations if the browser can't support it.
                    if ((!support.transform3d) && (
                        (i === 'rotateX') ||
                            (i === 'rotateY') ||
                            (i === 'perspective') ||
                            (i === 'transformOrigin'))) { continue; }

                    if (i[0] !== '_') {
                        if (use3d && (i === 'scale')) {
                            re.push(i + "3d(" + this[i] + ",1)");
                        } else if (use3d && (i === 'translate')) {
                            re.push(i + "3d(" + this[i] + ",0)");
                        } else {
                            re.push(i + "(" + this[i] + ")");
                        }
                    }
                }
            }

            return re.join(" ");
        }
    };

    function callOrQueue(self, queue, fn) {
        if (queue === true) {
            self.queue(fn);
        } else if (queue) {
            self.queue(queue, fn);
        } else {
            self.each(function () {
                fn.call(this);
            });
        }
    }

    // ### getProperties(dict)
    // Returns properties (for `transition-property`) for dictionary `props`. The
    // value of `props` is what you would expect in `$.css(...)`.
    function getProperties(props) {
        var re = [];

        $.each(props, function(key) {
            key = $.camelCase(key); // Convert "text-align" => "textAlign"
            key = $.transit.propertyMap[key] || $.cssProps[key] || key;
            key = uncamel(key); // Convert back to dasherized

            // Get vendor specify propertie
            if (support[key])
                key = uncamel(support[key]);

            if ($.inArray(key, re) === -1) { re.push(key); }
        });

        return re;
    }

    // ### getTransition()
    // Returns the transition string to be used for the `transition` CSS property.
    //
    // Example:
    //
    //     getTransition({ opacity: 1, rotate: 30 }, 500, 'ease');
    //     //=> 'opacity 500ms ease, -webkit-transform 500ms ease'
    //
    function getTransition(properties, duration, easing, delay) {
        // Get the CSS properties needed.
        var props = getProperties(properties);

        // Account for aliases (`in` => `ease-in`).
        if ($.cssEase[easing]) { easing = $.cssEase[easing]; }

        // Build the duration/easing/delay attributes for it.
        var attribs = '' + toMS(duration) + ' ' + easing;
        if (parseInt(delay, 10) > 0) { attribs += ' ' + toMS(delay); }

        // For more properties, add them this way:
        // "margin 200ms ease, padding 200ms ease, ..."
        var transitions = [];
        $.each(props, function(i, name) {
            transitions.push(name + ' ' + attribs);
        });

        return transitions.join(', ');
    }

    // ## $.fn.transition
    // Works like $.fn.animate(), but uses CSS transitions.
    //
    //     $("...").transition({ opacity: 0.1, scale: 0.3 });
    //
    //     // Specific duration
    //     $("...").transition({ opacity: 0.1, scale: 0.3 }, 500);
    //
    //     // With duration and easing
    //     $("...").transition({ opacity: 0.1, scale: 0.3 }, 500, 'in');
    //
    //     // With callback
    //     $("...").transition({ opacity: 0.1, scale: 0.3 }, function() { ... });
    //
    //     // With everything
    //     $("...").transition({ opacity: 0.1, scale: 0.3 }, 500, 'in', function() { ... });
    //
    //     // Alternate syntax
    //     $("...").transition({
    //       opacity: 0.1,
    //       duration: 200,
    //       delay: 40,
    //       easing: 'in',
    //       complete: function() { /* ... */ }
    //      });
    //
    $.fn.transition = $.fn.transit = function(properties, duration, easing, callback) {
        var self  = this;
        var delay = 0;
        var queue = true;

        var theseProperties = jQuery.extend(true, {}, properties);

        // Account for `.transition(properties, callback)`.
        if (typeof duration === 'function') {
            callback = duration;
            duration = undefined;
        }

        // Account for `.transition(properties, options)`.
        if (typeof duration === 'object') {
            easing = duration.easing;
            delay = duration.delay || 0;
            queue = typeof duration.queue === "undefined" ? true : duration.queue;
            callback = duration.complete;
            duration = duration.duration;
        }

        // Account for `.transition(properties, duration, callback)`.
        if (typeof easing === 'function') {
            callback = easing;
            easing = undefined;
        }

        // Alternate syntax.
        if (typeof theseProperties.easing !== 'undefined') {
            easing = theseProperties.easing;
            delete theseProperties.easing;
        }

        if (typeof theseProperties.duration !== 'undefined') {
            duration = theseProperties.duration;
            delete theseProperties.duration;
        }

        if (typeof theseProperties.complete !== 'undefined') {
            callback = theseProperties.complete;
            delete theseProperties.complete;
        }

        if (typeof theseProperties.queue !== 'undefined') {
            queue = theseProperties.queue;
            delete theseProperties.queue;
        }

        if (typeof theseProperties.delay !== 'undefined') {
            delay = theseProperties.delay;
            delete theseProperties.delay;
        }

        // Set defaults. (`400` duration, `ease` easing)
        if (typeof duration === 'undefined') { duration = $.fx.speeds._default; }
        if (typeof easing === 'undefined')   { easing = $.cssEase._default; }

        duration = toMS(duration);

        // Build the `transition` property.
        var transitionValue = getTransition(theseProperties, duration, easing, delay);

        // Compute delay until callback.
        // If this becomes 0, don't bother setting the transition property.
        var work = $.transit.enabled && support.transition;
        var i = work ? (parseInt(duration, 10) + parseInt(delay, 10)) : 0;

        // If there's nothing to do...
        if (i === 0) {
            var fn = function(next) {
                self.css(theseProperties);
                if (callback) { callback.apply(self); }
                if (next) { next(); }
            };

            callOrQueue(self, queue, fn);
            return self;
        }

        // Save the old transitions of each element so we can restore it later.
        var oldTransitions = {};

        var run = function(nextCall) {
            var bound = false;

            // Prepare the callback.
            var cb = function() {
                if (bound) { self.unbind(transitionEnd, cb); }

                if (i > 0) {
                    self.each(function() {
                        this.style[support.transition] = (oldTransitions[this] || null);
                    });
                }

                if (typeof callback === 'function') { callback.apply(self); }
                if (typeof nextCall === 'function') { nextCall(); }
            };

            if ((i > 0) && (transitionEnd) && ($.transit.useTransitionEnd)) {
                // Use the 'transitionend' event if it's available.
                bound = true;
                self.bind(transitionEnd, cb);
            } else {
                // Fallback to timers if the 'transitionend' event isn't supported.
                window.setTimeout(cb, i);
            }

            // Apply transitions.
            self.each(function() {
                if (i > 0) {
                    this.style[support.transition] = transitionValue;
                }
                $(this).css(properties);
            });
        };

        // Defer running. This allows the browser to paint any pending CSS it hasn't
        // painted yet before doing the transitions.
        var deferredRun = function(next) {
            this.offsetWidth; // force a repaint
            run(next);
        };

        // Use jQuery's fx queue.
        callOrQueue(self, queue, deferredRun);

        // Chainability.
        return this;
    };

    function registerCssHook(prop, isPixels) {
        // For certain properties, the 'px' should not be implied.
        if (!isPixels) { $.cssNumber[prop] = true; }

        $.transit.propertyMap[prop] = support.transform;

        $.cssHooks[prop] = {
            get: function(elem) {
                var t = $(elem).css('transit:transform');
                return t.get(prop);
            },

            set: function(elem, value) {
                var t = $(elem).css('transit:transform');
                t.setFromString(prop, value);

                $(elem).css({ 'transit:transform': t });
            }
        };

    }

    // ### uncamel(str)
    // Converts a camelcase string to a dasherized string.
    // (`marginLeft` => `margin-left`)
    function uncamel(str) {
        return str.replace(/([A-Z])/g, function(letter) { return '-' + letter.toLowerCase(); });
    }

    // ### unit(number, unit)
    // Ensures that number `number` has a unit. If no unit is found, assume the
    // default is `unit`.
    //
    //     unit(2, 'px')          //=> "2px"
    //     unit("30deg", 'rad')   //=> "30deg"
    //
    function unit(i, units) {
        if ((typeof i === "string") && (!i.match(/^[\-0-9\.]+$/))) {
            return i;
        } else {
            return "" + i + units;
        }
    }

    // ### toMS(duration)
    // Converts given `duration` to a millisecond string.
    //
    // toMS('fast') => $.fx.speeds[i] => "200ms"
    // toMS('normal') //=> $.fx.speeds._default => "400ms"
    // toMS(10) //=> '10ms'
    // toMS('100ms') //=> '100ms'
    //
    function toMS(duration) {
        var i = duration;

        // Allow string durations like 'fast' and 'slow', without overriding numeric values.
        if (typeof i === 'string' && (!i.match(/^[\-0-9\.]+/))) { i = $.fx.speeds[i] || $.fx.speeds._default; }

        return unit(i, 'ms');
    }

    // Export some functions for testable-ness.
    $.transit.getTransitionValue = getTransition;

    return $;
}));


$.fn.slots = function (slot_config, animations_config) {
    if (!slot_config) {
        slot_config = {
            labels: {
                messageBox: {
                    demo: ["!!! играйте на скидку !!!", "Это просто текст", "Внезапный текст"],
                    win: ["!!! выигрышь !!!", "ДЖЕКПОТ!!!!!", "Мама мыла раму"],
                    nowin: ["ничего не вышло {tryRemain}", "то ли еще будет {tryRemain}", "попробуйте еще {tryRemain}", "осталось {tryRemain} попыток"],
                    spinning: ["трах тиби дох", "абра кадабра", "кручу верчу"]

                },
                notAvailable: {
                    message: "Автомат временно не работает. Приходите завтра",
                    optionPrize: "Утешительный приз",
                    optionRemind: "Напомнить мне"
                }
            },
            handlers: {
                userUnauthorized: function (self, message) {
                    self.stillInGameState();
                    console.log('userUnauthorized: ' + message);
                }
            },
            api_url: {
                init: location.origin + "/init",
                play: "/play",
                img_led_off: "/img/slot_led_off.png"

            },
            userAutoPlay: false
        };
    }
    String.prototype.format = function (args) {
        if (!args) return this.toString();
        var str = this;

        return str.replace(String.prototype.format.regex, function (item) {
            var intVal = item.substring(1, item.length - 1);
            return args[intVal] ? args[intVal] : "";
        });
    };
    String.prototype.format.regex = new RegExp("{-?[0-9A-z]*}", "g");

    window.requestAnimFrame = (function () {
        return  window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame
    })();
    var $el = this;
    $el.slotMachine = {
        canvas: null,
        context: null,
        maxX: null,
        maxY: null,
        lamps: [],
        lamps2: [],
        isStopped: false,
        slot_config: slot_config,
        options: {type: "random", speed: 3, n: 0, m: 0, color: "58,209,255"},
        reels: $el.find('#reelsWrapper .chips'),
        $notAvailableMessageText: $el.find('#notAvailable .message'),
        $notAvailablePrizeText: $el.find('#notAvailable .option.prize'),
        $notAvailableRemindText: $el.find('#notAvailable .option.remind'),
        $slotMachine: $el.find('#slotsMachine'),
        $winChipContainerChip: $el.find('#winContainer .chip'),
        $winContainer: $el.find('#winContainer'),
        $lb_overlay: $el.find('.lbOverlay'),
        initialize: function () { //инитим автомат
            var self = this;
            self.slot_config = slot_config;
            self.config = animations_config ? animations_config : {};//сохраняем конфиги слот машины
            self.renderMarkup();
            self.$winChipContainerChip.click(function () {
                $el.find('.winChip').removeClass('winChip');
                $el.find('.lbOverlay').hide();
                $el.find('#winContainer').hide().removeClass('winner bigwinner');
                self.setLedPanelOptions('default');
                self.messageBox.stopAnimation();
                self.messageBox.setRandomText("demo");
                self.messageBox.animateText("defaultAnimation");
            });
            setTimeout(function () {
                $el.find('#slotsBack').css('top', $('#slotsWrapper').offset().top + 'px').show();
            }, 1000);


            if (!self.config.isAvailable) {
                self.notAvailableState(); // если автомат недоступен - стартуем режим недоступен
            } else {
                self.initLedPanel();//инициализируем лед панель
                self.messageBox.init();//инитим текстовую панель
            }

            self.send({
                type: "GET",
                url: self.slot_config.api_url.init,
                data: {},
                cb: function (response) {
                    if (response.success) {
                        self.game.init(response.reels);//иначе отрисовываем рельсы
                        self.game.bindAll();//биндимся на клики старт и стоп
                        self.demoMode = !response.user;//залогинен ли юзер

                    } else {
                        self.notAvailableState(response.error);
                    }
                },
                err: function () {
                    //fake response

                    self.notAvailableState();


                }
            });
        },
        send: function (data) { //общий для всего метод отправки запроса аяксом
            var self = this;
            data.data.t = new Date().getTime();
            $.ajax({
                type: data.type,
                url: data.url,
                data: data.data,
                success: function (r) {
                    if (!r.success || r.error) {
                        self.slot_config.handlers[r.error.code] && self.slot_config.handlers[r.error.code](self, r.error);
                        return;
                    }
                    data.cb && data.cb(r);
                },
                error: function (r) {
                    //   console.log('oooopsss', r);

                    data.err && data.err();

                }
            });
        },

        notAvailableState: function (message) {//метод для вызова режима "временно недоступен"
            var self = this;
            self.ledAnimations.animationHandler.stopAnimation();//останавливаем анимацию лед панели
            clearTimeout(self.spinningTimeout);
            self.$notAvailableMessageText.text(message ? message : self.slot_config.labels.notAvailable.message);//в  пишем сообщение что автомат недоступен
            self.$notAvailablePrizeText.text(self.slot_config.labels.notAvailable.optionPrize);//пишем тексты в ссылки
            self.$notAvailableRemindText.text(self.slot_config.labels.notAvailable.optionRemind);
            self.$slotMachine.addClass('notavailable');//вешаем класс недоступен на автомат
            self.messageBox.stopAnimation();//останавлеваем анимацию текстовой панели
            self.stopReels();
        },
        stillInGameState: function () {//метод для вызова режима "временно недоступен"
            var self = this;
            clearTimeout(self.spinningTimeout);
            self.game.inGame = false;
            self.game.buttonsRow.removeClass('stopplay').addClass('toplay');
            self.setLedPanelOptions('default');
            self.reels.addClass('spinning');
            self.messageBox.stopAnimation();
            self.messageBox.setRandomText("demo");
            self.messageBox.animateText("defaultAnimation");
            self.ledAnimations.animationHandler.startAnimation();
            self.$slotMachine.removeClass('notavailable');//вешаем класс недоступен на автомат

        },
        renderMarkup: function () {
            //append popup overlay
            $el.append('<div class="lbOverlay" style="display:none;height: 100%; position: fixed; width: 100%; top: 0px; left: 0px; z-index: 1001; opacity: 0.4; background: black;"></div>');
            //append slots background
            $el.append('<div id="slotsBack"></div>');
            //append slots body
            $el.append('<div id="slotsWrapper"> <div id="slotsMachine"> <div id="slotsGame"> <div id="slotsHeader"></div> <div id="notAvailable"> <div class="message"></div> <a href="#" class="option prize"></a> <a href="#" class="option remind"></a> <div class="plast br"></div> <div class="plast bl"></div> <div class="plast tr"></div> <div class="plast tl"></div> </div> <div id="slotsMessageBox"> <div id="slotsMessageReel"> <div id="messages" class="messageRow"></div> <div id="pixelOverlay"></div> </div> </div> <div id="reelsWrapper"> <div id="reel1" class="reel"> <div class="chips "> </div> <div class="slot-shadow-overlay overlay-top"></div> <div class="slot-shadow-overlay overlay-bottom"></div> </div> <div id="reel2" class="reel"> <div class="chips"> </div> <div class="slot-shadow-overlay overlay-top"></div> <div class="slot-shadow-overlay overlay-bottom"></div> </div> <div id="reel3" class="reel"> <div class="chips"> </div> <div class="slot-shadow-overlay overlay-top"></div> <div class="slot-shadow-overlay overlay-bottom"></div> </div> </div> <div id="winContainer" class=""> <div class="tile"> <div class="rulesText"> Фишка со скидкой <strong class="dicount">10 %</strong> на <strong class="category"><a target="_blank" style="text-decoration: underline;" href="/catalog/children">Детские товары</a></strong><br> Минимальная сумма заказа 499 руб<br> Действует c 28.05.2014 по 30.06.2014 </div> </div> <div class="confetil"></div> <div class="blue_shine"></div> <div class="shine"></div> <div class="chip "> <div class="border lime"> <div class="cuponImg__inner"> <div class="cuponIco"> <img src="http://content.enter.ru/wp-content/uploads/2014/03/kids.png"> </div> <div class="cuponDesc">Детские товары</div> <div class="cuponPrice">15% </div> </div> </div> </div> <div class=""></div> </div> <canvas height="388" width="588" id="canvas"></canvas> <div id="buttonsRow" class="toplay"> <div class="play btn_wrapper"> <div class="step_dots"> <div class="reel_dot first on"></div> <div class="reel_dot second on"></div> <div class="reel_dot third on"></div> </div> <div class="button">Играть</div> </div> <div class="stop btn_wrapper"> <div class="step_dots"> <div class="reel_dot first"></div> <div class="reel_dot second"></div> <div class="reel_dot third"></div> </div> <div class="button">Стоп</div> </div> </div> </div> </div> <div id="slotsBottomLine"></div> </div>');
            this.canvas = document.getElementById('canvas');
            this.context = canvas.getContext('2d');
            this.reels = $el.find('#reelsWrapper .chips');
            this.$notAvailableMessageText = $el.find('#notAvailable .message');
            this.$notAvailablePrizeText = $el.find('#notAvailable .option.prize');
            this.$notAvailableRemindText = $el.find('#notAvailable .option.remind');
            this.$slotMachine = $el.find('#slotsMachine');
            this.$winChipContainerChip = $el.find('#winContainer .chip');
            this.$winContainer = $el.find('#winContainer');
            this.$lb_overlay = $el.find('.lbOverlay');
        },
        game: {//логика слот машины1
            reelStoped: false,
            inGame: false,
            init: function (reels) {//рисуем рельсы.
                var self = this;
                for (var i = 0; i < reels.length; i++) {
                    $el.find('#reel' + (i + 1) + ' .chips').html('');
                    for (var ch = 0; ch < reels[i].length; ch++) {
                        var chip = reels[i][ch];
                        $el.find('#reel' + (i + 1) + ' .chips').append('<div class="chip"> <span class="border " style="background-image: url(' + chip.background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; "> <span class="cuponImg__inner"> <span class="cuponIco"> <img src="' + chip.icon + '"> </span> <span class="cuponDesc">' + chip.label + '</span> <span class="cuponPrice">' + chip.value + '</span> </span> </span> </div>');
                    }
                }
                $el.slotMachine.reels.addClass('spinning');
                if ($el.slotMachine.config.userAutoPlay) {
                    $el.slotMachine.spinningTimeout = setTimeout(function () {
                        self.start();
                    }, 30000);
                }
            },
            bindAll: function () {//биндимся на клики по кнопкам
                var game = this;
                game.buttonsRow = $el.find('#buttonsRow');
                game.play_button = game.buttonsRow.find('.play .button');
                game.stop_button = game.buttonsRow.find('.stop .button');


                game.play_button.click(function () {
                    if (game.inGame) {
                        return;
                    }
                    game.start();//иначе стартуем игру
                    game.buttonsRow.removeClass('toplay').addClass('stopplay');
                });
                game.stop_button.click(function () {//останавливаем игру
                    game.stop(game);
                    //game.buttonsRow.removeClass('stopplay').addClass('toplay');
                });
            },
            playResultHandler: function (response) {
                var self = $el.slotMachine;
                var game = this;
                if (!response.success && !response.result) {
                    self.slot_config.handlers[r.error.code] ? self.slot_config.handlers[r.error.code](r.error) : self.notAvailableState(r.error);
                    return;
                }
                if (response.spin && !response.spin.isAvailable) {//если автомат уже недоступен режим недоступен
                    self.notAvailableState();
                    return;
                }
                game.result = response.result;//результат спина
                game.user = response.user;
                var ch1 = $el.find(self.reels[0]).find('.chip:nth-child(2)');
                var ch2 = $el.find(self.reels[1]).find('.chip:nth-child(2)');
                var ch3 = $el.find(self.reels[2]).find('.chip:nth-child(2)');
                var winCh = $el.find('#winContainer .chip');

                var reels = response.result.line;

                reels[0] && ch1.find('.border').attr('style', 'background-image: url(' + reels[0].background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; ');
                reels[1] && ch2.find('.border').attr('style', 'background-image: url(' + reels[1].background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; ');
                reels[2] && ch3.find('.border').attr('style', 'background-image: url(' + reels[2].background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; ');
                reels[1] && winCh.find('.border').attr('style', 'background-image: url(' + reels[1].background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; ');

                reels[0] && ch1.find('.cuponPrice').text(reels[0].value);
                reels[1] && ch2.find('.cuponPrice').text(reels[1].value);
                reels[2] && ch3.find('.cuponPrice').text(reels[2].value);
                reels[1] && winCh.find('.cuponPrice').text(reels[1].value);

                reels[0] && ch1.find('.cuponIco img').attr('src', reels[0].icon);
                reels[1] && ch2.find('.cuponIco img').attr('src', reels[1].icon);
                reels[2] && ch3.find('.cuponIco img').attr('src', reels[2].icon);
                reels[1] && winCh.find('.cuponIco img').attr('src', reels[1].icon);


                reels[0] && ch1.find('.cuponDesc').text(reels[0].label);
                reels[1] && ch2.find('.cuponDesc').text(reels[1].label);
                reels[2] && ch3.find('.cuponDesc').text(reels[2].label);
                reels[1] && winCh.find('.cuponDesc').text(reels[1].label);


            },
            start: function () {
                var self = $el.slotMachine;
                var game = this;
                game.inGame = true;
                self.setLedPanelOptions('spining');// ставим параметры лед панели
                self.ledAnimations.animationHandler.startAnimation();//стартуем акнимацию лед панели
                $el.find('.winChip').removeClass('winChip');//чистим классы фишек
                self.reels.addClass('spinning');//стартуем анимацию кручения
                self.messageBox.stopAnimation();
                self.messageBox.setRandomText('spinning');//пишем текст текстовой панели
                self.messageBox.animateText("spiningAnimation");//стартуем анимацию текстовой панели
                clearTimeout(self.spinningTimeout);
                self.spinningTimeout = null;//чистим таймаут

                self.spinningTimeout = setTimeout(function () {//ставим таймер на максимальное кручение спинов
                    game.stopAll();//если таймер сработал останавливаем рельсы
                    clearTimeout(self.spinningTimeout);
                    self.spinningTimeout = null;//чистим таймаут
                }, self.config.game.maxTimeSpinning);
                game.result = null;//чистим предыдущий результат
                game.response = null;
                self.send({//шлем запрос на получения результатов спина
                    type: "GET",
                    url: self.slot_config.api_url.play,
                    data: {},
                    cb: function (response) {
                        game.response = response;

                        game.playResultHandler(response);


                    },
                    err: function () {
                        self.notAvailableState();
                    }
                });
            },
            stopAll: function (isAnimationsStopped) {
                var self = $el.slotMachine;
                var game = this;

                console.log('stopAll');
                if (game.response) {
                    self.stopReels();
                    self.config.game.usedUserAttempts++;
                    game.isWin(isAnimationsStopped);
                } else {
                    game.waitingForGameResult = setInterval(function () {
                        if (game.response) {
                            self.stopReels();
                            self.config.game.usedUserAttempts++;
                            game.isWin(isAnimationsStopped);
                            clearInterval(game.waitingForGameResult);
                        }
                    }, 500);
                }

            },
            stop: function (game) {
                var self = $el.slotMachine;
                console.log('stop');
                if (game.reelStoped === false) {
                    game.reelStoped = 0;
                } else {
                    game.reelStoped++;
                }
                self.stopReelSpin($(self.reels[game.reelStoped]));
                $(game.buttonsRow.find('.stop .reel_dot')[game.reelStoped]).addClass('on');
                if (game.reelStoped == 2) {
                    game.stopAll(true);
                    clearTimeout(self.spinningTimeout);
                    game.reelStoped = false;
//                    game.buttonsRow.find('.stop .reel_dot').removeClass('on');
//                    game.buttonsRow.removeClass('stopplay').addClass('toplay');
//                    self.ledAnimations.stopAnimation(500);
//                    self.setLedPanelOptions('stop');
//                    self.ledAnimations.animationHandler.startAnimation();
//                    self.messageBox.stopAnimation();
//                    self.messageBox.animateText("loseAnimation");
//                    self.messageBox.setRandomText('nowin', null, game.user);

                }

            },
            isWin: function (isAnimationsStopped) {
                /*if win*/
                var self = $el.slotMachine;
                var game = this;
                if (game.result) {
                    if (game.result.prizes) {
                        setTimeout(function () {
                            self[game.result.prizes.type == "regular" ? "winChipAnimation" : "winBigChipAnimation" ](game.result.prizes.message);
                        }, 50);
                    } else {
                        setTimeout(function () {
                            self.game.buttonsRow.find('.stop .reel_dot').removeClass('on');
                            self.game.buttonsRow.removeClass('stopplay').addClass('toplay');
                            self.ledAnimations.stopAnimation(500);
                            self.setLedPanelOptions('stop');
                            self.ledAnimations.animationHandler.startAnimation();
                            self.messageBox.stopAnimation();
                            self.messageBox.animateText("loseAnimation");
                            self.messageBox.setRandomText('nowin', null, game.user);
                        }, isAnimationsStopped ? 10 : 1500);
                        game.inGame = false;
                    }

                } else {
                    self.notAvailableState();
                }

            }
        },
        winChipAnimation: function (message) {

            var self = this;


            $.each(self.reels, function (i, reel) {
                setTimeout(function () {
                    $($(reel).find('.chip')[1]).find('.border').addClass('winChip');
                }, i * 500 + 500);

            });
            setTimeout(function () {
                self.$winContainer.find('.tile .rulesText').html(message);
                self.$winContainer.show();
                self.$winContainer.addClass('winner');
                self.$lb_overlay.show();
                self.game.inGame = false;
                self.game.buttonsRow.removeClass('stopplay').addClass('toplay');
                self.ledAnimations.stopAnimation(500);
                self.setLedPanelOptions('stop');
                self.ledAnimations.animationHandler.startAnimation();
                self.messageBox.stopAnimation();
                self.messageBox.animateText("winAnimation");
                self.game.buttonsRow.find('.stop .reel_dot').removeClass('on');
            }, self.reels.length * 500 + 200);
        },
        winBigChipAnimation: function (message) {
            var self = this;


            $.each(self.reels, function (i, reel) {
                setTimeout(function () {
                    $($(reel).find('.chip')[1]).find('.border').addClass('winChip');

                }, i * 500 + 500);

            });
            setTimeout(function () {
                self.$winContainer.find('.tile .rulesText').html(message);
                self.$winContainer.show();
                self.$winContainer.addClass('bigwinner');
                self.$lb_overlay.show();
                self.game.inGame = false;
                self.game.buttonsRow.removeClass('stopplay').addClass('toplay');
                self.ledAnimations.stopAnimation(500);
                self.setLedPanelOptions('stop');
                self.ledAnimations.animationHandler.startAnimation();
                self.messageBox.stopAnimation();
                self.messageBox.animateText("winAnimation");
                self.game.buttonsRow.find('.stop .reel_dot').removeClass('on');
            }, self.reels.length * 500 + 200);

        },
        setLedPanelOptions: function (type) {
            var self = this;
            var animationParams = self.config.ledPanel[type + 'Animation'];
            self.options.type = animationParams[0].type;
            self.options.speed = animationParams[0].speed;
            self.options.n = animationParams[0].n;
            self.options.m = animationParams[0].m;
            self.options.color = animationParams[0].color;
        },
        initLedPanel: function () {
            this.setLedPanelOptions('default');
            this.ledAnimations.initLampArrays();
        },
        ledAnimations: {
            currentAnimation: null,
            leds: null,
            params: {},
            initLampArrays: function () {
                var self = $el.slotMachine;
                self.$canvas = $el.find('#canvas');

                self.maxX = (parseInt(self.$canvas.width() - 48) / 10);
                self.maxY = (parseInt(self.$canvas.height() - 48) / 10);
                for (var i = 0; i < self.maxY; i++) {
                    self.lamps2[i] = [];
                    for (var j = 0; j < self.maxX; j++) {
                        var html = true;
                        if ((i == 0 && j == 0) || (i == self.maxY - 1 && j == 0) || (i == self.maxY - 1 && j == self.maxX - 1) || (i == 0 && j == self.maxX - 1)) {
                            html = false;
                        }
                        if ((i > 2 && i < self.maxY - 3) && (j > 2 && j < self.maxX - 3 )) {
                            html = false;
                        }
                        if (i >= self.maxY - 3 && (j > 15 && j < self.maxX - 16)) {
                            html = false;
                        }
                        if (html) {
                            var options = {};
                            options.y = i * 10 + 5;
                            options.x = j * 10 + 5;
                            options.isGlowing = false;
                            self.lamps.push(options);
                            self.lamps2[i].push(options);
                        }

                    }
                }
                this.step();
            },
            initAnimation: function (params) {
                var anim = this;
                $.each(params, function (i, animation) {
                    anim.setAnimation(animation.type);
                });
            },
            setAnimation: function (animationType) {
                var self = $el.slotMachine;
                if (!animationType) return;
                //animationHandler.stopAnimation();
                this.render(self.options);
                this.step();
            },
            animationHandler: {
                i: 0,
                row: 0,
                rowCol: 0,
                clean: function () {
                    var self = $el.slotMachine;
                    if (this.i >= self.lamps.length) {
                        this.i = 0;
                        this.turnOff();
                    }
                    this.row = 0;
                    this.rowCol = 0;
                },
                turnOff: function () {
                    var self = $el.slotMachine;
                    for (var j = 0; j < self.lamps.length; j++) {
                        self.lamps[j].isGlowing = false;
                    }
                },
                clearParams: function () {
                    var self = $el.slotMachine;
                    this.turnOff();
                    self.options.type = false;
                    self.options.speed = 0;
                    self.options.n = 0;
                    self.options.m = 0;
                    this.i = 0;
                    this.row = 0;
                    this.rowCol = 0;
                },
                random: function (i) {
                    var self = $el.slotMachine;
                    var isOn = parseInt(Math.random() * 2 + 1) % 2 == 0;
                    if (isOn) {
                        self.lamps[i].isGlowing = !self.lamps[i].isGlowing;
                    }
                },
                toggle: function (i) {
                    var self = $el.slotMachine;
                    self.lamps[i].isGlowing = !self.lamps[i].isGlowing;
                },
                leftToRight: function (i, n) {
                    var self = $el.slotMachine;
                    for (var a = 0; a < n; a++) {
                        if (this.i + a < self.lamps.length) {
                            self.lamps[this.i + a].isGlowing = true;
                        }
                    }
                },
                doubleRow: function (i, n, m) {
                    var temp = this.rowCol;
                    var self = $el.slotMachine;

                    for (var r = this.row; r < this.row + m; r++) {
                        //  console.log(temp)
                        for (var a = temp; a < n + temp; a++) {

                            if (self.lamps2[r] && self.lamps2[r][a]) {
                                self.lamps2[r][a].isGlowing = true;
                            }
                            if (self.lamps2[r] && this.rowCol >= self.lamps2[r].length) {
                                this.rowCol = 0;
                                this.row += m;
                            }


                        }
                    }
                },
                startAnimation: function () {
                    var self = $el.slotMachine;
                    this.turnOff();
                    this.i = 0;
                    this.row = 0;
                    this.rowCol = 0;
                    self.isStopped = false;
                    self.ledAnimations.step();
                },
                stopAnimation: function () {
                    var self = $el.slotMachine;
                    self.isStopped = true;
                    this.clearParams();
                }
            },
            render: function (type, n, m) {
                var self = $el.slotMachine;
                var anim = this;
                n = n || 1;
                m = m || 1;
                if (!self.led_off_loaded && !self.led_off_load_started) {
                    self.led_off = new Image();
                }
                var led_offLoadedCb = function () {
                    self.canvas.width = self.canvas.width;
                    self.led_off_loaded = true;
                    var lamps = self.lamps;
                    var lamps2 = self.lamps2;
                    var options = self.options;
                    var context = self.context;
                    for (var i = 0; i < lamps.length; i++) {
                        anim.animationHandler[type] && anim.animationHandler[type](i, n, m);

                        var r = 12;
                        var grd = context.createRadialGradient(lamps[i].x + r * 2, lamps[i].y + r * 2, 4, lamps[i].x + r * 2, lamps[i].y + r * 2, r * 2);
                        var blurColor = lamps[i].isGlowing ? grd : 'rgba(' + options.color + ',0)';
                        var lampColor = lamps[i].isGlowing ? 'rgba(' + options.color + ',1)' : 'rgba(' + options.color + ',0)';
                        grd.addColorStop(1, 'rgba(' + options.color + ',0)');
                        grd.addColorStop(0, 'rgba(' + options.color + ',0.2)');
                        context.save();
                        context.fillStyle = blurColor;
                        context.beginPath();
                        context.arc(lamps[i].x + r * 2, lamps[i].y + r * 2, r * 2, 0, Math.PI * 2, true);
                        context.closePath();
                        context.fill();

                        context.fillStyle = lampColor;
                        context.beginPath();
                        context.arc(lamps[i].x + r * 2, lamps[i].y + r * 2, 4, 0, Math.PI * 2, true);
                        context.closePath();
                        context.fill();
                        context.restore();

                        context.drawImage(self.led_off, lamps[i].x, lamps[i].y);
                    }
                    anim.animationHandler.rowCol += n;
                    anim.animationHandler.i += n;
                    if (type != 'doubleRow') {
                        anim.animationHandler.clean();
                    }
                    else if (anim.animationHandler.row >= lamps2.length) {
                        anim.animationHandler.clean();
                    }
                };
                if (self.led_off_loaded) {
                    led_offLoadedCb();
                } else {
                    if (!self.led_off_load_started) {
                        self.led_off.onload = led_offLoadedCb;
                        self.led_off.src = self.slot_config.api_url.img_led_off || "/css/game/slots/img/slot_led_off.png";
                        self.led_off_load_started = true;
                    }
                }


            },

            step: function () {
                var anim = this;
                var self = $el.slotMachine;
                if (!self.isStopped) {

                    setTimeout(function () {
                        self.ledAnimations.render(self.options.type, self.options.n, self.options.m);
                        requestAnimationFrame(self.ledAnimations.step);
                    }, 1000 / self.options.speed);
                }
            },
            stopAnimation: function (timer) {
                var anim = this;
                setTimeout(function () {
                    //anim.animationHandler.stopAnimation();
                }, timer || 400);

            }

        },
        stopReelSpin: function ($reel) {
            $reel.removeClass('spinning');
        },
        stopReels: function () {
            var self = this;
            $.each(self.reels, function (i, reel) {
                setTimeout(function () {
                    self.stopReelSpin($(reel));
                    $(self.game.buttonsRow.find('.stop .reel_dot')[i]).addClass('on');
                }, i * 500 + 500);
            });
        },

        stopChipAnimation: function () {
            $el.find('.winChip').removeClass('winChip');
        },
        messageBox: {
            textBox: $el.find('#slotsMessageReel #messages'),
            textInterval: {},
            left: 0,
            isShowing: false,
            getRandomText: function (type) {
                var self = $el.slotMachine;
                var labelArr = self.slot_config.labels.messageBox[type];
                return labelArr[parseInt(Math.random() * labelArr.length)];
            },
            setRandomText: function (type, message, inputs) {
                var self = $el.slotMachine;
                this.textBox = $el.find('#slotsMessageReel #messages');
                this.textBox.text(message ? message : this.getRandomText(type).format(inputs));
            },
            init: function () {
                this.stopAnimation();
                this.setRandomText("demo");
                this.animateText("defaultAnimation");
            },
            animateText: function (step) {
                var self = $el.slotMachine;
                var an = self.config.textPanel[step];
                this.applyAnimation(an.animationType, an.step, an.delay, an.speed);

            },
//            leftToRight1: function (step, delay, speed) {
//                console.log("leftToRight");
//                if (this.textBox.css('x') && parseInt(this.textBox.css('x').replace('px', '')) > 600) {
//                    this.textBox.css('x', '-300px');
//                }
//                this.textBox.transition({ x: '+=' + (step ? step : 3) + 'px', delay: delay ? delay : 1, duration: speed ? speed : 100 });
//            },
            leftToRight: function (step, delay, speed) {
                console.log("leftToRight new");
                var self = this;
                self.isShowing = true;
                self.textBox.attr('style', '');
//                self.textBox.animate({left:600}, {duration:15000, easing: "linear"}); //$({ tempMoney: 0 })
                self.textBox.css("left", self.left + "px");
                self.textBox.animate({ left: 577 }, {
//                $({ left: self.left }).animate({ left: 577 }, {
                    duration: speed ? (speed * (Math.abs(self.left) + 577)) / 3 : 100 * 300,
                    easing: 'linear',
                    step: function () {
                        if (self.isShowing) {
                            self.left += 3;
                            self.textBox.css("left", self.left);
                        } else if (self.left > 577) {
//                            this.complete();
                        }
                    },
                    complete: function () {
                        self.textBox.css("left", -577 + "px");
                        self.left = -577;
                        if (self.isShowing) {
                            self.applyAnimation("leftToRight", step, delay, speed);
                        }
                    }
                });
            },
//            rightToLeft: function (step, delay, speed) {
//                console.log("rightToLeft");
//                if (this.textBox.css('x') && parseInt(this.textBox.css('x').replace('px', '')) < -300) {
//                    this.textBox.css('x', '900px');
//                }
//                this.textBox.transition({ x: '-=' + (step ? step : 3) + 'px', delay: delay ? delay : 1, duration: speed ? speed : 100 });
//            },
            rightToLeft: function (step, delay, speed) {
                console.log("rightToLeft new");
                var self = this;
                self.isShowing = true;
                self.textBox.attr('style', '');
                self.textBox.css("left", self.left + "px");
                self.textBox.animate({ left: -577 }, {
//                $({ left: self.left }).animate({ left: -577 }, {
                    duration: speed ? (speed * (Math.abs(self.left) + 577)) / 3 : 100 * 300,
                    easing: 'linear',
                    step: function () {
                        if (self.isShowing) {
                            self.left -= 3;
                            self.textBox.css("left", self.left);
                        }
                    },
                    complete: function () {
                        self.textBox.css("left", 577 + "px");
                        self.left = 577;
                        if (self.isShowing) {
                            self.applyAnimation("rightToLeft", step, delay, speed);
                        }
                    }
                });
            },
//            toggle: function (step, delay, speed) {
//                console.log("toggle");
//                this.textBox.attr('style', '');
//                this.textBox.transition({ x: '0px', delay: 1, duration: 1});
//                var opacity = this.textBox.css('opacity') != 0;
//                this.textBox.transition({ opacity: opacity ? 0 : 1, delay: delay ? delay : 0, duration: speed ? speed : 100 });
//            },
            toggle: function (step, delay, speed) {
                console.log("toggle new");
                var self = this;
                self.isShowing = true;
                self.left = 0;
                self.textBox.css('left', 0 + "px");
                if (self.isShowing) {
                    self.textBox.attr('style', 'left: 1px; -webkit-animation: text_animation_toggle ' + ((speed ? speed : 500) / 1000) + 's linear infinite;')
                }

//                -webkit-animation: text_animation_toggle 0.5s linear infinite;
//                self.textBox.css('left', 0 + "px");
//                self.textBox.animate({opacity: 0 }, { duration: 1000, easing: "linear", complete: function () {
//                    self.textBox.css("opacity", 1);
//                    if (self.isShowing) {
//                        setTimeout(function () {
//                            self.applyAnimation("toggle", step, delay, speed)
//                        },(speed ? speed : 1000));
//
//                    }
//                }});
            },
            random: function (step, delay, speed) {
                console.log("toggle new");
                var self = this;
                self.isShowing = true;
                self.left = 0;
                self.textBox.css('left', 0 + "px");
                if (self.isShowing) {
                    self.textBox.attr('style', 'left: 1px; -webkit-animation: text_animation_toggle ' + ((speed ? speed : 500) / 1000) + 's linear infinite;')
                }
            },
            applyAnimation: function (animationType, step, delay, speed) {
                var textBoxPane = this;
                var self = $el.slotMachine;
//                this.textBox.transition({ x: '0px', delay: 1, duration: 1});
//                this.textBox.attr('style', '');
//                setTimeout(function () {
//                    textBoxPane.textInterval[animationType] = setInterval(function () {
//                        textBoxPane[animationType] && textBoxPane[animationType](step, delay, speed);
//                    }, (speed ? speed : 300) + 50);
//                    console.log(textBoxPane.textInterval);
//                }, speed ? speed : 300);
                textBoxPane.stopAnimation();
                textBoxPane[animationType] && textBoxPane[animationType](step, delay, speed);
            },
            stopAnimation: function () {
                var self = $el.slotMachine;
                var textBoxPane = this;
//                textBoxPane.clearIntervals();
//                this.textBox.attr('style', '');
                this.isShowing = false;
                this.textBox.stop(true, false);
//                this.textBox.transition({ x: '0px', delay: 0, duration: 0});
            },
            clearIntervals: function () {
                var textBoxPane = this;
                clearInterval(textBoxPane.textInterval["toggle"]);
                clearInterval(textBoxPane.textInterval["leftToRight"]);
                clearInterval(textBoxPane.textInterval["rightToLeft"]);
                textBoxPane.textInterval = {};
            }

        }
    };


    $el.slotMachine.initialize();
    $el.data("slotMachine", $el.slotMachine);
    return $el;
};
