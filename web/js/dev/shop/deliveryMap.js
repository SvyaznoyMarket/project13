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
            controls: ['geolocationControl', 'zoomControl']
        },{
            autoFitToViewport: 'always',
            suppressMapOpenBlock: true,
            suppressObsoleteBrowserNotifier: true
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

    });

    // Переключение партнеров
    $partnersList.on('click', function(){

        var activeClass = 'active';

        $(this).toggleClass(activeClass);

        activePartners = $.map($partnersList.filter(function(){return $(this).hasClass(activeClass)}),
            function(obj){ return $(obj).data('value')});

        if (typeof objectManager != 'undefined') {
            objectManager.setFilter(function(point) {
                return activePartners.length == 0 ? true : $.inArray(point.properties.ePartner, activePartners) !== -1;
            });
        }

        if (activePartners.length == 0) {
            $pointList.show();
        } else {
            $pointList.each(function(){
                if ($.inArray($(this).data('partner'), activePartners) === -1) {
                    $(this).hide()
                } else {
                    $(this).show()
                }
            });
        }

        if (activePartners.length == 1) {
            $pointListItemPartners.hide()
        } else {
            $pointListItemPartners.show()
        }

    });

    $pointList.on('click', function(){

        var $this = $(this),
            uid = $(this).attr('id').slice(4);

        if ($this.hasClass(pointActiveClass)) {
            $this.removeClass(pointActiveClass);
            if (typeof objectManager != 'undefined') {
                objectManager.setFilter(function(point){
                    return activePartners.length == 0 ? true : $.inArray(point.properties.ePartner, activePartners) !== -1;
                });
            }
        } else {
            if (typeof objectManager != 'undefined') {
                objectManager.setFilter(function(point){
                    return point.properties.eUid == uid;
                });
            }
            $pointList.removeClass(pointActiveClass);
            $this.addClass(pointActiveClass);
        }

        if (map && typeof map.events.fire == 'function') {
            map.events.fire('boundschange');
        }
    });


}(jQuery);