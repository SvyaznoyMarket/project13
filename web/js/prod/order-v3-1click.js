;(function(w) {
    w.ENTER.OrderV3 = {
        info: {},
        constructors: {},
        map: {},
        mapOptions: {},
        $map: {},
        kladrCityId: 0
    };

    $('body').bind('userLogged', function(e, data) {
        try {
            var
                $form = $('.jsOrderV3OneClickForm'),
                user = data.user

            ;

            if (user) {
                if (user.email) {
                    $form.find('[name="user_info[email]"]').val(user.email);
                }
                if (user.name) {
                    $form.find('[name="user_info[first_name]"]').val(user.name);
                }
                if (user.mobile) {
                    $form.find('[name="user_info[mobile]"]').val(w.ENTER.utils.Base64.decode(user.mobile));
                }
            }
        } catch (e) {
            console.error(e);
        }
    });
}(window));
(function($) {
    $.kladr = {};
    
    // Service URL
//    $.kladr.url = 'http://kladr-api.ru/api.php';
    $.kladr.url = 'http://kladr.enter.ru/api.php';

    // Enum KLADR object types
    $.kladr.type = {
        region: 'region',
        district: 'district',
        city: 'city',
        street: 'street',
        building: 'building'
    };
    
    // Send query to service
    $.kladr.api = function(query, callback) {
        var params = {};
        
        if( query.token ) params.token = query.token;
        if( query.key ) params.key = query.key;
        if( query.type ) params.contentType = query.type;
        if( query.name ) params.query = query.name;
        
        if( query.parentType && query.parentId ){
            params[query.parentType+'Id'] = query.parentId;
        }
        
        if( query.withParents ) params.withParent = 1;
        params.limit = query.limit ? query.limit : 2000;
        
        var completed = false;
        
        $.getJSON($.kladr.url + "?callback=?",
            params,
            function(data) {
                if(completed) return;
                completed = true;                
                callback && callback( data.result );
            }
        );
            
        setTimeout(function() {
            if(completed) return;
            completed = true;   
            console.error('Request error');
            callback && callback( [] );
        }, 5000);
    };
    
    // Check existence object
    $.kladr.check = function(query, callback) {
        query.withParents = false;
        query.limit = 1;
        
        $.kladr.api(query, function(objs) {
            if(objs && objs.length){
                callback && callback(objs[0]); 
            } else {
                callback && callback(false);
            }
        });
    };
})(jQuery);

(function($, undefined) {
    $.fn.kladr = function(param1, param2) {
        
        var result = undefined;        
        this.each(function() {
            var res = kladr($(this), param1, param2);
            if(result == undefined) result = res;
        });
        
        return result;
        
        function kladr(input, param1, param2) {
            var ac = null;        
            var spinner = null;

            var options = null;
            var defaultOptions = {
                token: null,
                key: null,
                type: null,
                parentType: null,
                parentId: null,
                limit: 10,
                withParents: false,
                verify: false,
                showSpinner: true,
                arrowSelect: true,
                current: null,

                open: null,
                close: null,
                send: null,
                received: null,
                select: null,
                check: null,

                source: function(query, callback) {
                    var params = {
                        token: options.token,
                        key: options.token,
                        type: options.type,
                        name: query,
                        parentType: options.parentType,
                        parentId: options.parentId,
                        withParents: options.withParents,
                        limit: options.limit
                    };

                    $.kladr.api(params, callback);
                },

                labelFormat: function(obj, query) {
                    var label = '';

                    var name = obj.name.toLowerCase();
                    query = query.toLowerCase();

                    var start = name.indexOf(query);
                    start = start > 0 ? start : 0;

                    if(obj.typeShort){
                        label += obj.typeShort + '. ';
                    }

                    if(query.length < obj.name.length){
                        label += obj.name.substr(0, start);
                        label += '<strong>' + obj.name.substr(start, query.length) + '</strong>';
                        label += obj.name.substr(start+query.length, obj.name.length-query.length-start);
                    } else {
                        label += '<strong>' + obj.name + '</strong>';
                    }

                    return label;
                },

                valueFormat: function(obj, query) {
                    return obj.name;
                }
            };

            var keys = {
                up:    38,
                down:  40,
                esc:   27,
                enter: 13
            };

            var spinnerInterval = null;
            
            return init(param1, param2, function() {
                var isActive = false;

                create(); 
                position();

                input.keyup(open);
                input.keydown(keyselect);
                input.change(function(){
                    if(!isActive) change();
                });
                input.blur(function(){
                    if(!isActive) close();
                });

                ac.on('click', 'li, a', mouseselect);
                ac.on('mouseenter', 'li', function(){ 
                    var $this = $(this);
                    
                    ac.find('li.active').removeClass('active');
                    $this.addClass('active');
                    
                    var obj = $this.find('a').data('kladr-object');
                    trigger('preselect', obj);
                    
                    isActive = true;
                });
                ac.on('mouseleave', 'li', function(){
                    $(this).removeClass('active'); 
                    isActive = false;
                });

                $(window).resize(position);
            });

            function init( param1, param2, callback ) {
                options = input.data('kladr-options');

                if(param2 !== undefined){
                    options[param1] = param2;
                    input.data('kladr-options', options);
                    return input;
                }

                if($.type(param1) === 'string'){
                    if(!options) return null;
                    return options[param1];
                }

                if(options){
                    return input;
                }

                options = defaultOptions;
                if($.type(param1) === 'object'){
                    for(var i in param1){
                        options[i] = param1[i];
                    }
                }

                input.data('kladr-options', options);
                callback && callback();
                return input;
            };

            function create() {
                var container = $(document.getElementById('kladr_autocomplete'));
                var inputName = input.attr('name');

                if(!container.length){
                    container = $('<div id="kladr_autocomplete"></div>').appendTo('body');
                }

                input.attr('autocomplete', 'off');

                ac = $('<ul class="kladr_autocomplete_'+inputName+'" style="display: none;"></ul>');
                ac.appendTo(container); 

                spinner = $('<div class="spinner kladr_autocomplete_'+inputName+'_spinner" class="spinner" style="display: none;"></div>');
                spinner.appendTo(container);
            };
            
            function render(objs, query) {        
                ac.empty();  
                for(var i in objs){
                    var obj = objs[i];                
                    var value = options.valueFormat(obj, query);
                    var label = options.labelFormat(obj, query);

                    var a = $('<a data-val="'+value+'">'+label+'</a>');
                    a.data('kladr-object', obj);

                    var li = $('<li></li>').append(a);                
                    li.appendTo(ac);
                }
            };

            function position() {
                var inputOffset = input.offset();
                var inputWidth = input.outerWidth();
                var inputHeight = input.outerHeight();

                ac.css({
                   top:  inputOffset.top + inputHeight + 'px',
                   left: inputOffset.left
                });

                var differ = ac.outerWidth() - ac.width();
                ac.width(inputWidth - differ);

                var spinnerWidth = spinner.width();
                var spinnerHeight = spinner.height();

                spinner.css({
                    top:  inputOffset.top + (inputHeight - spinnerHeight)/2 - 1,
                    left: inputOffset.left + inputWidth - spinnerWidth - 2,
                });
            };

            function open(event) {
                // return on keyup control keys
                if((event.which > 8) && (event.which < 46)) return;

                if(!validate()) return;

                var query = key(input.val());
                if(!$.trim(query)){
                    close();
                    return;
                }

                spinnerShow();
                trigger('send');

                options.source(query, function(objs) {
                    spinnerHide();
                    trigger('received');

                    if(!input.is(':focus')){
                        close();
                        return;
                    }

                    if(!$.trim(input.val()) || !objs.length){
                        close();
                        return;
                    } 

                    render(objs, query);
                    position();  
                    ac.slideDown(50);
                    trigger('open');
                });
            };

            function close() {
                select();            
                ac.hide();
                trigger('close');
            };
            
            function validate() {
                switch(options.type){
                    case $.kladr.type.region:
                    case $.kladr.type.district:
                    case $.kladr.type.city:
                        if(options.parentType && !options.parentId)
                        {
                            console.error('parentType is defined and parentId in not');
                            return false;
                        }
                        break;
                    case $.kladr.type.street:
                        if(options.parentType != $.kladr.type.city){
                            console.error('For street parentType must equal "city"');
                            return false;
                        }
                        if(!options.parentId){
                            console.error('For street parentId must defined');
                            return false;
                        }
                        break;
                    case $.kladr.type.building:
                        if(options.parentType != $.kladr.type.street){
                            console.error('For building parentType must equal "street"');
                            return false;
                        }
                        if(!options.parentId){
                            console.error('For building parentId must defined');
                            return false;
                        }
                        break;
                    default:
                        console.error('type must defined and equal "region", "district", "city", "street" or "building"');
                        return false;
                }

                if(options.limit < 1){
                    console.error('limit must greater than 0');
                    return false;
                }

                return true;
            };
            
            function select() {
                var a = ac.find('.active a');
                if(!a.length) return;

                input.val(a.attr('data-val'));
                options.current = a.data('kladr-object');
                input.data('kladr-options', options);
                trigger('select', options.current);
            }; 
            
            function keyselect(event) {
                var active = ac.find('li.active');  
                switch(event.which){
                    case keys.up:
                        if(active.length) {
                            active.removeClass('active');
                            active = active.prev();
                        } else {
                            active = ac.find('li').last();
                        }
                        active.addClass('active');
                        
                        var obj = active.find('a').data('kladr-object');
                        trigger('preselect', obj);
                        
                        if(options.arrowSelect) select();
                        break;
                    case keys.down:                    
                        if(active.length) {
                            active.removeClass('active');
                            active = active.next();
                        } else {
                            active = ac.find('li').first();
                        }
                        active.addClass('active');
                        
                        var obj = active.find('a').data('kladr-object');
                        trigger('preselect', obj);
                        
                        if(options.arrowSelect) select();
                        break;
                    case keys.esc:
                        active.removeClass('active');
                        close();
                        break;
                    case keys.enter:
                        if(!options.arrowSelect) select();
                        active.removeClass('active');
                        close();
                        return false;
                }
            };
            
            function mouseselect() {
                close();
                input.focus();
                return false;
            };
            
            function change() {
                if(!options.verify) return;

                if(!validate()) return;

                var query = key(input.val());
                if(!$.trim(query)) return;

                spinnerShow();
                trigger('send');

                options.source(query, function(objs) {
                    spinnerHide();
                    trigger('received');

                    var obj = null;                
                    for(var i=0; i<objs.length; i++){
                        var queryLowerCase = query.toLowerCase();
                        var nameLowerCase = objs[i].name.toLowerCase();
                        if(queryLowerCase == nameLowerCase){
                            obj = objs[i];
                            break;
                        }
                    }

                    if(obj) input.val(options.valueFormat(obj, query));

                    options.current = obj;
                    input.data('kladr-options', options);
                    trigger('check', options.current);
                });
            };

            function key(val) {
                var en = "1234567890qazwsxedcrfvtgbyhnujmik,ol.p;[']- " +
                         "QAZWSXEDCRFVTGBYHNUJMIK<OL>P:{\"} ";

                var ru = "1234567890йфяцычувскамепинртгоьшлбщдюзжхэъ- " +
                         "ЙФЯЦЫЧУВСКАМЕПИНРТГОЬШЛБЩДЮЗЖХЭЪ ";

                var strNew = '';
                var ch;
                var index;
                for( var i=0; i<val.length; i++ ){
                    ch = val[i];                    
                    index = en.indexOf(ch);

                    if(index > -1){
                        strNew += ru[index];
                        continue;
                    }

                    strNew += ch;
                }

                return strNew;
            };

            function trigger(event, obj) {
                if(!event) return;
                input.trigger('kladr_'+event, obj);
                if(options[event]) options[event].call(input.get(0), obj);
            };

            function spinnerStart() {
                if(spinnerInterval) return;

                var top = -0.2;
                spinnerInterval = setInterval(function() {
                    if(!spinner.is(':visible')){
                        clearInterval(spinnerInterval);
                        spinnerInterval = null;
                        return;
                    }

                    spinner.css('background-position', '0% '+top+'%');

                    top += 5.555556;
                    if(top > 95) top = -0.2;
                }, 30);
            };

            function spinnerShow() {
                if(options.showSpinner) {
                    spinner.show();
                    spinnerStart();
                }
            };

            function spinnerHide() {
                spinner.hide();
            };
        };
    };
})(jQuery);

/**
 * Copyright (c) 2011-2014 Felix Gnass
 * Licensed under the MIT license
 */
(function(root, factory) {

  /* CommonJS */
  if (typeof exports == 'object')  module.exports = factory()

  /* AMD module */
  else if (typeof define == 'function' && define.amd) define(factory)

  /* Browser global */
  else root.Spinner = factory()
}
(this, function() {
  "use strict";

  var prefixes = ['webkit', 'Moz', 'ms', 'O'] /* Vendor prefixes */
    , animations = {} /* Animation rules keyed by their name */
    , useCssAnimations /* Whether to use CSS animations or setTimeout */

  /**
   * Utility function to create elements. If no tag name is given,
   * a DIV is created. Optionally properties can be passed.
   */
  function createEl(tag, prop) {
    var el = document.createElement(tag || 'div')
      , n

    for(n in prop) el[n] = prop[n]
    return el
  }

  /**
   * Appends children and returns the parent.
   */
  function ins(parent /* child1, child2, ...*/) {
    for (var i=1, n=arguments.length; i<n; i++)
      parent.appendChild(arguments[i])

    return parent
  }

  /**
   * Insert a new stylesheet to hold the @keyframe or VML rules.
   */
  var sheet = (function() {
    var el = createEl('style', {type : 'text/css'})
    ins(document.getElementsByTagName('head')[0], el)
    return el.sheet || el.styleSheet
  }())

  /**
   * Creates an opacity keyframe animation rule and returns its name.
   * Since most mobile Webkits have timing issues with animation-delay,
   * we create separate rules for each line/segment.
   */
  function addAnimation(alpha, trail, i, lines) {
    var name = ['opacity', trail, ~~(alpha*100), i, lines].join('-')
      , start = 0.01 + i/lines * 100
      , z = Math.max(1 - (1-alpha) / trail * (100-start), alpha)
      , prefix = useCssAnimations.substring(0, useCssAnimations.indexOf('Animation')).toLowerCase()
      , pre = prefix && '-' + prefix + '-' || ''

    if (!animations[name]) {
      sheet.insertRule(
        '@' + pre + 'keyframes ' + name + '{' +
        '0%{opacity:' + z + '}' +
        start + '%{opacity:' + alpha + '}' +
        (start+0.01) + '%{opacity:1}' +
        (start+trail) % 100 + '%{opacity:' + alpha + '}' +
        '100%{opacity:' + z + '}' +
        '}', sheet.cssRules.length)

      animations[name] = 1
    }

    return name
  }

  /**
   * Tries various vendor prefixes and returns the first supported property.
   */
  function vendor(el, prop) {
    var s = el.style
      , pp
      , i

    prop = prop.charAt(0).toUpperCase() + prop.slice(1)
    for(i=0; i<prefixes.length; i++) {
      pp = prefixes[i]+prop
      if(s[pp] !== undefined) return pp
    }
    if(s[prop] !== undefined) return prop
  }

  /**
   * Sets multiple style properties at once.
   */
  function css(el, prop) {
    for (var n in prop)
      el.style[vendor(el, n)||n] = prop[n]

    return el
  }

  /**
   * Fills in default values.
   */
  function merge(obj) {
    for (var i=1; i < arguments.length; i++) {
      var def = arguments[i]
      for (var n in def)
        if (obj[n] === undefined) obj[n] = def[n]
    }
    return obj
  }

  /**
   * Returns the absolute page-offset of the given element.
   */
  function pos(el) {
    var o = { x:el.offsetLeft, y:el.offsetTop }
    while((el = el.offsetParent))
      o.x+=el.offsetLeft, o.y+=el.offsetTop

    return o
  }

  /**
   * Returns the line color from the given string or array.
   */
  function getColor(color, idx) {
    return typeof color == 'string' ? color : color[idx % color.length]
  }

  // Built-in defaults

  var defaults = {
    lines: 12,            // The number of lines to draw
    length: 7,            // The length of each line
    width: 5,             // The line thickness
    radius: 10,           // The radius of the inner circle
    rotate: 0,            // Rotation offset
    corners: 1,           // Roundness (0..1)
    color: '#000',        // #rgb or #rrggbb
    direction: 1,         // 1: clockwise, -1: counterclockwise
    speed: 1,             // Rounds per second
    trail: 100,           // Afterglow percentage
    opacity: 1/4,         // Opacity of the lines
    fps: 20,              // Frames per second when using setTimeout()
    zIndex: 2e9,          // Use a high z-index by default
    className: 'spinner', // CSS class to assign to the element
    top: '50%',           // center vertically
    left: '50%',          // center horizontally
    position: 'absolute'  // element position
  }

  /** The constructor */
  function Spinner(o) {
    this.opts = merge(o || {}, Spinner.defaults, defaults)
  }

  // Global defaults that override the built-ins:
  Spinner.defaults = {}

  merge(Spinner.prototype, {

    /**
     * Adds the spinner to the given target element. If this instance is already
     * spinning, it is automatically removed from its previous target b calling
     * stop() internally.
     */
    spin: function(target) {
      this.stop()

      var self = this
        , o = self.opts
        , el = self.el = css(createEl(0, {className: o.className}), {position: o.position, width: 0, zIndex: o.zIndex})
        , mid = o.radius+o.length+o.width

      css(el, {
        left: o.left,
        top: o.top
      })
        
      if (target) {
        target.insertBefore(el, target.firstChild||null)
      }

      el.setAttribute('role', 'progressbar')
      self.lines(el, self.opts)

      if (!useCssAnimations) {
        // No CSS animation support, use setTimeout() instead
        var i = 0
          , start = (o.lines - 1) * (1 - o.direction) / 2
          , alpha
          , fps = o.fps
          , f = fps/o.speed
          , ostep = (1-o.opacity) / (f*o.trail / 100)
          , astep = f/o.lines

        ;(function anim() {
          i++;
          for (var j = 0; j < o.lines; j++) {
            alpha = Math.max(1 - (i + (o.lines - j) * astep) % f * ostep, o.opacity)

            self.opacity(el, j * o.direction + start, alpha, o)
          }
          self.timeout = self.el && setTimeout(anim, ~~(1000/fps))
        })()
      }
      return self
    },

    /**
     * Stops and removes the Spinner.
     */
    stop: function() {
      var el = this.el
      if (el) {
        clearTimeout(this.timeout)
        if (el.parentNode) el.parentNode.removeChild(el)
        this.el = undefined
      }
      return this
    },

    /**
     * Internal method that draws the individual lines. Will be overwritten
     * in VML fallback mode below.
     */
    lines: function(el, o) {
      var i = 0
        , start = (o.lines - 1) * (1 - o.direction) / 2
        , seg

      function fill(color, shadow) {
        return css(createEl(), {
          position: 'absolute',
          width: (o.length+o.width) + 'px',
          height: o.width + 'px',
          background: color,
          boxShadow: shadow,
          transformOrigin: 'left',
          transform: 'rotate(' + ~~(360/o.lines*i+o.rotate) + 'deg) translate(' + o.radius+'px' +',0)',
          borderRadius: (o.corners * o.width>>1) + 'px'
        })
      }

      for (; i < o.lines; i++) {
        seg = css(createEl(), {
          position: 'absolute',
          top: 1+~(o.width/2) + 'px',
          transform: o.hwaccel ? 'translate3d(0,0,0)' : '',
          opacity: o.opacity,
          animation: useCssAnimations && addAnimation(o.opacity, o.trail, start + i * o.direction, o.lines) + ' ' + 1/o.speed + 's linear infinite'
        })

        if (o.shadow) ins(seg, css(fill('#000', '0 0 4px ' + '#000'), {top: 2+'px'}))
        ins(el, ins(seg, fill(getColor(o.color, i), '0 0 1px rgba(0,0,0,.1)')))
      }
      return el
    },

    /**
     * Internal method that adjusts the opacity of a single line.
     * Will be overwritten in VML fallback mode below.
     */
    opacity: function(el, i, val) {
      if (i < el.childNodes.length) el.childNodes[i].style.opacity = val
    }

  })


  function initVML() {

    /* Utility function to create a VML tag */
    function vml(tag, attr) {
      return createEl('<' + tag + ' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">', attr)
    }

    // No CSS transforms but VML support, add a CSS rule for VML elements:
    sheet.addRule('.spin-vml', 'behavior:url(#default#VML)')

    Spinner.prototype.lines = function(el, o) {
      var r = o.length+o.width
        , s = 2*r

      function grp() {
        return css(
          vml('group', {
            coordsize: s + ' ' + s,
            coordorigin: -r + ' ' + -r
          }),
          { width: s, height: s }
        )
      }

      var margin = -(o.width+o.length)*2 + 'px'
        , g = css(grp(), {position: 'absolute', top: margin, left: margin})
        , i

      function seg(i, dx, filter) {
        ins(g,
          ins(css(grp(), {rotation: 360 / o.lines * i + 'deg', left: ~~dx}),
            ins(css(vml('roundrect', {arcsize: o.corners}), {
                width: r,
                height: o.width,
                left: o.radius,
                top: -o.width>>1,
                filter: filter
              }),
              vml('fill', {color: getColor(o.color, i), opacity: o.opacity}),
              vml('stroke', {opacity: 0}) // transparent stroke to fix color bleeding upon opacity change
            )
          )
        )
      }

      if (o.shadow)
        for (i = 1; i <= o.lines; i++)
          seg(i, -2, 'progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)')

      for (i = 1; i <= o.lines; i++) seg(i)
      return ins(el, g)
    }

    Spinner.prototype.opacity = function(el, i, val, o) {
      var c = el.firstChild
      o = o.shadow && o.lines || 0
      if (c && i+o < c.childNodes.length) {
        c = c.childNodes[i+o]; c = c && c.firstChild; c = c && c.firstChild
        if (c) c.opacity = val
      }
    }
  }

  var probe = css(createEl('group'), {behavior: 'url(#default#VML)'})

  if (!vendor(probe, 'transform') && probe.adj) initVML()
  else useCssAnimations = vendor(probe, 'animation')

  return Spinner

}));

;(function($) {

    var body = $(document.body),
        _gaq = window._gaq,
        region = $('.jsChangeRegion').text().trim(),

        sendAnalytic = function sendAnalyticF (category, action, label, value) {
        var lbl = label || '',
            act = action || '';

        if (category && category.data) {
            if (category.data.step) act = category.data.step;
            if (category.data.action) lbl = category.data.action;
        }

        if (typeof ga === 'undefined') ga = window[window['GoogleAnalyticsObject']]; // try to assign ga

        // sending
        if (typeof _gaq === 'object') _gaq.push(['_trackEvent', 'Воронка_1 клик_' + region, act, lbl]);
        if (typeof ga === 'function') ga('send', 'event', 'Воронка_1 клик_' + region, act, lbl);

        // log to console
        if (typeof ga !== 'function') console.warn('Нет объекта ga');
        if (typeof ga === 'function' && typeof ga.getAll === 'function' &&  ga.getAll().length == 0) console.warn('Не установлен трекер для ga');
        console.log('[Google Analytics] Send event: category: "Воронка_1 клик_%s", action: "%s", label: "%s"', region, act, lbl);
    };

    // common listener for triggering from another files or functions
    body.on('trackUserAction.orderV3Tracking', sendAnalytic);

    // TODO вынести инициализацию трекера из ports.js
    if (typeof ga === 'function' && typeof ga.getAll === 'function' && ga.getAll().length == 0) {
        ga( 'create', 'UA-25485956-5', 'enter.ru' );
    }

})(jQuery);
;(function($){

    var $orderContent = $('#js-order-content');

    $orderContent.on('click', '.orderCol_data-count', function(e){
        var $this = $(this);
        e.stopPropagation();
        $this.hide().siblings('.orderCol_data-summ, .orderCol_data-price').hide();
        $this.siblings('.orderCol_data-edit').show();
    });

    $orderContent.on('click', '.bCountSection__eP, .bCountSection__eM', function(e){

        var $this = $(this),
            $input = $this.siblings('input'),
            stock = parseInt($input.data('stock'), 10),
            quantity = parseInt($input.val(), 10);

        if ($this.hasClass('bCountSection__eP')) {
            if (stock > quantity) $input.val(quantity + 1);
        }

        if ($this.hasClass('bCountSection__eM')) {
            if (quantity > 1) $input.val(quantity - 1);
        }

        e.preventDefault();
        e.stopPropagation();

    });

    $orderContent.on('change', '.bCountSection__eNum', function(e){
        e.stopPropagation();
    });


    function _counter() {

    }

    window.ENTER.OrderV3.constructors.counter = _counter;

}(jQuery));
;(function($) {

    function inputAddress(){
        var $body = $(document.body),
			$orderContent = $('#js-order-content'),
            config = $('#kladr-config').data('value'),
            $addressBlock = $('.orderCol_addrs'),
            $input = $addressBlock.find('input'),
            $inputPrefix = $addressBlock.find('#addressInputPrefix'),
            typeNames = {
                street: 'Улица',
                building: 'дом',
                apartment: 'квартира'
            },
            spinner = typeof Spinner == 'function' ? new Spinner({
                lines: 7, // The number of lines to draw
                length: 3, // The length of each line
                width: 3, // The line thickness
                radius: 2, // The radius of the inner circle
                corners: 1, // Corner roundness (0..1)
                rotate: 0, // The rotation offset
                direction: 1, // 1: clockwise, -1: counterclockwise
                color: '#666', // #rgb or #rrggbb or array of colors
                speed: 1, // Rounds per second
                trail: 60, // Afterglow percentage
                shadow: false, // Whether to render a shadow
                hwaccel: true, // Whether to use hardware acceleration
                className: 'spinner', // The CSS class to assign to the spinner
                zIndex: 2e9, // The z-index (defaults to 2000000000)
                top: '50%', // Top position relative to parent
                left: '50%' // Left position relative to parent
            }) : null,
            address, init, autocompleteRequest, spinnerBlock;

        if ($input.length === 0) return;

        spinnerBlock = $('<div />', {'class':'kladr_spinner'}).css({'position': 'absolute', top: 0, right: 0, height: '30px', width: '30px'});
        $addressBlock.prepend(spinnerBlock);

        function Address(c) {
            this.city = c;
            this.street = {};
            this.building = {};
            this.apartment = {};

            this.getParent = function() {
                //console.log('getParent()');
                if (this.street.id && !this.building.name) return { parentType: this.street.contentType, parentId: this.street.id, type: $.kladr.type.building };
                if (this.city.id && !this.street.name) return { parentType: this.city.contentType, parentId: this.city.id };
                else return false;
            };

            this.getLastType = function() {
                //console.log('getLastType()', this);
                if (typeof this.street.name === 'undefined') return 'street';
                else if (typeof this.building.name === 'undefined') return 'building';
                else if (typeof this.apartment.name === 'undefined') return 'apartment';
                else return false;
            };

            this.getNextType = function() {
                //console.log('getNextType()', this);
                //console.log('typeof this.street.name', typeof this.street.name);

                if (typeof this.building.name !== 'undefined') return 'apartment';
                else if (typeof this.street.name !== 'undefined') return 'building';
                else if (typeof this.street.name === 'undefined') return 'street';
                else return false;
            };

            this.update = function(item) {
                if (typeof item.contentType === 'undefined') {
                    if (item.type === false) {
                        console.error('False type in address update', item);
                        return;
                    }
                    item.contentType = item.type;
                }
                //console.log('update(), contentType', item.contentType);
                this[item.contentType] = item;
                $input.autocomplete('close').val('');
                addAddressItem(item);
                updatePrefix($('input:focus').eq(0));
                if (item.contentType == 'apartment') $input.parent().remove();

                // немного аналитики
                if (item.contentType == 'street') {
                    $body.trigger('trackUserAction', ['2_2 Ввод_данных_Самовывоза|Доставки'])
                } else if (item.contentType == 'building') {
                    $body.trigger('trackUserAction', ['2_2 Ввод_данных_Самовывоза|Доставки'])
                }

                //console.log('Address update: address, item', this, item);
                ENTER.OrderV3.address = this;
            };

            this.clear = function(til, elem) {
                var $elem = $('.jsAddressItem[data-type='+til+']');
                switch (til) {
                    case 'apartment': this.apartment = {}; break;
                    case 'building' : this.apartment = {}; this.building = {}; break;
                    case 'street'   : this.apartment = {}; this.building = {}; this.street = {}; break;
                }
                $elem.nextAll('.jsAddressItem').remove();
                if (elem) $(elem).closest('.orderCol_addrs').find('input').val($elem.find('.jsAddressItemName').eq(0).text()).show().focus();
                $elem.remove();
                //console.log('Address cleared til %s', til, this);
            };

            this.clearLast = function(elem) {
                var lastType = $('.jsAddressItem:last').data('type');
                //console.log('lastType', lastType);
                if (lastType) this.clear(lastType, elem);
            };

            return this;
        }

        function saveAddressRequest() {
            $.ajax({
				url: ENTER.utils.generateUrl('orderV3OneClick.delivery'),
                type: 'POST',
                data: {
                    action : 'changeAddress',
                    params : {
                        street: address.street.type + ' ' + address.street.name,
                        building: address.building.name,
                        apartment: address.apartment.name,
                        kladr_id: address.building.id ? address.building.id : address.street.id ? address.street.id : address.city.id
					},
					products : JSON.parse($orderContent.data('param')).products,
					update : 1
                }
            }).fail(function(jqXHR){
                var response = $.parseJSON(jqXHR.responseText);
                if (response.result) {
                    console.error(response.result);
                }
            }).done(function(data){
                //console.log("Query: %s", data.result.OrderDeliveryRequest);
                //console.log("Model:", data.result.OrderDeliveryModel);
                //console.log('Address saved');
            })
        }

        function updatePrefix(elem) {
            var type = address.getLastType(),
                $prefixHolder = $(elem).siblings('#addressInputPrefix');
            if (type !== false) $prefixHolder.text(typeNames[type] + (type == 'apartment' ? ' (необязательно)' : '') + ":");
        }

        /**
         * Генерация HTML
         * @param item
         */
        function addAddressItem(item) {
            var typeName,
                holder = $('<li />', {
                    "class": "orderCol_addrs_fld_i jsAddressItem",
                    "data-item": JSON.stringify(item),
                    "data-type": item.contentType
                });

            typeName = typeof item.id !== 'undefined' ? item.type : typeNames[item.contentType];

            holder.append($('<span />').addClass('orderCol_addrs_fld_n jsAddressItemType').text(typeName)).
                append($('<span />').addClass('orderCol_addrs_fld_val jsAddressItemName').text(item.name));

            holder.insertBefore($input.parent());
        }

        function formatStreetName(elem) {
            var name = elem.name,
                typeShort = elem.typeShort,
                dot = '';
            if ($.inArray(typeShort, ['ул', 'пер', 'пл', 'дор']) != -1) dot = '.';
            if (elem.contentType === 'street') {
                return name + ' ' +  typeShort + dot;
            } else {
                return name;
            }
        }

        function fillAddressBlock(address) {
            $.each(['street', 'building', 'apartment'], function (i,val){
                if (typeof address[val].type !== 'undefined' || typeof address[val].contentType !== 'undefined' ) addAddressItem(address[val]);
            });
        }

        // Удаление пунктов по клику на адресе
        $addressBlock.on('click', '.jsAddressItem', function(e) {
            e.stopPropagation();
            var type = $(this).data('type');
            address.clear(type, this);
        });

        // Клик по блоку адреса
        $addressBlock.on('click', function(e) {
            if (address.getLastType() !== false) {
				var $input = $(this).find('input').eq(0);
				$input.show();

				if (!$input.is(':focus')) {
					$input.focus();
				}
            }
            e.preventDefault();
        });


        /**
         * Запрос к КЛАДР API
         * @param request
         * @param response
         */
        autocompleteRequest = function autoCompleRequestF (request, response) {
            if (address.getParent() !== false) {
                var query = $.extend(config, { limit: 10, type: $.kladr.type.street, name: request.term }, address.getParent());
                if (spinner) spinner.spin($('.kladr_spinner')[0]);
                //console.log('[КЛАДР] запрос: ', query);
                $.kladr.api(query, function (data) {
                    //console.log('[КЛАДР] ответ', data);
                    if (spinner) spinner.stop();
                    response($.map(data, function (elem) {
                        return { label: formatStreetName(elem) , value: elem }
                    }))
                });
            }
        };

        $input.autocomplete({
//            appendTo: '#kladrAutocomplete',
            source: autocompleteRequest,
            minLength: 1,
            select: function( event, ui ) {
                this.value = '';
                address.update(ui.item.value);
                return false;
            },
            focus: function( event, ui ) {
                this.value = ui.item.label;
                event.preventDefault(); // without this: keyboard movements reset the input to ''
                event.stopPropagation(); // without this: keyboard movements reset the input to ''
            },
            change: function( event, ui ) {
            },
            messages: {
                noResults: '',
                results: function() {}
            }
        });

        $input.on({
            focus: function(){
                updatePrefix(this);
                $body.trigger('trackUserAction', ['2_1 Место_самовывоза|Адрес_доставки'])
            },
            blur: function(){
                $inputPrefix.text('');
				if ($(this).val().length > 0) address.update({type: address.getNextType(), name: $(this).val()});
                saveAddressRequest()
            }
        });

        // заполнение адреса по нажатию Enter
        $input.on('keypress', function(e){
            //console.log(address.getNextType());
            if (e.which == 13) {
                //console.log('Enter pressed, address: ', address);
                if ($(this).val().length > 0) {
                    address.update({
                        type: address.getNextType(),
                        name: $(this).val()
                    });
                }

                e.preventDefault();
            }
        });

        // обработка Backspace
        $input.on('keydown', function(e) {
            var key = e.keyCode || e.charCode;
            if (key === 8 && $(this).val().length === 0) {
                address.clearLast(this);
                e.preventDefault();
            }
        });

        /**
         * Рендеринг меню автокомплита
         * @param ul
         * @param items
         * @private
         */
        $input.data('ui-autocomplete')._renderMenu = function( ul, items ) {
            var that = this;
            $.each( items, function( index, item ) {
                that._renderItemData( ul, item );
            });
        };

        /**
         * Рендеринг элемента списка автокомплита
         * @param ul
         * @param item
         * @returns {*}
         * @private
         */
        $input.data('ui-autocomplete')._renderItem = function( ul, item ) {
            return $( "<li>" )
                .attr( "data-value", JSON.stringify(item.value) )
                .append( $( "<a>" ).text( item.label ) )
                .appendTo( ul );
        };

        /**
         * Инициализация: запрос города для дальнейшего поиска адреса
         */
        init = function initF() {
            if (typeof ENTER.OrderV3.address === 'object') {
                address = ENTER.OrderV3.address;
                fillAddressBlock(address);
                $input.hide();

            } else {
                if (spinner) spinner.spin($('.kladr_spinner')[0]);
                address = new Address({});
//                //console.log('Определение адреса КЛАДР, запрос', $.extend(config, {limit: 1, type: $.kladr.type.city, name: $('#region-name').data('value')}));
                $.kladr.api($.extend(config, {'limit': 1, type: $.kladr.type.city, name: $('#region-name').data('value')}), function (data){
                    //console.log('KLADR data', data);
                    var id = data.length > 0 ? data[0].id : 0;
                    if (id==0) console.error('КЛАДР не определил город, конфигурация запроса: ', $.extend(config, {limit: 1, type: $.kladr.type.city, name: $('#region-name').data('value')}));
                    else address.city = data[0];
                    if (spinner) spinner.stop()
                })
            }
        };

        init();
    }

    ENTER.OrderV3.constructors.smartAddress = inputAddress;

    inputAddress();

    //$(document).ajaxComplete(inputAddress);

}(jQuery));
(function($) {

    var E = ENTER.OrderV3,
        $mapContainer = $('#yandex-map-container');

    var init = function() {

        var options = $mapContainer.data('options');

        E.map = new ymaps.Map("yandex-map-container", {
            center: [options.latitude, options.longitude],
            zoom: options.zoom
        },{
            autoFitToViewport: 'always'
        });

        E.map.controls.remove('searchControl')

        E.mapOptions = options;
        E.$map = $mapContainer;

        console.info(E.map);

    };

    if ($mapContainer.length) ymaps.ready(init);

})(jQuery);
;(function($) {

    ENTER.OrderV3 = ENTER.OrderV3 || {};

    //console.log('Model', $('#initialOrderModel').data('value'));

    var body = document.getElementsByTagName('body')[0],
        $body = $(body),
        $orderContent = $('#js-order-content'),
        comment = '',
        spinner = typeof Spinner == 'function' ? new Spinner({
            lines: 11, // The number of lines to draw
            length: 5, // The length of each line
            width: 8, // The line thickness
            radius: 23, // The radius of the inner circle
            corners: 1, // Corner roundness (0..1)
            rotate: 0, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            color: '#666', // #rgb or #rrggbb or array of colors
            speed: 1, // Rounds per second
            trail: 62, // Afterglow percentage
            shadow: false, // Whether to render a shadow
            hwaccel: true, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            top: '50%', // Top position relative to parent
            left: '50%' // Left position relative to parent
        }) : null,
        changeDelivery = function changeDeliveryF (block_name, delivery_method_token) {
            sendChanges('changeDelivery', {'block_name': block_name, 'delivery_method_token': delivery_method_token});
        },
        changeDate = function changeDateF (block_name, timestamp) {
            sendChanges('changeDate', {'block_name': block_name, 'date': timestamp})
        },
        changePoint = function changePointF (block_name, id, token) {
            sendChanges('changePoint', {'block_name': block_name, 'id': id, 'token': token})
        },
        changeInterval = function changeIntervalF(block_name, interval) {
            sendChanges('changeInterval', {'block_name': block_name, 'interval': interval})
        },
        changeProductQuantity = function changeProductQuantityF(block_name, id, quantity) {
            sendChanges('changeProductQuantity', {'block_name': block_name, 'id': id, 'quantity': quantity})
        },
        changePaymentMethod = function changePaymentMethodF(block_name, method, isActive) {
            var params = {'block_name': block_name};
            params[method] = isActive;
            sendChanges('changePaymentMethod', params)
        },
        changeOrderComment = function changeOrderCommentF(comment){
            sendChanges('changeOrderComment', {'comment': comment})
        },
        applyDiscount = function applyDiscountF(block_name, number) {
            var pin = $('[data-block_name='+block_name+']').find('.jsCertificatePinInput').val();
            if (pin != '') applyCertificate(block_name, number, pin);
            else checkCertificate(block_name, number);
        },
        deleteDiscount = function deleteDiscountF(block_name, number) {
            sendChanges('deleteDiscount',{'block_name': block_name, 'number':number})
        },
        checkCertificate = function checkCertificateF(block_name, code){
            $.ajax({
                type: 'POST',
                url: '/certificate-check',
                data: {
                    code: code,
                    pin: '0000'
                }
            }).done(function(data){
                if (data.error_code == 742) {
                    // 742 - Неверный пин
                    //console.log('Сертификат найден');
                    $('[data-block_name='+block_name+']').find('.cuponPin').show();
                } else if (data.error_code == 743) {
                    // 743 - Сертификат не найден
                    sendChanges('applyDiscount',{'block_name': block_name, 'number':code})
                }
            }).always(function(data){
                //console.log('Certificate check response',data);
            })
        },
        applyCertificate = function applyCertificateF(block_name, code, pin) {
            sendChanges('applyCertificate', {'block_name': block_name, 'code': code, 'pin': pin})
        },
        deleteCertificate = function deleteCertificateF(block_name) {
            sendChanges('deleteCertificate', {'block_name': block_name})
        },
        sendChanges = function sendChangesF (action, params) {
            console.info('Sending action "%s" with params:', action, params);

            if ($orderContent.data('shop')) {
                params.shopId = $orderContent.data('shop')
            }

            $.ajax({
                url: '/order-1click/delivery',
                type: 'POST',
                data: {
                    action : action,
                    params : params,
                    products: JSON.parse($orderContent.data('param')).products,
                    update: 1
                },
                beforeSend: function() {
                    $orderContent.fadeOut(500);
                    if (spinner) spinner.spin(body)
                }
            }).fail(function(jqXHR){
                var response = $.parseJSON(jqXHR.responseText);

                if (response.result && response.result.errorContent) {
                    $('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
                }
            }).done(function(data) {
                //console.log("Query: %s", data.result.OrderDeliveryRequest);
                //console.log("Model:", data.result.OrderDeliveryModel);
                $orderContent.empty().html($(data.result.page).html());
                ENTER.OrderV3.constructors.smartAddress();
                $orderContent.find('input[name=address]').focus();
            }).always(function(){
                $orderContent.stop(true, true).fadeIn(200);
                if (spinner) spinner.stop();
            });

        },
        log = function logF(data){
            $.ajax({
                "type": 'POST',
                "data": data,
                "url": '/order/log'
            })
        },
        showMap = function(elem, token) {
            var $currentMap = elem.find('.js-order-map').first(),
                mapData = $currentMap.data('value'),
                mapOptions = ENTER.OrderV3.mapOptions,
                map = ENTER.OrderV3.map;

            if (!token) {
                token = Object.keys(mapData.points)[0];
                $currentMap.siblings('.selShop_l').hide();
                $currentMap.siblings('.selShop_l[data-token='+token+']').show();
            }

            if (mapData) {

                if (!elem.is(':visible')) elem.show();

                map.geoObjects.removeAll();
                map.setCenter([mapOptions.latitude, mapOptions.longitude], mapOptions.zoom);
                $currentMap.append(ENTER.OrderV3.$map.show());
                map.container.fitToViewport();

                for (var i = 0; i < mapData.points[token].length; i++) {
                    var point = mapData.points[token][i],
                        balloonContent = 'Адрес: ' + point.address;

                    if (!point.latitude || !point.longitude) continue;

                    if (point.regtime) balloonContent += '<br /> Время работы: ' + point.regtime;

                    // кнопка "Выбрать магазин"
                    balloonContent += '<br />' + $('<button />', {
                        'text':'Выбрать магазин',
                        'class': 'btnLightGrey jsChangePoint',
                        'data-id': point.id,
                        'data-token': token
                        }
                    )[0].outerHTML;

                    var placemark = new ymaps.Placemark([point.latitude, point.longitude], {
                        balloonContentHeader: point.name,
                        balloonContentBody: balloonContent,
                        hintContent: point.name
                    }, {
                        iconLayout: 'default#image',
                        iconImageHref: point.marker.iconImageHref,
                        iconImageSize: point.marker.iconImageSize,
                        iconImageOffset: point.marker.iconImageOffset
                    });

                    map.geoObjects.add(placemark);
                }

                if (map.geoObjects.getLength() === 1) {
                    map.setCenter(map.geoObjects.get(0).geometry.getCoordinates(), 15);
                } else {
                    map.setBounds(map.geoObjects.getBounds());
                }

            } else {
                console.error('No map data for token = "%s"', token,  elem);
            }

        };

    // TODO change all selectors to .jsMethod

    // клик по крестику на всплывающих окнах
    $orderContent.on('click', '.jsCloseFl', function(e) {
        e.stopPropagation();
        $(this).closest('.popupFl').hide();
        e.preventDefault();
    });

    // клик по "изменить дату" и "изменить место"
    $orderContent.on('click', '.orderCol_date, .js-order-changePlace-link', function(e) {
        var elemId = $(this).data('content');
        e.stopPropagation();
        $('.popupFl').hide();

        if ($(this).hasClass('js-order-changePlace-link')) {
            var token = $(elemId).find('.selShop_l:first').data('token');
            // скрываем все списки точек и показываем первую
            $(elemId).find('.selShop_l').hide().first().show();
            // первая вкладка активная
            $(elemId).find('.selShop_tab').removeClass('selShop_tab-act').first().addClass('selShop_tab-act');
            $(elemId).lightbox_me({
                centered: true,
                closeSelector: '.jsCloseFl',
                removeOtherOnCreate: false
            });
            showMap($(elemId), token);
            $body.trigger('trackUserAction', ['2_1 Место_самовывоза|Адрес_доставки']);
        } else {
            $(elemId).show();
            log({'action':'view-date'});
            //$body.trigger('trackUserAction', ['11 Срок_доставки_Доставка']);
        }

        e.preventDefault();
    });

    // клик по способу доставки
	$body.on('click', '.selShop_tab:not(.selShop_tab-act)', function(){
        var token = $(this).data('token'),
            id = $(this).closest('.popupFl').attr('id');
        // переключение списка магазинов
        $('.selShop_l').hide();
        $('.selShop_l[data-token='+token+']').show();
        // переключение статусов табов
        $('.selShop_tab').removeClass('selShop_tab-act');
        $('.selShop_tab[data-token='+token+']').addClass('selShop_tab-act');
        // показ карты
        showMap($('#'+id), token);
    });

    // клик по "Ввести код скидки"
    $orderContent.on('click', '.jsShowDiscountForm', function(e) {
        e.stopPropagation();
        $(this).hide().parent().next().show();
    });

    // клик по способу доставки
    $orderContent.on('click', '.orderCol_delivrLst li', function() {
        var $elem = $(this);
        if (!$elem.hasClass('orderCol_delivrLst_i-act')) {
//            if ($elem.data('delivery_group_id') == 1) {
//                showMap($elem.parent().siblings('.selShop').first());
//            } else {
                changeDelivery($(this).closest('.orderRow').data('block_name'), $(this).data('delivery_method_token'));
//            }
        }
    });

    // клик по дате в календаре
    $orderContent.on('click', '.celedr_col', function(){
        var timestamp = $(this).data('value');
        if (typeof timestamp == 'number') {
            //$body.trigger('trackUserAction', ['11_1 Срок_Изменил_дату_Доставка']);
            changeDate($(this).closest('.orderRow').data('block_name'), timestamp)
        }
    });

    // клик по списку точек самовывоза
    $body.on('click', '.jsChangePoint', function() {
        var id = $(this).data('id'),
            token = $(this).data('token');
        if (id && token) {
            $body.trigger('trackUserAction', ['2_2 Ввод_данных_Самовывоза|Доставки']);
            $body.children('.selShop, .lb_overlay').remove();
            changePoint($(this).closest('.selShop').data('block_name'), id, token);
        }
    });

    // клик на селекте интервала
    $orderContent.on('click', '.jsShowDeliveryIntervals', function() {
        $(this).find('.customSel_lst').show();
    });

    // клик по интервалу доставки
    $orderContent.on('click', '.customSel_lst li', function() {
        changeInterval($(this).closest('.orderRow').data('block_name'), $(this).data('value'));
    });

    // АНАЛИТИКА

    $body.on('focus', '.jsOrderV3PhoneField', function(){
        $body.trigger('trackUserAction',['1_1 Телефон'])
    });

    $body.on('focus', '.jsOrderV3EmailField', function(){
        $body.trigger('trackUserAction',['1_3 E-mail'])
    });

    $body.on('focus', '.jsOrderV3NameField', function(){
        $body.trigger('trackUserAction',['1_2 Имя'])
    });

	$body.on('click', '.jsOrderOneClickClose', function(e){
		e.preventDefault();
		$(this).closest('#jsOneClickContent').trigger('close');
	});

    // отслеживаем смену региона
    /*
    $body.on('click', 'a.jsChangeRegionAnalytics', function(e){
        var newRegion = $(this).text(),
            oldRegion = $('.jsRegion').data('value'),
            link = $(this).attr('href');

        e.preventDefault();
        // TODO вынести как функцию с проверкой существования ga и немедленным вызовом hitCallback в остуствии ga и трекера
        ga('send', 'event', {
            'eventCategory': 'Воронка_' + oldRegion,
            'eventAction': '8 Регион_Доставка',
            'eventLabel': 'Было: ' + oldRegion + ', Стало: ' + newRegion,
            'hitCallback': function() {
                window.location.href = link;
            }
        });

    })
    */

})(jQuery);
;(function($) {

    var $pageNew = $('#jsOneClickContentPage'),
        $validationErrors = $('.jsOrderValidationErrors'),
		$form = $('.jsOrderV3OneClickForm'),
        errorClass = 'textfield-err',
        validateEmail = function validateEmailF(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        validate = function validateF(){
            var error = [],
                $phoneInput = $('[name=user_info\\[mobile\\]]'),
                $emailInput = $('[name=user_info\\[email\\]]'),
//				$deliveryMethod = $('.orderCol_delivrLst_i-act span'),
                phone = $phoneInput.val().replace(/\s+/g, '');

            if (!/8\(\d{3}\)\d{3}-\d{2}-\d{2}/.test(phone)) {
                error.push('Неверный формат телефона');
                $phoneInput.addClass(errorClass).siblings('.errTx').show();
            } else {
                $phoneInput.removeClass(errorClass).siblings('.errTx').hide();
            }

            if ($emailInput.val().length != 0 && !validateEmail($emailInput.val())) {
                error.push('Неверный формат E-mail');
                $emailInput.addClass(errorClass).siblings('.errTx').show();
            } else {
                $emailInput.removeClass(errorClass).siblings('.errTx').hide();
            }

			/*if ($deliveryMethod.text() == 'Самовывоз' && $('.orderCol_addrs_tx').text().replace(/\s/g, '') == '') {
				error.push('Не выбран адрес доставки или самовывоза');
			}

			if ($deliveryMethod.text() == 'Доставка') {
				if (!ENTER.OrderV3.address || !ENTER.OrderV3.address.building.name) {
					error.push('Не выбран адрес доставки или самовывоза');
				}
			}*/

            return error;
        };

    if ($validationErrors.length) {
        console.warn('Validation errors', $validationErrors);
    }

    $pageNew.on('blur', 'input', function(){
        validate()
    }).on('keyup', '.jsOrderV3PhoneField', function(){
		var val = $(this).val();
		if (val[val.length-1] != '_') validate();
	});


	$form.on('submit', function(e){

		var	$el = $(this),
			$submitBtn = $el.find('.orderCompl_btn'),
			data = $el.serializeArray(),
			error = validate(),
			errorHtml = '';

		if (error.length != 0) {
			console.warn('Ошибки валидации', error);
			$.each(error, function(i,val){ errorHtml += val + '<br/>'});
			$('#OrderV3ErrorBlock').html(errorHtml).show();
			e.preventDefault();
			return;
		}

		$.ajax({
			type: 'POST',
			url: $el.attr('action'),
			data: data,
			beforeSend: function(){
				$submitBtn.attr('disabled', true)
			}
			})
			.always(function(){
				$submitBtn.attr('disabled', false)
			})
			.done(function(response) {
				if (typeof response.result !== 'undefined') {
					$('#jsOneClickContentPage').hide();
					$('#jsOneClickContent').append(response.result.page);

					$('body').trigger('trackUserAction', ['3_1 Оформить_успешно']);

					// Счётчик GetIntent (BlackFriday)
					(function() {
						if (response.result.lastPartner != 'blackfridaysale') {
							return '';
						}

						$.each(response.result.orders, function(index, order) {
							var products = [];
							var revenue = 0;
							$.each(order.products, function(index, product) {
								products.push({
									id: product.id + '',
									price: product.price + '',
									quantity: parseInt(product.quantity)
								});

								revenue += parseFloat(product.price) * parseInt(product.quantity);
							});

							ENTER.counters.callGetIntentCounter({
								type: "CONVERSION",
								orderId: order.id + '',
								orderProducts: products,
								orderRevenue: revenue + ''
							});
						});
					})();

					// Счётчик RetailRocket
					(function() {
						$.each(response.result.orders, function(index, order) {
							var products = [];
							$.each(order.products, function(index, product) {
								products.push({
									id: product.id,
									qnt: product.quantity,
									price: product.price
								});
							});

							ENTER.counters.callRetailRocketCounter('order.complete', {
								transaction: order.id,
								items: products
							});
						});
					})();
				}

				var $orderContainer = $('#jsOrderV3OneClickOrder');
				if ($orderContainer.length) {
					$.get($orderContainer.data('url')).done(function(response) {
						$orderContainer.html(response.result.page);

						if (typeof ENTER.utils.sendOrderToGA == 'function') ENTER.utils.sendOrderToGA($('#jsOrder').data('value'));

					});
				}
			})
			.fail(function(jqXHR){
				var response = $.parseJSON(jqXHR.responseText);

				if (response.result && response.result.errorContent) {
					$('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
				}

				var error = (response.result && response.result.error) ? response.result.error : {};

				$('body').trigger('trackUserAction', ['3_2 Оформить_ошибка', 'Поле ошибки: '+ ((typeof error !== 'undefined') ? error.join(', ') : '')]);
			})
		;

		e.preventDefault();
	})


}(jQuery));