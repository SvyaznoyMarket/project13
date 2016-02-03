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
		animationDuration = 400,
		viewedBanners = {},

		slidesWidth = 473,
		slidesDotClass = 'slidesBox_dott_i',

		// НИЖНИЕ СЛАЙДЕРЫ
		slidesDotActiveClass = 'slidesBox_dott_i-act',
		slidesWideWidth = 958,
		$jsSlidesWideHolder = $('.jsSlidesWideHolder').first(),
		$jsSlidesWideItems = $('.jsSlidesWideItem'),
		$jsSlidesWideName = $('.jsSlidesWideName'),

		// ПРОСМОТРЕННЫЕ ТОВАРЫ
		$viewedSliders = $('.js-viewed-slider');

	function slideRecommendations($block, toIndex) {

		var $dots = $block.find('.' + slidesDotClass);

		$block.find('.jsMainSlidesProductBlock').animate({
			'margin-left' : - toIndex * slidesWidth
		}, {
			duration: 0,
			complete: function(){
				$dots.removeClass(slidesDotActiveClass);
				$dots.eq(toIndex).addClass(slidesDotActiveClass);
				/* Ecommerce analytic */
				$.each($block.find('.jsBuyButton').slice( toIndex * 4, (toIndex + 1) * 4), function(i,el) {
					ENTER.utils.analytics.addImpression(el, {
						list: $block.data('block'),
						position: toIndex * 4 + i
					});
				});
				$body.trigger('trackGoogleEvent',['RR_взаимодействие', 'Пролистывание', $block.data('block')])
			}
		});
	}

	// Слайдеры рекомендаций
	$body.on('click', '.jsMainSlidesButton', function(){
		var $block = $(this).closest('.jsMainSlidesRetailRocket'), // родительский блок
			$dots = $block.find('.' + slidesDotClass), // точки навигации
			index = $dots.index($block.find('.' + slidesDotActiveClass).first()),
			nextIndex = $(this).hasClass('jsMainSlidesLeftButton') ? index - 1 : index + 1;

		if (nextIndex == $block.find('.jsMainSlidesProductBlock').data('count')) nextIndex = 0;
		if (nextIndex == -1) nextIndex = $block.find('.jsMainSlidesProductBlock').data('count') - 1;

		slideRecommendations($block, nextIndex);

	});

	// Клик по навигации в слайдерах рекомендаций
	$body.on('click', '.jsMainSlidesRetailRocket .slidesBox_dott_i', function(){

		var $this = $(this),
			$block = $(this).closest('.jsMainSlidesRetailRocket'),
			$dots = $block.find('.slidesBox_dott_i'),
			index = $dots.index($this);

		slideRecommendations($block, index);
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
	 	timeoutId = setTimeout(showNextSlide, parseInt(timeout, 10));
	}

	function showNextSlide(direction, times) {
		var index = $bannerThumbs.find('img').index($bannerThumbs.find('img.'+activeThumbClass)),
			bannersLength = $bannerThumbs.length,
            dir = typeof direction != 'undefined' ? direction : 1,
			nextIndex = index + dir == bannersLength ? 0 : index + dir,
            duration = times || 400,
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

	// Листалка широкого нижнего слайдера
	$body.on('click', '.jsSlidesWideLeft, .jsSlidesWideRight', function(){
		var index = $('.jsSlidesWide .slidesBox_dott_i').index($('.jsSlidesWide .' + slidesDotActiveClass)),
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

	if ($viewedSliders.length) {
		$viewedSliders.each(function(i,dom){
			var url = $(dom).data('slider').url;
			if (url) {
				$.ajax(url).done(function(resp){
					if (resp.result && resp.result.content) {
						$(dom).after(resp.result.content).remove();
					}
				});
			}
		});
	}

	// БЛОК "ВЫ СМОТРЕЛИ" (новый дизайн)
	$body.on('click', '.jsViewedBlock', function(e){
		var $target = $(e.target),
				$this = $(this),
				$holder = $('.jsViewedBlockHolder', $this),
				currentIndex = $('.jsViewedBlockDot', $this).index($('.slidesBox_dott_i-act', $this)),
				index, direction;

		function animate(index) {
			$holder.animate({
				'margin-left': - (index * 920)
			}, {
				complete: function(){
					$('.slidesBox_dott_i', $this).removeClass('slidesBox_dott_i-act');
					$('.slidesBox_dott_i', $this).eq(index).addClass('slidesBox_dott_i-act')
				}
			});
			$body.trigger('trackGoogleEvent', {
				category: 'RR_взаимодействие',
				action: 'Пролистывание',
				label: 'Interest_Main'
			})
		}

		if ($target.hasClass('jsViewedBlockDot')) {
			index = $('.jsViewedBlockDot', $this).index($target);
			animate(index);
		} else if ($target.hasClass('jsViewedBlockArror')) {
			direction = $target.data('direction');
			if (currentIndex == 0 && direction == -1) {
				animate($('.slidesBox_dott_i', $this).length - 1);
			} else if (currentIndex + 1 == $('.slidesBox_dott_i', $this).length && direction == 1) {
				animate(0);
			} else {
				animate(currentIndex + direction)
			}
		}
	});

	// БЛОК "ВЫ СМОТРЕЛИ" (сезонный дизайн)
	$body.on('click', '.jsSeasonViewed', function(e) {
		var $target = $(e.target),
				WIDTH = 224 * 4,
				$this = $(this),
				disabledClass = 'disabled',
				$buttons = $('.jsSeasonBtn', $this),
				$holder = $('.jsSeasonViewedHolder', $this),
				productsCount = $holder.data('count'),
				maxMargin = - Math.floor((productsCount-1)/4) * WIDTH,
				currentMargin = parseInt($holder.css('margin-left'), 10);

		if (!$target.hasClass('jsSeasonBtn')) return;

		var direction = $target.data('direction'),
			nextMargin = currentMargin - (direction * WIDTH);

		if (nextMargin >= maxMargin && nextMargin <= 0 && !$holder.is(':animated')) {
			$holder.animate({
				'margin-left': currentMargin - (direction * WIDTH)
			}, {
				complete: function () {

					$buttons.removeClass(disabledClass);

					if (nextMargin == 0) {
						$buttons.eq(0).addClass(disabledClass);
					}

					if (parseInt($holder.css('margin-left'), 10) == maxMargin) {
						$buttons.eq(1).addClass(disabledClass);
					}
				}
			});
			$body.trigger('trackGoogleEvent', {
				category: 'RR_взаимодействие',
				action: 'Пролистывание',
				label: 'Present_Main'
			})
		}

	});

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

	$body.on('click', '.jsSeasonViewed .jsBuyButton, .jsViewedBlock .jsBuyButton', function(e) {
		var blockname = $(this).closest('.jsSeasonViewed').length ? 'Present_Main' : 'Interest_Main';
		e.preventDefault();
		$body.trigger('trackGoogleEvent', {
			category: 'RR_взаимодействие',
			action: 'Добавил в корзину',
			label: blockname
		})
	});

	$body.on('click', '.jsProductLinkViewedMain', function(e) {
		var blockname = $(this).closest('.jsSeasonViewed').length ? 'Present_Main' : 'Interest_Main',
				link = $(this).attr('href');
		e.preventDefault();
		$body.trigger('trackGoogleEvent', {
			category: 'RR_взаимодействие',
			action: 'Перешел на карточку товара',
			label: blockname,
			hitCallback: function(){
				window.location.href = link
			}
		})
	});

	$body.on('mainBannerView', function(event, bannerIndex) {

		var $banner = $bannerThumbs.eq(bannerIndex),
			data = {
				id: $banner.data('uid'),
				name: $banner.data('name'),
				position: bannerIndex + 1
			};

		if (ENTER.utils.analytics.isEnabled() && typeof viewedBanners[data.id] == 'undefined') {
			// Добавляем баннер в ecommerce
			ga('ec:addPromo', data);
			// Отсылаем событие
			$body.trigger('trackGoogleEvent', {
				category: 'Internal Promotions',
				action: 'view',
				label: data.name,
				nonInteraction: 1
			});
			// Добавляем баннер в просмотренные баннеры
			viewedBanners[data.id] = data;
		}
	});

	// Tрекаем первый баннер
	$body.trigger('mainBannerView', 0);

	// Клики по товарам в рекомендациях
	$body.on('click', '.jsMainSlidesRetailRocket a:not(.js-orderButton)', function(e){
		var block = $(this).closest('.jsMainSlidesRetailRocket').data('block'),
			$productContainer = $(this).closest('.jsProductContainer'),
			link = $(this).attr('href'),
            aTarget = $(this).attr('target');

		if (aTarget != '_blank') e.preventDefault();

		ENTER.utils.analytics.addProduct($productContainer[0], {
			position: $productContainer.data('position')
		});

		ENTER.utils.analytics.setAction('click', {list: block});

		$body.trigger('trackGoogleEvent', {
			category: 'RR_взаимодействие',
			action: 'Перешел на карточку товара',
			label: block,
			hitCallback: aTarget == '_blank' ? null : link
		})
	});

	$('.js-slider-2').goodsSlider({
        leftArrowSelector: '.newyear-gifts-slider__btn_prev',
        rightArrowSelector: '.newyear-gifts-slider__btn_next',
        sliderWrapperSelector: '.newyear-gifts-slider-wrap',
        sliderSelector: '.newyear-gifts-slider',
        itemSelector: '.newyear-gifts-slider__item'
    });

	//

	$bannerWrapper.on('wheel', function(event){

		event = event || window.event;

		var direction = $(this).hasClass(bannersUpClass) ? -1 : 1,
			delta = event.originalEvent.deltaY || event.originalEvent.detail || event.originalEvent.wheelDelta; // Направление колёсика мыши

		if(!(navigator.platform.toLowerCase() == 'macintel')){
			if(navigator.userAgent.indexOf('Chrome') !== -1){
				if((delta >= -100) && (delta <= 100)){

					if(delta < 0){
						direction = -direction;
					}
					stopSlider();
					showNextSlide(direction, 1);
					event.preventDefault();
				}
			}else{
				if((delta >= -1) && (delta <= 1)){

					if(delta < 0){
						direction = -direction;
					}
					stopSlider();
					showNextSlide(direction, 1);
					event.preventDefault();
				}
			}
		}
	});


}(jQuery));