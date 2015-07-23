/**
 * Базовый класс представления
 *
 * @module      enter.BaseViewClass
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.BaseViewClass',
        [
            'jQuery',
            'underscore',
            'ajaxCall'
        ],
        module
    );
}(
    this.modules,
    function( provide, $, _, ajaxCall ) {
        'use strict';

        var
            BaseViewClass = (function() {
                var
                    viewOptions = ['el', 'events'],

                    /**
                     * =====================
                     * == PRIVATE METHODS ==
                     * =====================
                     */

                    /**
                     * @memberOf    module:enter.BaseViewClass~BaseViewClass#
                     * @method      _setElement
                     * @private
                     */
                    _setElement = function( el ) {
                        this.$el = el instanceof $ ? el : $(el);
                        this.el  = this.$el[0];
                    },

                    /**
                     * @memberOf    module:enter.BaseViewClass~BaseViewClass#
                     * @method      setElement
                     * @private
                     */
                    setElement = function( element ) {
                        this.undelegateEvents();
                        _setElement.call(this, element);
                        this.delegateEvents();

                        return this;
                    },

                    /**
                     * @memberOf    module:enter.BaseViewClass~BaseViewClass#
                     * @method      setAttributes
                     * @private
                     */
                    setAttributes = function( attributes ) {
                      this.$el.attr(attributes);
                    },

                    /**
                     * @memberOf    module:enter.BaseViewClass~BaseViewClass#
                     * @method      createElement
                     * @private
                     */
                    createElement = function(tagName) {
                        return document.createElement(tagName);
                    },

                    /**
                     * @memberOf    module:enter.BaseViewClass~BaseViewClass#
                     * @method      ensureElement
                     * @private
                     */
                    ensureElement = function() {
                        var
                            attrs;

                        if ( !this.el ) {
                            attrs                              = _.extend({}, _.result(this, 'attributes'));
                            if (this.id) attrs.id              = _.result(this, 'id');
                            if (this.className) attrs['class'] = _.result(this, 'className');

                            setElement.call(this, createElement.call(this, _.result(this, 'tagName')));
                            setAttributes.call(this, attrs);
                        } else {
                            setElement.call(this, _.result(this, 'el'));
                        }
                    },

                    /**
                     * Пародия на Backbone.View, а часть кода в заимствована ;)
                     *
                     * @classdesc   Базовый класс представления
                     * @memberOf    module:enter.BaseViewClass~
                     * @constructs  BaseViewClass
                     * @this        {BaseViewClass}
                     *
                     * @param       {Object}    options    args
                     */
                    BaseViewClass = function( options ) {
                        // enforces new
                        // if ( !(this instanceof BaseViewClass) ) {
                        //     return new BaseViewClass(options);
                        // }

                        this.cid      = _.uniqueId('view');
                        this.subViews = {};
                        options       = options || {};

                        _.extend(this, _.pick(options, viewOptions));
                        ensureElement.call(this);

                        this.initialize.apply(this, arguments);
                    };

                /**
                 * ====================
                 * == PUBLIC METHODS ==
                 * ====================
                 */
                _.extend(BaseViewClass.prototype, {
                    /**
                     * Инициализация представления
                     *
                     * @memberOf    module:enter.BaseViewClass~BaseViewClass#
                     * @public
                     */
                    initialize: function() {},

                    /**
                     * Уничтожение представления
                     *
                     * @memberOf    module:enter.BaseViewClass~BaseViewClass#
                     * @public
                     */
                    destroy: function() {
                        var
                            subView;

                        for ( subView in this.subViews ) {
                            if ( this.subViews.hasOwnProperty(subView) ) {
                                if ( typeof this.subViews[subView].off === 'function' ) {
                                    this.subViews[subView].off();
                                }

                                if ( typeof this.subViews[subView].destroy === 'function' ) {
                                    this.subViews[subView].destroy();
                                } else if ( typeof this.subViews[subView].remove === 'function' ) {
                                    this.subViews[subView].remove();
                                }

                                delete this.subViews[subView];
                            }
                        }

                        delete this.subViews;

                        // COMPLETELY UNBIND THE VIEW
                        this.undelegateEvents();

                        this.$el.removeData().unbind();
                        this.$el.empty();
                        this.$el.remove();
                    },

                    undelegateEvents: function() {
                        if ( this.$el ) {
                            this.$el.off('.delegateEvents' + this.cid);
                        }

                        return this;
                    },

                    delegate: function( eventName, selector, listener ) {
                        this.$el.on(eventName + '.delegateEvents' + this.cid, selector, listener);
                    },

                    delegateEvents: function( events ) {
                        var
                            delegateEventSplitter = /^(\S+)\s*(.*)$/,
                            key, method, match;

                        if ( !(events || (events = _.result(this, 'events'))) ) {
                            return this;
                        }

                        this.undelegateEvents();

                        for ( key in events ) {
                            method = events[key];
                            if ( !_.isFunction(method) ) {
                                method = this[events[key]];
                            }

                            if ( !method ) {
                                continue;
                            }

                            match = key.match(delegateEventSplitter);
                            this.delegate(match[1], match[2], _.bind(method, this));
                        }

                        return this;
                    }

                }, ajaxCall);


                BaseViewClass.extend = function( protoProps, staticProps ) {
                    var
                        parent = this,
                        child, Surrogate;

                    // The constructor function for the new subclass is either defined by you
                    // (the "constructor" property in your `extend` definition), or defaulted
                    // by us to simply call the parent's constructor.
                    if ( protoProps && _.has(protoProps, 'constructor') ) {
                      child = protoProps.constructor;
                    } else {
                      child = function() { return parent.apply(this, arguments); };
                    }

                    // Add static properties to the constructor function, if supplied.
                    _.extend(child, parent, staticProps);

                    // Set the prototype chain to inherit from `parent`, without calling
                    // `parent`'s constructor function.
                    Surrogate           = function(){ this.constructor = child; };
                    Surrogate.prototype = parent.prototype;
                    child.prototype     = new Surrogate;

                    // Add prototype properties (instance properties) to the subclass,
                    // if supplied.
                    if ( protoProps ) _.extend(child.prototype, protoProps);

                    // Set a convenience property in case the parent's prototype is needed
                    // later.
                    child.__super__ = parent.prototype;

                    return child;
                };

                return BaseViewClass;

            }());

        provide(BaseViewClass);
    }
);
