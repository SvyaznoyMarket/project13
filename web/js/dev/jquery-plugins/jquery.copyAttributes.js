(function($) {
	$.fn.copyAttributes = function($from, removeNotExistsInFromElementAttributes) {
		this.each(function() {
			var
				$to = $(this),
				fromAttributes = {};

			if ($from.prop('attributes')) {
				$.each($from.prop('attributes'), function() {
					$to.attr(this.name, this.value);
					fromAttributes[this.name] = null;
				});

				if (removeNotExistsInFromElementAttributes) {
					$.each($to.prop('attributes'), function() {
						if (!fromAttributes.hasOwnProperty(this.name)) {
							$to.removeAttr(this.name);
						}
					});
				}
			}
		});

		return this;
	};
})(jQuery);