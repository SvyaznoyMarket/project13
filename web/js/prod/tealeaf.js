/*!
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 *
 * @version 3.1.0.1520
 * @flags jQuery,DEBUG,SUPPORT_LEGACY_HEADERS,ENABLE_LOCALSTORAGE_CAPTURE
 */

/**
 * @fileOverview Defines the core of the system, namely the TLT object.
 * @exports TLT
 */
/*global window*/
/*jshint loopfunc:true*/
/**
 * TLT (short for Tealeaf Technology) is the top-level object for the system. All
 * objects and functions live under TLT to prevent polluting the global
 * scope. This object also manages the modules and services on the page,
 * controlling their lifecycle, manages inter-module communication.
 * @namespace
 */
// Sanity check
if (window.TLT) {
    throw "Attempting to recreate TLT. Library may be included more than once on the page.";
}
var TLT = (function () {

    "use strict";

    /* Create and add a screenview message to the default queue. Also
     * notifies any listeners of the screenview load/unload event.
     * @param {Enum} type "LOAD" or "UNLOAD" indicating the type
     * of screenview event.
     * @param {string} name User friendly name of the screenview.
     * @param {string} [referrerName] Name of the previous screenview that
     * is being replaced.
     * @param {object} [root] DOMNode which represents the root or
     * parent of this screenview. Usually this is a div container.
     * @returns {void}
     */
    function logScreenview(type, name, referrerName, root) {
        var screenviewMsg = null,
            queue = TLT.getService("queue"),
            replay = TLT.getModule("replay"),
            webEvent = null,
            winLocation = window.location,
            host = winLocation.origin || null;

        // Sanity checks
        if (!name || typeof name !== "string") {
            return;
        }
        if (!referrerName || typeof referrerName !== "string") {
            referrerName = "";
        }

        if (!host) {
            host = (winLocation.protocol || "") + "//" + (winLocation.host || "");
        }

        screenviewMsg = {
            type: 2,
            screenview: {
                type: type,
                name: name,
                url: winLocation.pathname,
                host: host,
                referrer: referrerName
            }
        };

        // XXX: Fix this hack. At least send a fully populated WebEvent object.
        // Ideally, want to use the publishEvent to route this to the correct modules.
        if (type === "LOAD") {
            webEvent = {
                type: "screenview_load"
            };
        } else if (type === "UNLOAD") {
            webEvent = {
                type: "screenview_unload"
            };
        }

        if (webEvent && replay) {
            replay.onevent(webEvent);
        }

        if (type === "LOAD" || type === "UNLOAD") {
            queue.post("", screenviewMsg, "DEFAULT");
        }
    }


    var tltStartTime = (new Date()).getTime(),

        /**
         * A collection of module information. The keys in this object are the
         * module names and the values are an object consisting of three pieces
         * of information: the creator function, the instance, and context object
         * for that module.
         * @private
         */
        modules = {},

        /**
         * A collection of service information. The keys in this object are the
         * service names and the values are an object consisting of two pieces
         * of information: the creator function and the service object.
         * @private
         */
        services = {},

        /**
         * Indicates if the core has been initialized or not.
         * @private
         */
        initialized = false,
        state = null,

        /**
         * Checks whether given frame is blacklisted (in the config) or not.
         * @function
         * @private
         * @param {DOMElement} iframe an element to examine
         * @return {boolean} true if given iframe is blacklisted, false otherwise
         */
        isFrameBlacklisted = (function () {
            var blacklistedFrames,
                checkedFrames = [];

            function prepareBlacklistedFrames(scope) {
                var browserService = core.getService("browser"),
                    blacklist = core.getCoreConfig().framesBlacklist,
                    foundFrames,
                    i;
                blacklistedFrames = blacklistedFrames || [];
                scope = scope || null;
                if (typeof blacklist !== "undefined" && blacklist.length > 0) {
                    for (i = 0; i < blacklist.length; i += 1) {
                        foundFrames = browserService.queryAll(blacklist[i], scope);
                        if (foundFrames && foundFrames.length > 0) {
                            blacklistedFrames = blacklistedFrames.concat(foundFrames);
                        }
                    }
                    checkedFrames = checkedFrames.concat(browserService.queryAll('iframe', scope));
                }
            }

            function isFrameBlacklisted(iframe) {
                if (core.utils.indexOf(checkedFrames, iframe) < 0) {
                    prepareBlacklistedFrames(iframe.ownerDocument);
                }
                return core.utils.indexOf(blacklistedFrames, iframe) > -1;
            }

            isFrameBlacklisted.clearCache = function () {
                blacklistedFrames = null;
            };

            return isFrameBlacklisted;
        }()),

        /**
         * Last clicked element, needed for IE and 'beforeunload'
         * @private
         */
        lastClickedElement = null,

        /**
         * List of service passthroughs. These are methods that are called
         * from TLT and simply pass through to the given service without
         * changing the arguments. Doing this dynamically keeps the code
         * smaller and easier to update.
         * @private
         */
        servicePassthroughs = {

            "config": [

                /**
                 * Returns the global configuration object (the one passed to init()).
                 * @name getConfig
                 * @memberOf TLT
                 * @function
                 * @returns {Object} The global configuration object.
                 */
                "getConfig",

                /**
                 * Updates the global configuration object (the one passed to init()).
                 * @name updateConfig
                 * @memberOf TLT
                 * @function
                 * @returns {void}
                 */
                "updateConfig",

                /**
                 * Returns the core configuration object.
                 * @name getCoreConfig
                 * @memberOf TLT
                 * @function
                 * @returns {Object} The core configuration object.
                 */
                "getCoreConfig",

                /**
                 * Updates the core configuration object.
                 * @name updateCoreConfig
                 * @memberOf TLT
                 * @function
                 * @param {Object} config The updated configuration object.
                 * @returns {void}
                 */
                "updateCoreConfig",

                /**
                 * Returns the configuration object for a module.
                 * @name getModuleConfig
                 * @memberOf TLT
                 * @function
                 * @param {String} moduleName The name of the module to retrieve config data for.
                 * @returns {Object} The configuration object for the given module.
                 */
                "getModuleConfig",

                /**
                 * Updates a configuration object for a module.
                 * @name updateModuleConfig
                 * @memberOf TLT
                 * @function
                 * @param {String} moduleName The name of the module to retrieve config data for.
                 * @param {Object} config The updated configuration object.
                 * @returns {void}
                 */
                "updateModuleConfig",

                /**
                 * Returns a configuration object for a service.
                 * @name getServiceConfig
                 * @memberOf TLT
                 * @function
                 * @param {String} serviceName The name of the service to retrieve config data for.
                 * @returns {Object} The configuration object for the given module.
                 */
                "getServiceConfig",

                /**
                 * Updates a configuration object for a service.
                 * @name updateServiceConfig
                 * @memberOf TLT
                 * @function
                 * @param {String} serviceName The name of the service to retrieve config data for.
                 * @param {Object} config The updated configuration object.
                 * @returns {void}
                 */
                "updateServiceConfig"

            ],

            "queue": [
                /**
                 * Add HTTP header information to the module's default queue.
                 * This doesn't force the event data to be sent to the server,
                 * as this behavior is defined by the queue itself.
                 * @name addHeader
                 * @memberOf TLT
                 * @function
                 * @param  {String} moduleName  The name of the module saving the header.
                 * @param  {String} headerName  The name of the header.
                 * @param  {String} headerValue The value of the header.
                 * @param  {String} [queueId]   Specifies the ID of the queue to receive the event.
                 * @returns {void}
                 */
                "addHeader",
                /**
                 * Send event information to the module's default queue.
                 * This doesn't necessarily force the event data to be sent to the server,
                 * as this behavior is defined by the queue itself.
                 * @name post
                 * @memberOf TLT
                 * @function
                 * @param  {String} moduleName The name of the module saving the event.
                 * @param  {Object} queueEvent The event information to be saved to the queue.
                 * @param  {String} [queueId]    Specifies the ID of the queue to receive the event.
                 * @returns {void}
                 */
                "post",
                /**
                 * Enable/disable the automatic flushing of all queues.
                 * Either periodically by a timer or whenever the queue threshold is reached.
                 * @name setAutoFlush
                 * @memberOf TLT
                 * @function
                 * @param {Boolean} flag Set this to false to disable flushing
                 *                 or set it to true to enable automatic flushing (default)
                 * @returns {void}
                 */
                "setAutoFlush",
                /**
                 * Forces all queues to send their data to the server.
                 * @name flushAll
                 * @memberOf TLT
                 * @function
                 */
                "flushAll"

            ],

            "browserBase": [
                /**
                 * Calculates the xpath of the given DOM Node.
                 * @name getXPathFromNode
                 * @memberOf TLT
                 * @function
                 * @param {DOMElement} node The DOM node who's xpath is to be calculated.
                 * @returns {String} The calculated xpath.
                 */
                "getXPathFromNode",

                /**
                 * Let the UIC library process a DOM event, which was prevented
                 * from bubbling by the application.
                 * @name processDOMEvent
                 * @memberOf TLT
                 * @function
                 * @param {Object} event The browsers event object which was prevented.
                 */
                "processDOMEvent"
            ]
        },

        /**
         * Provides methods for handling load/unload events to make sure that this
         * kind of events will be handled independently to browser caching mechanism
         * @namespace
         * @private
         */
        loadUnloadHandler = (function () {
            var status = {};

            return {

                /**
                 * Normalizes the events specified in the configuration in the following ways:
                 * - For each load/unload module event adds corresponding pageshow/pagehide event.
                 * - Adds beforeunload
                 * - Adds propertychange if W3C service is being used for correct operation on legacy IE.
                 * @param {String} moduleName Name of the module
                 * @param {Array} moduleEvents An array of module event configs
                 * @param {object} [localTop] Local window element
                 * @param {object} [documentScope] document element
                 */
                normalizeModuleEvents: function (moduleName, moduleEvents, localTop, documentScope) {
                    var load = false,
                        unload = false,
                        browserService = core.getService("browser");

                    localTop = localTop || core._getLocalTop();
                    documentScope = documentScope || localTop.document;

                    status[moduleName] = {
                        loadFired: false,
                        pageHideFired: false
                    };

                    core.utils.forEach(moduleEvents, function (eventConfig) {
                        switch (eventConfig.name) {
                        case "load":
                            load = true;
                            moduleEvents.push(core.utils.mixin(core.utils.mixin({}, eventConfig), {
                                name: "pageshow"
                            }));
                            break;

                        case "unload":
                            unload = true;
                            moduleEvents.push(core.utils.mixin(core.utils.mixin({}, eventConfig), {
                                name: "pagehide"
                            }));
                            moduleEvents.push(core.utils.mixin(core.utils.mixin({}, eventConfig), {
                                name: "beforeunload"
                            }));
                            break;

                        // IE6, IE7 and IE8 - catching 'onpropertychange' event to
                        // simulate correct 'change' events on radio and checkbox.
                        // required for W3C only as jQuery normalizes it.
                        case "change":
                            if (core.utils.isLegacyIE && core.getFlavor() === "w3c") {
                                moduleEvents.push(core.utils.mixin(core.utils.mixin({}, eventConfig), {
                                    name: "propertychange"
                                }));
                            }
                            break;
                        }
                    });
                    if (!load && !unload) {
                        delete status[moduleName];
                        return;
                    }
                    status[moduleName].silentLoad = !load;
                    status[moduleName].silentUnload = !unload;
                    if (!load) {
                        moduleEvents.push({name: "load", target: localTop});
                    }
                    if (!unload) {
                        moduleEvents.push({name: "unload", target: localTop});
                    }
                },

                /**
                 * Checks if event can be published for the module(s) or not.
                 * The negative case can take place for load/unload events only, to avoid
                 * redundancy in handler execution. If as example load event was handled
                 * properly, the pageshow event will be ignored.
                 * @param {string} moduleName Name of the module
                 * @param {WebEvent} event An instance of WebEvent
                 * @return {boolean}
                 */
                canPublish: function (moduleName, event) {
                    var mod;
                    if (status.hasOwnProperty(moduleName) === false) {
                        return true;
                    }
                    mod = status[moduleName];
                    switch (event.type) {
                    case "load":
                        mod.pageHideFired = false;
                        mod.loadFired = true;
                        return !mod.silentLoad;
                    case "pageshow":
                        mod.pageHideFired = false;
                        event.type = "load";
                        return !mod.loadFired && !mod.silentLoad;
                    case "pagehide":
                        event.type = "unload";
                        mod.loadFired = false;
                        mod.pageHideFired = true;
                        return !mod.silentUnload;
                    case "unload":
                    case "beforeunload":
                        event.type = "unload";
                        mod.loadFired = false;
                        return !mod.pageHideFired && !mod.silentUnload;
                    }
                    return true;
                },

                /**
                 * Checks if event indicates the core context is unloading.
                 * @param {WebEvent} event An instance of WebEvent
                 * @return {boolean}
                 */
                isUnload: function (event) {
                    return typeof event === "object" ?
                            (event.type === "unload" || event.type === "beforeunload" || event.type === "pagehide") :
                            false;
                }
            };

        }()),

        /**
         * Keeps track of the events being handled.
         * @private
         */
        events = {},

        /**
         * Keeps track of callback functions registered by the iOS and Android native libraries.
         * These are used for communication with the native library.
         */
        bridgeCallbacks = {},

        /**
         * init implementation (defined later)
         * @private
         */
        _init = function () {},
        _callback = null,

        /**
         * Flag to track if TLT.init API can been called.
         * @private
         */
        okToCallInit = true,

        // Used to track touch events for Android due to they do not fire touchends
        _lastTouch = null,
        // Used to track scroll for Android due to they fire after touchend in iOS. I will mimic iOS behavior.
        _hasScroll = false,
        _sendScroll = false,
        // TODO add to a global section
        _isApple = navigator.userAgent.indexOf("iPhone") > -1 || navigator.userAgent.indexOf("iPod") > -1 || navigator.userAgent.indexOf("iPad") > -1,

        // main interface for the core
        core = /**@lends TLT*/ {

            /**
             * @returns {integer} Returns the recorded timestamp in milliseconds corresponding to when the TLT object was created.
             */
            getStartTime: function () {
                return tltStartTime;
            },

            //---------------------------------------------------------------------
            // Core Lifecycle
            //---------------------------------------------------------------------

            /**
             * Initializes the system. The configuration information is passed to the
             * config service to management it. All modules are started (unless their
             * configuration information indicates they should be disabled), and web events
             * are hooked up.
             * @param {Object} config The global configuration object.
             * @param {function} [callback] function executed after initialization and destroy
                    the callback function takes one parameter which describes UIC state;
                    its value can be set to "initialized" or "destroyed"
             * @returns {void}
             */
            init: function (config, callback) {
                var timeoutCallback;
                _callback = callback;
                if (!okToCallInit) {
                    throw "init must only be called once!";
                }
                okToCallInit = false;
                timeoutCallback = function (event) {
                    event = event || window.event || {};
                    if (document.addEventListener || event.type === "load" || document.readyState === "complete") {
                        if (document.removeEventListener) {
                            document.removeEventListener("DOMContentLoaded", timeoutCallback, false);
                            window.removeEventListener("load", timeoutCallback, false);
                        } else {
                            document.detachEvent("onreadystatechange", timeoutCallback);
                            window.detachEvent("onload", timeoutCallback);
                        }
                        _init(config, callback);
                    }
                };

                // case when DOM already loaded (lazy-loaded UIC)
                if (document.readyState === "complete") {
                    // Lets the current browser cycle to complete before calling init
                    setTimeout(timeoutCallback);
                } else if (document.addEventListener) {
                    document.addEventListener("DOMContentLoaded", timeoutCallback, false);
                    // A fallback in case DOMContentLoaded is not supported
                    window.addEventListener("load", timeoutCallback, false);
                } else {
                    document.attachEvent("onreadystatechange", timeoutCallback);
                    // A fallback in case onreadystatechange is not supported
                    window.attachEvent("onload", timeoutCallback);
                }
            },

            /**
             * Indicates if the system has been initialized.
             * @returns {Boolean} True if init() has been called, false if not.
             */
            isInitialized: function () {
                return initialized;
            },

            getState: function () {
                return state;
            },

            /**
             * Shuts down the system. All modules are stopped and all web events
             * are unsubscribed.
             * @returns {void}
             */
            // destroy: function (skipEvents, callback) {
            destroy: function (skipEvents) {

                var token = "",
                    eventName = "",
                    target = null,
                    serviceName = null,
                    service = null,
                    browser = null,
                    delegateTarget = false;

                if (okToCallInit) { //nothing to do
                    return false;
                }

                this.stopAll();

                if (!skipEvents) {
                    browser = this.getService("browser");
                    // Unregister events
                    for (token in events) {
                        if (events.hasOwnProperty(token) && browser !== null) {
                            eventName = token.split("|")[0];
                            target = events[token].target;
                            delegateTarget = events[token].delegateTarget || undefined;
                            browser.unsubscribe(eventName, target, this._publishEvent, delegateTarget);
                        }
                    }
                }

                // call destroy on services that have it
                for (serviceName in services) {
                    if (services.hasOwnProperty(serviceName)) {
                        service = services[serviceName].instance;

                        if (service && typeof service.destroy === "function") {
                            service.destroy();
                        }

                        services[serviceName].instance = null;
                    }
                }

                isFrameBlacklisted.clearCache();
                events = {};
                initialized = false;

                // Reset to allow re-initialization.
                okToCallInit = true;

                state = "destroyed";

                if (typeof _callback === "function") {
                    // Protect against unexpected exceptions since _callback is 3rd party code.
                    try {
                        _callback("destroyed");
                    } catch (e) {
                        // Do nothing!
                    }
                }
            },

            /**
             * Iterates over each module and starts or stops it according to
             * configuration information.
             * @returns {void}
             * @private
             */
            _updateModules: function (scope) {

                var config = this.getCoreConfig(),
                    browser = this.getService("browser"),
                    moduleConfig = null,
                    moduleName = null;

                if (config && browser && config.modules) {
                    try {
                        for (moduleName in config.modules) {
                            if (config.modules.hasOwnProperty(moduleName)) {
                                moduleConfig = config.modules[moduleName];

                                if (modules.hasOwnProperty(moduleName)) {
                                    if (moduleConfig.enabled === false) {
                                        this.stop(moduleName);
                                        continue;
                                    }

                                    this.start(moduleName);

                                    // If the module has specified events in the configuration
                                    // register event handlers for them.
                                    if (moduleConfig.events) {
                                        this._registerModuleEvents(moduleName, moduleConfig.events, scope);
                                    }
                                } else {    // it needs to be loaded
                                    if (browser.loadScript) {
                                        browser.loadScript(config.moduleBase + moduleName + ".js");
                                        // no callback needed because the module will start automatically
                                    }
                                }
                            }
                        }
                        this._registerModuleEvents.clearCache();
                    } catch (e) {
                        core.destroy();
                        return false;
                    }
                } else {
                    return false;
                }
                return true;
            },

            /**
             * Registers event handlers for all modules in a specific scope.
             * E.g. if the application changed the DOM via ajax and want to let
             * us rebind event handlers in this scope.
             * @param  {Object} scope A DOM element as a scope.
             */
            rebind: function (scope) {
                core._updateModules(scope);
            },

            /* Public API which returns the Tealeaf session data that has been
             * configured to be shared with 3rd party scripts.
             * @returns {object} JSON object containing the session data as
             * name-value pairs. If no data is available then returns null.
             */
            getSessionData: function () {

				if (!core.isInitialized()) {
					throw "getSessionData API was called before UIC is initialized.";
				}

                var rv = null,
                    sessionData = null,
                    scName,
                    scValue,
                    config = core.getCoreConfig();

                if (!config || !config.sessionDataEnabled) {
                    return null;
                }

                sessionData = config.sessionData || {};

                // Add any session ID data
                scName = sessionData.sessionQueryName;
                if (scName) {
                    scValue = core.utils.getQueryStringValue(scName, sessionData.sessionQueryDelim);
                } else {
                    // Either the cookie name is configured or the default is assumed.
                    scName = sessionData.sessionCookieName || "TLTSID";
                    scValue = core.utils.getCookieValue(scName);
                }

                if (scName && scValue) {
                    rv = rv || {};
                    rv.tltSCN = scName;
                    rv.tltSCV = scValue;
                    rv.tltSCVNeedsHashing = !!sessionData.sessionValueNeedsHashing;
                }

                return rv;
            },

            /* Public API to create and add a custom event message to the default
             * queue.
             * @param {string} name Name of the custom event.
             * @param {object} customObj Custom object which will be serialized
             * to JSON and included with the custom message.
             * @returns {void}
             */
            logCustomEvent: function (name, customMsgObj) {

				if (!core.isInitialized()) {
					throw "logCustomEvent API was called before UIC is initialized.";
				}

                var customMsg = null,
                    queue = this.getService("queue");

                // Sanity checks
                if (!name || typeof name !== "string") {
                    name = "CUSTOM";
                }
                customMsgObj = customMsgObj || {};

                customMsg = {
                    type: 5,
                    customEvent: {
                        name: name,
                        data: customMsgObj
                    }
                };
                queue.post("", customMsg, "DEFAULT");
            },

            /* Public API to create and add an exception event message to the
             * default queue.
             * @param {string} msg Description of the error or exception.
             * @param {string} [url] URL related to the error or exception.
             * @param {integer} [line] Line number associated with the error or exception.
             * @returns {void}
             */
            logExceptionEvent: function (msg, url, line) {

				if (!core.isInitialized()) {
					throw "logExceptionEvent API was called before UIC is initialized.";
				}

                var exceptionMsg = null,
                    queue = this.getService("queue");

                // Sanity checks
                if (!msg || typeof msg !== "string") {
                    return;
                }
                url = url || "";
                line = line || "";

                exceptionMsg = {
                    type: 6,
                    exception: {
                        description: msg,
                        url: url,
                        line: line
                    }
                };

                queue.post("", exceptionMsg, "DEFAULT");
            },

            /* Public API to create and add a screenview LOAD message to the
             * default queue.
             * @param {string} name User friendly name of the screenview that is
             * being loaded. Note: The same name must be used when the screenview
             * UNLOAD API is called.
             * @param {string} [referrerName] Name of the previous screenview that
             * is being replaced.
             * @param {object} [root] DOMNode which represents the root or
             * parent of this screenview. Usually this is a div container.
             * @returns {void}
             */
            logScreenviewLoad: function (name, referrerName, root) {

				if (!core.isInitialized()) {
					throw "logScreenviewLoad API was called before UIC is initialized.";
				}

                logScreenview("LOAD", name, referrerName, root);
            },

            /* Public API to create and add a screenview UNLOAD message to the
             * default queue.
             * @param {string} name User friendly name of the screenview that is
             * unloaded. Note: This should be the same name used in the screenview
             * LOAD API.
             * @returns {void}
             */
            logScreenviewUnload: function (name) {

				if (!core.isInitialized()) {
					throw "logScreenviewUnload API was called before UIC is initialized.";
				}

                logScreenview("UNLOAD", name);
            },

            /**
             * Public API to add a screenshot capture. This needs to be
             * implemented and registered (see registerBridgeCallbacks)
             * If no callback has been registered, then a call to this API
             * does nothing.
             * @returns {void}
             */
            logScreenCapture: function () {

                if (!core.isInitialized()) {
                    throw "logScreenCapture API was called before UIC is initialized.";
                }

                var screenCaptureCB = bridgeCallbacks.screenCapture;
                if (screenCaptureCB && screenCaptureCB.enabled) {
                    screenCaptureCB.cbFunction();
                }
            },

            /**
             * Public API to allow registration of callback functions
             * These callback types are supported currently:
             * 1. screenCapture: Registering this type enables ability to
             *    take screenshots from script.
             * 2. messageRedirect: Registering this type will allow the
             *    callback function to process (and consume) the message
             *    instead of being handled by the default queue.
             * 3. addRequestHeaders: Registering this type will allow the
             *    callback function to return an array of HTTP request headers
             *    that will be set by the UIC in it's requests to the target.
             * @param {Array} callbacks Array of callback objects. Each object
             *                is of the format: {
             *                    {boolean}  enabled
             *                    {string}   cbType
             *                    {function} cbFunction
             *                }
             *                If the callbacks array is empty then any previously
             *                registered callbacks would be removed.
             * @returns {boolean} true if callbacks were registered. false otherwise.
             */
            registerBridgeCallbacks: function (callbacks) {
                var i = 0,
                    len = 0,
                    cb = null;

                // Sanity check
                if (!callbacks) {
                    return false;
                }
                if (callbacks.length === 0) {
                    // Reset any previously registered callbacks.
                    bridgeCallbacks = {};
                    return false;
                }
                try {
                    for (i = 0, len = callbacks.length; i < len; i += 1) {
                        cb = callbacks[i];
                        if (typeof cb === "object" && cb.cbType && cb.cbFunction) {
                            bridgeCallbacks[cb.cbType] = {
                                enabled: cb.enabled,
                                cbFunction: cb.cbFunction
                            };
                        }
                    }
                } catch (e) {
                    return false;
                }
                return true;
            },

            /**
             * Core function which is invoked by the queue service to allow
             * for the queue to be redirected if a messageRedirect callback
             * has been registered. (see registerBridgeCallbacks)
             * @param {array} queue The queue array containing the individual
             *                message objects.
             * @returns {array} The array that should replace the previously
             *                  passed queue.
             */
            redirectQueue: function (queue) {
                var i,
                    len,
                    cb,
                    retval,
                    sS;

                // Sanity check
                if (!queue || !queue.length) {
                    return queue;
                }

                cb = bridgeCallbacks.messageRedirect;
                if (cb && cb.enabled) {
                    sS = core.getService("serializer");
                    for (i = 0, len = queue.length; i < len; i += 1) {
                        retval = cb.cbFunction(sS.serialize(queue[i]), queue[i]);
                        if (retval && typeof retval === "object") {
                            queue[i] = retval;
                        } else {
                            queue.splice(i, 1);
                            i -= 1;
                            len = queue.length;
                        }
                    }
                }
                return queue;
            },

            _hasSameOrigin: function (iframe) {
                try {
                    return iframe.document.location.host === document.location.host && iframe.document.location.protocol === document.location.protocol;
                } catch (e) {
                    // to be ignored. Error when iframe from different domain
                    //#ifdef DEBUG
                    //TODO add debug log
                    //#endif
                }
                return false;
            },

            /**
             * Core function which is invoked by the queue service to allow
             * for the addRequestHeaders callback (if registered) to be invoked.
             * (see registerBridgeCallbacks)
             * @returns {array} The array of request headers to be set. Each
             *                  object is of the format:
             *                  {
             *                      name: "header name",
             *                      value: "header value",
             *                      recurring: true
             *                  }
             */
            provideRequestHeaders: function () {
                var headers = null,
                    addHeadersCB = bridgeCallbacks.addRequestHeaders;

                if (addHeadersCB && addHeadersCB.enabled) {
                    headers = addHeadersCB.cbFunction();
                }

                return headers;
            },

            /**
             * Utility function used by core._updateModules.
             * It registers event listners according to module configuration.
             * @name core._registerModuleEvents
             * @function
             * @param {string} moduleName name of the module
             * @param {Array} moduleEvents an array of all module-specific events (from UIC configuration)
             * @param {object} scope DOM element where event will be registered; points either to a main window
             *                 object or to IFrame's content window
             */
            _registerModuleEvents: (function () {

                /**
                 * An instance of core.utils.WeakMap us as a cache for mapping DOM elements with their IDs.
                 * Introduced to reduce number of expensive browserBase.ElementData.prototype.examineID calls.
                 * Object initialization in _registerModuleEvents function
                 * @private
                 * @type {object}
                 */
                var idCache,
                    /**
                     * Helper function that returns the localTop or documentScope object if the
                     * specified prop is "window" or "document" respectively.
                     * @private
                     * @function
                     * @param {string|object} prop
                     * @param {object} localTop
                     * @param {object} documentScope
                     * @returns {string|object} localTop if prop value is "window",
                     *                          documentScope if prop value is "document" else
                     *                          returns the prop value itself
                     */
                    normalizeToObject = function (prop, localTop, documentScope) {
                        if (prop === "window") {
                            return localTop;
                        }
                        if (prop === "document") {
                            return documentScope;
                        }
                        return prop;
                    };

                /**
                 * Helper function for core._registerModuleEvents
                 * It does actual event listeners registration, while the main function managesthe scopes.
                 * @function
                 * @private
                 */
                function _registerModuleEventsOnScope(moduleName, moduleEvents, scope) {
                    var browserBase = core.getService("browserBase"),
                        browser = core.getService("browser"),
                        documentScope = core.utils.getDocument(scope),
                        localTop = core._getLocalTop(),
                        isFrame = core.utils.isIFrameDescendant(scope),
                        frameId,
                        e,
						i;

                    scope = scope || documentScope;
                    loadUnloadHandler.normalizeModuleEvents(moduleName, moduleEvents, localTop, documentScope);

					if (isFrame) {
                        frameId = browserBase.ElementData.prototype.examineID(scope).id;
                        // remove one closing ']'
                        if (typeof frameId === "string") {
                            frameId = frameId.slice(0, frameId.length - 1);
                            for (e in events) {
								if (events.hasOwnProperty(e)) {
									for (i = 0; i < events[e].length; i += 1) {
										if (moduleName === events[e][i]) {
											if (e.indexOf(frameId) !== -1) {
												delete events[e];
												break;
											}
										}
									}
								}
							}
                        }
                    }

                    core.utils.forEach(moduleEvents, function (eventConfig) {
                        var target = normalizeToObject(eventConfig.target, localTop, documentScope) || documentScope,
                            delegateTarget = normalizeToObject(eventConfig.delegateTarget, localTop, documentScope),
                            token = "";

                        if (eventConfig.recurseFrames !== true && isFrame) {
                            return;
                        }

                        // If the target is a string it is a CSS query selector, specified in the config.
                        if (typeof target === "string") {
                            if (eventConfig.delegateTarget && core.getFlavor() === "jQuery") {
                                token = core._buildToken4delegateTarget(eventConfig.name, target, eventConfig.delegateTarget);

                                if (!events.hasOwnProperty(token)) {
                                    events[token] = [moduleName];
                                    events[token].target = target;
                                    events[token].delegateTarget = delegateTarget;
                                    browser.subscribe(eventConfig.name, target, core._publishEvent, delegateTarget, token);
                                } else {
                                    events[token].push(moduleName);
                                }
                            } else {
                                core.utils.forEach(browser.queryAll(target, scope), function (element) {
                                    var idData = idCache.get(element);
                                    if (!idData) {
                                        idData = browserBase.ElementData.prototype.examineID(element);
                                        idCache.set(element, idData);
                                    }
                                    token = eventConfig.name + "|" + idData.id + idData.type;
                                    // If the token already exists, do nothing
                                    if (core.utils.indexOf(events[token], moduleName) !== -1) {
                                        return;
                                    }
                                    events[token] = events[token] || [];
                                    events[token].push(moduleName);
                                    // Save a reference to the tokens target to be able to unregister it later.
                                    events[token].target = element;
                                    browser.subscribe(eventConfig.name, element, core._publishEvent);
                                });
                            }
                        // Else: The target, specified in the config, is an object or empty
                        // (defaults to document), generate a token for events which bubble up
                        // (to the window or document object).
                        } else {
                            token = core._buildToken4bubbleTarget(eventConfig.name, target, typeof eventConfig.target === "undefined");
                            if (!events.hasOwnProperty(token)) {
                                events[token] = [moduleName];
                                browser.subscribe(eventConfig.name, target, core._publishEvent);
                            } else {
                                /* XXX: Only add if module entry doesn't exist. */
                                if (core.utils.indexOf(events[token], moduleName) === -1) {
                                    events[token].push(moduleName);
                                }
                            }
                        }

                        if (token !== "") {
                            if (typeof target !== "string") {
                                events[token].target = target;
                            }
                        }
                    });
                }

                /**
                 * Helper function for core._registerModuleEvents. Checks load status of iframes.
                 * @function
                 * @private
                 * @returns {boolean} true when given frame is completely loaded; false otherwise
                 */
                function _isFrameLoaded(hIFrame) {
                    var iFrameWindow = core.utils.getIFrameWindow(hIFrame);
                    return (iFrameWindow !== null) &&
                            core._hasSameOrigin(iFrameWindow) &&
                            (iFrameWindow.document !== null) &&
                            iFrameWindow.document.readyState === "complete";
                }

                // actual implementation of core._registerModuleEvents
                function registerModuleEvents(moduleName, moduleEvents, scope) {
                    scope = scope || core._getLocalTop().document;
                    idCache = idCache || new core.utils.WeakMap();

                    _registerModuleEventsOnScope(moduleName, moduleEvents, scope);
                    if (moduleName !== "performance") {
                        var hIFrame = null,
                            hIFrameWindow = null,
                            browserService = core.getService("browser"),
                            cIFrames = browserService.queryAll("iframe, frame", scope),
                            i,
                            iLength;

                        for (i = 0, iLength = cIFrames.length; i < iLength; i += 1) {
                            hIFrame = cIFrames[i];
                            if (isFrameBlacklisted(hIFrame)) {
                                continue;
                            }
                            if (_isFrameLoaded(hIFrame)) {
                                hIFrameWindow = core.utils.getIFrameWindow(hIFrame);
                                core._registerModuleEvents(moduleName, moduleEvents, hIFrameWindow.document);
                            }

                            (function (moduleName, moduleEvents, hIFrame) {
                                var hIFrameWindow = null,
                                    _iframeContext = {
                                        moduleName: moduleName,
                                        moduleEvents: moduleEvents,
                                        hIFrame: hIFrame,

                                        _registerModuleEventsDelayed: function () {
                                            var hIFrameWindow = null;

                                            if (!isFrameBlacklisted(hIFrame)) {
                                                hIFrameWindow = core.utils.getIFrameWindow(hIFrame);
                                                if (core._hasSameOrigin(hIFrameWindow)) {
                                                    core._registerModuleEvents(moduleName, moduleEvents, hIFrameWindow.document);
                                                }
                                            }
                                        }
                                    };

                                core.utils.addEventListener(hIFrame, "load", function () {
                                    _iframeContext._registerModuleEventsDelayed();
                                });

                                if (core.utils.isLegacyIE && _isFrameLoaded(hIFrame)) {
                                    hIFrameWindow = core.utils.getIFrameWindow(hIFrame);
                                    core.utils.addEventListener(hIFrameWindow.document, "readystatechange", function () {
                                        _iframeContext._registerModuleEventsDelayed();
                                    });
                                }

                            }(moduleName, moduleEvents, hIFrame));
                        }
                    }
                }

                registerModuleEvents.clearCache = function () {
                    if (idCache) {
                        idCache.clear();
                        idCache = null;
                    }
                };

                return registerModuleEvents;
            }()), // end of _registerModuleEvents factory


            /**
             * Build the token for an event using the currentTarget of the event
             * (only if the current browser supports currenTarget) Otherwise uses
             * the event.target
             * @param  {Object} event The WebEvent
             * @return {String}       Returns the token as a string, consist of:
             *         eventType | target id target idtype
             */
            _buildToken4currentTarget: function (event) {
                var target = event.nativeEvent ? event.nativeEvent.currentTarget : null,
                    idData = target ? this.getService("browserBase").ElementData.prototype.examineID(target) :
                            {
                                id: event.target.id,
                                type: event.target.idType
                            };
                return event.type + "|" + idData.id + idData.type;
            },

            /**
             * Build the token for delegate targets
             * @param  {String} eventType The event.type property of the WebEvent
             * @param  {Object} target    The target or currentTarget of the event.
             * @param  {Object} delegateTarget    The delegated target of the event.
             * @return {String}           Returns the token as a string, consist of:
             *            eventType | target | delegateTarget
             */
            _buildToken4delegateTarget: function (eventType, target, delegateTarget) {
                return eventType + "|" + target + "|" + delegateTarget;
            },

            /**
             * Build the token for bubble targets (either window or document)
             * @param  {String} eventType The event.type property of the WebEvent
             * @param  {Object} target    The target or currentTarget of the event.
             * @param  {Object} delegateTarget    The delegated target of the event.
             * @return {String}           Returns the token as a string, consist of:
             *            eventType | null-2 | window or document
             */
            _buildToken4bubbleTarget: function (eventType, target, checkIframe, delegateTarget) {
                var localTop = core._getLocalTop(),
                    localWindow,
                    browserService = core.getService("browser"),
                    _getIframeElement = function (documentScope) {
                        var retVal = null;

                        if (core._hasSameOrigin(localWindow.parent)) {
                            core.utils.forEach(browserService.queryAll("iframe, frame", localWindow.parent.document), function (iframe) {
                                var iFrameWindow = null;

                                if (!isFrameBlacklisted(iframe)) {
                                    iFrameWindow = core.utils.getIFrameWindow(iframe);
                                    if (core._hasSameOrigin(iFrameWindow) && iFrameWindow.document === documentScope) {
                                        retVal = iframe;
                                    }
                                }
                            });
                        }
                        return retVal;
                    },
                    documentScope = core.utils.getDocument(target),
                    browserBase = this.getService("browserBase"),
                    iframeElement = null,
                    tmpTarget,
                    retVal = eventType,
                    idData;

                if (documentScope) {
                    localWindow = documentScope.defaultView || documentScope.parentWindow;
                }

                if (target === window || target === window.window) {
                    retVal += "|null-2|window";
                } else {
                    if (checkIframe && localWindow && core._hasSameOrigin(localWindow.parent) && typeof documentScope !== "undefined" && localTop.document !== documentScope) {
                        iframeElement = _getIframeElement(documentScope);
                        if (iframeElement) {
                            tmpTarget = browserBase.ElementData.prototype.examineID(iframeElement);
                            retVal += "|" + tmpTarget.xPath + "-2";
                        }
                    } else if (delegateTarget && delegateTarget !== document && core.getFlavor() === "jQuery") {
                        // NOTE: elegateTarget !== document  --- because simple jQuery.on has delegateTarget set to document
                        // for event defined without target e.g. { name: "click", recurseFrame: true }
                        retVal += "|null-2|" + core.utils.getTagName(target) + "|" + core.utils.getTagName(delegateTarget);
                    } else {
                        retVal += "|null-2|document";
                    }
                }

                return retVal;
            },

            /**
             * Event handler for when configuration gets updated.
             * @returns {void}
             * @private
             */
            _reinitConfig: function () {

                // NOTE: Don't use "this" in this method, only use "core" to preserve context.
                core._updateModules();
            },

            /**
             * Used to handle touchstart events for nonIOS devices.
             * @returns {Boolean} True added to lastTouch which tracks touch events, false if not.
             * @private
             */
            _handleTouchStart: function (event) {
                var i, j;

                if (_isApple) {
                    return false;
                }
                // First touchStart nothing to compare
                if (_lastTouch === null) {
                    _lastTouch = event;
                    return true;
                }

                // Compare to see if it is a new touch series or older one so it can be handled as a touchEnd
                for (i = 0; i < _lastTouch.nativeEvent.touches.length; i += 1) {
                    for (j = 0; j < event.nativeEvent.touches.length; j += 1) {
                        // It just needs one to be in set so it can be claimed that touchStart is being added to existing touches
                        if (_lastTouch.nativeEvent.touches[i] === event.nativeEvent.touches[j]) {
                            return true;
                        }
                    }
                }

                // It is a new touchStart so we need to handle older touch series
                core._prepNonIosTouchEnd();
                _lastTouch = event;
                return true;
            },

            /**
             * Used to handle touchmove events for nonIOS devices.
             * @returns {Boolean} True added to lastTouch which tracks touch events, false if not.
             * @private
             */
            _handleTouchMove: function (event) {
                if (_isApple) {
                    return;
                }
                _lastTouch = event;
            },

            /**
            * Used to handle scroll events for nonIOS devices due to Android throws these during pinch events.
            * @returns {Boolean} True added to lastTouch which tracks touch events, false if not.
            * @private
            */
            _handleTouchScroll: function (event) {
                if (_isApple) {
                    return false;
                }
                if (_lastTouch !== null && event.type === "scroll") {
                    _lastTouch.target.position.x = event.target.position.x;
                    _lastTouch.target.position.y = event.target.position.y;
                    _hasScroll = true;
                }

                return true;
            },

            /**
             * Used to create and publish touchend event for nonIOS devices.
             * @returns {Boolean} True if touchend event was published, false if not.
             * @private
             */
            _prepNonIosTouchEnd: function () {
                var hasBeenPublished = false;

                if (_lastTouch !== null) {
                    _lastTouch.type = "touchend";
                    _lastTouch.nativeEvent.type = "touchend";
                    core._publishEvent(_lastTouch);
                    // iOS throws scroll event after touchend event
                    if (_hasScroll) {
                        _lastTouch.type = "scroll";
                        _lastTouch.nativeEvent.type = "scroll";
                        _sendScroll = true;
                        core._publishEvent(_lastTouch);
                    }
                    hasBeenPublished = true;
                }
                _lastTouch = null;
                _hasScroll = false;
                _sendScroll = false;

                return hasBeenPublished;
            },

            /**
             * Iterates over each module delivers the event object if the module
             * is interested in that event.
             * @param {Event} event An event object published by the browser service.
             * @returns {void}
             * @private
             */
            _publishEvent: function (event) {

                // NOTE: Don't use "this" in this method, only use "core" to preserve context.

                var moduleName = null,
                    module = null,
                    // generate the explicit token for the element which received the event
                    // if event is delegated it will have event.data set to the token
                    token = (event.delegateTarget && event.data) ? event.data : core._buildToken4currentTarget(event),
                    modules = null,
                    i,
                    len,
                    target,
                    modEvent = null,
                    canIgnore = false,
                    canPublish = false,
                    browserService = core.getService("browser"),
                    delegateTarget = event.delegateTarget || null;

                // ignore native browser 'load' events
                if ((event.type === "load" || event.type === "pageshow") && !event.nativeEvent.customLoad) {
                    return;
                }

                // Touchend events fire properly, we do not care for capturing touchstart or touchend for iOS
                // we only use them for other devices
                if (_isApple &&
                        (event.type === "touchstart" || event.type === "touchmove")) {
                    return;
                }

                if (_lastTouch !== null && event.type !== "touchstart" && event.type !== "touchmove" && event.type !== "scroll" && event.type !== "touchend") {
                    // Android has issues throwing touchend events, we will create one to indicate fingers are off device
                    core._prepNonIosTouchEnd();
                } else {
                    if (event.type === "touchstart") {
                        core._handleTouchStart(event);
                        return;
                    }
                    if (event.type === "touchmove") {
                        core._handleTouchMove(event);
                        return;
                    }
                    if (_lastTouch !== null && event.type === "scroll" && !_sendScroll) {
                        core._handleTouchScroll(event);
                        return;
                    }
                    if (_hasScroll) {
                        token = "scroll|null-2|window";
                    }
                }

                // IE only: ignore 'beforeunload' fired by link placed in blacklist of excluded links
                if (core.utils.isIE) {
                    if (event.type === "click") {
                        lastClickedElement = event.target.element;
                    }
                    if (event.type === "beforeunload") {
                        canIgnore = false;
                        core.utils.forEach(core.getCoreConfig().ieExcludedLinks, function (selector) {
                            var i,
                                len,
                                el = browserService.queryAll(selector);

                            for (i = 0, len = el ? el.length : 0; i < len; i += 1) {
                                if (typeof el[i] !== undefined && el[i] === lastClickedElement) {
                                    // Last clicked element was in the blacklist. Set the ignore flag.
                                    canIgnore = true;
                                    return;
                                }
                            }
                        });

                        if (canIgnore) {
                            // The beforeunload can be ignored.
                            return;
                        }
                    }
                }

                // if an unload event is triggered update the core's internal state to "unloading"
                if (loadUnloadHandler.isUnload(event)) {
                    state = "unloading";
                }

                // ignore native browser 'change' events on IE<9/W3C for radio buttons and checkboxes
                if (event.type === "change" && core.utils.isLegacyIE && core.getFlavor() === "w3c" &&
                        (event.target.element.type === "checkbox" || event.target.element.type === "radio")) {
                    return;
                }

                // use 'propertychange' event in IE<9 to simulate 'change' event on radio and checkbox
                if (event.type === "propertychange") {
                    if (event.nativeEvent.propertyName === "checked" && (event.target.element.type === "checkbox" || (event.target.element.type === "radio" && event.target.element.checked))) {
                        event.type = event.target.type = "change";
                    } else {
                        return;
                    }
                }

                // No module has registered the event for the currentTarget,
                // build token for bubble target (document or window)
                if (!events.hasOwnProperty(token)) {
                    if (event.hasOwnProperty("nativeEvent")) {
                        target = event.nativeEvent.currentTarget || event.nativeEvent.target;
                    }
                    token = core._buildToken4bubbleTarget(event.type, target, true, delegateTarget);
                }

                if (events.hasOwnProperty(token)) {
                    modules = events[token];
                    for (i = 0, len = modules.length; i < len; i += 1) {
                        moduleName = modules[i];
                        module = core.getModule(moduleName);
                        modEvent = core.utils.mixin({}, event);
                        if (module && core.isStarted(moduleName) && typeof module.onevent === "function") {
                            canPublish = loadUnloadHandler.canPublish(moduleName, modEvent);
                            if (canPublish) {
                                module.onevent(modEvent);
                            }
                        }
                    }
                }

                if (modEvent && modEvent.type === "unload" && canPublish) {
                    core.destroy();
                }

            },

            _getLocalTop: function () {
                // Return window.window instead of window due to an IE quirk where (window == top) is true but (window === top) is false
                // In such cases, (window.window == top) is true and so is (window.window === top)  Hence window.window is more reliable
                // to compare to see if the library is included in the top window.
                return window.window;
            },

            //---------------------------------------------------------------------
            // Module Registration and Lifecycle
            //---------------------------------------------------------------------

            /**
             * Registers a module creator with TLT.
             * @param {String} moduleName The name of the module that is created using
             *      the creator.
             * @param {Function} creator The function to call to create the module.
             * @returns {void}
             */
            addModule: function (moduleName, creator) {

                if (modules.hasOwnProperty(moduleName)) {
                    throw new Error("Attempting to add duplicate module '" + moduleName +
                            "' on TLT.");
                }

                modules[moduleName] = {
                    creator: creator,
                    instance: null,
                    context: null,
                    messages: []
                };

                // If the core is initialized, then this module has been dynamically loaded. Start it.
                if (this.isInitialized()) {
                    this.start(moduleName);
                }
            },

            /**
             * Returns the module instance of the given module.
             * @param {String} moduleName The name of the module to retrieve.
             * @returns {Object} The module instance if it exists, null otherwise.
             */
            getModule: function (moduleName) {
                if (modules[moduleName] && modules[moduleName].instance) {
                    return modules[moduleName].instance;
                }
                return null;
            },

            /**
             * Unregisters a module and stops and destroys its instance.
             * @param {String} moduleName The name of the module to remove.
             * @returns {void}
             */
            removeModule: function (moduleName) {

                this.stop(moduleName);
                delete modules[moduleName];
            },

            /**
             * Determines if a module is started by looking for the instance.
             * @param {String} moduleName The name of the module to check.
             * @returns {void}
             */
            isStarted: function (moduleName) {
                return modules.hasOwnProperty(moduleName) && modules[moduleName].instance !== null;
            },

            /**
             * Creates a new module instance and calls it's init() method.
             * @param {String} moduleName The name of the module to start.
             * @returns {void}
             */
            start: function (moduleName) {

                var moduleData = modules[moduleName],
                    instance = null;

                if (!modules.hasOwnProperty(moduleName)) {
                    throw new Error("Attempting to start nonexistent module '" + moduleName +
                            "' on TLT.");
                }

                // Only continue if the module data exists and there's not already an instance
                if (moduleData && moduleData.instance === null) {

                    // create the context and instance
                    moduleData.context = new TLT.ModuleContext(moduleName, this);
                    instance = moduleData.instance = moduleData.creator(moduleData.context);

                    // allow module to initialize itself
                    if (typeof instance.init === "function") {
                        instance.init();
                    }

                }
            },

            /**
             * Starts all registered modules, creating an instance and calling their
             * init() methods.
             * @returns {void}
             */
            startAll: function () {

                var moduleName = null;

                for (moduleName in modules) {
                    if (modules.hasOwnProperty(moduleName)) {
                        this.start(moduleName);
                    }
                }
            },

            /**
             * Stops a module, calls it's destroy() method, and deletes the instance.
             * @param {String} moduleName The name of the module to stop.
             * @returns {void}
             */
            stop: function (moduleName) {

                var moduleData = modules[moduleName],
                    instance = null;

                // Only continue if the module instance exists
                if (moduleData && moduleData.instance !== null) {

                    instance = moduleData.instance;

                    // allow module to clean up after itself
                    if (typeof instance.destroy === "function") {
                        instance.destroy();
                    }

                    moduleData.instance = moduleData.context = null;

                }
            },

            /**
             * Stops all registered modules, calling their destroy() methods,
             * and removing their instances.
             * @returns {void}
             */
            stopAll: function () {

                var moduleName = null;

                for (moduleName in modules) {
                    if (modules.hasOwnProperty(moduleName)) {
                        this.stop(moduleName);
                    }
                }
            },

            //---------------------------------------------------------------------
            // Service Registration and Lifecycle
            //---------------------------------------------------------------------

            /**
             * Registers a service creator with TLT.
             * @param {String} serviceName The name of the service that is created using
             *      the creator.
             * @param {Function} creator The function to call to create the service.
             * @returns {void}
             */
            addService: function (serviceName, creator) {

                if (services.hasOwnProperty(serviceName)) {
                    throw new Error("Attempting to add duplicate service '" + serviceName +
                            "' on TLT.");
                }

                services[serviceName] = {
                    creator: creator,
                    instance: null
                };
            },

            /**
             * Retrieves a service instance, creating it if one doesn't already exist.
             * @param {String} serviceName The name of the service to retrieve.
             * @returns {Object} The service object as returned from the service
             *      creator or null if the service doesn't exist.
             */
            getService: function (serviceName) {
               /* if (!services.hasOwnProperty(serviceName)) {
                    throw new Error("Attempting to request non-existent service '" + serviceName +
                            "' on TLT.");
                }*/
                if (services.hasOwnProperty(serviceName)) {
                    if (!services[serviceName].instance) {
                        // If you want to have a separate ServiceContext, pass it here instead of "this"
                        try {
                            services[serviceName].instance = services[serviceName].creator(this);
                            if (typeof services[serviceName].instance.init === "function") {
                                services[serviceName].instance.init();
                            }
                        } catch (e) {
                            // shut the library down if jQuery or sizzle is not found / not supported
                            if (e.code && (e.code === "JQUERYNOTSUPPORTED" || e.code === "NOQUERYSELECTOR")) {
                                // TODO: reset service instance to null. In light of this, does explicit init for services make sense?
                                // Services should implicitly initialize when created and if any problems are encountered then throw appropriate exception.
                                return null;
                            }
                            // otherwise rethrow the error
                            throw e;
                        }
                        if (typeof services[serviceName].instance.getServiceName !== "function") {
                            services[serviceName].instance.getServiceName = function () {
                                return serviceName;
                            };
                        }
                    }
                    return services[serviceName].instance;
                }
                return null;
            },

            /**
             * Unregisters a service and destroys its instance.
             * @param {String} serviceName The name of the service to remove.
             * @returns {void}
             */
            removeService: function (serviceName) {
                delete services[serviceName];
            },

            //---------------------------------------------------------------------
            // Intermodule Communication
            //---------------------------------------------------------------------

            /**
             * Broadcasts a message throughout the system to all modules who are
             * interested.
             * @param {Object} message An object containing at least a type property
             *      indicating the message type.
             * @returns {void}
             */
            broadcast: function (message) {
                var i = 0,
                    len = 0,
                    prop = null,
                    module = null;

                if (message && typeof message === "object") {

                    if (!message.hasOwnProperty("type")) {
                        throw new Error("Message is missing property 'type'.");
                    }

                    for (prop in modules) {
                        if (modules.hasOwnProperty(prop)) {
                            module = modules[prop];

                            if (core.utils.indexOf(module.messages, message.type) > -1) {
                                if (typeof module.instance.onmessage === "function") {
                                    module.instance.onmessage(message);
                                }
                            }
                        }
                    }
                }
            },

            /**
             * Instructs a module to listen for a particular type of message.
             * @param {String} moduleName The module that's interested in the message.
             * @param {String} messageType The type of message to listen for.
             * @returns {void}
             */
            listen: function (moduleName, messageType) {
                var module = null;

                if (this.isStarted(moduleName)) {
                    module = modules[moduleName];

                    if (core.utils.indexOf(module.messages, messageType) === -1) {
                        module.messages.push(messageType);
                    }
                }
            },
            /**
             * Returns all services
             */
            _getServices: function () {
                return services;
            },
            /**
             * Stops UIC and throws an error.
             * @function
             * @throws {UICError}
             */
            fail: function (message, failcode, skipEvents) {
                message = "UIC FAILED. " + message;
                try {
                    core.destroy(!!skipEvents);
                } finally {
                    core.utils.clog(message);
                    throw new core.UICError(message, failcode);
                }
            },

            /**
             * @constructor
             */
            UICError: (function () {
                function UICError(message, errorCode) {
                    this.message = message;
                    this.code = errorCode;
                }
                UICError.prototype = new Error();
                UICError.prototype.name = "UICError";
                UICError.prototype.constructor = UICError;
                return UICError;
            }()),


            /**
             * Return the name of UIC flavor ("w3c" or "jQuery")
             * @function
             */
            getFlavor: function () {
                // TODO: Use the existing browserService method here
                return "jQuery";
            }
        };

    /**
     * Actual init function called from TLT.init when the DOM is ready.
     * @private
     * @see TLT.init
     */
    _init = function (config, callback) {
        var configService,
            event,
            webEvent,
            baseBrowser,
            browserService;

        if (initialized) {
            core.utils.clog("TLT.init() called more than once. Ignoring.");
            return;
        }

        // Do not initialize if replay is enabled.
        if (TLT && TLT.replay) {
            return;
        }

        configService = core.getService("config");
        configService.updateConfig(config);

        if (!core._updateModules()) {
            if (state !== "destroyed") {
                core.destroy();
            }
            return;
        }

        if (configService.subscribe) {
            configService.subscribe("configupdated", core._reinitConfig);
        }

        initialized = true;
        state = "loaded";

        //generate fake load event to send for modules
        event = {
            type: 'load',
            target: window.window,
            srcElement: window.window,
            currentTarget: window.window,
            bubbles: true,
            cancelBubble: false,
            cancelable: true,
            timeStamp: +new Date(),
            customLoad: true
        };

        baseBrowser = core.getService("browserBase");
        webEvent = new baseBrowser.WebEvent(event);
        core._publishEvent(webEvent);

        if (typeof _callback === "function") {
            // Protect against unexpected exceptions since _callback is 3rd party code.
            try {
                _callback("initialized");
            } catch (e) {
                // Do nothing!
            }
        }
    };

    // Add methods that passthrough to services
    (function () {

        var name = null,
            i,
            len;

        for (name in servicePassthroughs) {
            if (servicePassthroughs.hasOwnProperty(name)) {
                for (i = 0, len = servicePassthroughs[name].length; i < len; i += 1) {
                    (function (serviceName, methodName) {
                        core[methodName] = function () {
                            var service = this.getService(serviceName);
                            if (service) {
                                return service[methodName].apply(service, arguments);
                            }
                        };
                    }(name, servicePassthroughs[name][i]));

                }
            }
        }

    }());

    return core;
}());
/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview Defines utility functions available to all modules via context object or as TLT.utils
 * @exports TLT.utils
 */

/*global TLT, window*/
/*jshint loopfunc:true*/

(function () {

    "use strict";

    var _isIE = (function () {
            var ua = window.navigator.userAgent.toLowerCase();
            return (ua.indexOf("msie") !== -1);
        }()),

        _isLegacyIE = (function () {
            // W3 Navigation timing spec. supported from IE 9 onwards.
            var isNavTimingSupported = !!window.performance;
            return (_isIE && (!isNavTimingSupported || document.documentMode < 9));
        }()),

        utils = {
            /**
             * Indicates if browser is IE.
             */
            isIE: _isIE,

            /**
             * Indicates if browser is IE<9 or IE 9+ running in
             * compatibility mode.
             */
            isLegacyIE: _isLegacyIE,

            /**
             * Helper function to find an item in an array.
             * @param {Array} array The array to search.
             * @param {String} item The item to search for.
             * @returns {int} The index of the item if found, -1 if not.
             */
            indexOf: function (array, item) {
                var i,
                    len;

                if (array && array instanceof Array) {
                    for (i = 0, len = array.length; i < len; i += 1) {
                        if (array[i] === item) {
                            return i;
                        }
                    }
                }

                return -1;
            },

            /**
             * Invokes callback for each element of an array.
             * @param {Array} array The array (or any indexable object) to walk through
             * @param {function} callback Callback function
             * @param {object} [context] context object; if not provided global object will be considered
             */
            forEach: function (array, callback, context) {
                var i,
                    len;

                // Sanity checks
                if (!array || !array.length || !callback || !callback.call) {
                    return;
                }

                for (i = 0, len = array.length; i < len; i += 1) {
                    callback.call(context, array[i], i, array);
                }
            },

            /**
             * Returns true if callback returns true at least once. Callback is
             * called for each array element unless it reaches end of array or
             * returns true.
             * @param {object} array An Array or any indexable object to walk through
             * @param {function} callback A callback function
             * @returns {boolean} True if callback returned true at least once; false otherwise
             */
            some: function (array, callback) {
                var i,
                    len,
                    val = false;

                for (i = 0, len = array.length; i < len; i += 1) {
                    val = callback(array[i], i, array);
                    if (val) {
                        return val;
                    }
                }
                return val;
            },

            /**
             * Converts an arguments object into an array. This is used to augment
             * the arguments passed to the TLT methods used by the Module Context.
             * @param {Arguments} items An array-like collection.
             * @return {Array} An array containing the same items as the collection.
             */
            convertToArray: function (items) {
                var i = 0,
                    len = items.length,
                    result = [];

                while (i < len) {
                    result.push(items[i]);
                    i += 1;
                }

                return result;
            },

            /**
             * Checks whether given parameter is null or undefined
             * @param {*} obj Any value
             * @returns {boolean} True if obj is null or undefined; false otherwise
             */
            isUndefOrNull: function (obj) {
                return typeof obj === "undefined" || obj === null;
            },

            mixin: function (dst) {
                var prop,
                    src,
                    srcId,
                    len;

                for (srcId = 1, len = arguments.length; srcId < len; srcId += 1) {
                    src = arguments[srcId];
                    for (prop in src) {
                        if (Object.prototype.hasOwnProperty.call(src, prop)) {
                            dst[prop] = src[prop];
                        }
                    }
                }
                return dst;
            },

            extend: function (deep, target, src) {
                var prop = "";

                for (prop in src) {
                    if (Object.prototype.hasOwnProperty.call(src, prop)) {
                        if (deep && Object.prototype.toString.call(src[prop]) === "[object Object]") {
                            if (typeof target[prop] === "undefined") {
                                target[prop] = {};
                            }
                            utils.extend(deep, target[prop], src[prop]);
                        } else {
                            target[prop] = src[prop];
                        }
                    }
                }
                return target;
            },

            /**
             * Makes copy of an object.
             * @function
             * @name core.utils.clone
             * @param {object} obj A object that will be cloned.
             * @return {object} Object cloned.
             */
            clone: function (obj) {
                var copy,
                    attr;

                if (null === obj || "object" !== typeof obj) {
                    return obj;
                }

                if (obj instanceof Object) {
                    copy = (Object.prototype.toString.call(obj) === "[object Array]") ? [] : {};
                    for (attr in obj) {
                        if (Object.prototype.hasOwnProperty.call(obj, attr)) {
                            copy[attr] = utils.clone(obj[attr]);
                        }
                    }
                    return copy;
                }
            },

            /**
             *
             */
            createObject: (function () {
                var fn = null,
                    F = null;
                if (typeof Object.create === "function") {
                    fn = Object.create;
                } else {
                    F = function () {};
                    fn = function (o) {
                        if (typeof o !== "object" && typeof o !== "function") {
                            throw new TypeError("Object prototype need to be an object!");
                        }
                        F.prototype = o;
                        return new F();
                    };
                }
                return fn;
            }()),

            /**
             * Method access the object element based on a string. By default it searches starting from window object.
             * @function
             * @example core.utils.access("document.getElementById");
             * @example core.utils.access("address.city", person);
             * @param {string} path Path to object element. Currently on dot separators are supported (no [] notation support)
             * @param {object} [rootObj=window] Root object where there search starts. window by default
             * @return {*} Object element or undefined if the path is not valid
             */
            access: function (path, rootObj) {
                var obj = rootObj || window,
                    arr,
                    i,
                    len;

                if (typeof path !== "string" || (typeof obj !== "object" && obj !== null)) {
                    return;
                }
                arr = path.split(".");
                for (i = 0, len = arr.length; i < len; i += 1) {
                    if (i === 0 && arr[i] === "window") {
                        continue;
                    }
                    if (!Object.prototype.hasOwnProperty.call(obj, arr[i])) {
                        return;
                    }
                    obj = obj[arr[i]];
                    if (i < (len - 1) && !(obj instanceof Object)) {
                        return;
                    }
                }
                return obj;
            },

            /**
             * Checks if a given character is numeric.
             * @param  {String}  character The character to test.
             * @return {Boolean}      Returns true if the given character is a number.
             */
            isNumeric: function (character) {
                return !isNaN(character + 1 - 1);
            },

            /**
             * Checks if a given character is uppercase.
             * @param  {String}  character The character to test.
             * @return {Boolean}      Returns true if the character is uppercase.
             *                        Otherwise false.
             */
            isUpperCase: function (character) {
                return character === character.toUpperCase() &&
                        character !== character.toLowerCase();
            },

            /**
             * Checks if a given character is lowercase.
             * @param  {String}  character The character to test.
             * @return {Boolean}      Returns true if the character is lowercase.
             *                        Otherwise false.
             */
            isLowerCase: function (character) {
                return character === character.toLowerCase() &&
                        character !== character.toUpperCase();
            },

            getDocument: function (node) {
                if (node.nodeType !== 9) {
                    return (!utils.isUndefOrNull(node.ownerDocument)) ? (node.ownerDocument) : (node.document);
                }
                return node;
            },

            getWindow: function (node) {
                if (node.self !== node) {
                    var ownerDocument = utils.getDocument(node);
                    return (!utils.isUndefOrNull(ownerDocument.defaultView)) ? (ownerDocument.defaultView) : (ownerDocument.parentWindow);
                }
                return node;
            },

            /**
             * Given a HTML frame element, returns the window object of the frame. Tries the contentWindow property
             * first. If contentWindow is not accessible, tries the contentDocument.parentWindow property instead.
             * @param {Object} iFrameElement The HTML frame element object.
             * @return {Object} Returns the window object of the frame element or null.
             */
            getIFrameWindow: function (iFrameElement) {
                var contentWindow = null;

                if (!iFrameElement) {
                    return contentWindow;
                }

                try {
                    contentWindow = iFrameElement.contentWindow ||
                        (iFrameElement.contentDocument ? iFrameElement.contentDocument.parentWindow : null);
                } catch (e) {
                    // Do nothing.
                }

                return contentWindow;
            },

            getTagName: function (node) {
                if (node === document) {
                    return "document";
                }
                if (node === window || node === window.window) {
                    return "window";
                }
                if (typeof node === "string") {
                    return node.toLowerCase();
                }
                if (typeof node === "object" && !utils.isUndefOrNull(node) && typeof node.tagName === "string") {
                    return node.tagName.toLowerCase();
                }
                return "";
            },

            /**
             * Returns true if given node is element from a frame
             * @private
             * @param {Element} node DOM element
             * @return {boolean} true if input element is element from a frame; false otherwise
             */
            isIFrameDescendant: function (node) {
                /*jshint eqeqeq:false, eqnull: false */
                /* The != operator below is on purpose due to legacy IE issues, where:
                   window === top returns false, but window == top returns true */
                return utils.getWindow(node) != TLT._getLocalTop();
            },

            /**
             * Takes the orientation in degrees and returns the orientation mode as a
             * text string. 0, 180 and 360 correspond to portrait mode while 90, -90
             * and 270 correspond to landscape.
             * @function
             * @name core.utils.getOrientationMode
             * @param {number} orientation A normalized orientation value such as
             *          0, -90, 90, 180, 270, 360.
             * @return {string} "PORTRAIT" or "LANDSCAPE" for known orientation values.
             * "UNKNOWN" for unrecognized values. "INVALID" in case of error.
             */
            getOrientationMode: function (orientation) {
                var mode = "INVALID";

                if (typeof orientation !== "number") {
                    return mode;
                }

                switch (orientation) {
                case 0:
                case 180:
                case 360:
                    mode = "PORTRAIT";
                    break;
                case 90:
                case -90:
                case 270:
                    mode = "LANDSCAPE";
                    break;
                default:
                    mode = "UNKNOWN";
                    break;
                }

                return mode;
            },

            clog: (function (window) {
                // Console logging should be only enabled in debug builds.
                if (typeof window.console === "object" && typeof window.console.log === "function" && typeof window.console.log.apply === "function") {
                    var c = window.console;
                    return function () {
                        c.log.apply(c, arguments);
                    };
                }
                return function () {
                    // Do nothing!
                };
            }(window)),

            /**
             * Trims any whitespace and returns the trimmed string.
             * @function
             * @name core.utils.trim
             * @param {string} str The string to be trimmed.
             * @return {string} The trimmed string.
             */
            trim: function (str) {
                // Sanity check.
                if (!str || !str.toString) {
                    return str;
                }
                return str.toString().replace(/^\s+|\s+$/g, "");
            },

            /**
             * Trims any whitespace at the beginning of the string and returns the
             * trimmed string.
             * @function
             * @name core.utils.ltrim
             * @param {string} str The string to be trimmed.
             * @return {string} The trimmed string.
             */
            ltrim: function (str) {
                // Sanity check.
                if (!str || !str.toString) {
                    return str;
                }
                return str.toString().replace(/^\s+/, "");
            },

            /**
             * Trims any whitespace at the end of the string and returns the
             * trimmed string.
             * @function
             * @name core.utils.rtrim
             * @param {string} str The string to be trimmed.
             * @return {string} The trimmed string.
             */
            rtrim: function (str) {
                // Sanity check.
                if (!str || !str.toString) {
                    return str;
                }
                return str.toString().replace(/\s+$/, "");
            },

            /**
             * Finds and returns the named cookie's value.
             * @function
             * @name core.utils.getCookieValue
             * @param {string} cookieName The name of the cookie.
             * @param {string} [cookieString] Optional cookie string in which to search for cookieName.
             * If none is specified, then document.cookie is used by default.
             * @return {string} The cookie value if a match is found or null.
             */
            getCookieValue: function (cookieName, cookieString) {
                var i,
                    len,
                    cookie,
                    cookies,
                    cookieValue = null,
                    cookieNameLen;

                try {
                    cookieString = cookieString || document.cookie;

                    // Sanity check.
                    if (!cookieName || !cookieName.toString) {
                        return null;
                    }

                    // Append an '=' to the cookie name
                    cookieName += "=";
                    cookieNameLen = cookieName.length;

                    // Get the individual cookies into an array and look for a match
                    cookies = cookieString.split(';');
                    for (i = 0, len = cookies.length; i < len; i += 1) {
                        cookie = cookies[i];
                        cookie = utils.ltrim(cookie);

                        // Check if cookieName matches the current cookie prefix.
                        if (cookie.indexOf(cookieName) === 0) {
                            // Match found! Get the value (i.e. RHS of "=" sign)
                            cookieValue = cookie.substring(cookieNameLen, cookie.length);
                            break;
                        }
                    }
                } catch (e) {
                    cookieValue = null;
                }

                return cookieValue;
            },

            /**
             * Finds and returns the query parameter's value.
             * @function
             * @name core.utils.getQueryStringValue
             * @param {string} paramName The name of the query parameter.
             * @param {string} [queryDelim] The query string delimiter. Either ";" or "&"
             * @param {string} [queryString] Optional query string in which to search for the query parameter.
             * If none is specified, then document.location.search is used by default.
             * @return {string} The query parameter value if a match is found or null.
             */
            getQueryStringValue: function (paramName, queryDelim, queryString) {
                var i,
                    j,
                    queryStringLen,
                    paramValue = null,
                    valueStartIndex;

                try {
                    queryString = queryString || window.location.search;
                    queryStringLen = queryString.length;

                    // Sanity check.
                    if (!paramName || !paramName.toString || !queryStringLen) {
                        return null;
                    }

                    // Default delimiter is &
                    queryDelim = queryDelim || "&";
                    // Normalize for easy searching by replacing initial '?' with the delimiter
                    queryString = queryDelim + queryString.substring(1);
                    // Modify the parameter name to prefix the delimiter and append an '='
                    paramName = queryDelim + paramName + "=";

                    i = queryString.indexOf(paramName);
                    if (i !== -1) {
                        valueStartIndex = i + paramName.length;
                        // Match found! Get the value (i.e. RHS of "=" sign upto the delim or end of string)
                        j = queryString.indexOf(queryDelim, valueStartIndex);
                        if (j === -1) {
                            j = queryStringLen;
                        }
                        paramValue = decodeURIComponent(queryString.substring(valueStartIndex, j));
                    }
                } catch (e) {
                    // Do nothing!
                }

                return paramValue;
            },

            /**
             * Quick wrapper for addEventL:istener/attachEvent. Mainly to be used for core, before UIC is fully
             * initialized
             * @function
             * @name core.util.addEventListener
             */
            addEventListener: (function () {
                if (window.addEventListener) {
                    return function (element, eventName, listener) {
                        element.addEventListener(eventName, listener, false);
                    };
                }
                return function (element, eventName, listener) {
                    element.attachEvent("on" + eventName, listener);
                };
            }()),

            /**
             * Basic WeakMap implementation - a map which can be indexed with objects.
             * In comparison to the original API 'delete' method has been replaced with 'remove'
             * due to compatibility with legacy IE
             * @constructor
             * @see https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/WeakMap
             */
            WeakMap: (function () {
                function index(data, key) {
                    var i,
                        len;
                    data = data || [];
                    for (i = 0, len = data.length; i < len; i += 1) {
                        if (data[i][0] === key) {
                            return i;
                        }
                    }
                    return -1;
                }
                return function () {
                    var data = [];
                    this.set = function (key, val) {
                        var idx = index(data, key);
                        data[idx > -1 ? idx : data.length] = [key, val];
                    };
                    this.get = function (key) {
                        var arr = data[index(data, key)];
                        return (arr ? arr[1] : undefined);
                    };
                    this.clear = function () {
                        data = [];
                    };
                    this.has = function (key) {
                        return (index(data, key) >= 0);
                    };
                    this.remove = function (key) {
                        var idx = index(data, key);
                        if (idx >= 0) {
                            data.splice(idx, 1);
                        }
                    };
                    this["delete"] = this.remove;
                };
            }())
        };


    if (typeof TLT === "undefined" || !TLT) {
        window.TLT = {};
    }

    TLT.utils = utils;

}());
/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview Defines a simple event target interface that can be inherited
 *      from by other parts of the system.
 * @exports TLT.EventTarget
 */
/*global TLT*/

(function () {

    "use strict";

    /**
     * Abstract type that implements basic event handling capabilities.
     * Other types may inherit from this in order to provide custom
     * events.
     * @constructor
     */
    TLT.EventTarget = function () {

        /**
         * Holds all registered event handlers. Each property represents
         * a specific event, each property value is an array containing
         * the event handlers for that event.
         * @type Object
         */
        this._handlers = {};

    };

    TLT.EventTarget.prototype = {

        /**
         * Restores the constructor to the correct value.
         * @private
         */
        constructor: TLT.EventTarget,

        /**
         * Publishes an event with the given name, which causes all
         * event handlers for that event to be called.
         * @param {String} name The name of the event to publish.
         * @param {Variant} [data] The data to provide for the event.
         * @returns {void}
         */
        publish: function (name, data) {

            var i = 0,
                len = 0,
                handlers = this._handlers[name],
                event = {
                    type: name,
                    data: data
                };

            if (typeof handlers !== "undefined") {
                for (len = handlers.length; i < len; i += 1) {
                    handlers[i](event);
                }
            }

        },

        /**
         * Registers an event handler for the given event.
         * @param {String} name The name of the event to subscribe to.
         * @param {Function} handler The function to call when the event occurs.
         * @returns {void}
         */
        subscribe: function (name, handler) {

            if (!this._handlers.hasOwnProperty(name)) {
                this._handlers[name] = [];
            }

            if (typeof handler !== "function") {
                throw new Error("Event handler for '" + name + "' isn't a function.");
            }

            this._handlers[name].push(handler);
        },

        /**
         * Unregisters an event handler for the given event.
         * @param {String} name The name of the event to unsubscribe from.
         * @param {Function} handler The event handler to remove.
         * @returns {void}
         */
        unsubscribe: function (name, handler) {

            var i = 0,
                len = 0,
                handlers = this._handlers[name];

            if (handlers) {
                for (len = handlers.length; i < len; i += 1) {
                    if (handlers[i] === handler) {
                        handlers.splice(i, 1);
                        return;
                    }
                }
            }
        }

    };

}());
/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview Defines ModuleContext, which is used by all modules.
 * @exports TLT.ModuleContext
 */

/*global TLT*/
/*jshint loopfunc:true*/

/**
 * A layer that abstracts core functionality for each modules. Modules interact
 * with a ModuleContext object to ensure that they're not doing anything
 * they're not allowed to do.
 * @class
 * @param {String} moduleName The name of the module that will use this context.
 * @param {TLT} core The core object. This must be passed in to enable easier
 *        testing.
 */
TLT.ModuleContext = (function () {

    "use strict";

    /**
     * Methods to be exposed from the Core to ModuleContext. ModuleContext
     * simply passes through these methods to the Core. By listing the
     * methods here, the ModuleContext object can be dynamically created
     * to keep the code as small as possible. You can easily add new methods
     * to ModuleContext by adding them to this array. Just make sure the
     * method also exists on TLT and that the first argument for the method
     * on TLT is always the module name.
     *
     * If the method name on ModuleContext is different than on TLT, you can
     * specify that via "contextMethodName:coreMethodName", where contextMethodName
     * is the name of the method on ModuleContext and coreMethodName is
     * the name of the method on TLT.
     *
     * Because the methods aren't actually defined in the traditional sense,
     * the documentation comments are included within the array for proper
     * context.
     * @private
     * @type String[]
     */
    var methodsToExpose = [

        /**
         * Broadcasts a message to the entire system.
         * @name broadcast
         * @memberOf TLT.ModuleContext#
         * @function
         * @param {String} messageName The name of the message to send.
         * @param {Variant} data The data to send along with the message.
         * @returns {void}
         */
        "broadcast",

        /**
         * Returns the configuration object for the module.
         * @name getConfig
         * @memberOf TLT.ModuleContext#
         * @function
         * @returns {Object} The configuration object for the module.
         */
        "getConfig:getModuleConfig",

        /**
         * Tells the system that the module wants to know when a particular
         * message occurs.
         * @name listen
         * @memberOf TLT.ModuleContext#
         * @function
         * @param {String} messageName The name of the message to listen for.
         * @returns {void}
         */
        "listen",

        /**
         * Add HTTP header information to the module's default queue.
         * @name addHeader
         * @memberOf TLT.ModuleContext#
         * @function
         * @param {String} headerName The name of the header.
         * @param {String} headerValue The value of the header.
         * @param {String} [queueId] Specifies the ID of the queue to receive the event.
         * @returns {void}
         */
        "addHeader",

        /**
         * Posts an event to the module's queue.
         * @name post
         * @memberOf TLT.ModuleContext#
         * @function
         * @param {Object} event The event to put into the queue.
         * @param {String} [queueId] The ID of the queue to add the event to.
         * @returns {void}
         */
        "post",

        /**
         * Calculates the xpath of the given DOM Node.
         * @name getXPathFromNode
         * @memberOf TLT.ModuleContext#
         * @function
         * @param {DOMElement} node The DOM node who's xpath is to be calculated.
         * @returns {String} The calculated xpath.
         */
        "getXPathFromNode",

        /**
         * @name getStartTime
         * @memberOf TLT.ModuleContext#
         * @function
         * @returns {integer} Returns the recorded timestamp in milliseconds corresponding to when the TLT object was created.
         */
        "getStartTime"
    ];

    /**
     * Creates a new ModuleContext object. This function ends up at TLT.ModuleContext.
     * @private
     * @param {String} moduleName The name of the module that will use this context.
     * @param {TLT} core The core object. This must be passed in to enable easier
     *        testing.
     */
    return function (moduleName, core) {

        // If you want to add methods that aren't directly mapped from TLT, do it here
        var context = {},
            i = 0,
            len = methodsToExpose.length,
            parts = null,
            coreMethod = null,
            contextMethod = null;

        // Copy over all methods onto the context object
        for (i = 0; i < len; i += 1) {

            // Check to see if the method names are the same or not
            parts = methodsToExpose[i].split(":");
            if (parts.length > 1) {
                contextMethod = parts[0];
                coreMethod = parts[1];
            } else {
                contextMethod = parts[0];
                coreMethod = parts[0];
            }

            context[contextMethod] = (function (coreMethod) {

                return function () {

                    // Gather arguments and put moduleName as the first one
                    var args = core.utils.convertToArray(arguments);
                    args.unshift(moduleName);

                    if (!core.hasOwnProperty(coreMethod)) {
                        throw new Error("Attempting to access method '" + coreMethod +
                                "' on TLT, but it doesn't exist. There's a " +
                                "misconfigured passthru method.");
                    }

                    // Pass through to the Core
                    return core[coreMethod].apply(core, args);
                };

            }(coreMethod));
        }

        context.utils = core.utils;

        return context;
    };

}());
/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview The ConfigService is responsible for managing global configuration settings.
 * This may include receiving dynamic configuration updates from the server at regular intervals.
 * The ConfigService fires a configupdated event when it receives updated configuration information.
 * @exports configService
 */

/*global TLT:true */

/**
 * @name configService
 * @namespace
 */
TLT.addService("config", function (core) {
    "use strict";

    /**
     * Merges a new configuration object/diff into the existing configuration by doing a deep copy.
     * @name configService-mergeConfig
     * @function
     * @private
     * @param  {Object} oldConf Existing configuration object.
     * @param  {Object} newConf New configuration object.
     */
    function mergeConfig(oldConf, newConf) {
        core.utils.extend(true, oldConf, newConf);
        configService.publish("configupdated", configService.getConfig());
    }



    /**
     * Holds the config for core and all services and modules.
     * @private
     * @name configService-config
     * @type {Object}
     */
    var config = {
            core: {},
            modules: {},
            services: {}
        },
        configService = core.utils.extend(false, core.utils.createObject(new TLT.EventTarget()), {
            /**
             * Returns the global configuration object.
             * @return {Object} The global configuration object.
             */
            getConfig: function () {
                return config;
            },
            /**
             * Assigns the global configuration for the system.
             * This is first called when Core.init() is called and also may be called later if new
             * configuration settings are returned from the server. After initial configuration is set,
             * all further calls are assumed to be diffs of settings that should be changed rather than
             * an entirely new configuration object.
             * @param  {Object} newConf The global configuration object.
             */
            updateConfig: function (newConf) {
                mergeConfig(config, newConf);
            },
            /**
             * Returns the configuration object for the core.
             * @return {Object} The core configuration object.
             */
            getCoreConfig: function () {
                return config.core;
            },
            /**
             * Assigns the configuration for the core. All calls are assumed to be diffs
             * of settings that should be changed rather than an entirely new configuration object.
             * @param  {Object} newConf     A partial or complete core configuration object.
             */
            updateCoreConfig: function (newConf) {
                mergeConfig(config.core, newConf);
            },
            /**
             * Returns the configuration object for a given service.
             * @param {String} serviceName The name of the service to retrieve configuration information for.
             * @return {Object|null} The service configuration object or null if the named service doesn't exist.
             */
            getServiceConfig: function (serviceName) {
                // XXX - Return empty object {} instead of null and correct all places where this is being called.
                return config.services[serviceName] || null;
            },
            /**
             * Assigns the configuration for the named service. All calls are assumed to be diffs
             * of settings that should be changed rather than an entirely new configuration object.
             * @param  {String} serviceName The name of the service to update configuration information for.
             * @param  {Object} newConf     A partial or complete service configuration object.
             */
            updateServiceConfig: function (serviceName, newConf) {
                if (typeof config.services[serviceName] === "undefined") {
                    config.services[serviceName] = {};
                }
                mergeConfig(config.services[serviceName], newConf);
            },
            /**
             * Returns the configuration object for a given module.
             * @param {String} moduleName The name of the module to retrieve configuration information for.
             * @return {Object|null} The module configuration object or null if the named module doesn't exist.
             */
            getModuleConfig: function (moduleName) {
                return config.modules[moduleName] || null;
            },
            /**
             * Assigns the configuration for the named module. All calls are assumed to be diffs
             * of settings that should be changed rather than an entirely new configuration object.
             * @param  {String} moduleName The name of the module to update configuration information for.
             * @param  {Object} newConf     A partial or complete module configuration object.
             */
            updateModuleConfig: function (moduleName, newConf) {
                if (typeof config.modules[moduleName] === "undefined") {
                    config.modules[moduleName] = {};
                }
                mergeConfig(config.modules[moduleName], newConf);
            },
            destroy: function () {
                config = {
                    core: {},
                    modules: {},
                    services: {}
                };
            }
        });

    return configService;

});
/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview The QueueService manages all queues in the system.
 * @exports queueService
 */

/*global TLT:true */

/**
 * @name queueService
 * @namespace
 */
TLT.addService("queue", function (core) {
    "use strict";

    /**
     * queueMananger
     * @private
     * @static
     * @name queueService-queueManager
     * @namespace
     */
    var CONFIG       = null,    // queue configuration
        // TODO: replace these with long form names i.e. aS -> ajaxService
        aS           = core.getService("ajax"),          // ajaxService
        bS           = core.getService("browser"),       // browserService
        sS           = core.getService("serializer"),    // serializerService
        cS           = core.getService("config"),        // configService
        mS           = core.getService("message"),       // messageService
        defaultQueue = null,    // config object for default queue
        queueTimers  = {},      // timer id for the queueTick
        autoFlushing = true,    // Bool, indicates whether to flush queues when
                                // threshold is reached or let the application control flushing.
        isInitialized = false,
        queueManager = (function () {
            var queues = {};

            /**
             * Checks if the specified queue exists.
             * @function
             * @name queueService-queueManager.exists
             * @param  {String} queueId The id of the queue to check for existence.
             * @return {Boolean}         Returns true if the queue exists, otherwise false.
             */
            function queueExists(queueId) {
                return typeof queues[queueId] !== "undefined";
            }

            /**
             * Adds a queue to the system.
             * @function
             * @name queueService-queueManager.add
             * @param {String} queueId The id of the queue to add.
             * @param {Object} opts    Some additional configuration options for this queue.
             * @param {String} opts.url  The endpoint URL to which the queue should be flushed.
             * @param {Number} opts.threshold The maximal amount of messages to store
             * in the queue before it gets flushed.
             * @param {String} opts.serialzer The serializer which should be used to serialize
             * the data in the queue when sending it to the server.
             * @return {Object} Returns the newly created queue.
             */
            function addQueue(queueId, opts) {
                if (!queueExists(queueId)) {
                    /* TODO: Add prototype functions to access queue members */
                    queues[queueId] = {
                        data: [],
                        queueId: queueId,
                        url: opts.url,
                        threshold: opts.threshold,
                        serializer: opts.serializer,
                        crossDomainEnabled: !!opts.crossDomainEnabled,
                        crossDomainIFrame: opts.crossDomainIFrame
                    };
                }
                return queues[queueId];
            }

            /**
             * Removes a queue from the system.
             * @function
             * @name queueService-queueManager.remove
             * @param  {String} queueId The id of the queue which should be deleted.
             */
            function removeQueue(queueId) {
                if (queueExists(queueId)) {
                    delete queues[queueId];
                }
            }

            /**
             * Returns the queue object associated with the given queueId.
             * @function
             * @name queueService-queueManager.get
             * @param  {String} queueId The id of the queue to return.
             * @return {Object}         Returns the queue object for the given id.
             */
            function getQueue(queueId) {
                if (queueExists(queueId)) {
                    return queues[queueId];
                }
                return null;
            }

            /**
             * Clears all items in the queue specified by the queue id.
             * @function
             * @name queueService-queueManager.clear
             * @param  {String} queueId The id of the queue which should be cleared.
             */
            function clearQueue(queueId) {
                var queue = getQueue(queueId);
                if (queue !== null) {
                    queue.data = [];
                }
            }

            /**
             * Returns the queue data and clears the queue.
             * @function
             * @name queueService-queueManager.flush
             * @param  {String} queueId The id of the queue to be flushed.
             * @return {Array}         Returns all items which were stored in the queue.
             */
            function flushQueue(queueId) {
                var data = null;
                if (queueExists(queueId)) {
                    data = getQueue(queueId).data;
                    clearQueue(queueId);
                }
                return data;
            }

            /**
             * Adds an item to a specific queue.
             * @function
             * @name queueService-queueManager.push
             * @param  {String} queueId The id of the queue to which the item should be added.
             * @param  {Object} data    The message object which should be stored in the queue.
             * @return {Number}         Returns the current length of the queue.
             */
            function pushToQueue(queueId, data) {
                var queue = null,
                    jsonStr = null,
                    bridgeAndroid = window.tlBridge,
                    bridgeiOS = window.iOSJSONShuttle;

                // Send to Native Android Bridge
                if ((typeof bridgeAndroid !== "undefined") &&
                        (typeof bridgeAndroid.addMessage === "function")) {
                    jsonStr = sS.serialize(data);
                    bridgeAndroid.addMessage(jsonStr);
                // Send to Native iOS Bridge
                } else if ((typeof bridgeiOS !== "undefined") &&
                        (typeof bridgeiOS === "function")) {
                    jsonStr = sS.serialize(data);
                    bridgeiOS(jsonStr);
                // Send to normal library queue
                } else {
                    if (queueExists(queueId)) {
                        queue = getQueue(queueId);
/*jshint devel:true */
                        if (typeof console !== "undefined") {
                            console.log("Added to queueId: ", queueId, " data: ", data);
                        }
                        queue.data.push(data);
                        /* Redirect the queue so any registered callback function
                         * can optionally modify it.
                         */
                        queue.data = core.redirectQueue(queue.data);
                        return queue.data.length;
                    }
                }
                return 0;
            }

            /**
             * @scope queueManager
             */
            return {
                exists: queueExists,
                add: addQueue,
                remove: removeQueue,
                get: getQueue,
                clear: clearQueue,
                flush: flushQueue,
                push: pushToQueue
            };

        }());


    /**
     * Handles the xhr response of the server call.
     * @function
     * @private
     * @name queueService-handleXhrCallback
     */
    function handleXhrCallback() {
        // TODO
    }

    /**
    * Get the path relative to the host.
    * @addon
    */
    function getUrlPath() {
        return window.location.pathname;
    }

    /**
     * Adds a HTTP header (name,value) pair to the specified queue.
     * @function
     * @private
     * @name queueService-addHeaderToQueue
     * @param  {String} queueId The id of the queue which should be flushed.
     * @param  {String} headerName The name of the header to be added.
     * @param  {String} headerValue The value of the header to be added.
     * @param  {Boolean} [recurring] Flag specifying if header should be sent
     *                   once (false) or always (true). Default behavior is to
     *                   send the header once.
     */
    function addHeaderToQueue(queueId, headerName, headerValue, recurring) {
        var queue = queueManager.get(queueId),
            header = {
                name: headerName,
                value: headerValue
            },
            qHeadersList = null;

        // Sanity check
        if (typeof headerName !== "string" || typeof headerValue !== "string") {
            return;
        }

        if (!queue.headers) {
            // TODO: Add prototype functions to help add/copy/remove headers
            queue.headers = {
                once: [],
                always: []
            };
        }

        qHeadersList = !!recurring ? queue.headers.always : queue.headers.once;
        qHeadersList.push(header);
    }

    /**
     * Copies HTTP headers {name,value} from the specified queue to an
     * object.
     * @function
     * @private
     * @name queueService-copyHeaders
     * @param  {String} queueId The id of the queue whose headers are copied.
     * @param  {Object} [headerObj] The object to which headers are added. If no
     * object is specified then a new one is created.
     * @return {Object} The object containing the copied headers.
     */
    function copyHeaders(queueId, headerObj) {
        var i = 0,
            len = 0,
            queue = queueManager.get(queueId),
            qHeaders = queue.headers,
            headersList = null;

        headerObj = headerObj || {};

        function copy(l, o) {
            var i = 0,
                len = 0,
                header = null;

            for (i = 0, len = l.length; i < len; i += 1) {
                header = l[i];
                o[header.name] = header.value;
            }
        }

        if (qHeaders) {
            headersList = [qHeaders.always, qHeaders.once];

            for (i = 0, len = headersList.length; i < len; i += 1) {
                copy(headersList[i], headerObj);
            }
        }

        return headerObj;
    }

    /**
     * Clear HTTP headers {name,value} from the specified queue. Only headers
     * that are to be sent once are cleared.
     * @function
     * @private
     * @name queueService-clearHeaders
     * @param  {String} queueId The id of the queue whose headers are cleared.
     */
    function clearHeaders(queueId) {
        var queue = null,
            qHeaders = null;

        if (!queueManager.exists(queueId)) {
            throw new Error("Queue: " + queueId + " does not exist!");
        }

        queue = queueManager.get(queueId);
        qHeaders = queue ? queue.headers : null;
        if (qHeaders) {
            // Only reset headers that are sent once.
            qHeaders.once = [];
        }
    }

    /**
     * Invoke the core function to get any HTTP request headers from
     * external scripts and add these headers to the default queue.
     * @function
     * @private
     * @returns The number of external headers added to the queue.
     */
    function getExternalRequestHeaders() {
        var i = 0,
            len,
            header,
            headers = core.provideRequestHeaders();

        if (headers && headers.length) {
            for (i = 0, len = headers.length; i < len; i += 1) {
                header = headers[i];
                addHeaderToQueue("DEFAULT", header.name, header.value, header.recurring);
            }
        }
        return i;
    }

    /**
     * Clears a specific queue and sends its serialized content to the server.
     * @function
     * @private
     * @name queueService-flushQueue
     * @param  {String} queueId The id of the queue to be flushed.
     */
    function flushQueue(queueId, sync) {
        var data = queueManager.flush(queueId),
            count = data !== null ? data.length : 0,
            queue = queueManager.get(queueId),
            httpHeaders = {
                "Content-Type": "application/json",
                "X-Tealeaf": "device (UIC) Lib/3.1.0.1520",
                "X-TealeafType": "GUI",  // For our past sins
                "X-TeaLeaf-Page-Url": getUrlPath()
            },
            serializer = queue.serializer || "json",
            requestData,
            xdomainFrameWindow = null;

        data = mS.wrapMessages(data);

        if (count) {
            getExternalRequestHeaders();
            copyHeaders(queueId, httpHeaders);

            if (queue.crossDomainEnabled) {
                xdomainFrameWindow = core.utils.getIFrameWindow(queue.crossDomainIFrame);
                if (!xdomainFrameWindow) {
                    core.utils.clog("Cannot access xdomain frame window.");
                    return;
                }
                requestData = {
                    request: {
                        url: queue.url,
                        async: !sync,
                        headers: httpHeaders,
                        data: sS.serialize(data, serializer)
                    }
                };

                if (!core.utils.isIE && typeof window.postMessage === "function") {
                    xdomainFrameWindow.postMessage(requestData, queue.crossDomainIFrame.src);
                } else {
                    try {
                        xdomainFrameWindow.sendMessage(requestData);
                    } catch (e) {
                        core.utils.clog("Cannot access sendMessage API on xdomain frame window.");
                        return;
                    }
                }
            } else {
                aS.sendRequest({
                    oncomplete: handleXhrCallback,
                    url: queue.url,
                    async: !sync,
                    headers: httpHeaders,
                    data: sS.serialize(data, serializer)
                });
            }

            clearHeaders(queueId);
        }
    }

    /**
     * Iterates over all queues and sends their contents to the servers.
     * @function
     * @private
     * @name queueServive-flushAll
     */
    function flushAll(sync) {
        var conf = null,
            queues = CONFIG.queues,
            i = 0;
        for (i = 0; i < queues.length; i += 1) {
            conf = queues[i];
            flushQueue(conf.qid, sync);
        }
        return true;
    }


    /**
     * Adds a message event to the specified queue.
     * If the queue threshold is reached the queue gets flushed.
     * @function
     * @private
     * @name queueService-addToQueue
     * @param {String} queueId The id of the queue which should be flushed.
     * @param {Object} data    The message event which should be stored in the queue.
     */
    function addToQueue(queueId, data) {
        var length = queueManager.push(queueId, mS.createMessage(data));
        if (length >= queueManager.get(queueId).threshold &&
                autoFlushing && core.getState() !== "unloading") {
            flushQueue(queueId);
        }
    }

    /**
     * Returns the queue id for the queue which is responsible for the given module.
     * @function
     * @private
     * @name queueService-getQueueId
     * @param  {String} moduleName The name of the module for which the id should get looked up.
     * @return {String}            Returns the queue id for the corresponding queue or the default queue id.
     */
    function getQueueId(moduleName) {
        var conf = null,
            queues = CONFIG.queues,
            module = "",
            i = 0,
            j = 0;

        for (i = 0; i < queues.length; i += 1) {
            conf = queues[i];
            if (conf && conf.modules) {
                for (j = 0; j < conf.modules.length; j += 1) {
                    module = conf.modules[j];
                    if (module === moduleName) {
                        return conf.qid;
                    }
                }
            }
        }
        return defaultQueue.qid;
    }


    function setTimer(qid, interval) {
        queueTimers[qid] = window.setTimeout(function tick() {
            flushQueue(qid);
            queueTimers[qid] = window.setTimeout(tick, interval);
        }, interval);
    }


    function clearTimers() {
        var key = 0;

        for (key in queueTimers) {
            if (queueTimers.hasOwnProperty(key)) {
                window.clearTimeout(queueTimers[key]);
                delete queueTimers[key];
            }
        }

        queueTimers = {};
    }


    /**
     * Handles the configupdated event from the configService and reinitialize all queues.
     * @function
     * @private
     * @name queueService-handleConfigUpdated
     * @param  {Object} newConf The new configuration object diff.
     */
    function handleConfigUpdated(newConf) {
        // TODO: merge config
    }



    /**
     * Sets up all the needed queues and event handlers and start the queueTick.
     * @function
     * @private
     * @param  {Object} config The queueService configuration object.
     */
    function initQueueService(config) {
        CONFIG = config;

        core.utils.forEach(CONFIG.queues, function (conf, i) {
            var crossDomainIFrame = null;
            if (conf.qid === "DEFAULT") {
                defaultQueue = conf;
            }
            if (conf.crossDomainEnabled) {
                crossDomainIFrame = bS.query(conf.crossDomainFrameSelector);
                if (!crossDomainIFrame) {
                    core.fail("Cross domain iframe not found");
                }
            }

            queueManager.add(conf.qid, {
                url: conf.endpoint,
                threshold: conf.maxEvents,
                serializer: conf.serializer,
                timerInterval: conf.timerInterval || 0,
                crossDomainEnabled: conf.crossDomainEnabled || false,
                crossDomainIFrame: crossDomainIFrame
            });

            if (typeof conf.timerInterval !== "undefined" && conf.timerInterval > 0) {
                setTimer(conf.qid, conf.timerInterval);
            }
        });

        cS.subscribe("configupdated", handleConfigUpdated);

        isInitialized = true;
    }

    function destroy() {
        if (autoFlushing) {
            flushAll(!CONFIG.asyncReqOnUnload);
        }
        cS.unsubscribe("configupdated", handleConfigUpdated);

        clearTimers();

        CONFIG = null;
        defaultQueue = null;
        isInitialized = false;
    }

    /**
     * @scope queueService
     */
    return {
        addHeaderToQueue: addHeaderToQueue,
        copyHeaders: copyHeaders,
        clearHeaders: clearHeaders,
        getExternalRequestHeaders: getExternalRequestHeaders,
        getQueueManager: function () {
            return queueManager;
        },
        getAutoFlushing: function () {
            return autoFlushing;
        },
        init: function () {
            if (!isInitialized) {
                initQueueService(cS.getServiceConfig("queue") || {});
            } else {
                core.utils.clog("Attempt to initialize service which has been already initialized(queueService)");
            }
        },

        /**
         * Get's called when the core shut's down.
         * Clean up everything.
         */
        destroy: function () {
            destroy();
        },

        // TODO: Need to expose for selenium functional tests
        _getQueue: function (qid) { return queueManager.get(qid).data; },

        /**
         * Adds a HTTP header (name,value) pair to the specified queue.
         * @param  {String} moduleName The name of the module saving the event.
         * @param  {String} headerName The name of the header to be added.
         * @param  {String} headerValue The value of the header to be added.
         * @param  {String} queueId The id of the queue which should be flushed.
         */
        addHeader: function (moduleName, headerName, headerValue, queueId) {
            queueId = queueId || getQueueId(moduleName);
            if (!queueManager.exists(queueId)) {
                throw new Error("Queue: " + queueId + " does not exist!");
            }
            addHeaderToQueue(queueId, headerName, headerValue);
        },

        /**
         * Enables/disables automatic flushing of queues so that the application
         * could decide on their own when to flush by calling flushAll.
         * @param {Boolean} flag Could be either true or false to enable or disable
         *                  auto flushing respectively.
         */
        setAutoFlush: function (flag) {
            if (flag === true) {
                autoFlushing = true;
            } else {
                autoFlushing = false;
            }
        },

        /**
         * Forces a particular queue to be flushed, sending its information to the server.
         * @param  {String} queueId The ID of the queue to be flushed.
         */
        flush: function (queueId) {
            if (!queueManager.exists(queueId)) {
                throw new Error("Queue: " + queueId + " does not exist!");
            }
            flushQueue(queueId);
        },

        /**
         * Forces all queues to be flushed, sending all queue information to the server.
         */
        flushAll: function (sync) {
            return flushAll(!!sync);
        },

        /**
         * Send event information to the module's default queue.
         * This doesn't necessarily force the event data to be sent to the server,
         * as this behavior is defined by the queue itself.
         * @param  {String} moduleName The name of the module saving the event.
         * @param  {Object} queueEvent The event information to be saved to the queue.
         * @param  {String} [queueId]    Specifies the ID of the queue to receive the event.
         */
        post: function (moduleName, queueEvent, queueId) {
            queueId = queueId || getQueueId(moduleName);
            if (!queueManager.exists(queueId)) {
                throw new Error("Queue: " + queueId + " does not exist!");
            }
            addToQueue(queueId, queueEvent);
        }
    };

});

/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview The browserService implements some low-level methods for
 * modifying / accessing the DOM.
 * @exports browserService
 */

/*global TLT, XPathResult, document, ActiveXObject */

/**
 * @name browserService
 * @namespace
 */
TLT.addService("browserBase", function (core) {
    "use strict";

    var nonClickableTags = {
            OPTGROUP: true,
            OPTION: true,
            NOBR: true
        },
        queryDom = {},
        configService = core.getService("config"),
        serializer,
        config,
        blacklist,
        customid,
        getXPathFromNode,
        isInitialized = false;

    function updateConfig() {
        configService = core.getService("config");
        serializer = core.getService("serializer");
        config = core.getService("config").getServiceConfig("browser") || {};
        blacklist = config.hasOwnProperty("blacklist") ? config.blacklist : [];
        customid = config.hasOwnProperty("customid") ? config.customid : [];
    }

    function initBrowserBase() {
        updateConfig();
        configService.subscribe("configupdated", updateConfig);

        isInitialized = true;
    }

    function destroy() {
        configService.unsubscribe("configupdated", updateConfig);

        isInitialized = false;
    }

    function checkId(node) {
        var i,
            len,
            re;

        if (!node || !node.id || typeof node.id !== "string") {
            return false;
        }

        for (i = 0, len = blacklist.length; i < len; i += 1) {
            if (typeof blacklist[i] === "string") {
                if (node.id === blacklist[i]) {
                    return false;
                }
            } else if (typeof blacklist[i] === "object") {
                re = new RegExp(blacklist[i].regex, blacklist[i].flags);
                if (re.test(node.id)) {
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * Generates an XPath for a given node
     * @function
     */
    getXPathFromNode = (function () {

        var specialChildNodes = {
                "NOBR": true,
                "P": true
            };

        /**
         * Returns Xpath string for a node
         * @private
         * @param {Element} node DOM element
         * @return {string} xpath string
         */
        function getXPathArrayFromNode(node) {
            var i,
                j,
                idValid = false,
                tmp_child = null,
                parent_window = null,
                parent_node = null,
                xpath = [],
                loop = true,
                localTop = core._getLocalTop();

            while (loop) {
                loop = false;

                if (!core.utils.isUndefOrNull(node)) {
                    if (!core.utils.isUndefOrNull(node.tagName)) {
                        // Hack fix to handle tags that are not normally visual elements
                        if (specialChildNodes.hasOwnProperty(node.tagName)) {
                            node = node.parentNode;
                        }
                    }
                    for (idValid = checkId(node);
                            node !== document && !idValid;
                            idValid = checkId(node)) {
                        parent_node = node.parentNode;
                        if (!parent_node) {
                            parent_window = core.utils.getWindow(node);
                            parent_node = (parent_window !== localTop) ? parent_window.frameElement : document;
                        }

                        tmp_child = parent_node.firstChild;
                        if (typeof tmp_child === "undefined") {
                            return xpath;
                        }

                        for (j = 0; tmp_child; tmp_child = tmp_child.nextSibling) {
                            if (tmp_child.nodeType === 1 && tmp_child.tagName === node.tagName) {
                                if (tmp_child === node) {
                                    xpath[xpath.length] = [node.tagName, j];
                                    break;
                                }
                                j += 1;
                            }
                        }
                        node = parent_node;
                    }

                    if (idValid) {
                        xpath[xpath.length] = [node.id];
                        if (core.utils.isIFrameDescendant(node)) {
                            loop = true;
                            node = core.utils.getWindow(node).frameElement;
                        }
                    }
                }
            }

            return xpath;
        }

        // actual getXPathFromNode function
        return function (node) {
            var xpath = getXPathArrayFromNode(node),
                parts = [],
                i = xpath.length;

            if (i < 1) {
                return "null";
            }
            while (i) {
                i -= 1;
                if (xpath[i].length > 1) {
                    parts[parts.length] = '["' + xpath[i][0] + '",' + xpath[i][1] + "]";
                } else {
                    parts[parts.length] = '[' + serializer.serialize(xpath[i][0], "json") + ']';
                }
            }
            return ("[" + parts.join(",") + "]");
        };
    }());


    /**
     * Returns true if an event is a jQuery event wrpper object.
     * @private
     * @param {UIEvent} event Browser event to examine
     * @return {boolean} true if given event is jQuery event
     */
    function isJQueryEvent(event) {
        return event && typeof event.originalEvent !== "undefined" &&
            typeof event.isDefaultPrevented !== "undefined"  &&
            !event.isSimulated;
    }


    /**
     * Looks for event details. Usually it returns an event itself, but for touch events
     * function returns an element from one of the touch arrays.
     * @private
     * @param {UIEvent} event Browser event. If skipped function will look for window.event
     * @return {UIEvent} latest touch details for touch event or original event object
     *          for all other cases
     */
    function getEventDetails(event) {
        if (!event) {
            return null;
        }
        if (event.type && event.type.indexOf("touch") === 0) {
            if (isJQueryEvent(event)) {
                event = event.originalEvent;
            }
            if (event.type === "touchstart") {
                event = event.touches[event.touches.length - 1];
            } else if (event.type === "touchend") {
                event = event.changedTouches[0];
            }
        }
        return event;
    }


    /**
     * Normalizes the event object for InternetExplorer older than 9.
     * @return {HttpEvent} normalized event object
     */
    function normalizeEvent(event) {
        var e = event || window.event,
            doc = document.documentElement,
            body = document.body;

        // skip jQuery event wrapper
        if (isJQueryEvent(e)) {
            e = e.originalEvent;
        }

        // IE case
        if (typeof event === 'undefined' || typeof e.target === 'undefined') {
            e.target = e.srcElement || window.window;
            e.timeStamp = Number(new Date());
            if (e.pageX === null || typeof e.pageX === "undefined") {
                e.pageX = e.clientX + ((doc && doc.scrollLeft) || (body && body.scrollLeft) || 0) -
                    ((doc && doc.clientLeft) || (body && body.clientLeft) || 0);
                e.pageY = e.clientY + ((doc && doc.scrollTop)  || (body && body.scrollTop)  || 0) -
                    ((doc && doc.clientTop)  || (body && body.clientTop)  || 0);
            }
            e.preventDefault = function () {
                this.returnValue = false;
            };
            e.stopPropagation = function () {
                this.cancelBubble = true;
            };
        }

        return e;
    }

    /**
     * Normalizes target element. In case of touch event the target is considered to be an
     * element for whch the last action took place
     * @private
     * @param {UIEvent} event browser event
     * @return {Element} DOM element
     */
    function normalizeTarget(event) {
        var itemSource = null;

        if (!event) {
            return null;
        }

        if (event.srcElement) {
            // IE
            itemSource = event.srcElement;
        } else {
            // W3C
            itemSource = event.target;
            if (!itemSource) {
                // Mozilla only (non-standard)
                itemSource = event.explicitOriginalTarget;
            }
            if (!itemSource) {
                // Mozilla only (non-standard)
                itemSource = event.originalTarget;
            }
        }

        if (!itemSource && event.type.indexOf("touch") === 0) {
            itemSource = getEventDetails(event).target;
        }

        while (itemSource && nonClickableTags[itemSource.tagName]) {
            itemSource = itemSource.parentNode;
        }

        // IE when srcElement pointing to window
        if (!itemSource && event.srcElement === null) {
            itemSource = window.window;
        }

        return itemSource;
    }


    /**
     * Returns event position independently to the event type.
     * In case of touch event the position of last action will be returned.
     * @private
     * @param {UIEvent} event Browser event
     * @return {Object} object containing x and y properties
     */
    function getEventPosition(event) {
        var posX = 0,
            posY = 0,
            doc = document.documentElement,
            body = document.body;

        event = getEventDetails(event);

        if (event) {
            if (event.pageX || event.pageY) {
                posX = event.pageX;
                posY = event.pageY;
            } else if (event.clientX || event.clientY) {
                posX = event.clientX + (doc ? doc.scrollLeft : (body ? body.scrollLeft : 0)) -
                                       (doc ? doc.clientLeft : (body ? body.clientLeft : 0));
                posY = event.clientY + (doc ? doc.scrollTop : (body ? body.scrollTop : 0)) -
                                       (doc ? doc.clientTop : (body ? body.clientTop : 0));
            }
        }

        return {
            x: posX,
            y: posY
        };
    }

    /**
     * Find one or more elements using a XPath selector.
     * TODO: Move xpath to browser base service.
     * @function
     * @name browserService-queryDom.xpath
     * @param  {String} query The XPath query to search for.
     * @param  {Object} [scope="document"] The DOM subtree to run the query in.
     * @return {Object}       Returns the DOM element matching the XPath.
     * @todo test the xpath implementation and probably fix it.
     */
    queryDom.xpath = function (query, scope) {
        var xpath = serializer.parse(query),
            elem,
            pathElem = null,
            i,
            j,
            k,
            len,
            jlen;

        scope = typeof scope !== "undefined" ? scope : document;
        elem = scope;

        if (!xpath) {
            return null;
        }

        for (i = 0, len = xpath.length; i < len && elem; i += 1) {
            pathElem = xpath[i];
            if (pathElem.length === 1) {
                elem = scope.getElementById(pathElem[0]);
            } else {
                for (j = 0, k = -1, jlen = elem.childNodes.length; j < jlen; j += 1) {
                    if (elem.childNodes[j].nodeType === 1 && elem.childNodes[j].tagName.toUpperCase() === pathElem[0]) {
                        k += 1;
                        if (k === pathElem[1]) {
                            elem = elem.childNodes[j];
                            break;
                        }
                    }
                }
                if (k === -1) {
                    return null;
                }
            }
        }

        return elem === scope || !elem ? null : elem;
    };


    /**
     * The Point interface represents a point on the page to
     *     x- and y-coordinates.
     * @constructor
     * @private
     * @name browserService-Point
     * @param {Integer} x The x-coordinate of the point.
     * @param {Integer} y The y-coordinate of the point.
     */
    function Point(x, y) {
        this.x = x || 0;
        this.y = y || 0;
    }


    /**
     * The Size  interface represents the width and height of an element
     *     on the page.
     * @constructor
     * @private
     * @name browserService-Size
     * @param {Integer} width  Width of the element that received the event.
     * @param {Integer} height Height of the element that received the event.
     */
    function Size(width, height) {
        this.width = width || 0;
        this.height = height || 0;
    }


    /**
     * The ElementData interface represents a normalized browser event object.
     * @constructor
     * @private
     * @name browserService-ElementData
     * @param {Object} event  The browser event.
     * @param {Object} target The HTML element which received the event.
     */
    function ElementData(event, target) {
        var id,
            type,
            pos;

        target = normalizeTarget(event);
        id = this.examineID(target);
        type = this.examineType(target, event);
        pos = this.examinePosition(event, target);

        this.element = target;
        this.id = id.id;
        this.idType = id.type;
        this.type = type.type;
        this.subType = type.subType;
        this.state = this.examineState(target);
        this.position = new Point(pos.x, pos.y);
        this.size = new Size(pos.width, pos.height);
        this.xPath = id.xPath;
        this.name = id.name;
    }

    /**#@+
     * @constant
     * @enum {Number}
     * @fieldOf browserService-ElementData
     */
    ElementData.HTML_ID = -1;
    ElementData.XPATH_ID = -2;
    ElementData.ATTRIBUTE_ID = -3;
    /**#@-*/

    /**
     * Examines how to specify the target element
     *     (either by css selectors or xpath)
     *     and returns an object with the properties id and type.
     * @function
     * @name browserService-ElementData.examineID
     * @param  {Object} target The HTML element which received the event.
     * @return {Object}        Returns an object with the properties id and type.
     *      id contains either a css or xpath selector.
     *      type contains a reference to either ElementData.HTML_ID,
     *      ElementData.XPATH_ID or ElementData.ATTRIBUTE_ID
     * @todo determine the element css/xpath/attribute selector.
     */
    ElementData.prototype.examineID = function (target) {
        var id,
            type,
            xPath,
            attribute_id,
            name,
            i = customid.length,
            attrib;

        try {
            xPath = getXPathFromNode(target);
        } catch (e) { }
        name = target.name;

        try {
            if (!core.utils.isIFrameDescendant(target)) {

                if (checkId(target)) {
                    id = target.id;
                    type = ElementData.HTML_ID;
                } else if (customid.length && target.attributes) {
                    while (i) {
                        i -= 1;
                        attrib = target.attributes[customid[i]];
                        if (typeof attrib !== "undefined") {
                            id = customid[i] + "=" + (attrib.value || attrib);
                            type = ElementData.ATTRIBUTE_ID;
                        }
                    }
                }
            }
        } catch (e2) { }

        if (!id) {
            id = xPath;
            type = ElementData.XPATH_ID;
        }

        return {
            id: id,
            type: type,
            xPath: xPath,
            name: name
        };
    };


    /**
     * Examines the type and subType of the event.
     * @function
     * @name browserService-ElementData.examineType
     * @param  {Object} event The native browser event.
     * @return {Object}       Returns an object which contains the type and
     *     subType of the event.
     * @todo determine the event type and subtype.
     */
    ElementData.prototype.examineType = function (target, event) {
        var subType = "";
        if (event.type === "change") {
            if (target.tagName === "TEXTAREA" ||
                    (target.tagName === "INPUT" && target.type === "text")) {
                subType = "textChange";
            } else {
                subType = "valueChange";
            }
        } else {
            subType = event.type;
        }
        return {
            type: event.type,
            subType: subType
        };
    };

    /**
     * Examines the current state of the HTML element if it's an input/ui element.
     * @function
     * @name browserService-ElementData.examineState
     * @param  {Object} target The HTML element which received the event.
     * @return {Object}        Returns an object which contains all properties
     *     to describe the state.
     * @todo determine the current state.
     */
    ElementData.prototype.examineState = function (target) {
        var tagnames = {
                "a": ["innerText", "href"],
                "input": {
                    "range": ["maxValue:max", "value"],
                    "checkbox": ["value", "checked"],
                    "radio": ["value", "checked"],
                    "image": ["src"]
                },
                "select": ["value"],
                "button": ["value", "innerText"],
                "textarea": ["value"]
            },
            tagName = typeof target.tagName !== "undefined" ? target.tagName.toLowerCase() : "",
            properties = tagnames[tagName] || null,
            selectedOption = null,
            values = null,
            i = 0,
            len = 0,
            alias = null,
            key = "";

        if (properties !== null) {
            // For input elements, another level of indirection is required
            if (Object.prototype.toString.call(properties) === "[object Object]") {
                // default state for input elements is represented by the "value" property
                properties = properties[target.type] || ["value"];
            }
            values = {};
            for (key in properties) {
                if (properties.hasOwnProperty(key)) {
                    if (properties[key].indexOf(":") !== -1) {
                        alias = properties[key].split(":");
                        values[alias[0]] = target[alias[1]];
                    } else if (properties[key] === "innerText") {
                        values[properties[key]] = core.utils.trim(target.innerText || target.textContent);
                    } else {
                        values[properties[key]] = target[properties[key]];
                    }
                }
            }
        }

        // Special processing for select lists
        if (tagName === "select" && target.options && !isNaN(target.selectedIndex)) {
            values.index = target.selectedIndex;
            if (values.index >= 0 && values.index < target.options.length) {
                selectedOption = target.options[target.selectedIndex];
                /* Select list value is derived from the selected option's properties
                 * in the following order:
                 * 1. value
                 * 2. label
                 * 3. text
                 * 4. innerText
                 */
                values.value = selectedOption.getAttribute("value") || selectedOption.getAttribute("label") || selectedOption.text || selectedOption.innerText;
                values.text = selectedOption.text || selectedOption.innerText;
            }
        }

        return values;
    };


    /**
     * Gets the current zoom value of the browser with 1 being equivalent to 100%.
     * @function
     * @name getZoomValue
     * @return {int}        Returns zoom value of the browser.
     */
    function getZoomValue() {
        var factor = 1,
            rect,
            physicalW,
            logicalW;

        if (document.body.getBoundingClientRect) {
            // rect is only in physical pixel size in IE before version 8
            // CS-8780: getBoundingClientRect() can throw an exception in certain instances. Observed
            // on IE 9
            try {
                rect = document.body.getBoundingClientRect();
            } catch (e) {
                core.utils.clog("getBoundingClientRect failed.", e);
                return factor;
            }
            physicalW = rect.right - rect.left;
            logicalW = document.body.offsetWidth;

            // the zoom level is always an integer percent value
            factor = Math.round((physicalW / logicalW) * 100) / 100;
        }
        return factor;
    }

    /**
     * Gets BoundingClientRect value from a HTML element.
     * @function
     * @name getBoundingClientRectNormalized
     * @param  {Object} element The HTML element.
     * @return {Object} An object with x, y, width, and height.
     */
    function getBoundingClientRectNormalized(element) {
        var rect,
            rectangle,
            zoom,
            scrollPos;

        if (!element || !element.getBoundingClientRect) {
            return { x: 0, y: 0, width: 0, height: 0 };
        }
        // CS-8780: getBoundingClientRect() can throw an exception in certain instances. Observed
        // on IE 9
        try {
            rect = element.getBoundingClientRect();
            scrollPos = getDocScrollPosition(document);
        } catch (e) {
            core.utils.clog("getBoundingClientRect failed.", e);
            return { x: 0, y: 0, width: 0, height: 0 };
        }
        rectangle = {
            // Normalize viewport-relative left & top with scroll values to get left-x & top-y relative to the document
            x: rect.left + scrollPos.left,
            y: rect.top + scrollPos.top,
            width: rect.right - rect.left,
            height: rect.bottom - rect.top
        };
        if (core.utils.isIE) {
            // IE ONLY: the bounding rectangle include the top and left borders of the client area
            rectangle.x -= document.documentElement.clientLeft;
            rectangle.y -= document.documentElement.clientTop;

            zoom = getZoomValue();
            if (zoom !== 1) {  // IE 7 at non-default zoom level
                rectangle.x = Math.round(rectangle.x / zoom);
                rectangle.y = Math.round(rectangle.y / zoom);
                rectangle.width = Math.round(rectangle.width / zoom);
                rectangle.height = Math.round(rectangle.height / zoom);
            }
        }
        return rectangle;
    }

    /**
     * Examines the position of the event relative to the HTML element which
     * received the event on the page. The top left corner of the element is 0,0
     * and bottom right corner of the element is equal to it's width, height.
     * @function
     * @name browserService-ElementData.examinePosition
     * @param  {Object} target The HTML element which received the event.
     * @return {Point}        Returns a Point object.
     */
    ElementData.prototype.examinePosition = function (event, target) {
        var posOnDoc = getEventPosition(event),
            elPos = getBoundingClientRectNormalized(target);

        elPos.x = (posOnDoc.x || posOnDoc.y) ? Math.round(Math.abs(posOnDoc.x - elPos.x)) : elPos.width / 2;
        elPos.y = (posOnDoc.x || posOnDoc.y) ? Math.round(Math.abs(posOnDoc.y - elPos.y)) : elPos.height / 2;

        return elPos;
    };


    /**
     * The WebEvent  interface represents a normalized browser event object.
     *     When an event occurs, the BrowserService wraps the native event
     *     object in a WebEvent.
     * @constructor
     * @private
     * @name browserService-WebEvent
     * @param {Object} event The native browser event.
     */
    function WebEvent(event) {
        var pos;

        this.data = event.data || null;
        this.delegateTarget = event.delegateTarget || null;

        event = normalizeEvent(event);
        pos = getEventPosition(event);
        this.custom = false;    // @TODO: how to determine if it's a custom event?
        this.nativeEvent = this.custom === true ? null : event;
        this.position = new Point(pos.x, pos.y);
        this.target = new ElementData(event, event.target);
        // Do not rely on browser provided event.timeStamp since FF sets
        // incorrect values. Refer to Mozilla Bug 238041
        this.timestamp = (new Date()).getTime();
        this.type = event.type;

        // normalize event type for jQuery events focusin, focusout
        switch (this.type) {
        case "focusin":
            this.type = "focus";
            break;
        case "focusout":
            this.type = "blur";
            break;
        default:
            break;
        }
    }


    function processDOMEvent(event) {
        core._publishEvent(new WebEvent(event));
    }

    /**
     * Returns the scroll position (left, top) of the document
     * Reference: https://developer.mozilla.org/en-US/docs/Web/API/Window.scrollX
     * @private
     * @param {DOMObject} doc The document object.
     * @return {Object} An object specifying the document's scroll offset position {left, top}
     */
    function getDocScrollPosition(doc) {
        var scrollPos = {
                left: -1,
                top: -1
            },
            docElement;

        doc = doc || document;
        // Get the scrollLeft, scrollTop from documentElement or body.parentNode or body in that order.
        docElement = doc.documentElement || doc.body.parentNode || doc.body;

        // If window.pageXOffset exists, use it. Otherwise fallback to getting the scrollLeft position.
        scrollPos.left = (typeof window.pageXOffset === "number") ? window.pageXOffset : docElement.scrollLeft;
        scrollPos.top = (typeof window.pageYOffset === "number") ? window.pageYOffset : docElement.scrollTop;

        return scrollPos;
    }

    return {
        // Expose private functions for unit testing
        normalizeEvent: normalizeEvent,
        normalizeTarget: normalizeTarget,
        getEventDetails: getEventDetails,
        getEventPosition: getEventPosition,
        getBoundingClientRectNormalized: getBoundingClientRectNormalized,
        checkId: checkId,
        getZoomValue: getZoomValue,
        getDocScrollPosition: getDocScrollPosition,
        init: function () {
            if (!isInitialized) {
                initBrowserBase();
            } else {
                core.utils.clog("Attempt to initialize service which has been already initialized(browserBaseService)");
            }
        },
        destroy: function () {
            destroy();
        },
        WebEvent: WebEvent,
        ElementData: ElementData,
        processDOMEvent: processDOMEvent,
        getXPathFromNode: function (moduleName, node) {
            return getXPathFromNode(node);
        },
        queryDom: queryDom
    };

});
/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview The browserService implements some low-level methods for
 * modifying / accessing the DOM.
 * @exports browserService
 */

/*global TLT:true, XPathResult:true, document: true */
/*global console: false */

/**
 * @name browserService
 * @namespace
 */
TLT.addService("browser", function (core) {
    "use strict";

    var jQuery,
        queryDom,
        handlerMappings,
        errorCodes = {
            JQUERY_NOT_SUPPORTED: "JQUERYNOTSUPPORTED",
            JQUERY_NOT_FOUND: "JQUERYNOTFOUND"
        },
        configService = core.getService("config"),
        base = core.getService('browserBase'),
        browser  = configService.getServiceConfig("browser") || {},
        // from w3c
        addEventListener = null,
        removeEventListener = null,
        isInitialized = false;


    /**
     * Returns a new function which will be used in the subscribe method and
     *     which calls the handler function with the normalized WebEvent.
     * @private
     * @function
     * @name browserService-wrapWebEvent
     * @param  {Function} handler The handler which was passed to the
     *     browserService's subscribe method.
     * @return {Function}         Returns a new function which, when called,
     *     passes a WebEvent to the handler.
     */
    function wrapWebEvent(handler) {
        return function (event) {
            var webEvent = new base.WebEvent(event);
            handler(webEvent);
        };
    }


    /**
     * Check wether a certain method exists on the jQuery object. If not an exception is thrown.
     * @function
     * @private
     * @name browserService-jQueryFnExists
     * @param  {Objcet} object   The jQuery object.
     * @param  {String} property The methodname te test for.
     */
    function jQueryFnExists(object, property) {
        if (typeof object[property] !== "function") {
            core.fail("jQuery Object does not support " + property, errorCodes.JQUERY_NOT_SUPPORTED);
        }
    }


    /**
     * Check for correct jQuery version and methods.
     * Throws an exception if jQuery is not supported by the library.
     * @function
     * @private
     * @name browserService-verifyJQuery
     */
    function verifyJQuery() {
        var wrapperFunctions = ["on", "off"],
            jQFunctions = ["ajax", "find", "getScript"],
            jQVersion = (typeof jQuery === "function" && typeof jQuery.fn === "object") ? jQuery.fn.jquery : 0,
			jQVersionMajor = jQVersion !== 0 ? parseInt(jQVersion.split(".")[0], 10) : 0,
			jQVersionMinor = jQVersion !== 0 ? parseInt(jQVersion.split(".")[1], 10) : 0,
            i,
            len = 0,
            dummyWrapper = null;
        if (typeof jQuery !== "function") {
            core.fail("jQuery not found.", errorCodes.JQUERY_NOT_FOUND);
        }

        for (i = 0, len = jQFunctions.length; i < len; i += 1) {
            jQueryFnExists(jQuery, jQFunctions[i]);
        }

        for (i = 0, len = wrapperFunctions.length, dummyWrapper = jQuery({}); i < len; i += 1) {
            jQueryFnExists(dummyWrapper, wrapperFunctions[i]);
        }

        if (!(jQVersionMajor >= 2 || (jQVersionMajor === 1 && jQVersionMinor >= 7))) {
            core.fail("jQuery Object has the wrong version (" + jQVersion + ")", errorCodes.JQUERY_NOT_SUPPORTED);
        }
    }


    /**
     * @private
     * @namespace
     * @name browserService-queryDom
     */
    queryDom = {
        /**
         * Helper function to transform a nodelist into an array.
         * @function
         * @name browserService-queryDom.list2Array
         * @param  {List} nodeList Pass in a DOM NodeList
         * @return {Array}          Returns an array.
         */
        list2Array: function (nodeList) {
            var len = nodeList.length,
                result = [],
                i;

            // Sanity check
            if (!nodeList) {
                return result;
            }

            if (typeof nodeList.length === "undefined") {
                return [nodeList];
            }

            for (i = 0; i < len; i += 1) {
                result[i] = nodeList[i];
            }
            return result;
        },

        /**
         * Finds one or more elements in the DOM using a CSS or XPath selector
         * and returns an array instead of a NodeList.
         * @function
         * @name browserService-queryDom.find
         * @param  {String} query Pass in a CSS or XPath selector query.
         * @param  {Object} [scope="document"]  The DOM subtree to run the query in.
         *      If not provided, document is used.
         * @param  {String} [type="css"]  The type of query. Either "css' (default)
         *      or 'xpath' to allow XPath queries.
         * @return {Array}       Returns an array of nodes that matches the particular query.
         */
        find: function (query, scope, type) {
            type = type || "css";
            return this.list2Array(this[type](query, scope));
        },

        /**
         * Find one or more elements using a CSS selector.
         * @function
         * @name browserService-queryDom.css
         * @param  {String} query The CSS selector query.
         * @param  {Object} [scope="document"] The DOM subtree to run the query in.
         * @return {Array}       Returns an array of nodes that matches the particular query.
         */
        css: function (query, scope) {
            scope = scope || document;
            return jQuery(scope).find(query).get();
        }
    };

    // store handler functions which got passed to subscribe/unsubscribe.
    handlerMappings = (function () {
        var data = new core.utils.WeakMap();

        return {
            add: function (originalHandler) {
                var handlers = data.get(originalHandler) || [wrapWebEvent(originalHandler), 0];

                handlers[1] += 1;
                data.set(originalHandler, handlers);
                return handlers[0];
            },

            find: function (originalHandler) {
                var handlers = data.get(originalHandler);
                return handlers ? handlers[0] : null;
            },

            remove: function (originalHandler) {
                var handlers = data.get(originalHandler);
                if (handlers) {
                    handlers[1] -= 1;
                    if (handlers[1] <= 0) {
                        data.remove(originalHandler);
                    }
                }
            }
        };
    }());

    /**
     * Initialization function
     * @function
     */
    function initBrowserServiceJQuery(config) {
        var useCapture = (browser.useCapture === true);

        queryDom.xpath = base.queryDom.xpath;

        // find jQuery object
        if (config.hasOwnProperty("jQueryObject")) {
            jQuery = core.utils.access(config.jQueryObject);
        } else {
            jQuery = window.jQuery;
        }

        // verify jQuery
        verifyJQuery();

        // register event functions
        if (useCapture && typeof document.addEventListener === 'function') {
            addEventListener = function (target, eventName, handler) {
                var _handler = function (e) { handler(jQuery.event.fix(e)); };
                target.addEventListener(eventName, _handler, useCapture);
            };
            removeEventListener = function (target, eventName, handler) {
                var _handler = function (e) { handler(jQuery.event.fix(e)); };
                target.removeEventListener(eventName, _handler, useCapture);
            };
        } else {
            jQueryFnExists(jQuery({}), "on");
            addEventListener = function (target, eventName, handler) {
                jQuery(target).on(eventName, handler);
            };
            jQueryFnExists(jQuery({}), "off");
            removeEventListener = function (target, eventName, handler) {
                jQuery(target).off(eventName, handler);
            };
        }

        isInitialized = true;
    }


    /**
     * @scope browserService
     */
    return {
        jQueryFnExists: jQueryFnExists,
        verifyJQuery: verifyJQuery,
        handlerMappings: handlerMappings,

        /**
         * Initializes the service
         */
        init: function () {
            if (!isInitialized) {
                initBrowserServiceJQuery(configService.getServiceConfig("browser") || {});
            } else {
                core.utils.clog("Attempt to initialize service which has been already initialized(browserService.jQuery)");
            }
        },

        /**
         * Destroys service state
         */
        destroy: function () {
            isInitialized = false;
        },

        /**
         * Returns service name
         */
        getServiceName: function () {
            return "jQuery";
        },

        /**
         * Find a single element in the DOM mathing a particular query.
         * @param  {String} query Either a CSS or XPath query.
         * @param {Object} [scope="document"] The DOM subtree to run the query in.
         *     If not provided document is used.
         * @param  {String} [type="css"]  The type of the query. Either 'css' (default)
         *     or 'xpath' to allow XPath queries.
         * @return {Object|null}       The first matching HTML element or null if not found.
         */
        query: function (query, scope, type) {
            jQueryFnExists(jQuery, "find");
            try {
				return queryDom.find(query, scope, type)[0] || null;
			} catch (err) {
				console.log(err.message);
				return [];
			}
        },

        /**
         * Find all elements in the DOM mathing a particular query.
         * @param  {String} query Either a CSS or XPath query.
         * @param {Object} [scope="document"] The DOM subtree to run the query in.
         *     If not provided document is used.
         * @param  {String} [type="css"]  The type of the query. Either 'css' (default)
         *     or 'xpath' to allow XPath queries.
         * @return {Object[]|Array}       An array of HTML elements matching the query
         *     or and empty array if no elements are matching.
         */
        queryAll: function (query, scope, type) {
            jQueryFnExists(jQuery, "find");
            try {
				return queryDom.find(query, scope, type);
			} catch (err) {
				console.log(err.message);
				return [];
			}
        },

        /**
         * Loads a JavaScript file onto the current page.
         * @param  {String} url The URL of the JavaScript file to load.
         */
        loadScript: function (url) {
            jQueryFnExists(jQuery, "getScript");
            jQuery.getScript(url);
        },


        /**
         * Subscribes an event handler to be called when a particular event occurs.
         * @param  {String} eventName The name of the event to listen for.
         * @param  {Object} target    The object on which the event will fire.
         * @param  {Function} handler   The function to call when the event occurs.
         *     The browserServices passes a WebEvent object to this handler
         * @param  {Object} [delegateTarget] The delegated target on which the event will fire.
         * @param  {String} [data] The token data which will be returned as event.data when the event triggers.
         */
        subscribe: function (eventName, target, handler, delegateTarget, data) {
            var wrappedHandler = handlerMappings.add(handler);

            jQueryFnExists(jQuery({}), "on");

            if (!delegateTarget) {
                addEventListener(target, eventName, wrappedHandler);
            } else {
                jQuery(delegateTarget).on(eventName, target, data, wrappedHandler);
            }
        },

        /**
         * Unsubscribes an event handler from a particular event.
         * @param  {String} eventName The name of the event for which the handler was subscribed.
         * @param  {Object} target    The object on which the event fires.
         * @param  {Function} handler   The function to remove as an event handler.
         * @param  {Object} delegateTarget The delegated target on which the event fires.
         */
        unsubscribe: function (eventName, target, handler, delegateTarget) {
            jQueryFnExists(jQuery({}), "off");
            var wrappedHandler = handlerMappings.find(handler);
            if (wrappedHandler) {
                try {
                    if (!delegateTarget) {
                        removeEventListener(target, eventName, wrappedHandler);
                    } else {
                        jQuery(delegateTarget).off(eventName, target, wrappedHandler);
                    }
                } catch (e) {
                    core.utils.clog("Unsubscribe failed for event: " + eventName + "\n" + e.message);
                }
                handlerMappings.remove(handler);
            }
        },

        /**
         * Returns a reference to jQuery object used by the service
         * @return {Object} reference to jQuery used by the service
         */
        getJQuery: function () {
            return jQuery;
        }
    };

});
/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/*global TLT:true, window: true */

/**
 * @name ajaxService
 * @namespace
 */
TLT.addService("ajax", function (core) {
    "use strict";

    var makeAjaxCall,
        configService = core.getService("config"),
        browser = core.getService('browser'),
        jQuery,
        isInitialized = false;

    /**
     * Builds an object of key => value pairs of HTTP headers from a string.
     * @param  {String} headers The string of HTTP headers separated by newlines
     *      (i.e.: "Content-Type: text/html\nLast-Modified: ..")
     * @return {Object}         Returns an object where every key is a header
     *     and every value it's correspondending value.
     */
    function extractResponseHeaders(headers) {
        headers = headers.split('\n');
        var headersObj = {},
            i = 0,
            len = headers.length,
            header = null;
        for (i = 0; i < len; i += 1) {
            header = headers[i].split(': ');
            headersObj[header[0]] = header[1];
        }
        return headersObj;
    }

    /**
     * This function returns a function which can be passed to the jQuery ajax call
     * as callback handler.
     * It will call the sendRequest callback with the correct ajaxResponse interface.
     * @private
     * @function
     * @name browserService-wrapAjaxResponse
     * @param  {Function} complete The original callback function which should be when
     *      the request finishes.
     * @return {Function}          A function which could be passed as a callback handler to
     *      the jquery ajax handler.
     */
    function wrapAjaxResponse(complete) {
        /**
         * Calls the ajax callback function and provides the ajaxResponse in the correct format.
         * This Function gets called by the jQuery ajax callback method.
         * @private
         * @function
         * @name browserService-wrapAjaxResponse-ajaxResponseHandler
         * @param  {Object} jqXhrError   In case of an error this object would become the jqXhr object
         *      otherwise it's the parsed data.
         * @param  {String} status       The status of the ajax call as textstring.
         * @param  {Object} jqXhrSuccess In case of a successfull ajax request this object would
         *      become the jqXhr object.
         */
        return function ajaxResponseHandler(jqXhrError, status, jqXhrSuccess) {
            var jqXhr = jqXhrError,
                success = false;
            if (status === "success") {
                jqXhr = jqXhrSuccess || jqXhrError;
                success = true;
            }
            complete({
                headers: extractResponseHeaders(jqXhr.getAllResponseHeaders()),
                responseText: jqXhr.responseText,
                statusCode: jqXhr.status,
                success: success
            });
        };
    }

    /**
     * @private
     * @function
     * @name browserService-makeAjaxCall
     * @see browserService.sendRequest
     */
    makeAjaxCall = {
        /**
         * @see browserService.sendRequest
         */
        init: function (message) {
            var version = parseFloat(jQuery.fn.jquery);

            if (version <= 1.7) {
                this.init = makeAjaxCall["jQuery<=1.7"];
            } else {
                this.init = makeAjaxCall["jQuery>=1.8"];
            }
            this.init(message);
        },

        /**
         * @see browserService.sendRequest
         */
        "jQuery<=1.7": function (message) {
            message.complete = wrapAjaxResponse(message.oncomplete);
            delete message.oncomplete;
            jQuery.ajax(message);
        },

        /**
         * @see browserService.sendRequest
         */
        "jQuery>=1.8": function (message) {
            var oncomplete = wrapAjaxResponse(message.oncomplete),
                jqXhr;
            delete message.oncomplete;
            jqXhr = jQuery.ajax(message.url, message);
            browser.jQueryFnExists(jqXhr, "always");
            jqXhr.always(oncomplete);
		}
	};

	function initAjaxService(config) {
		// find jQuery object
		if (config.hasOwnProperty("jQueryObject")) {
			jQuery = core.utils.access(config.jQueryObject);
		} else {
			jQuery = window.jQuery;
		}

		isInitialized = true;
	}

	return {
		init: function () {
			if (!isInitialized) {
				initAjaxService(configService.getServiceConfig("browser") || {});
			} else {
				core.utils.clog("Attempt to initialize service which has been already initialized(ajaxService.jQuery)");
			}
		},

		/**
         * Destroys service state
         */
        destroy: function () {
            isInitialized = false;
        },

		/**
         * Makes an Ajax request to the server.
         * @param {Object} message An AjaxRequest object containing all the information
         *     neccessary for making the request.
         * @param {String} [message.contentType] Set to a string to override the default
         *     content type of the request.
         * @param {String} [message.data] A string containing data to POST to the server.
         * @param {Object} [message.headers] An object whose properties represent HTTP headers.
         * @param {Function} message.oncomplete A callback function to call when the
         *     request has completed.
         * @param {Number} [message.timeout] The number of milliseconds to wait
         *     for a response before closing the Ajax request.
         * @param {String} [message.type="POST"] Either 'GET' or 'POST',
         *     indicating the type of the request to make.
         * @param {String} message.url The URL to send the request to.
         *     This should contain any required query string parameters.
         */
        sendRequest: function (message) {
            browser.jQueryFnExists(jQuery, "ajax");
            message.type = message.type || "POST";
            makeAjaxCall.init(message);
        }
    };
});
/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview The MessageService creates messages in the correct format to be transmitted to the server.
 * @exports messageService
 */

/*global TLT:true */

/**
 * @name messageService
 * @namespace
 */
TLT.addService("message", function (core) {
    "use strict";

    var screenviewOffsetTime = null,
        count             = 0,
        messageCount      = 0,
        sessionStart      = new Date(),
        tlStartLoad       = new Date(),
        browserBaseService = core.getService("browserBase"),
        browserService    = core.getService("browser"),
        configService     = core.getService("config"),
        config            = configService.getServiceConfig("message") || {},
        windowHref        = window.location.href,
        windowId          = "TODO",
        pageId            = "ID" + tlStartLoad.getHours() + "H" +
                            tlStartLoad.getMinutes() + "M" +
                            tlStartLoad.getSeconds() + "S" +
                            tlStartLoad.getMilliseconds() + "R" +
                            Math.random(),
        privacy           = config.hasOwnProperty("privacy") ? config.privacy : [],
        privacyMasks      = {},
        maskingCharacters = {
            lower: "x",
            upper: "X",
            numeric: "9",
            symbol: "@"
        },

        //TODO move these to a global section due to they might be used elsewhere
        isApple = navigator.userAgent.indexOf("iPhone") > -1 || navigator.userAgent.indexOf("iPod") > -1 || navigator.userAgent.indexOf("iPad") > -1,
        isAndroidChrome = navigator.userAgent.indexOf("Chrome") > -1 && navigator.userAgent.indexOf("Android") > -1,
        devicePixelRatio = window.devicePixelRatio || 1,
        deviceOriginalWidth = window.screen ? window.screen.width : 0,
        deviceOriginalHeight = window.screen ? window.screen.height : 0,
        deviceOrientation = window.orientation || 0,
        deviceWidth = isApple || isAndroidChrome ? deviceOriginalWidth : deviceOriginalWidth <= 320 ? deviceOriginalWidth : deviceOriginalWidth / devicePixelRatio,
        deviceHeight = isApple || isAndroidChrome ? deviceOriginalHeight : deviceOriginalWidth <= 320 ? deviceOriginalHeight : deviceOriginalHeight / devicePixelRatio,
        deviceToolbarHeight = (window.screen ? window.screen.height - window.screen.availHeight : 0),
        startWidth = window.innerWidth || document.documentElement.clientWidth,
        startHeight = window.innerHeight || document.documentElement.clientHeight,
        isInitialized = false;


    /**
     * Base structure for a message object.
     * @constructor
     * @private
     * @name messageService-Message
     * @param {Object} event The QueueEvent to transform into a message object.
     */
    function Message(event) {
        var key = '';

        /**
         * The message type.
         * @type {Number}
         * @see browserService-Message.TYPES
         */
        this.type          = event.type;
        /**
         * The offset from the beginning of the session.
         * @type {Number}
         */
        this.offset        = (new Date()).getTime() - sessionStart.getTime();
        /**
         * The offset from the most recent application context message.
         * @type {Number}
         */
        if ((event.type === 2) || (screenviewOffsetTime === null)) {
            screenviewOffsetTime = new Date();
        }
        this.screenviewOffset = (new Date()).getTime() - screenviewOffsetTime.getTime();

        /**
         * The count of the overall messages until now.
         * @type {Number}
         */
        this.count         = (messageCount += 1);

        /**
         * To indicate that user action came from the web.
         * @type {Boolean}
         */
        this.fromWeb       = true;

        // iterate over the properties in the queueEvent and add all the objects to the message.
        for (key in event) {
            if (event.hasOwnProperty(key)) {
                this[key] = event[key];
            }
        }
    }

    /**
     * Empty filter. Returns an empty string which would be used as value.
     * @param  {String} value The value of the input/control.
     * @return {String}       Returns an empty string.
     */
    privacyMasks.PVC_MASK_EMPTY = function (value) {
        return "";
    };

    /**
     * Basic filter. Returns a predefined string for every value.
     * @param  {String} value The value of the input/control.
     * @return {String}       Returns a predefined mask/string.
     */
    privacyMasks.PVC_MASK_BASIC = function (value) {
        var retMask = "XXXXX";

        // Sanity check
        if (typeof value !== "string") {
            return "";
        }
        return (value.length ? retMask : "");
    };

    /**
     * Type filter. Returns predefined values for uppercase/lowercase
     *                         and numeric values.
     * @param  {String} value The value of the input/control.
     * @return {String}       Returns a string/mask which uses predefined
     *                        characters to mask the value.
     */
    privacyMasks.PVC_MASK_TYPE = function (value) {
        var characters,
            i = 0,
            len = 0,
            retMask = "";

        // Sanity check
        if (typeof value !== "string") {
            return retMask;
        }

        characters = value.split("");

        for (i = 0, len = characters.length; i < len; i += 1) {
            if (core.utils.isNumeric(characters[i])) {
                retMask += maskingCharacters.numeric;
            } else if (core.utils.isUpperCase(characters[i])) {
                retMask += maskingCharacters.upper;
            } else if (core.utils.isLowerCase(characters[i])) {
                retMask += maskingCharacters.lower;
            } else {
                retMask += maskingCharacters.symbol;
            }
        }
        return retMask;
    };

    privacyMasks.PVC_MASK_EMPTY.maskType = 1; // reported value is empty string.
    privacyMasks.PVC_MASK_BASIC.maskType = 2; // reported value is fixed string "XXXXX".
    privacyMasks.PVC_MASK_TYPE.maskType = 3;  // reported value is a mask according to character type
                                              // as per configuration, e.g. "HelloWorld123" becomes "XxxxxXxxxx999".
    privacyMasks.PVC_MASK_CUSTOM = {
        maskType: 4 // reported value is return value of custom function provided by config.
    };

    /**
     * Checks which mask should be used to replace the value and applies
     * it on the message object. By default, if an invalid mask is specified,
     * the BASIC mask will be applied.
     * @param  {Object} mask    The privacy object.
     * @param  {Object} message The entire message object.
     */
    function applyMask(mask, message) {
        var filter = privacyMasks.PVC_MASK_BASIC;
        if (mask.maskType === privacyMasks.PVC_MASK_EMPTY.maskType) {
            filter = privacyMasks.PVC_MASK_EMPTY;
        } else if (mask.maskType === privacyMasks.PVC_MASK_BASIC.maskType) {
            filter = privacyMasks.PVC_MASK_BASIC;
        } else if (mask.maskType === privacyMasks.PVC_MASK_TYPE.maskType) {
            filter = privacyMasks.PVC_MASK_TYPE;
        } else if (mask.maskType === privacyMasks.PVC_MASK_CUSTOM.maskType) {
            if (typeof mask.maskFunction === "string") {
                filter = core.utils.access(mask.maskFunction);
            } else {
                filter = mask.maskFunction;
            }
            if (typeof filter !== "function") {
                // Reset to default
                filter = privacyMasks.PVC_MASK_BASIC;
            }
        }
        if (typeof message.target.prevState !== "undefined" && message.target.prevState.hasOwnProperty("value")) {
            message.target.prevState.value = filter(message.target.prevState.value);
        }
        if (typeof message.target.currState !== "undefined" && message.target.currState.hasOwnProperty("value")) {
            message.target.currState.value = filter(message.target.currState.value);
        }
    }

    /**
     * Checks whether one of the privacy targets matches the target
     *                          of the current mesage.
     * TODO: There are several places in the library where the same type
     * of matching result is required based on id or selector. This should
     * be consolidated into a single helper function.
     * @param  {Array} targets An array of objects as defined in the
     *                         privacy configuration.
     * @param  {Object} target  The target object of the message.
     * @return {Boolean}         Returns true if one of the targets match.
     *                           Otherwise false.
     */
    function matchesTarget(targets, target) {
        var i,
            j,
            qr,
            qrLen,
            qrTarget,
            regex,
            len,
            tmpTarget;

        for (i = 0, len = targets.length; i < len; i += 1) {
            tmpTarget = targets[i];

            // Check if target in config is a selector string.
            if (typeof tmpTarget === "string") {
                qr = browserService.queryAll(tmpTarget);
                for (j = 0, qrLen = qr ? qr.length : 0; j < qrLen; j += 1) {
                    if (qr[j]) {
                        qrTarget = browserBaseService.ElementData.prototype.examineID(qr[j]);
                        if (qrTarget.type === target.idType && qrTarget.id === target.id) {
                            return true;
                        }
                    }
                }
            } else if (tmpTarget.id && tmpTarget.idType && target.idType.toString() === tmpTarget.idType.toString()) {
                // Note: idType provided by wizard is a string so convert both to strings before comparing.

                // An id in the configuration could be a direct match, in which case it will be a string OR
                // it could be a regular expression in which case it would be an object like this:
                // {regex: ".+private$", flags: "i"}
                switch (typeof tmpTarget.id) {
                case "string":
                    if (tmpTarget.id === target.id) {
                        return true;
                    }
                    break;
                case "object":
                    regex = new RegExp(tmpTarget.id.regex, tmpTarget.id.flags);
                    if (regex.test(target.id)) {
                        return true;
                    }
                    break;
                }
            }
        }
        return false;
    }

    /**
     * Runs through all privacy configurations and checks if it matches
     * the current message object.
     * @param  {Object} message The message object.
     * @return {Object}         The message, either with replaced values
     *                          if a target of the privacy configuration
     *                          matched or the original message if the
     *                          configuration didn't match.
     */
    function privacyFilter(message) {
        var i,
            len,
            mask;

        if (!message || !message.hasOwnProperty("target")) {
            return message;
        }

        for (i = 0, len = privacy.length; i < len; i += 1) {
            mask = privacy[i];
            if (matchesTarget(mask.targets, message.target)) {
                applyMask(mask, message);
                break;
            }
        }
        return message;
    }

    /**
     * Gets called when the configserver fires configupdated event.
     */
    function updateConfig() {
        configService = core.getService("config");
        config = configService.getServiceConfig("message") || {};
        privacy = config.hasOwnProperty("privacy") ? config.privacy : [];
    }

    function initMessageService() {
        if (configService.subscribe) {
            configService.subscribe("configupdated", updateConfig);
        }

        isInitialized = true;
    }

    function destroy() {
        configService.unsubscribe("configupdated", updateConfig);

        isInitialized = false;
    }


    /**
     * @scope messageService
     */
    return {
        privacyMasks: privacyMasks,
        applyMask: applyMask,
        matchesTarget: matchesTarget,
        privacyFilter: privacyFilter,
        updateConfig: updateConfig,

        init: function () {
            if (!isInitialized) {
                initMessageService();
            } else {
                core.utils.clog("Attempt to initialize service which has been already initialized(messageService)");
            }
        },

        destroy: function () {
            destroy();
        },

        /**
         * Accepts a simple queue event  and wraps it into a complete message that the server can understand.
         * @param  {Object} event The simple event information
         * @return {Object}       A complete message that is ready for transmission to the server.
         */
        createMessage: function (event) {
            if (typeof event.type === "undefined") {
                throw new TypeError("Invalid queueEvent given!");
            }
            return privacyFilter(new Message(event));
        },

        /**
         * Mock function to create a JSON structure around messages before sending to server.
         * @param  {Array} messages An array of messages
         * @return {Object}          Returns a JavaScript object which can be serialized to JSON
         *      and send to the server.
         *  @todo rewrite functionality
         */
        wrapMessages: function (messages) {
            var messagePackage = {
                messageVersion: "2.2.0.0",
                serialNumber: (count += 1),
                sessions: [{
                    id: pageId,
                    startTime: tlStartLoad.getTime(),
                    timezoneOffset: tlStartLoad.getTimezoneOffset(),
                    messages: messages,
                    clientEnvironment: {
                        webEnvironment: {
                            libVersion: "3.1.0.1520",
                            page: windowHref,
                            windowId: windowId,
                            screen: {
                                devicePixelRatio: devicePixelRatio,
                                deviceOriginalWidth: isApple || isAndroidChrome ? deviceOriginalWidth * devicePixelRatio : deviceOriginalWidth,
                                deviceOriginalHeight: isApple || isAndroidChrome ? deviceOriginalHeight * devicePixelRatio : deviceOriginalHeight,
                                deviceWidth: deviceWidth,
                                deviceHeight: deviceHeight,
                                deviceToolbarHeight: deviceToolbarHeight,
                                width: startWidth,
                                height: startHeight,
                                orientation: deviceOrientation
                            }
                        }
                    }
                }]
            },
                webEnvScreen = messagePackage.sessions[0].clientEnvironment.webEnvironment.screen;

            webEnvScreen.orientationMode = core.utils.getOrientationMode(webEnvScreen.orientation);
            /*
            if (true) { // Add usability to config settings
                //messagePackage.domainId = "<<TODO domainId>>"; This was used to send to correct posting url, no longer needed. Followup with Chris. Checked with Joe.
                //messagePackage.samplingRate = "<<TODO samplingRate>>"; This is no longer needed. We will not focus on sampling for this release of 8.6.
            }
            */
            return messagePackage;
        }
    };

});
/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview The SerializerService provides the ability to serialize
 * data into one or more string formats.
 * @exports serializerService
 */

/*global TLT:true, window: true */
/*global console: false */

/**
 * @name serializerService
 * @namespace
 */
TLT.addService("serializer", function (core) {
    "use strict";

    /**
     * JSON serializer. If possible it uses JSON.stringify method, but
     * for older browsers it provides minimalistic implementaction of
     * custom serializer (limitations: does not detect circular
     * dependencies, does not serialize date objects and does not
     * validate names of object fields).
     * @private
     * @function
     * @name serializerService-serializeToJSON
     * @param {Any} obj - any value
     * @returns {string} serialized string
     */
    function serializeToJSON(obj) {
        var str,
            key,
            len = 0;
        if (typeof obj !== "object" || obj === null) {
            switch (typeof obj) {
            case "function":
            case "undefined":
                return "null";
            case "string":
                return '"' + obj.replace(/\"/g, '\\"') + '"';
            default:
                return String(obj);
            }
        } else if (Object.prototype.toString.call(obj) === "[object Array]") {
            str = "[";
            for (key = 0, len = obj.length; key < len; key += 1) {
                if (Object.prototype.hasOwnProperty.call(obj, key)) {
                    str += serializeToJSON(obj[key]) + ",";
                }
            }
        } else {
            str = "{";
            for (key in obj) {
                if (Object.prototype.hasOwnProperty.call(obj, key)) {
                    str = str.concat('"', key, '":', serializeToJSON(obj[key]), ",");
                    len += 1;
                }
            }
        }
        if (len > 0) {
            str = str.substring(0, str.length - 1);
        }
        str += String.fromCharCode(str.charCodeAt(0) + 2);
        return str;
    }


    /**
     * Serializer / Parser implementations
     * @type {Object}
     */
    var configService = core.getService("config"),
        serialize = {},
        parse = {},
        defaultSerializers = {
            json: (function () {
                if (typeof window.JSON !== "undefined") {
                    return {
                        serialize: window.JSON.stringify,
                        parse: window.JSON.parse
                    };
                }

                return {
                    serialize: serializeToJSON,
                    // TODO: find a better way than using eval
                    parse: function (data) {
                        return eval("(" + data + ")");
                    }
                };
            }())
        },
        updateConfig = null,
        isInitialized = false;

    function addObjectIfExist(paths, rootObj, propertyName) {
        var i,
            len,
            obj;

        paths = paths || [];
        for (i = 0, len = paths.length; i < len; i += 1) {
            obj = paths[i];
            if (typeof obj === "string") {
                obj = core.utils.access(obj);
            }
            if (typeof obj === "function") {
                rootObj[propertyName] = obj;
                break;
            }
        }
    }
	function checkParserAndSerializer() {
		var isParserAndSerializerInvalid;
        if (typeof serialize.json !== "function" || typeof parse.json !== "function") {
			isParserAndSerializerInvalid = true;
        } else {
			if (typeof parse.json('{"foo": "bar"}') === "undefined") {
				isParserAndSerializerInvalid = true;
			} else {
				isParserAndSerializerInvalid = parse.json('{"foo": "bar"}').foo !== "bar";
			}
			if (typeof parse.json("[1, 2]") === "undefined") {
				isParserAndSerializerInvalid = true;
			} else {
				isParserAndSerializerInvalid = isParserAndSerializerInvalid || parse.json("[1, 2]")[0] !== 1;
				isParserAndSerializerInvalid = isParserAndSerializerInvalid || parse.json("[1,2]")[1] !== 2;
			}
			isParserAndSerializerInvalid = isParserAndSerializerInvalid || serialize.json({"foo": "bar"}) !== '{"foo":"bar"}';
			isParserAndSerializerInvalid = isParserAndSerializerInvalid || serialize.json([1, 2]) !== "[1,2]";
		}
		return isParserAndSerializerInvalid;
	}
    function initSerializerService(config) {
        var format;
        for (format in config) {
            if (config.hasOwnProperty(format)) {
                addObjectIfExist(config[format].stringifiers, serialize, format);
                addObjectIfExist(config[format].parsers, parse, format);
            }
        }

        // use default JSON parser/serializer if possible
        if (!(config.json && config.json.hasOwnProperty("defaultToBuiltin")) || config.json.defaultToBuiltin === true) {
            serialize.json = serialize.json || defaultSerializers.json.serialize;
            parse.json = parse.json || defaultSerializers.json.parse;
        }

        //sanity check
        if (typeof serialize.json !== "function" || typeof parse.json !== "function") {
            core.fail("JSON parser and/or serializer not provided in the UIC config. Can't continue.");
        }
		if (checkParserAndSerializer()) {
			if (typeof serialize.json !== "function" && typeof parse.json !== "function") {
				console.log("parse.json() and serialize.json() are not a functions.");
			} else if (typeof serialize.json !== "function") {
				console.log("serialize.json() is not a function.");
			} else if (typeof parse.json !== "function") {
				console.log("parse.json() is not a function.");
			} else {
				if (typeof parse.json('{"foo": "bar"}') === "undefined") {
					console.log("parse.json('{'foo': 'bar'}') is undefined");
				} else if (parse.json('{"foo":"bar"}').foo !== "bar") {
					console.log('Parsing of JSON object is failing.');
				}
				if (typeof parse.json("[1, 2]") === "undefined") {
					console.log("parse.json('[1, 2]') is undefined");
				} else if (parse.json("[1,2]")[0] !== 1 || parse.json("[1,2]")[1] !== 2) {
					console.log('Parsing of JSON array is failing.');
				}
				if (serialize.json({"foo": "bar"}) !== '{"foo":"bar"}') {
					console.log('Stringification of JSON object is failing.');
				}
				if (serialize.json([1, 2]) !== "[1,2]") {
					console.log('Stringification of JSON array is failing.');
				}
			}
			core.fail("JSON stringification and parsing are not working as expected");
		}
        if (configService.subscribe) {
            configService.subscribe("configupdated", updateConfig);
        }

        isInitialized = true;
    }


    function destroy() {
        serialize = {};
        parse = {};

        configService.unsubscribe("configupdated", updateConfig);

        isInitialized = false;
    }

    updateConfig = function () {
        configService = core.getService("config");
        // TODO: reinit only if config changed. Verify initSerializerService is idempotent
        initSerializerService(configService.getServiceConfig("serializer") || {});
    };

    /**
     * @scope serializerService
     */
    return {
        // Expose private functions for unit testing
        updateConfig: updateConfig,
        init: function () {
            if (!isInitialized) {
                initSerializerService(configService.getServiceConfig("serializer") || {});
            } else {
                core.utils.clog("Attempt to initialize service which has been already initialized(serializerService)");
            }
        },

        destroy: function () {
            destroy();
        },

        /**
         * Parses a string into a JavaScript object.
         * @param  {String} data The string to parse.
         * @param  {String} [type="json"] The format of the data.
         * @return {Object}      An object representing the string data.
         */
        parse: function (data, type) {
            type = type || "json";
            if (typeof parse[type] !== "function") {
                core.utils.clog("Unsupported type of data in parse method of serializer service: " + type);
            }
            return parse[type](data);
        },

        /**
         * Serializes object data into a string using the format specified.
         * @param  {Object} data The data to serialize.
         * @param  {String} [type="json"] The format to serialize the data into.
         * @return {String}      A string containing the serialization of the data.
         */
        serialize: function (data, type) {
            type = type || "json";
            if (typeof serialize[type] !== "function") {
                core.utils.clog("Unsupported type of data in serializer method of serializer service: " + type);
            }
            return serialize[type](data);
        }
    };

});
/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview The Overstat module implements the logic for collecting
 * data for cxOverstat. The current uses are for the Hover Event and
 * Hover To Click event.
 * @exports overstat
 */

/*global TLT:true */
/*global console:true */

// Sanity check
if (TLT && typeof TLT.addModule === "function") {
    /**
     * @name overstat
     * @namespace
     */
    TLT.addModule("overstat", function (context) {
        "use strict";

        var tlTypes = {
            "input:radio": "radioButton",
            "input:checkbox": "checkBox",
            "input:text": "textBox",
            "input:password": "textBox",
            "input:file": "fileInput",
            "input:button": "button",
            "input:submit": "submitButton",
            "input:reset": "resetButton",
            "input:image": "image",
            "input:color": "color",
            "input:date": "date",
            "input:datetime": "datetime",
            "input:datetime-local": "datetime-local",
            "input:number": "number",
            "input:email": "email",
            "input:tel": "tel",
            "input:search": "search",
            "input:url": "url",
            "input:time": "time",
            "input:week": "week",
            "input:month": "month",
            "textarea:": "textBox",
            "select:": "selectList",
            "button:": "button",
            "a:": "link"
        },

            eventMap = {},
            configDefaults = { "UPDATE_INTERVAL" : 250,
                                "HOVER_THRESHOLD_MIN" : 1000,
                                "HOVER_THRESHOLD_MAX" : 2 * 60 * 1000,
                                "GRIDCELL_MAX_X" : 10,
                                "GRIDCELL_MAX_Y" : 10,
                                "GRIDCELL_MIN_WIDTH" : 20,
                                "GRIDCELL_MIN_HEIGHT" : 20
                };

        /**
         * Used to test and get value from an object.
         * @private
         * @function
         * @name replay-getValue
         * @param {object} parentObj An object you want to get a value from.
         * @param {string} propertyAsStr A string that represents dot notation to get a value from object.
         * @return {object} If object is found, if not then null will be returned.
         */
        function getValue(parentObj, propertyAsStr) {
            var i,
                properties;

            // Sanity check
            if (!parentObj || typeof parentObj !== "object") {
                return null;
            }

            properties = propertyAsStr.split(".");
            for (i = 0; i < properties.length; i += 1) {
                if ((typeof parentObj === "undefined") || (parentObj[properties[i]] === null)) {
                    return null;
                }
                parentObj = parentObj[properties[i]];
            }
            return parentObj;
        }

        function getConfigValue(key) {
            var overstatConfig = context.getConfig() || {},
                value = overstatConfig[key];
            return typeof value === "number" ? value : configDefaults[key];
        }

        function postUIEvent(hoverEvent, options) {
            var tagName = getValue(hoverEvent, "webEvent.target.element.tagName") || "",
                type = tagName.toLowerCase() === "input" ? getValue(hoverEvent, "webEvent.target.element.type") : "",
                tlType = tlTypes[tagName.toLowerCase() + ":" + type] || tagName,

                uiEvent = {
                    type: 9,
                    event: {
                        hoverDuration: hoverEvent.hoverDuration,
                        hoverToClick: getValue(options, "hoverToClick")
                    },
                    target: {
                        id: getValue(hoverEvent, "webEvent.target.id") || "",
                        idType: getValue(hoverEvent, "webEvent.target.idType") || "",
                        name: getValue(hoverEvent, "webEvent.target.name") || "",
                        tlType: tlType,
                        type: tagName,
                        subType: type,
                        position: {
                            width: getValue(hoverEvent, "webEvent.target.element.offsetWidth") || 0,
                            height: getValue(hoverEvent, "webEvent.target.element.offsetHeight") || 0,
                            relXY: hoverEvent.gridX + "," + hoverEvent.gridY
                        }
                    }
                };

                // if id is null or empty, what are we firing on? it can't be replayed anyway
            if ((typeof uiEvent.target.id) === undefined || uiEvent.target.id === "") {
                return;
            }

            console.log("Overstat - posted hover event");
            console.log(uiEvent);
            context.post(uiEvent);
        }

        function stopNode(node) {
            if (node && node.element) { node = node.element; }
            return !node || node === document.body || node === document.html || node === document;
        }

        function getParent(node) {
            if (!node) { return null; }
            return node.element ? node.element.parentNode : node.parentNode;
        }

        function getOffsetParent(node) {
            if (!node) { return null; }
            var parent = node.element ? node.element.offsetParent : node.offsetParent;
            return parent || getParent(node);
        }

        /*
         * for when mouseout is called - if you have moved over a child element, mouseout is fired for the parent element
         * @private
         * @function
         * @name overstat-isChildOf
         * @return {boolean} Returns whether node is a child of root
         */
        function isChildOf(root, node) {
            if (!node || node === root) { return false; }
            node = getParent(node);

            while (!stopNode(node)) {
                if (node === root) { return true; }
                node = getParent(node);
            }

            return false;
        }

        function getNativeEvent(e) {
            if (e.nativeEvent) { e = e.nativeEvent; }
            return e;
        }

        function getNativeTarget(e) {
            return getNativeEvent(e).target;
        }

        function getNativeNode(node) {
            if (!node) { return null; }
            return node.element || node;
        }

        function getNodeType(node) {
            if (!node) { return -1; }
            if (node.element) { node = node.element; }
            return node.nodeType || -1;
        }

        function getNodeTagName(node) {
            if (!node) { return ""; }
            if (node.element) { node = node.element; }
            return node.tagName ? node.tagName.toUpperCase() : "";
        }

        function getNodeElement(node) {
            if (node && node.element) { node = node.element; }
            return node;
        }

        function stopEventPropagation(e) {
            if (!e) { return; }
            if (e.nativeEvent) { e = e.nativeEvent; }

            if (e.stopPropagation) {
                e.stopPropagation();
            } else if (e.cancelBubble) {
                e.cancelBubble();
            }
        }

        function ignoreNode(node) {
            var tagName = getNodeTagName(node);
            return getNodeType(node) !== 1 || tagName === "TR" || tagName === "TBODY" || tagName === "THEAD";
        }

        /**
         * Generates an XPath for a given node, stub method until the real one is available
         * @function
         */
        function getXPathFromNode(node) {
            if (!node) { return ""; }
            if (node.xPath) { return node.xPath; }
            node = getNativeNode(node);
            return context.getXPathFromNode(node);
        }

        /*
         * replacement for lang.hitch(), setTimeout loses all scope
         * @private
         * @function
         * @name overstat-callHoverEventMethod
         * @return {object} Returns the value of the called method
         */
        function callHoverEventMethod(key, methodName) {
            var hEvent = eventMap[key];
            if (hEvent && hEvent[methodName]) { return hEvent[methodName](); }
        }

        function HoverEvent(dm, gx, gy, webEvent) {
            this.xPath = dm !== null ? getXPathFromNode(dm) : "";
            this.domNode = dm;
            this.hoverDuration = 0;
            this.hoverUpdateTime = 0;
            this.gridX = Math.max(gx, 0);
            this.gridY = Math.max(gy, 0);
            this.parentKey = "";
            this.updateTimer = -1;
            this.disposed = false;
            this.childKeys = {};
            this.webEvent = webEvent;

            /*
             * @public
             * @function
             * @name overstat-HoverEvent.getKey
             * @return {string} Returns the string unique key of this event
             */
            this.getKey = function () {
                return this.xPath + ":" + this.gridX + "," + this.gridY;
            };

            /*
             * update hoverTime, set timer to update again
             * @public
             * @function
             * @name overstat-HoverEvent.update
             */
            this.update = function () {
                var curTime = new Date().getTime(),
                    key = this.getKey();

                if (this.hoverUpdateTime !== 0) {
                    this.hoverDuration += curTime - this.hoverUpdateTime;
                }

                this.hoverUpdateTime = curTime;

                clearTimeout(this.updateTimer);
                this.updateTimer = setTimeout(function () { callHoverEventMethod(key, "update"); }, getConfigValue("UPDATE_INTERVAL"));
            };

            /*
             * leaveClone is true if you want to get rid of an event but leave a new one in it's place.
             * usually this will happen due to a click, where the hover ends, but you want a new hover to
             * begin in the same place
             * @public
             * @function
             * @name overstat-HoverEvent.dispose
             */
            this.dispose = function (leaveClone) {
                clearTimeout(this.updateTimer);
                delete eventMap[this.getKey()];
                this.disposed = true;

                if (leaveClone) {
                    var cloneEvt = this.clone();
                    eventMap[cloneEvt.getKey()] = cloneEvt;
                    cloneEvt.update();
                }
            };

            /*
             * clear update timer, add to hover events queue if threshold is reached, dispose in any case
             * @public
             * @function
             * @name overstat-HoverEvent.process
             * @return {boolean} Returns whether or not the event met the threshold requirements and was added to the queue
             */
            this.process = function (wasClicked) {
                clearTimeout(this.updateTimer);
                if (this.disposed) { return false; }

                var addedToQueue = false,
                    hEvent = this,
                    key = null;
                if (this.hoverDuration >= getConfigValue("HOVER_THRESHOLD_MIN")) {
                    this.hoverDuration = Math.min(this.hoverDuration, getConfigValue("HOVER_THRESHOLD_MAX"));
                    // add to ui event queue here
                    addedToQueue = true;
                    postUIEvent(this, { hoverToClick : !!wasClicked });

                    while (typeof hEvent !== "undefined") {
                        hEvent.dispose(wasClicked);
                        hEvent = eventMap[hEvent.parentKey];
                    }
                } else {
                    this.dispose(wasClicked);
                }

                return addedToQueue;
            };

            /*
             * return a fresh copy of this event
             * @public
             * @function
             * @name overstat-HoverEvent.clone
             * @return {HoverTest} Returns a copy of this event with a reset hover time
             */
            this.clone = function () {
                var cloneEvent = new HoverEvent(this.domNode, this.gridX, this.gridY);
                cloneEvent.parentKey = this.parentKey;

                return cloneEvent;
            };
        }

        function createHoverEvent(node, x, y) {
            return new HoverEvent(node, x, y);
        }

        /*
         * get element offset according to the top left of the document
         * @private
         * @function
         * @name overstat-calculateNodeOffset
         * @return {object} Returns an object with x and y offsets
         */
        function calculateNodeOffset(node) {
            if (node && node.position) { return { x: node.position.x, y: node.position.y }; }
            node = getNodeElement(node);
            var offsetX = node.offsetLeft,
                offsetY = node.offsetTop,
                lastOffsetX = offsetX,
                lastOffsetY = offsetY,
                offsetDiffX = 0,
                offsetDiffY = 0,
                curNode = getOffsetParent(node);

            while (curNode) {
                if (stopNode(curNode)) { break; }

                offsetDiffX = curNode.offsetLeft - (curNode.scrollLeft || 0);
                offsetDiffY = curNode.offsetTop - (curNode.scrollTop || 0);

                if (offsetDiffX !== lastOffsetX || offsetDiffY !== lastOffsetY) {
                    offsetX += offsetDiffX;
                    offsetY += offsetDiffY;

                    lastOffsetX = offsetDiffX;
                    lastOffsetY = offsetDiffY;
                }

                curNode = getOffsetParent(curNode);
            }

            if (isNaN(offsetX)) { offsetX = 0; }
            if (isNaN(offsetY)) { offsetY = 0; }
            return { x: offsetX, y: offsetY };
        }

        /*
         * calculate position relative to top left corner of element
         * @private
         * @function
         * @name overstat-calculateRelativeCursorPos
         * @return {object} Returns an object with x and y offsets
         */
        function calculateRelativeCursorPos(node, cursorX, cursorY) {
            node = getNodeElement(node);
            var nodeOffset = calculateNodeOffset(node),
                offsetX = cursorX - nodeOffset.x,
                offsetY = cursorY - nodeOffset.y;

            if (!isFinite(offsetX)) { offsetX = 0; }
            if (!isFinite(offsetY)) { offsetY = 0; }
            return { x: offsetX, y: offsetY };
        }

        /*
         * determine grid cell dimensions based on the constants
         * @private
         * @function
         * @name overstat-calculateGridCell
         * @return {object} Returns the x and y grid location
         */
        function calculateGridCell(node, offsetX, offsetY) {
            node = getNodeElement(node);
            var cellWidth = node.offsetWidth > 0 ? Math.max(node.offsetWidth / getConfigValue("GRIDCELL_MAX_X"), getConfigValue("GRIDCELL_MIN_WIDTH")) : getConfigValue("GRIDCELL_MIN_WIDTH"),
                cellHeight = node.offsetHeight > 0 ? Math.max(node.offsetHeight / getConfigValue("GRIDCELL_MAX_X"), getConfigValue("GRIDCELL_MIN_HEIGHT")) : getConfigValue("GRIDCELL_MIN_HEIGHT"),

                cellX = Math.floor(offsetX / cellWidth),
                cellY = Math.floor(offsetY / cellHeight);

            if (!isFinite(cellX)) { cellX = 0; }
            if (!isFinite(cellY)) { cellY = 0; }
            return { x: cellX, y: cellY };
        }

        /*
         * called when a hover event fires - processes all unrelated hover events from the queue.
         * events are related if they are the calling event, or any parent events
         * @private
         * @function
         * @name overstat-cleanupHoverEvents
         */
        function cleanupHoverEvents(curEvent) {
            var hEvent = curEvent,
                curKey = curEvent.getKey(),
                allowedKeyMap = {},
                key = null,
                childKey = null;

            allowedKeyMap[curKey] = true;

            while (typeof hEvent !== "undefined") {
                allowedKeyMap[hEvent.parentKey] = true;
                if (hEvent.parentKey === "" || hEvent.parentKey === hEvent.getKey()) {
                    break;
                }

                hEvent = eventMap[hEvent.parentKey];
            }

            for (key in eventMap) {
                if (eventMap.hasOwnProperty(key) && !allowedKeyMap[key]) {
                    hEvent = eventMap[key];
                    if (hEvent) {
                        hEvent.process();
                    }
                }
            }
        }

        /*
         * similar to cleanupHoverEvents, this will process all events within a domNode (fired on mouseout)
         * @private
         * @function
         * @name overstat-processEventsByDomNode
         */
        function processEventsByDomNode(eventNode, keyToIgnore) {
            var hEvent = null,
                key = null;
            for (key in eventMap) {
                if (eventMap.hasOwnProperty(key)) {
                    hEvent = eventMap[key];
                    if (hEvent.domNode === eventNode && hEvent.getKey() !== keyToIgnore) {
                        hEvent.process();
                    }
                }
            }
        }

        /*
         * 1) determine element and grid position for event
         * 2) find existing matching event if possible
         * 3) update event hover time
         * 4) bubble to parent node, for linking purposes
         * within the UI SDK framework, this should be called for each node in the heirarchy (box model)
         * going top down. so the parent (if the calculation is correct) should already exist, and have
         * it's own parent link, which helps during cleanupHoverEvents
         * @private
         * @function
         * @name overstat-hoverHandler
         * @return {HoverEvent} Returns the relevant HoverEvent object (either found or created)
         */
        function hoverHandler(e, node, isParent) {
            if (!node) { node = e.target; }
            if (stopNode(node)) { return null; }

            var rPos, gPos, hEvent, key, parentKey, parentEvent, offsetParent;

            if (!ignoreNode(node)) {
                rPos = calculateRelativeCursorPos(node, e.position.x, e.position.y);
                gPos = calculateGridCell(node, rPos.x, rPos.y);
                hEvent = new HoverEvent(node, gPos.x, gPos.y, e);
                key = hEvent.getKey();

                if (eventMap[key]) {
                    hEvent = eventMap[key];
                } else {
                    eventMap[key] = hEvent;
                }

                hEvent.update();

                // link parent, but in the case that it refers to itself (sometimes with frames) make sure the parentKey
                // is not the same as the current key
                if (!isParent) {
                    offsetParent = getOffsetParent(node);
                    if (offsetParent) {
                        parentEvent = hoverHandler(e, offsetParent, true);
                        if (parentEvent !== null) {
                            parentKey = parentEvent.getKey();
                            key = hEvent.getKey();
                            if (key !== parentKey) {
                                hEvent.parentKey = parentKey;
                            }
                        }
                    }

                    cleanupHoverEvents(hEvent);
                }
            } else {
                hEvent = hoverHandler(e, getOffsetParent(node), isParent);
            }

            return hEvent;
        }

        /*
         * process all events related to the event target, as hovering stops when leaving the element
         * @private
         * @function
         * @name overstat-leaveHandler
         */
        function leaveHandler(e) {
            e = getNativeEvent(e);
            if (isChildOf(e.target, e.relatedTarget)) {
                return;
            }

            processEventsByDomNode(e.target);
        }

        /*
         * on click, resolve current hover events, and reset hover count
         * @private
         * @function
         * @name overstat-clickHandler
         */
        function clickHandler(e) {
            var hEvent = null, key;
            for (key in eventMap) {
                if (eventMap.hasOwnProperty(key)) {
                    hEvent = eventMap[key];
                    hEvent.process(true);
                }
            }
        }

        /*
         * switches on window event type and routes it appropriately
         * @private
         * @function
         * @name overstat-handleEvent
         */
        function handleEvent(e) {
            var targetId = getValue(e, "target.id");

            // Sanity check
            if (!targetId) {
                return;
            }

            switch (e.type) {
            case "mousemove":
                hoverHandler(e);
                break;
            case "mouseout":
                leaveHandler(e);
                break;
            case "click":
                clickHandler(e);
                break;
            }
        }

        // Module interface.
        /**
         * @scope performance
         */
        return {

            // Expose for unit testing

            DEBUG: 1,

            // Properties

            // Functions
            postUIEvent: postUIEvent,
            getValue: getValue,

            /**
             * Initialize the overstat module.
             */
            init: function () {
            },

            /**
             * Terminate the overstat module.
             */
            destroy: function () {
                var key, i;
                for (key in eventMap) {
                    if (eventMap.hasOwnProperty(key)) {
                        eventMap[key].dispose();
                        delete eventMap[key];
                    }
                }
            },

            /**
             * Handle events subscribed by the overstat module.
             * @param  {Object} event The normalized data extracted from a browser event object.
             */
            onevent: function (event) {
                // Sanity check
                if (typeof event !== "object" || !event.type) {
                    return;
                }

                handleEvent(event);
            },

            /**
             * Handle system messages subscribed by the overstat module.
             * @param  {Object} msg An object containing the message information.
             */
            onmessage: function (msg) {

            },

			createHoverEvent: createHoverEvent,
			cleanupHoverEvents: cleanupHoverEvents,
            eventMap: eventMap
        };
    });  // End of TLT.addModule
} else {

    // Only throw an error in DEBUG mode.
    throw "Overstat module included but TLT is not defined!!!";

}

/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview The Performance module implements the logic for monitoring and
 * reporting performance data such as the W3C Navigation Timing.
 * @exports performance
 */

/*global TLT:true */

// Sanity check
if (TLT && typeof TLT.addModule === "function") {
    /**
     * @name performance
     * @namespace
     */
    TLT.addModule("performance", function (context) {
        "use strict";

        var moduleState = {
                loadReceived: false,
                unloadReceived: false,
                perfEventSent: false
            },
            calculatedRenderTime = 0;


        /**
         * Returns true if the property is filtered out. The property is considered
         * to be filtered out if it exists in the filter object with a value of true.
         * @private
         * @function
         * @name performance-isFiltered
         * @param {string} prop The property name to be tested.
         * @param {object} [filter] An object that contains property names and their
         * associated boolean value. A property marked true will be filtered out.
         * @return {boolean} true if the property is filtered out, false otherwise.
         */
        function isFiltered(prop, filter) {
            // Sanity check
            if (typeof prop !== "string") {
                return false;
            }

            // If there is no filter object then the property is not filtered out.
            if (!filter || typeof filter !== "object") {
                return false;
            }

            return (filter[prop] === true);
        }

        /**
         * Returns the normalized timing object. Normalized values are offsets measured
         * from the "navigationStart" timestamp which serves as the epoch. Also applies
         * the filter.
         * @private
         * @function
         * @name performance-parseTiming
         * @param {object} timing An object implementing the W3C PerformanceTiming
         * interface.
         * @param {object} [filter] An object that contains property names and their
         * associated boolean value. A property marked true will be filtered out.
         * @return {object} The normalized timing properties.
         */
        function parseTiming(timing, filter) {
            var epoch = 0,
                normalizedTiming = {},
                prop = "",
                value = 0;

            // Sanity checks
            if (!timing || typeof timing !== "object" || !timing.navigationStart) {
                return {};
            }

            epoch = timing.navigationStart;
            for (prop in timing) {
                // IE_COMPAT, FF_COMPAT: timing.hasOwnProperty(prop) returns false for
                // performance timing members in IE 9 and Firefox 14.0.1.

                // IE_COMPAT: timing.hasOwnProperty does not exist in IE8 and lower for
                // host objects. Legacy IE does not support hasOwnProperty on hosted objects.
                if (Object.prototype.hasOwnProperty.call(timing, prop) || typeof timing[prop] === "number") {
                    if (!isFiltered(prop, filter)) {
                        value = timing[prop];
                        if (typeof value === "number" && value && prop !== "navigationStart") {
                            normalizedTiming[prop] = value - epoch;
                        } else {
                            normalizedTiming[prop] = value;
                        }
                    }
                }
            }

            return normalizedTiming;
        }

        /**
         * Calculates the render time from the given timing object.
         * @private
         * @function
         * @name performance-getRenderTime
         * @param {object} timing An object implementing the W3C PerformanceTiming
         * interface.
         * @return {integer} The calculated render time or 0.
         */
        function getRenderTime(timing) {
            var renderTime = 0,
                startTime,
                endTime,
                utils = context.utils;

            if (timing) {
                // Use the lesser of domLoading or responseEnd as the start of render, see data in CS-8915
                startTime = (timing.responseEnd > 0 && timing.responseEnd < timing.domLoading) ? timing.responseEnd : timing.domLoading;
                endTime = timing.loadEventStart;
                if (utils.isNumeric(startTime) && utils.isNumeric(endTime) && endTime > startTime) {
                    renderTime = endTime - startTime;
                }
            }

            return renderTime;
        }

        /**
         * Calculates the render time by measuring the difference between when the
         * library core was loaded and when the page load event occurs.
         * @private
         * @function
         * @name performance-processLoadEvent
         * @param  {Object} event The normalized data extracted from a browser event object.
         */
        function processLoadEvent(event) {
            var startTime = context.getStartTime();
            if (event.timestamp > startTime && !calculatedRenderTime) {
                // Calculate the render time
                calculatedRenderTime = event.timestamp - startTime;
            }
        }

        /**
         * Posts the performance event.
         * @private
         * @function
         * @name performance-postPerformanceEvent
         * @param {object} window The DOM window
         */
        function postPerformanceEvent(window) {
            var config = context.getConfig() || {},
                navType = "UNKNOWN",
                queueEvent = {
                    type: 7,
                    performance: {}
                },
                navigation,
                performance,
                timing;

            // Sanity checks
            if (!window || moduleState.perfEventSent) {
                return;
            }

            performance = window.performance || {};
            timing = performance.timing;
            navigation = performance.navigation;

            if (timing) {
                queueEvent.performance.timing = parseTiming(timing, config.filter);
                queueEvent.performance.timing.renderTime = getRenderTime(timing);
            } else if (config.calculateRenderTime) {
                queueEvent.performance.timing = {
                    renderTime: calculatedRenderTime,
                    calculated: true
                };
            } else {
                // Nothing to report.
                return;
            }

            if (navigation) {
                switch (navigation.type) {
                case 0:
                    navType = "NAVIGATE";
                    break;
                case 1:
                    navType = "RELOAD";
                    break;
                case 2:
                    navType = "BACKFORWARD";
                    break;
                default:
                    navType = "UNKNOWN";
                    break;
                }
                queueEvent.performance.navigation = {
                    type: navType,
                    redirectCount: navigation.redirectCount
                };
            }

            // Invoke the context API to post this event
            context.post(queueEvent);
            // TODO: Remove all instances of perfEventSent flag from this method and localize it's use in the caller?
            moduleState.perfEventSent = true;
        }

        // Module interface.
        /**
         * @scope performance
         */
        return {

            // Expose private functions for unit testing
            isFiltered: isFiltered,
            parseTiming: parseTiming,
            getRenderTime: getRenderTime,
            postPerformanceEvent: postPerformanceEvent,

            /**
             * Initialize the performance module.
             */
            init: function () {
                // TODO: Possibly add check to see if navigation timing interface is supported. If not, short circuit the implementation below.
            },

            /**
             * Terminate the performance module.
             */
            destroy: function () {

            },

            /**
             * Handle events subscribed by the performance module.
             * @param  {Object} event The normalized data extracted from a browser event object.
             */
            onevent: function (event) {
                // Sanity check
                if (typeof event !== "object" || !event.type) {
                    return;
                }

                switch (event.type) {
                case "load":
                    moduleState.loadReceived = true;
                    processLoadEvent(event);
                    break;
                case "unload":
                    moduleState.unloadReceived = true;
                    // Force the performance data to be posted (if it hasn't been done already.)
                    if (!moduleState.perfEventSent) {
                        // TODO: Directly referencing the global window but may want to sandbox this.
                        postPerformanceEvent(window);
                    }
                    break;
                default:
                    break;
                }
            },

            /**
             * Handle system messages subscribed by the performance module.
             * @param  {Object} msg An object containing the message information.
             */
            onmessage: function (msg) {

            }
        };
    });  // End of TLT.addModule
} else {

    // Only throw an error in DEBUG mode.
    throw "Performance module included but TLT is not defined!!!";

}

/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview The Replay module implements the logic for monitoring and
 * reporting user interaction data used for replay and usability.
 * @exports replay
 */

/*global TLT:true */

// Sanity check
TLT.addModule("replay", function (context) {
    "use strict";

    var tlTypes = {
            "input:radio": "radioButton",
            "input:checkbox": "checkBox",
            "input:text": "textBox",
            "input:password": "textBox",
            "input:file": "fileInput",
            "input:button": "button",
            "input:submit": "submitButton",
            "input:reset": "resetButton",
            "input:image": "image",
            "input:color": "color",
            "input:date": "date",
            "input:datetime": "datetime",
            "input:datetime-local": "datetime-local",
            "input:number": "number",
            "input:email": "email",
            "input:tel": "tel",
            "input:search": "search",
            "input:url": "url",
            "input:time": "time",
            "input:week": "week",
            "input:month": "month",
            "textarea:": "textBox",
            "select:": "selectList",
            "button:": "button",
            "a:": "link"
        },
        currOrientation = window.orientation || 0,
        savedTouch = {
            scale: 0,
            timestamp: 0
        },
        pastEvents = {},
        prevHash = window.location.hash,
        lastEventId = null,
        tmpQueue = [],
        eventCounter = 0,
        curClientState = null,
        pastClientState = null,
        errorCount = 0,
        visitOrder = "",
        lastVisit = "",
        pageLoadTime = (new Date()).getTime(),
        pageDwellTime = 0,
        prevWebEvent = null,
        viewEventStart = null,
        viewTimeStart = null,
        viewPortXStart = 0,
        viewPortYStart = 0,
        inBetweenEvtsTimer = null,
        lastFocusEvent = { inFocus: false },
        lastClickEvent = null,
        //TODO move these to a global section due to they might be used elsewhere
        isApple = navigator.userAgent.indexOf("iPhone") > -1 || navigator.userAgent.indexOf("iPod") > -1 || navigator.userAgent.indexOf("iPad") > -1,
        isAndroidChrome = navigator.userAgent.indexOf("Chrome") > -1 && navigator.userAgent.indexOf("Android") > -1,
        devicePixelRatio = window.devicePixelRatio || 1,
        deviceOriginalWidth = (window.screen ? window.screen.width : 0),
        deviceOriginalHeight = (window.screen ? window.screen.height : 0),
        deviceToolbarHeight = (window.screen ? window.screen.height - window.screen.availHeight : 0),
        config = context.getConfig(),
        extendGetItem;

    /**
     * Used to test and get value from an object.
     * @private
     * @function
     * @name replay-getValue
     * @param {object} parentObj An object you want to get a value from.
     * @param {string} propertyAsStr A string that represents dot notation to get a value from object.
     * @return {object} If object is found, if not then null will be returned.
     */
    function getValue(parentObj, propertyAsStr) {
        var i,
            properties;

        // Sanity check
        if (!parentObj || typeof parentObj !== "object") {
            return null;
        }

        properties = propertyAsStr.split(".");
        for (i = 0; i < properties.length; i += 1) {
            if ((typeof parentObj === "undefined") || (parentObj[properties[i]] === null)) {
                return null;
            }
            parentObj = parentObj[properties[i]];
        }
        return parentObj;
    }

    function parentElements(node) {
        var parents = [];
        node = node.parentNode;
        while (node) {
            parents.push(node);
            node = node.parentNode;
        }
        return parents;
    }

    function getParentLink(parents) {
        return context.utils.some(parents, function (node) {
            // Either links or buttons could have content
            if (node.tagName === "A" || node.tagName === "BUTTON") {
                return node;
            }
            return null;
        });
    }

    /**
     * Get tlEvent from webEvent.
     * @private
     * @param {object} webEvent A webEvent with properties a type 4 object that is a control.
     * @return {string} tlEvent.
     */
    function getTlEvent(webEvent) {
        var tlEvent = webEvent.type;

        if (typeof tlEvent === "string") {
            tlEvent = tlEvent.toLowerCase();
        } else {
            tlEvent = "unknown";
        }

        if (tlEvent === "blur") {
            tlEvent = "focusout";
        }
        return tlEvent;
    }

    /**
     * Used to create control object from a webEvent.
     * TODO: Move tlType and similar normalization to message service.
     * XXX - Requires review and clean-up.
     * @private
     * @function
     * @name replay-createQueueEvent
     * @param {object} options An object with the following properties:
     *                 webEvent A webEvent that will created into a control.
     *                 id Id of the object.
     *                 prevState Previous state of the object.
     *                 currState Current state of the object.
     *                 visitedCount Visited count of the object.
     *                 dwell Dwell time on the object.
     *                 focusInOffset When you first focused on the object.
     * @return {object} Control object.
     */
    function createQueueEvent(options) {
        var control,
            tagName       = getValue(options, "webEvent.target.element.tagName"),
            type          = tagName.toLowerCase() === "input" ? getValue(options, "webEvent.target.element.type") : "",
            tlType        = tlTypes[tagName.toLowerCase() + ":" + type] || tagName,
            parents       = parentElements(getValue(options, "webEvent.target.element")),
            parentLinkNode = null,
            relXY         = getValue(options, "webEvent.target.position.relXY"),
            eventSubtype  = getValue(options, "webEvent.target.subtype");

        control = {
            type: 4,
            target: {
                id: options.id || "",
                idType: getValue(options, "webEvent.target.idType"),
                name: getValue(options, "webEvent.target.name"),
                tlType: tlType,
                type: tagName,
                subType: type,
                position: {
                    width: getValue(options, "webEvent.target.element.offsetWidth"),
                    height: getValue(options, "webEvent.target.element.offsetHeight")
                },
                currState: options.currState || null
            },
            event: {
                tlEvent: getTlEvent(getValue(options, "webEvent")),
                type: getValue(options, "webEvent.target.type")
            }
        };

        if (relXY) {
            control.target.position.relXY = relXY;
        }

        if (typeof options.dwell === "number" && options.dwell > 0) {
            control.target.dwell = options.dwell;
        }

        if (typeof options.visitedCount === "number") {
            control.target.visitedCount = options.visitedCount;
        }

        if (typeof options.prevState !== "undefined") {
            control.prevState = options.prevState;
        }

        if (typeof eventSubtype !== "undefined") {
            control.event.subType = eventSubtype;
        }

        // Add usability to config settings
        control.target.name = getValue(options, "webEvent.target.name");
        parentLinkNode = getParentLink(parents);
        control.target.isParentLink = !!parentLinkNode;
        if (parentLinkNode) {
            // Add the parent's href, value and innerText if the actual target doesn't
            // support these properties
            if (parentLinkNode.href) {
                control.target.currState = control.target.currState || {};
                control.target.currState.href = control.target.currState.href || parentLinkNode.href;
            }
            if (parentLinkNode.value) {
                control.target.currState = control.target.currState || {};
                control.target.currState.value = control.target.currState.value || parentLinkNode.value;
            }
            if (parentLinkNode.innerText || parentLinkNode.textContent) {
                control.target.currState = control.target.currState || {};
                control.target.currState.innerText = context.utils.trim(control.target.currState.innerText || parentLinkNode.innerText || parentLinkNode.textContent);
            }
        }

		if (control.target.currState === null) {
			delete control.target.currState;
		}
		if (control.target.name === null || typeof control.target.name === "undefined") {
			delete control.target.name;
		}
        return control;
    }

    function postUIEvent(queueEvent) {
        context.post(queueEvent);
    }

    function updateVisitOrder(msg) {
        var name = getValue(msg, "target.name"),
            dwell = getValue(msg, "target.dwell") || -1;

        if (name) {
            visitOrder += name + ":" + dwell + ";";
            lastVisit = name;
        }
    }

    /**
     * Posts all events from given array to the message service. The input
     * array is cleared on exit from the function.
     * Function additionally consolidates events fired on the same DOM element
     * TODO: Explain the consolidation process. Needs to be refactored!
     * @private
     * @param {Array} queue An array of QueueEvents
     * @return void
     */
    function postEventQueue(queue) {
        var i = 0,
            j,
            len = queue.length,
            e1,
            e2,
            tmp,
            ignoredEvents = {
                mouseout: true,
                mouseover: true
            },
            results = [];

        for (i = 0; i < len; i += 1) {
            e1 = queue[i];
            if (!e1) {
                continue;
            }
            if (ignoredEvents[e1.event.type]) {
                results.push(e1);
            } else {
                for (j = i + 1; j < len && queue[j]; j += 1) {
                    if (!ignoredEvents[queue[j].event.type]) {
                        break;
                    }
                }
                if (j < len) {
                    e2 = queue[j];
                    if (e2 && e1.target.id === e2.target.id && e1.event.type !== e2.event.type) {
                        if (e1.event.type === "click") {
                            tmp = e1;
                            e1 = e2;
                            e2 = tmp;
                        }
                        if (e2.event.type === "click") {
                            e1.target.position = e2.target.position;
                            i += 1;
                        } else if (e2.event.type === "blur") {
                            e1.target.dwell = e2.target.dwell;
                            e1.target.visitedCount = e2.target.visitedCount;
                            e1.focusInOffset = e2.focusInOffset;
                            e1.target.position = e2.target.position;
                            i += 1;
                        }
                        queue[j] = null;
                        queue[i] = e1;
                    }
                }
                results.push(queue[i]);
            }
        }

        for (e1 = results.shift(); e1; e1 = results.shift()) {
            updateVisitOrder(e1);
            context.post(e1);
        }
        queue.splice(0, queue.length);
    }


    if (typeof window.onerror !== "function") {
        window.onerror = function (msg, url, line) {
            var errorMessage = null;

            if (typeof msg !== "string") {
                return;
            }
            line = line || -1;
            errorMessage = {
                type: 6,
                exception: {
                    description: msg,
                    url: url,
                    line: line
                }
            };

            errorCount += 1;
            context.post(errorMessage);
        };
    }

    /**
     * Handles the focus events. It is fired either when the real focus event take place
     * or right after the click event on an element (only when browser focus event was not fired)
     * @private
     * @param {string} id ID of an elment
     * @param {WebEvent} webEvent Normalized browser event
     * @return void
     */
    function handleFocus(id, webEvent) {
        lastFocusEvent = webEvent;
        lastFocusEvent.inFocus = true;
        if (typeof pastEvents[id] === "undefined") {
            pastEvents[id] = {};
        }

        pastEvents[id].focus = lastFocusEvent.dwellStart = Number(new Date());
        pastEvents[id].focusInOffset = viewTimeStart ? lastFocusEvent.dwellStart - Number(viewTimeStart) : -1;
        pastEvents[id].prevState = getValue(webEvent, "target.state");
        pastEvents[id].visitedCount = pastEvents[id].visitedCount + 1 || 1;
    }

    /**
     * Create and add value that will be posted to queue.
     * @private
     * @param {string} id ID of an elment
     * @param {WebEvent} webEvent Normalized browser event
     * @return void
     */
    function addToTmpQueue(webEvent, id) {
        tmpQueue.push(createQueueEvent({
            webEvent: webEvent,
            id: id,
            currState: getValue(webEvent, "target.state")
        }));
    }

    /**
     * Returns true if the click event changes the target state or is otherwise
     * relevant for the target.
     * @private
     * @param {WebEvent.target} target Webevent target
     * @return {boolean} true if the click event is relevant for the target, false otherwise.
     */
    function isTargetClickable(target) {
        var clickable = false,
            clickableInputTypes = "|button|image|submit|reset|checkbox|radio|",
            subType = null;

        if (typeof target !== "object" || !target.type) {
            return clickable;
        }

        switch (target.type) {
        case "INPUT":
            // Clicks are relevant for button type inputs only.
            subType = "|" + (target.subType || "") + "|";
            if (clickableInputTypes.indexOf(subType.toLowerCase()) === -1) {
                clickable = false;
            } else {
                clickable = true;
            }
            break;
        case "TEXTAREA":
            clickable = false;
            break;
        default:
            // By default, clicks are relevant for all targets.
            clickable = true;
            break;
        }

        return clickable;
    }

    /**
     * Handles blur events. It is invoked when browser blur events fires or from the
     * handleFocus method (only when browser 'blur' event didn't take place).
     * In the first case it's called with current event details, in the second one -
     * with lastFocusEvent. Method posts the tmpQueue of events. If during the same
     * focus time change event was fired the focus data will be combined together with
     * the last change event from the tmpQueue.
     * @private
     * @param {string} id ID of an elment
     * @param {WebEvent} webEvent Normalized browser event
     * @return void
     */
    function handleBlur(id, webEvent) {
        var lastQueueEvent;

        if (typeof id === "undefined" || id === null || typeof webEvent === "undefined" || webEvent === null) {
            return;
        }

        lastFocusEvent.inFocus = false;

        if (typeof pastEvents[id] !== "undefined" && pastEvents[id].hasOwnProperty("focus")) {
            pastEvents[id].dwell =  Number(new Date()) - pastEvents[id].focus;
        } else {
            // TODO this seem to be unexpected state, fix it
            pastEvents[id] = {};
            pastEvents[id].dwell = 0;
        }

        if (tmpQueue.length === 0) {
            webEvent.type = webEvent.target.type = "blur";
            addToTmpQueue(webEvent, id);
        }

        lastQueueEvent = tmpQueue[tmpQueue.length - 1];
        if (lastQueueEvent) {
            lastQueueEvent.target.dwell = pastEvents[id].dwell;
            lastQueueEvent.focusInOffset = pastEvents[id].focusInOffset;
            lastQueueEvent.target.visitedCount = pastEvents[id].visitedCount;

            // if the click (without generating change event) fires on an
            // input element for which it's not relevant - report event as a blur and update the currState
            if (lastQueueEvent.event.type === "click" && !isTargetClickable(lastQueueEvent.target)) {
				lastQueueEvent.target.currState = getValue(webEvent, "target.state");
                lastQueueEvent.event.type = "blur";
                lastQueueEvent.event.tlEvent = "focusout";
            }
        }

        postEventQueue(tmpQueue);
    }

    /**
     * Checks to see in tmpQueue there is an older control that needs to be posted to server.
     * @private
     * @param {string} id ID of an elment
     * @param {WebEvent} webEvent Normalized browser event
     * @return Whether it has been sent to server.
     */
    function checkQueue(id, webEvent) {
        var hasInQueue = false;

        // TODO: Optimize the index by storing tmpQueue.length - 1 into a variable?
        if (tmpQueue.length > 0 && tmpQueue[tmpQueue.length - 1] && tmpQueue[tmpQueue.length - 1].target.id !== id &&
                // iOS scrolls & Android resizes after selecting a textbox
                webEvent.type !== "scroll" && webEvent.type !== "resize" &&
                // mouseover should not affect handleBlur invocation
                webEvent.type !== "mouseout" && webEvent.type !== "mouseover" &&
                // Need focus and click values to complete consolidation of message for these types
                (tmpQueue[tmpQueue.length - 1].target.tlType !== "textBox" &&
                tmpQueue[tmpQueue.length - 1].target.tlType !== "selectList")) {
            handleBlur(tmpQueue[tmpQueue.length - 1].target.id, tmpQueue[tmpQueue.length - 1]);
            hasInQueue = true;
        }
        return hasInQueue;
    }

    /**
     * Handles change and click events. Its called when browser 'change' event fires
     * or together with click event (from 'handleClick' method).
     * @private
     * @param {string} id ID of an elment
     * @param {WebEvent} webEvent Normalized browser event
     * @return void
     */
    function handleChange(id, webEvent) {
        if (typeof pastEvents[id] !== "undefined" && !pastEvents[id].hasOwnProperty("focus")) {
            handleFocus(id, webEvent);
        }

        addToTmpQueue(webEvent, id);

        if (typeof pastEvents[id] !== "undefined" && typeof pastEvents[id].prevState !== "undefined") {
            // TODO: Optimize the index by storing tmpQueue.length - 1 to a variable.
            if (tmpQueue[tmpQueue.length - 1].target.tlType === "textBox" ||
                    tmpQueue[tmpQueue.length - 1].target.tlType === "selectList") {
                tmpQueue[tmpQueue.length - 1].target.prevState = pastEvents[id].prevState;
            }
        }
    }

    /**
     * Sets the relative X & Y values to a webEvent.
     * TODO: Explain how relative X & Y should be calculated (in other words, define relative X & Y)
     * XXX - Shouldn't this be named "get" instead of "set"?
     * @private
     * @param {WebEvent} webEvent Normalized browser event
     * @return String value of relative X & Y
     */
    function setRelativeXY(webEvent) {
        var x = webEvent.target.position.x,
            y = webEvent.target.position.y,
            width = webEvent.target.size.width,
            height = webEvent.target.size.height,
            relX = Math.abs(x / width).toFixed(1),
            relY = Math.abs(y / height).toFixed(1);

        relX = relX > 1 || relX < 0 ? 0.5 : relX;
        relY = relY > 1 || relY < 0 ? 0.5 : relY;

        return relX + "," + relY;
    }

    /**
     * Handles click events. Additionally it recognizes situations when browser didn't
     * fire the focus event and in such case it invokes 'handleFocus' method.
     * @private
     * @param {string} id ID of an elment
     * @param {WebEvent} webEvent Normalized browser event
     * @return void
     */
    function handleClick(id, webEvent) {
        var relXY,
            addRelXY = true,
            tmpQueueLength = 0;

        if (webEvent.target.element.tagName === "SELECT" && lastClickEvent && lastClickEvent.target.id === id) {
            lastClickEvent = null;
            return;
        }

        if (!lastFocusEvent.inFocus) {
            handleFocus(id, webEvent);
        }

        // Sometimes the change triggers before the click (observed in Chrome and Android)
        // XXX - Not sure I fully understand this logic - MP
        tmpQueueLength = tmpQueue.length;
        if (tmpQueueLength && getValue(tmpQueue[tmpQueueLength - 1], "event.type") !== "change") {
            handleChange(id, webEvent);
        }

        relXY = setRelativeXY(webEvent);

        // During use of arrow keys to select a radio option, it throws a click event after change event
        // which is incorrect for usability data. We only capture user clicks and not framework clicks.
        tmpQueueLength = tmpQueue.length;

        if (webEvent.position.x === 0 && webEvent.position.y === 0 && tmpQueueLength &&
                getValue(tmpQueue[tmpQueueLength - 1], "target.tlType") === "radioButton") {
            addRelXY = false;
        } else {
            // For all other cases, record the relXY in the target.position
            webEvent.target.position.relXY = relXY;
        }

        // Update the existing queue entry with relXY info. from the click event
        if (tmpQueueLength &&
                getValue(tmpQueue[tmpQueueLength - 1], "target.id") === id) {
            if (addRelXY) {
                tmpQueue[tmpQueueLength - 1].target.position.relXY = relXY;
            }
        } else {
            // Else add the click event to the queue
            addToTmpQueue(webEvent, id);
        }

        // XXX - What is lastClickEvent being used for? - MP
        lastClickEvent = webEvent;
    }

    /**
     * Returns the normalized orientation in degrees. Normalized values are measured
     * from the default portrait position which has an orientation of 0. From this
     * position the respective values are as follows:
     * 0   - Portrait orientation. Default
     * -90 - Landscape orientation with screen turned clockwise.
     * 90  - Landscape orientation with screen turned counterclockwise.
     * 180 - Portrait orientation with screen turned upside down.
     * @private
     * @function
     * @name replay-getNormalizedOrientation
     * @return {integer} The normalized orientation value.
     */
    function getNormalizedOrientation() {
        var orientation = window.orientation || 0;
        // XXX - This functionality should probably be moved into the browser service.
        // TODO: Normalize for Android

        return orientation;
    }


    /**
     * Handles the "orientationchange" event and posts the appropriate message
     * to the replay module's queue.
     * @private
     * @function
     * @name replay-handleOrientationChange
     * @param {object} webEvent A normalized event object per the WebEvent
     * interface definition.
     */
    function handleOrientationChange(webEvent) {
        var newOrientation = getNormalizedOrientation(),
            orientationChangeEvent = {
                type: 4,
                event: {
                    type: "orientationchange"
                },
                target: {
                    prevState: {
                        orientation: currOrientation,
                        orientationMode: context.utils.getOrientationMode(currOrientation)
                    },
                    currState: {
                        orientation: newOrientation,
                        orientationMode: context.utils.getOrientationMode(newOrientation)
                    }
                }
            };

        postUIEvent(orientationChangeEvent);
        currOrientation = newOrientation;
    }

    /* TODO: Refactor this to use a well-defined touchState object */
    function isDuplicateTouch(touchState) {
        var result = false;

        if (!touchState) {
            return result;
        }

        result = (savedTouch.scale === touchState.scale &&
                Math.abs((new Date()).getTime() - savedTouch.timestamp) < 500);

        return result;
    }

    function saveTouchState(touchState) {
        savedTouch.scale = touchState.scale;
        savedTouch.rotation = touchState.rotation;
        savedTouch.timestamp = (new Date()).getTime();
    }

    /**
     * Takes the scale factor and returns the pinch mode as a text string.
     * Values less than 1 correspond to a pinch close gesture. Values greater
     * than 1 correspond to a pinch open gesture.
     * @private
     * @function
     * @name replay-getPinchType
     * @param {float} scale A normalized value less than, equal to or greater than 1.
     * @return {String} "CLOSE", "OPEN" or "NONE" for valid scale values.
     * "INVALID" in case of error.
     */
    function getPinchType(scale) {
        var s,
            pinchType = "INVALID";

        if (typeof scale === "undefined" || scale === null) {
            return pinchType;
        }

        s = Number(scale);
        if (isNaN(s)) {
            pinchType = "INVALID";
        } else if (s < 1) {
            pinchType = "CLOSE";
        } else if (s > 1) {
            pinchType = "OPEN";
        } else {
            pinchType = "NONE";
        }

        return pinchType;
    }

    /**
     * Handles the "touchend" event and posts the appropriate message to the
     * replay module's queue.
     * @private
     * @function
     * @name replay-handleTouchEnd
     * @param {object} webEvent A normalized event object per the WebEvent
     * interface definition.
     */
    function handleTouchEnd(webEvent) {
        var prevTouchState = {},
            rotation = getValue(webEvent, "nativeEvent.rotation") || 0,
            scale = getValue(webEvent, "nativeEvent.scale") || 1,
            touchState = null,
            touchEndEvent = {
                type: 4,
                event: {
                    type: "touchend"
                },
                target: {
                    id: getValue(webEvent, "target.id"),
                    idType: getValue(webEvent, "target.idType")
                }
            };

        // Test for single finger touches and return if true. We will only send touchend for gestures.
        if ((isApple && (!scale || scale === 1)) ||
                (!isApple && webEvent.nativeEvent.touches.length <= 1)) {
            return;
        }

        touchState = {
            rotation: rotation ? rotation.toFixed(2) : 0,
            scale: scale ? scale.toFixed(2) : 1
        };
        touchState.pinch = getPinchType(touchState.scale);
        if (isDuplicateTouch(touchState)) {
            // Do not record if this event has duplicate scale info.
            return;
        }
        if (savedTouch && savedTouch.timestamp) {
            prevTouchState.rotation = savedTouch.rotation;
            prevTouchState.scale = savedTouch.scale;
            prevTouchState.pinch = getPinchType(prevTouchState.scale);
        }
        if (getValue(prevTouchState, "scale")) {
            touchEndEvent.target.prevState = prevTouchState;
        }
        touchEndEvent.target.currState = touchState;
        postUIEvent(touchEndEvent);
        saveTouchState(touchState);
    }

    function addLegacyHeaders(eventType) {
        var pageObjects = [],
            renderTime = 0,
            RENDER_TIME_CAP = 3600000,
            timing = null;

        switch (eventType) {
        case "load":
            pageLoadTime = (new Date()).getTime();

            pageObjects = window.document.getElementsByName("object");
            context.addHeader("X-TeaLeaf-Page-Objects", pageObjects.length);
            break;
        case "unload":
            // Add the render time to the HTTP headers
            timing = getValue(window, "performance.timing");
            if (timing && timing.loadEventStart) {
                renderTime = Math.abs(timing.loadEventStart - timing.responseEnd);
                context.addHeader("X-TeaLeaf-Page-Render", renderTime > RENDER_TIME_CAP ? RENDER_TIME_CAP : renderTime);
            }

            // Add the dwell time to the HTTP headers
            pageDwellTime = (new Date()).getTime() - pageLoadTime;
            context.addHeader("X-TeaLeaf-Page-Dwell", pageDwellTime);

            context.addHeader("X-TeaLeaf-Page-Cui-Exceptions", errorCount);

            context.addHeader("X-TeaLeaf-Visit-Order", visitOrder);

            context.addHeader("X-TeaLeaf-Page-Last-Field", lastVisit);
            break;
        default:
            break;
        }
    }

    /**
     * Used to create client state from a webEvent.
     * @private
     * @function
     * @name replay-handleClientState
     * @param {object} webEvent A webEvent that will created into a clientState and saved for previous and current client state.
     * @return {object} Client state object.
     */
    function handleClientState(webEvent) {
        var documentElement = document.documentElement,
            documentBody = document.body,
            clientState = {
                type: 1,
                clientState: {
                    pageWidth: document.width || (!documentElement ? 0 : documentElement.offsetWidth),
                    pageHeight: Math.max((!document.height ? 0 : document.height), (!documentElement ? 0 : documentElement.offsetHeight), (!documentElement ? 0 : documentElement.scrollHeight)),
                    viewPortWidth: window.innerWidth || documentElement.clientWidth,
                    viewPortHeight: window.innerHeight || documentElement.clientHeight,
                    viewPortX: window.pageXOffset || (!documentElement ? (!documentBody ? 0 : documentBody.scrollLeft) : documentElement.scrollLeft || 0),
                    viewPortY: window.pageYOffset || (!documentElement ? (!documentBody ? 0 : documentBody.scrollTop) : documentElement.scrollTop || 0),
                    deviceOrientation: window.orientation || 0,
                    event: getValue(webEvent, "type")
                }
            },
            deviceWidth = 1,
            scaleWidth = 1;

        if (Math.abs(clientState.clientState.deviceOrientation) === 90) {
            if (isApple || isAndroidChrome) {
                deviceWidth = deviceOriginalHeight - deviceToolbarHeight;
            } else {
                // Need to display web content no smaller than 320 or it will look incorrect. Older Android devices give these values due to they are built on a webview and not an actual browser.
                deviceWidth = deviceOriginalWidth <= 320 ? deviceOriginalHeight - deviceToolbarHeight : ((deviceOriginalHeight / devicePixelRatio) - deviceToolbarHeight);
            }
        } else {
            if (isApple || isAndroidChrome) {
                deviceWidth = deviceOriginalWidth + deviceToolbarHeight;
            } else {
                // Need to display web content no smaller than 320 or it will look incorrect. Older Android devices give these values due to they are built on a webview and not an actual browser.
                deviceWidth = deviceOriginalWidth <= 320 ? deviceOriginalWidth - deviceToolbarHeight : ((deviceOriginalWidth / devicePixelRatio) - deviceToolbarHeight);
            }
        }

        scaleWidth = (clientState.clientState.viewPortWidth === 0 ? 1 : deviceWidth / clientState.clientState.viewPortWidth);

        // Made scale a bit smaller to adjust for scroll bars that appear on top of content on certain browsers.
        clientState.clientState.deviceScale = scaleWidth - 0.02;
        clientState.clientState.deviceScale = clientState.clientState.deviceScale.toFixed(3);
        clientState.clientState.viewTime = viewEventStart === null ? 0 : (new Date()).getTime() - viewEventStart.getTime();

        if (webEvent.type === "scroll" && eventCounter <= 0) {
            viewPortXStart = pastClientState.clientState.viewPortX;
            viewPortYStart = pastClientState.clientState.viewPortY;
        }

        if (webEvent.type === "scroll") {
            clientState.clientState.viewPortXStart = viewPortXStart;
            clientState.clientState.viewPortYStart = viewPortYStart;
        }
        curClientState = context.utils.clone(clientState);

        return clientState;
    }

    /**
     * Used to create client state for an attention event.
     * @private
     * @function
     * @name replay-checkViewClientState
     * @return {boolean} Whether attention was sent.
     */
    function checkViewClientState() {
        if (curClientState !== null && curClientState.clientState.event !== "load") {
            if (curClientState.clientState.event === "scroll") {
                delete curClientState.clientState.viewPortXStart;
                delete curClientState.clientState.viewPortYStart;
            }

            curClientState.clientState.event = "attention";
            curClientState.clientState.viewTime = viewTimeStart === null ? 0 : (new Date()).getTime() - viewTimeStart.getTime();
            postUIEvent(curClientState);
            viewTimeStart = new Date();
            return true;
        }
        return false;
    }

    /**
     * Used to check client state of a scroll event to see if there ws an actual scroll.
     * @private
     * @function
     * @name replay-checkScrollState
     * @param {object} clientState A clientState with a scroll event.
     * @return {boolean} Whether scroll values have changed.
     */
    function checkScrollState(clientState) {
        if ((clientState.clientState.event === "scroll") &&
                (clientState.clientState.viewPortXStart === clientState.clientState.viewPortX) &&
                (clientState.clientState.viewPortYStart === clientState.clientState.viewPortY)) {
            return false;
        }
        return true;
    }

    /**
     * Used to check client state and see if it is posted.
     * @private
     * @function
     * @name replay-checkClientState
     * @param {object} webEvent A webEvent that will created into a clientState and saved for previous and current client state.
     * @return {boolean} Whether attention was sent.
     */
    function checkClientState(webEvent) {
        var inBetweenEvtsTime = inBetweenEvtsTimer === null ? 0 : (new Date()).getTime() - inBetweenEvtsTimer.getTime();
        if (curClientState !== null && (webEvent.type !== curClientState.clientState.event || inBetweenEvtsTime >= 1000)) {
            if (checkScrollState(curClientState)) {
                postUIEvent(curClientState);
                if (curClientState.clientState.event !== "touchend") {
                    pastClientState = context.utils.clone(curClientState);
                }
            }

            curClientState = null;
            viewEventStart = null;
            eventCounter = 0;
            return true;
        }

        if (curClientState !== null && (eventCounter === 1 && inBetweenEvtsTime >= 1000) &&
                (curClientState.clientState.event === "resize" || curClientState.clientState.event === "scroll" || curClientState.clientState.event === "orientationchange" || webEvent.type === "screenview_load")) {
            // time to send attention data
            checkViewClientState();
        }
        return false;
    }

    /**
     * Compares two WebEvent's to determine if they are duplicates. Examines
     * the event type, target id and the timestamp to make this determination.
     * XXX - Push this into the browser service or core?!?
     * @private
     * @function
     * @name replay-isDuplicateEvent
     * @param {object} curr A WebEvent object
     * @param {object} prev A WebEvent object
     * @return {boolean} Returns true if the WebEvents are duplicates.
     */
    function isDuplicateEvent(curr, prev) {
        var propsToCompare = ["type", "target.id"],
            prop = null,
            i,
            len,
            duplicate = true,
            DUPLICATE_EVENT_THRESHOLD_TIME = 10,
            timeDiff = 0,
            currTimeStamp = 0,
            prevTimeStamp = 0;

        // Sanity check
        if (!curr || !prev || typeof curr !== "object" || typeof prev !== "object") {
            duplicate = false;
        }

        // Compare WebEvent properties
        for (i = 0, len = propsToCompare.length; duplicate && i < len; i += 1) {
            prop = propsToCompare[i];
            if (getValue(curr, prop) !== getValue(prev, prop)) {
                duplicate = false;
                break;
            }
        }

        if (duplicate) {
            currTimeStamp = getValue(curr, "timestamp");
            prevTimeStamp = getValue(prev, "timestamp");
            // Don't compare if neither objects have a timestamp
            if (!(isNaN(currTimeStamp) && isNaN(prevTimeStamp))) {
                // Check if the event timestamps are within the predefined threshold
                timeDiff = Math.abs(getValue(curr, "timestamp") - getValue(prev, "timestamp"));
                if (isNaN(timeDiff) || timeDiff > DUPLICATE_EVENT_THRESHOLD_TIME) {
                    duplicate = false;
                }
            }
        }

        return duplicate;
    }

    /**
     * Keeps track of the location.hash and logs the appropriate screenview messages
     * when a hash change is detected.
     * @private
     * @function
     * @name replay-trackHashchange
     */
    function trackHashchange() {
        var currHash = window.location.hash;

        if (currHash === prevHash) {
            return;
        }

        // TODO: Expose logScreenview on context so we don't reference TLT
        if (prevHash) {
            // Send the screenview unload
            TLT.logScreenviewUnload(prevHash);
        }

        if (currHash) {
            // Send the screenview load
            TLT.logScreenviewLoad(currHash);
        }

        // Save the current hash value
        prevHash = currHash;
    }

    /**
      * Returns true if the key (localStorage key) is to be captured. The key is considered
      * to be filtered out if it does not exists in the capture object.
      * @private
      * @function
      * @name replay-isStorageKeyCaptured
      * @param {string} key The key name to be tested
      * associated boolean value. A key marked false will be filtered out or if the key in not found
      * @return {boolean} true if the key is to be captured, false otherwise.
      */
    function isStorageKeyCaptured(key) {
        //[capture] An object that contains key names
        var capture = config.storageKeys;

        // Sanity check
        if (typeof key !== "string") {
            return false;
        }

        if (context.utils.indexOf(capture, key) === -1) {
            return false;
        }
        return true;
    }

    /**
     * Handles the "webStorage" event and posts the appropriate message to the
     * replay module's queue only if isStorage(key)
     * @private
     * @function
     * @name replay-handleStorage
     * @param {object} webEvent A normalized event object per the WebEvent
     * interface definition.
     */
    function handleStorage(data) {
        var storage = null;
        if (data && isStorageKeyCaptured(data.key)) {
            storage = {
                type: 8,
                webStorage: data
            };

            postUIEvent(storage);
        }

        return storage;
    }

    /**
     * Extend the getItem() method of the object Storage
     * This is done to capture reads from localStorage.getItem().
     * Assuming client js is using this api and this method is run before everything else.
     * @private
     * @function
     * @name replay extendGetItem
     * @returns {Boolean} false for failiure and true for success.
     **/
    extendGetItem = (function (window) {
        var _getItem = window.Storage ? window.Storage.prototype.getItem : function () {};

        return function () {
            var storageData;
            try {
                window.Storage.prototype.getItem = function (key) {
                    try {
                        var value = _getItem.call(localStorage, key);
                        storageData = {
                            key: key || null,
                            value: value
                        };

                        handleStorage(storageData);
                        return value;
                    } catch (e) {}
                };
            } catch (e) {
                return false;
            }
            return true;
        };
    }(window));

    /**
     * Default handler for event types that are not being processed by the module.
     * @private
     * @function
     * @param {object} webEvent A WebEvent object
     * @name replay-defaultEventHandler
     */
    function defaultEventHandler(webEvent) {
        var msg = {
                type: 4,
                event: {
                    type: webEvent.type
                },
                target: {
                    id: getValue(webEvent, "target.id"),
                    idType: getValue(webEvent, "target.idType")
                }
            };

        postUIEvent(msg);
    }

    return {
        // Expose private functions for unit testing
        tlTypes: tlTypes,
        currOrientation: currOrientation,
        pastEvents: pastEvents,
        lastEventId: lastEventId,
        tmpQueue: tmpQueue,
        postEventQueue: postEventQueue,
        eventCounter: eventCounter,
        curClientState: curClientState,
        getViewEventStart: function () {return viewEventStart; },
        setViewEventStart: function (newViewEventStart) {viewEventStart = newViewEventStart; },
        viewTimeStart: viewTimeStart,
        getValue: getValue,
        parentElements: parentElements,
        getParentLink: getParentLink,
        createQueueEvent: createQueueEvent,
        postUIEvent: postUIEvent,
        handleFocus: handleFocus,
        handleBlur: handleBlur,
        handleChange: handleChange,
        handleClick: handleClick,
        getNormalizedOrientation: getNormalizedOrientation,
        handleOrientationChange: handleOrientationChange,
        handleClientState: handleClientState,
        checkViewClientState: checkViewClientState,
        checkClientState: checkClientState,
        getPinchType: getPinchType,
        saveTouchState: saveTouchState,
        isDuplicateTouch: isDuplicateTouch,
        getTlEvent: getTlEvent,
        isDuplicateEvent: isDuplicateEvent,
        trackHashchange: trackHashchange,
        isTargetClickable: isTargetClickable,
        defaultEventHandler: defaultEventHandler,
        extendGetItem: extendGetItem,
        isStorageKeyCaptured: isStorageKeyCaptured,
        handleStorage: handleStorage,
        init: function () {

        },
        destroy: function () {
            handleBlur(lastEventId);
        },
        onevent: function (webEvent) {
            var id = null,
                handleObj = null;

            // Sanity checks
            if (typeof webEvent !== "object" || !webEvent.type) {
                return;
            }

            if (isDuplicateEvent(webEvent, prevWebEvent)) {
                prevWebEvent = webEvent;
                return;
            }

            prevWebEvent = webEvent;

/*jshint devel:true */

            if (typeof console !== "undefined") {
                console.log("Replay event: ", webEvent);
            }

            id = getValue(webEvent, "target.id");

            if (Object.prototype.toString.call(pastEvents[id]) !== "[object Object]") {
                pastEvents[id] = {};
            }

            checkClientState(webEvent);
            checkQueue(id, webEvent);
            inBetweenEvtsTimer = new Date();

            switch (webEvent.type) {
            case "hashchange":
                trackHashchange();
                break;
            case "focus":
                handleObj = handleFocus(id, webEvent);
                break;
            case "blur":
                handleObj = handleBlur(id, webEvent);
                break;
            case "click":
                // Normal click processing
                handleObj = handleClick(id, webEvent);
                break;
            case "change":
                handleObj = handleChange(id, webEvent);
                break;
            case "orientationchange":
                handleObj = handleOrientationChange(webEvent);
                break;
            case "touchend":
                handleObj = handleTouchEnd(webEvent);
                handleObj = handleClientState(webEvent);
                break;
            case "load":
                extendGetItem();
                addLegacyHeaders("load");
                // XXX - Use the context instead?
                TLT.logScreenviewLoad("root");

                handleObj = handleClientState(webEvent);
                // starts attention time
                viewTimeStart = new Date();
                break;
            case "screenview_load":
                // starts attention time
                viewTimeStart = new Date();
                break;
            case "screenview_unload":
                // Do nothing.
                break;
            case "resize":
            case "scroll":
                if (viewEventStart === null && eventCounter <= 0) {
                    viewEventStart = new Date();
                }

                handleObj = handleClientState(webEvent);

                // Sent scroll event, but no movement from last value
                if (checkScrollState(handleObj)) {
                    handleObj = null;
                } else {
                    eventCounter += 1;
                }
                break;
            case "unload":
                // Flush any saved control
                if (tmpQueue !== null) {
                    postEventQueue(tmpQueue);
                }

                addLegacyHeaders("unload");

                // create unload
                handleObj = handleClientState(webEvent);
                // post attention
                checkViewClientState();
                // post unload
                postUIEvent(handleObj);

                // XXX - Use the context instead?
                TLT.logScreenviewUnload("root");

                break;
            default:
                // Call the default handler for all other DOM events
                defaultEventHandler(webEvent);
                break;
            }

            lastEventId = id;
            return handleObj;
        },
        onmessage: function () {
        }
    };
});

/**
 * Licensed Materials - Property of IBM
 * � Copyright IBM Corp. 2014
 * US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.
 */

/**
 * @fileOverview The SaaS module implements the logic for using Tealeaf in the cloud.
 * @exports saas
 */

/*global TLT:true */

// Sanity check
TLT.addModule("saas", function (context) {
    "use strict";

    /**
     * Sets the SaaS data object to the configuration specified by the user in the config.
     * @private
     */
    var SaasData = function () {
			if (typeof TLT.getCoreConfig().modules.saas !== "undefined") {
				var key;

				for (key in TLT.getCoreConfig().modules.saas) {
					if (TLT.getCoreConfig().modules.saas.hasOwnProperty(key) && typeof key === "string" && typeof TLT.getCoreConfig().modules.saas[key] === "string") {
						this[key] = TLT.getCoreConfig().modules.saas[key];
						document.cookie = key + "=" + this[key];
					}
				}

				/**
				* Gets Tealeaf SaaS session data
				* @function
				* @name saas-saasData.get
				* @param {string} key SaaS session key to get.
				* @return {string} Value associated with the SaaS session key or error description.
				*/
				this.get = function (key) {
					if (typeof key !== "string" || typeof this === "undefined") {
						return "SaaS Data undefined or key is not a string";
					}
					if (typeof this[key] === "undefined") {
						return "Key does not exist within saasData";
					}
					return this[key];
				};

				/**
				* Sets Tealeaf SaaS session data.
				* @function
				* @name saas-saasData.set
				* @param {string} key SaaS session key to be changed or created.
				* @param {string} value SaaS session value to be set.
				* @return {boolean} True if the cookie was set, false if not.
				*/
				this.set = function (key, value) {
					if (typeof key !== "string" || typeof value !== "string" || typeof this === "undefined" || key === "get" || key === "set" || key === "toSaasString" || key === "clear" || key === "remove") {
						return false;
					}
					this[key] = value;
					document.cookie = key + "=" + value;
					return true;
				};

				/**
				* Clears Tealeaf SaaS data.
				* @function
				* @name saas-saasData.clear
				* @returns {void}
				*/
				this.clear = function () {
					var key;
					for (key in this) {
						if (this.hasOwnProperty(key) && key !== "get" && key !== "set" && key !== "toSaasString" && key !== "clear" && key !== "remove") {
							document.cookie = key + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
							delete this[key];
						}
					}
					return;
				};

				/**
				* Removes a key/value pair from Tealeaf SaaS data.
				* @function
				* @name saas-saasData.remove
				* @returns {void}
				*/
				this.remove = function (key) {
					if (this.hasOwnProperty(key) && key !== "get" && key !== "set" && key !== "toSaasString" && key !== "clear" && key !== "remove") {
						document.cookie = key + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
						delete this[key];
					}
					return;
				};

				/**
				* Converts Tealeaf SaaS session key/value pairs into a semi-colon separated string
				* @function
				* @name saas-saasData.toString
				* @return {string} Key/value pairs in a semi-colon separated string like "key1=value1;key2=value2..."
				*/
				this.toSaasString = function () {
					var saasDataString = "",
						key;
					for (key in this) {
						if (this.hasOwnProperty(key) && typeof this.get(key) === "string") {
							saasDataString += key + "=" + this.get(key) + ";";
						}
					}
					return saasDataString;
				};
			}
		},
		_saasData = new SaasData();

    // Return the module's interface object. This contains callback functions which
    // will be invoked by the UIC core.
    return {
        init: function () {
            // Attach any custom event handlers here
			TLT.saasData = _saasData;
        },
        destroy: function () {
            // Detach any custom event handlers here
        },
        onevent: function (webEvent) {
            // Process DOM events that you registered in the configuration as
            // per your customized requirements
        }
    };

});

var changeTarget;
TLT.init({
  "services": {
    "browser": {
      "jQueryObject": "window.jQuery"
    },
    "queue": {
      "queues": [
        {
          "qid": "DEFAULT",
          "endpoint": "/TealeafTarget.php",
          "maxEvents": 25,
          "timerinterval": 0
        }
      ],
      "asyncReqOnUnload": false
    },
    "serializer": {
      "json": {
        "defaultToBuiltin": true,
        "parsers": [],
        "stringifiers": []
      }
    }
  },
  "core": {
    "modules": {
      "performance": {
        "enabled": true,
        "events": [
          {
            "name": "load",
            "target": window
          },
          {
            "name": "unload",
            "target": window
          }
        ]
      },
      "replay": {
        "enabled": true,
        "events": [
          {
            "name": "load",
            "target": window
          },
          {
            "name": "unload",
            "target": window
          },
          {
            "name": "click",
            "recurseFrames": true
          },
          {
            "name": "focus",
            "target": "input, select, textarea, button",
            "recurseFrames": true
          },
          {
            "name": "blur",
            "target": "input, select, textarea, button",
            "recurseFrames": true
          },
          {
            "name": "change",
            "target": changeTarget,
            "recurseFrames": true
          },
          {
            "name": "resize",
            "target": window
          },
          {
            "name": "scroll",
            "target": window
          },
          {
            "name": "hashchange",
            "target": window
          },
          {
            "name": "orientationchange",
            "target": window
          },
          {
            "name": "touchend"
          }
        ]
      },
      "overstat": {
        "enabled": true,
        "events": [
          {
            "name": "click",
            "recurseFrames": true
          },
          {
            "name": "mousemove",
            "recurseFrames": true
          },
          {
            "name": "mouseout",
            "recurseFrames": true
          }
        ]
      }
    },
    "sessionData": {
      "sessionCookieName": "enter"
    }
  },
  "modules": {
    "performance": {
      "calculateRenderTime": true,
      "filter": {
        "navigationStart": true,
        "unloadEventStart": true,
        "unloadEventEnd": true,
        "redirectStart": true,
        "redirectEnd": true,
        "fetchStart": true,
        "domainLookupStart": true,
        "domainLookupEnd": true,
        "connectStart": true,
        "connectEnd": true,
        "secureConnectionStart": true,
        "requestStart": true,
        "responseStart": true,
        "responseEnd": true,
        "domLoading": true,
        "domInteractive": true,
        "domContentLoadedEventStart": true,
        "domContentLoadedEventEnd": true,
        "domComplete": true,
        "loadEventStart": true,
        "loadEventEnd": true
      }
    }
  }
});
;(function($) {

    var
        $body = $('body'),

        TLT = (typeof this.TLT === 'object') ? this.TLT : null,

        TLT_logCustomEvent = function(event, TLT_eventName, TLT_eventData) {
            try {
                console.info('TLT_logCustomEvent', TLT_eventName, TLT_eventData);

                TLT.logCustomEvent(TLT_eventName, TLT_eventData);
            } catch (e) {
                console.error(e);
            }
        },

        TLT_processDOMEvent = function(event, originalEvent) {
            try {
                console.info('TLT_processDOMEvent', originalEvent);

                TLT.processDOMEvent(originalEvent);
            } catch (e) {
                console.error(e);
            }
        }
    ;

    //$body.on('TLT_logCustomEvent', TLT_logCustomEvent);
    $body.on('TLT_processDOMEvent', TLT_processDOMEvent);

})(jQuery);