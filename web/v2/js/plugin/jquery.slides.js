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
				item = $self.find(options.itemControl),
				cont = $self.find(options.slidesContControl),
				pager = $self.find(options.pagerControl),

				slidesCount = item.length,

				curSlides = 1;
			// end of vars

			var
				slidesResize = function slidesResize() {
					var slidesH = cont.height();

					$self.css({ 'height' : slidesH });
				},

			  	nextSlides = function nextSlides() {
			  		curSlides++;

					slidesCenter = $self.find('.slidesImg_item-center');

			  		slidesCenter.removeClass('slidesImg_item-center').addClass('slidesImg_item-left');
			  		slidesCenter.next().removeClass('slidesImg_item-right').addClass('slidesImg_item-center');

			  		pagerCustom();
			  	},

			  	prevSlides = function prevSlides() {
			  		curSlides--;
			  		
					slidesCenter = $self.find('.slidesImg_item-center');

			  		slidesCenter.removeClass('slidesImg_item-center').addClass('slidesImg_item-right');
			  		slidesCenter.prev().removeClass('slidesImg_item-left').addClass('slidesImg_item-center');

			  		pagerCustom();
			  	},

			  	pagerCustom = function pagerCustom() {
			  		var pager = $('.js-slides-img-pag'),
					    pagerItem = pager.find('.js-slides-img-pag-item'),
					    pagerItemData = pagerItem.data('slide-index');

					pagerItem.first().addClass('slidesImg_pager_item-active');

			  		pagerItem.each(function () {
			  			$(this).removeClass('slidesImg_pager_item-active');

			  			if ( curSlides == $(this).data('slide-index') ) {
			  				$(this).addClass('slidesImg_pager_item-active');
			  			}
			  		});
			  	},

			  	slidesImgNext = function slidesImgNext() {
			  		if( curSlides <= slidesCount - 1 ) {
			  			nextSlides();
			  		}
			  	},

			  	slidesImgPrev = function slidesImgPrev() {
			  		if( curSlides >= 2 ) {
			  			prevSlides();
			  		}
			  	},

			  	addPager = function addPager() {
			  		var pagerHtml = '';

			  		sliderPager = $('<div class="js-slides-img-pag slidesImg_pager" />');

			  		if ( slidesCount > 1 ) {
			  			$self.append(sliderPager);
			  		};

					for ( var i = 1; i <= slidesCount; i++ ) {
						pagerHtml += '<div class="js-slides-img-pag-item slidesImg_pager_item" data-slide-index="' + i + '" />';
					}; 

					sliderPager.html(pagerHtml);
			  	};
			//end of functions

			addPager();
			pagerCustom();

			$('.js-slides-img-pag').css({'margin-left' : - $('.js-slides-img-pag').width() / 2 })

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
		pagerControl: '.js-slides-img-pag',
		itemControl: '.js-slides-img-item',
		slidesContControl: '.js-slides-img-cont'
	};

	$('.slidesImg').slidesbox();
})( jQuery );