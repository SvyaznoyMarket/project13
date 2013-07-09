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
							$.fn.customRadio.defaults,
							params);
			var $self = $(this);

			var hasCategory = $self.hasClass('mWithCategory');
			var leftBtn = $self.find(leftArrowSelector);
			var rightBtn = $self.find(rightArrowSelector);
			var slider = $self.find(sliderSelector);
			var item = $self.find(itemSelector);

			var nextSlide = function(){

			};

			var prevSlide = function(){

			};

			var reWidthSlider = function(){
				
			};

			rightBtn.bind('click', nextSlide);
			leftBtn.bind('click', prevSlide);

		});
	};

	$.fn.goodsSlider.defaults = {
		leftArrowSelector: '.bSliderAction__eBtn.mPrev',
		rightArrowSelector: '.bSliderAction__eBtn.mNext',
		sliderSelector: '.bSliderAction__eList',
		itemSelector: '.bSliderAction__eItem'
	};

})(jQuery);

$(document).ready(function() {
	if ($('.bGoodsSlider').length) {
		$('.bGoodsSlider').goodsSlider();
	}
});