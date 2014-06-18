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
                changeClass = 'slidesImg_item-change',

                slidesDataLength = $self.data('value').length,

                curSlides = 0,
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

	                slidesResize();
	                console.log(slides);

	                console.log(slidesDataLength);
	            },

                slidesResize = function slidesResize() {
                    slidesH = cont.height();

                    $self.css({'height': slidesH});
                    slides.css({'height': slidesH});
                },

                nextSlides = function nextSlides() {
                	curSlides++;

                	if (curSlides <= slidesDataLength - 1) {
	                	index++;

	                    if (index <= slidesDataLength - 1) {
	                        itemUrl = $self.data('value')[index].url;
	                    	contSrc = $self.data('value')[index].image;
	                    };

	                    slidesImgCenter = $self.find('.slidesImg_item-center');
	                    slidesImgCenter.removeClass(centerClass).addClass(leftClass);
	                    slidesImgCenter.next().removeClass(rightClass).addClass(centerClass);

	                    slides.append('<a href="' + itemUrl + '" class="js-slides-img-item slidesImg_item slidesImg_item-right"><img src="' + contSrc + '" class="js-slides-img-cont slidesImg_cont"></a>');
		               	$('.slidesImg_item-left').prev().remove();

	                    pagerCustom();

	                    console.log('nextSlides index=' + index);
	                }
                },

                prevSlides = function prevSlides() {
                	curSlides--;

                	if (curSlides >= 0) {
	                    index-- ;

	                    itemUrl = $self.data('value')[index].url;
	                    contSrc = $self.data('value')[index].image;

	                    slidesImgCenter = $self.find('.slidesImg_item-center');
	                    slidesImgCenter.removeClass(centerClass).addClass(rightClass);
	                    slidesImgCenter.prev().removeClass(leftClass).addClass(centerClass);

	                    slides.prepend('<a href="' + itemUrl + '" class="js-slides-img-item slidesImg_item slidesImg_item-left"><img src="' + contSrc + '" class="js-slides-img-cont slidesImg_cont"></a>');
	                    $('.slidesImg_item-right').next().remove();

	                    pagerCustom();
	                    
	                    console.log('prevSlides index=' + index);
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

            $self.find(options.pagerControl).css({
                'margin-left': -$self.find(options.pagerControl).width() / 2
            });

            $self.touchwipe({
                wipeLeft: function() {
                    nextSlides();
                },
                wipeRight: function() {
                    prevSlides();Selector
                }
            });

            rightBtn.on('click', nextSlides);
            leftBtn.on('click', prevSlides);
            w.on('resize', slidesResize);
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

    $('.slidesImg').slidesbox();
})(jQuery);