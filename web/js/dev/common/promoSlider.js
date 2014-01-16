;(function($){	
	/*paginator*/
	var EnterPaginator = function( domID,totalPages, visPages, activePage ) {
		
		var self = this;

		self.inputVars = {
			domID: domID, // id элемента для пагинатора
			totalPages:totalPages, //общее количество страниц
			visPages:visPages?visPages:10, // количество видимых сраниц
			activePage:activePage?activePage:1 // текущая активная страница
		};

		var pag = $('#'+self.inputVars.domID), // пагинатор
			pagW = pag.width(), // ширина пагинатора
			eSliderFillW = (pagW*self.inputVars.visPages)/self.inputVars.totalPages, // ширина закрашенной области слайдера
			onePageOnSlider = eSliderFillW / self.inputVars.visPages, // ширина соответствующая одной странице на слайдере
			onePage = pagW / self.inputVars.visPages, // ширина одной цифры на пагинаторе
			center = Math.round(self.inputVars.visPages/2);
		// end of vars

		var scrollingByBar = function scrollingByBar ( left ) {
			var pagLeft = Math.round(left/onePageOnSlider);

			$('.bPaginator_eWrap', pag).css('left', -(onePage * pagLeft));
		};

		var enableHandlers = function enableHandlers() {
			// биндим хандлеры
			var clicked = false,
				startX = 0,
				nowLeft = 0;
			// end of vars
			
			$('.bPaginatorSlider', pag).bind('mousedown', function(e){
				startX = e.pageX;
				nowLeft = parseInt($('.bPaginatorSlider_eFill', pag).css('left'), 10);
				clicked = true;
			});

			$('.bPaginatorSlider', pag).bind('mouseup', function(){
				clicked = false;
			});

			pag.bind('mouseout', function(){
				clicked = false;
			});

			$('.bPaginatorSlider', pag).bind('mousemove', function(e){
				if ( clicked ) {
					var newLeft = nowLeft+(e.pageX-startX);

					if ( (newLeft >= 0) && (newLeft <= pagW - eSliderFillW) ) {
						$('.bPaginatorSlider_eFill', pag).css('left', nowLeft + (e.pageX - startX));
						scrollingByBar(newLeft);
					}
				}
			});
		};

		var init = function init() {
			pag.append('<div class="bPaginator_eWrap"></div>');
			pag.append('<div class="bPaginatorSlider"><div class="bPaginatorSlider_eWrap"><div class="bPaginatorSlider_eFill" style="width:'+eSliderFillW+'px"></div></div></div>');
			for ( var i = 0; i < self.inputVars.totalPages; i++ ) {
				$('.bPaginator_eWrap', pag).append('<a class="bPaginator_eLink" href="#' + i + '">' + (i + 1) + '</a>');

				if ( (i + 1) === self.inputVars.activePage ) {
					$('.bPaginator_eLink', pag).eq(i).addClass('active');
				}
			}
			var realLinkW = $('.bPaginator_eLink', pag).width(); // реальная ширина цифр

			$('.bPaginator_eLink', pag).css({'marginLeft':(onePage - realLinkW - 2)/2, 'marginRight':(onePage - realLinkW - 2)/2}); // размазываем цифры по ширине слайдера
			$('.bPaginator_eWrap', pag).addClass('clearfix').width(onePage * self.inputVars.totalPages); // устанавливаем ширину wrap'а, добавляем ему очистку
		};

		self.setActive = function ( page ) {
			var left = parseInt($('.bPaginator_eWrap', pag).css('left'), 10), // текущее положение пагинатора
				barLeft = parseInt($('.bPaginatorSlider_eFill', pag).css('left'), 10), // текущее положение бара
				nowLeftElH = Math.round(left/onePage) * (-1), // количество скрытых элементов
				diff = -(center - (page - nowLeftElH)); // на сколько элементов необходимо подвинуть пагинатор для центрирования
			// end of vars
			
			$('.bPaginator_eLink', pag).removeClass('active');
			$('.bPaginator_eLink', pag).eq(page).addClass('active');

			if ( left - (diff * onePage) > 0 ) {
				left = 0;
				barLeft = 0;
			}
			else if ( page > self.inputVars.totalPages - center ) {
				left = Math.round(self.inputVars.totalPages - self.inputVars.visPages) * onePage*(-1);
				barLeft = Math.round(self.inputVars.totalPages - self.inputVars.visPages) * onePageOnSlider;
			}
			else {
				left = left - (diff * onePage);
				barLeft = barLeft + (diff * onePageOnSlider);
			}

			$('.bPaginator_eWrap').animate({'left': left});
			$('.bPaginatorSlider_eFill', pag).animate({'left': barLeft});
		};

		init();
		enableHandlers();
	};

	/* promo catalog */
	if ( $('#promoCatalog').length ) {
		var data = $('#promoCatalog').data('slides'),
		
		//первоначальная настройка
			slider_SlideCount = data.length, //количество слайдов
			catalogPaginator = new EnterPaginator('promoCatalogPaginator',slider_SlideCount, 12, 1);
		// end of vars

		var initSlider = function initSlider() {
			for ( var slide in data ) {
				var slideTmpl = tmpl('slide_tmpl', data[slide]);

				$('.bPromoCatalogSliderWrap').append(slideTmpl);

				if ( $('.bPromoCatalogSliderWrap_eSlideLink').eq(slide).attr('href') === '' ) {
					$('.bPromoCatalogSliderWrap_eSlideLink').eq(slide).removeAttr('href');
				}

				$('.bPromoCatalogNav').append('<a id="promoCatalogSlide' + slide + '" href="#' + slide + '" class="bPromoCatalogNav_eLink">' + ((slide * 1) + 1) + '</a>');
			}
		};

		initSlider(); //запуск слайдера

		var slider_SlideW = $('.bPromoCatalogSliderWrap_eSlide').width(),	// ширина одного слайда
			slider_WrapW = $('.bPromoCatalogSliderWrap').width( slider_SlideW * slider_SlideCount + (920/2 - slider_SlideW/2)),	// установка ширины обертки
			nowSlide = 0;	//текущий слайд
		// end of vars

		//листание стрелками
		$('.bPromoCatalogSlider_eArrow').bind('click', function() {
			var pos = ( $(this).hasClass('mArLeft') ) ? '-1' : '1';

			moveSlide(nowSlide + pos * 1);

			return false;
		});

		//пагинатор
		$('.bPaginator_eLink').bind('click', function() {
			if ( $(this).hasClass('active') ) {
				return false;
			}

			var link = $(this).attr('href').slice(1) * 1;

			moveSlide(link);

			return false;
		});

		//перемещение слайдов на указанный слайд
		var moveSlide = function moveSlide( slide ) {
			if ( slide === 0 ) {
				$('.bPromoCatalogSlider_eArrow.mArLeft').hide();
			}
			else{
				$('.bPromoCatalogSlider_eArrow.mArLeft').show();
			}

			if ( slide === slider_SlideCount - 1 ) {
				$('.bPromoCatalogSlider_eArrow.mArRight').hide();
			}
			else {
				$('.bPromoCatalogSlider_eArrow.mArRight').show();
			}

			$('.bPromoCatalogSliderWrap').animate({'left': -(slider_SlideW * slide)},500, function() {
				nowSlide = slide;
			});

			window.location.hash = 'slide' + (slide + 1);
			catalogPaginator.setActive(slide);
		};

		var hash = window.location.hash;

		if ( hash.indexOf('slide') + 1 ) {
			var toSlide = parseInt(hash.slice(6), 10) - 1;

			moveSlide(toSlide);
		}
	}
})(jQuery);