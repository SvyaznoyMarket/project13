function epInfoShow(){
    $(document).on('click', function(e){
        if($('.js-ep-item-info').hasClass('active')){
            if($(e.target).closest('.js-ep-item-info').length || $(e.target).closest('.js-ep-item').length){
                return;
            }
            $('.js-ep-item-info').removeClass('active');
            $('.js-ep-item').removeClass('active');
            $('.js-ep-item-margin').css('margin-top', '');
        }
    });

    $('.js-ep-item').on('click', function(e){
        e.preventDefault();

        var $this = $(this),
            container = $this.closest('.js-ep-container'),
            pointReport = $this.closest('.js-ep-pointReport'),
            itemInfo = container.find('.js-ep-item-info'),
            itemMargin = container.find('.js-ep-item-margin'),
            itemTop = container.find('.js-ep-item-top'),
            margin = 0;

        $this.addClass('active')
            .siblings()
            .removeClass('active');

        itemInfo.eq($this.filter('.active').index())
            .toggleClass('active')
            .siblings()
            .removeClass('active');

        if(itemInfo.hasClass('active')){
            if(itemTop.outerHeight() < pointReport.outerHeight()) {
                margin = itemInfofilter('.active').outerHeight() + pointReport.outerHeight() - itemTop.find('.js-ep-item-top-header').outerHeight(true);
            }else{
                margin = itemInfo.filter('.active').outerHeight(true) + parseInt(itemInfo.css('margin-bottom'));
            }
            itemMargin.css('margin-top', margin);
        }else{
            itemMargin.css('margin-top', '');
        }

        epInfoSlide();
    });
}

function itemInfo (duration, itemLen, next, prev, itemVis){
    if(duration == itemLen){
        next.css('display', 'none');
    }else{
        next.css('display', '');
    }
    if(duration == itemVis){
        prev.css('display', 'none');
    }else{
        prev.css('display', '');
    }
}

function epInfoSlide(){

    var duration = 4,
        position = 0,
        item = $('.js-epSlideItem'),
        itemW = item.outerWidth(true),
        itemLen = item.length,
        itemWAll = itemW * itemLen,
        container = $('.js-epSlide'),
        itemList = container.find('.js-epSlideList'),
        containerW = container.width(),
        nextControl = container.find('.js-epSlideControlsNext'),
        prevControl = container.find('.js-epSlideControlsPrev');

    itemList.css('left', position);

    itemInfo(duration, itemLen, nextControl, prevControl, 4);

    $('.js-epSlideControls').on('click', 'a', function(e){
        e.preventDefault();

        var $this = $(this),
            containerEp =  $this.closest('.js-epSlide'),
            itemList = containerEp.find('.js-epSlideList'),
            item = itemList.find('.js-epSlideItem'),
            itemW = item.outerWidth(true),
            itemLen = item.length,
            itemWAll = itemW * itemLen,
            nextControl = containerEp.find('.js-epSlideControlsNext'),
            prevControl = containerEp.find('.js-epSlideControlsPrev');

        if($this.hasClass('js-epSlideControlsPrev')){
            position += itemW;
            duration--;
        }else if($this.hasClass('js-epSlideControlsNext')){
            position -= itemW;
            duration++;
        }

        itemInfo(duration, itemLen, nextControl, prevControl, 4);

        itemList.css('left', position);
    });
}

function viewedSlider(){
    var duration = 6,
        position = 0,
        item = $('.js-viewed-slider-item'),
        itemW = item.outerWidth(true),
        itemLen = item.length,
        itemWAll = itemW * itemLen,
        container = $('.js-viewed-slider'),
        itemList = container.find('.js-epSlideList'),
        containerW = container.width(),
        nextControl = container.find('.js-viewedSlideControlsNext'),
        prevControl = container.find('.js-viewedSlideControlsPrev'),
        containerFull = $('.js-viewed-slider-full'),
        allPage = containerFull.find('.js-viewed-slider-allPage'),
        page = containerFull.find('.js-viewed-slider-page');

    itemList.css('left', position);

    itemInfo(duration, itemLen, nextControl, prevControl, 6);

    allPage.text(Math.ceil(itemLen / duration));

    $('.js-viewedSlideControls').on('click', 'a', function(e){
        e.preventDefault();

        var $this = $(this),
            container =  $this.closest('.js-viewed-slider'),
            itemList = container.find('.js-viewed-slider-list'),
            item = itemList.find('.js-viewed-slider-item'),
            itemW = item.outerWidth(true),
            itemLen = item.length,
            itemWAll = itemW * itemLen,
            nextControl = container.find('.js-viewedSlideControlsNext'),
            prevControl = container.find('.js-viewedSlideControlsPrev');

        if($this.hasClass('js-viewedSlideControlsPrev')){
            position += itemW;
            duration--;
        }else if($this.hasClass('js-viewedSlideControlsNext')){
            position -= itemW;
            duration++;
        }

        console.log(itemLen);

        itemInfo(duration, itemLen, nextControl, prevControl, 6);

        itemList.css('left', position);

        page.text(Math.ceil(duration/6));
    });
}


$(function(){
    epInfoShow();

    viewedSlider();
});