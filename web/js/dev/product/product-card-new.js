/*
* Новая карточка товара
* */
;(function($){

    var $window = $(window),
        $body = $(document.body),
        $creditButton = $body.find('.jsProductCreditButton'),
        $reviewFormStars = $('.jsReviewFormRating'),
        $userbar = $('.js-topbar-fixed'),
        $tabs = $('.jsProductTabs'),
        $epFishka = $('.js-pp-ep-fishka'),
        tabsOffset,// это не очень хорошее поведение, т.к. при добавлении сверху элементов (AJAX, например) offset не изменяется
        popupDefaults = {
            centered: true,
            closeSelector: '.jsPopupCloser',
            closeClick: true
        };

    /* Если это не новая карточка, то do nothing */
    if (!$body.hasClass('product-card-new')) return;

    tabsOffset = $tabs.offset().top;


    // Кредит
    if ($creditButton.length > 0 && typeof window['dc_getCreditForTheProduct'] == 'function') {
        window['dc_getCreditForTheProduct'](
            4427,
            window.docCookies.getItem('enter_auth'),
            'getPayment',
            { price : $creditButton.data('credit')['price'], count : 1, type : $creditButton.data('credit')['product_type'] },
            function( result ) {
                if( typeof result['payment'] != 'undefined' && result['payment'] > 0 ) {
                    $creditButton.find('.jsProductCreditPrice').text( printPrice( Math.ceil(result['payment']) ) );
                    $creditButton.show();
                }
            }
        )
    }

    // Добавление отзыва
    $body.on('click', '.jsReviewAdd', function(){
        $('.jsReviewForm2').lightbox_me($.extend(popupDefaults, {
            onLoad: function() {},
            onClose: function() {}
        }));
    });

    // Отзывы
    $body.on('click', '.jsShowMoreReviews', function(){
        var productUi = $(this).data('ui'),
            totalNum = $(this).data('total-num'),
            $hiddenReviews = $('.jsReviewItem:hidden'),
            currentCount;
        if ($hiddenReviews.length > 0) {
            $hiddenReviews.show();
            if ($('.jsReviewItem').length == totalNum) $('.jsShowMoreReviews').hide();
        } else {
            currentCount = $('.jsReviewItem').length;
            $.ajax(
                '/product-reviews/' + productUi, {
                    data: {
                        page: currentCount / 10,
                        numOnPage: 10
                    }
                }
            ).done(function(data){
                    if (data.content) {
                        $('.jsReviewsList').append(data.content);
                        if ($('.jsReviewItem').length == totalNum) $('.jsShowMoreReviews').hide();
                    }
                });
        }
    });

    // Плавающая навигация ala scrollspy
    if ($tabs.length) {
        $window.on('scroll', function(){
            var fixedClass = 'pp-fixed';
            if ($window.scrollTop() - 110 > tabsOffset) {
                $tabs.addClass(fixedClass);
                $epFishka.addClass('fadeIn');
                $userbar.addClass(fixedClass);
            } else {
                $tabs.removeClass(fixedClass);
                $epFishka.removeClass('fadeIn');
                $userbar.removeClass(fixedClass);
            }
        });
    }

    $body.scrollspy({ target: '#jsScrollSpy', offset: 120 });

    $body.on('click', '.jsProductTabs a', function(e) {
        var hash = $(this).attr('href');
        e.preventDefault();
        window.location.hash = hash;
        window.scrollTo(0, $(hash).offset().top - 100);
    });

    $body.on('click', '.jsOneClickButtonOnDeliveryMap', function(){
        $('.jsProductPointsMap').trigger('close');
    });

    $body.on('click', '.jsShowDeliveryMap', function(){

        var productId = $(this).data('product-id'),
            $div = $('.jsProductPointsMap');

        // Если нет пунктов самовывоза
        if ($('.jsDeliveryPickupAvailable').length == 0) return;

        // Если точки были загружены, то просто показываем этот div
        if ($div.find('.jsDeliveryMapPoints').length > 0) {
            $div.lightbox_me({
                centered: true,
                preventScroll: true
            });
            return ;
        }

        if ($div.data('xhr')) return;

        $.ajax('/ajax/product/map/' + productId, {
            dataType: 'json',
            beforeSend: function(){
                $div.data('xhr', true);
                $div.lightbox_me({
                    centered: true,
                    preventScroll: true
                })
            }
        }).always(function(){
            $div.data('xhr', false)
        }).done(function(data){

            if (!data.success) {
                console.error('product/map response: %s', data.error);
            } else {
                var $mapContainer = $(data.html),
                    mapData = $.parseJSON($mapContainer.find('.jsMapData').html()),
                    mapDivId = $mapContainer.find('.js-order-map').first().attr('id'),
                    yMap, pointsModel;

                $div.html(data.html);

                $body.on('click', '.jsCloseFl', function(){
                    $div.trigger('close');
                });

                if (mapData) {

                    yMap = new ymaps.Map(mapDivId, {
                        center: [mapData.latitude, mapData.longitude],
                        zoom: mapData.zoom
                    },{
                        autoFitToViewport: 'always'
                    });

                    yMap.controls.remove('searchControl');

                    yMap.geoObjects.removeAll();
                    yMap.container.fitToViewport();

                    pointsModel = new ENTER.DeliveryPoints(mapData.points, yMap);

                    // Эвент на изменение размера карты (для фильтрации точек)
                    yMap.events.add('boundschange', function (event) {
                        var bounds;
                        if (event.get('newBounds')) {
                            bounds = event.get('target').getBounds();
                            pointsModel.latitudeMin(bounds[0][0]);
                            pointsModel.latitudeMax(bounds[1][0]);
                            pointsModel.longitudeMin(bounds[0][1]);
                            pointsModel.longitudeMax(bounds[1][1]);
                        }
                    });

                    // добавляем видимые точки на карту
                    $.each(mapData.points, function(i, point){
                        try {
                            yMap.geoObjects.add(new ENTER.Placemark(point, true, 'jsOneClickButtonOnDeliveryMap jsOneClickButton-new'));
                        } catch (e) {
                            console.error('Ошибка добавления точки на карту', e);
                        }
                    });

                    if (yMap.geoObjects.getLength() === 1) {
                        yMap.setCenter(yMap.geoObjects.get(0).geometry.getCoordinates(), 15);
                        yMap.geoObjects.get(0).options.set('visible', true);
                    } else {
                        yMap.setBounds(yMap.geoObjects.getBounds());
                        // точки становятся видимыми только при увеличения зума
                        yMap.events.once('boundschange', function(event){
                            if (event.get('oldZoom') < event.get('newZoom')) {
                                yMap.geoObjects.each(function(point) { point.options.set('visible', true)})
                            }
                        })
                    }

                    ko.applyBindings(pointsModel, $div[0]);


                    $body.on('click', '.jsOrderV3Dropbox',function(){
                        $(this).siblings().removeClass('opn').find('.jsOrderV3DropboxInner').hide(); // скрываем все, кроме потомка
                        $(this).find('.jsOrderV3DropboxInner').toggle(); // потомка переключаем
                        $(this).hasClass('opn') ? $(this).removeClass('opn') : $(this).addClass('opn');
                    });

                }
            }

        })

    });

    $('.js-slider-2').goodsSlider({
        leftArrowSelector: '.goods-slider__btn--prev',
        rightArrowSelector: '.goods-slider__btn--next',
        sliderWrapperSelector: '.goods-slider__inn',
        sliderSelector: '.goods-slider-list',
        itemSelector: '.goods-slider-list__i',
        //categoryItemSelector: '.bGoodsSlider__eCatItem',
        //pageTitleSelector: '.slideItem_cntr',
        onLoad: function(goodsSlider) {
            ko.applyBindings(ENTER.UserModel, goodsSlider);

            // Для табов в новой карточке товара
            if ($(goodsSlider).data('position') == 'ProductSimilar') $('.jsSimilarTab').show();
        }
    });

    $reviewFormStars.on('click', function(){
        var activeClass = 'popup-rating__i--fill',
            rate = $(this).index();
        $reviewFormStars.removeClass(activeClass);
        $(this).addClass(activeClass).prevAll().addClass(activeClass);
        $('#reviewFormRating').val(++rate)
    });

    $('#reviewForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: $(this).attr('action'),
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(data){
                console.log(data);
            },
            complete: function(data) {
                console.log(data);
            }
        });
    });

    $body.on('click', '.jsReviewVote', function(e){
        var $button = $(e.target),
            buttonVote = $button.data('vote'),
            $voteDiv = $(this),
            userVote = $voteDiv.data('user-vote'),
            reviewUi = $voteDiv.closest('.jsReviewItem').data('review-ui'),
            $voteButtons = $voteDiv.find('.jsReviewVoteBtn'),
            activeClass = 'active',
            voteClass = 'voting';

        if ($voteDiv.data('xhr')) return;

        $.ajax(
            '/product-reviews/vote',
            {   type: 'post',
                data: {
                    'review-ui': reviewUi,
                    'vote': userVote == buttonVote ? 0 : buttonVote
                    },
                beforeSend: function() {
                    $button.addClass(voteClass);
                    $voteDiv.data('xhr', true);
                },
                success: function(data){
                    if (data.success) {
                        $voteDiv.data('user-vote', data.vote);
                        $voteDiv.find('.jsReviewVoteBtn').removeClass(activeClass);
                        if (data.vote != 0) $voteButtons.eq(data.vote == 1 ? 0 : 1).addClass(activeClass);
                        if (typeof data.positive != 'undefined') $voteButtons.eq(0).text(data.positive);
                        if (typeof data.negative != 'undefined') $voteButtons.eq(1).text(data.negative);

                    } else {
                        console.error('Ошибка голосования: %s', data.error)
                    }
                }
            }).always(function(){
                $button.removeClass(voteClass);
                $voteDiv.data('xhr', false);
            })
    })

})(jQuery);
