;(function($){
	var $body = $(document.body),
		timeoutId,

		// БАННЕРЫ
		$bannerWrapper = $('.jsMainBannerWrapper'),
		$bannerHolder = $('.jsMainBannerHolder'),
		bannerHeight = 300,
		$bannerThumbs = $('.jsMainBannerThumb'), // превью баннеров
		activeThumbClass = 'slidesbnnr_thmbs_img-act',

		slidesWidth = 473,
		slidesDotClass = 'slidesBox_dott_i',

		// НИЖНИЕ СЛАЙДЕРЫ
		slidesDotActiveClass = 'slidesBox_dott_i-act',
		slidesWideWidth = 958,
		$jsSlidesWideHolder = $('.jsSlidesWideHolder').first(),
		$jsSlidesWideItems = $('.jsSlidesWideItem'),
		$jsSlidesWideName = $('.jsSlidesWideName');

	// Ничего не выполняем, если у нас не новая главная
	if (!$body.hasClass('jsMainNew')) return;

	// Слайдеры рекомендаций
	$body.on('click', '.jsMainSlidesButton', function(){
		var step = slidesWidth,
			$block = $(this).closest('.jsMainSlidesRetailRocket'), // родительский блок
			$dots = $block.find('.' + slidesDotClass), // точки навигации
			index = $dots.index($block.find('.' + slidesDotActiveClass).first()),
			nextIndex = $(this).hasClass('jsMainSlidesLeftButton') ? index - 1 : index + 1
			;

		if (nextIndex == $block.find('.jsMainSlidesProductBlock').data('count')) nextIndex = 0;
		if (nextIndex == -1) nextIndex = $block.find('.jsMainSlidesProductBlock').data('count') - 1;

		if ($(this).hasClass('jsMainSlidesLeftButton')) {
			$block.find('.jsMainSlidesProductBlock').css('margin-left', - nextIndex * step);
			$dots.removeClass(slidesDotActiveClass);
			$dots.eq(nextIndex).addClass(slidesDotActiveClass)
		} else {
			$block.find('.jsMainSlidesProductBlock').css('margin-left', - nextIndex * step);
			$dots.removeClass(slidesDotActiveClass);
			$dots.eq(nextIndex).addClass(slidesDotActiveClass)
		}

	});

	// Клик по навигации в слайдерах рекомендаций
	$body.on('click', '.jsMainSlidesRetailRocket .slidesBox_dott_i', function(){

		var $this = $(this),
			$block = $(this).closest('.jsMainSlidesRetailRocket'),
			$dots = $block.find('.slidesBox_dott_i'),
			index = $dots.index($this),
			margin = index * slidesWidth;

		$block.find('.jsMainSlidesProductBlock').animate({
			'margin-left': - margin
		},{
			complete: function(){
				$dots.removeClass(slidesDotActiveClass);
				$this.addClass(slidesDotActiveClass);
			}
		})
	});

	// Маленькие блоки с информацией под баннерами
	$body.on('click', '.jsShopInfoPreview', function(){
		var $block = $('.jsShopInfoBlock[data-id='+ $(this).data('id')+']');
		$('.jsShopInfoBlock').not($block).hide();
		$block.toggle();
	});

	// Листалка баннеров
	$body.on('click', '.jsMainBannerThumb',function(){
		var $this = $(this),
			index = $bannerThumbs.index($this);

		clearTimeout(timeoutId); // очистим при клике
		autoSlide($this.find('img').data('timeout'));

		$bannerHolder.animate({
			'margin-top': -(index * bannerHeight)
		},{
			duration: 400,
			complete: function(){
				$bannerThumbs.find('img').removeClass(activeThumbClass);
				$this.find('img').addClass(activeThumbClass);
				$body.trigger('mainBannerView', index)
			}
		})
	});

	// запускаем листалку при загрузке
	autoSlide($bannerThumbs.find('img.'+activeThumbClass).data('timeout'));

	// прекращаем листать при наведении на крутилку
	$body.on('mouseenter', '.jsMainBannerWrapper', function() {
		stopSlide();
	});

	$body.on('mouseout', '.jsMainBannerWrapper', function() {
		autoSlide($bannerThumbs.find('img.'+activeThumbClass).data('timeout'));
	});

	// Автоматическая листалка
	function autoSlide( timeout) {
	 	timeoutId = setTimeout(showNextSlide, parseInt(timeout, 10))
	}

	function stopSlide( timeout ) {
		console.log('slidesStop');
		clearTimeout(timeoutId);
	}

	function showNextSlide() {
		var index = $bannerThumbs.find('img').index($bannerThumbs.find('img.'+activeThumbClass)),
			bannersLength = $bannerThumbs.length,
			nextIndex = index + 1 == bannersLength ? 0 : index + 1;

		$bannerHolder.animate({
			'margin-top': -(nextIndex * bannerHeight)
		},{
			duration: 400,
			complete: function(){
				$bannerThumbs.find('img').removeClass(activeThumbClass);
				$bannerThumbs.find('img').eq(nextIndex).addClass(activeThumbClass);
				autoSlide($bannerThumbs.find('img').eq(nextIndex).data('timeout'));
				$body.trigger('mainBannerView', nextIndex)
			}
		})
	}

	// Установка корректной ширины блоков со слайдерами
	$jsSlidesWideHolder.css('width', slidesWideWidth * $jsSlidesWideItems.length);

	$('.jsMainSlidesProductBlock').each(function(){
		var $this = $(this);
		$this.css('width', $this.data('count') * slidesWidth)
	});

	// Листалка нижнего слайдера
	$body.on('click', '.jsSlidesWideLeft, .jsSlidesWideRight', function(){
		var index = $('.jsSlidesWide .slidesBox_dott_i').index($('.jsSlidesWide .'+slidesDotActiveClass)),
			nextIndex = $(this).hasClass('jsSlidesWideLeft') ? index - 1: index + 1,
			margin;

		if (nextIndex == $jsSlidesWideItems.length) nextIndex = 0;
		if (nextIndex == -1 ) nextIndex = $jsSlidesWideItems.length - 1;

		margin = - nextIndex * slidesWideWidth;

		$jsSlidesWideHolder.animate({
			'margin-left': margin
		},{
			complete: function(){
				$('.jsSlidesWide .slidesBox_dott_i').removeClass(slidesDotActiveClass).eq(nextIndex).addClass(slidesDotActiveClass);
				$jsSlidesWideName.text($('.jsSlidesWide .' + slidesDotActiveClass).data('name'));
				$body.trigger('mainSlidesWideView', [nextIndex])
			}
		});
	});

	// Клики по маленьким точкам на широком нижнем баннере
	$body.on('click', '.jsSlidesWide .slidesBox_dott_i', function(){
		var $this = $(this),
			index = $('.jsSlidesWide .slidesBox_dott_i').index($this),
			margin = index * slidesWideWidth;

		$jsSlidesWideHolder.animate({
			'margin-left': - margin
		},{
			complete: function(){
				$('.jsSlidesWide .slidesBox_dott_i').removeClass(slidesDotActiveClass);
				$this.addClass(slidesDotActiveClass);
				$jsSlidesWideName.text($this.data('name'));
				$body.trigger('mainSlidesWideView', [index])
			}
		});
	});

	function bindUserModel() {
		$('.jsDivForRecommend').each(function(index, element){
			ko.applyBindings(ENTER.UserModel, element);
		});
	}

	// Если нет блоков RR, то загрузим их через AJAX, там таймаут побольше на ответ от RR поставлен
	if ($('.jsMainSlidesRetailRocket').length == 0) {
		$.get('/index/recommend').done(function(data){
			if (data.result) {
				$('.jsDivForRecommend').append($(data.result));
				bindUserModel();
			}
		})
	} else {
		bindUserModel();
	}

	// АНАЛИТИКА
	// переход по нижним категориям
	$body.on('click', '.jsMainCategoryTracking a', function(e){
		var categoryName = $(this).find('span').text(),
			link = $(this).attr('href');
		e.preventDefault();
		$body.trigger('trackGoogleEvent', {
			category: 'main category',
			action: 'bottom menu',
			label: categoryName,
			hitCallback: function(){
				window.location.href = link
			}
		})
	});

	// просмотр коллекций
	$body.on('mainSlidesWideView', function(e, index) {
		$body.trigger('trackGoogleEvent',['slider view', 'main collections', index + 1 + ''])
	});

	// просмотр главного баннера
	$body.on('mainBannerView', function(e, index) {
		$body.trigger('trackGoogleEvent',['slider view', 'main banner', index + 1 + ''])
	});

	// пролистывание рекомендаций
	$body.on('click', '.jsMainSlidesRetailRocket .jsMainSlidesButton, .jsMainSlidesRetailRocket .slidesBox_dott', function(){
		var block = $(this).closest('.jsMainSlidesRetailRocket').data('block');
		$body.trigger('trackGoogleEvent',['RR_взаимодействие', 'Пролистывание', block])
	});

	$body.on('click', '.jsMainSlidesRetailRocket a:not(.jsBuyButton, .jsOneClickButton-new)', function(e){
		var block = $(this).closest('.jsMainSlidesRetailRocket').data('block'),
			link = $(this).attr('href');
		e.preventDefault();
		$body.trigger('trackGoogleEvent', {
			category: 'RR_взаимодействие',
			action: 'Перешел на карточку товара',
			label: block,
			hitCallback: function(){
				window.location.href = link
			}
		})
	});

}(jQuery));
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
                    //_gaq.push( ['_trackEvent', 'Carousel', 'Click_' + currImg.pos, currImg.imgb ] ); SITE-3952
                }
                location.href = $(this).data('url');
            });

            exclImg.mouseover(function () {
                if ( typeof(_gaq) !== 'undefined' && typeof(currImg.pos) !== 'undefined' && typeof(currImg.imgb) !== 'undefined' && typeof(currImg.ga) !== 'undefined') {
                    //_gaq.push(['_trackEvent', 'BannerClick', initis[1].ga ]);
                    //_gaq.push( ['_trackEvent', 'Carousel', 'View_' + currImg.pos, currImg.imgb ] ); SITE-3952
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
			//_gaq.push( ['_trackEvent', 'Carousel', 'Click_' + currImg.pos, currImg.imgb ] ); SITE-3952
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
			// _gaq.push( ['_trackEvent', 'Carousel', 'View_' + currImg.pos, currImg.imgb ] ); SITE-3952
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
