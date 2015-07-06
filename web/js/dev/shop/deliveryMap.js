/* Новая страница /delivery со всеми точками самовывоза */
+function($){

    var $mapContainer = $('#jsDeliveryMap'),
        partners = $.parseJSON($('#partnersJSON').html()),
        geoObjects = $.parseJSON($('#objectManagerDataJSON').html()),
        $partnersList = $('.jsPartnerListItem'),
        $pointListHolder= $('.jsPointList'),
        $pointList = $('.jsPointListItem'),
        $pointListItemPartners = $('.jsPointListItemPartner'),
        pointActiveClass = 'current',
        $searchInput = $('#searchInput'),
        $searchClear = $('.jsSearchClear'),
        $searchAutocompleteList = $('.jsSearchAutocompleteList'),
        $searchAutocompleteHolder = $('.jsSearchAutocompleteHolder'),
        activePartners = [], map, objectManager, uidsToShow = [];

    if ($mapContainer.length == 0) return ;

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
            center: [68, 68],
            zoom: 11,
            controls: ['geolocationControl', 'zoomControl', 'searchControl']
        },{
            autoFitToViewport: 'always',
            suppressMapOpenBlock: true,
            suppressObsoleteBrowserNotifier: true
        });

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
        map.setGlobalPixelCenter([ position[0] + 110, position[1] ]);

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
        } else {
            $pointList.removeClass(pointActiveClass);
            $this.addClass(pointActiveClass);
            if (map) {
                map.setCenter($this.data('geo'), 15);
                var position = map.getGlobalPixelCenter();
                map.setGlobalPixelCenter([ position[0] + 110, position[1] ]);
            }
        }

        fireMapEvent('boundschange');
    });


}(jQuery);