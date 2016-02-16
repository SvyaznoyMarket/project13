/**
 * Created by alexandr.anpilogov on 12.02.16.
 */


;(function($){
    var $body = $('body'),
        counter = 0,
        times = 300,
        slider = function(e){
            e.preventDefault();

            var $this = $(this),
                container = $this.closest('.js-header-slider'),
                itemContainer = container.find('.js-header-slider-items-block'),
                item = itemContainer.find('.js-header-slider-item'),
                itemW = item.outerWidth(),
                itemCounter = item.length -1;

            if($this.hasClass('js-header-slider-btn-next')){
                counter++;

                if(counter > itemCounter){
                    counter = 0;
                }
            }else{
                counter--;

                if(counter < 0){
                    counter = itemCounter;
                }
            }

            itemContainer.animate({
                'margin-left': - itemW*counter
            }, {
                duration: times
            });
        };

    if($('.js-header-slider-item').length == 1){
        $('.js-header-slider-btn').css('display', 'none');
    }

    $body.on('click', '.js-header-slider-btn', slider);
})(jQuery);