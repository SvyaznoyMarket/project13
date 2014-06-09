; (function( $ ){
	var 
	    body = $('body'),
	    slides = $('.js-slides-img'),
	    slidesItem = slides.find('.js-slides-img-item'),
	    slidesImg = slidesItem.find('.js-slides-img-cont')

	    sliderPag = $('.js-slides-img-pag'),
	    slidesImgCount = slidesItem.length,

	    pager = $('.js-slides-img-pag'),

	    curSlide = 1,

	    btnNext = $('.js-slides-img-right'),
	    btnPrev = $('.js-slides-img-left');
    // end of vars

	var 
		slidesResize = function slidesResize() {
			slidesImgH = slidesImg.height();

			slides.css({ 'height' : slidesImgH });
		},

	  	nextSlides = function nextSlides() {
	  		curSlide++;

			slidesImgCenter = slides.find('.slidesImg_item-center');

	  		slidesImgCenter.removeClass('slidesImg_item-center').addClass('slidesImg_item-left');
	  		slidesImgCenter.next().removeClass('slidesImg_item-right').addClass('slidesImg_item-center');

	  		pagerCustom();
	  	},

	  	prevSlides = function prevSlides() {
	  		curSlide--;
	  		
			slidesImgCenter = slides.find('.slidesImg_item-center');

	  		slidesImgCenter.removeClass('slidesImg_item-center').addClass('slidesImg_item-right');
	  		slidesImgCenter.prev().removeClass('slidesImg_item-left').addClass('slidesImg_item-center');

	  		pagerCustom();
	  	},

	  	pagerCustom = function pagerCustom() {
	  		var pager = $('.js-slides-img-pag'),
			    pagerItem = pager.find('.js-slides-img-pag-item'),
			    pagerItemData = pagerItem.data('slide-index');

			pagerItem.first().addClass('slidesImg_pager_item-active');

	  		pagerItem.each(function () {
	  			$(this).removeClass('slidesImg_pager_item-active');

	  			if ( curSlide == $(this).data('slide-index') ) {
	  				$(this).addClass('slidesImg_pager_item-active');
	  			}
	  		});
	  	},

	  	slidesImgNext = function slidesImgNext() {
	  		if( curSlide <= slidesImgCount - 1 ) {
	  			nextSlides();
	  		}
	  	},

	  	slidesImgPrev = function slidesImgPrev() {
	  		if( curSlide >= 2 ) {
	  			prevSlides();
	  		}
	  	},

	  	addPagination = function addPagination( i ) {
	  		var pagerHtml = '';

	  		sliderPager = $('<div class="js-slides-img-pag slidesImg_pager" />');

	  		if ( slidesImgCount > 1 ) {
	  			slides.append(sliderPager);
	  		};

			for ( var i = 1; i <= slidesImgCount; i++ ) {
				pagerHtml += '<div class="js-slides-img-pag-item slidesImg_pager_item" data-slide-index="' + i + '" />';
			}; 

			sliderPager.html(pagerHtml);
	  	};
	//end of functions

	addPagination();
	pagerCustom();

	$('.js-slides-img-pag').css({'margin-left' : - $('.js-slides-img-pag').width() / 2 })

	slides.touchwipe({
	    wipeLeft : function() {
	    	slidesImgNext();
	    },
	    wipeRight : function() {
	    	slidesImgPrev();
	    }
	});

	btnNext.on('click', slidesImgNext);
	btnPrev.on('click', slidesImgPrev);
	$(window).on('resize', slidesResize);
  	slidesResize();

})( jQuery );