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