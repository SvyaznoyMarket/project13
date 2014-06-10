(function( $ ){
	$.fn.slidesbox = function( params ) {
		var 
		    w = $(window);
	    // end of vars

		return this.each(function() {
			var
				options = $.extend(
							{},
							$.fn.slidesbox.defaults,
							params ),
				$self = $(this),

				item = $self.find(options.itemControl),
				cont = $self.find(options.slidesContControl),
				pager = $self.find(options.pagerControl),

				changeClass = 'slidesImg_item-change'

				slidesData = $self.data('value'),
				slidesDataLength = $self.data('value').length,

				curSlides = 0;
			// end of vars

			var
				/**
				 * Изменение размеров блока слайдера, при ресайзе окна.
				 */
				slidesResize = function slidesResize() {
					slidesH = cont.height();
					$self.css({ 'height' : slidesH });
				},

				/**
				 * Получение данные баннера, переключение анимационного класса.
				 */
				setData = function setData() {
					itemUrl = $self.data('value')[curSlides].url;
					contSrc = $self.data('value')[curSlides].image;
					item.attr('href' , itemUrl);
					cont.attr('src' , contSrc);

					item.addClass(changeClass).delay(500).queue(function() {
                       $(this).removeClass(changeClass);
                       $(this).dequeue();
                   });
				},

				/**
				 * Переключение на следующий слайд.
				 */
			  	nextSlides = function nextSlides() {
			  		curSlides++;
			  		setData();
			  		pagerCustom();
			  	},

			  	/**
				 * Переключение на предыдущий слайд.
				 */
			  	prevSlides = function prevSlides() {
			  		curSlides--;
			  		setData();
			  		pagerCustom();
			  	},

			  	/**
				 * Добавляем пагинатор.
				 */
			  	addPager = function addPager() {
			  		var pagerHtml = '';

			  		sliderPager = $('<div class="js-slides-img-pag slidesImg_pager" />');

			  		if ( slidesDataLength > 0 ) {
			  			$self.append(sliderPager);
			  		};

					for ( var i = 0; i <= slidesDataLength - 1; i++ ) {
						pagerHtml += '<div class="js-slides-img-pag-item slidesImg_pager_item" data-slide-index="' + i + '" />';
					}; 

					sliderPager.html(pagerHtml);
			  	},

			  	/**
				 * Управление активным классом пагинатора.
				 */
			  	pagerCustom = function pagerCustom() {
			  		pagerItem = $self.find('.js-slides-img-pag-item'),
					pagerItemData = pagerItem.data('slide-index');

					pagerItem.first().addClass('slidesImg_pager_item-active');

			  		pagerItem.each(function () {
			  			$(this).removeClass('slidesImg_pager_item-active');

			  			if ( curSlides == $(this).data('slide-index') ) {
			  				$(this).addClass('slidesImg_pager_item-active');
			  			}
			  		});
			  	};
			//end of functions

			setData();
			addPager();
			pagerCustom();
			
			$self.find(options.pagerControl).css({
				'margin-left' : - $self.find(options.pagerControl).width() / 2 
			});

			$self.touchwipe({
			    wipeLeft : function() {
			    	if( curSlides <= slidesDataLength - 2) {
			  			nextSlides();
			  		}
			    },
			    wipeRight : function() {
			    	if( curSlides >= 1 ) {
			  			prevSlides();
			  		}
			    }
			});

			w.on('load resize', slidesResize);
			slidesResize();
		});
	};

	$.fn.slidesbox.defaults = {
		slidesControl: '.js-slides-img',
		pagerControl: '.js-slides-img-pag',
		itemControl: '.js-slides-img-item',
		slidesContControl: '.js-slides-img-cont'
	};

	$('.js-slides-img').slidesbox();
})( jQuery );