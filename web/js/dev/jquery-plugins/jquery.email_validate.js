/**
 * jQuery плагин валидации e-mail'ов
 *
 * @author	Zaytsev Alexandr
 * @return	{jQuery object}
 */
;(function($) {
	$.fn.emailValidate = function(params) {

		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.emailValidate.defaults,
							params);
			var $self = $(this);

			var validate = function(e){
				var email = $self.val(),
					re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

				if (re.test(email)){
					options.onValid();
				}
				else{
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
	}

})(jQuery);