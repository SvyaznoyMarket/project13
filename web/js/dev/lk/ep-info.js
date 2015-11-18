$(function(){
    var $body = $('body');

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
            dataSlider = $this.data('slider'),
            relations = $this.data('relation'),
            $sliderContainer = (relations && relations.container) ? $(relations.container) : null;
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
                    margin = itemInfo.filter('.active').outerHeight() + pointReport.outerHeight() - itemTop.find('.js-ep-item-top-header').outerHeight(true);
                }else{
                    margin = itemInfo.filter('.active').outerHeight(true) + parseInt(itemInfo.css('margin-bottom'));
                }
                itemMargin.css('margin-top', margin);
            }else{
                itemMargin.css('margin-top', '');
            }
        }

        if ($body.data('enterprizeSliderXhr')) { // если до этого была загрузка слайдера - прибиваем
            try {
                $body.data('enterprizeSliderXhr').abort();
            } catch (error) {
                console.error(error);
            }
        }

        var xhr = $.get(dataSlider.url);
        xhr.done(function(response) {
            if ($sliderContainer && response.content) {
                console.log($sliderContainer);
                $sliderContainer.removeClass('mLoader').html(response.content)
                    .find('.mLoader').removeClass('mLoader');

                $sliderContainer.find('.js-epInfoSlide').goodsSlider({
                    leftArrowSelector: '.js-ep-info__product-prev',
                    rightArrowSelector: '.js-ep-info__product-next',
                    sliderWrapperSelector: '.js-ep-info__product-slide',
                    sliderSelector: '.js-ep-info__product-list',
                    itemSelector: '.js-ep-info__product-item'
                });
            }
        });
        xhr.always(function() {
            $body.data('enterprizeSliderXhr', null);
        });

        $body.data('enterprizeSliderXhr', xhr);
    });

});