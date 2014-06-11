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

				leftBtn = $self.find(options.leftBtnControl),
				rightBtn = $self.find(options.rightBtnControl),
				slides = $self.find(options.slidesControl),
				item = $self.find(options.itemControl),
				cont = $self.find(options.slidesContControl),
				pager = $self.find(options.pagerControl),

				leftClass = 'slidesImg_item-left',
				centerClass = 'slidesImg_item-center',
				rightClass = 'slidesImg_item-right',
				changeClass = 'slidesImg_item-change'

				slidesData = $self.data('value'),
				slidesDataLength = $self.data('value').length,

				curSlides = 0,
				index = 0;
			// end of vars

			var
				setData = function setData() {
					item.each(function () {
						itemUrl = $self.data('value')[index].url;
						contSrc = $self.data('value')[index].image;
						$(this).attr('href' , itemUrl);
						$(this).find(cont).attr('src' , contSrc);

						index++;
					})
				},

				setDataD = function setDataD() {
					itemUrl = $self.data('value')[curSlides].url;
					contSrc = $self.data('value')[curSlides].image;
					$(this).attr('href' , itemUrl);
					$(this).find(cont).attr('src' , contSrc);

					console.log(curSlides);
				},

				slidesResize = function slidesResize() {
					slidesH = cont.height();
					$self.css({ 'height' : slidesH });
					slides.css({ 'height' : slidesH });
				},

			  	nextSlides = function nextSlides() {
					curSlides++;
					setDataD();

					slidesImgCenter = $self.find('.slidesImg_item-center');
					slidesImgCenter.removeClass(centerClass).addClass(leftClass);
					slidesImgCenter.next().removeClass(rightClass).addClass(centerClass);

					slides.append('<a href="'+ itemUrl +'" class="js-slides-img-item slidesImg_item slidesImg_item-right"><img src="'+ contSrc +'" class="js-slides-img-cont slidesImg_cont"></a>');
					$('.slidesImg_item-left').prev().remove();

					pagerCustom();

					console.log(curSlides);
				},

				prevSlides = function prevSlides() {
					curSlides--;
					setDataD();

					slidesImgCenter = $self.find('.slidesImg_item-center');
					slidesImgCenter.removeClass(centerClass).addClass(rightClass);
					slidesImgCenter.prev().removeClass(leftClass).addClass(centerClass);

					slides.prepend('<a href="'+ itemUrl +'" class="js-slides-img-item slidesImg_item slidesImg_item-left"><img src="'+ contSrc +'" class="js-slides-img-cont slidesImg_cont"></a>');
					$('.slidesImg_item-right').next().remove();
					
					pagerCustom();

					console.log(curSlides);
				},

			  	slidesImgNext = function slidesImgNext() {
			  		if( curSlides <= slidesDataLength ) {
			  			nextSlides();
			  		}
			  	},

			  	slidesImgPrev = function slidesImgPrev() {
			  		if( curSlides >= 0 ) {
			  			prevSlides();
			  		}
			  	},

			  	addPager = function addPager() {
			  		var pagerHtml = '';

			  		sliderPager = $('<div class="js-slides-img-pag slidesImg_pager" />');

			  		if ( slidesDataLength > 0 ) {
			  			$self.append(sliderPager);
			  		};

					for ( var i = 0; i <= slidesDataLength; i++ ) {
						pagerHtml += '<div class="js-slides-img-pag-item slidesImg_pager_item" data-slide-index="' + i + '" />';
					}; 

					sliderPager.html(pagerHtml);
			  	},

			  	pagerCustom = function pagerCustom() {
			  		pagerItem = $self.find('.js-slides-img-pag-item'),
					pagerItemData = pagerItem.data('slide-index');

					pagerItem.first().addClass('slidesImg_pager_item-active');

			  		pagerItem.each(function () {
			  			$(this).removeClass('slidesImg_pager_item-active');

			  			if ( curSlides == $(this).data('slide-index')) {
			  				$(this).addClass('slidesImg_pager_item-active');
			  			}
			  		});
			  	};
			//end of functions

			console.log(slidesDataLength);

			setData();
			addPager();
			pagerCustom();
			
			$self.find(options.pagerControl).css({
				'margin-left' : - $self.find(options.pagerControl).width() / 2 
			});

			$self.touchwipe({
			    wipeLeft : function() {
			    	slidesImgNext();
			    },
			    wipeRight : function() {
			    	slidesImgPrev();
			    }
			});

			rightBtn.on('click', slidesImgNext);
			leftBtn.on('click', slidesImgPrev);
			w.on('resize', slidesResize);

			slidesResize();
		});

	};

	$.fn.slidesbox.defaults = {
		leftBtnControl: '.js-slides-img-left',
		rightBtnControl: '.js-slides-img-right',
		slidesControl: '.js-slides-img-list',
		pagerControl: '.js-slides-img-pag',
		itemControl: '.js-slides-img-item',
		slidesContControl: '.js-slides-img-cont'
	};

	$('.slidesImg').slidesbox();
})( jQuery );