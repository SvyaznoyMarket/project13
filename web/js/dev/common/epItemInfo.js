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

function itemInfo (duration, itemLen, container){
    if(duration == itemLen){
        container.find('.js-epSlideControlsNext').css('display', 'none');
    }else{
        container.find('.js-epSlideControlsNext').css('display', '');
    }
    if(duration == 4){
        container.find('.js-epSlideControlsPrev').css('display', 'none');
    }else{
        container.find('.js-epSlideControlsPrev').css('display', '');
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
        containerW = container.width();

    itemList.css('left', position);

    itemInfo(duration, itemLen, container);

    $('.js-epSlideControls').on('click', 'a', function(e){
        e.preventDefault();

        var $this = $(this),
            containerEp =  $this.closest('.js-epSlide'),
            itemList = containerEp.find('.js-epSlideList'),
            item = itemList.find('.js-epSlideItem'),
            itemW = item.outerWidth(true),
            itemLen = item.length,
            itemWAll = itemW * itemLen;

        if($this.hasClass('js-epSlideControlsPrev')){
            position += itemW;
            duration--;
        }else if($this.hasClass('js-epSlideControlsNext')){
            position -= itemW;
            duration++;
        }

        itemInfo(duration, itemLen, containerEp);

        itemList.css('left', position);
    });
}


$(function(){
    epInfoShow();
});