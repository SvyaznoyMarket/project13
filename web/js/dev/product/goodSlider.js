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
							params);
			var $self = $(this);

			var hasCategory = $self.hasClass('mWithCategory');
			var leftBtn = $self.find(options.leftArrowSelector);
			var rightBtn = $self.find(options.rightArrowSelector);
			var wrap = $self.find(options.sliderWrapperSelector);
			var slider = $self.find(options.sliderSelector);
			var item = $self.find(options.itemSelector);
			var catItem = $self.find(options.categoryItemselector);
			
			var itemW = item.width() + parseInt(item.css('marginLeft'),10) + parseInt(item.css('marginRight'),10);
			var elementOnSlide = wrap.width()/itemW;

			var nowLeft = 0;

			var nextSlide = function(){
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
			};

			var prevSlide = function(){
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
			};

			var reWidthSlider = function(nowItems){
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
			};

			var showCategoryGoods = function(){
				item.hide();
				var nowCategoryId = catItem.filter('.mActive').attr('id');
				var nowShowItem = item.filter('[data-category="'+nowCategoryId+'"]');
				reWidthSlider(nowShowItem);
			};

			var selectCategory = function(){
				catItem.removeClass('mActive');
				$(this).addClass('mActive');
				showCategoryGoods();
			};

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