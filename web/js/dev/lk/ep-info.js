$(function(){
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
                .siblings().add(container.siblings('.js-ep-container').find('.js-ep-item-info'))
                .removeClass('active');

            if(pointReport){
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
            }

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

    epInfoShow();
});