/**
 * Слайдер товаров
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */

;(function($){
	$.fn.goodsSlider = function(params) {
		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.goodsSlider.defaults,
							params),
				$self = $(this);

				hasCategory = $self.hasClass('mWithCategory'),
				leftBtn = $self.find(options.leftArrowSelector),
				rightBtn = $self.find(options.rightArrowSelector),
				wrap = $self.find(options.sliderWrapperSelector),
				slider = $self.find(options.sliderSelector),
				item = $self.find(options.itemSelector),
				catItem = $self.find(options.categoryItemselector),
				
				itemW = item.width() + parseInt(item.css('marginLeft'),10) + parseInt(item.css('marginRight'),10),
				elementOnSlide = wrap.width()/itemW,

				nowLeft = 0,

				nextSlide = function(){
					if ($(this).hasClass('mDisabled')){
						return false;
					}

					leftBtn.removeClass('mDisabled');

					if (nowLeft + elementOnSlide * itemW >= slider.width()-elementOnSlide * itemW){
						nowLeft = slider.width()-elementOnSlide * itemW
						rightBtn.addClass('mDisabled');
					}
					else{
						nowLeft = nowLeft + elementOnSlide * itemW;
						rightBtn.removeClass('mDisabled');
					}

					slider.animate({'left': -nowLeft });

					return false;
				},

				prevSlide = function(){
					if ($(this).hasClass('mDisabled')){
						return false;
					}

					rightBtn.removeClass('mDisabled');

					if (nowLeft - elementOnSlide * itemW <= 0){
						nowLeft = 0;
						leftBtn.addClass('mDisabled');
					}
					else{
						nowLeft = nowLeft - elementOnSlide * itemW;
						leftBtn.removeClass('mDisabled');
					}

					slider.animate({'left': -nowLeft });

					return false;
				},

				reWidthSlider = function(nowItems){
					leftBtn.addClass('mDisabled');
					rightBtn.addClass('mDisabled');

					if (nowItems.length > elementOnSlide) {
						rightBtn.removeClass('mDisabled');
					}

					slider.width(nowItems.length * itemW);
					nowLeft = 0;
					leftBtn.addClass('mDisabled');
					slider.css({'left':nowLeft});
					nowItems.show();
				},

				showCategoryGoods = function(){
					var nowCategoryId = catItem.filter('.mActive').attr('id'),
						showAll = (catItem.filter('.mActive').data('product') === 'all'),
						nowShowItem = (showAll) ? item : item.filter('[data-category="'+nowCategoryId+'"]');
					//end of vars
					
					item.hide();
					reWidthSlider(nowShowItem);
				},

				selectCategory = function(){
					catItem.removeClass('mActive');
					$(this).addClass('mActive');
					showCategoryGoods();
				};
		//end of vars

			if (hasCategory) {
				showCategoryGoods();
			}
			else {
				reWidthSlider(item);
			}

			rightBtn.bind('click', nextSlide);
			leftBtn.bind('click', prevSlide);
			catItem.bind('click', selectCategory)
		});
	};

	$.fn.goodsSlider.defaults = {
		leftArrowSelector: '.bSliderAction__eBtn.mPrev',
		rightArrowSelector: '.bSliderAction__eBtn.mNext',
		sliderWrapperSelector: '.bSliderAction__eInner',
		sliderSelector: '.bSliderAction__eList',
		itemSelector: '.bSliderAction__eItem',
		categoryItemselector: '.bGoodsSlider__eCatItem'
	};

})(jQuery);

$(document).ready(function() {
	if ($('.bGoodsSlider').length) {
		$('.bGoodsSlider').goodsSlider();
	}
});