$(function() {
    var
        $body = $('body')
    ;

    // клик на главном баннере
    $body.on('click', '.jsMainBannerLink', function() {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'click',
            label: 'banner'
        });
    });

    // скролл на главном баннере
    $body.on('click', '.jsMainBannerThumb, .jsMainBannersButton', function(e) {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'scroll',
            label: 'banner'
        });
    });

    // клик на нижнем баннере
    $body.on('click', '.jsSlidesWideItem', function() {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'click',
            label: 'collection'
        });
    });

    // скрол на нижнем баннере
    $body.on('click', '.jsSlidesWideButton', function(e) {
        $body.trigger('trackGoogleEvent', {
            category: 'banner_main',
            action: 'scroll',
            label: 'collection'
        });
    });

    // клик по бренду
    $body.on('click', '.jsMainBrand', function(e) {
        $body.trigger('trackGoogleEvent', {
            category: 'brand_main',
            action: $(this).attr('title'),
            label: ''
        });
    });

    // клик по трастфактору
    $body.on('click', '.jsShopInfoPreview', function(){
        $body.trigger('trackGoogleEvent', {
            category: 'trust_main',
            action: $(this).data('name'),
            label: ''
        });
    });
});
;(function($){
	var $body = $(document.body),
		timeoutId,      // id таймаута для пролистывания баннеров

		// БАННЕРЫ
		$bannerWrapper = $('.jsMainBannerWrapper'),
		$bannerHolder = $('.jsMainBannerHolder'),
		bannerHeight = 240,
		$bannerThumbs = $('.jsMainBannerThumb'), // превью баннеров
        $bannerThumbsWrapper = $('.jsMainBannerThumbsWrapper'),
		activeThumbClass = 'slidesbnnr_thmbs_img-act',
        visibleThumbsCount = 4,         // количество видимых превьюшек баннеров
        thumbsHeightWithMargin = 58,    // количество пикселов для промотки превьюшек
        $bannersButtons = $('.jsMainBannersButton'),    // кнопки вверх-вниз у превьюшек
        bannersUpClass = 'jsMainBannersUpButton',

		slidesWidth = 473,
		slidesDotClass = 'slidesBox_dott_i',

		// НИЖНИЕ СЛАЙДЕРЫ
		slidesDotActiveClass = 'slidesBox_dott_i-act',
		slidesWideWidth = 958,
		$jsSlidesWideHolder = $('.jsSlidesWideHolder').first(),
		$jsSlidesWideItems = $('.jsSlidesWideItem'),
		$jsSlidesWideName = $('.jsSlidesWideName');

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

    /**
     * Остановка слайдера
     */
    function stopSlider(){
        clearTimeout(timeoutId);
    }

    /**
     * Запуск слайдера
     */
    function startSlider(){
        autoSlide($bannerThumbs.find('img.'+activeThumbClass).data('timeout'));
    }

    // остановка/воспроизведение листалки при наведении на баннеры
    $bannerWrapper.hover(function(){
        stopSlider()
    }, function(){
        startSlider()
    });

    /** остановка/воспроизведение c visibility api (пробная реализация)
     * @link https://developer.mozilla.org/en-US/docs/Web/Guide/User_experience/Using_the_Page_Visibility_API
     * */
    if (typeof document.hidden !== 'undefined') {
        document.addEventListener('visibilitychange', function(){
            if (document.hidden) {
                stopSlider()
            } else {
                startSlider()
            }
        }, false);
    }

    // клик по кнопкам превьюшек баннеров
    $bannersButtons.on('click', function(){
        var direction = $(this).hasClass(bannersUpClass) ? -1 : 1;
        stopSlider();
        showNextSlide(direction);
    });

	// запускаем листалку при загрузке
	startSlider();

	// Автоматическая листалка
	function autoSlide(timeout) {
        stopSlider();
	 	timeoutId = setTimeout(showNextSlide, parseInt(timeout, 10))
	}

	function showNextSlide(direction) {
		var index = $bannerThumbs.find('img').index($bannerThumbs.find('img.'+activeThumbClass)),
			bannersLength = $bannerThumbs.length,
            dir = typeof direction != 'undefined' ? direction : 1,
			nextIndex = index + dir == bannersLength ? 0 : index + dir,
            duration = 400,
            thumbsMarginMultiplier;

        if (nextIndex == -1) nextIndex = bannersLength - 1; // fix для нажатия кнопки вверх у превью

        // если текущий баннер является последним и мы не пролистываем назад, то пролистываем на первый мгновенно
        if (index == bannersLength - 1 && dir != -1) duration = 0;

        thumbsMarginMultiplier = nextIndex >= visibleThumbsCount ? nextIndex - visibleThumbsCount + 1 : 0;

		// пролистываем баннеры
        $bannerHolder.animate({
			'margin-top': -(nextIndex * bannerHeight)
		},{
			duration: duration,
			complete: function(){
				$bannerThumbs.find('img').removeClass(activeThumbClass);
				$bannerThumbs.find('img').eq(nextIndex).addClass(activeThumbClass);
				autoSlide($bannerThumbs.find('img').eq(nextIndex).data('timeout'));
				$body.trigger('mainBannerView', nextIndex)
			}
		});

        // пролистываем превью баннеров
        $bannerThumbsWrapper.animate({
            'margin-top': -(thumbsMarginMultiplier * thumbsHeightWithMargin)
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

	// Если нет блоков RR, то загрузим их через AJAX, там таймаут побольше на ответ от RR поставлен
	if ($('.jsMainSlidesRetailRocket').length == 0) {
		$.get('/index/recommend').done(function(data){
			if (data.result) {
				$('.jsDivForRecommend').append($(data.result));
			}
		})
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
	/*$body.on('mainBannerView', function(e, index) {
		$body.trigger('trackGoogleEvent',['slider view', 'main banner', index + 1 + ''])
	});*/

	// пролистывание рекомендаций
	$body.on('click', '.jsMainSlidesRetailRocket .jsMainSlidesButton, .jsMainSlidesRetailRocket .slidesBox_dott', function(){
		var block = $(this).closest('.jsMainSlidesRetailRocket').data('block');
		$body.trigger('trackGoogleEvent',['RR_взаимодействие', 'Пролистывание', block])
	});

	$body.on('click', '.jsMainSlidesRetailRocket a:not(.js-orderButton)', function(e){
		var block = $(this).closest('.jsMainSlidesRetailRocket').data('block'),
			link = $(this).attr('href'),
            aTarget = $(this).attr('target');
		if (aTarget != '_blank') e.preventDefault();
		$body.trigger('trackGoogleEvent', {
			category: 'RR_взаимодействие',
			action: 'Перешел на карточку товара',
			label: block,
			hitCallback: aTarget == '_blank' ? null : link
		})
	});

}(jQuery));