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
				$self = $(this);
			//end of vars


			/**
			 * Выполенение валидации regExp'ом по нажатию клавиши в поле ввода
			 * 
			 * @param	{Event}		e		Событие
			 * @param	{Object}	email	Введенная в поле ввода сторока
			 * @param	{String}	rEmail	Шаблон соответствия e-mail
			 */
			var validate = function validate() {
				var email = $self.val(),
					rEmail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				//end of vars
				
				if ( rEmail.test(email) ) {
					options.onValid();
				}
				else {
					options.onInvalid();
				}
			};

			$self.bind('keyup', validate);
		});
	};

	$.fn.emailValidate.defaults = {
		// callbacks
		onValid: function() {},
		onInvalid: function() {}
	};

})(jQuery);