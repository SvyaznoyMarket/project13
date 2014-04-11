;$(document).ready(function(){

    var $jsDataSmartChoice = $('.jsDataSmartChoice'); // div c каруселями smart-choice

    $.getJSON('/ajax/product-smartchoice',{
            "products[]": $jsDataSmartChoice.data('smartchoice') },
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
    });

    function track(event, article) {
        var ga = window[window.GoogleAnalyticsObject],
            _gaq = window['_gaq'],
            loc = window.location.href;

        if (ga) ga('send', 'event', event, loc, article);
        if (_gaq) _gaq.push(['_trackEvent', event, loc, article]);
    }

    // Tracking click on <a>
    $('.specialPriceItem').on('click', '.specialPriceItemCont_imgLink, .specialPriceItemCont_name', function(){
        var article = $(this).data('article');
        track('SmartChoice_click', article);
    });

    // Tracking click on <a> in similar carousel
    $jsDataSmartChoice.on('click', '.productImg, .productName a', function(e){
        var article = $(e.target).closest('.bSlider__eItem').data('product').article;
        track('SmartChoice_similar_click', article);
    });

});