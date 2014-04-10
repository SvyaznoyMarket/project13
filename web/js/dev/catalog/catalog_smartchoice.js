;$(document).ready(function(){
    $.getJSON('/ajax/product-smartchoice',{
            "products[]": $('.jsDataSmartChoice').data('smartchoice') },
        function(data){
            if (data.success) {
                $.each(data.result, function(i, value){
                        $slider = $.parseHTML(value.content);
                        $($slider).hide();
                        $('.specialBorderBox').append($slider);
                        $('.smartChoiceSliderToggle-'+i).show();
                    });
                $('.bGoodsSlider').goodsSlider();
                console.info('smartchoice ajax: ', data.result);
            }
        }
    );

    $('.jsSmartChoiceSliderToggle a').click(function(e){
        e.preventDefault();
        var $target = $(e.target),
            id = $target.closest('div').data('smartchoice'),
            $link = $target.closest('a'),
            $specialPriceItemFoot_links = $('.specialPriceItemFoot_link');
        if (!$link.hasClass('mActive')) {
            $specialPriceItemFoot_links.removeClass('mActive');
            $link.addClass('mActive');
            $('.bGoodsSlider').hide();
            $('.smartChoiceId-' + id).parent().show();
        } else {
            $specialPriceItemFoot_links.removeClass('mActive');
            $('.smartChoiceId-' + id).parent().hide();
        }
    })
});