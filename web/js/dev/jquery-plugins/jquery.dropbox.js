(function($) {
    $.fn.dropbox = function(options) {
		return this.each(function() {
			var
				$dropBox = $(this),
				$dropBoxTitle = $(options.titleCssSelector, $dropBox),
				$dropBoxOpener = $(options.openerCssSelector, $dropBox);

			$dropBoxTitle.text(getTitle($dropBox, options.defaultTitle));

			$dropBoxOpener.click(function(e) {
				e.preventDefault();

				$dropBox.removeClass(options.openCssClass);

				if (!$dropBox.hasClass(options.openCssClass)) {
					$dropBox.addClass(options.openCssClass);
				}
			});

			$('html').click(function() {
				$dropBox.removeClass(options.openCssClass);
			});

			$('input[type="checkbox"], input[type="radio"]', $dropBox).on('click', function(e) {
				$dropBoxTitle.text(getTitle($dropBox, options.defaultTitle));

				if (options.onChoose && e.currentTarget.checked) {
					options.onChoose(e.currentTarget.value, $(e.currentTarget));
				}
			});

			$dropBoxOpener.add($dropBox).click(function(e) {
				e.stopPropagation();
			});

			// Закрытие по нажати/ на Esc
			$(document).keyup(function(e) {
				if (e.keyCode == 27) {
					$dropBox.removeClass(options.openCssClass);
				}
			});
		});
    };

	function getTitle($dropBox, defaultTitle) {
		var title = '';

		$('input[type="checkbox"], input[type="radio"]', $dropBox).each(function(i, input) {
			if (input.checked) {
				if (title != '') {
					title += '...';
					return false;
				}

				var
					$label,
					$input = $(input);

				if ($input.attr('id')) {
					$label = $dropBox.find('label[for="' + $input.attr('id') + '"]');
				}

				if (!$label || !$label.length) {
					$label = $input.closest('label');
				}

				if ($label) {
					title += ENTER.utils.trim($label.text());
				}
			}
		});

		if (!title) {
			title = defaultTitle || '';
		}

		return title;
	}
})(jQuery);