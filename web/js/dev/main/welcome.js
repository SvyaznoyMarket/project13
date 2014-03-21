var addKISSmetricsEvent = function(eventName, bannerId, banner) {
    var
        centerImageUrl = banner.attr('src'),
        bannerUrl = banner.data('url');

    if (typeof(_kmq) !== 'undefined') {
        _kmq.push(['record', eventName, {
            'Banner id': bannerId,
            'Center Image URL': centerImageUrl,
            'Banner URL': bannerUrl
        }]);
    }
}

$(document).ready(function () {
    // promo block after header GA
    $('.bPromoCategory a').bind('click', function(){
        var link = $(this).attr('href');
        if( typeof(_gaq) !== 'undefined' )
            _gaq.push(['_trackEvent', 'CategoryClick', 'Верхнее меню', link ]);
    });


    if ( !$( '#main_banner-data' ).length ) {
        return;
    }

    var
        i,
        promos = $('#main_banner-data').data('value' ),
        l = promos.length;

    /* Shit happens */
    for (i = 0; i < l; i++) {
        if (typeof(promos[i].imgb) === 'undefined' || typeof(promos[i].imgs) === 'undefined') {
            promos.splice(i, 1);
        }
        if (typeof(promos[i].url) === 'undefined') {
            promos[i].url = '';
        }
        if (typeof(promos[i].t) === 'undefined') {
            promos[i].url = 4000;
        }
        if (typeof(promos[i].alt) === 'undefined') {
            promos[i].url = '';
        }
    }

    if (l == 0) {
        return;
    }

    if (l == 1) {
        if ('is_exclusive' in promos[0] && promos[0].is_exclusive) {

            var 
                exclImg = $('<img>').attr('src', promos[0].imgb).css('cursor', 'pointer').data('url', promos[0].url);

            exclImg.click(function () {
                if ( typeof(_gaq) !== 'undefined' && typeof(currImg.pos) !== 'undefined' && typeof(currImg.imgb) !== 'undefined' && typeof(currImg.ga) !== 'undefined') {
                    //_gaq.push(['_trackEvent', 'BannerClick', initis[1].ga ]);
                    _gaq.push( ['_trackEvent', 'Carousel', 'Click_' + currImg.pos, currImg.imgb ] );
                }
                location.href = $(this).data('url');
            });

            exclImg.mouseover(function () {
                if ( typeof(_gaq) !== 'undefined' && typeof(currImg.pos) !== 'undefined' && typeof(currImg.imgb) !== 'undefined' && typeof(currImg.ga) !== 'undefined') {
                    //_gaq.push(['_trackEvent', 'BannerClick', initis[1].ga ]);
                    _gaq.push( ['_trackEvent', 'Carousel', 'View_' + currImg.pos, currImg.imgb ] );
                } 
            });

            $('.bCarouselWrap').html(exclImg);
            return;
        }
        $('.centerImage').attr('src', promos[0].imgb).data('url', promos[0].url)
            .click(function () {
				if ( typeof(_gaq) !== 'undefined' && typeof(promos[0].ga) !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'BannerClick', '' + promos[0].ga ]);
					console.log('GA: _trackEvent, BannerClick promos ', promos[0].ga);
				}
                addKISSmetricsEvent('Carousel banner view', 'bigbanner', $(this));
                location.href = $(this).data('url');
            });
        return;
    }
    /* Preload */
    var hb = $('<div>').css('display', 'none');
    for (i = 0; i < l; i++) {
        $('<img>').attr('src', promos[i].imgb).appendTo(hb)
        $('<img>').attr('src', promos[i].imgs).appendTo(hb)
    }
    $('body').append(hb);

    /* Init */
    $('.leftImage').attr({ "src":promos[l - 1].imgs, "alt":promos[l - 1].alt, "title":promos[l - 1].alt});
    $('.centerImage').attr('src', promos[0].imgb).data('url', promos[0].url);
    $('.rightImage').attr({ "src":promos[1].imgs, "alt":promos[1].alt, "title":promos[1].alt});

    var
        currentSl = l - 1,
        idto = null,
        initis = [],
        sliding = false,
        permission = true;

    changeSrc(currentSl);
    idto = setTimeout(function () {
        goSlide();
    }, initis[1].t);
    /* Visuals */
    
    var b = new brwsr();
    if ( b.isAndroid || b.isOSX) {
        $('.bCarousel div').show();
        $('.allpage').css('overflow', 'hidden');
    } else {
        $('.bCarousel').mouseenter(
            function () {
                $('.bCarousel div').show();
            }).mouseleave(function () {
                $('.bCarousel div').hide();
            })
    }
    $('.leftArrow').click(function () {
        goSlide(-1, true);
    });
    $('.leftImage').click(function () {
        goSlide(-1, true);
    });
    $('.rightArrow').click(function () {
        goSlide(1, true);
    });
    $('.rightImage').click(function () {
        goSlide(1, true);
    });
    $('.centerImage').click(function () {
        var
            currImg = initis[1];
        /**
         * Текущие изобрежения слайдера хранятся в initis так:
         * initis[0] - leftImage
         * initis[1] - centerImage
         * initis[2] - rightImage
         * при листании карусельки изменяются и текущие данные в initis[]
         */

        clearTimeout(idto);

		if ( typeof(_gaq) !== 'undefined' && typeof(currImg.pos) !== 'undefined' && typeof(currImg.imgb) !== 'undefined' && typeof(currImg.ga) !== 'undefined' ) {
			console.log( '## click on bigbanner:' );
			_gaq.push( ['_trackEvent', 'BannerClick', '' + currImg.pos ] );
			_gaq.push( ['_trackEvent', 'Carousel', 'Click_' + currImg.pos, currImg.imgb ] );
			console.log( 'GA: _trackEvent, BannerClick,', '' + currImg.pos );
			console.log( 'GA: _trackEvent, Carousel, Click_' + currImg.pos + ' | ' + currImg.imgb + currImg.ga );
		}
        addKISSmetricsEvent('Carousel banner view', 'bigbanner', $(this));
        location.href = $(this).data('url');
    });

    $('.promos').click(function () {
        location.href = $(this).data('url');
    });
    $('.centerImage').hover(function () {
        permission = false;
    }, function () {
        permission = true;
    });

    function sideBanner(block, i) {
        $(block).animate({
                "opacity":"0"
            },
            400,
            function () {
                setTimeout(function () {
                    block.attr({ "src":initis[i].imgs, "alt":initis[i].alt, "title":initis[i].alt})
                    $(block).animate({
                        "opacity":"1"
                    })
                }, 350);
            });
    }

    function changeSrc(currentSl) {
        var delta = l - currentSl - 3;
        if (delta >= 0)
            initis = promos.slice(currentSl, currentSl + 3);
        else
            initis = promos.slice(currentSl).concat(promos.slice(0, -delta));
    }

    function goSlide( dir, isClick ) {
		dir = dir || 0;
		isClick = isClick || false;
        if ( !permission ) {
            idto = setTimeout(function () {
                goSlide();
            }, initis[1].t);
            return;
        }
        if (sliding)
            return false;
        sliding = true;
        if ( 0 === dir )
            dir = 1;
        else // custom call
            clearTimeout(idto);
        var shift = '-=1000px',
            inileft = '1032px';

        if (dir < 0) {
            shift = '+=1000px',
                inileft = '-968px'
        }
        currentSl = (currentSl + dir) % l;
        if (currentSl < 0) {
            currentSl = l - 1;
        }
        changeSrc(currentSl);

        $('.centerImage').animate(
            {
                'left':shift,
                'opacity':'0'
            },
            800,
            function () {
                $('.centerImage').attr("src", initis[1].imgb).data('url', initis[1].url)
                $(this).css('left', inileft).animate({
                    'opacity':'1',
                    'left':shift
                })
                sliding = false;
                idto = setTimeout(function () {
                    goSlide();
                }, initis[1].t) // AUTOPLAY
            });
        sideBanner($('.leftImage'), 0);
        sideBanner($('.rightImage'), 2);

		var
			currImg = initis[1];
		/**
		 * Текущие изобрежения слайдера хранятся в initis так:
		 * initis[0] - leftImage
		 * initis[1] - centerImage
		 * initis[2] - rightImage
		 * при листании карусельки изменяются и текущие данные в initis[]
		 */

		if ( typeof(_gaq) !== 'undefined' && typeof(currImg.pos) !== 'undefined' && typeof(currImg.imgb) !== 'undefined' && typeof(currImg.ga) !== 'undefined') {
			/** Отправляет аналитику в GA при любой (ручной или автоматической) смене элементов слайдера */
			_gaq.push( ['_trackEvent', 'Carousel', 'View_' + currImg.pos, currImg.imgb ] );
			console.log( '#GA: _trackEvent, Carousel, View_' + currImg.pos + ' | ' + currImg.imgb + currImg.ga );
			if ( isClick ) {
				/** Сработает только при ручной смене баннера (при клике) */
				_gaq.push(['_trackEvent', 'BannerClick', '' + currImg.pos ]);
				console.log('GA: _trackEvent BannerClick', currImg.pos);

				if ( 'function' === typeof(ga) ) {
					/** Google Analytics Universal */
					ga( 'send', 'event', 'Internal_Promo', currImg.pos );
					console.log( 'GA: send', 'event', 'Internal_Promo', currImg.pos );
				}
			}
		}
    }
});
