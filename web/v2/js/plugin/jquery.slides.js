(function($) {
    $.fn.slidesbox = function(params) {
        var
        w = $(window);
        // end of vars

        return this.each(function() {
            var
            options = $.extend({},
	                  $.fn.slidesbox.defaults,
	                  params),

                $self = $(this),

                slides = $self.find(options.slidesSelector),
                item = $self.find(options.itemSelector),
                cont = $self.find(options.slidesContSelector),

                leftBtn = $self.find(options.leftBtnSelector),
                rightBtn = $self.find(options.rightBtnSelector),
                pager = $self.find(options.pagerSelector),

                leftClass = 'slidesImg_item-left',
                centerClass = 'slidesImg_item-center',
                rightClass = 'slidesImg_item-right',

                slidesDataLength = $self.data('value').length,

                curSlides = 0,
                direction = 0,
                index = 1;
            // end of vars

            var
	            setData = function setData() {
	            	var i = 0;

	                item.each(function() {
	                    itemUrl = $self.data('value')[i].url;
	                    contSrc = $self.data('value')[i].image;
	                    $(this).attr('href', itemUrl);
	                    $(this).find(cont).attr('src', contSrc);

	                    i++;
	                });
	            },

                slidesResize = function slidesResize() {

                    slidesH = $self.find(options.slidesContSelector).height();

                    $self.css({'height': slidesH + 15});
                    slides.css({'height': slidesH});
                },

                nextSlides = function nextSlides() {
                	if ( curSlides <= slidesDataLength - 2 ) {
                		curSlides++;

                		if ( direction == 1 ) {
                			index = index + 3;
                		} else { index++; }

                		direction = 0;

	                    if ( index <= slidesDataLength - 1 ) {
	                        itemUrl = $self.data('value')[index].url;
	                    	contSrc = $self.data('value')[index].image;
	                    };

	                    slidesImgCenter = $self.find('.slidesImg_item-center');
	                    slidesImgCenter.removeClass(centerClass).addClass(leftClass);
	                    slidesImgCenter.next().removeClass(rightClass).addClass(centerClass);

	                    slides.append('<a href="' + itemUrl + '" class="js-slides-img-item slidesImg_item slidesImg_item-right"><img src="' + contSrc + '" class="js-slides-img-cont slidesImg_cont"></a>');
		               	$('.slidesImg_item-left').prev().remove();

	                    pagerCustom();

	                    // console.log('index next= ' + index);
	                    // console.log('direction next= ' + direction);
	                }
                },

                prevSlides = function prevSlides() {
                	if ( curSlides >= 1 ) {
                		curSlides--;

                		if ( direction == 0 ) {
                			index = index - 3;
                		} else { index--; }

                		direction = 1;
	                    
                		if ( index >= 0 ) {		
		                    itemUrl = $self.data('value')[index].url;
		                    contSrc = $self.data('value')[index].image;
		                }

	                    slidesImgCenter = $self.find('.slidesImg_item-center');
	                    slidesImgCenter.removeClass(centerClass).addClass(rightClass);
	                    slidesImgCenter.prev().removeClass(leftClass).addClass(centerClass);

	                    slides.prepend('<a href="' + itemUrl + '" class="js-slides-img-item slidesImg_item slidesImg_item-left"><img src="' + contSrc + '" class="js-slides-img-cont slidesImg_cont"></a>');
	                    $('.slidesImg_item-right').next().remove();

	                    pagerCustom();

	                    // console.log('index prev= ' + index);
	                    // console.log('direction prev= ' + direction);https://www.google.ru/search?q=IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3QzRGRTI5MEE1MzQxMUUzODExREU3OTc1MTBDRTA1QyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3QzRGRTI5MUE1MzQxMUUzODExREU3OTc1MTBDRTA1QyI&ie=utf-8&oe=utf-8&aq=t&rls=org.mozilla:ru:official&client=firefox-a&channel=fflb&gfe_rd=cr&ei=6pOiU_nuJI2BNKGjglg
	                    // console.log('curSlides prev= ' + curSlides);
	                }
                },

                addPager = function addPager() {
                    var pagerHtml = '';

                    sliderPager = $('<div class="js-slides-img-pag slidesImg_pager" />');

                    if (slidesDataLength > 0) {
                        $self.append(sliderPager);
                    };

                    for (var i = 0; i <= slidesDataLength - 1; i++) {
                        pagerHtml += '<div class="js-slides-img-pag-item slidesImg_pager_item" data-slide-index="' + i + '" />';
                    };

                    sliderPager.html(pagerHtml);
                    $self.find(options.pagerSelector).css({'margin-left': -$self.find(options.pagerSelector).width() / 2});
                    pagerCustom();
                },

                pagerCustom = function pagerCustom() {
                    var pagerItem = $self.find('.js-slides-img-pag-item'),
                        pagerItemData = pagerItem.data('slide-index');

                    pagerItem.each(function() {
                        $(this).removeClass('slidesImg_pager_item-active');

                        if (curSlides == $(this).data('slide-index')) {
                            $(this).addClass('slidesImg_pager_item-active');
                        }
                    });
                };
            //end of functions

            setData();
            addPager();
            cont.on('load resize', slidesResize);
            rightBtn.on('click', nextSlides);
            leftBtn.on('click', prevSlides);
            w.on('resize', slidesResize);

            $self.touchwipe({
            	min_move_x: 20,
      			min_move_y: 20,
                wipeLeft: function() {
                    nextSlides();
                },
                wipeRight: function() {
                    prevSlides();
                }
            });
        });
    };

    $.fn.slidesbox.defaults = {
    	slidesSelector: '.js-slides-img-list',
    	itemSelector: '.js-slides-img-item',
    	slidesContSelector: '.js-slides-img-cont',

        leftBtnSelector: '.js-slides-img-left',
        rightBtnSelector: '.js-slides-img-right',
        pagerSelector: '.js-slides-img-pag'
    };

    $('.js-slides-img').slidesbox();
})(jQuery);