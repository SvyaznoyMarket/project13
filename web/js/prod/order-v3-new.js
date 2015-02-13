;(function(w) {
    w.ENTER.OrderV3 = {
        info: {},
        constructors: {},
        map: {},
        mapOptions: {},
        $map: {},
        kladrCityId: 0
    };
}(window));
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
        region = $('.jsRegion').data('value'),

        sendAnalytic = function sendAnalyticF (category, action, label, value) {
        var lbl = label || '',
            act = action || '';

        if (category && category.data) {
            if (category.data.step) act = category.data.step;
            if (category.data.action) lbl = category.data.action;
        }

        if (typeof ga === 'undefined') ga = window[window['GoogleAnalyticsObject']]; // try to assign ga

        // sending
        if (typeof _gaq === 'object') _gaq.push(['_trackEvent', 'Воронка_новая_v2_' + region, act, lbl]);
        if (typeof ga === 'function') ga('send', 'event', 'Воронка_новая_v2_' + region, act, lbl);

        // log to console
        if (typeof ga !== 'function') console.warn('Нет объекта ga');
        if (typeof ga === 'function' && typeof ga.getAll == 'function' && ga.getAll().length == 0) console.warn('Не установлен трекер для ga');
        console.log('[Google Analytics] Send event: category: "Воронка_новая_v2_%s", action: "%s", label: "%s"', region, act, lbl);
    };

    // common listener for triggering from another files or functions
    body.on('trackUserAction.orderV3Tracking', sendAnalytic);

    // TODO вынести инициализацию трекера из ports.js
    if (typeof ga === 'function' && typeof ga.getAll == 'function' && ga.getAll().length == 0) {
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
;(function(w,ko,$) {

	var address,
		initialAddressData = $('#jsUserAddress').data('value'),
		kladrConfig = $('#kladr-config').data('value'),
		regionName = $('#page-config').data('value').user.region.name;

	function AddressModel () {

		var self = this,
			streetTypeDefault = 'Улица';

		// сокращенные названия улиц из КЛАДРА (для автосогласования в 1C)
		self.streetShortToLong = {
			'пр-кт': 'Проспект',
			'ш': 'Шоссе',
			'ул.': 'Улица',
			'ул': 'Улица',
			'пер': 'Переулок',
			'пл': 'Площадь',
			'дор': 'Дорога'
		};

		self.cityName = ko.observable('');
		self.cityId = ko.observable(0);
		self.streetName = ko.observable('');
		self.streetType = ko.observable(streetTypeDefault);
		self.streetTypeShort = ko.observable('');
		self.streetId = ko.observable(0);
		self.buildingName = ko.observable('');
		self.buildingId = ko.observable(0);
		self.apartmentName = ko.observable('');

		self.inputFocus = ko.observable(true);
		self.inputPrefix = ko.computed(function(){
			if (self.streetName() == '') return 'Улица:';
			else if (self.buildingName() == '') return 'дом:';
			else return 'квартира:';
		});

		// return {type, parentType, parentId} or false
		self.getParent = function(){
			var result = false;
			if (self.cityId() != 0 && self.inputPrefix() == 'Улица:') result = { type: $.kladr.type.street, parentType: 'city', parentId: self.cityId() };
			else if (self.streetId() != 0  && self.inputPrefix() == 'дом:') result = { type: $.kladr.type.building ,parentType: 'street', parentId: self.streetId() };
			return result;
		};

		self.update = function(val) {
			// обработка ручного ввода
			if (typeof val == 'string') {
				if (self.streetName() == '') {
					self.streetName(val);
					self.streetTypeShort('');
				}
				else if (self.buildingName() == '') self.buildingName(val);
				else if (self.apartmentName() == '') self.apartmentName(val);
			}
			// обработка автодополнения
			else if (typeof val == 'object') {
				if (val.contentType == 'street') {
					self.streetName(val.name).streetId(val.id).streetType(val.type).streetTypeShort(val.typeShort)
				}
				else if (val.contentType == 'building') {
					self.buildingName(val.name).buildingId(val.id)
				}
			}
		};

		self.clearCity = function() { self.cityName('').cityId(0); return self };
		self.clearStreet = function() { self.streetName('').streetType(streetTypeDefault).streetId(0); return self};
		self.clearBuilding = function() { self.buildingName('').buildingId(0); return self };
		self.clearApartment = function() { self.apartmentName(''); return self };

		return self;

	}

	function saveAddress(address) {

		if (address.streetName() == '') return;

		$.ajax({
			type: 'POST',
			data: {
				'action' : 'changeAddress',
				'params' : {
					// сохраняем улицу в формате "Название + сокращенный тип" для автосогласования в 1С
					street: address.streetName() + ' ' + (address.streetTypeShort() == '' ? address.streetType() : address.streetTypeShort()),
					building: address.buildingName(),
					apartment: address.apartmentName(),
					kladr_id: address.buildingId() != 0 ? address.buildingId() : address.streetId() != 0 ? address.streetId() : address.cityId() != 0 ? address.cityId() : '' }
			}
		}).fail(function(jqXHR){
			var response = $.parseJSON(jqXHR.responseText);
			if (response.result) {
				console.error(response.result);
			}
		}).done(function(data){
//			console.log("Query: %s", data.result.OrderDeliveryRequest);
			console.log("Saved address:", data.result.OrderDeliveryModel.user_info.address);
		})
	}

	function autoCompleteRequest (request, response) {
		if (address.getParent() !== false) {
			var query = $.extend({}, { limit: 10, name: request.term }, address.getParent());
//			if (spinner) spinner.spin($('.kladr_spinner')[0]);
			console.log('[КЛАДР] запрос: ', query);
			$.kladr.api(query, function (data) {
				console.log('[КЛАДР] ответ', data);
//				if (spinner) spinner.stop();
				response($.map(data, function (elem) {
					return { label: formatStreetName(elem) , value: elem }
				}))
			});
		}
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

	// чтобы вызывать функцию после AJAX-запросов, копируем её в глобальную переменную
	ENTER.OrderV3.constructors.smartAddressInit = function() {
		var $input = $('.jsSmartAddressInput'),
			initKladrQuery = $.extend(kladrConfig, {'limit': 1, type: $.kladr.type.city, name: regionName});

		// jQuery-ui autocomplete from КЛАДР
		$input.autocomplete({
//            appendTo: '#kladrAutocomplete',
			source: autoCompleteRequest,
			minLength: 1,
			open: function( event, ui ) {
				$('.ui-autocomplete').css({'position' : 'absolute', 'top' : 29, 'left' : 0});
			},
			select: function( event, ui ) {
				this.value = '';
				$input.val('');
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

		// Обработка event-ов на поле ввода
		$input.on({
			keypress: function(e){
				// Нажатие ENTER означает ручной ввод улицы, дома, квартиры
				if (e.which == 13) {
					if ($(this).val().length > 0) {
						address.update($(this).val()); // обновляем
						$input.val(''); // очищаем поля ввода
						$input.autocomplete('close'); // скрываем автокомплит
					}
				}
			},
			keydown: function(e){
				// Обработка Backspace
				var key = e.keyCode || e.charCode;
				if (key === 8 && $(this).val().length === 0) {
					if (address.inputPrefix() == 'дом:') {
						$input.val(address.streetName());
						address.clearBuilding().clearStreet();
					}
					if (address.inputPrefix() == 'квартира:') {
						$input.val(address.buildingName());
						address.clearApartment().clearBuilding();
					}
					e.preventDefault();
				}
			},
			blur: function(){
				address.update($(this).val()); // обновляем
				$input.val(''); // очищаем поле ввода
				saveAddress(address);
			}
		});

		// клик по блоку (улица, дом, квартира) в адресе
		$('.jsSmartAddressEditField').on('click', function(){
			var dataType = $(this).data('type');
			if (dataType && typeof address[dataType] == 'function') {
				$input.val(address[dataType]()); // записываем значение в поле ввода
				if (dataType == 'apartmentName') address.clearApartment();
				if (dataType == 'buildingName') address.clearBuilding().clearApartment();
				if (dataType == 'streetName') {
					address.clearStreet().clearBuilding().clearApartment();
					if (address.streetTypeShort() != '') $input.val($input.val() + ' ' + address.streetTypeShort()); // дописываем сокращенное название
				}
			}
		});

		if (kladrConfig && regionName) {
			$.kladr.api(initKladrQuery, function (data){
				var id = data.length > 0 ? data[0].id : 0;
				if (id==0) console.error('КЛАДР не определил город, конфигурация запроса: ', initKladrQuery);
				else address.cityId(data[0].id);
			})
		}

	};

	// начинаем отсюдова

	address = new AddressModel();

	// Заполняем модель данными при загрузке или рефреше страницы
	if (typeof initialAddressData == 'object') {
		if (initialAddressData.street) {
			var regexResult = initialAddressData.street.match(/(.+)\s+(.+)$/);
			if (regexResult) {
				if (address.streetShortToLong.hasOwnProperty(regexResult[2])) {
					address.streetType(address.streetShortToLong[regexResult[2]]);
					address.streetTypeShort(regexResult[2]);
				} else {
					address.streetType(regexResult[2]);
				}
				address.streetName(regexResult[1]);
			}
		}
		if (initialAddressData.building) address.buildingName(initialAddressData.building);
		if (initialAddressData.apartment) address.apartmentName(initialAddressData.apartment);
	}

	ko.applyBindings(address);

	ENTER.OrderV3.address = address;
	ENTER.OrderV3.constructors.smartAddressInit();

}(window, ko, jQuery));
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

    };

    if ($mapContainer.length) ymaps.ready(init);

})(jQuery);
;(function($){
    var body = document.getElementsByTagName('body')[0],
        $body = $(body),
        $orderContent = $('.orderCnt'),
        $jsOrder = $('#jsOrder'),
        region = $('.jsRegion').data('value'),
        isOnlineMotivPage = $('.jsNewOnlineCompletePage').length > 0,
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

        getForm = function getFormF(methodId, orderId, orderNumber, action) {
            var data = {
                'method' : methodId,
                'order': orderId,
                'number': orderNumber
            };
            if (typeof action !== 'undefined' && action != '') data.action = action;
            $.ajax({
                'url': 'getPaymentForm',
                'type': 'POST',
                'data': data,
                'success': function(data) {
                    var $form;
                    if (data.form != '') {
                        $form = $(data.form);
                        if (spinner) spinner.spin(body);

                        if ($form.hasClass('jsPaymentFormPaypal') && typeof $form.attr('action') != 'undefined') {
                            window.location.href = $form.attr('action');
                        } else {
							$body.append($form);
							$form.submit();
                        }
                    }
                    console.log('Payment data', data);

                }
            })
        },

        showCreditWidget = function showCreditWidgetF(bankProviderId, data, number_erp, bank_id) {

            if ( bankProviderId == 1 ) showKupiVKredit(data['kupivkredit']);
            if ( bankProviderId == 2 ) showDirectCredit(data['direct-credit']);

            $.ajax({
                type: 'POST',
                url: '/order/update-credit',
                data: {
                    number_erp: number_erp,
                    bank_id: bank_id
                }
            });

            /* При выборе варианта заявки на кредит */
            if (bank_id == 1) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19_1 Заявка_кредит_Оплата', 'Тинькофф']);
            if (bank_id == 2) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19_1 Заявка_кредит_Оплата', 'Ренесанс']);
            if (bank_id == 3) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19_1 Заявка_кредит_Оплата', 'ОТП-Банк']);

        },

        showKupiVKredit = function showKupiVKreditF(data){
            var callback_close = function(decision) { },
                callback_decision = function(decision) { },
                vkredit;

            console.log(data);

            $LAB.script( '//www.kupivkredit.ru/widget/vkredit.js')
                .wait( function() {
                    vkredit = new VkreditWidget(1, data.vars.sum,  {
                        order: data.vars.order,
                        sig: data.vars.sig,
                        callbackUrl: window.location.href,
                        onClose: callback_close,
                        onDecision: callback_decision
                    });

                    vkredit.openWidget();
                });
        },

        showDirectCredit = function showDirectCreditF(data){
            var productArr = [];

            $LAB.script( '//api.direct-credit.ru/JsHttpRequest.js' )
                .script( '//api.direct-credit.ru/dc.js' )
                .wait( function() {
                    console.info('скрипты загружены для кредитного виджета. начинаем обработку');

                    $.each(data.vars.items, function(index, elem){
                        productArr.push({
                            id: elem.articul,
                            name: elem.name,
                            price: elem.price,
                            type: elem.type,
                            count: elem.quantity
                        })
                    });


                    DCLoans(data.vars.partnerID, 'getCredit', { products: productArr, order: data.vars.number, codeTT: data.vars.region }, function(result){
                       console.log(result);
                    }, false);

            });
        };

    // клик по методу онлайн-оплаты
    $orderContent.on('click', '.jsPaymentMethod', function(){
        var id = $(this).data('value'),
            $order = $(this).closest('.orderLn').length > 0 ? $(this).closest('.orderLn') : $orderContent,
            orderId = $order.data('order-id'),
            orderNumber = $order.data('order-number'),
            action = $order.data('order-action');
        switch (id) {
            case 5:
                getForm(5, orderId, orderNumber, action);
                $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '17_1 Оплатить_онлайн_Оплата', 'Онлайн-оплата']);
                break;
            case 8:
                getForm(8, orderId, orderNumber, action);
                $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '17_1 Оплатить_онлайн_Оплата', 'Psb']);
                break;
            case 13:
                getForm(13, orderId, orderNumber, action);
                $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '17_1 Оплатить_онлайн_Оплата', 'PayPal']);
                break;
			case 14:
				getForm(14, orderId, orderNumber, action);
				$body.trigger('trackUserAction', ['17_1 Оплатить_онлайн_Связной_клуб_баллы']);
				break;
        }
    });

	// Мотивация онлайн-оплаты
    $orderContent.on('click', '.jsOnlinePaymentPossible', function(){
		$(this).hide();
		$orderContent.find('.jsOnlinePaymentBlock').show();
        $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '17 Оплатить_онлайн_вход_Оплата']);
	});

    // Мотивация онлайн-оплаты (купон)
    $orderContent.on('click', '.jsOrderCouponInitial', function(){
        $(this).hide();
        $('.jsOrderCouponExpanded').show();
    });

    // клик по "оплатить онлайн"
    $orderContent.on('click', '.jsOnlinePaymentSpan', function(e){
        $(this).parent().siblings('.jsOnlinePaymentList').show();
        $body.trigger('trackUserAction', ['17 Оплатить_онлайн_вход_Оплата']);
        e.stopPropagation();
    });

    $orderContent.on('click', '.jsOnlinePaymentBlock', function(e) {
        if ($(this).find('.jsOnlinePaymentList').length == 0) $(this).siblings('.jsOnlinePaymentList').show();
        else $(this).find('.jsOnlinePaymentList').show();
        if ( $(this).find('.jsCreditList').length != 0 )  $(this).find('.jsCreditList').show();
        e.stopPropagation();
    });

    // клик на кнопке "Заполнить заявку"
    $orderContent.on('click', '.jsCreditButton', function(e){
        $(this).siblings('.jsCreditList').show();
        e.preventDefault();
        e.stopPropagation();
        $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19 Заявка_кредит_Оплата']);
    });

    // клик по кредитному банку
    $orderContent.on('click', '.jsCreditList li', function(e){
        var bankProviderId = $(this).data('bank-provider-id'),
            bank_id = $(this).data('value'),
            creditData = $(this).parent().siblings('.credit-widget').data('value'),
            order_number_erp = $(this).closest('.orderLn').data('order-number-erp');

		if (typeof order_number_erp == 'undefined') order_number_erp = $orderContent.data('order-number-erp');

        /* При клике условия кредитования */
        if ( $(e.target).hasClass('jsCreditListOnlineMotivRules') ) {
            $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19_2 Условия_кредит_Оплата']);
            return true;
        }

		e.preventDefault();
        e.stopPropagation();

        if (!$(this).closest('ul').hasClass('jsCreditListOnlineMotiv')) $(this).parent().hide();
        showCreditWidget(bankProviderId, creditData, order_number_erp, bank_id);
    });

    $body.on('click', function(){
        if (window.location.pathname == '/order/complete') $('.popupFl').hide();
    });

    // выполняем данный блок только на финальной странице
    if (/order\/complete/.test(window.location.href)) {

        /* АНАЛИТИКА МОТИВАЦИИ ОНЛАЙН-ОПЛАТЫ */
        if (isOnlineMotivPage) {
            // если невозможна онлайн-оплата
            if ($('.jsGAOnlinePaymentNotPossible').length > 0) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '16 Вход_Оплата_ОБЯЗАТЕЛЬНО', 'нет онлайн оплаты']);
            // Без мотиватора
            if ($('.jsOnlinePaymentPossibleNoMotiv').length > 0) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '16 Вход_Оплата_ОБЯЗАТЕЛЬНО', 'нет мотиватора']);
            // При попадании пользователя на экран “Варианты оплаты онлайн”
            if ($('.jsOnlinePaymentBlockVisible').length > 0) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '17 Оплатить_онлайн_вход_Оплата']);
            // При попадании на экран с вариантами заявок на кредит */
            if ($('.jsCreditBlock').length > 0) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '19 Заявка_кредит_Оплата']);
            // При клике на ссылку “как добраться”
            $body.on('click', '.jsCompleteOrderShowShop', function(){ $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '16_1 Как_добраться']); })
        } else {
            $body.trigger('trackUserAction', ['16 Вход_Оплата_ОБЯЗАТЕЛЬНО']);
        }

        // При успешной онлайн-оплате
        if ($('.jsOrderPaid').length > 0) $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '18 Успешная_Оплата']);
    }

    if ($jsOrder.length != 0) {
		if (typeof ENTER.utils.sendOrderToGA == 'function') ENTER.utils.sendOrderToGA($jsOrder.data('value'));
    }

	$(function(){
		var data = $('.js-orderV3New-complete-subscribe').data('value');

		if (data && data.subscribe && data.email) {
			$body.trigger('trackGoogleEvent', {
				category: 'subscription',
				action: 'subscribe_order_confirmation',
				label: data.email
			});
		}
	});
}(jQuery));
;(function($) {

    ENTER.OrderV3 = ENTER.OrderV3 || {};

    console.log('Model', $('#initialOrderModel').data('value'));

    var body = document.getElementsByTagName('body')[0],
        $body = $(body),
        $orderContent = $('#js-order-content'),
        comment = '',
        region = $('.jsRegion').data('value'),
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
                    console.log('Сертификат найден');
                    $('[data-block_name='+block_name+']').find('.cuponPin').show();
                } else if (data.error_code == 743) {
                    // 743 - Сертификат не найден
                    sendChanges('applyDiscount',{'block_name': block_name, 'number':code})
                }
            }).always(function(data){
                console.log('Certificate check response',data);
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
            $.ajax({
                type: 'POST',
                data: {
                    'action' : action,
                    'params' : params
                },
                beforeSend: function() {
                    $orderContent.fadeOut(500);
                    if (spinner) spinner.spin(body)
                }
            }).fail(function(jqXHR){
                var response = $.parseJSON(jqXHR.responseText);
                if (response.result) {
                    console.error(response.result);
                }
                if (response.result.redirect) {
                    window.location.href = response.result.redirect;
                }
            }).done(function(data) {
                console.log("Query: %s", data.result.OrderDeliveryRequest);
                console.log("Model:", data.result.OrderDeliveryModel);
                $orderContent.empty().html($(data.result.page).find('#js-order-content').html());
				if ($orderContent.find('.jsAddressRootNode').length > 0) {
					$.each($orderContent.find('.jsAddressRootNode'), function(i,val){
						ko.applyBindings(ENTER.OrderV3.address, val);
					});
					if (typeof ENTER.OrderV3.constructors.smartAddressInit == 'function') ENTER.OrderV3.constructors.smartAddressInit();
				}
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
		},

		showOfertaPopup = function showOfertaPopupF() {
			$('.js-order-oferta-popup').lightbox_me();
		},

		tabsOfertaAction = function tabsOfertaActionF(that) {
			var $self = $(that),
				tabContent = $('.js-tab-oferta-content'),
				tab_id = $(that).attr('data-tab');

			$('.js-oferta-tab').removeClass('orderOferta_tabs_i-cur');
			tabContent.removeClass('orderOferta_tabcnt-cur');

			$self.addClass('orderOferta_tabs_i-cur');
			$("#"+tab_id).addClass('orderOferta_tabcnt-cur');
		};

    // TODO change all selectors to .jsMethod

    // клик по крестику на всплывающих окнах
    $orderContent.on('click', '.jsCloseFl', function(e) {
        e.stopPropagation();
        $(this).closest('.popupFl').hide();
        e.preventDefault();
    });

	$orderContent.on('click', '.jsAddressRootNode', function() {
		ENTER.OrderV3.address.inputFocus(true);
        $(this).find('.jsSmartAddressInput').focus();
	});

	$orderContent.on('blur', '.jsSmartAddressInput', function() {
		ENTER.OrderV3.address.inputFocus(false);
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
                closeSelector: '.jsCloseFl'
            });
            showMap($(elemId), token);
            $body.trigger('trackUserAction', ['10 Место_самовывоза_Доставка_ОБЯЗАТЕЛЬНО']);
        } else {
            $(elemId).show();
            log({'action':'view-date'});
            $body.trigger('trackUserAction', ['11 Срок_доставки_Доставка']);
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
            $body.trigger('trackUserAction', ['11_1 Срок_Изменил_дату_Доставка']);
            changeDate($(this).closest('.orderRow').data('block_name'), timestamp)
        }
    });

    // клик по списку точек самовывоза
    $body.on('click', '.jsChangePoint', function() {
        var id = $(this).data('id'),
            token = $(this).data('token');
        if (id && token) {
            $body.trigger('trackUserAction', ['10_1 Ввод_данных_Самовывоза_Доставка_ОБЯЗАТЕЛЬНО']);
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

    // клик по ссылке "Применить" у каунтера
    $orderContent.on('click', '.jsChangeProductQuantity', function(e){
        var $this = $(this),
            quantity = $this.parent().find('input').val();
        changeProductQuantity($this.data('block_name'), $this.data('id'), quantity);
        e.preventDefault();
    });

    // клик по ссылке "Удалить" у каунтера
    $orderContent.on('click', '.jsDeleteProduct', function(e){
        var $this = $(this);
        changeProductQuantity($this.data('block_name'), $this.data('id'), 0);
        e.preventDefault();
    });

    // клик по безналичному методу оплаты
    $orderContent.on('change', '.jsCreditCardPayment', function(){
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name');
        if ($this.is(':checked')) $body.trigger('trackUserAction', ['13_1 Оплата_банковской_картой_Доставка']);
        changePaymentMethod(block_name, 'by_credit_card', $this.is(':checked'))
    });

    // клик по "купить в кредит"
    $orderContent.on('change', '.jsCreditPayment', function() {
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name');
        if ($this.is(':checked')) $body.trigger('trackUserAction', ['13_2 Оплата_в_кредит_Доставка']);
        changePaymentMethod(block_name, 'by_online_credit', $(this).is(':checked'))
    });

    // сохранение комментария
    $orderContent.on('blur focus', '.orderComment_fld', function(){
        if (comment != $(this).val()) {
            comment = $(this).val();
            changeOrderComment($(this).val());
        }
    });

    // клик по "Дополнительные пожелания"
    $orderContent.on('click', '.orderComment_t', function(){
        $('.orderComment_fld').show();
    });

    // применить скидку
    $orderContent.on('click', '.jsApplyDiscount', function(e){
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name'),
            number = $this.parent().siblings('input').val();
        // TODO mask
        if (number != '') applyDiscount(block_name, number);
        e.preventDefault();
    });

    // удалить скидку
    $orderContent.on('click', '.jsDeleteDiscount', function(e){
        var $this = $(this),
            block_name = $this.closest('.orderRow').data('block_name'),
            number = $this.data('value');
        deleteDiscount(block_name, number);
        e.preventDefault();
    });

    // клик по "хочу быстрее"
    $orderContent.on('click', '.jsWanna', function(){
        var span = '<span style="margin: 5px 0 17px 10px; display: inline-block; color: #878787;">Спасибо за участие в опросе.</span>';
        $(span).insertAfter($(this));
        $(this).hide();
        window.docCookies.setItem('enter_order_v3_wanna', 1, 0, '/order');
        $body.trigger('trackUserAction', ['1_2 Срок_Хочу_быстрее_Доставка']);
        log({'action':'wanna'});
    });

    $orderContent.on('click', '.jsDeleteCertificate', function(){
        var block_name = $(this).closest('.orderRow').data('block_name');
        deleteCertificate(block_name);
    });

    // клик по "Я ознакомлен и согласен..."
    $orderContent.on('click', '.jsAcceptTerms', function(){
        $body.trigger('trackUserAction', ['14 Согласен_оферта_Доставка_ОБЯЗАТЕЛЬНО']);
    });

	/* Оферта */
	$body.on('click', '.js-order-oferta-popup-btn', function(e){
		var href = $(this).data('value');
		e.preventDefault();
		if (href != '') {
			console.log('OLD href', href);
			if (window.location.host != 'www.enter.ru') href = href.replace(/^.*enter.ru/, ''); /* для работы на demo-серверах */
			console.log('NEW href', href);
			$.ajax({
				url: href,
				success: function(data) {
					$('.orderOferta_tl:first').html($(data).find('.entry-content').html());
					showOfertaPopup();
				}
			})
		}
	});

	$body.on('click', '.js-oferta-tab', function(){
		tabsOfertaAction(this)
	});

	// ДЛЯ АБ-ТЕСТА ПО МОТИВАЦИИ ОНЛАЙН-ОПЛАТЫ
	$body.on('click', '.jsPaymentMethodRadio', function(){
		var $this = $(this),
			block_name = $this.closest('.orderRow').data('block_name'),
			method = $this.val();
        if (method == 'by_online_credit') $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '13_3 Способы_оплаты_Доставка', 'Кредит']);
        if (method == 'by_online') $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '13_3 Способы_оплаты_Доставка', 'Онлайн-оплата']);
		changePaymentMethod(block_name, method, 'true')
	});

	$body.on('change', '.jsPaymentMethodSelect', function(e){
		var $this = $(this),
			block_name = $this.closest('.orderRow').data('block_name'),
			selectedMethod = $this.find(':selected').val();
		changePaymentMethod(block_name, selectedMethod, 'true');
        console.log('[G changed', e);
        if (selectedMethod == 'by_credit_card') $body.trigger('trackGoogleEvent', ['Воронка_новая_v2_'+region, '13_3 Способы_оплаты_Доставка', 'Картой_курьеру']);
		e.preventDefault();
	});

    // АНАЛИТИКА

    if (/order\/delivery/.test(window.location.href)) {
        $body.trigger('trackUserAction', ['6_1 Далее_успешно_Получатель_ОБЯЗАТЕЛЬНО']); // TODO перенести в validate.js
        $body.trigger('trackUserAction', ['7 Вход_Доставка_ОБЯЗАТЕЛЬНО', 'Количество заказов: ' + $('.orderRow').length]);
    }

    // отслеживаем смену региона
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

})(jQuery);
(function($) {
    var $body = $(document.body),
        $orderContent = $('.orderCnt'),
        $inputs = $orderContent.find('input');

    // jQuery masked input
	delete $.mask.definitions[9];
    $.mask.definitions['x']='[0-9]';
    $.mask.placeholder= "_";
	$.mask.autoclear = false;
    $.map($inputs, function(elem, i) {
        if (typeof $(elem).data('mask') !== 'undefined') $(elem).mask($(elem).data('mask'));
    });

    // переключение бонусных карт
    $orderContent.on('click', '.bonusCnt_i', function(e) {
        e.stopPropagation();

        var $elem = $(this),
            eq = $elem.data('eq'),
            $cardsDescriptions = $('.bonusCnt_it');

        if ($elem.hasClass('bonusCnt_i-act')) return;

        $('.bonusCnt_i').removeClass('bonusCnt_i-act');
        $elem.addClass('bonusCnt_i-act');
        $cardsDescriptions.hide().eq(eq).show();
		$elem.find('.bonusCnt_tx_code .brb-dt').hide();
    });

	/* Подсказки бонусных карт */

	$body.on('mouseenter', '.jsShowBonusCardHint', function(){
		$(this).parent().siblings('.bonusCnt_popup').show()
	});

	$body.on('mouseleave', '.jsShowBonusCardHint', function(){
		$(this).parent().siblings('.bonusCnt_popup').hide()
	});

	$('.jsOrderV3BonusCardField').each(function(i,elem){
		$(elem).closest('.bonusCnt-v2').find('.bonusCnt_tx_code .brb-dt').text($(elem).val())
	});

    // АНАЛИТИКА

    $body.on('focus', '.jsOrderV3PhoneField', function(){
        $body.trigger('trackUserAction',['1 Телефон_Получатель_ОБЯЗАТЕЛЬНО'])
    });

    $body.on('focus', '.jsOrderV3EmailField', function(){
        $body.trigger('trackUserAction',['2 Email_Получатель'])
    });

    $body.on('focus', '.jsOrderV3NameField', function(){
        $body.trigger('trackUserAction',['3 Имя_Получатель_ОБЯЗАТЕЛЬНО'])
    });

    $body.on('focus', '.jsOrderV3BonusCardField', function(){
        $body.trigger('trackUserAction',['4 Начислить_баллы_Получатель'])
    });

    $body.on('click', '.jsOrderV3AuthLink', function(){
        $body.trigger('trackUserAction',['5 Войти_с_паролем_Получатель'])
    });

    if (/orde(r|rs)\/new/.test(window.location.href)) {
        $body.trigger('trackUserAction', ['1 Вход_Получатель_ОБЯЗАТЕЛЬНО']);
    }

	// Если стоит галка на чекбоксе
	if ($('.jsOrderV3SubscribeCheckbox').is(':checked')) {
		docCookies.setItem('enter_wanna_subscribe', true, 0, '/'); // ставим куку на сессию
	}

	// Меняем куку по изменению "Подписаться на рассылку"
	$body.on('change', '.jsOrderV3SubscribeCheckbox', function(){
		docCookies.setItem('enter_wanna_subscribe', $(this).is(':checked'), 0, '/');
	});

	$('.jsOrderV3PhoneField').focus();

})(jQuery);
;(function($) {

    var $body = $(document.body),
        $orderContent = $('.orderCnt'),
        $errorBlock = $orderContent.find('#OrderV3ErrorBlock'),
        $pageNew = $('.jsOrderV3PageNew'),
        $pageDelivery = $('.jsOrderV3PageDelivery'),
        $validationErrors = $('.jsOrderValidationErrors'),
        errorClass = 'textfield-err',
        validateEmail = function validateEmailF(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        validate = function validateF(){
			var error = [],
				$phoneInput = $('[name=user_info\\[phone\\]]'),
				$emailInput = $('[name=user_info\\[email\\]]'),
				$bonusCardInput =  $('[name=user_info\\[bonus_card_number\\]]'),
				$subscribeInput = $('.jsOrderV3SubscribeCheckbox'),
				phone = $phoneInput.val().replace(/\s+/g, '');

			if (!/8\(\d{3}\)\d{3}-\d{2}-\d{2}/.test(phone)) {
				error.push('Неверный формат телефона');
				$phoneInput.addClass('textfield-err').siblings('.errTx').show();
			} else {
				$phoneInput.removeClass('textfield-err').siblings('.errTx').hide();
			}

			if (($subscribeInput.is(':checked') || $emailInput.hasClass('jsOrderV3EmailRequired')) && $emailInput.val().length == 0) {
				error.push('Не указан email');
				$emailInput.addClass('textfield-err').siblings('.errTx').text('Не указан email').show();
			} else if ($emailInput.val().length != 0 && !validateEmail($emailInput.val())) {
				error.push('Неверный формат E-mail');
				$emailInput.addClass('textfield-err').siblings('.errTx').text('Неверный формат email').show();
			} else {
				$emailInput.removeClass('textfield-err').siblings('.errTx').hide();
			}

			$bonusCardInput.mask($bonusCardInput.data('mask')); // еще раз, т.к. событие blur и последующий validate проскакивает раньше обновления значения инпута плагином

			if ($bonusCardInput.val().length != 0 && !ENTER.utils.checkEan($bonusCardInput.val())) {
				error.push('Неверный код карты лояльности');
				$bonusCardInput.addClass(errorClass).siblings('.errTx').show();
			} else {
				$bonusCardInput.removeClass('textfield-err').siblings('.errTx').hide();
			}

			return error;
		};

    if ($validationErrors.length) {
        console.warn('Validation errors', $validationErrors);
    }

	/* Проверяем форму при потере фокуса любого input */
	$pageNew.on('blur', 'input', function(){
		validate();
	});

    // PAGE NEW

    // проверка телефона и email
    $pageNew.find('form').on('submit', function (e) {
		var error = validate();
        if (error.length != 0) {
            e.preventDefault();
            $body.trigger('trackUserAction', ['6_2 Далее_ошибка_Получатель', 'Поле ошибки: '+ error.join(', ')])
        }
    });

	$pageNew.on('change', '.jsOrderV3SubscribeCheckbox', function(){
		if (!$(this).is(':checked')) $body.trigger('trackGoogleEvent', ['Email_checkout', 'unsubscribe', 'email']);
	});

	$pageNew.on('blur', '.jsOrderV3EmailField', function(){
		var $this = $(this);
		validateEmail($this.val())
			? $body.trigger('trackGoogleEvent', ['Email_checkout', 'success_validation', 'email'])
			: $body.trigger('trackGoogleEvent', ['Email_checkout', 'fail_prevalidation', 'email'])
	});

    // PAGE DELIVERY

    $pageDelivery.on('click', '.orderCompl_btn', function(e){
        var error = [],
			$agreement = $('.jsAcceptAgreement'),
			$form = $(this).closest('form'),
			send15_3 = false,
			partnerOrders = $('.jsPartnerOrder');

        if (!$agreement.is(':checked')) {
            error.push('Необходимо согласие с информацией о продавце и его офертой');
			$agreement.parent().addClass('accept-err')
        } else {
			$agreement.parent().removeClass('accept-err')
		}

        // Проверяем заказы от партнеров
		partnerOrders.each(function(){
			// Доставка
			if ($(this).find('.orderCol_delivrLst_i-act').text().indexOf('Доставка') != -1) {
				if (!ENTER.OrderV3.address || !ENTER.OrderV3.address.buildingName()) {
					$('.jsSmartAddressBlock').addClass('orderCol_delivrIn-err');
					error.push('Укажите адрес доставки');
				} else {
					$('.jsSmartAddressBlock').removeClass('orderCol_delivrIn-err');
				}
			}
			// Самовывоз
			$(this).find('.orderCol_addrs_tx').each(function(i,val){
				if ($(val).text().replace(/\s+/, '').length == 0) {
					$(this).closest('.orderCol_delivrIn-empty').addClass('orderCol_delivrIn-err');
					error.push('Укажите адрес самовывоза');
				}
			});
		});

		e.preventDefault();

        if (error.length != 0) {
            $errorBlock = $orderContent.find('#OrderV3ErrorBlock'); // TODO не очень хорошее поведение
            $body.trigger('trackUserAction', ['15_2 Оформить_ошибка_Доставка', 'Поле ошибки: '+error.join(', ')]);
        } else {

			// Два условия, по которым мы должны отправить событие 15_3
			if ( $('.orderCol_addrs_fld').length > 0 && ENTER.OrderV3.address.buildingName() == "") send15_3 = true;
			if ( $('.orderCol_delivrIn-empty:not(.jsSmartAddressBlock)').length > 0 ) send15_3 = true;

			if (send15_3) $body.trigger('trackUserAction', ['15_3 Оформить_успешно_КЦ']);

            $body.trigger('trackUserAction', ['15_1 Оформить_успешно_Доставка_ОБЯЗАТЕЛЬНО']);
			$(this).attr('disabled', true); // блокируем кнопку "Отправить"
			setTimeout(function() {	$form.submit(); }, 1000 ); // быстрая обертка для отправки аналитики, иногда не успевает отправляться
        }

    });

}(jQuery));