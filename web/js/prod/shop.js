/* Новая страница /delivery со всеми точками самовывоза */
+function($){

    var $window = $(window),
        menuItems = $('.menu-item'),
        $mapContainer = $('#jsDeliveryMap'),
        partners = $.parseJSON($('#partnersJSON').html()),
        geoObjects = $.parseJSON($('#objectManagerDataJSON').html()),
        $partnersList = $('.jsPartnerListItem'),
        $pointListHolder = $('.jsPointList'),
        $pointList = $('.jsPointListItem'),
        $pointListItemPartners = $('.jsPointListItemPartner'),
        pointActiveClass = 'current',
        $searchInput = $('#searchInput'),
        $searchClear = $('.jsSearchClear'),
        $searchAutocompleteList = $('.jsSearchAutocompleteList'),
        $searchAutocompleteHolder = $('.jsSearchAutocompleteHolder'),
        activePartners = [], map, objectManager, uidsToShow = [],
        scrollAnalyticsTimer = null,
        windowScrollTimer = null
    ;

    if ($mapContainer.length == 0) return ;

    // Выделение активного пункта в боковом меню
    $.each(menuItems, function() {
        var $this = $(this);
        if ($this.find('a').attr('href') == location.pathname ) {
            $this.addClass('active');
            return false;
        }
    });

    console.log('Список партнеров', partners);
    console.log('Список для geoManager', geoObjects);

    function filterPointsList() {
        $pointList.each(function(){
            if ($.inArray(this.id.slice(4), uidsToShow) === -1) {
                $(this).hide()
            } else {
                $(this).show()
            }
        });
    }

    function fireMapEvent(eventName) {
        if (map && typeof map.events.fire == 'function') {
            map.events.fire(eventName);
        }
    }

    // Поиск
    $searchInput.on('keyup', function(e){
        var text = $(this).val(),
            keycode = e.which,
            $elements = $('.jsSearchAutocompleteItem'),
            $list = $('.deliv-suggest__list'),
            activeClass = 'deliv-suggest__i--active',
            index = $elements.index($elements.filter('.'+activeClass)),
            extendValue = 1, extendedBounds;

        if (text.length == 0) {
            $searchClear.hide()
        } else {
            $searchClear.show()
        }

        if (!ymaps || typeof ymaps.geocode != 'function') return;

        extendedBounds = [[map.getBounds()[0][0] - extendValue, map.getBounds()[0][1] - extendValue],[map.getBounds()[1][0] + extendValue, map.getBounds()[1][1] + extendValue]];

        if ($.inArray(keycode, [13,38,40]) === -1) ymaps.geocode(text, { boundedBy: extendedBounds, strictBounds: true }).then(
            function(res){
                var $list = $searchAutocompleteList.empty();
                res.geoObjects.each(function(obj){
                    $list.append(
                        $('<li class="deliv-suggest__i jsSearchAutocompleteItem" />')
                            .data('bounds', obj.geometry.getBounds())
                            .text(obj.properties.get('name') + ', ' + obj.properties.get('description')));
                });

                if (res.geoObjects.getLength()) $searchAutocompleteHolder.show(); else $searchAutocompleteHolder.hide();
                $elements = $('.jsSearchAutocompleteItem');
            },
            function(err){
                console.warn('Geocode error', err)
            }
        );

        $elements.removeClass(activeClass);

        switch (keycode) {
            case 13: // Enter key
                if (index > -1) {
                    $elements.eq(index).click();
                    return false;
                }
                return false;
            case 38: // up key
                if (index == -1) index = $elements.length;
                $elements.eq(index - 1).addClass(activeClass);
                $list.scrollTo('.' + activeClass);
                return false;
            case 40: // down key
                $elements.eq(index + 1).addClass(activeClass);
                $list.scrollTo('.' + activeClass);
                return false;
        }

    });

    // Очистка поиска
    $searchClear.on('click', function() {
        $searchInput.val('');
        $searchClear.hide();
        $searchAutocompleteHolder.hide();
    });

    $(document.body).on('click', '.jsSearchAutocompleteItem', function() {
        var bounds = $(this).data('bounds');
        if (bounds) {
            map.setCenter(bounds[0], 14);
            $searchAutocompleteHolder.hide();
            $searchInput.val($(this).text())
        }

        // analytics
        $('body').trigger('trackGoogleEvent', {
            action: 'pickup_ux_shops',
            category: 'search_enter',
            label: $(this).text()
        });
    });

    // инициализация карты
    ymaps.ready(function(){

        objectManager = window.om = new ymaps.ObjectManager();
        objectManager.objects.options.set('iconLayout', 'default#image');
        objectManager.objects.options.set('iconImageSize', [23,30]);

        objectManager.objects.events.add(['click'], function(e){
            var objId = e.get('objectId'),
                idSelector ='#uid-' + objId;

            $pointList.removeClass(pointActiveClass).filter(idSelector).addClass(pointActiveClass);
            $pointListHolder.scrollTo(idSelector, 100, {offset: {top: -100}});

        });

        map = window.omap = new ymaps.Map("jsDeliveryMap", {
            center: [55.76, 37.64],
            zoom: 11,
            controls: ['geolocationControl', 'zoomControl', 'searchControl']
        },{
            autoFitToViewport: 'always',
            suppressMapOpenBlock: true,
            suppressObsoleteBrowserNotifier: true
        });

        if (location.hash == '#full') {
            map.controls.add('fullscreenControl');
            $pointListHolder.hide();
        }

        var searchControl = map.controls.get('searchControl');
        searchControl.options.set('size', 'small');
        searchControl.events.add('click', function(){
            searchControl.options.set('size', 'large')
        });

        map.events.add('boundschange', function (event) {
            var bounds = event.get('newBounds') ? event.get('newBounds') : map.getBounds();
            var uids = [];
            bounds = event.get('target').getBounds();
            objectManager.objects.each(function(object) {
                var state = objectManager.getObjectState(object.id),
                    geo = object.geometry.coordinates,
                    uid = object.properties.eUid,
                    inBounds;

                inBounds = bounds[0][0] < geo[0] && bounds[0][1] < geo[1] && bounds[1][0] > geo[0] && bounds[1][1] > geo[1];

                if (!state.isFilteredOut && inBounds) {
                    uids.push(uid)
                }
            });
            uidsToShow = uids;
            filterPointsList();
        });

        objectManager.add(geoObjects);
        map.geoObjects.add(objectManager);
        map.setBounds(map.geoObjects.getBounds());

        var position = map.getGlobalPixelCenter();
        if (location.hash != '#full') map.setGlobalPixelCenter([ position[0] + 110, position[1] ]);

        // analytics
        var control;
        if (control = map.controls.get('geolocationControl')) {
            control.events.add(['click'], function(e){
                $('body').trigger('trackGoogleEvent', {
                    action: 'pickup_ux_shops',
                    category: 'geo-position',
                    label: ''
                });
            });
        }
        if (control = map.controls.get('searchControl')) {
            control.events.add(['click'], function(e){
                $('body').trigger('trackGoogleEvent', {
                    action: 'pickup_ux_shops',
                    category: 'search_yandex',
                    label: ''
                });
            });
        }
        if (control = map.controls.get('zoomControl')) {
            control.events.add(['click'], function(e){
                $('body').trigger('trackGoogleEvent', {
                    action: 'pickup_ux_shops',
                    category: 'scale',
                    label: (e.get('target').state.get('zoom') > map.getZoom()) ? 'plus' : 'minus'
                });
            });
        }

        objectManager.objects.events.add(['click'], function(e){
            var objId = e.get('objectId'),
                idSelector ='#uid-' + objId
            ;

            $('body').trigger('trackGoogleEvent', {
                action: 'pickup_ux_shops',
                category: 'map',
                label: $pointList.filter(idSelector).data('partner')
            });
        });
    });

    // Переключение партнеров
    $partnersList.on('click', function(){

        var activeClass = 'active';

        $(this).toggleClass(activeClass);

        activePartners = $.map($partnersList.filter(function(){return $(this).hasClass(activeClass)}),
            function(obj){ return $(obj).data('value')});

        if (typeof objectManager != 'undefined') {
            objectManager.setFilter(function(point) {
                var inActivePartners =  $.inArray(point.properties.ePartner, activePartners) !== -1,
                    $listItem = $('#uid-' + point.properties.eUid);

                if (inActivePartners && activePartners.length != 0) {$listItem.show()} else {$listItem.hide()}

                return activePartners.length == 0 ? true : inActivePartners;
            });
        }

        fireMapEvent('boundschange');

        if (activePartners.length == 1 && activePartners[0] != 'pickpoint') {
            $pointListItemPartners.hide()
        } else {
            $pointListItemPartners.show()
        }

        $('body').trigger('trackGoogleEvent', {
            action: 'pickup_ux_shops',
            category: 'filter',
            label: $(this).data('value')
        });
    });

    $pointList.on('click', function(){

        var $this = $(this);

        if ($this.hasClass(pointActiveClass)) {
            $this.removeClass(pointActiveClass);
            if (typeof objectManager != 'undefined') {
                objectManager.setFilter(function(point){
                    return activePartners.length == 0 ? true : $.inArray(point.properties.ePartner, activePartners) !== -1;
                });
            }

            // analytics
            $('body').trigger('trackGoogleEvent', {
                action: 'pickup_ux_shops',
                category: 'list',
                label: 'click_out_' + $(this).data('partner')
            });
        } else {
            $pointList.removeClass(pointActiveClass);
            $this.addClass(pointActiveClass);
            if (map) {
                map.setCenter($this.data('geo'), 15);
                var position = map.getGlobalPixelCenter();
                map.setGlobalPixelCenter([ position[0] + 110, position[1] ]);
            }

            // analytics
            $('body').trigger('trackGoogleEvent', {
                action: 'pickup_ux_shops',
                category: 'list',
                label: 'click_' + $(this).data('partner')
            });
        }

        fireMapEvent('boundschange');
    });

    // analytics
    $pointListHolder.on('scroll', function() {
        if (!scrollAnalyticsTimer) {
            scrollAnalyticsTimer = setTimeout(function() {
                $('body').trigger('trackGoogleEvent', {
                    action: 'pickup_ux_shops',
                    category: 'list',
                    label: 'scroll'
                });

                scrollAnalyticsTimer = null;
            }, 1200)
        }
    });

    $window.on('scroll', function() {
        if (!windowScrollTimer) {
            windowScrollTimer = setTimeout(function() {
                var
                    scrollTop = $window.scrollTop(),
                    scrollBottom = $window.scrollTop() + $window.height(),
                    $el,
                    elMiddle
                ;

                windowScrollTimer = null;

                $el = $('#deliv-free');
                if ($el.length && $el.offset()) {
                    elMiddle = $el.offset().top + 50;
                    console.info($el, [scrollTop, scrollBottom], elMiddle);
                    if (((elMiddle> scrollTop) || (elMiddle > scrollBottom)) && ((elMiddle < scrollTop) || (elMiddle < scrollBottom))) {
                        $('body').trigger('trackGoogleEvent', {
                            action: 'pickup_ux_shops',
                            category: 'text_free-pickup',
                            label: ''
                        });
                    }
                }

                $el = $('#deliv-nonfree');
                if ($el.length && $el.offset()) {
                    elMiddle = $el.offset().top + 50;
                    console.info($el, [scrollTop, scrollBottom], elMiddle);
                    if (((elMiddle> scrollTop) || (elMiddle > scrollBottom)) && ((elMiddle < scrollTop) || (elMiddle < scrollBottom))) {
                        $('body').trigger('trackGoogleEvent', {
                            action: 'pickup_ux_shops',
                            category: 'text_pickup-points',
                            label: ''
                        });
                    }
                }

                $el = $('#postamat-video');
                if ($el.length && $el.offset()) {
                    elMiddle = $el.offset().top + 50;
                    console.info($el, [scrollTop, scrollBottom], elMiddle);
                    if (((elMiddle> scrollTop) || (elMiddle > scrollBottom)) && ((elMiddle < scrollTop) || (elMiddle < scrollBottom))) {
                        $('body').trigger('trackGoogleEvent', {
                            action: 'pickup_ux_shops',
                            category: 'video_pick-point',
                            label: 'reached'
                        });
                    }
                }
            }, 2000);
        }
    });

    $('.delivery-video').find('video').on('play', function() {
        $('body').trigger('trackGoogleEvent', {
            action: 'pickup_ux_shops',
            category: 'video_pick-point',
            label: 'play'
        });
    });

}(jQuery);
$(function($){
    var $viewport = $('.js-shop-viewport');

    if ($viewport.length) {
        ymaps.ready(function () {
			var coords = [$viewport.data('map-latitude'), $viewport.data('map-longitude')];
            var map = new ymaps.Map($viewport[0], {
                center: coords,
                zoom: 16
            });

            map.geoObjects.add(new ymaps.Placemark(coords, {}, {
				iconLayout: 'default#image',
				iconImageHref: '/images/map/marker-shop.png',
				iconImageSize: [28, 39],
				iconImageOffset: [-14, -39]
			}));
        });

		$('.js-shop-image-opener').click(function(e){
			var $self = $(e.currentTarget);

			if ($self.data('type') == 'image') {
				var
					bigUrl = $self.data('big-url'),
					$img = $viewport.find('img')
				;

				$viewport.find('ymaps:first').hide();

				if ($img.length) {
					$img.attr('src', bigUrl).show();
				} else {
					$viewport.append($('<img />', {
						src: bigUrl,
						width: $viewport.width()
					}));
				}
			} else if ($self.data('type') == 'map') {
				$viewport.find('ymaps:first').show();
				$viewport.find('img:first').hide();
			}
		});
    }
});
