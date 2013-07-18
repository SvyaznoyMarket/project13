;(function($) {
	/**
	 * jQuery плагин валидации e-mail'ов
	 *
	 * @requires jQuery
	 * @author	Zaytsev Alexandr
	 * @return	{jQuery}
	 */
	$.fn.emailValidate = function(params) {

		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.emailValidate.defaults,
							params),
				$self = $(this),

				/**
				 * Выполенение валидации regExp'ом по нажатию клавиши в поле ввода
				 * 
				 * @param	{Event}	e Событие
				 */
				validate = function(e){
					var email = $self.val(),
						re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					//end of vars
					
					if (re.test(email)){
						options.onValid();
					}
					else{
						options.onInvalid();
					}
				};
			//end of vars

			$self.bind('keyup', validate);
		});
	};

	$.fn.emailValidate.defaults = {
		// callbacks
		onValid: function() {},
		onInvalid: function() {}
	};

})(jQuery);