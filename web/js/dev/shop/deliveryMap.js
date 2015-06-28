/* Новая страница /delivery со всеми точками самовывоза */
+function($){

    var $mapContainer = $('#jsDeliveryMap'),
        partners = $.parseJSON($('#partnersJSON').html()),
        geoObjects = $.parseJSON($('#objectManagerDataJSON').html()),
        $partnersList = $('.jsPartnerListItem'),
        $pointListHolder= $('.jsPointList'),
        $pointList = $('.jsPointListItem'),
        pointActiveClass = 'current',
        activePartners = [], map, objectManager;

    if ($mapContainer.length == 0) return ;

    console.log('Список партнеров', partners);
    console.log('Список для geoManager', geoObjects);

    // инициализация карты
    ymaps.ready(function(){

        objectManager = new ymaps.ObjectManager();
        objectManager.objects.options.set('iconLayout', 'default#image');
        objectManager.objects.options.set('iconImageSize', [23,30]);

        objectManager.objects.events.add(['click'], function(e){
            var objId = e.get('objectId'),
                idSelector ='#uid-' + objId;

            $pointList.removeClass(pointActiveClass).filter(idSelector).addClass(pointActiveClass);
            $pointListHolder.scrollTo(idSelector);

        });

        map = window.map = new ymaps.Map("jsDeliveryMap", {
            center: [68, 68],
            zoom: 11,
            controls: ['geolocationControl', 'zoomControl']
        },{
            autoFitToViewport: 'always',
            suppressMapOpenBlock: true,
            suppressObsoleteBrowserNotifier: true
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

        console.log(activePartners);

        if (typeof objectManager != 'undefined') {
            objectManager.setFilter(function(point) {
                return activePartners.length == 0 ? true : $.inArray(point.properties.ePartner, activePartners) !== -1;
            });
        }

        if (activePartners.length == 0) {
            $pointList.show();
        } else {
            $pointList.filter(function (i, domEl) {
                return $.inArray($(domEl).data('partner'), activePartners) === -1;
            }).hide();
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
            return;
        }

        if (typeof objectManager != 'undefined') {
            objectManager.setFilter(function(point){
                return point.properties.eUid == uid;
            });
        }

        $pointList.removeClass(pointActiveClass);
        $this.addClass(pointActiveClass);
    });


}(jQuery);