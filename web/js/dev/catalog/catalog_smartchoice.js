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
        $target = $(e.target);
        id = $target.closest('div').data('smartchoice');
        console.log($target, id);
        $('.specialPriceItemFoot_link').removeClass('mActive');
        $target.closest('a').addClass('mActive');
        $('.bGoodsSlider').hide();
        $('.smartChoiceId-'+id).parent().show();
    })
});