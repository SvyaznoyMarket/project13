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
        var $reviewForm = $('.jsReviewForm2');
        var user = ENTER.config.userInfo.user;
        if (user.name) $('[name=review\\[author_name\\]]').val(user.name.slice(0,19));
        if (user.email) $('[name=review\\[author_email\\]]').val(user.email);
        $reviewForm.lightbox_me($.extend(popupDefaults, {
            onLoad: function() {},
            onClose: function() {
                $reviewForm.find('.form-ctrl__textarea--err, .form-ctrl__input--err').removeClass('form-ctrl__textarea--err form-ctrl__input--err')
            }
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
            if ($window.scrollTop() - 100 > tabsOffset) {
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
        window.scrollTo(0, $(hash).offset().top - 105);
    });

    $body.on('click', '.jsOneClickButton-new', function(){
        $('.jsProductPointsMap').trigger('close');
    });

    $body.on('click', '.jsShowDeliveryMap', function(){

        var productId = $(this).data('product-id'),
            productUi = $(this).data('product-ui'),
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

        $.ajax('/ajax/product/map/' + productId + '/' + productUi, {
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
                        zoom: mapData.zoom,
                        controls: ['zoomControl', 'fullscreenControl', 'geolocationControl', 'typeSelector']
                    },{
                        autoFitToViewport: 'always',
                        suppressMapOpenBlock: true
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
                            yMap.geoObjects.add(new ENTER.Placemark(point, true, 'jsOneClickButton-new'));
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
        categoryItemSelector: '.js-product-accessoires-category',
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

    // отправка отзыва
    $('#reviewForm').on('submit', function(e){
        var $form = $(this),
            textareaErrClass = 'form-ctrl__textarea--err',
            inputErrClass = 'form-ctrl__input--err',
            textareaLblErrClass = 'form-ctrl__textarea-lbl--err';
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: $(this).attr('action'),
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(data){
                if (data.error) {
                    console.log('errors in review form', data);
                    $.each(data.form.error, function(i,val){
                        var $field = $form.find('[name="review['+ val.field +']"]');
                        $field.removeClass(textareaErrClass).removeClass(inputErrClass); // снимаем ошибки
                        if (val.message) {
                            if ($field.is('textarea')) {
                                $field.addClass(textareaErrClass);
                                $field.siblings('.' + textareaLblErrClass).show();
                            }
                            if ($field.is('input')) $field.addClass(inputErrClass);
                        } else {
                            $field.siblings('.' + textareaLblErrClass).hide();
                        }
                    });
                } else if (data.success) {
                    var $successDiv = $('.jsReviewSuccessAdd');
                    $('.jsReviewFormInner').hide();
                    $('.jsReviewForm2').animate({'height': $successDiv.height()});
                    $successDiv.fadeIn();
                }
            },
            complete: function(data) {
                //console.log('complete', data);
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
    });

    // Оферта партнера
    $('.jsProductPartnerOffer').on('click', function(e){
        e.preventDefault();
        var link = $(this).attr('href'),
            $offer = $('.jsProductPartnerOfferDiv');

        if (!link) return;
        link = link.replace(/^http:\/\/.*?\//, '/');

        if ($offer.length == 0) {
            $.get(link).done(function (doc) {
                $('<div class="jsProductPartnerOfferDiv partner-offer-popup"></div>')
                    .append($('<i class="closer jsPopupCloser">×</i>'), $('<div class="inn" />').append($(doc).find('h1'), $(doc).find('article')))
                    .lightbox_me(popupDefaults)
            });
        } else {
            $offer.lightbox_me(popupDefaults)
        }

    });

    // Таблица размеров в лайтбоксе
    $body.on('click', '.jsImageInLightBox', function(e){
        e.preventDefault();
        var imageLink = $(this).data('href');
        $('<div class="popup popup--normal"><div class="closer jsPopupCloser">×</div> </div>').append($('<img />', { src: imageLink})).lightbox_me({
            destroyOnClose: true,
            closeSelector: ".jsPopupCloser",
            centered: true
        });
    });

    $body.on('click', '.jsProductImgPopup .jsBuyButton', function(){ $(this).closest('.jsProductImgPopup').trigger('close'); });

    $('.js-description-expand').on('click', function(){

        $(this).removeClass('collapsed js-description-expand');

    });

    $body.on('click', '.jsProductCardNewLabelInfo', function(){
        $('.jsProductCardNewLabelPopup').toggleClass('info-popup--open');
    });

    //
    $body.on('click', '.jsSubscribeAfterReview', function() {

        if (!$('.js-registerForm-subscribe').is(':checked')) return;
        $.ajax({
            type: "POST",
            url: '/subscribe/create',
            data: { channel: 1, email: $('#reviewFormEmail').val() },
            success: function(data) {
                if (data.success) {
                    $('.jsReviewSuccessJustSubscribed').lightbox_me(popupDefaults);
                } else {
                    $('.jsReviewSuccessSubscribed').lightbox_me(popupDefaults);
                    if (data.code != 910) $('.jsReviewSuccessSubscribed .popup-form-success__txt').text(data.error)
                }
            }
        });
    })

})(jQuery);


/**
 * Обратный счетчик акции
 */
!function() {
    var
        countDownWrapper = $('.js-countdown'),
        countDownOut     = $('.js-countdown-out'),
        expDate          = countDownWrapper.attr('data-expires'),

        getDeclension = function( days ) {
            var
                str      = days + '',
                lastChar = str.slice(-1),
                lastNum  = lastChar * 1;

            if ( days > 4 && days < 21 ) {
                return 'дней';
            } else if ( lastNum > 4 && days > 20 ) {
                return 'дней';
            } else if ( lastNum > 1 && lastNum < 5 ) {
                return 'дня';
            }

            return 'день';
        },

        tick = function( opts ) {
            var
                mask = ( opts.days > 0 ) ? 'D ' + getDeclension(opts.days) + ' HH:MM:SS' : 'HH:MM:SS';

            mask = mask.replace(/(D+)/, function( str, d) { return (d.length > 1 && opts.days < 10 ) ? '0' + opts.days : opts.days });
            mask = mask.replace(/(H+)/, function( str, h) { return (h.length > 1 && opts.hours < 10 ) ? '0' + opts.hours : opts.hours });
            mask = mask.replace(/(M+)/, function( str, m) { return (m.length > 1 && opts.minutes < 10 ) ? '0' + opts.minutes : opts.minutes });
            mask = mask.replace(/(S+)/, function( str, s) { return (s.length > 1 && opts.seconds < 10 ) ? '0' + opts.seconds : opts.seconds });

            countDownOut.html(mask);
        },

        countDown;

    try {
        countDown = new CountDown({
            // timestamp: 1445597200000,
            timestamp: expDate * 1000,
            tick: tick
        });
    } catch ( err ) {
        console.warn('Не удалось запустить обратный счетчик акции');
        console.warn(err);
    }

}();