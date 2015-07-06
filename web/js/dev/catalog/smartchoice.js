;$(document).ready(function(){

	var smartChoiceSlider = $('.jsDataSmartChoice'),
		smartChoiceItem = $('.specialPriceItem'),
		smartChoiceItemAttr = smartChoiceSlider.attr('data-smartchoice');

	if ( typeof smartChoiceSlider.data('smartchoice') !== 'undefined' ) {
		$.getJSON('/ajax/product-smartchoice',{
				"products[]": smartChoiceSlider.data('smartchoice') },
			function(data){
				if (data.success) {
					$.each(data.result, function(i, value){
						var $slider = $.parseHTML(value.content);
						$($slider).hide();
						$('.specialBorderBox').append($slider);
						$('.smartChoiceSliderToggle-'+i).show();
					});

					$('.js-slider').goodsSlider();

					console.info('smartchoice ajax: ', data.result);
				}
			}
		);
	}

	$('.jsSmartChoiceSliderToggle a').click(function(e){
		e.preventDefault();
		var $target = $(e.target),
			id = $target.closest('div').data('smartchoice'),
			$link = $target.closest('a'),
			$specialPriceItemFoot_links = $('.specialPriceItemFoot_link');
		if (!$link.hasClass('mActive')) {
			$specialPriceItemFoot_links.removeClass('mActive');
			$link.addClass('mActive');
			$('.js-slider').hide();
			$('.specialBorderBox').addClass('specialBorderBox_render');
			$('.smartChoiceId-' + id).parent().show();
		} else {
			$specialPriceItemFoot_links.removeClass('mActive');
			$('.smartChoiceId-' + id).parent().hide();
			$('.specialBorderBox').removeClass('specialBorderBox_render');
		}
	});

	if ( typeof smartChoiceItemAttr !== 'undefined' && smartChoiceItemAttr !== false ) {
		smartChoiceItem.addClass('specialPriceItem_minHeight');
	}
	else { smartChoiceItem.removeClass('specialPriceItem_minHeight') };

	function track(event, article) {
		var ga = window[window.GoogleAnalyticsObject],
			_gaq = window['_gaq'],
			loc = window.location.href;

		if (ga) ga('send', 'event', event, loc, article);
		if (_gaq) _gaq.push(['_trackEvent', event, loc, article]);
	}

	// Tracking click on <a>
	smartChoiceItem.on('click', '.specialPriceItemCont_imgLink, .specialPriceItemCont_name', function(){
		var article = $(this).data('article');
		track('SmartChoice_click', article);
	});

	// Tracking click on <a> in similar carousel
	smartChoiceSlider.on('click', '.productImg, .productName a', function(e){
		var article = $(e.target).closest('.bSlider__eItem').data('product').article;
		track('SmartChoice_similar_click', article);
	});

	var $specialPrice = $('.js-specialPrice');
	if ($specialPrice.length) {
		ko.applyBindings(ENTER.UserModel, $specialPrice[0]);
	}
});