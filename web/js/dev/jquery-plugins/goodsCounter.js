/**
 * jQuery плагин каунтера
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 * @return		{jQuery object}
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

			var counterGroupName = $self.attr('data-spinner-for');
			var counterGroup = $('[data-spinner-for="'+counterGroupName+'"]');

			var timeout_id = '';

			var changeHandler = function(count){
				clearTimeout(timeout_id);
				timeout_id = setTimeout(function(){
					counterGroup.find('input').val(count);
					options.onChange(count);
				}, 400);
			};

			var plusHandler = function(e){
				e.stopPropagation();

				if ($self.hasClass('mDisabled')){
					return false;
				}

				var nowCount = input.val();
				if ((nowCount*1)+1 > options.maxVal){
					return false;
				}
				nowCount++;
				input.val(nowCount);
				changeHandler(nowCount);
				return false;
			};

			var minusHandler = function(e){
				e.stopPropagation();

				if ($self.hasClass('mDisabled')){
					return false;
				}

				var nowCount = input.val();
				if ((nowCount*1)-1 < 1){
					return false;
				}
				nowCount--;
				input.val(nowCount);
				changeHandler(nowCount);
				return false;
			};

			var keyupHandler = function(e){
				e.stopPropagation();

				if ($self.hasClass('mDisabled')){
					return false;
				}

				var nowCount = input.val();

				nowCount = input.val();
				if ((nowCount*1) < 1){
					nowCount = 1;
				}

				if ((nowCount*1) > options.maxVal){
					nowCount = options.maxVal;
				}

				input.val(nowCount);
				changeHandler(nowCount);

				return false;
			};

			var keydownHandler = function(e){
				e.stopPropagation();

				if (e.which === 38){ // up arrow
					plusBtn.trigger('click');
					return false;
				}
				else if (e.which === 40){ // down arrow
					minusBtn.trigger('click');
					return false;
				}
				else if ( !(( (e.which >= 48) && (e.which <= 57) ) ||  
							( (e.which >= 96) && (e.which <= 105) ) || 
							(e.which === 8) ||
							(e.which === 46) )){
					return false;
				}
			};

			var updatespinner = function(e, products){
				for (var i = products.product.length - 1; i >= 0; i--) {
					var spinner = $('[data-spinner-for="'+products.product[i].id+'"]');
					spinner.addClass('mDisabled');
					var input = spinner.find('input');
					input.val(products.product[i].quantity).attr('disabled','disabled');
				}
			};

			plusBtn.bind('click', plusHandler);
			minusBtn.bind('click',minusHandler);
			input.bind('keydown', keydownHandler);
			input.bind('keyup', keyupHandler);
			$('body').bind('updatespinner', updatespinner);
		});
	};

	$.fn.goodsCounter.defaults = {
		// callbacks
		plusSelector:'.bCountSection__eP',
		minusSelector:'.bCountSection__eM',
		inputSelector:'.bCountSection__eNum',

		maxVal: 99,

		onChange: function(){}
	};

})(jQuery);