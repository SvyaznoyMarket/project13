;(function($){
	/**
	 * Плагин кастомных элементов select для карточки товара
	 *
	 * @author		Zaytsev Alexandr
	 * @requires	jQuery
	 * @param		{Object}	params
	 */
	$.fn.customDropDown = function( params ) {
		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.customDropDown.defaults,
							params),
				$self = $(this),

				select = $self.find(options.selectSelector),
				value = $self.find(options.valueSelector);
			// end of vars

			var selectChangeHandler = function selectChangeHandler() {
				var selectedOption = select.find('option:selected');

				value.html( selectedOption.val() );
				options.changeHandler( selectedOption );
			};

			select.on('change', selectChangeHandler);
		});
	};
			
	$.fn.customDropDown.defaults = {
		valueSelector: '.bDescSelectItem__eValue',
		selectSelector: '.bDescSelectItem__eSelect',
		changeHandler: function(){}
	};

})(jQuery);