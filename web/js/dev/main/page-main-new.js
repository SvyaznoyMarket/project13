;(function($){
	var $body = $(document.body),

		// БАННЕРЫ
		$bannerHolder = $('.jsMainBannerHolder'),
		bannerHeight = 299,
		$banners = $('.jsMainBannerImage'), // баннеры
		$bannerThumbs = $('.jsMainBannerThumb'), // превью баннеров
		activeThumbClass = 'slidesbnnr_thmbs_img-act',

		// НИЖНИЕ СЛАЙДЕРЫ
		slidesWideActiveDotClass = 'slidesBox_dott_i-act',
		slidesWideWidth = 958,
		$jsSlidesWideHolder = $('.jsSlidesWideHolder').first(),
		$jsSlidesWideItems = $('.jsSlidesWideItem');

	// Ничего не выполняем, если у нас не новая главная
	if (!$body.hasClass('jsMainNew')) return;

	// Слайдеры рекомендаций
	$body.on('click', '.jsMainSlidesButton', function(){
		var step = 473,
			$block = $(this).closest('.jsMainSlidesRetailRocket');
		if ($(this).hasClass('jsMainSlidesLeftButton')) {
			$block.find('.jsMainSlidesProductBlock').css('margin-left', '+='+step);
		} else {
			$block.find('.jsMainSlidesProductBlock').css('margin-left', '-='+step);
		}

	});

	// Маленькие блоки с информацией под баннерами
	$body.on('click', '.jsShopInfoPreview', function(){
		$('.jsShopInfoBlock').hide();
		$('.jsShopInfoBlock[data-id='+ $(this).data('id')+']').toggle();
	});

	// Листалка баннеров
	$body.on('click', '.jsMainBannerThumb',function(){
		var $this = $(this),
			index = $bannerThumbs.index($this);

		$bannerHolder.animate({
			'margin-top': -(index * bannerHeight)
		},{
			duration: 200,
			complete: function(){
				$bannerThumbs.find('img').removeClass(activeThumbClass);
				$this.find('img').addClass(activeThumbClass);
			}
		})
	});

	$jsSlidesWideHolder.css('width', slidesWideWidth * $jsSlidesWideItems.length);

	// Листалка нижнего слайдера
	$body.on('click', '.jsSlidesWideLeft, .jsSlidesWideRight', function(){
		var index = $('.jsSlidesWide .slidesBox_dott_i').index($('.jsSlidesWide .'+slidesWideActiveDotClass)),
			nextIndex = $(this).hasClass('jsSlidesWideLeft') ? index - 1: index + 1,
			margin = - nextIndex * slidesWideWidth;

		if (nextIndex == $jsSlidesWideItems.length || nextIndex < 0) return;

		$jsSlidesWideHolder.animate({
			'margin-left': margin
		},{
			complete: function(){
				$('.jsSlidesWide .slidesBox_dott_i').removeClass(slidesWideActiveDotClass).eq(nextIndex).addClass(slidesWideActiveDotClass);
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
				$('.jsSlidesWide .slidesBox_dott_i').removeClass(slidesWideActiveDotClass);
				$this.addClass(slidesWideActiveDotClass);
			}
		});
	});

	// Если нет блоков RR, то загрузим их через AJAX, там таймаут побольше на ответ от RR поставлен
	if ($('.jsMainSlidesRetailRocket').length == 0) {
		$.get('/index/recommend').done(function(data){
			if (data.result) {
				$(data.result).insertBefore($('.jsDivBeforeRecommend'));
			}
		})
	}


}(jQuery));