/**
 * jQuery плагин каунтера
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery
 * @return	{jQuery object}
 */
;(function($) {
	$.fn.goodsCounter = function(params) {

		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.goodsCounter.defaults,
							params);
			var $self = $(this);

			var plusBtn = $self.find(options.plusSelector);
			var minusBtn = $self.find(options.minusSelector);
			var input = $self.find(options.inputSelector);
			var nowCount = input.val();

			var trigger = true;

			var plusHandler = function(e){
				e.stopPropagation();
				if ((nowCount*1)+1 > options.maxVal){
					return false;
				}
				nowCount++;
				input.val(nowCount);
				options.onPlus(nowCount);
				options.onChange(nowCount);
			};

			var minusHandler = function(e){
				e.stopPropagation();
				if ((nowCount*1)-1 < 1){
					return false;
				}
				nowCount--;
				input.val(nowCount);
				options.onMinus(nowCount);
				options.onChange(nowCount);
			};

			var keyupHandler = function(e){
				e.stopPropagation();
				if (trigger){
					nowCount = input.val();
					if ((nowCount*1) < 1){
						nowCount = 1;
					}
					if ((nowCount*1) > options.maxVal){
						nowCount = options.maxVal;
					}
					input.val(nowCount);
					options.onChange(nowCount);
				}
				else{
					nowCount = input.val().replace(/\D/g,'') * 1;
					input.val(nowCount);
				}
			};

			var keydownHandler = function(e){
				e.stopPropagation();
				if (e.which === 38){ // up arrow
					plusBtn.trigger('click');
				}
				else if (e.which === 40){ // down arrow
					minusBtn.trigger('click');
				}
				else if (	( (e.which >= 48) && (e.which <= 57) ) ||  // num or backspace or delete
							( (e.which >= 96) && (e.which <= 105) ) || 
							(e.which === 8) ||
							(e.which === 46) ){
					trigger = true;
				}
				else{
					trigger = false;
				}
			};

			plusBtn.bind('click', plusHandler);
			minusBtn.bind('click',minusHandler);
			input.bind('keydown', keydownHandler);
			input.bind('keyup', keyupHandler);
		});
	};

	$.fn.goodsCounter.defaults = {
		// callbacks
		plusSelector:'.bCountSection__eP',
		minusSelector:'.bCountSection__eM',
		inputSelector:'.bCountSection__eNum',

		maxVal: 99,

		onPlus: function(){},
		onMinus: function(){},
		onChange: function(){}
	};

})(jQuery);